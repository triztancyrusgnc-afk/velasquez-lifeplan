<?php
/**
 * AI assistant endpoint (POST only, JSON in / JSON out).
 *
 * The browser never sees the API key: it posts a question here, this script
 * attaches the live system context, calls Google Gemini, and returns the reply.
 *
 * Public by design - visitors need it - so it is rate limited per session to
 * protect the free-tier quota.
 */

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

/** Emits a JSON error and stops. */
function ai_fail($message, $status = 400) {
    http_response_code($status);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ai_fail('POST required', 405);
}

$pdo = getDB();
$settings = getSettings($pdo);

// Loaded up front: ai_local_answer() is used by the quota backoff below.
require_once __DIR__ . '/ai_context.php';
require_once __DIR__ . '/under_construction.php';

// The admin can switch the assistant off site-wide.
if (trim($settings['ai_enabled'] ?? '1') !== '1') {
    ai_fail('The assistant is currently unavailable.', 503);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$question = is_array($data) ? trim((string)($data['question'] ?? '')) : '';

if ($question === '') {
    ai_fail('Please type a question.');
}
if (mb_strlen($question) > AI_MAX_QUESTION_CHARS) {
    ai_fail('That question is too long. Please shorten it to ' . AI_MAX_QUESTION_CHARS . ' characters.');
}

// ---- Rate limit -----------------------------------------------------------
// Counts against the session first; anonymous visitors all share PHP's session
// cookie per browser, which is enough to stop casual scripted hammering.
$now = time();
if (!isset($_SESSION['ai_recent']) || !is_array($_SESSION['ai_recent'])) {
    $_SESSION['ai_recent'] = [];
}
$_SESSION['ai_recent'] = array_values(array_filter($_SESSION['ai_recent'], function ($t) use ($now) {
    return is_int($t) && ($now - $t) < AI_RATE_WINDOW;
}));
if (count($_SESSION['ai_recent']) >= AI_RATE_LIMIT) {
    ai_fail('You have asked several questions in a row. Please wait a moment and try again.', 429);
}
$_SESSION['ai_recent'][] = $now;

// ---- Cache ---------------------------------------------------------------
// The free tier is ~20 requests/day, so an identical question must not cost a
// second call. The key includes a fingerprint of prices and pause switches, so
// changing either invalidates the stale answers by itself.
require_once __DIR__ . '/ai_cache.php';
$cacheKey = ai_cache_key($question, ai_cache_fingerprint($pdo, $settings));
$cached = ai_cache_get($cacheKey);
if ($cached !== null) {
    echo json_encode(['ok' => true, 'answer' => $cached, 'cached' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

// ---- Quota backoff --------------------------------------------------------
// Once every model has returned 429 there is no point spending three more
// HTTPS round-trips on the next question. Answer locally until the marker
// expires, then try the API again by itself.
if (ai_quota_is_blocked()) {
    $local = ai_local_answer($pdo, $settings, $question);
    if ($local !== null) {
        echo json_encode([
            'ok' => true, 'answer' => $local, 'cached' => false, 'fallback' => true,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    ai_fail('The assistant has reached its daily free limit. Please call 0917-123-4567 '
        . 'or use the contact page in the meantime.', 503);
}

// ---- Build the request ----------------------------------------------------
require_once __DIR__ . '/ai_context.php';
require_once __DIR__ . '/under_construction.php';

$persona = trim($settings['ai_persona'] ?? '');
if ($persona !== '') {
    // Optional extra guidance from the admin, appended to the base instructions.
    $context = ai_context($pdo, $settings) . "\n\nADDITIONAL NOTE FROM THE BUSINESS\n" . $persona;
} else {
    $context = ai_context($pdo, $settings);
}

$payload = [
    'systemInstruction' => [
        'parts' => [['text' => $context]],
    ],
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => $question]],
        ],
    ],
    'generationConfig' => [
        'temperature'   => 0.3,
        'topP'          => 0.95,
        'maxOutputTokens' => 2048,
        // Keep it fast and cheap: no visible reasoning tokens for a FAQ bot.
        'thinkingConfig' => ['thinkingBudget' => 0],
    ],
    'safetySettings' => [
        // A funeral-services assistant should be allowed to discuss death,
        // grief and bereavement plainly. Block only the hard-stop categories.
        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
    ],
];

/**
 * POSTs one payload to one model.
 *
 * Returns [$status, $body, $curlError]. Endpoint URL is built from
 * GEMINI_ENDPOINT + the model name, because quota is counted per model.
 */
$aiPost = function (array $p, $model) {
    $url = GEMINI_ENDPOINT . rawurlencode($model) . ':generateContent?key=' . GEMINI_API_KEY;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($p, JSON_UNESCAPED_UNICODE),
    ]);
    $body   = curl_exec($ch);
    $err    = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, $body, $err];
};

$body = false; $curlErr = ''; $status = 0; $exhausted = false;

foreach (GEMINI_MODELS as $model) {
    list($status, $body, $curlErr) = $aiPost($payload, $model);

    // A few models answer a bare 400 to thinkingConfig. Rather than maintain a
    // list of which is which, retry once without it.
    if ($status === 400 && isset($payload['generationConfig']['thinkingConfig'])) {
        unset($payload['generationConfig']['thinkingConfig']);
        list($status, $body, $curlErr) = $aiPost($payload, $model);
    }

    if ($status < 400) {
        break; // got an answer
    }

    error_log("AI assistant $model error $status: " . substr((string)$body, 0, 300));

    if ($status === 429) {
        // This model's own bucket is empty - quota is per model, so the next
        // one in the list may still have headroom.
        $exhausted = true;
        ai_quota_mark_exhausted(); // checked on the next question, see backoff
        continue;
    }
    break; // a non-quota fault (bad key, bad request) would hit every model alike
}

$json = json_decode((string)$body, true);

if ($status >= 400 || !is_array($json)) {
    if ($body === false || $curlErr) {
        error_log('AI assistant transport error: ' . $curlErr);
        ai_fail('The assistant could not be reached. Please try again shortly.', 502);
    }
    // Every model ran dry today. Fall back to the local responder so the widget
    // still gives useful answers instead of dead-ending.
    if ($exhausted && $status === 429) {
        $local = ai_local_answer($pdo, $settings, $question);
        if ($local !== null) {
            echo json_encode([
                'ok'       => true,
                'answer'   => $local,
                'cached'   => false,
                'fallback' => true,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        ai_fail('The assistant has reached its daily free limit. Please call 0917-123-4567 '
            . 'or use the contact page in the meantime.', 503);
    }
    ai_fail('The assistant hit an error. Please try again shortly.', 502);
}

$partsOut = $json['candidates'][0]['content']['parts'] ?? null;
if (!is_array($partsOut)) {
    // Usually a safety block.
    $block = $json['promptFeedback']['blockReason'] ?? ($json['candidates'][0]['finishReason'] ?? 'unknown');
    error_log('AI assistant no content, reason: ' . $block);
    ai_fail('I could not answer that one. Please try rephrasing your question.', 422);
}

$answer = '';
foreach ($partsOut as $part) {
    if (isset($part['text'])) {
        $answer .= $part['text'];
    }
}
$answer = trim($answer);

if ($answer === '') {
    ai_fail('I could not answer that one. Please try rephrasing your question.', 422);
}

echo json_encode([
    'ok'     => true,
    'answer' => $answer,
    'cached' => false,
], JSON_UNESCAPED_UNICODE);

ai_cache_set($cacheKey, $answer);
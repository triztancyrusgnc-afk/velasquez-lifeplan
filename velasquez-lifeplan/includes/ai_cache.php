<?php
/**
 * Tiny file cache for AI answers.
 *
 * The Gemini free tier allows only ~20 requests a day per key, which a public
 * chat can burn in minutes. Caching identical questions means the API is only
 * called the first time something is asked, and repeat questions cost nothing.
 *
 * Entries are keyed on the question AND a fingerprint of the site state, so
 * pausing a section or changing a price invalidates the affected answers
 * automatically - no manual cache clearing needed.
 */

define('AI_CACHE_DIR', __DIR__ . '/../cache/ai');
define('AI_CACHE_TTL', 3600); // 1 hour

function ai_cache_fingerprint(PDO $pdo, array $settings) {
    $plans = '';
    try {
        $rows = $pdo->query('SELECT name, price, monthly_5yr, monthly_10yr, is_active FROM plans ORDER BY id')->fetchAll();
        foreach ($rows as $r) {
            $plans .= $r['name'] . ':' . $r['price'] . ':' . $r['monthly_5yr'] . ':' . $r['monthly_10yr'] . ':' . $r['is_active'] . ';';
        }
    } catch (Throwable $e) {
        $plans = 'unavailable';
    }
    return substr(md5(
        $plans
        . '|' . ($settings['plans_under_construction'] ?? '')
        . '|' . ($settings['chapels_under_construction'] ?? '')
        // Version of the instructions themselves - bump AI_CONTEXT_VERSION when
        // ai_context.php changes wording, or stale answers would outlive it.
        . '|' . (defined('AI_CONTEXT_VERSION') ? AI_CONTEXT_VERSION : 1)
    ), 0, 16);
}

function ai_cache_key($question, $fingerprint) {
    $normalised = mb_strtolower(trim(preg_replace('/\s+/', ' ', $question)));
    return $fingerprint . '-' . sha1($normalised);
}

function ai_cache_file($key) {
    return AI_CACHE_DIR . '/' . preg_replace('/[^a-f0-9\-]/', '', $key) . '.json';
}

function ai_cache_get($key) {
    $file = ai_cache_file($key);
    if (!is_file($file)) {
        return null;
    }
    $age = time() - (int)filemtime($file);
    if ($age > AI_CACHE_TTL) {
        @unlink($file);
        return null;
    }
    $raw = @file_get_contents($file);
    if ($raw === false) {
        return null;
    }
    $data = json_decode($raw, true);
    return (is_array($data) && isset($data['answer'])) ? (string)$data['answer'] : null;
}

function ai_cache_set($key, $answer) {
    if (!is_dir(AI_CACHE_DIR) && !@mkdir(AI_CACHE_DIR, 0755, true)) {
        return; // caching is an optimisation, never a hard requirement
    }
    $file = ai_cache_file($key);
    $tmp  = $file . '.' . getmypid() . '.tmp';
    if (@file_put_contents($tmp, json_encode(['answer' => $answer, 'at' => time()])) !== false) {
        @rename($tmp, $file); // atomic-ish: avoids half-written reads
    }
}

/** Wipes every cached answer. Called when the admin saves site settings. */
function ai_cache_clear() {
    if (!is_dir(AI_CACHE_DIR)) {
        return;
    }
    foreach (glob(AI_CACHE_DIR . '/*.json') ?: [] as $file) {
        @unlink($file);
    }
}

/**
 * "The quota just ran out" marker, so we do not spend a second on three failed
 * HTTPS calls for every question while the free tier is empty. The marker
 * expires after a few minutes and then the assistant tries again by itself -
 * free-tier quotas reset daily, and this keeps the widget responsive meanwhile.
 */
define('AI_QUOTA_BACKOFF', 300); // seconds to skip the API after a 429

function ai_quota_marker() {
    return AI_CACHE_DIR . '/.quota-exhausted';
}

function ai_quota_is_blocked() {
    $file = ai_quota_marker();
    if (!is_file($file)) {
        return false;
    }
    if (time() - (int)filemtime($file) > AI_QUOTA_BACKOFF) {
        @unlink($file); // period is over, try the API again
        return false;
    }
    return true;
}

function ai_quota_mark_exhausted() {
    if (!is_dir(AI_CACHE_DIR) && !@mkdir(AI_CACHE_DIR, 0755, true)) {
        return;
    }
    @touch(ai_quota_marker());
}
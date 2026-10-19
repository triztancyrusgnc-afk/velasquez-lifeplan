<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'velasquez_lifeplan');
define('APP_NAME', 'Velasquez Funeral Service');
define('APP_VERSION', '1.0.0');

/*
 * AI assistant (Google Gemini).
 *
 * The key is used ONLY by includes/ai_chat.php, which calls the API from the
 * server. It is never rendered into a page or sent to the browser, so visitors
 * cannot read or copy it.
 *
 * NEVER echo GEMINI_API_KEY anywhere in output. If you ever move this file
 * outside the web root, delete this comment's worry entirely.
 */
define('GEMINI_API_KEY', 'AQ.Ab8RN6IME9sVDvS1BHwiQiRY2XAGLv9CwAomxCVMramfPMsqPA');

/**
 * Models are tried in order, cheapest/quota-friendliest first, and the first
 * one that answers wins. Free-tier quota is counted separately per model, so
 * if one is exhausted the widget quietly moves to the next instead of failing.
 *
 * Note: some models reject thinkingConfig (a bare 400) - ai_chat.php retries
 * without it automatically, so this list does not need to track that.
 */
define('GEMINI_MODELS', [
    'gemini-3.5-flash-lite',
    'gemini-3.1-flash-lite',
    'gemini-3.7-flash',
]);
// Kept as the alias other code may read; always the first choice above.
define('GEMINI_MODEL', GEMINI_MODELS[0]);
define('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/');
define('AI_MAX_QUESTION_CHARS', 1000);
define('AI_RATE_LIMIT', 6);       // questions allowed per session window
define('AI_RATE_WINDOW', 300);    // ...within this many seconds

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }
    return $pdo;
}

/**
 * Editable website content. Every key here is safe to delete from the
 * site_settings table - the default below is what renders when it is missing.
 */
function defaultSettings() {
    return [
        // Hero
        'hero_eyebrow'        => 'Est. 1990 · Guagua, Pampanga',
        'hero_title'          => 'Every Life Deserves',
        'hero_title_accent'   => 'a Dignified Farewell',
        'hero_subtitle'       => "Pre-need life plans, beautiful chapels, and a team that answers day and night - so your family never has to face loss unprepared or in debt.",
        'hero_image'          => 'img/hero-chapel.jpg',
        'hero_cta_primary'    => 'Explore Life Plans',
        'hero_cta_secondary'  => 'Need Help Now?',
        'hero_avatars'        => 'JR,MG,AD',
        'hero_proof_text'     => 'Trusted by 15,000+ Filipino families across Pampanga',

        // Trust badges (stat band under the hero)
        'stat_1_value' => '30+', 'stat_1_label' => 'Years of Service',
        'stat_2_value' => '15K+', 'stat_2_label' => 'Families Served',
        'stat_3_value' => '6',   'stat_3_label' => 'Plans & Chapels',
        'stat_4_value' => '24/7', 'stat_4_label' => 'Emergency Care',

        // Life plans availability
        'plans_under_construction' => '0',
        'plans_title'    => 'Our Life Plans Are Being Updated',
        'plans_message'  => "We are giving our pre-need catalogue a fresh look. Online enrolment and plan comparison are temporarily unavailable while we finish the updates. Our eServices are open as usual, and our family care team is happy to answer any questions in the meantime.",
        'plans_cta_label' => 'Talk to Our Team',

        // Chapel availability
        'chapels_under_construction' => '0',
        'chapels_title'    => 'Our New Chapel Is Being Built',
        'chapels_message'  => 'Our chapel facility is currently under construction and is not yet open to the public. Chapel, event hall and venue enquiries are temporarily unavailable while the build is completed. Please contact our family care team for assistance in the meantime.',
        'chapels_cta_label' => 'Talk to Our Team',

        // AI assistant
        'ai_enabled' => '1',
        'ai_name'     => 'Vela',
        'ai_welcome'  => "Hello. I'm Vela, the Velasquez assistant. Ask me about life plans, prices, how payments and claims work, or how to use this website.",
        'ai_persona'  => '',
    ];
}

/**
 * Created lazily so the settings page works on installs that predate this
 * table, without forcing anyone to re-run a setup step.
 */
function ensureSettingsTable($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
        setting_value TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

/** Returns key => value map, with defaults filled in for anything unset. */
function getSettings($pdo) {
    $settings = defaultSettings();
    try {
        ensureSettingsTable($pdo);
        $rows = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
    } catch (Throwable $e) {
        return $settings;
    }
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = (string)$row['setting_value'];
    }
    return $settings;
}

/** Upserts the given key => value pairs. */
function saveSettings($pdo, array $settings) {
    ensureSettingsTable($pdo);
    $stmt = $pdo->prepare(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    foreach ($settings as $key => $value) {
        $stmt->execute([(string)$key, (string)$value]);
    }
}

/** Absolute URL for a stored image path. Falls back to the bundled hero. */
function settingImageUrl($path) {
    global $appBase;
    $path = trim((string)$path);
    if ($path === '') {
        $path = 'img/hero-chapel.jpg';
    }
    return $appBase . '/' . ltrim($path, '/');
}

/** Escapes a single-line setting for output. */
function settingText($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Escapes a multi-line setting, turning blank lines into <br>. */
function settingBlock($value) {
    $value = str_replace(["\r\n", "\r"], "\n", (string)$value);
    return nl2br(htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8'), false);
}

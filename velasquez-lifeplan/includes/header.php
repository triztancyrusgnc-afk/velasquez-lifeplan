<?php
// Buffer ALL output unconditionally (nested buffers are safe) so redirects,
// cookies, and session headers always work no matter how large this file gets.
// (Checking ob_get_level() first is wrong: php.ini's small default buffer can
// fill up and flush early, which is exactly what broke logins/redirects.)
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

$pageTitle = $pageTitle ?? '';
$appBase = '/velasquez-lifeplan';

// Auth state for the navbar (shows profile instead of Sign In when logged in)
$navLoggedIn = isset($_SESSION['user_id']);
$navUserName = $_SESSION['user_name'] ?? '';
$navUserRole = $_SESSION['user_role'] ?? 'customer';
$navFirstName = $navUserName !== '' ? explode(' ', $navUserName)[0] : '';
$navInitial = $navUserName !== '' ? strtoupper(substr($navUserName, 0, 1)) : '?';
$navDashLink = $appBase . ($navUserRole === 'admin' ? '/pages/admin.php' : ($navUserRole === 'employee' ? '/pages/staff.php' : '/pages/dashboard.php'));
$navRoleLabel = ucfirst($navUserRole);

// Connect to the database. If it is missing/unreachable (e.g. visitor opened
// some other page before running the setup), show a friendly notice instead
// of a raw error — with a button that goes straight to Step 1.
$pdo = null;
try {
    $pdo = getDB();
    $pdo->query('SELECT 1');
} catch (Throwable $e) {
    http_response_code(503);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup Required - Velasquez Funeral Service</title>
    <link rel="icon" href="<?= $appBase ?>/img/logo.svg" type="image/svg+xml">
</head>
<body style="margin:0;font-family:'Segoe UI',Arial,sans-serif;background:#101922;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;box-sizing:border-box;">
    <div style="max-width:520px;width:100%;text-align:center;background:#1A2634;border:1px solid #CCB877;border-radius:16px;padding:48px 36px;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
        <img src="<?= $appBase ?>/img/logo.svg" alt="Velasquez Funeral Service" width="72" height="72" style="border-radius:14px;margin-bottom:20px;background:#F8F6F0;padding:6px;">
        <h1 style="font-size:1.75rem;margin:0 0 12px;color:#fff;">Database Not Found</h1>
        <p style="color:rgba(255,255,255,0.7);line-height:1.7;margin:0 0 8px;">This system needs its database before any page can load.</p>
        <p style="color:rgba(255,255,255,0.7);line-height:1.7;margin:0 0 28px;">Start <strong>MySQL in XAMPP Control Panel</strong>, then run the setup:</p>
        <a href="<?= $appBase ?>/Step1.php" style="display:inline-block;background:#CCB877;color:#1A2634;font-weight:700;font-size:1.05rem;padding:15px 42px;border-radius:10px;text-decoration:none;">🔄 Go to Step 1: Database Setup</a>
        <p style="margin:24px 0 0;font-size:0.85rem;color:rgba(255,255,255,0.45);">Step 1 → Step 2 → Step 3 → Step 4 takes about 2 minutes.</p>
    </div>
</body>
</html>
    <?php
    exit;
}

// Admin-editable website content (hero photo, hero copy, trust badges,
// life-plan availability). Defaults are filled in by getSettings().
$siteSettings = getSettings($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
    /* Resolve the theme before first paint so the page never flashes light on
       a dark-mode device. Mirrors the logic in js/app.js. */
    (function () {
        try {
            var t = localStorage.getItem('vls-theme');
            if (t !== 'dark' && t !== 'light') {
                t = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.dataset.theme = t;
        } catch (e) {
            document.documentElement.dataset.theme = 'light';
        }
    })();
    </script>
    <title><?= $pageTitle ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Velasquez Funeral Service</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $appBase ?>/css/style.css?v=<?= filemtime(__DIR__ . '/../css/style.css') ?>">
    <?php if (!empty($panelStyles)): ?>
    <link rel="stylesheet" href="<?= $appBase ?>/css/panel.css?v=<?= filemtime(__DIR__ . '/../css/panel.css') ?>">
    <?php endif; ?>
    <?php if (trim($siteSettings['ai_enabled'] ?? '1') === '1'): ?>
    <link rel="stylesheet" href="<?= $appBase ?>/css/ai.css?v=<?= filemtime(__DIR__ . '/../css/ai.css') ?>">
    <?php endif; ?>
    <link rel="icon" href="<?= $appBase ?>/img/logo.svg" type="image/svg+xml">
</head>
<body>
    <div id="pageTransition" aria-hidden="true">
        <img src="<?= $appBase ?>/img/logo.svg" alt="">
        <div class="pt-bar"><span></span></div>
    </div>
    <div id="pageProgress" aria-hidden="true"></div>

    <nav class="navbar<?= !empty($navTransparent) ? ' transparent' : '' ?>" id="navbar">
        <div class="container navbar-inner">
            <a href="<?= $appBase ?>/home.php" class="navbar-brand">
                <img src="<?= $appBase ?>/img/logo.svg" alt="Velasquez Funeral Service" width="42" height="42" style="border-radius:10px;">
                <div class="navbar-brand-text">
                    <span class="brand-name">Velasquez Funeral</span>
                    <span class="brand-tagline">Life Plan &middot; Est. 1990</span>
                </div>
            </a>

            <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

            <div class="nav-menu" id="navMenu">
                <ul class="navbar-links" id="navLinks">
                    <li><a href="<?= $appBase ?>/home.php">Home</a></li>
                    <li><a href="<?= $appBase ?>/pages/plans.php">Life Plans</a></li>
                    <li><a href="<?= $appBase ?>/pages/chapels.php">Chapels</a></li>
                    <li><a href="<?= $appBase ?>/pages/caskets.php">Caskets</a></li>
                    <li><a href="<?= $appBase ?>/pages/grief.php">Grief Support</a></li>
                    <li><a href="<?= $appBase ?>/pages/eservices.php">eServices</a></li>
                    <li><a href="<?= $appBase ?>/pages/about.php">About</a></li>
                    <li><a href="<?= $appBase ?>/pages/contact.php">Contact</a></li>
                </ul>

                <div class="navbar-actions">
                    <button type="button" id="themeToggle" class="theme-toggle" aria-pressed="false" aria-label="Switch to dark theme" title="Switch to dark theme"></button>
                    <a href="<?= $appBase ?>/pages/plans.php" class="btn btn-gold btn-sm">Buy Now</a>
                    <?php if ($navLoggedIn): ?>
                        <div class="profile-menu">
                            <button class="profile-btn" id="profileBtn" aria-haspopup="true" aria-expanded="false">
                                <span class="profile-avatar"><?= htmlspecialchars($navInitial) ?></span>
                                <span class="profile-name"><?= htmlspecialchars($navFirstName) ?></span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="profile-dropdown" id="profileDropdown" role="menu">
                                <div class="profile-head">
                                    <span class="profile-avatar lg"><?= htmlspecialchars($navInitial) ?></span>
                                    <div>
                                        <div class="profile-head-name"><?= htmlspecialchars($navUserName) ?></div>
                                        <span class="badge badge-gold profile-head-role"><?= htmlspecialchars($navRoleLabel) ?></span>
                                    </div>
                                </div>
                                <a href="<?= $navDashLink ?>" role="menuitem">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                    My Dashboard
                                </a>
                                <a href="<?= $appBase ?>/login.php?logout=1" role="menuitem" class="danger">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Sign Out
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= $appBase ?>/login.php" class="btn btn-navy btn-sm">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

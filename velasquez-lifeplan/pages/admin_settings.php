<?php
$pageTitle = 'Website Settings';
$panelStyles = true; // pulls in css/panel.css
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// ---------- Save handlers (POST + redirect, never resubmit on refresh) ----------
$heroUploadDir = __DIR__ . '/../uploads/site/';
$heroUploadPrefix = 'uploads/site/';
$flash = $_SESSION['admin_flash'] ?? '';
unset($_SESSION['admin_flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['settings_action'] ?? 'save';

    // ---- Remove the custom hero photo and fall back to the bundled one ----
    if ($action === 'remove_hero') {
        $current = trim($_POST['current_hero'] ?? '');
        if ($current !== '' && strpos($current, $heroUploadPrefix) === 0) {
            $old = __DIR__ . '/../' . $current;
            if (is_file($old)) {
                @unlink($old);
            }
        }
        saveSettings($pdo, ['hero_image' => defaultSettings()['hero_image']]);
        $_SESSION['admin_flash'] = 'Custom hero photo removed. The default photo is back in use.';
        header('Location: admin_settings.php');
        exit;
    }

    // ---- Save hero copy, badges and the life plans toggle ----
    if ($action === 'save') {
        $errors = [];

        $clean = function ($key, $max = 255) {
            return mb_substr(trim((string)($_POST[$key] ?? '')), 0, $max);
        };

        // Text settings. Multi-line fields are normalised to \n so textarea
        // line breaks survive the round-trip through the key/value column.
        $updates = [
            'hero_eyebrow'          => $clean('hero_eyebrow'),
            'hero_title'            => $clean('hero_title', 120),
            'hero_title_accent'     => $clean('hero_title_accent', 120),
            'hero_cta_primary'      => $clean('hero_cta_primary', 60),
            'hero_cta_secondary'    => $clean('hero_cta_secondary', 60),
            'hero_avatars'          => $clean('hero_avatars', 60),
            'ai_name'              => $clean('ai_name', 40),
        ];
        foreach (['hero_subtitle', 'hero_proof_text', 'ai_welcome', 'ai_persona'] as $multiline) {
            $updates[$multiline] = str_replace(["\r\n", "\r"], "\n", trim((string)($_POST[$multiline] ?? '')));
        }
        for ($i = 1; $i <= 4; $i++) {
            $updates['stat_' . $i . '_value'] = $clean('stat_' . $i . '_value', 20);
            $updates['stat_' . $i . '_label'] = $clean('stat_' . $i . '_label', 60);
        }

        // Pausable sections. Each is an on/off flag plus its own three copy
        // fields, named '<section>_title' / '_message' / '_cta_label'.
        // Keep this list in step with $pausableSections in the form below.
        foreach (['plans', 'chapels'] as $sectionKey) {
            $updates[$sectionKey . '_under_construction'] = isset($_POST[$sectionKey . '_under_construction']) ? '1' : '0';
            $updates[$sectionKey . '_title'] = $clean($sectionKey . '_title', 160);
            $updates[$sectionKey . '_cta_label'] = $clean($sectionKey . '_cta_label', 60);
            $updates[$sectionKey . '_message'] = str_replace(
                ["\r\n", "\r"],
                "\n",
                trim((string)($_POST[$sectionKey . '_message'] ?? ''))
            );
        }

        $updates['ai_enabled'] = isset($_POST['ai_enabled']) ? '1' : '0';

        // Hero headline must not end up empty - the hero would collapse.
        if ($updates['hero_title'] === '') {
            $errors[] = 'The hero headline cannot be blank.';
            $updates['hero_title'] = defaultSettings()['hero_title'];
        }
        if ($updates['hero_title_accent'] === '') {
            $errors[] = 'The hero accent line cannot be blank.';
            $updates['hero_title_accent'] = defaultSettings()['hero_title_accent'];
        }

        // ---- Optional new hero photo ----
        $currentHero = trim($_POST['current_hero'] ?? '');
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['hero_image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'The hero photo must be a JPG, PNG, WEBP or GIF file.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $errors[] = 'The hero photo must be smaller than 5MB.';
            } elseif (@getimagesize($file['tmp_name']) === false) {
                $errors[] = 'That file is not a readable image.';
            } else {
                if (!is_dir($heroUploadDir)) {
                    mkdir($heroUploadDir, 0755, true);
                }
                $newName = 'hero_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $heroUploadDir . $newName)) {
                    // Only delete the previous custom upload, never the bundled img/ one.
                    if ($currentHero !== '' && strpos($currentHero, $heroUploadPrefix) === 0) {
                        $old = __DIR__ . '/../' . $currentHero;
                        if (is_file($old)) {
                            @unlink($old);
                        }
                    }
                    $updates['hero_image'] = $heroUploadPrefix . $newName;
                } else {
                    $errors[] = 'Could not save the uploaded photo. Check the folder permissions.';
                }
            }
        } elseif (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'The photo upload failed. Please try again.';
        }

        // Only touch the database when there is nothing to complain about.
        if ($errors) {
            $_SESSION['admin_flash'] = implode(' ', $errors);
            header('Location: admin_settings.php');
            exit;
        }

        saveSettings($pdo, $updates);

        // Cached AI answers may now be out of date (prices, pause switches).
        if (file_exists(__DIR__ . '/../includes/ai_cache.php')) {
            require_once __DIR__ . '/../includes/ai_cache.php';
            ai_cache_clear();
        }

        $_SESSION['admin_flash'] = 'Homepage updated. Changes are live now.';
        header('Location: admin_settings.php');
        exit;
    }
}

$settings = getSettings($pdo);
$currentHero = trim($settings['hero_image']);
$isCustomHero = strpos($currentHero, 'uploads/site/') === 0;

// Avatar initials -> short list for the preview swatches.
$avatarInitials = array_values(array_filter(array_map('trim', explode(',', $settings['hero_avatars'])), function ($v) {
    return $v !== '';
}));
?>

<style>
.dashboard-content{background:var(--bg) !important;}
.setting-hint{font-size:.8125rem;color:var(--text-muted);margin:6px 0 0;line-height:1.6;}
.setting-label{display:block;font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;font-weight:700;color:var(--text-strong);margin:0 0 7px;}
.switch{display:flex;align-items:center;gap:12px;cursor:pointer;user-select:none;}
.switch input{position:absolute;opacity:0;pointer-events:none;}
.switch-track{width:52px;height:28px;border-radius:999px;background:#D7D2C8;position:relative;transition:background .2s ease;flex-shrink:0;}
.switch-track::after{content:"";position:absolute;top:3px;left:3px;width:22px;height:22px;border-radius:50%;background:var(--surface);box-shadow:0 1px 3px rgba(0,0,0,.25);transition:transform .2s ease;}
.switch input:checked + .switch-track{background:#47624F;}
.switch input:checked + .switch-track::after{transform:translateX(24px);}
.switch input:focus-visible + .switch-track{outline:2px solid #CCB877;outline-offset:2px;}
.hero-preview{width:100%;height:220px;object-fit:cover;border-radius:16px;border:1px solid var(--border);display:block;background:#E9E5DD;}
.badge-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.avatar-chip{width:100%;max-width:34px;height:34px;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.18);background:var(--surface-navy);color:var(--ink-on-navy);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;}
@media(max-width:640px){.badge-row{grid-template-columns:1fr;}.hero-photo-grid{grid-template-columns:1fr !important;}}
</style>

<?php
// Shared panel shell - one sidebar definition drives every signed-in panel.
$panelRoleLabel = 'Administrator';
$panelMenu = [
    ['title' => 'Overview', 'items' => [
        ['label' => 'Dashboard', 'href' => 'admin.php', 'icon' => 'grid'],
    ]],
    ['title' => 'Website', 'items' => [
        ['label' => 'Website Settings', 'href' => 'admin_settings.php', 'icon' => 'cog', 'active' => true],
        ['label' => 'View Homepage', 'href' => $appBase . '/home.php', 'icon' => 'eye'],
    ]],
];
$panelMenuFooter = [
    ['label' => 'Sign Out', 'href' => '../login.php?logout=1', 'icon' => 'logout', 'muted' => true],
];
?>
<div class="dashboard-layout">
<?php
require __DIR__ . '/../includes/panel_sidebar.php';
?>
    <main class="dashboard-content">

        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <div class="panel-topbar">
            <div class="panel-topbar-titles">
                <p class="panel-crumb">
                    <a href="admin.php">Backoffice</a>
                    <span class="panel-crumb-sep">/</span>
                    <span>Website</span>
                </p>
                <h1 class="panel-title">Homepage Settings</h1>
            </div>
            <div class="panel-actions">
                <a href="<?= $appBase ?>/home.php" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Preview
                </a>
            </div>
        </div>

        <?php if ($flash): ?>
        <div class="panel-flash">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M20 6L9 17l-5-5"/></svg>
            <span><?= htmlspecialchars($flash) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="admin_settings.php" enctype="multipart/form-data">
            <input type="hidden" name="settings_action" value="save">
            <input type="hidden" name="current_hero" value="<?= htmlspecialchars($currentHero) ?>">

            <!-- ============ HERO PHOTO ============ -->
            <div class="panel-card">
                <h3 class="panel-card-title">Hero Photo</h3>
                <p class="setting-hint" style="margin:0 0 24px;">The wide photo shown on the right side of the homepage hero.</p>

                <div style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,0.9fr);gap:28px;align-items:start;" class="hero-photo-grid">
                    <div>
                        <img src="<?= htmlspecialchars(settingImageUrl($currentHero)) ?>?v=<?= @filemtime(__DIR__ . '/../' . $currentHero) ?: time() ?>" alt="Current hero photo preview" class="hero-preview">
                        <p class="setting-hint" style="margin-top:10px;">
                            <?= $isCustomHero
                                ? 'Using your uploaded photo.'
                                : 'Using the bundled default photo.' ?>
                        </p>
                    </div>
                    <div>
                        <label class="setting-label" for="hero_image">Upload a new photo</label>
                        <input type="file" id="hero_image" name="hero_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                        <p class="setting-hint">JPG, PNG, WEBP or GIF, up to 5MB. Landscape works best (about 3:1). Leave empty to keep the current photo.</p>

                        <?php if ($isCustomHero): ?>
                        <button type="submit" name="settings_action" value="remove_hero"
                                formnovalidate
                                onclick="return confirm('Remove the uploaded hero photo and restore the default?');"
                                style="margin-top:18px;background:none;border:1px solid #B04A3F;color:#B04A3F;font-weight:600;font-size:.875rem;padding:9px 18px;border-radius:8px;cursor:pointer;">
                            Remove uploaded photo
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ============ HERO TEXT ============ -->
            <div class="panel-card">
                <h3 class="panel-card-title">Hero Text</h3>
                <p class="setting-hint" style="margin:0 0 24px;">The headline and supporting copy at the top of the homepage.</p>

                <div style="display:grid;gap:20px;">
                    <div>
                        <label class="setting-label" for="hero_eyebrow">Small label above the headline</label>
                        <input type="text" id="hero_eyebrow" name="hero_eyebrow" class="form-control" maxlength="90" value="<?= htmlspecialchars($settings['hero_eyebrow']) ?>">
                    </div>

                    <div class="badge-row">
                        <div>
                            <label class="setting-label" for="hero_title">Headline - first line</label>
                            <input type="text" id="hero_title" name="hero_title" class="form-control" maxlength="120" value="<?= htmlspecialchars($settings['hero_title']) ?>" required>
                            <p class="setting-hint">Plain white text.</p>
                        </div>
                        <div>
                            <label class="setting-label" for="hero_title_accent">Headline - gold line</label>
                            <input type="text" id="hero_title_accent" name="hero_title_accent" class="form-control" maxlength="120" value="<?= htmlspecialchars($settings['hero_title_accent']) ?>" required>
                            <p class="setting-hint">Shown below in italic gold.</p>
                        </div>
                    </div>

                    <div>
                        <label class="setting-label" for="hero_subtitle">Supporting paragraph</label>
                        <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3" maxlength="600"><?= htmlspecialchars($settings['hero_subtitle']) ?></textarea>
                    </div>

                    <div class="badge-row">
                        <div>
                            <label class="setting-label" for="hero_cta_primary">Primary button label</label>
                            <input type="text" id="hero_cta_primary" name="hero_cta_primary" class="form-control" maxlength="60" value="<?= htmlspecialchars($settings['hero_cta_primary']) ?>">
                        </div>
                        <div>
                            <label class="setting-label" for="hero_cta_secondary">Secondary button label</label>
                            <input type="text" id="hero_cta_secondary" name="hero_cta_secondary" class="form-control" maxlength="60" value="<?= htmlspecialchars($settings['hero_cta_secondary']) ?>">
                        </div>
                    </div>

                    <div class="badge-row">
                        <div>
                            <label class="setting-label" for="hero_avatars">Profile initials</label>
                            <input type="text" id="hero_avatars" name="hero_avatars" class="form-control" maxlength="60" value="<?= htmlspecialchars($settings['hero_avatars']) ?>">
                            <p class="setting-hint">Comma separated, e.g. JR, MG, AD. Leave blank for none.</p>
                            <div style="display:flex;gap:8px;margin-top:12px;">
                                <?php foreach (array_slice($avatarInitials, 0, 4) as $initial): ?>
                                    <span class="avatar-chip"><?= htmlspecialchars(mb_strtoupper(mb_substr($initial, 0, 2))) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label" for="hero_proof_text">Line under the initials</label>
                            <textarea id="hero_proof_text" name="hero_proof_text" class="form-control" rows="3" maxlength="200"><?= htmlspecialchars($settings['hero_proof_text']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ TRUST BADGES ============ -->
            <div class="panel-card">
                <h3 class="panel-card-title">Trust Badges</h3>
                <p class="setting-hint" style="margin:0 0 24px;">The four counters in the band under the hero. Clear a label to hide that badge.</p>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div>
                            <label class="setting-label" for="stat_<?= $i ?>_value">Badge <?= $i ?></label>
                            <input type="text" id="stat_<?= $i ?>_value" name="stat_<?= $i ?>_value" class="form-control" maxlength="20" placeholder="30+" value="<?= htmlspecialchars($settings['stat_' . $i . '_value']) ?>">
                            <input type="text" name="stat_<?= $i ?>_label" class="form-control" maxlength="60" placeholder="Years of Service" value="<?= htmlspecialchars($settings['stat_' . $i . '_label']) ?>" style="margin-top:10px;">
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- ============ PAUSABLE SECTIONS ============ -->
            <div class="panel-card">
                <h3 class="panel-card-title">Sections Under Construction</h3>
                <p class="setting-hint" style="margin:0 0 24px;">Pause a part of the site without touching any code. Each switch is independent, and each holding page's wording is editable.</p>

                <?php
                // One loop drives every pausable section. To add one, add its
                // copy keys to defaultSettings() in config.php - the toggle and
                // its fields appear here automatically.
                $pausableSections = [
                    'plans' => [
                        'label' => 'Life Plan Pages',
                        'flag'  => 'plans_under_construction',
                        'summary' => 'Affects <strong>Life Plans</strong>, <strong>Compare Plans</strong> and <strong>Checkout</strong>. On the homepage the plan grid, category tabs and plan modal are replaced by a notice. Customers can still sign in to their own dashboard.',
                    ],
                    'chapels' => [
                        'label' => 'Chapel Pages',
                        'flag'  => 'chapels_under_construction',
                        'summary' => 'Affects <strong>Chapels &amp; Facilities</strong>. On the homepage the chapel teaser is replaced by a notice. Caskets, grief support and eServices stay open.',
                    ],
                ];
                ?>
                <div style="display:grid;gap:28px;">
                    <?php foreach ($pausableSections as $key => $sec): ?>
                        <?php $isOn = trim($settings[$sec['flag']] ?? '0') === '1'; ?>
                        <div style="padding:22px;border:1px solid var(--border);border-radius:var(--radius-md);background:var(--surface-2);">
                            <label class="switch" style="margin-bottom:8px;">
                                <input type="checkbox" name="<?= htmlspecialchars($sec['flag']) ?>" value="1" <?= $isOn ? 'checked' : '' ?>>
                                <span class="switch-track"></span>
                                <span style="font-weight:600;color:var(--text-strong);font-size:0.9375rem;">
                                    Show &ldquo;Under Construction&rdquo; instead of <?= htmlspecialchars($sec['label']) ?>
                                </span>
                            </label>
                            <p class="setting-hint" style="margin-bottom:20px;"><?= $sec['summary'] ?></p>

                            <div style="display:grid;gap:16px;">
                                <div>
                                    <label class="setting-label" for="<?= $key ?>_title">Holding page headline</label>
                                    <input type="text" id="<?= $key ?>_title" name="<?= $key ?>_title" class="form-control" maxlength="160" value="<?= htmlspecialchars($settings[$key . '_title'] ?? '') ?>">
                                </div>
                                <div>
                                    <label class="setting-label" for="<?= $key ?>_message">Holding page message</label>
                                    <textarea id="<?= $key ?>_message" name="<?= $key ?>_message" class="form-control" rows="4" maxlength="1200"><?= htmlspecialchars($settings[$key . '_message'] ?? '') ?></textarea>
                                </div>
                                <div>
                                    <label class="setting-label" for="<?= $key ?>_cta_label">Holding page button label</label>
                                    <input type="text" id="<?= $key ?>_cta_label" name="<?= $key ?>_cta_label" class="form-control" maxlength="60" value="<?= htmlspecialchars($settings[$key . '_cta_label'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ============ AI ASSISTANT ============ -->
            <div class="panel-card">
                <h3 class="panel-card-title">AI Assistant</h3>
                <p class="setting-hint" style="margin:0 0 24px;">The floating assistant answers visitor questions using the live plan prices and the current status of each section.</p>

                <label class="switch" style="margin-bottom:8px;">
                    <input type="checkbox" name="ai_enabled" value="1" <?= trim($settings['ai_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <span class="switch-track"></span>
                    <span style="font-weight:600;color:var(--text-strong);font-size:0.9375rem;">
                        Show the assistant on the public site
                    </span>
                </label>
                <p class="setting-hint" style="margin-bottom:26px;">
                    Switching this off hides the chat button and makes the endpoint refuse questions.
                    It stays on the public site only &mdash; admin and staff panels are unaffected.
                </p>

                <div style="display:grid;gap:20px;">
                    <div>
                        <label class="setting-label" for="ai_name">Assistant name</label>
                        <input type="text" id="ai_name" name="ai_name" class="form-control" maxlength="40" value="<?= htmlspecialchars($settings['ai_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="setting-label" for="ai_welcome">Opening message</label>
                        <textarea id="ai_welcome" name="ai_welcome" class="form-control" rows="3" maxlength="500"><?= htmlspecialchars($settings['ai_welcome'] ?? '') ?></textarea>
                        <p class="setting-hint">The first thing a visitor sees when they open the chat.</p>
                    </div>
                    <div>
                        <label class="setting-label" for="ai_persona">Extra guidance for the assistant</label>
                        <textarea id="ai_persona" name="ai_persona" class="form-control" rows="4" maxlength="1500"><?= htmlspecialchars($settings['ai_persona'] ?? '') ?></textarea>
                        <p class="setting-hint">Optional. Use this for anything that changes often, such as branch hours or a promotion. It is appended to the instructions the assistant already has.</p>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:14px;flex-wrap:wrap;align-items:center;margin-bottom:16px;">
                <button type="submit" class="btn btn-gold btn-lg">Save Changes</button>
                <a href="<?= $appBase ?>/home.php" target="_blank" rel="noopener" class="btn btn-navy btn-lg">Preview Homepage</a>
            </div>
        </form>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

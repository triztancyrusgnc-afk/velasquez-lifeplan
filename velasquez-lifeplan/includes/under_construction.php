<?php
/**
 * Shared "under construction" gate.
 *
 * Call guardSection($siteSettings, 'plans') right after including header.php
 * in plans.php / compare.php / checkout.php, or guardSection($siteSettings,
 * 'chapels') in chapels.php. When the admin has paused that section it renders
 * a styled holding page, includes the footer and exits - so none of the normal
 * page logic (or checkout writes) ever runs.
 *
 * To pause a NEW section, add its copy keys to defaultSettings() in config.php
 * and nothing else: the helper, the admin form and the fallback text below all
 * key off the same '<section>_' prefix.
 *
 * Copy comes from admin settings; the fallbacks below are hardcoded so the page
 * still reads well if a setting row was deleted.
 */

/** Per-section defaults, used when a setting row is missing. */
function sectionDefaults($section) {
    $sections = [
        'plans' => [
            'flag'   => 'plans_under_construction',
            'title'   => 'Our Life Plans Are Being Updated',
            'message' => 'Online enrolment and plan comparison are temporarily unavailable while we finish the updates. Our eServices are open as usual - please contact our family care team for assistance.',
            'cta'     => 'Talk to Our Team',
            'links'   => [
                ['label' => 'Chapels & Facilities', 'href' => '/pages/chapels.php', 'icon' => '&#10013;', 'text' => 'View our chapel accommodations and event halls.'],
                ['label' => 'Caskets & Urns', 'href' => '/pages/caskets.php', 'icon' => '&#10015;', 'text' => 'Browse our available casket and urn selections.'],
                ['label' => 'Grief Support', 'href' => '/pages/grief.php', 'icon' => '&#10043;', 'text' => 'Free guidance and resources for bereaved families.'],
                ['label' => 'eServices', 'href' => '/pages/eservices.php', 'icon' => '&#9744;', 'text' => 'Online payments, documents and scheduling.'],
            ],
        ],
        'chapels' => [
            'flag'   => 'chapels_under_construction',
            'title'   => 'Our New Chapel Is Being Built',
            'message' => 'Our chapel facility is currently under construction and is not yet open to the public. Chapels, event halls and venue enquiries are temporarily unavailable while the build is completed. Please contact our family care team for assistance in the meantime.',
            'cta'     => 'Talk to Our Team',
            'links'   => [
                ['label' => 'Caskets & Urns', 'href' => '/pages/caskets.php', 'icon' => '&#10015;', 'text' => 'Browse our available casket and urn selections.'],
                ['label' => 'Grief Support', 'href' => '/pages/grief.php', 'icon' => '&#10043;', 'text' => 'Free guidance and resources for bereaved families.'],
                ['label' => 'eServices', 'href' => '/pages/eservices.php', 'icon' => '&#9744;', 'text' => 'Online payments, documents and scheduling.'],
                ['label' => 'About Us', 'href' => '/pages/about.php', 'icon' => '&#10003;', 'text' => 'Learn about our service and our history since 1990.'],
            ],
        ],
    ];
    return $sections[$section] ?? $sections['plans'];
}

/** True when the admin has switched this section off. */
function sectionPaused(array $siteSettings, $section) {
    $d = sectionDefaults($section);
    return trim($siteSettings[$d['flag']] ?? '0') === '1';
}

// Kept as named wrappers so the call sites read clearly.
function plansSectionPaused(array $siteSettings) {
    return sectionPaused($siteSettings, 'plans');
}

function chapelsSectionPaused(array $siteSettings) {
    return sectionPaused($siteSettings, 'chapels');
}

function guardPlansSection(array $siteSettings) {
    guardSection($siteSettings, 'plans');
}

/**
 * Renders the holding page and exits when $section is paused. Returns silently
 * otherwise, so callers can put it straight after the header include.
 */
function guardSection(array $siteSettings, $section = 'plans') {
    if (!sectionPaused($siteSettings, $section)) {
        return;
    }

    // $appBase is set by includes/header.php in the global scope; this function
    // needs it explicitly, otherwise every link below renders with an empty href.
    global $appBase;

    $d = sectionDefaults($section);
    $prefix = $section . '_';

    $title   = trim($siteSettings[$prefix . 'title'] ?? '');
    $message = trim($siteSettings[$prefix . 'message'] ?? '');
    $cta     = trim($siteSettings[$prefix . 'cta_label'] ?? '');

    if ($title === '')   { $title = $d['title']; }
    if ($message === '') { $message = $d['message']; }
    if ($cta === '')     { $cta = $d['cta']; }
    ?>
    <section class="sn-section" style="background:var(--surface-navy);position:relative;overflow:hidden;padding:150px 0 90px;">
        <div style="position:absolute;top:-120px;right:-80px;width:480px;height:480px;border-radius:50%;background:rgba(204,184,119,0.08);"></div>
        <div style="position:absolute;bottom:-160px;left:-100px;width:420px;height:420px;border-radius:50%;background:rgba(108,139,170,0.10);"></div>
        <div class="container" style="position:relative;z-index:2;">
            <div style="max-width:720px;margin:0 auto;text-align:center;">

                <div class="uc-loader" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>

                <span class="sn-eyebrow fade-in" style="display:inline-block;background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.18);color:#CCB877;">Under Construction</span>

                <h1 class="sn-title fade-in" style="color:var(--ink-on-navy);font-size:clamp(2.1rem,4.4vw,3.4rem);line-height:1.1;margin:18px 0 20px;">
                    <?= settingText($title) ?>
                </h1>

                <p class="sn-sub fade-in" style="color:rgba(255,255,255,0.78);max-width:600px;margin:0 auto;">
                    <?= settingBlock($message) ?>
                </p>

                <div class="fade-in" style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-top:36px;">
                    <a href="<?= $appBase ?>/pages/contact.php" class="btn btn-gold btn-lg"><?= settingText($cta) ?></a>
                    <a href="tel:+639171234567" class="btn btn-ghostlight btn-lg">Call 0917-123-4567</a>
                    <a href="<?= $appBase ?>/home.php" class="btn btn-ghostlight btn-lg">Back to Home</a>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="fade-in" style="margin-top:40px;">
                        <p style="margin:0;color:rgba(255,255,255,0.6);font-size:0.9375rem;line-height:1.7;">
                            <?= $section === 'chapels'
                                ? 'Already scheduled with us? Your chapel booking, policy details and claims are all available in your dashboard.'
                                : 'Already holding a plan? Your policy details, payment schedule and claims are all available in your dashboard.' ?>
                        </p>
                        <a href="<?= $appBase ?>/pages/dashboard.php" class="btn btn-white" style="margin-top:14px;">Go to My Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="sn-section" style="background:var(--bg);padding:70px 0;">
        <div class="container">
            <div class="sn-center" style="max-width:640px;margin:0 auto;">
                <span class="sn-eyebrow">Meanwhile</span>
                <h2 class="sn-title">Everything Else<br>&amp; <em>Stays Open</em></h2>
                <p class="sn-sub"><?= $section === 'chapels'
                    ? 'Our chapel facility is still being built. The rest of our services carry on as normal.'
                    : 'Only online life plan enrolment is paused. The rest of our services carry on as normal.' ?></p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:24px;margin-top:44px;">
                <?php foreach ($d['links'] as $link): ?>
                    <?php
                    // Don't advertise a section that is itself paused, otherwise
                    // "everything else stays open" would link into another notice.
                    $pausedTarget = null;
                    if (preg_match('#/pages/(plans|chapels)\.php$#', $link['href'], $m)) {
                        $pausedTarget = $m[1];
                    }
                    if ($pausedTarget && sectionPaused($siteSettings, $pausedTarget)) {
                        continue;
                    }
                    ?>
                    <a href="<?= $appBase . $link['href'] ?>" class="sn-card topline" style="padding:30px 26px;text-decoration:none;color:inherit;">
                        <div class="sn-icon" style="width:48px;height:48px;margin-bottom:14px;"><?= $link['icon'] ?></div>
                        <h3 style="margin:0 0 8px;font-size:1.2rem;"><?= settingText($link['label']) ?></h3>
                        <p class="text-sm text-muted" style="margin:0;line-height:1.6;"><?= settingText($link['text']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <style>
    .uc-loader{display:flex;gap:10px;justify-content:center;margin-bottom:34px;}
    .uc-loader span{width:14px;height:14px;border-radius:50%;background:#CCB877;display:block;animation:ucBounce 1.3s ease-in-out infinite;}
    .uc-loader span:nth-child(2){animation-delay:0.16s;}
    .uc-loader span:nth-child(3){animation-delay:0.32s;}
    @keyframes ucBounce{0%,80%,100%{transform:scale(0.6);opacity:0.45;}40%{transform:scale(1);opacity:1;}}
    @media (prefers-reduced-motion:reduce){.uc-loader span{animation:none;}}
    </style>

    <?php
    require __DIR__ . '/footer.php';
    exit;
}
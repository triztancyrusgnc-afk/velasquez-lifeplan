<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
require_once __DIR__ . '/includes/under_construction.php';

try {
    $plansStmt = $pdo->prepare("SELECT * FROM plans WHERE is_active = 1 ORDER BY category, price ASC");
    $plansStmt->execute();
    $plans = $plansStmt->fetchAll();
} catch (PDOException $e) {
    $plans = [];
}

// Hero content is admin-editable (see pages/admin_settings.php). Trust badges
// with a blank label are skipped so the admin can hide them without code edits.
$heroInitials = array_values(array_filter(array_map('trim', explode(',', $siteSettings['hero_avatars'] ?? '')), function ($v) {
    return $v !== '';
}));
$heroStats = [];
for ($i = 1; $i <= 4; $i++) {
    $label = trim($siteSettings['stat_' . $i . '_label'] ?? '');
    if ($label !== '') {
        $heroStats[] = [
            'value' => trim($siteSettings['stat_' . $i . '_value'] ?? ''),
            'label' => $label,
        ];
    }
}

// Flattened plan data for the shared View Details modal.
$planDetails = [];
foreach ($plans as $pl) {
    $feat = json_decode($pl['features'] ?? '[]', true);
    if (!is_array($feat)) $feat = [];
    $numOrNull = function ($v) {
        return (isset($v) && $v !== '' && $v !== null) ? (float)$v : null;
    };
    $planDetails[(int)$pl['id']] = [
        'id' => (int)$pl['id'],
        'name' => $pl['name'] ?? 'Life Plan',
        'category' => $pl['category'] ?? 'traditional',
        'description' => $pl['description'] ?? '',
        'price' => (float)($pl['price'] ?? 0),
        'spot' => $numOrNull($pl['spot_cash'] ?? $pl['spot_cash_price'] ?? null),
        'm5' => $numOrNull($pl['monthly_5yr'] ?? null),
        'm10' => $numOrNull($pl['monthly_10yr'] ?? null),
        'down' => $numOrNull($pl['downpayment'] ?? null),
        'casket' => $pl['casket_type'] ?? null,
        'viewing' => isset($pl['chapel_viewing_days']) ? (int)$pl['chapel_viewing_days'] : null,
        'features' => array_values($feat),
    ];
}

// Homepage quick-contact form: saves straight to contact_inquiries
$homeContactSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['home_contact'])) {
    $hcName = trim($_POST['hc_name'] ?? '');
    $hcEmail = trim($_POST['hc_email'] ?? '');
    $hcPhone = trim($_POST['hc_phone'] ?? '');
    $hcMessage = trim($_POST['hc_message'] ?? '');
    if ($hcName !== '' && $hcMessage !== '') {
        try {
            $hcStmt = $pdo->prepare('INSERT INTO contact_inquiries (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $hcStmt->execute([$hcName, $hcEmail, $hcPhone, 'General Inquiry', $hcMessage]);
            $homeContactSent = true;
        } catch (PDOException $e) {
            $homeContactSent = true; // table missing: still confirm to visitor
        }
    }
}
?>

<!-- Hero Section -->
<section class="sn-section" style="background:var(--surface-navy);position:relative;overflow:hidden;padding:150px 0 70px;">
    <div style="position:absolute;top:-120px;right:-80px;width:480px;height:480px;border-radius:50%;background:rgba(204,184,119,0.08);"></div>
    <div style="position:absolute;bottom:-160px;left:-100px;width:420px;height:420px;border-radius:50%;background:rgba(108,139,170,0.10);"></div>
    <div class="container" style="position:relative;z-index:2;">
        <div class="hero-grid fade-in">
            <div class="hero-copy">
                <span class="sn-eyebrow" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.18);color:#CCB877;"><?= settingBlock($siteSettings['hero_eyebrow']) ?></span>
                <h1 class="sn-title" style="color:var(--ink-on-navy);font-size:clamp(2.6rem,5vw,4.2rem);line-height:1.05;"><?= settingText($siteSettings['hero_title']) ?><br><em style="font-style:italic;color:#CCB877;"><?= settingText($siteSettings['hero_title_accent']) ?></em></h1>
                <p class="sn-sub" style="color:rgba(255,255,255,0.78);max-width:560px;"><?= settingBlock($siteSettings['hero_subtitle']) ?></p>
                <div class="hero-actions">
                    <a href="#plans" class="btn btn-gold btn-lg"><?= settingText($siteSettings['hero_cta_primary']) ?></a>
                    <a href="pages/claim.php" class="btn btn-ghostlight btn-lg"><?= settingText($siteSettings['hero_cta_secondary']) ?></a>
                </div>
                <?php if ($heroInitials || trim($siteSettings['hero_proof_text']) !== ''): ?>
                <div class="hero-proof">
                    <?php if ($heroInitials): ?>
                    <div class="hero-avatars"><?php foreach ($heroInitials as $initial): ?><span><?= htmlspecialchars(mb_strtoupper(mb_substr($initial, 0, 2))) ?></span><?php endforeach; ?></div>
                    <?php endif; ?>
                    <?php if (trim($siteSettings['hero_proof_text']) !== ''): ?>
                    <p><?= settingText($siteSettings['hero_proof_text']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="hero-side" style="flex-direction:column;align-items:stretch;gap:20px;">
                <img src="<?= htmlspecialchars(settingImageUrl($siteSettings['hero_image'])) ?>" alt="Chapel interior at Velasquez Funeral Service" style="width:100%;height:260px;object-fit:cover;border-radius:16px;border:1px solid rgba(255,255,255,0.18);">
                <div class="hero-emergency sn-card" style="width:100%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-radius:24px;">
                    <div class="he-top"><span class="hf-dot"></span> 24/7 Emergency Dispatch</div>
                    <div class="he-num">0917-123-4567</div>
                    <p>Hearse, chapel &amp; full funeral assistance — one call, any hour.</p>
                    <div class="he-btns">
                        <a href="tel:09171234567" class="btn btn-gold btn-block">Call Now</a>
                        <a href="pages/claim.php" class="btn btn-ghostlight btn-block btn-sm">File a Claim Online</a>
                    </div>
                </div>
            </div>
        </div>
        <a href="#plans" class="scroll-cue" aria-label="Scroll to plans"><span></span></a>
        <?php if ($heroStats): ?>
        <div class="stat-band sn-card fade-in" style="margin-top:56px;border-radius:24px;">
            <?php foreach ($heroStats as $stat): ?>
            <div><strong><?= settingText($stat['value']) ?></strong><span><?= settingText($stat['label']) ?></span></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.hero-grid{display:grid;grid-template-columns:1.15fr 0.85fr;gap:64px;align-items:center;}
.hero-kicker{display:flex;align-items:center;gap:14px;color:var(--gold);font-size:0.78rem;font-weight:700;letter-spacing:2.5px;margin-bottom:20px;}
.hero-kicker span{width:44px;height:2px;background:var(--gold);display:inline-block;}
.hero-copy h1{font-size:3.4rem;line-height:1.08;margin-bottom:20px;color:var(--ink-on-navy);}
.hero-copy h1 em{font-style:italic;color:var(--gold);}
.hero-sub{font-size:1.15rem;color:rgba(255,255,255,0.8);line-height:1.75;max-width:560px;margin-bottom:0;}
.hero-actions{margin-top:32px;display:flex;gap:16px;flex-wrap:wrap;}
.hero-actions .btn{min-width:200px;}
.btn-ghostlight{background:transparent;color:var(--ink-on-navy);border:2px solid rgba(255,255,255,0.5);}
.btn-ghostlight:hover{border-color:var(--ink-on-navy);background:rgba(255,255,255,0.1);transform:translateY(-1px);}
.hero-proof{display:flex;align-items:center;gap:14px;margin-top:36px;}
.hero-avatars{display:flex;}
.hero-avatars span{width:38px;height:38px;border-radius:50%;background:var(--navy-light);color:var(--gold);border:2px solid var(--gold);display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;margin-left:-10px;}
.hero-avatars span:first-child{margin-left:0;}
.hero-proof p{margin:0;font-size:0.9rem;color:rgba(255,255,255,0.7);}
.hero-proof strong{color:var(--ink-on-navy);}
.hero-side{display:flex;justify-content:flex-end;}
.hero-emergency{width:min(340px,100%);background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-radius:var(--radius-lg);padding:30px;}
.he-top{display:flex;align-items:center;gap:10px;font-size:0.75rem;font-weight:700;letter-spacing:1.5px;color:var(--gold);text-transform:uppercase;margin-bottom:10px;}
.he-num{font-family:var(--font-serif);font-size:2rem;font-weight:800;color:var(--ink-on-navy);line-height:1.1;margin-bottom:8px;}
.hero-emergency p{font-size:0.9rem;color:rgba(255,255,255,0.7);margin-bottom:20px;}
.he-btns{display:grid;gap:10px;}
.hf-dot{width:10px;height:10px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 4px rgba(34,197,94,0.2);flex-shrink:0;}
.scroll-cue{position:absolute;left:50%;bottom:26px;transform:translateX(-50%);width:26px;height:44px;border:2px solid rgba(255,255,255,0.4);border-radius:14px;display:flex;justify-content:center;padding-top:8px;z-index:3;}
.scroll-cue span{width:4px;height:9px;border-radius:4px;background:var(--gold);animation:cueDrop 1.8s ease infinite;}
@keyframes cueDrop{0%{transform:translateY(0);opacity:1;}70%{transform:translateY(14px);opacity:0;}100%{transform:translateY(0);opacity:0;}}
.stat-band{display:grid;grid-template-columns:repeat(4,1fr);background:var(--white);border:1px solid var(--gray-200);border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);margin-top:-64px;padding:10px 0;position:relative;}
.stat-band>div{padding:22px 12px;text-align:center;border-left:1px solid var(--gray-200);}
.stat-band>div:first-child{border-left:none;}
.stat-band strong{display:block;font-family:var(--font-serif);font-size:2rem;color:var(--text-strong);line-height:1.1;}
.stat-band span{font-size:0.8rem;color:var(--gray-500);font-weight:600;letter-spacing:0.5px;text-transform:uppercase;}
@media (max-width: 960px){
  .hero-grid{grid-template-columns:1fr;gap:40px;}
  .hero-copy h1{font-size:2.4rem;}
  .hero-side{justify-content:flex-start;}
  .hero-emergency{width:100%;}
  .stat-band{grid-template-columns:repeat(2,1fr);margin-top:-40px;}
  .stat-band>div{border-left:none;padding:16px 8px;}
  .scroll-cue{display:none;}
}
.term-pills{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.term-pill{border:2px solid var(--gray-200);background:var(--white);border-radius:var(--radius);padding:14px;cursor:pointer;font-family:var(--font-sans);font-weight:700;font-size:1rem;color:var(--navy);transition:var(--transition);}
.term-pill span{display:block;font-size:0.75rem;font-weight:500;color:var(--gray-500);margin-top:2px;}
.term-pill.active{border-color:var(--gold);background:#FFFBEB;box-shadow:var(--shadow-sm);}
.calc-row{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.12);font-size:0.92rem;}
.calc-row span:first-child{color:rgba(255,255,255,0.6);}
.calc-row strong{color:var(--ink-on-navy);font-weight:600;}
.how-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:32px;margin-top:48px;position:relative;}
.how-grid::before{content:'';position:absolute;top:32px;left:14%;right:14%;border-top:2px dashed rgba(204,184,119,0.4);}
.how-step{text-align:center;position:relative;padding:0 12px;}
.how-num{width:64px;height:64px;border-radius:50%;background:rgba(204,184,119,0.1);border:2px solid var(--gold);color:var(--gold);font-family:var(--font-serif);font-weight:800;font-size:1.25rem;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;position:relative;z-index:1;}
.how-step h4{color:var(--ink-on-navy);margin:0 0 8px;}
.how-step p{color:rgba(255,255,255,0.65);font-size:0.95rem;margin-bottom:0;}
@media (max-width: 768px){.how-grid{grid-template-columns:1fr;}.how-grid::before{display:none;}}
.chap-row{display:grid;grid-template-columns:1fr 1fr;overflow:hidden;}
.chap-row.flip .chap-img{order:2;}
.chap-img{position:relative;min-height:300px;}
.chap-img img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
.chap-flag{position:absolute;top:16px;left:16px;}
.chap-txt{padding:44px 40px;display:flex;flex-direction:column;justify-content:center;}
.chap-txt h3{margin:10px 0 8px;font-size:1.6rem;}
.chap-meta{display:flex;gap:8px;flex-wrap:wrap;margin:16px 0 24px;}
.chap-meta span{font-size:0.75rem;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:var(--accent);background:var(--cream);border:1px solid var(--gray-200);border-radius:100px;padding:6px 14px;}
.chap-txt .btn{align-self:flex-start;}
@media (max-width: 860px){.chap-row,.chap-row.flip{grid-template-columns:1fr;}.chap-row.flip .chap-img{order:0;}.chap-img{min-height:220px;}.chap-txt{padding:28px 24px;}}
.tm-feature{background:linear-gradient(135deg,#1A2634,#2E4A6B);border-radius:var(--radius-lg);padding:56px 48px;text-align:center;position:relative;overflow:hidden;}
.tm-mark{font-family:var(--font-serif);font-size:5rem;line-height:0.6;color:var(--gold);opacity:0.9;margin-bottom:20px;}
.tm-text{font-family:var(--font-serif);font-style:italic;font-size:1.4rem;line-height:1.7;color:var(--ink-on-navy);max-width:760px;margin:0 auto 20px;}
.tm-stars{color:var(--gold);letter-spacing:6px;font-size:1.1rem;margin-bottom:20px;}
.tm-stars.sm{font-size:0.9rem;margin-bottom:0;}
.tm-who{display:flex;align-items:center;justify-content:center;gap:14px;}
.tm-who.left{justify-content:flex-start;}
.tm-ava{width:52px;height:52px;border-radius:50%;background:var(--gold);color:var(--ink-on-gold);display:inline-flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;border:2px solid rgba(255,255,255,0.25);}
.tm-who strong{display:block;color:var(--ink-on-navy);}
.tm-who span{font-size:0.82rem;color:rgba(255,255,255,0.6);}
.tm-who.left strong{color:var(--text-strong);}
.tm-who.left span{color:var(--gray-500);}
.tm-who.left .tm-ava{border:none;width:44px;height:44px;font-size:0.9rem;}
@media (max-width: 640px){.tm-feature{padding:36px 24px;}.tm-text{font-size:1.1rem;}}
.plans-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-bottom:36px;}
.plans-head h2{margin:10px 0 8px;}
.plan-band{background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);padding:24px 24px 22px;color:var(--ink-on-navy);}
.plan-band h3{color:var(--ink-on-navy);font-size:1.3rem;margin:12px 0 2px;}
.plan-figure{font-family:var(--font-serif);font-size:2rem;font-weight:800;color:var(--gold);line-height:1.1;margin-top:6px;}
.plan-duo{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:14px 0 6px;}
.plan-duo>div{background:var(--cream);border-radius:var(--radius);padding:10px 12px;text-align:center;}
.plan-duo span{display:block;font-size:0.68rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--gray-500);margin-bottom:2px;}
.plan-duo strong{color:var(--text-strong);font-size:0.95rem;}
@media (max-width: 640px){.plans-head{align-items:flex-start;flex-direction:column;}}
@media (max-width: 860px){#plansGrid .plan-card{grid-template-columns:1fr !important;}}
</style>

<!-- Plan Matrix Section -->
<?php if (!plansSectionPaused($siteSettings)): ?>
<section class="sn-section" id="plans" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Pre-Need Packages</span>
            <h2 class="sn-title">Plans for Every Family<br>&amp; <em>Every Budget</em></h2>
            <p class="sn-sub">Lock in today's prices with flexible 5- and 10-year terms. Open any plan for the full breakdown.</p>
            <a href="pages/compare.php" class="btn btn-outline" style="margin-top:20px;">Compare All Plans</a>
        </div>

        <div class="tabs fade-in" id="planTabs" style="margin-bottom: 32px;">
            <button class="tab-btn active" data-filter="all">All Plans</button>
            <button class="tab-btn" data-filter="traditional">Traditional</button>
            <button class="tab-btn" data-filter="cremation">Cremation</button>
            <button class="tab-btn" data-filter="executive">Executive</button>
        </div>

        <div class="fade-in" id="plansGrid" style="display:grid;gap:24px;">
            <?php foreach ($plans as $i => $plan): ?>
                <?php
                    $features = json_decode($plan['features'], true) ?? [];
                    $categoryLabel = ucfirst($plan['category']);
                    $badgeClass = 'badge-' . ($plan['category'] === 'executive' ? 'gold' : ($plan['category'] === 'cremation' ? 'green' : 'navy'));
                ?>
                <div class="sn-card topline plan-card fade-in" data-category="<?= htmlspecialchars($plan['category']) ?>" style="display:grid;grid-template-columns:1.3fr 1fr auto;gap:28px;text-align:left;padding:32px;">
                    <div>
                        <div class="d-flex align-center gap-1" style="margin-bottom:10px;">
                            <?php if ($plan['category'] === 'executive'): ?>
                                <span class="badge badge-gold"><?= $categoryLabel ?></span>
                            <?php else: ?>
                                <span class="badge <?= $badgeClass ?>"><?= $categoryLabel ?></span>
                            <?php endif; ?>
                            <span class="text-xs text-muted">from</span>
                        </div>
                        <h3 style="margin:0 0 4px;font-size:1.5rem;"><?= htmlspecialchars($plan['name']) ?></h3>
                        <div style="font-family:var(--font-serif);font-size:2rem;font-weight:800;color:var(--text-strong);line-height:1.1;">&#8369;<?= number_format($plan['price'], 0) ?></div>
                        <p class="text-sm text-muted" style="margin:10px 0 0; line-height: 1.6;"><?= htmlspecialchars($plan['description']) ?></p>
                    </div>
                    <div>
                        <div class="plan-duo" style="margin:0 0 12px;">
                            <div><span>Monthly · 5yr</span><strong>&#8369;<?= number_format($plan['monthly_5yr'], 0) ?></strong></div>
                            <div><span>Monthly · 10yr</span><strong>&#8369;<?= number_format($plan['monthly_10yr'], 0) ?></strong></div>
                        </div>
                        <ul style="text-align: left; margin:0;">
                            <?php foreach (array_slice($features, 0, 5) as $feature): ?>
                                <li><?= htmlspecialchars($feature) ?></li>
                            <?php endforeach; ?>
                            <li><strong>Casket:</strong> <?= htmlspecialchars($plan['casket_type']) ?></li>
                            <li><strong>Viewing:</strong> <?= $plan['chapel_viewing_days'] ?> Days</li>
                        </ul>
                    </div>
                    <div style="min-width:200px;">
                        <button type="button" class="btn btn-gold btn-block" onclick="openPlanModal(<?= (int)$plan['id'] ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($plans)): ?>
            <div class="text-center" style="padding: 80px 0;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p class="text-muted">Plans are currently being updated. Please check back soon.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($plans)): ?>
        <div class="text-center fade-in" style="margin-top: 40px;">
            <a href="pages/compare.php" class="btn btn-navy btn-lg">
                Compare All Plans Side by Side
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px;">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php else: ?>
<!-- Catalogue paused from Admin > Website Settings. The browse and checkout
     pages show the holding page; this mirrors that so the homepage does not
     advertise plans that cannot be bought yet. -->
<section class="sn-section" id="plans" style="background:var(--surface);">
    <div class="container">
        <div class="sn-band harbor" style="text-align:center;padding:56px 48px;">
            <span class="sn-eyebrow" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.18);color:var(--gold);">Under Construction</span>
            <h2 class="sn-title" style="color:var(--ink-on-navy);">Life Plans Are<br><em style="color:var(--gold);">Being Refreshed</em></h2>
            <p class="sn-sub" style="color:var(--text-on-navy);max-width:620px;margin:0 auto;">We are giving our pre-need catalogue a fresh look. The full plan list, side-by-side comparison and online enrolment are temporarily unavailable while we finish the updates.</p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-top:30px;">
                <a href="pages/contact.php" class="btn btn-gold btn-lg">Talk to Our Team</a>
                <a href="tel:+639171234567" class="btn btn-ghostlight btn-lg">Call 0917-123-4567</a>
            </div>
            <p style="margin:26px 0 0;font-size:0.875rem;color:var(--text-on-navy-muted);">
                Already holding a Velasquez plan? Your policy and payment schedule are in
                <a href="pages/dashboard.php" style="color:var(--gold);font-weight:600;">your dashboard</a>.
            </p>
        </div>
    </div>
</section>
<?php endif; ?>
<?php if (!plansSectionPaused($siteSettings)): ?>
<script>
var PLAN_DETAILS = <?= json_encode($planDetails, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var PLAN_CHECKOUT_BASE = 'checkout.php';
var PLAN_COMPARE_LINK = 'pages/compare.php';
</script>
<?php include 'includes/plan-modal.php'; ?>
<?php endif; ?>

<!-- Pre-Need / At-Need Split -->
<section class="sn-section" style="padding-top:48px;background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Two Ways We Help</span>
            <h2 class="sn-title">Plan Ahead, or Get <em>Help Right Now</em></h2>
            <p class="sn-sub">Pre-need peace of mind for tomorrow, and at-need response for today.</p>
        </div>
        <div class="grid-2 fade-in" style="gap:24px;">
            <div class="sn-card topline" style="padding:40px 36px;">
                <span class="text-xs" style="font-weight:700;letter-spacing:2px;color:var(--gold-dark);text-transform:uppercase;">Pre-Need &middot; Plan Ahead</span>
                <h3 style="margin:10px 0 8px;">Secure Tomorrow's Peace, Today</h3>
                <p class="text-muted" style="margin-bottom:20px;">Lock in today's prices with flexible terms your family can afford — and transfer coverage whenever life changes.</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="pages/plans.php" class="btn btn-gold">Browse Life Plans</a>
                    <a href="pages/compare.php" class="btn btn-outline">Compare</a>
                </div>
            </div>
            <div class="sn-card" style="padding:40px 36px;background:linear-gradient(135deg,#1A2634,#2E4A6B);border:none;color:var(--ink-on-navy);">
                <span class="text-xs" style="font-weight:700;letter-spacing:2px;color:var(--gold);text-transform:uppercase;">At-Need &middot; Help Right Now</span>
                <h3 style="margin:10px 0 8px;color:var(--ink-on-navy);">Facing a Loss Today?</h3>
                <p style="margin-bottom:20px;color:rgba(255,255,255,0.7);">Hearse dispatch, chapel viewing, and full funeral assistance — our team answers in minutes, day or night.</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="tel:09171234567" class="btn btn-gold">Call 0917-123-4567</a>
                    <a href="pages/claim.php" class="btn btn-white">File a Claim</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="sn-section" style="background:var(--surface);padding:84px 0;position:relative;overflow:hidden;">
    <div class="container" style="position:relative;z-index:1;">
        <div class="sn-band harbor fade-in" style="padding:64px 56px;">
        <div class="sn-center" style="margin-bottom:40px;">
            <span class="sn-eyebrow" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.18);color:#CCB877;">Simple Process</span>
            <h2 class="sn-title" style="color:var(--ink-on-navy);">Planning Ahead in <em style="color:#CCB877;">3 Easy Steps</em></h2>
            <p class="sn-sub" style="color:rgba(255,255,255,0.7);">No paperwork maze, no pressure — just a clear path to peace of mind.</p>
        </div>
        <div class="how-grid fade-in">
            <div class="how-step">
                <div class="sn-stepnum">01</div>
                <h4>Choose Your Plan</h4>
                <p>Browse traditional, cremation, or executive packages — or talk to our team for guidance.</p>
            </div>
            <div class="how-step">
                <div class="sn-stepnum">02</div>
                <h4>Pay Your Way</h4>
                <p>Spot-cash discounts or easy 5- and 10-year terms via GCash, bank transfer, or cash.</p>
            </div>
            <div class="how-step">
                <div class="sn-stepnum">03</div>
                <h4>Rest Assured</h4>
                <p>Fully transferable plans with 24/7 support — we handle everything when the time comes.</p>
            </div>
        </div>
        <div class="text-center fade-in" style="margin-top:44px;">
            <a href="pages/plans.php" class="btn btn-gold btn-lg">Start with Step 1: Choose a Plan</a>
        </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Why Velasquez</span>
            <h2 class="sn-title">Why Choose <em>Velasquez</em></h2>
            <p class="sn-sub">We are committed to providing the best funeral service experience for Filipino families. Here's what sets us apart.</p>
        </div>

        <div class="fade-in" style="margin-top:8px;display:grid;grid-template-columns:repeat(2,1fr);gap:24px;">
            <!-- Trusted Since 1990 -->
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Since 1990</span>
                    <h4 style="font-size: 1.05rem; margin-bottom: 8px; color: var(--text-strong);">Trusted Since 1990</h4>
                    <p class="text-sm text-muted" style="margin-bottom: 0;">Over 30 years of serving Filipino families with dignity, honor, and unwavering commitment to excellence in every service we provide.</p>
                </div>
            </div>

            <!-- Flexible Payment -->
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Pay Your Way</span>
                    <h4 style="font-size: 1.05rem; margin-bottom: 8px; color: var(--text-strong);">Flexible Payment</h4>
                    <p class="text-sm text-muted" style="margin-bottom: 0;">Pay via GCash, Bank Transfer, or Cash. Choose from 5-year or 10-year installment plans that fit your budget and timeline.</p>
                </div>
            </div>

            <!-- Transferable Plans -->
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Family Cover</span>
                    <h4 style="font-size: 1.05rem; margin-bottom: 8px; color: var(--text-strong);">Transferable Plans</h4>
                    <p class="text-sm text-muted" style="margin-bottom: 0;">Plans can be transferred to family members, ensuring your investment is always protected and your loved ones are covered.</p>
                </div>
            </div>

            <!-- 24/7 Support -->
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Always Open</span>
                    <h4 style="font-size: 1.05rem; margin-bottom: 8px; color: var(--text-strong);">24/7 Support</h4>
                    <p class="text-sm text-muted" style="margin-bottom: 0;">Round the clock assistance whenever you need us. We are always here for your family, day or night, rain or shine.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Calculator Section -->
<section class="sn-section" id="calculator" style="background:var(--surface);">
    <div class="container">
        <div class="sn-band linen fade-in" style="padding:56px 48px;">
        <div class="sn-center fade-in" style="margin-bottom:36px;">
            <span class="sn-eyebrow">Cost Estimator</span>
            <h2 class="sn-title">Payment <em>Calculator</em></h2>
            <p class="sn-sub">Estimate your monthly payments based on the plan you choose. See how affordable peace of mind can be.</p>
            <?php if (plansSectionPaused($siteSettings)): ?>
            <p class="sn-sub" style="margin-top:14px;color:var(--accent);font-weight:600;">Online enrolment is paused while we refresh the catalogue &mdash; use this for budgeting, then talk to our team to finalise.</p>
            <?php endif; ?>
        </div>

        <div class="fade-in" style="max-width: 960px; margin: 0 auto;">
            <div class="sn-card topline" style="padding:0;overflow:hidden;">
                <div class="grid-2" style="gap:0;">
                    <div style="padding:36px;">
                        <div style="text-align:center;margin-bottom:24px;">
                            <div style="width:56px;height:56px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="2" width="16" height="20" rx="2"/>
                                    <line x1="8" y1="6" x2="16" y2="6"/>
                                    <line x1="16" y1="14" x2="16" y2="14.01"/>
                                    <line x1="12" y1="14" x2="12" y2="14.01"/>
                                    <line x1="8" y1="14" x2="8" y2="14.01"/>
                                </svg>
                            </div>
                            <h3 style="font-size:1.3rem;margin-bottom:4px;color:var(--navy);">Plan Cost Estimator</h3>
                            <p class="text-sm text-muted" style="margin-bottom:0;">Pick a plan and term to see your personalized breakdown.</p>
                        </div>

                        <div class="form-group">
                            <label for="calcPlan" style="font-weight:600;color:var(--navy);">Select a Plan</label>
                            <select class="form-control" id="calcPlan" onchange="calculatePayment()" style="font-size:1rem;padding:14px 16px;">
                                <option value="">-- Choose a Plan --</option>
                                <?php foreach ($plans as $plan): ?>
                                    <option value="<?= $plan['id'] ?>"
                                        data-price="<?= $plan['price'] ?>"
                                        data-monthly-5yr="<?= $plan['monthly_5yr'] ?>"
                                        data-monthly-10yr="<?= $plan['monthly_10yr'] ?>"
                                        data-downpayment="<?= htmlspecialchars($plan['downpayment'] ?? '') ?>"
                                        data-spot="<?= htmlspecialchars($plan['spot_cash'] ?? '') ?>">
                                        <?= htmlspecialchars($plan['name']) ?> — &#8369;<?= number_format($plan['price'], 0) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:8px;">
                            <label style="font-weight:600;color:var(--navy);">Payment Term</label>
                            <div class="term-pills">
                                <button type="button" class="term-pill active" data-term="5" onclick="setCalcTerm(5, this)">5 Years<span>60 monthly payments</span></button>
                                <button type="button" class="term-pill" data-term="10" onclick="setCalcTerm(10, this)">10 Years<span>120 monthly payments</span></button>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:8px;">
                            <label style="font-weight:600;color:var(--navy);">Payment Frequency</label>
                            <div class="term-pills" style="grid-template-columns:1fr 1fr 1fr;">
                                <button type="button" class="term-pill freq-pill active" data-freq="1" onclick="setCalcFreq(1, this)">Monthly</button>
                                <button type="button" class="term-pill freq-pill" data-freq="3" onclick="setCalcFreq(3, this)">Quarterly</button>
                                <button type="button" class="term-pill freq-pill" data-freq="12" onclick="setCalcFreq(12, this)">Annual</button>
                            </div>
                        </div>
                        <p class="text-xs text-muted" style="margin-bottom:0;">Pay via GCash, bank transfer, or cash at any branch.</p>
                    </div>
                    <div style="background:var(--navy);padding:36px;color:var(--ink-on-navy);display:flex;flex-direction:column;justify-content:center;">
                        <div id="calcEmpty" style="text-align:center;padding:24px 0;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.35)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
                                <rect x="4" y="2" width="16" height="20" rx="2"/>
                                <line x1="8" y1="6" x2="16" y2="6"/>
                                <line x1="16" y1="14" x2="16" y2="14.01"/>
                                <line x1="12" y1="14" x2="12" y2="14.01"/>
                                <line x1="8" y1="14" x2="8" y2="14.01"/>
                            </svg>
                            <p style="margin-bottom:0;color:rgba(255,255,255,0.6);font-size:0.95rem;">Select a plan to see<br>your personalized breakdown.</p>
                        </div>
                        <div id="calcResults" style="display:none;">
                            <p class="text-xs" style="color:rgba(255,255,255,0.55);margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;" id="calcFreqEyebrow">Your Estimated Monthly</p>
                            <p id="calcMonthly" style="font-family:var(--font-serif);font-size:2.6rem;font-weight:800;color:var(--gold);margin:0;line-height:1;">₱0</p>
                            <p id="calcPerMo" class="text-xs" style="color:rgba(255,255,255,0.55);margin:6px 0 20px;"></p>
                            <div class="calc-row"><span>Total contract price</span><strong id="calcTotal">—</strong></div>
                            <div class="calc-row" id="calcDownRow"><span>Downpayment today</span><strong id="calcDown">—</strong></div>
                            <div class="calc-row"><span>Amount financed</span><strong id="calcFinanced">—</strong></div>
                            <div class="calc-row"><span>Term</span><strong id="calcTermLabel">—</strong></div>
                            <div class="calc-row"><span>Number of payments</span><strong id="calcCount">—</strong></div>
                            <div class="calc-row" id="calcSpotRow"><span>Spot cash option</span><strong><span id="calcSpot">—</span> <span id="calcSave" class="badge badge-gold" style="font-size:0.65rem;"></span></strong></div>
                            <div class="calc-row" style="border-bottom:none;"><span>Total of all payments</span><strong id="calcGrand">—</strong></div>
                            <a id="calcCta" href="<?= plansSectionPaused($siteSettings) ? 'pages/contact.php' : 'checkout.php' ?>" class="btn btn-gold btn-block" style="margin-top:24px;"><?= plansSectionPaused($siteSettings) ? 'Talk to Our Team' : 'Get This Plan' ?></a>
                            <button type="button" class="btn btn-outline btn-block btn-sm" style="margin-top:10px;border-color:rgba(255,255,255,0.3);color:var(--ink-on-navy);" onclick="toggleCalcSchedule(this)">View Full Payment Schedule</button>
                            <p style="font-size:0.75rem;color:rgba(255,255,255,0.45);margin:12px 0 0;text-align:center;"><?= plansSectionPaused($siteSettings) ? 'Estimates use the official plan schedule; final terms are confirmed by our team.' : 'Estimates use the official plan schedule; final terms are confirmed at checkout.' ?></p>
                            <div id="calcScheduleWrap" style="display:none;margin-top:12px;max-height:300px;overflow:auto;border:1px solid rgba(255,255,255,0.15);border-radius:var(--radius);">
                                <table style="width:100%;font-size:0.8rem;">
                                    <thead style="position:sticky;top:0;background:var(--navy-light);">
                                        <tr><th style="padding:8px 10px;text-align:left;color:var(--ink-on-navy);background:none;border:none;">#</th><th style="padding:8px 10px;text-align:left;color:var(--ink-on-navy);background:none;border:none;">Due</th><th style="padding:8px 10px;text-align:right;color:var(--ink-on-navy);background:none;border:none;">Payment</th><th style="padding:8px 10px;text-align:right;color:var(--ink-on-navy);background:none;border:none;">Balance</th></tr>
                                    </thead>
                                    <tbody id="calcScheduleBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- Our Chapels Teaser Section -->
<?php if (!chapelsSectionPaused($siteSettings)): ?>
<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Visit Us</span>
            <h2 class="sn-title">Our <em>Chapels</em></h2>
            <p class="sn-sub">Visit any of our state-of-the-art chapels across Central Luzon. Each facility is designed to provide comfort and dignity.</p>
        </div>

        <div style="margin-top: 40px;display:flex;flex-direction:column;gap:24px;">
            <div class="sn-card topline fade-in chap-row" style="padding:0;">
                <div class="chap-img">
                    <img src="/velasquez-lifeplan/img/chapel-anne.jpg" alt="Guagua Chapel interior">
                    <span class="badge badge-gold chap-flag">Main Branch</span>
                </div>
                <div class="chap-txt">
                    <span class="text-xs" style="font-weight:700;letter-spacing:2px;color:var(--gold-dark);text-transform:uppercase;">Flagship Chapel</span>
                    <h3>Guagua Chapel</h3>
                    <p class="text-muted">Three viewing halls, 500+ capacity, and state-of-the-art audio-visual systems in the heart of Sto. Cristo — our most complete facility for grand memorial services.</p>
                    <div class="chap-meta">
                        <span>3 Viewing Halls</span><span>500+ Guests</span><span>24/7 Access</span>
                    </div>
                    <a href="pages/chapels.php" class="btn btn-outline">View Chapel</a>
                </div>
            </div>
            <div class="card hover-lift fade-in chap-row flip">
                <div class="chap-img">
                    <img src="/velasquez-lifeplan/img/chapel-hall.jpg" alt="Gold Memorial Hall">
                    <span class="badge badge-gold chap-flag">Premium</span>
                </div>
                <div class="chap-txt">
                    <span class="text-xs" style="font-weight:700;letter-spacing:2px;color:var(--gold-dark);text-transform:uppercase;">Executive Venue</span>
                    <h3>Gold Memorial Hall</h3>
                    <p class="text-muted">Executive lounges, live-streaming for relatives abroad, and premium catering — a serene, private setting for intimate farewells of any size.</p>
                    <div class="chap-meta">
                        <span>Live Streaming</span><span>Private Lounges</span><span>Catering</span>
                    </div>
                    <a href="pages/chapels.php" class="btn btn-outline">View Chapel</a>
                </div>
            </div>
        </div>
        <p class="text-center text-muted fade-in" style="margin-top:28px;">Also welcoming families at our <strong>Angeles City Chapel</strong> — <a href="pages/chapels.php" style="color:var(--gold-dark);font-weight:700;">see all chapels &rarr;</a></p>
    </div>
</section>
<?php else: ?>
<!-- Chapel paused from Admin > Website Settings. Mirrors the holding page so
     the homepage stops advertising a facility that is not open yet. -->
<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-band harbor" style="text-align:center;padding:56px 48px;">
            <span class="sn-eyebrow" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.18);color:var(--gold);">Under Construction</span>
            <h2 class="sn-title" style="color:var(--ink-on-navy);">Our Chapel Is<br><em style="color:var(--gold);">Still Being Built</em></h2>
            <p class="sn-sub" style="color:var(--text-on-navy);max-width:620px;margin:0 auto;">Our new chapel facility is currently under construction and is not yet open to the public. Chapel, event hall and venue enquiries are temporarily unavailable while the build is completed.</p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-top:30px;">
                <a href="pages/contact.php" class="btn btn-gold btn-lg">Talk to Our Team</a>
                <a href="tel:+639171234567" class="btn btn-ghostlight btn-lg">Call 0917-123-4567</a>
            </div>
            <p style="margin:26px 0 0;font-size:0.875rem;color:var(--text-on-navy-muted);">
                Meanwhile our casket selection, grief support and eServices are open as usual.
            </p>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Section -->
<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Testimonials</span>
            <h2 class="sn-title">What Families <em>Say</em></h2>
            <p class="sn-sub">Hear from the families we have had the privilege of serving during their most difficult times.</p>
        </div>

        <div class="sn-band harbor tm-feature fade-in" style="text-align:center;">
            <div class="tm-mark">&ldquo;</div>
            <p class="sn-quote tm-text" style="color:var(--ink-on-navy);max-width:760px;margin:0 auto 20px;">Velasquez Funeral Service made an incredibly difficult time so much easier for our family. Their compassion and professionalism were evident from the first phone call to the memorial service. We felt truly cared for.</p>
            <div class="tm-stars">★★★★★</div>
            <div class="tm-who"><span class="tm-ava">JR</span><div><strong>Juan Reyes</strong><span>Sto. Cristo, Guagua, Pampanga</span></div></div>
        </div>
        <div class="fade-in" style="margin-top:24px;display:grid;grid-template-columns:repeat(2,1fr);gap:24px;">
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="tm-stars sm">★★★★★</div>
                <p style="font-size:0.95rem;line-height:1.8;color:var(--gray-500);margin:12px 0 20px;font-style:italic;">"We pre-need with Velasquez 10 years ago and when the time came, everything was seamless. No stress, no hidden charges. The staff handled everything with so much respect. Highly recommended!"</p>
                <div class="tm-who left"><span class="tm-ava">MG</span><div><strong>Maria Garcia</strong><span>Angeles City, Pampanga</span></div></div>
            </div>
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="tm-stars sm">★★★★★</div>
                <p style="font-size:0.95rem;line-height:1.8;color:var(--gray-500);margin:12px 0 20px;font-style:italic;">"The live-streaming service was a blessing. Our relatives in the US were able to attend my lola's funeral virtually. Velasquez truly understands the needs of modern Filipino families."</p>
                <div class="tm-who left"><span class="tm-ava">AD</span><div><strong>Ana Dela Cruz</strong><span>Mabalacat City, Pampanga</span></div></div>
            </div>
        </div>
    </div>
</section>

<!-- Emergency Band -->
<section class="sn-section" style="background:var(--bg);padding:48px 0;">
    <div class="container fade-in">
        <div class="sn-band linen" style="padding:34px 36px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;background:linear-gradient(135deg,#CBB37C 0%,#CCB877 50%,#C0AC85 100%);">
        <div style="display:flex;align-items:center;gap:16px;">
            <div class="sn-icon" style="width:52px;height:52px;border-radius:16px;margin-bottom:0;flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            </div>
            <div>
                <div style="font-family:var(--font-serif);font-weight:800;font-size:1.3rem;color:var(--navy);line-height:1.2;">Need immediate assistance?</div>
                <div style="color:rgba(26,38,52,0.75);font-size:0.95rem;">Our 24/7 response team is one call away — day or night.</div>
            </div>
        </div>
        <a href="tel:09171234567" class="btn btn-navy btn-lg">Call 0917-123-4567</a>
        </div>
    </div>
</section>

<!-- Contact / CTA Section -->
<section class="sn-section" id="contact" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">We're Here to Help</span>
            <h2 class="sn-title">Talk to Our <em>Family Care Team</em></h2>
            <p class="sn-sub">Free consultation, no obligations — just honest guidance for your family's future.</p>
        </div>
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 32px; align-items: stretch;" class="fade-in">
            <div class="sn-band harbor" style="border:none;padding:44px 40px;border-radius:24px;">
                <h2 style="margin-bottom: 16px; font-size: 2rem; color: var(--ink-on-navy);">Need Help Choosing a Plan?</h2>
                <p style="font-size: 1.0625rem; color: rgba(255,255,255,0.7); margin-bottom: 36px; line-height: 1.8;">Our experienced team is ready to assist you in finding the perfect life plan for your family. Reach out today for a free, no-obligation consultation.</p>

                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(204,184,119,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CCB877" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs" style="color: rgba(255,255,255,0.45); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Hotline</p>
                            <p style="color: var(--white); margin-bottom: 0; font-weight: 600; font-size: 1.05rem;">0917-123-4567</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(204,184,119,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CCB877" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs" style="color: rgba(255,255,255,0.45); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Address</p>
                            <p style="color: var(--white); margin-bottom: 0; font-weight: 600; font-size: 1.05rem;">Sto. Cristo, Guagua, Pampanga, Philippines</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(204,184,119,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CCB877" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs" style="color: rgba(255,255,255,0.45); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Office Hours</p>
                            <p style="color: var(--white); margin-bottom: 0; font-weight: 600; font-size: 1.05rem;">Mon - Sat: 8:00 AM - 5:00 PM</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(204,184,119,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CCB877" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs" style="color: rgba(255,255,255,0.45); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Email</p>
                            <p style="color: var(--white); margin-bottom: 0; font-weight: 600; font-size: 1.05rem;">info@velasquezfuneral.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sn-card topline" style="padding:40px 36px;">
                <span class="sn-eyebrow" style="margin-bottom:10px;">Write To Us</span>
                <h3 style="margin-bottom: 20px; color: var(--navy); font-size: 1.25rem;">Send Us a Message</h3>
                <?php if ($homeContactSent): ?>
                    <div style="background:rgba(34,197,94,0.12);border:1px solid #22c55e;border-radius:var(--radius);padding:20px;color:var(--navy);">
                        <strong>Message sent!</strong> Thank you for reaching out — we'll get back to you within 24 hours.
                    </div>
                <?php else: ?>
                <form action="home.php#contact" method="POST">
                    <input type="hidden" name="home_contact" value="1">
                    <div class="form-group">
                        <label style="color:var(--navy);font-size:0.85rem;font-weight:600;">Full Name</label>
                        <input type="text" name="hc_name" class="form-control" placeholder="Juan Dela Cruz" style="padding:12px 16px;" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label style="color:var(--navy);font-size:0.85rem;font-weight:600;">Phone Number</label>
                            <input type="tel" name="hc_phone" class="form-control" placeholder="0917-123-4567" style="padding:12px 16px;" required>
                        </div>
                        <div class="form-group">
                            <label style="color:var(--navy);font-size:0.85rem;font-weight:600;">Email Address</label>
                            <input type="email" name="hc_email" class="form-control" placeholder="juan@email.com" style="padding:12px 16px;" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="color:var(--navy);font-size:0.85rem;font-weight:600;">Subject</label>
                        <select class="form-control" style="padding:12px 16px;">
                            <option value="">Select a topic</option>
                            <option value="plan-inquiry">Plan Inquiry</option>
                            <option value="payment">Payment Question</option>
                            <option value="consultation">Free Consultation</option>
                            <option value="complaint">Feedback / Concern</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="color:var(--navy);font-size:0.85rem;font-weight:600;">Message</label>
                        <textarea name="hc_message" class="form-control" rows="4" placeholder="How can we help you?" style="resize:vertical;padding:12px 16px;" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold btn-block" style="padding: 14px 24px; font-size: 1rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        Send Message
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Floating Emergency Button -->
<a href="tel:09171234567" style="position: fixed; bottom: 24px; right: 24px; z-index: 999; display: flex; align-items: center; gap: 10px; background: #dc2626; color: var(--white); padding: 14px 24px; border-radius: 100px; font-weight: 600; font-size: 0.9rem; box-shadow: 0 8px 24px rgba(220,38,38,0.4); transition: all 0.3s ease; text-decoration: none; animation: pulse-emergency 2s infinite;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
    </svg>
    Emergency 24/7
</a>

<style>
@keyframes pulse-emergency {
    0% { box-shadow: 0 8px 24px rgba(220,38,38,0.4); }
    50% { box-shadow: 0 8px 32px rgba(220,38,38,0.6); }
    100% { box-shadow: 0 8px 24px rgba(220,38,38,0.4); }
}
</style>

<script>
// Plan Tab Filtering
(function() {
    var tabs = document.querySelectorAll('#planTabs .tab-btn');
    var cards = document.querySelectorAll('#plansGrid .plan-card');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var filter = this.getAttribute('data-filter');

            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');

            cards.forEach(function(card) {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
})();

// Payment Calculator (plan + term + frequency + full amortization schedule)
var calcTerm = 5;
var calcFreq = 1; // months per payment: 1 = monthly, 3 = quarterly, 12 = annual

function peso0(n) {
    n = Number(n);
    if (!isFinite(n)) return '--';
    return '\u20B1' + n.toLocaleString('en-PH', { maximumFractionDigits: 0 });
}

function setCalcTerm(years, btn) {
    calcTerm = years;
    document.querySelectorAll('.term-pill[data-term]').forEach(function (b) { b.classList.remove('active'); });
    if (btn) btn.classList.add('active');
    calculatePayment();
}

function setCalcFreq(monthsPerPay, btn) {
    calcFreq = monthsPerPay;
    document.querySelectorAll('.term-pill[data-freq]').forEach(function (b) { b.classList.remove('active'); });
    if (btn) btn.classList.add('active');
    calculatePayment();
}

function calcFreqMeta() {
    if (calcFreq === 12) return { label: 'Annual', per: 'per year', step: 12 };
    if (calcFreq === 3) return { label: 'Quarterly', per: 'per quarter', step: 3 };
    return { label: 'Monthly', per: 'per month', step: 1 };
}

function toggleCalcSchedule(btn) {
    var wrap = document.getElementById('calcScheduleWrap');
    var open = wrap.style.display === 'none' || wrap.style.display === '';
    wrap.style.display = open ? 'block' : 'none';
    btn.textContent = open ? 'Hide Payment Schedule' : 'View Full Payment Schedule';
}

function calcDueLabel(d) {
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return months[d.getMonth()] + ' ' + d.getFullYear();
}

function calculatePayment() {
    var sel = document.getElementById('calcPlan');
    var resultsDiv = document.getElementById('calcResults');
    var emptyDiv = document.getElementById('calcEmpty');

    if (!sel || !sel.value) {
        resultsDiv.style.display = 'none';
        emptyDiv.style.display = 'block';
        return;
    }

    var opt = sel.options[sel.selectedIndex];
    var price = parseFloat(opt.getAttribute('data-price'));
    var m5 = parseFloat(opt.getAttribute('data-monthly-5yr'));
    var m10 = parseFloat(opt.getAttribute('data-monthly-10yr'));
    var down = parseFloat(opt.getAttribute('data-downpayment'));
    var spot = parseFloat(opt.getAttribute('data-spot'));
    var monthly = calcTerm === 5 ? m5 : m10;
    var months = calcTerm === 5 ? 60 : 120;
    var fm = calcFreqMeta();
    var perPay = monthly * fm.step;
    var count = months / fm.step;
    var downVal = (isFinite(down) && down > 0) ? down : 0;
    var financed = isFinite(price) ? Math.max(0, price - downVal) : 0;

    document.getElementById('calcFreqEyebrow').textContent = 'Your Estimated ' + fm.label;
    document.getElementById('calcMonthly').textContent = peso0(perPay);
    document.getElementById('calcPerMo').textContent = fm.per + ' x ' + count + (count === 1 ? ' payment' : ' payments');
    document.getElementById('calcTotal').textContent = peso0(price);

    var downRow = document.getElementById('calcDownRow');
    if (downVal > 0) {
        document.getElementById('calcDown').textContent = peso0(downVal);
        downRow.style.display = '';
    } else {
        downRow.style.display = 'none';
    }

    document.getElementById('calcFinanced').textContent = peso0(financed);
    document.getElementById('calcTermLabel').textContent = calcTerm + ' Years';
    document.getElementById('calcCount').textContent = count + ' ' + fm.label.toLowerCase() + ' payments';

    var spotRow = document.getElementById('calcSpotRow');
    if (isFinite(spot) && spot > 0 && isFinite(price)) {
        document.getElementById('calcSpot').textContent = peso0(spot);
        var save = price - spot;
        document.getElementById('calcSave').textContent = save > 0 ? 'Save ' + peso0(save) : '';
        spotRow.style.display = '';
    } else {
        spotRow.style.display = 'none';
    }

    document.getElementById('calcGrand').textContent = peso0(downVal + (isFinite(perPay) ? perPay * count : 0));
    document.getElementById('calcCta').href = <?= plansSectionPaused($siteSettings) ? "'pages/contact.php'" : "'checkout.php?plan_id=' + encodeURIComponent(sel.value)" ?>;

    var tbody = document.getElementById('calcScheduleBody');
    var schedHtml = '';
    var balance = isFinite(price) ? price : 0;
    var rowStyle = 'padding:8px 10px;border-top:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.85);';
    var numStyle = 'padding:8px 10px;border-top:1px solid rgba(255,255,255,0.08);color:#fff;text-align:right;font-weight:600;';
    if (downVal > 0) {
        balance = Math.max(0, balance - downVal);
        schedHtml += '<tr><td style="' + rowStyle + '">DP</td><td style="' + rowStyle + '">Today (downpayment)</td><td style="' + numStyle + '">' + peso0(downVal) + '</td><td style="' + numStyle + '">' + peso0(balance) + '</td></tr>';
    }
    if (isFinite(perPay) && count > 0) {
        var d = new Date();
        d.setDate(1);
        d.setMonth(d.getMonth() + 1);
        for (var i = 1; i <= count; i++) {
            var amt = (i === count) ? balance : Math.min(perPay, balance);
            balance = Math.max(0, Math.round(balance - amt));
            schedHtml += '<tr><td style="' + rowStyle + '">' + i + '</td><td style="' + rowStyle + '">' + calcDueLabel(d) + '</td><td style="' + numStyle + '">' + peso0(amt) + '</td><td style="' + numStyle + '">' + peso0(balance) + '</td></tr>';
            d.setMonth(d.getMonth() + fm.step);
        }
    }
    tbody.innerHTML = schedHtml;
    document.getElementById('calcScheduleWrap').style.display = 'none';
    var schedBtn = document.querySelector('#calcResults .btn-outline');
    if (schedBtn) schedBtn.textContent = 'View Full Payment Schedule';

    resultsDiv.style.display = 'block';
    emptyDiv.style.display = 'none';
}

// Intersection Observer for scroll animations
(function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-in, .slide-up').forEach(function(el) {
        observer.observe(el);
    });
})();

// Responsive CTA grid
(function() {
    var ctaGrid = document.querySelector('#contact .container > div');
    if (ctaGrid) {
        var mq = window.matchMedia('(max-width: 768px)');
        function handleMobile(e) {
            if (e.matches) {
                ctaGrid.style.gridTemplateColumns = '1fr';
                ctaGrid.style.gap = '32px';
            } else {
                ctaGrid.style.gridTemplateColumns = '1.2fr 1fr';
                ctaGrid.style.gap = '64px';
            }
        }
        mq.addListener(handleMobile);
        handleMobile(mq);
    }
})();
</script>

<?php include 'includes/footer.php'; ?>

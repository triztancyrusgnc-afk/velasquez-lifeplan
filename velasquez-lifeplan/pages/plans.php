<?php
$pageTitle = 'Our Life Plans';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/under_construction.php';
guardPlansSection($siteSettings);

$plans = [];
try {
    $stmt = $pdo->prepare('SELECT * FROM plans WHERE is_active = 1 ORDER BY price ASC');
    $stmt->execute();
    $plans = $stmt->fetchAll();
} catch (PDOException $e) {
    // table may not exist yet; fall through with empty array
}

function formatPeso($amount) {
    return '&#8369;' . number_format($amount, 2, '.', ',');
}

function getBadgeClass($category) {
    $cat = strtolower(trim($category ?? ''));
    if ($cat === 'cremation') return 'badge-green';
    if ($cat === 'executive') return 'badge-gold';
    return 'badge-navy';
}
?>

<section class="sn-section" style="background:var(--bg);padding:60px 0 40px;">
    <div class="container">
        <div class="sn-band harbor fade-in" style="text-align:center;padding:56px 48px;">
        <span class="sn-eyebrow fade-in" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.18);color:#CCB877;">Life Planning</span>
        <h1 class="sn-title fade-in" style="color:var(--ink-on-navy);">Our <em style="color:#CCB877;">Life Plans</em></h1>
        <p class="sn-sub fade-in" style="color:rgba(255,255,255,0.75);max-width:640px;margin:0 auto;">Find the perfect plan to protect your family's future. Secure their peace of mind today.</p>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Browse Packages</span>
            <h2 class="sn-title">Choose Your <em>Plan</em></h2>
            <p class="sn-sub">We offer flexible life plans tailored to every family's needs and budget.</p>
        </div>

        <!-- Category Tabs -->
        <div class="text-center mb-2" style="margin-bottom:32px;">
            <div id="planTabs" style="display:inline-flex;gap:8px;background:var(--cream);padding:6px;border-radius:var(--radius-lg);">
                <button class="btn btn-sm plan-tab active" data-category="all" onclick="filterPlans('all', this)" style="border-radius:var(--radius);padding:10px 24px;">All</button>
                <button class="btn btn-sm btn-outline plan-tab" data-category="traditional" onclick="filterPlans('traditional', this)" style="border-radius:var(--radius);padding:10px 24px;">Traditional</button>
                <button class="btn btn-sm btn-outline plan-tab" data-category="cremation" onclick="filterPlans('cremation', this)" style="border-radius:var(--radius);padding:10px 24px;">Cremation</button>
                <button class="btn btn-sm btn-outline plan-tab" data-category="executive" onclick="filterPlans('executive', this)" style="border-radius:var(--radius);padding:10px 24px;">Executive</button>
            </div>
        </div>

        <!-- Plan Cards Grid -->
        <div class="fade-in" id="plansGrid" style="display:grid;gap:24px;">

            <?php if (empty($plans)): ?>
                <div class="text-center" style="grid-column:1/-1;padding:60px 0;">
                    <div style="width:80px;height:80px;border-radius:50%;background:var(--cream);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h4>Plans Coming Soon</h4>
                    <p class="text-muted">We are currently finalizing our life plans. Please check back soon or contact us for more information.</p>
                    <a href="contact.php" class="btn btn-gold">Contact Us</a>
                </div>
            <?php else: ?>
                <?php foreach ($plans as $plan):
                    $category = $plan['category'] ?? 'traditional';
                    $badgeClass = getBadgeClass($category);
                    $features = [];
                    if (!empty($plan['features'])) {
                        $decoded = json_decode($plan['features'], true);
                        if (is_array($decoded)) $features = $decoded;
                    }
                    $spotCash = $plan['spot_cash'] ?? $plan['spot_cash_price'] ?? null;
                    $monthly5 = $plan['monthly_5yr'] ?? null;
                    $monthly10 = $plan['monthly_10yr'] ?? null;
                    $casketType = $plan['casket_type'] ?? null;
                    $chapelDays = $plan['chapel_viewing_days'] ?? null;
                ?>
                <div class="sn-card topline plan-card fade-in" data-category="<?= htmlspecialchars($category) ?>" style="display:grid;grid-template-columns:1.4fr 1fr auto;gap:28px;text-align:left;">
                    <div>
                        <div class="d-flex align-center gap-1 mb-1" style="margin-bottom:10px;">
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst(htmlspecialchars($category)) ?></span>
                            <?php if (!empty($plan['is_featured'])): ?>
                                <span class="badge badge-gold">Featured</span>
                            <?php endif; ?>
                        </div>
                        <h4 class="card-title" style="margin-bottom:8px;"><?= htmlspecialchars($plan['name'] ?? 'Life Plan') ?></h4>

                        <div class="card-price" style="margin-bottom:8px;"><?= formatPeso($plan['price'] ?? 0) ?></div>

                        <?php if ($spotCash): ?>
                        <div style="margin-bottom:12px;">
                            <span style="text-decoration:line-through;color:var(--gray-400);font-size:0.875rem;"><?= formatPeso($plan['price'] ?? 0) ?></span>
                            <span style="color:var(--green);font-weight:600;font-size:0.875rem;margin-left:6px;">Spot Cash: <?= formatPeso($spotCash) ?></span>
                        </div>
                        <?php endif; ?>

                        <div style="display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
                            <?php if ($monthly5): ?>
                            <div style="flex:1;min-width:100px;background:var(--cream);padding:10px 12px;border-radius:var(--radius);text-align:center;">
                                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--gray-500);margin-bottom:2px;">5 Years</div>
                                <div style="font-weight:700;color:var(--navy);font-size:0.95rem;"><?= formatPeso($monthly5) ?>/mo</div>
                            </div>
                            <?php endif; ?>
                            <?php if ($monthly10): ?>
                            <div style="flex:1;min-width:100px;background:var(--cream);padding:10px 12px;border-radius:var(--radius);text-align:center;">
                                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--gray-500);margin-bottom:2px;">10 Years</div>
                                <div style="font-weight:700;color:var(--navy);font-size:0.95rem;"><?= formatPeso($monthly10) ?>/mo</div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <?php if ($casketType): ?>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;padding:8px 12px;background:var(--cream);border-radius:var(--radius);font-size:0.85rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v3"/></svg>
                            <span style="color:var(--gray-600);"><strong style="color:var(--navy);">Casket:</strong> <?= htmlspecialchars($casketType) ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ($chapelDays): ?>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding:8px 12px;background:var(--cream);border-radius:var(--radius);font-size:0.85rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span style="color:var(--gray-600);"><strong style="color:var(--navy);">Chapel:</strong> <?= htmlspecialchars($chapelDays) ?> viewing day(s)</span>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($features)): ?>
                        <ul style="margin:0 0 16px;padding:0;list-style:none;">
                            <?php foreach ($features as $feature): ?>
                            <li style="padding:5px 0;border-bottom:1px solid var(--gray-100);font-size:0.85rem;color:var(--gray-600);display:flex;align-items:flex-start;gap:8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:3px;"><polyline points="20 6 9 17 4 12"/></svg>
                                <?= htmlspecialchars($feature) ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <div style="min-width:200px;">
                        <button type="button" class="btn btn-gold btn-block" onclick="openPlanModal(<?= (int)($plan['id'] ?? 0) ?>)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Details
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Smart Choice</span>
            <h2 class="sn-title">Why <em>Pre-Plan?</em></h2>
            <p class="sn-sub">Planning ahead is one of the most thoughtful gifts you can give your family.</p>
        </div>

        <div class="fade-in" style="display:grid;grid-template-columns:repeat(2,1fr);gap:24px;">
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Price Lock</span>
                    <h5 style="margin-bottom:8px;">Lock In Today's Prices</h5>
                    <p class="text-muted text-sm mb-0">Protect yourself from rising costs by securing current rates. Your plan price is guaranteed no matter when it's needed.</p>
                </div>
            </div>

            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Easy Terms</span>
                    <h5 style="margin-bottom:8px;">Flexible Payment Terms</h5>
                    <p class="text-muted text-sm mb-0">Choose from spot cash, 5-year, or 10-year installment plans that fit your budget. Zero interest on select terms.</p>
                </div>
            </div>

            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Family Cover</span>
                    <h5 style="margin-bottom:8px;">Transferable to Family</h5>
                    <p class="text-muted text-sm mb-0">Plans can be transferred to any immediate family member. Your investment is protected and versatile.</p>
                </div>
            </div>

            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon" style="flex-shrink:0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    </div>
                <div>
                    <span class="sn-chip" style="margin-bottom:10px;">Always Open</span>
                    <h5 style="margin-bottom:8px;">24/7 Emergency Support</h5>
                    <p class="text-muted text-sm mb-0">Our team is available around the clock to assist you and your family whenever you need us most.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Good To Know</span>
            <h2 class="sn-title">Frequently Asked <em>Questions</em></h2>
            <p class="sn-sub">Everything you need to know about our life plans.</p>
        </div>

        <div style="max-width:800px;margin:0 auto;">
            <div class="sn-card topline mb-2 fade-in" style="overflow:hidden;padding:0 0;">
                <button onclick="toggleFaq(this)" style="width:100%;text-align:left;background:none;border:none;padding:20px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <h5 style="margin:0;font-size:1rem;">What is a pre-need life plan?</h5>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:transform 0.3s;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer" style="max-height:0;overflow:hidden;transition:max-height 0.3s ease;">
                    <div style="padding:0 24px 20px;color:var(--gray-600);font-size:0.95rem;line-height:1.7;">
                        A pre-need life plan is an arrangement made in advance to cover funeral and memorial expenses at the time of need. By planning ahead, you lock in today's prices, reduce the financial burden on your family, and ensure that your final wishes are honored exactly as you intended.
                    </div>
                </div>
            </div>

            <div class="sn-card topline mb-2 fade-in" style="overflow:hidden;padding:0 0;">
                <button onclick="toggleFaq(this)" style="width:100%;text-align:left;background:none;border:none;padding:20px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <h5 style="margin:0;font-size:1rem;">How do I pay for my plan?</h5>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:transform 0.3s;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer" style="max-height:0;overflow:hidden;transition:max-height 0.3s ease;">
                    <div style="padding:0 24px 20px;color:var(--gray-600);font-size:0.95rem;line-height:1.7;">
                        We offer several convenient payment options: spot cash with a special discount, 5-year monthly installments, and 10-year monthly installments. You can pay via bank transfer, GCash, Maya, check, or over-the-counter at any of our branches. Our staff will help you find the best payment plan for your budget.
                    </div>
                </div>
            </div>

            <div class="sn-card topline mb-2 fade-in" style="overflow:hidden;padding:0 0;">
                <button onclick="toggleFaq(this)" style="width:100%;text-align:left;background:none;border:none;padding:20px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <h5 style="margin:0;font-size:1rem;">Can I transfer my plan?</h5>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:transform 0.3s;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer" style="max-height:0;overflow:hidden;transition:max-height 0.3s ease;">
                    <div style="padding:0 24px 20px;color:var(--gray-600);font-size:0.95rem;line-height:1.7;">
                        Yes, your plan is fully transferable to any immediate family member including your spouse, children, parents, or siblings. Simply visit any of our branches with the original policy and valid IDs to process the transfer. There is no additional cost for the transfer.
                    </div>
                </div>
            </div>

            <div class="sn-card topline mb-2 fade-in" style="overflow:hidden;padding:0 0;">
                <button onclick="toggleFaq(this)" style="width:100%;text-align:left;background:none;border:none;padding:20px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <h5 style="margin:0;font-size:1rem;">What happens if I miss a payment?</h5>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:transform 0.3s;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer" style="max-height:0;overflow:hidden;transition:max-height 0.3s ease;">
                    <div style="padding:0 24px 20px;color:var(--gray-600);font-size:0.95rem;line-height:1.7;">
                        We understand that circumstances can change. If you miss a payment, you have a 30-day grace period to catch up. Our team will reach out to help you arrange a solution. In extreme cases, we offer plan restructuring options to keep your coverage active. We are committed to working with you.
                    </div>
                </div>
            </div>

            <div class="sn-card topline mb-2 fade-in" style="overflow:hidden;padding:0 0;">
                <button onclick="toggleFaq(this)" style="width:100%;text-align:left;background:none;border:none;padding:20px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <h5 style="margin:0;font-size:1rem;">How do I file a claim?</h5>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:transform 0.3s;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer" style="max-height:0;overflow:hidden;transition:max-height 0.3s ease;">
                    <div style="padding:0 24px 20px;color:var(--gray-600);font-size:0.95rem;line-height:1.7;">
                        Filing a claim is simple. Call our 24/7 hotline at +63 917 123 4567 and our team will guide you through the process. You will need the original life plan certificate and a valid government-issued ID. Our staff will handle the arrangements from start to finish, ensuring a smooth and dignified service.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--bg);text-align:center;padding:64px 0;">
    <div class="container">
        <div class="sn-band harbor fade-in" style="text-align:center;padding:56px 48px;">
        <h2 style="color:var(--ink-on-navy);margin-bottom:12px;">Ready to Secure Your <em style="color:#CCB877;">Family's Future?</em></h2>
        <p style="color:rgba(255,255,255,0.7);max-width:600px;margin:0 auto 24px;">Speak with our life plan consultants today and find the perfect plan for you and your loved ones.</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="contact.php" class="btn btn-gold">Contact Us</a>
            <a href="tel:+639171234567" class="btn btn-outline" style="color:var(--gold);border-color:var(--gold);">Call +63 917 123 4567</a>
        </div>
        </div>
    </div>
</section>

<script>
// Plan Category Filter
function filterPlans(category, btn) {
    var tabs = document.querySelectorAll('.plan-tab');
    tabs.forEach(function(t) {
        t.classList.remove('active');
        t.classList.add('btn-outline');
    });
    btn.classList.add('active');
    btn.classList.remove('btn-outline');

    var cards = document.querySelectorAll('.plan-card');
    cards.forEach(function(card) {
        if (category === 'all' || card.getAttribute('data-category') === category) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// FAQ Accordion
function toggleFaq(btn) {
    var answer = btn.nextElementSibling;
    var icon = btn.querySelector('.faq-icon');
    var isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';

    // Close all other FAQs
    document.querySelectorAll('.faq-answer').forEach(function(a) {
        a.style.maxHeight = '0px';
    });
    document.querySelectorAll('.faq-icon').forEach(function(i) {
        i.style.transform = 'rotate(0deg)';
    });

    if (!isOpen) {
        answer.style.maxHeight = answer.scrollHeight + 'px';
        if (icon) icon.style.transform = 'rotate(180deg)';
    }
}

// Fade-in animation
document.querySelectorAll('.fade-in').forEach(function(el) {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    observer.observe(el);
});
</script>

<style>
    .plan-tab.active {
        background: var(--navy) !important;
        color: var(--white) !important;
        border-color: var(--navy) !important;
        font-weight: 600;
    }
    .plan-tab:not(.active) {
        color: var(--navy);
    }
    .plan-tab:not(.active):hover {
        background: rgba(15, 23, 42, 0.08);
    }
    @media (max-width: 860px){#plansGrid .plan-card{grid-template-columns:1fr !important;}}
</style>

<?php
// Flattened plan data for the shared View Details modal.
$planDetails = [];
foreach ($plans as $pl) {
    $feat = json_decode($pl['features'] ?? '[]', true);
    if (!is_array($feat)) $feat = [];
    $numOrNull = function ($v) {
        return (isset($v) && $v !== '' && $v !== null) ? (float)$v : null;
    };
    $planDetails[(int)($pl['id'] ?? 0)] = [
        'id' => (int)($pl['id'] ?? 0),
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
?>
<script>
var PLAN_DETAILS = <?= json_encode($planDetails, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var PLAN_CHECKOUT_BASE = '../checkout.php';
var PLAN_COMPARE_LINK = 'compare.php';
</script>
<?php include __DIR__ . '/../includes/plan-modal.php'; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

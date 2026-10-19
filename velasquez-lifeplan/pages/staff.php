<?php
$pageTitle = 'Staff Panel';
$panelStyles = true; // pulls in css/panel.css
require_once __DIR__ . '/../includes/header.php';

// Employees only: admins get the full backoffice, customers get their own dashboard.
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
if (($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: admin.php');
    exit;
}
if (($_SESSION['user_role'] ?? '') !== 'employee') {
    header('Location: dashboard.php');
    exit;
}

// ---------- Staff actions: payment verification only ----------
// (Policy approvals, claim decisions, and inventory edits stay admin-only.)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff_action'])) {
    $action = $_POST['staff_action'];
    $flash = '';

    if ($action === 'verify_payment' || $action === 'reject_payment') {
        $payId = (int)($_POST['payment_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? AND status = 'pending'");
        $stmt->execute([$payId]);
        $payment = $stmt->fetch();
        if ($payment) {
            if ($action === 'verify_payment') {
                $orNumber = 'OR-' . str_pad((string)$payId, 6, '0', STR_PAD_LEFT);
                $pdo->prepare("UPDATE payments SET status = 'verified', receipt_number = ? WHERE id = ?")->execute([$orNumber, $payId]);
                if (!empty($payment['installment_id'])) {
                    $pdo->prepare("UPDATE payment_schedule SET status = 'paid', paid_date = CURDATE() WHERE id = ?")
                        ->execute([$payment['installment_id']]);
                }
                $pdo->prepare("UPDATE subscriptions SET paid_amount = paid_amount + ?, remaining_balance = remaining_balance - ? WHERE id = ?")
                    ->execute([$payment['amount'], $payment['amount'], $payment['subscription_id']]);
                $flash = 'Payment verified. Schedule and balances updated.';
            } else {
                $pdo->prepare("UPDATE payments SET status = 'rejected' WHERE id = ?")->execute([$payId]);
                $flash = 'Payment rejected.';
            }
        } else {
            $flash = 'Payment was already processed.';
        }
        $_SESSION['staff_flash'] = $flash;
        header('Location: staff.php#verifications');
        exit;
    }
}

$flashMsg = $_SESSION['staff_flash'] ?? '';
unset($_SESSION['staff_flash']);

$totalCustomers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$activePolicies = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
$monthCollections = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'verified' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())")->fetchColumn();

$pendingPays = $pdo->query("SELECT pay.*, u.name AS user_name, p.name AS plan_name FROM payments pay JOIN subscriptions s ON pay.subscription_id = s.id JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id WHERE pay.status = 'pending' ORDER BY pay.created_at DESC LIMIT 20")->fetchAll();

$customers = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM subscriptions s WHERE s.user_id = u.id) AS plan_count FROM users u WHERE u.role = 'customer' ORDER BY u.created_at DESC LIMIT 50")->fetchAll();

$policies = $pdo->query("SELECT s.*, u.name AS user_name, p.name AS plan_name FROM subscriptions s JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id ORDER BY s.created_at DESC LIMIT 15")->fetchAll();

$inventory = $pdo->query("SELECT * FROM logistics_inventory ORDER BY category, item_name LIMIT 20")->fetchAll();

$followups = $pdo->query("SELECT ps.*, u.name AS user_name, u.phone AS phone, p.name AS plan_name FROM payment_schedule ps JOIN subscriptions s ON ps.subscription_id = s.id JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id WHERE ps.status != 'paid' AND ps.due_date < CURDATE() ORDER BY ps.due_date ASC LIMIT 15")->fetchAll();

$recentlyVerified = $pdo->query("SELECT pay.*, u.name AS user_name FROM payments pay JOIN subscriptions s ON pay.subscription_id = s.id JOIN users u ON s.user_id = u.id WHERE pay.status = 'verified' ORDER BY pay.created_at DESC LIMIT 5")->fetchAll();

$staffTrend = [];
for ($m = 5; $m >= 0; $m--) {
    $staffTrend[date('Y-m', strtotime("-$m months"))] = 0;
}
$staffTrendRows = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS m, SUM(amount) AS total FROM payments WHERE status = 'verified' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY m")->fetchAll();
foreach ($staffTrendRows as $tr) {
    if (isset($staffTrend[$tr['m']])) $staffTrend[$tr['m']] = (float)$tr['total'];
}
$staffTrendMax = max(array_values($staffTrend));
if ($staffTrendMax <= 0) $staffTrendMax = 1;
?>

<?php
// Shared panel shell - one sidebar definition drives every signed-in panel.
$panelStyles = true;
$panelRoleLabel = 'Employee';
$panelMenu = [
    ['title' => 'Work Queue', 'items' => [
        ['label' => 'Overview', 'href' => '#overview', 'icon' => 'grid', 'active' => true],
        ['label' => 'Verifications', 'href' => '#verifications', 'icon' => 'card', 'badge' => $pendingPayments ?: null],
        ['label' => 'Follow-ups', 'href' => '#followups', 'icon' => 'clock', 'badge' => count($followups) ?: null],
    ]],
    ['title' => 'Records', 'items' => [
        ['label' => 'Customers', 'href' => '#customers', 'icon' => 'users'],
        ['label' => 'Policies', 'href' => '#policies', 'icon' => 'file'],
        ['label' => 'Inventory', 'href' => '#inventory', 'icon' => 'box'],
    ]],
];
$panelMenuFooter = [
    ['label' => 'Back to Admin', 'href' => 'admin.php', 'icon' => 'shield'],
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

        <div class="sn-band harbor mb-4" id="overview" style="padding:40px 36px;">
        <div class="panel-topbar">
            <div class="panel-topbar-titles">
                <p class="panel-crumb">
                    <a href="staff.php">Front Desk</a>
                    <span class="panel-crumb-sep">/</span>
                    <span>Day Book</span>
                </p>
                <h1 class="panel-title">Good day, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'Staff')[0]) ?></h1>
            </div>
            <div class="panel-actions">
                <a href="#verifications" class="btn btn-outline btn-sm">Verify Payments<?php if ($pendingPayments): ?> (<?= $pendingPayments ?>)<?php endif; ?></a>
            </div>
        </div>

        <?php if ($flashMsg): ?>
        <div class="panel-flash">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M20 6L9 17l-5-5"/></svg>
            <span><?= htmlspecialchars($flashMsg) ?></span>
        </div>
        <?php endif; ?>

        <div class="kpi-grid" id="overview">
            <div class="kpi">
                <div class="kpi-top">
                    <span class="kpi-label">Customers</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></span>
                </div>
                <div class="kpi-value"><?= $totalCustomers ?></div>
                <div class="kpi-foot">On record</div>
            </div>
            <div class="kpi" data-tone="good">
                <div class="kpi-top">
                    <span class="kpi-label">Active Policies</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                </div>
                <div class="kpi-value"><?= $activePolicies ?></div>
                <div class="kpi-foot">Currently in force</div>
            </div>
            <div class="kpi" data-tone="<?= $pendingPayments > 0 ? 'warn' : 'gold' ?>">
                <div class="kpi-top">
                    <span class="kpi-label">To Verify</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
                </div>
                <div class="kpi-value"><?= $pendingPayments ?></div>
                <div class="kpi-foot"><?= $pendingPayments > 0 ? 'Waiting on you' : 'Queue is clear' ?></div>
            </div>
            <div class="kpi" data-tone="gold">
                <div class="kpi-top">
                    <span class="kpi-label">Verified This Month</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 010-4h14v4"/><path d="M3 5v14a2 2 0 002 2h16v-5"/><path d="M18 12a2 2 0 000 4h4v-4h-4z"/></svg></span>
                </div>
                <div class="kpi-value" style="font-size:1.7rem;">&#8369;<?= number_format($monthCollections, 0) ?></div>
                <div class="kpi-foot">Current month</div>
            </div>
        </div>

        <div class="sn-card topline mb-3">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Fig. 01 &mdash; Collections Trend</span>
                <h4>Six-Month Takings</h4>
                <p>Verified payments, last 6 months.</p>
            </div>
            <div style="padding:0;">
                <div style="display:flex;gap:8px;height:130px;">
                    <?php foreach ($staffTrend as $tkey => $tval):
                        $th = round(($tval / $staffTrendMax) * 100);
                    ?>
                    <div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;text-align:center;min-width:0;">
                        <div style="font-size:0.65rem;font-weight:700;color:var(--navy);margin-bottom:3px;"><?= $tval > 0 ? number_format($tval / 1000, 1) . 'k' : '' ?></div>
                        <div title="<?= date('F Y', strtotime($tkey . '-01')) ?>: &#8369;<?= number_format($tval, 0) ?>" style="height:<?= $tval > 0 ? max($th, 6) : 3 ?>%;min-height:3px;background:linear-gradient(180deg,#2E4A6B,#1A2634);border-radius:5px 5px 0 0;"></div>
                        <div style="font-size:0.65rem;color:var(--gray-500);margin-top:4px;"><?= date('M', strtotime($tkey . '-01')) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="verifications">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 01 — Verification Queue</span>
                <div style="display:flex;align-items:center;gap:14px;margin-top:8px;">
                    <span class="sn-stepnum" style="margin-bottom:0;">1</span>
                    <h4 style="margin:0;">Payment Verifications</h4>
                </div>
                <p class="sn-sub" style="font-size:.875rem;">Check each proof, then verify or reject. Verifying updates the installment and the policy balance automatically.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($pendingPays)): ?>
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Customer</th><th>Plan</th><th>Amount</th><th>Reference</th><th>Proof</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($pendingPays as $pp): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($pp['user_name']) ?></td>
                                <td><?= htmlspecialchars($pp['plan_name']) ?></td>
                                <td style="font-weight:600;">&#8369;<?= number_format($pp['amount'], 2) ?></td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($pp['reference_number'] ?: '—') ?></td>
                                <td>
                                    <?php if (!empty($pp['proof_image'])): ?>
                                        <a href="../<?= htmlspecialchars($pp['proof_image']) ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
                                    <?php else: ?>
                                        <span class="text-muted text-sm">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display:flex;gap:8px;">
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="staff_action" value="verify_payment">
                                            <input type="hidden" name="payment_id" value="<?= $pp['id'] ?>">
                                            <button type="submit" class="btn btn-gold btn-sm">Verify</button>
                                        </form>
                                        <form method="POST" style="margin:0;" onsubmit="return confirm('Reject this payment?');">
                                            <input type="hidden" name="staff_action" value="reject_payment">
                                            <input type="hidden" name="payment_id" value="<?= $pp['id'] ?>">
                                            <button type="submit" class="btn btn-outline btn-sm">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center" style="padding:24px 0;">Queue is clear. No payments awaiting verification.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="followups">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 02 — Call Sheet</span>
                <div style="display:flex;align-items:center;gap:14px;margin-top:8px;">
                    <span class="sn-stepnum" style="margin-bottom:0;">2</span>
                    <h4 style="margin:0;">Customer Follow-ups</h4>
                </div>
                <p class="sn-sub" style="font-size:.875rem;">Overdue installments to remind by phone — oldest first.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($followups)): ?>
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Customer</th><th>Phone</th><th>Plan</th><th>Due</th><th>Amount</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($followups as $fu): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($fu['user_name']) ?></td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($fu['phone'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($fu['plan_name']) ?></td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($fu['due_date']) ?></td>
                                <td style="font-weight:600;">&#8369;<?= number_format($fu['amount'], 2) ?></td>
                                <td><a href="tel:<?= preg_replace('/[^0-9+]/', '', $fu['phone'] ?? '') ?>" class="btn btn-gold btn-sm">Call</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center" style="padding:24px 0;">Nothing overdue. No follow-up calls needed.</p>
                <?php endif; ?>

                <div style="margin:24px 0 12px;"><span class="sn-chip">Recently Verified</span></div>
                <h4 style="margin:0 0 12px;color:var(--text-strong);">Recently Verified</h4>
                <?php if (!empty($recentlyVerified)): ?>
                <?php foreach ($recentlyVerified as $rv): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                    <div>
                        <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($rv['user_name']) ?></div>
                        <div class="text-muted text-xs">&#8369;<?= number_format($rv['amount'], 2) ?> &bull; <?= htmlspecialchars($rv['reference_number'] ?: 'no ref') ?></div>
                    </div>
                    <span class="badge badge-green">Verified</span>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted text-center" style="padding:16px 0;">No verified payments yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="customers">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 03 &mdash; Directory</span>
                <h4>Customer Directory</h4>
                <p>Every customer on record, newest first.</p>
            </div>
            <div style="padding:0;">
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Plans</th><th>Joined</th></tr></thead>
                        <tbody>
                            <?php foreach ($customers as $cu): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($cu['name']) ?></td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($cu['email']) ?></td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($cu['phone'] ?? '—') ?></td>
                                <td><?= (int)$cu['plan_count'] ?></td>
                                <td class="text-muted text-sm"><?= date('M d, Y', strtotime($cu['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="policies">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 04 &mdash; Policy Book</span>
                <h4>Policies <span class="text-muted text-sm" style="font-weight:400;">(view only &mdash; approvals require an admin)</span></h4>
                <p>Read-only policy register for front-desk reference.</p>
            </div>
            <div style="padding:0;">
                <div class="table-wrapper" style="margin-top:16px;">
                    <table>
                        <thead><tr><th>Customer</th><th>Plan</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($policies as $po): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($po['user_name']) ?></td>
                                <td><?= htmlspecialchars($po['plan_name']) ?></td>
                                <td style="font-weight:600;">&#8369;<?= number_format($po['total_amount'], 2) ?></td>
                                <td><span class="badge <?= $po['status'] === 'active' ? 'badge-sage' : ($po['status'] === 'pending' ? 'badge-bronze' : 'badge-slate') ?>" style="text-transform:capitalize;"><?= htmlspecialchars($po['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="inventory">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 05 &mdash; Stockroom</span>
                <h4>Inventory <span class="text-muted text-sm" style="font-weight:400;">(view only)</span></h4>
                <p>Current chapel and supply counts for reference.</p>
            </div>
            <div style="padding:0;">
                <div class="table-wrapper" style="margin-top:16px;">
                    <table>
                        <thead><tr><th>Item</th><th>Category</th><th>Quantity</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($inventory as $inv): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($inv['item_name']) ?></td>
                                <td class="text-muted text-sm" style="text-transform:capitalize;"><?= htmlspecialchars($inv['category'] ?? '—') ?></td>
                                <td><?= (int)($inv['quantity'] ?? 0) ?></td>
                                <td><span class="badge badge-green" style="text-transform:capitalize;"><?= htmlspecialchars($inv['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.querySelectorAll('.sidebar-menu-item').forEach(function(item) {
    item.addEventListener('click', function() {
        document.querySelectorAll('.sidebar-menu-item').forEach(function(i) { i.classList.remove('active'); });
        item.classList.add('active');
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

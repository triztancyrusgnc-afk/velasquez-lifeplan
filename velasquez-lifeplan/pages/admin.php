<?php
$pageTitle = 'Admin Dashboard';
$panelStyles = true; // pulls in css/panel.css
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// ---------- Admin actions (POST + redirect, no resubmits) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {
    $action = $_POST['admin_action'];
    $flash = '';

    if ($action === 'approve_subscription' || $action === 'reject_subscription') {
        $subId = (int)($_POST['subscription_id'] ?? 0);
        $newStatus = $action === 'approve_subscription' ? 'active' : 'cancelled';
        $stmt = $pdo->prepare("UPDATE subscriptions SET status = ? WHERE id = ? AND status = 'pending'");
        $stmt->execute([$newStatus, $subId]);
        $flash = $stmt->rowCount() > 0
            ? ($newStatus === 'active' ? 'Subscription approved and activated.' : 'Subscription rejected and cancelled.')
            : 'Subscription was already processed.';
        header('Location: admin.php#approvals');
        $_SESSION['admin_flash'] = $flash;
        exit;
    }

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
        header('Location: admin.php#billing');
        $_SESSION['admin_flash'] = $flash;
        exit;
    }

    if ($action === 'update_claim') {
        $claimId = (int)($_POST['claim_id'] ?? 0);
        $newStatus = $_POST['claim_status'] ?? 'pending';
        $allowed = ['pending', 'processing', 'approved', 'rejected', 'completed'];
        if (in_array($newStatus, $allowed, true)) {
            $pdo->prepare("UPDATE claims SET status = ? WHERE id = ?")->execute([$newStatus, $claimId]);
            $flash = 'Claim status updated to ' . $newStatus . '.';
        } else {
            $flash = 'Invalid claim status.';
        }
        header('Location: admin.php#claims');
        $_SESSION['admin_flash'] = $flash;
        exit;
    }

    if ($action === 'update_inventory') {
        $invId = (int)($_POST['inventory_id'] ?? 0);
        $qty = max(0, (int)($_POST['quantity'] ?? 0));
        $pdo->prepare("UPDATE logistics_inventory SET quantity = ? WHERE id = ?")->execute([$qty, $invId]);
        $flash = 'Inventory quantity updated.';
        header('Location: admin.php#inventory');
        $_SESSION['admin_flash'] = $flash;
        exit;
    }
}

$flashMsg = $_SESSION['admin_flash'] ?? '';
unset($_SESSION['admin_flash']);

$totalPolicies = $pdo->query("SELECT COUNT(*) FROM subscriptions")->fetchColumn();
$activeSubs = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'")->fetchColumn();
$totalCollections = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM subscriptions")->fetchColumn();
$pendingClaims = $pdo->query("SELECT COUNT(*) FROM claims WHERE status = 'pending'")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();

$recentPolicies = $pdo->query("SELECT s.*, u.name AS user_name, u.email, p.name AS plan_name FROM subscriptions s JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id ORDER BY s.created_at DESC LIMIT 10")->fetchAll();

$pendingApprovals = $pdo->query("SELECT s.*, u.name AS user_name, u.email, p.name AS plan_name FROM subscriptions s JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id WHERE s.status = 'pending' ORDER BY s.created_at DESC LIMIT 10")->fetchAll();

$overdueSubs = $pdo->query("SELECT s.id, u.name AS user_name, p.name AS plan_name, COUNT(ps.id) AS overdue_count, SUM(ps.amount) AS overdue_amount FROM subscriptions s JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id JOIN payment_schedule ps ON ps.subscription_id = s.id WHERE ps.status != 'paid' AND ps.due_date < CURDATE() GROUP BY s.id ORDER BY overdue_amount DESC LIMIT 8")->fetchAll();

$lowStock = $pdo->query("SELECT * FROM logistics_inventory WHERE quantity <= 2 ORDER BY quantity ASC, item_name ASC LIMIT 8")->fetchAll();

$pendingPays = $pdo->query("SELECT pay.*, u.name AS user_name, p.name AS plan_name FROM payments pay JOIN subscriptions s ON pay.subscription_id = s.id JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id WHERE pay.status = 'pending' ORDER BY pay.created_at DESC LIMIT 20")->fetchAll();

$allClaims = $pdo->query("SELECT c.*, u.name AS user_name, p.name AS plan_name FROM claims c JOIN users u ON c.user_id = u.id JOIN subscriptions s ON c.subscription_id = s.id JOIN plans p ON s.plan_id = p.id ORDER BY c.created_at DESC LIMIT 20")->fetchAll();

$customers = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM subscriptions s WHERE s.user_id = u.id) AS plan_count FROM users u WHERE u.role IN ('customer','employee') ORDER BY u.created_at DESC LIMIT 50")->fetchAll();

$inventory = $pdo->query("SELECT * FROM logistics_inventory ORDER BY category, item_name")->fetchAll();

$claimStats = $pdo->query("SELECT status, COUNT(*) as cnt FROM claims GROUP BY status")->fetchAll();
$topPlans = $pdo->query("SELECT p.name, COUNT(s.id) as sub_count, SUM(s.paid_amount) as total_paid FROM subscriptions s JOIN plans p ON s.plan_id = p.id GROUP BY p.id ORDER BY sub_count DESC LIMIT 5")->fetchAll();
$statusBreakdown = $pdo->query("SELECT status, COUNT(*) as cnt FROM subscriptions GROUP BY status")->fetchAll();
$maxSubCount = 1;
foreach ($statusBreakdown as $row) { $maxSubCount = max($maxSubCount, (int)$row['cnt']); }

// Collections trend: verified payments per month, last 6 months (gaps filled).
$collections = [];
for ($m = 5; $m >= 0; $m--) {
    $collections[date('Y-m', strtotime("-$m months"))] = 0;
}
$collRows = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS m, SUM(amount) AS total FROM payments WHERE status = 'verified' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY m")->fetchAll();
foreach ($collRows as $cr) {
    if (isset($collections[$cr['m']])) $collections[$cr['m']] = (float)$cr['total'];
}
$maxColl = max(array_values($collections));
if ($maxColl <= 0) $maxColl = 1;

// Donut slices for policy statuses. These are CSS variables, not literal
    // hex, so each slice re-colours itself when the theme flips.
    $donutColors = [
        'active'    => 'var(--chart-1)',
        'pending'   => 'var(--chart-2)',
        'expired'   => 'var(--chart-3)',
        'cancelled' => 'var(--chart-4)',
        'claimed'   => 'var(--chart-5)',
    ];
$totalSubs = array_sum(array_map(function ($r) { return (int)$r['cnt']; }, $statusBreakdown));
if ($totalSubs <= 0) $totalSubs = 1;
$donutOffset = 0;
$donutSlices = [];
foreach ($statusBreakdown as $row) {
    $frac = ((int)$row['cnt']) / $totalSubs;
    $donutSlices[] = [
        'status' => $row['status'],
        'cnt' => (int)$row['cnt'],
        'color' => $donutColors[$row['status']] ?? 'var(--chart-4)',
        'dash' => round($frac * 282.74, 2),
        'offset' => round(-$donutOffset, 2),
    ];
    $donutOffset += $frac * 282.74;
}

// Revenue scale for the per-plan bars.
$maxRevenue = 1;
foreach ($topPlans as $tp) { $maxRevenue = max($maxRevenue, (float)($tp['total_paid'] ?? 0)); }
?>

<?php
// Shared panel shell: one sidebar definition drives every signed-in panel.
$panelStyles = true;
$panelRoleLabel = 'Administrator';
$attentionCount = count($overdueSubs) + count($lowStock);
$panelMenu = [
    ['title' => 'Overview', 'items' => [
        ['label' => 'Dashboard', 'href' => '#overview', 'icon' => 'grid', 'active' => true],
        ['label' => 'Attention', 'href' => '#attention', 'icon' => 'alert', 'badge' => $attentionCount ?: null],
    ]],
    ['title' => 'Management', 'items' => [
        ['label' => 'Approvals', 'href' => '#approvals', 'icon' => 'shield', 'badge' => count($pendingApprovals) ?: null],
        ['label' => 'Billing', 'href' => '#billing', 'icon' => 'card', 'badge' => $pendingPayments ?: null],
        ['label' => 'Claims', 'href' => '#claims', 'icon' => 'file'],
        ['label' => 'Customers', 'href' => '#customers', 'icon' => 'users'],
        ['label' => 'Inventory', 'href' => '#inventory', 'icon' => 'box'],
        ['label' => 'Analytics', 'href' => '#analytics', 'icon' => 'chart'],
    ]],
    ['title' => 'Website', 'items' => [
        ['label' => 'Website Settings', 'href' => 'admin_settings.php', 'icon' => 'cog'],
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
                    <span>Overview</span>
                </p>
                <h1 class="panel-title">Admin Dashboard</h1>
            </div>
            <div class="panel-actions">
                <a href="#attention" class="btn btn-outline btn-sm">Needs Attention<?php if ($attentionCount): ?> (<?= $attentionCount ?>)<?php endif; ?></a>
                <a href="admin_settings.php" class="btn btn-gold btn-sm">Edit Homepage</a>
            </div>
        </div>

        <?php if ($flashMsg): ?>
        <div class="panel-flash">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M20 6L9 17l-5-5"/></svg>
            <span><?= htmlspecialchars($flashMsg) ?></span>
        </div>
        <?php endif; ?>

        <div class="kpi-grid" id="overview">
            <div class="kpi" data-tone="gold">
                <div class="kpi-top">
                    <span class="kpi-label">Total Policies</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
                </div>
                <div class="kpi-value"><?= $totalPolicies ?></div>
                <div class="kpi-foot">All contracts on record</div>
            </div>
            <div class="kpi" data-tone="good">
                <div class="kpi-top">
                    <span class="kpi-label">Active Subscriptions</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                </div>
                <div class="kpi-value"><?= $activeSubs ?></div>
                <div class="kpi-foot">Currently in force</div>
            </div>
            <div class="kpi">
                <div class="kpi-top">
                    <span class="kpi-label">Total Collections</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 010-4h14v4"/><path d="M3 5v14a2 2 0 002 2h16v-5"/><path d="M18 12a2 2 0 000 4h4v-4h-4z"/></svg></span>
                </div>
                <div class="kpi-value" style="font-size:1.7rem;">&#8369;<?= number_format($totalCollections, 0) ?></div>
                <div class="kpi-foot">Verified to date</div>
            </div>
            <div class="kpi" data-tone="<?= $pendingClaims > 0 ? 'warn' : 'gold' ?>">
                <div class="kpi-top">
                    <span class="kpi-label">Pending Claims</span>
                    <span class="kpi-glyph"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                </div>
                <div class="kpi-value"><?= $pendingClaims ?></div>
                <div class="kpi-foot"><?= $pendingClaims > 0 ? 'Awaiting review' : 'Nothing outstanding' ?></div>
            </div>
        </div>

        <div class="panel-card" id="attention">
            <div class="panel-card-head">
                <div>
                    <h3 class="panel-card-title">Needs Attention</h3>
                    <p class="panel-card-sub">Overdue accounts and items running low — sorted by amount owed.</p>
                </div>
                <div class="panel-card-tools">
                    <span class="badge badge-navy"><?= count($overdueSubs) ?> overdue</span>
                    <span class="badge badge-<?= count($lowStock) > 0 ? 'gold' : 'sage' ?>"><?= count($lowStock) ?> low stock</span>
                </div>
            </div>
            <div class="panel-card-body">
                <?php if (empty($overdueSubs) && empty($lowStock)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
                    <p class="empty-state-title">All clear</p>
                    <p class="empty-state-text">No overdue accounts and stock levels are healthy. Nothing needs your attention right now.</p>
                </div>
                <?php else: ?>
                <div class="grid-2" style="gap:24px;">
                    <div>
                        <h5 style="margin-bottom:12px;">Overdue Accounts</h5>
                        <?php if (empty($overdueSubs)): ?>
                        <p class="text-muted text-sm">None. Every account is current.</p>
                        <?php else: ?>
                        <?php foreach ($overdueSubs as $od): ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                            <div>
                                <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($od['user_name']) ?></div>
                                <div class="text-muted text-xs"><?= htmlspecialchars($od['plan_name']) ?> &mdash; <?= $od['overdue_count'] ?> overdue</div>
                            </div>
                            <span style="font-weight:700;color:var(--red);font-size:0.875rem;white-space:nowrap;">&#8369;<?= number_format($od['overdue_amount'], 0) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h5 style="margin-bottom:12px;">Low Stock (2 or fewer left)</h5>
                        <?php if (empty($lowStock)): ?>
                        <p class="text-muted text-sm">None. Inventory levels are healthy.</p>
                        <?php else: ?>
                        <?php foreach ($lowStock as $ls): ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                            <div>
                                <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($ls['item_name'] ?? $ls['name']) ?></div>
                                <div class="text-muted text-xs" style="text-transform:capitalize;"><?= htmlspecialchars($ls['category'] ?? $ls['type'] ?? '') ?></div>
                            </div>
                            <span class="badge" style="background:#FEF3C7;color:#D97706;"><?= (int)($ls['quantity'] ?? 0) ?> left</span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="approvals">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 01 — Pending Approvals</span>
                <div style="display:flex;align-items:center;gap:14px;margin-top:8px;">
                    <span class="sn-stepnum" style="margin-bottom:0;">1</span>
                    <h4 style="margin:0;">Applications Awaiting Review</h4>
                </div>
                <p class="sn-sub" style="font-size:.875rem;">New applications waiting for your review. Approving activates the policy.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($pendingApprovals)): ?>
                <?php foreach ($pendingApprovals as $pa): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:12px 0;border-bottom:1px solid var(--gray-100);">
                    <div>
                        <div style="font-weight:600;font-size:0.9375rem;"><?= htmlspecialchars($pa['user_name']) ?></div>
                        <div class="text-muted text-xs"><?= htmlspecialchars($pa['email']) ?> &mdash; <?= htmlspecialchars($pa['plan_name']) ?> &mdash; &#8369;<?= number_format($pa['total_amount'], 2) ?></div>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="admin_action" value="approve_subscription">
                            <input type="hidden" name="subscription_id" value="<?= $pa['id'] ?>">
                            <button type="submit" class="btn btn-gold btn-sm">Approve</button>
                        </form>
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Reject this application?');">
                            <input type="hidden" name="admin_action" value="reject_subscription">
                            <input type="hidden" name="subscription_id" value="<?= $pa['id'] ?>">
                            <button type="submit" class="btn btn-outline btn-sm">Reject</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted text-center" style="padding:24px 0;">No pending approvals. All applications have been reviewed.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="billing">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 02 — Billing Ledger</span>
                <div style="display:flex;align-items:center;gap:14px;margin-top:8px;">
                    <span class="sn-stepnum" style="margin-bottom:0;">2</span>
                    <h4 style="margin:0;">Payments Awaiting Verification</h4>
                </div>
                <p class="sn-sub" style="font-size:.875rem;">Verifying a payment marks the installment paid and updates the policy balance.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($pendingPays)): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Proof</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingPays as $pp): ?>
                            <tr>
                                <td><div style="font-weight:600;"><?= htmlspecialchars($pp['user_name']) ?></div></td>
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
                                <td class="text-muted text-sm"><?= date('M d, Y', strtotime($pp['created_at'])) ?></td>
                                <td>
                                    <div style="display:flex;gap:8px;">
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="admin_action" value="verify_payment">
                                            <input type="hidden" name="payment_id" value="<?= $pp['id'] ?>">
                                            <button type="submit" class="btn btn-gold btn-sm">Verify</button>
                                        </form>
                                        <form method="POST" style="margin:0;" onsubmit="return confirm('Reject this payment?');">
                                            <input type="hidden" name="admin_action" value="reject_payment">
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
                <p class="text-muted text-center" style="padding:24px 0;">No payments awaiting verification.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="claims">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 03 — Claims Desk</span>
                <div style="display:flex;align-items:center;gap:14px;margin-top:8px;">
                    <span class="sn-stepnum" style="margin-bottom:0;">3</span>
                    <h4 style="margin:0;">Claims Management</h4>
                </div>
                <p class="sn-sub" style="font-size:.875rem;">Update a claim&rsquo;s status as it moves through review.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($allClaims)): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Claimant</th>
                                <th>Plan</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allClaims as $cl): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars($cl['deceased_name'] ?: $cl['user_name']) ?></div>
                                    <div class="text-muted text-xs">Filed by <?= htmlspecialchars($cl['user_name']) ?></div>
                                    <?php if (!empty($cl['death_certificate_path'])): ?>
                                    <div style="margin-top:6px;"><a href="../<?= htmlspecialchars($cl['death_certificate_path']) ?>" target="_blank" class="btn btn-outline btn-sm">Death Certificate</a></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($cl['plan_name']) ?></div>
                                    <?php if (!empty($cl['chapel_reserved'])): ?><div class="text-muted text-xs">Chapel: <?= htmlspecialchars($cl['chapel_reserved']) ?></div><?php endif; ?>
                                    <?php if (!empty($cl['hearse_assigned'])): ?><div class="text-muted text-xs">Hearse: <?= htmlspecialchars($cl['hearse_assigned']) ?></div><?php endif; ?>
                                    <?php if (!empty($cl['date_of_death'])): ?><div class="text-muted text-xs">Passed: <?= htmlspecialchars($cl['date_of_death']) ?></div><?php endif; ?>
                                </td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($cl['claim_type'] ?? '—') ?></td>
                                <td><span class="badge <?= in_array($cl['status'], ['approved', 'completed'], true) ? 'badge-sage' : (in_array($cl['status'], ['pending', 'processing'], true) ? 'badge-bronze' : 'badge-slate') ?>" style="text-transform:capitalize;"><?= htmlspecialchars($cl['status']) ?></span></td>
                                <td class="text-muted text-sm"><?= date('M d, Y', strtotime($cl['created_at'])) ?></td>
                                <td>
                                    <form method="POST" style="margin:0;display:flex;gap:8px;">
                                        <input type="hidden" name="admin_action" value="update_claim">
                                        <input type="hidden" name="claim_id" value="<?= $cl['id'] ?>">
                                        <select name="claim_status" class="form-control" style="max-width:150px;padding:6px 10px;font-size:0.8125rem;">
                                            <?php foreach (['pending','processing','approved','rejected','completed'] as $cs): ?>
                                            <option value="<?= $cs ?>" <?= $cl['status'] === $cs ? 'selected' : '' ?>><?= ucfirst($cs) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-gold btn-sm">Save</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center" style="padding:24px 0;">No claims filed yet. Claims appear here when customers submit them.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="customers">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 04 &mdash; Register</span>
                <h4>Customers &amp; Staff (<?= $totalCustomers ?> customers)</h4>
                <p>Everyone registered on the system.</p>
            </div>
            <div style="padding:0;">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Plans</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $cu): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($cu['name']) ?></td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($cu['email']) ?></td>
                                <td><span class="badge <?= $cu['role'] === 'employee' ? 'badge-navy' : 'badge-gold' ?>" style="text-transform:capitalize;"><?= htmlspecialchars($cu['role']) ?></span></td>
                                <td class="text-muted text-sm"><?= htmlspecialchars($cu['contact_number'] ?? $cu['phone'] ?? '—') ?></td>
                                <td><?= (int)$cu['plan_count'] ?></td>
                                <td class="text-muted text-sm"><?= date('M d, Y', strtotime($cu['created_at'])) ?></td>
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
                <h4>Chapels &amp; Inventory</h4>
                <p>Adjust stock counts for caskets, flowers, and supplies.</p>
            </div>
            <div style="padding:0;">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory as $inv): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($inv['item_name'] ?? $inv['name']) ?></td>
                                <td class="text-muted text-sm" style="text-transform:capitalize;"><?= htmlspecialchars($inv['category'] ?? $inv['type'] ?? '—') ?></td>
                                <td><?= (int)($inv['quantity'] ?? 0) ?></td>
                                <td><span class="badge badge-green" style="text-transform:capitalize;"><?= htmlspecialchars($inv['status']) ?></span></td>
                                <td>
                                    <form method="POST" style="margin:0;display:flex;gap:8px;">
                                        <input type="hidden" name="admin_action" value="update_inventory">
                                        <input type="hidden" name="inventory_id" value="<?= $inv['id'] ?>">
                                        <input type="number" name="quantity" value="<?= (int)($inv['quantity'] ?? 0) ?>" min="0" class="form-control" style="max-width:90px;padding:6px 10px;">
                                        <button type="submit" class="btn btn-gold btn-sm">Save</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="overview">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Sec. 06 &mdash; Recent Policies</span>
                <h4>Latest Contracts</h4>
                <p>The ten most recent policies, newest first.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($recentPolicies)): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPolicies as $rp): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars($rp['user_name']) ?></div>
                                    <div class="text-muted text-xs"><?= htmlspecialchars($rp['email']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($rp['plan_name']) ?></td>
                                <td style="font-weight:600;">&#8369;<?= number_format($rp['total_amount'], 2) ?></td>
                                <td>
                                    <?php if ($rp['status'] === 'active'): ?>
                                        <span class="badge badge-sage">Active</span>
                                    <?php elseif ($rp['status'] === 'pending'): ?>
                                        <span class="badge badge-bronze">Pending</span>
                                    <?php elseif ($rp['status'] === 'expired'): ?>
                                        <span class="badge badge-slate">Expired</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:var(--red-light);color:var(--red);">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted text-sm"><?= date('M d, Y', strtotime($rp['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:24px 0;">No policies found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-center" style="margin-top:8px;margin-bottom:24px;"><span class="sn-eyebrow">Analytics</span><h3 class="sn-title" style="font-size:1.6rem;">Charts &amp; Ledgers</h3><p class="sn-sub" style="font-size:.875rem;">Collections, standing, revenue and claims at a glance.</p><span class="sn-chip" style="margin-top:10px;">6-month view</span></div>
        <div class="grid-2 mb-3" id="analytics">
            <div class="sn-card topline">
                <div style="margin-bottom:20px;">
                    <span class="sn-eyebrow">Fig. 01 &mdash; Collections Trend</span>
                    <h4>Six-Month Takings</h4>
                    <p>Verified payments over the last 6 months.</p>
                </div>
                <div style="padding:0;">
                    <div style="display:flex;gap:8px;height:210px;">
                        <?php foreach ($collections as $ckey => $cval):
                            $bh = $maxColl > 0 ? round(($cval / $maxColl) * 100) : 0;
                            $isTop = $cval > 0 && $cval >= $maxColl;
                        ?>
                        <div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;text-align:center;min-width:0;">
                            <div style="font-size:0.68rem;font-weight:700;color:var(--navy);margin-bottom:4px;white-space:nowrap;"><?= $cval > 0 ? number_format($cval / 1000, 1) . 'k' : '' ?></div>
                            <div title="<?= date('F Y', strtotime($ckey . '-01')) ?>: &#8369;<?= number_format($cval, 0) ?>" style="height:<?= $cval > 0 ? max($bh, 5) : 2 ?>%;min-height:3px;background:<?= $isTop ? 'linear-gradient(180deg,#CCB877,#B3A37F)' : 'var(--bar-fill)' ?>;border-radius:6px 6px 0 0;"></div>
                            <div style="font-size:0.68rem;color:var(--gray-500);margin-top:6px;"><?= date('M', strtotime($ckey . '-01')) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="sn-card topline">
                <div style="margin-bottom:20px;">
                    <span class="sn-eyebrow">Fig. 02 &mdash; Policies by Status</span>
                    <h4>Standing of the Book</h4>
                    <p>Where every policy stands right now.</p>
                </div>
                <div style="padding:0;">
                    <?php if (!empty($statusBreakdown)): ?>
                    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                        <svg width="150" height="150" viewBox="0 0 120 120" style="flex-shrink:0;">
                            <circle cx="60" cy="60" r="45" fill="none" style="stroke:var(--gray-200);" stroke-width="16"/>
                            <?php foreach ($donutSlices as $sl): ?>
                            <circle cx="60" cy="60" r="45" fill="none" stroke="<?= $sl['color'] ?>" style="stroke:<?= $sl['color'] ?>;" stroke-width="16" stroke-dasharray="<?= $sl['dash'] ?> 282.74" stroke-dashoffset="<?= $sl['offset'] ?>" transform="rotate(-90 60 60)"/>
                            <?php endforeach; ?>
                            <text x="60" y="59" text-anchor="middle" style="font-size:1.5rem;font-weight:800;fill:var(--navy);font-family:var(--font-serif);"><?= $totalSubs ?></text>
                            <text x="60" y="75" text-anchor="middle" style="font-size:0.65rem;fill:var(--gray-500);">policies</text>
                        </svg>
                        <div style="flex:1;min-width:140px;">
                            <?php foreach ($donutSlices as $sl): ?>
                            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:0.875rem;">
                                <span style="width:12px;height:12px;border-radius:4px;background:<?= $sl['color'] ?>;flex-shrink:0;"></span>
                                <span style="text-transform:capitalize;font-weight:500;"><?= htmlspecialchars($sl['status']) ?></span>
                                <span class="badge badge-navy" style="margin-left:auto;"><?= $sl['cnt'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center" style="padding:24px 0;">No policy data.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="sn-card topline mb-3">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Fig. 03 &mdash; Revenue by Plan</span>
                <h4>Takings per Package</h4>
                <p>Total collected per package, ranked highest first.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($topPlans)): ?>
                <?php foreach ($topPlans as $idx => $tp):
                    $rw = $maxRevenue > 0 ? round(((float)($tp['total_paid'] ?? 0) / $maxRevenue) * 100) : 0;
                ?>
                <div style="margin-bottom:14px;">
                    <div class="d-flex justify-between align-center mb-1">
                        <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($tp['name']) ?> <span class="text-muted text-xs" style="font-weight:400;">&mdash; <?= $tp['sub_count'] ?> policies</span></div>
                        <div style="font-weight:700;color:var(--navy);font-size:0.875rem;">&#8369;<?= number_format($tp['total_paid'] ?? 0, 0) ?></div>
                    </div>
                    <div style="width:100%;height:10px;background:var(--gray-200);border-radius:100px;overflow:hidden;">
                        <div style="width:<?= max($rw, 3) ?>%;height:100%;background:<?= $idx === 0 ? 'linear-gradient(90deg,#CCB877,#B3A37F)' : 'linear-gradient(90deg,#2E4A6B,#3D5A76)' ?>;border-radius:100px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted text-center" style="padding:24px 0;">No plan data.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Fig. 04 &mdash; Claims by Status</span>
                <h4>Claims Register</h4>
                <p>Count of claims at each stage of review.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($claimStats)): ?>
                <?php foreach ($claimStats as $cs): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                    <span style="text-transform:capitalize;font-weight:500;"><?= htmlspecialchars($cs['status']) ?></span>
                    <span class="badge badge-navy"><?= $cs['cnt'] ?></span>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted text-center" style="padding:24px 0;">No claims recorded.</p>
                <?php endif; ?>
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

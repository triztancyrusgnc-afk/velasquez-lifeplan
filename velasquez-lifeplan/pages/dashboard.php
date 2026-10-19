<?php
$pageTitle = 'My Dashboard';
$panelStyles = true; // pulls in css/panel.css
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Keep each role on its own panel.
if (($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: admin.php');
    exit;
}
if (($_SESSION['user_role'] ?? '') === 'employee') {
    header('Location: staff.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT s.*, p.name AS plan_name, p.category, p.price, p.features FROM subscriptions s JOIN plans p ON s.plan_id = p.id WHERE s.user_id = ? ORDER BY s.created_at DESC LIMIT 1");
$stmt->execute([$userId]);
$subscription = $stmt->fetch();

$payments = [];
$beneficiaries = [];
$recentActivity = [];

if ($subscription) {
    $stmt = $pdo->prepare("SELECT * FROM payment_schedule WHERE subscription_id = ? ORDER BY installment_no ASC");
    $stmt->execute([$subscription['id']]);
    $payments = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE subscription_id = ?");
    $stmt->execute([$subscription['id']]);
    $beneficiaries = $stmt->fetchAll();
}

$stmt = $pdo->prepare("SELECT * FROM audit_log WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$userId]);
$recentActivity = $stmt->fetchAll();

// Beneficiary add/remove (customer's own latest subscription only).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bene_action'])) {
    $subCheck = $pdo->prepare("SELECT id FROM subscriptions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $subCheck->execute([$userId]);
    $ownSub = $subCheck->fetchColumn();
    if ($ownSub) {
        if ($_POST['bene_action'] === 'add' && trim($_POST['bene_name'] ?? '') !== '') {
            $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE subscription_id = ?");
            $cntStmt->execute([$ownSub]);
            $isPrimary = ((int)$cntStmt->fetchColumn() === 0) ? 1 : 0;
            $ins = $pdo->prepare("INSERT INTO beneficiaries (subscription_id, name, relationship, phone, is_primary) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([$ownSub, trim($_POST['bene_name']), trim($_POST['bene_relationship'] ?? ''), trim($_POST['bene_phone'] ?? ''), $isPrimary]);
        } elseif ($_POST['bene_action'] === 'remove') {
            $del = $pdo->prepare("DELETE FROM beneficiaries WHERE id = ? AND subscription_id = ?");
            $del->execute([(int)($_POST['beneficiary_id'] ?? 0), $ownSub]);
        }
    }
    header('Location: dashboard.php#beneficiaries');
    exit;
}

$paidAmount = $subscription ? (float)$subscription['paid_amount'] : 0;
$totalAmount = $subscription ? (float)$subscription['total_amount'] : 1;
$progress = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100) : 0;

// Verified payments (receipts) + account health from overdue installments.
$receipts = [];
$overdueCount = 0;
if ($subscription) {
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE subscription_id = ? AND status = 'verified' ORDER BY created_at DESC");
    $stmt->execute([$subscription['id']]);
    $receipts = $stmt->fetchAll();

    foreach ($payments as $p) {
        if ($p['status'] !== 'paid' && $p['due_date'] < date('Y-m-d')) {
            $overdueCount++;
        }
    }
}
$healthLabel = 'Good Standing';
$healthStyle = 'background:var(--green-light);color:var(--green);';
if ($overdueCount >= 3) {
    $healthLabel = 'Attention Needed';
    $healthStyle = 'background:var(--red-light);color:var(--red);';
} elseif ($overdueCount >= 1) {
    $healthLabel = 'Grace Period';
    $healthStyle = 'background:#FEF3C7;color:#D97706;';
}
?>

<style>
/* Sanctuary — client portal (Harbor & Linen only: #1A2634/#2E4A6B/#6C8BAA/#BFAE8E/#CCB877/#F6F4EF/white/#E1DDD5) */
.dashboard-content{background:var(--bg) !important;}
.sn-title{color:var(--text-strong);}
.sn-sub{color:var(--accent);}
.sn-progress{height:10px;background:var(--bg);border:1px solid var(--border);border-radius:100px;overflow:hidden;}
.sn-progress>div{height:100%;background:linear-gradient(90deg,#BFAE8E,#CCB877);border-radius:100px;}
.sn-meta{font-size:.6875rem;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);font-weight:700;}
.sn-ledger-row{padding:14px 16px;border:1px solid var(--border);border-radius:16px;background:var(--surface);}
</style>

<?php
// Shared panel shell - one sidebar definition drives every signed-in panel.
$panelStyles = true;
$panelRoleLabel = 'Policy Holder';
$panelMenu = [
    ['title' => 'Main Menu', 'items' => [
        ['label' => 'My Account', 'href' => '#account', 'icon' => 'user', 'active' => true],
        ['label' => 'Browse Plans', 'href' => '../pages/plans.php', 'icon' => 'grid'],
    ]],
    ['title' => 'Account', 'items' => [
        ['label' => 'Payment History', 'href' => '#payments', 'icon' => 'wallet'],
        ['label' => 'Beneficiaries', 'href' => '#beneficiaries', 'icon' => 'heart'],
        ['label' => 'Notifications', 'href' => '#notifications', 'icon' => 'bell'],
    ]],
];
$panelMenuFooter = [
    ['label' => 'File a Claim', 'href' => '../pages/claim.php', 'icon' => 'file'],
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

        <div class="sn-band harbor mb-4" id="account" style="padding:40px 36px;">
            <span class="sn-eyebrow" style="background:var(--surface);border-color:var(--border);">Client Ledger — Private Record</span>
            <h2 class="sn-title" style="margin:0 0 8px;font-size:2.1rem;color:var(--ink-on-navy);">Welcome back, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></h2>
            <p class="sn-sub" style="color:#E1DDD5;">Here&rsquo;s an overview of your life plan account &mdash; schedule, receipts, beneficiaries and recent activity in one sanctuary.</p>
        </div>

        <?php if ($subscription): ?>
        <div class="sn-card topline mb-3">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">01 — Policy Overview</span>
                <h4 style="margin:0;color:var(--text-strong);font-size:1.4rem;">Contract &amp; Standing</h4>
                <p class="sn-sub" style="font-size:.875rem;">Your current plan, balance progress and account health.</p>
            </div>
            <div style="padding:0;">
                <div class="d-flex justify-between align-center flex-wrap gap-2 mb-2">
                    <div>
                        <h4 style="margin-bottom:2px;"><?= htmlspecialchars($subscription['plan_name']) ?></h4>
                        <p class="text-muted text-sm mb-0">Category: <?= ucfirst(htmlspecialchars($subscription['category'])) ?></p>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <span class="badge <?= $subscription['status'] === 'active' ? 'badge-sage' : ($subscription['status'] === 'pending' ? 'badge-bronze' : 'badge-slate') ?>" style="text-transform:capitalize;"><?= htmlspecialchars($subscription['status']) ?></span>
                        <span class="badge" style="<?= $healthStyle ?>"><?= $healthLabel ?><?php if ($overdueCount > 0): ?> (<?= $overdueCount ?> overdue)<?php endif; ?></span>
                    </div>
                </div>
                <?php if ($subscription['status'] === 'active'): ?>
                <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="claim.php" class="btn btn-gold btn-sm">File Emergency Claim</a>
                    <a href="compare.php" class="btn btn-outline btn-sm">Compare Plans</a>
                </div>
                <?php endif; ?>
                <div style="margin-top:18px;border-top:1px solid var(--border);padding-top:16px;">
                    <div class="d-flex justify-between mb-1">
                        <span class="sn-meta">Payment Progress</span>
                        <span class="text-sm" style="font-weight:700;color:var(--text-strong);"><?= number_format($paidAmount, 2) ?> / <?= number_format($totalAmount, 2) ?> (<?= $progress ?>%)</span>
                    </div>
                    <div class="sn-progress">
                        <div style="width:<?= $progress ?>%;"></div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap" style="margin-top:16px;">
                    <span class="sn-chip">Method — <strong style="text-transform:capitalize;color:var(--text-strong);"><?= htmlspecialchars($subscription['payment_method'] ?? 'N/A') ?></strong></span>
                    <span class="sn-chip">Start — <strong style="color:var(--text-strong);"><?= htmlspecialchars($subscription['start_date']) ?></strong></span>
                    <span class="sn-chip">End — <strong style="color:var(--text-strong);"><?= htmlspecialchars($subscription['end_date']) ?></strong></span>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="sn-card topline mb-3 sn-center" style="padding:48px 24px;">
            <div class="sn-icon" style="margin:0 auto 16px;">✦</div>
            <span class="sn-chip" style="margin-bottom:12px;">No Active Plan</span>
            <h4 style="color:var(--text-strong);">Begin Your Sanctuary Plan</h4>
            <p class="sn-sub">You don't have an active life plan yet. Browse our plans to get started.</p>
            <a href="../pages/plans.php" class="btn btn-gold">Browse Plans</a>
        </div>
        <?php endif; ?>

        <div class="sn-card topline mb-3" id="payments">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">02 — Payment Schedule</span>
                <h4 style="margin:0;color:var(--text-strong);font-size:1.4rem;">Installment Ledger</h4>
                <p class="sn-sub" style="font-size:.875rem;">Due dates, amounts and proof-of-payment actions.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($payments)): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Installment #</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $pay): ?>
                            <tr>
                                <td><?= $pay['installment_no'] ?></td>
                                <td><?= htmlspecialchars($pay['due_date']) ?></td>
                                <td style="font-weight:600;">&#8369;<?= number_format($pay['amount'], 2) ?></td>
                                <td>
                                    <?php if ($pay['status'] === 'paid'): ?>
                                        <span class="badge badge-green">Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-bronze">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($pay['status'] === 'pending'): ?>
                                        <button class="btn btn-gold btn-sm" onclick="openModal('uploadModal');document.getElementById('upload_installment_id').value='<?= $pay['id'] ?>';">Upload Proof</button>
                                    <?php else: ?>
                                        <span class="text-muted text-sm">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:24px 0;">No payment records found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="receipts">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">03 — Receipts Ledger</span>
                <h4 style="margin:0;color:var(--text-strong);font-size:1.4rem;">Verified Payments</h4>
                <p class="sn-sub" style="font-size:.875rem;">Official receipts appear here once the office verifies your uploads.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($receipts)): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Receipt No.</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receipts as $rc): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($rc['receipt_number'] ?: ('OR-' . str_pad((string)$rc['id'], 6, '0', STR_PAD_LEFT))) ?></td>
                                <td class="text-muted text-sm"><?= date('M d, Y', strtotime($rc['created_at'])) ?></td>
                                <td style="font-weight:600;">&#8369;<?= number_format($rc['amount'], 2) ?></td>
                                <td class="text-muted text-sm" style="text-transform:capitalize;"><?= htmlspecialchars($rc['payment_method'] ?: '—') ?></td>
                                <td><a href="receipt.php?id=<?= $rc['id'] ?>" class="btn btn-outline btn-sm">View / Print</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:24px 0;">No verified payments yet. Receipts appear here once the office verifies your uploads.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="beneficiaries">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">04 — Beneficiaries</span>
                <h4 style="margin:0;color:var(--text-strong);font-size:1.4rem;">Named Beneficiaries</h4>
                <p class="sn-sub" style="font-size:.875rem;">Primary designee first, followed by contingent family members.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($beneficiaries)): ?>
                <div class="grid-2" style="gap:16px;margin-bottom:20px;">
                    <?php foreach ($beneficiaries as $b): ?>
                    <div class="sn-ledger-row" style="padding:16px;border-left:3px solid #BFAE8E;">
                        <div class="d-flex align-center gap-2 mb-1">
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--surface-navy);color:#CCB877;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8125rem;flex-shrink:0;border:1px solid var(--border);">
                                <?= strtoupper(substr($b['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:0.9375rem;"><?= htmlspecialchars($b['name']) ?></div>
                                <div class="text-muted text-xs"><?= htmlspecialchars($b['relationship'] ?? '') ?><?= !empty($b['phone']) ? ' &bull; ' . htmlspecialchars($b['phone']) : '' ?></div>
                            </div>
                            <?php if ($b['is_primary']): ?>
                                <span class="badge badge-gold" style="margin-left:auto;">Primary</span>
                            <?php else: ?>
                                <form method="POST" style="margin:0 0 0 auto;" onsubmit="return confirm('Remove this beneficiary?');">
                                    <input type="hidden" name="bene_action" value="remove">
                                    <input type="hidden" name="beneficiary_id" value="<?= $b['id'] ?>">
                                    <button type="submit" class="btn btn-outline btn-sm">Remove</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:24px 0;">No beneficiaries added yet.</p>
                <?php endif; ?>
                <?php if ($subscription): ?>
                <details class="expandable">
                    <summary class="btn btn-outline btn-sm btn-block">Add Beneficiary</summary>
                    <div class="expandable-body">
                        <form method="POST" style="margin-top:16px;">
                            <input type="hidden" name="bene_action" value="add">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="bene_name" class="form-control" placeholder="Beneficiary full name" required>
                            </div>
                            <div class="grid-2" style="gap:16px;">
                                <div class="form-group">
                                    <label>Relationship</label>
                                    <input type="text" name="bene_relationship" class="form-control" placeholder="e.g. Spouse, Child">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" name="bene_phone" class="form-control" placeholder="+63 9XX XXX XXXX">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-gold btn-block">Save Beneficiary</button>
                        </form>
                    </div>
                </details>
                <?php endif; ?>
            </div>
        </div>

        <div class="sn-card topline mb-3" id="notifications">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">05 — Chronicle</span>
                <h4 style="margin:0;color:var(--text-strong);font-size:1.4rem;">Recent Activity</h4>
                <p class="sn-sub" style="font-size:.875rem;">Latest entries from your account journal, newest first.</p>
            </div>
            <div style="padding:0;">
                <?php if (!empty($recentActivity)): ?>
                <div style="border-left:2px solid var(--surface-navy);margin-left:8px;padding-left:20px;">
                    <?php foreach ($recentActivity as $log): ?>
                    <div style="position:relative;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:12px;">
                        <div style="width:10px;height:10px;border-radius:50%;background:#CCB877;border:2px solid var(--surface-navy);position:absolute;left:-27px;top:4px;"></div>
                        <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($log['action'] ?? 'Activity') ?></div>
                        <div class="text-muted text-xs"><?= htmlspecialchars($log['created_at'] ?? '') ?></div>
                        <?php if (!empty($log['details'])): ?>
                            <div class="text-sm" style="margin-top:4px;"><?= htmlspecialchars($log['details']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:24px 0;">No recent activity.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<div class="modal-overlay" id="uploadModal">
    <div class="modal" style="border-top:3px solid #CCB877;border-left:1px solid var(--border);border-right:1px solid var(--border);">
        <div class="modal-header" style="background:var(--bg);border-bottom:1px solid var(--border);">
            <div>
                <div class="sn-meta" style="margin-bottom:2px;">Sanctuary — Proof of Payment</div>
                <h3 style="margin:0;color:var(--text-strong);">Upload Payment Proof</h3>
            </div>
            <button class="modal-close" onclick="closeModal('uploadModal')">&times;</button>
        </div>
        <form method="POST" action="../includes/upload_proof.php" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="installment_id" id="upload_installment_id" value="">
                <div class="form-group">
                    <label>Select proof image or receipt</label>
                    <input type="file" name="proof_image" class="form-control" accept="image/*,.pdf" required>
                </div>
                <div class="form-group">
                    <label>Reference Number (optional)</label>
                    <input type="text" name="reference_number" class="form-control" placeholder="e.g. GCash Ref No.">
                </div>
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Any additional details..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('uploadModal')">Cancel</button>
                <button type="submit" class="btn btn-gold">Upload</button>
            </div>
        </form>
    </div>
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

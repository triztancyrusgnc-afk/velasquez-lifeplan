<?php
$pageTitle = 'Official Receipt';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$payId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT pay.*, u.name AS customer_name, u.email AS customer_email, p.name AS plan_name, s.id AS sub_id FROM payments pay JOIN subscriptions s ON pay.subscription_id = s.id JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id WHERE pay.id = ?");
$stmt->execute([$payId]);
$receipt = $stmt->fetch();

$allowed = false;
if ($receipt) {
    $role = $_SESSION['user_role'] ?? 'customer';
    if ($role === 'admin' || (int)$receipt['sub_id'] > 0 && $role === 'customer') {
        // Customers may only view their own receipts.
        $check = $pdo->prepare("SELECT user_id FROM subscriptions WHERE id = ?");
        $check->execute([$receipt['subscription_id']]);
        $owner = $check->fetchColumn();
        $allowed = ($role === 'admin') || ((int)$owner === (int)$_SESSION['user_id']);
    }
}
?>

<style>
/* Sanctuary receipt folio — Harbor & Linen only #1A2634/#2E4A6B/#6C8BAA/#BFAE8E/#CCB877/#F6F4EF/#E1DDD5 */
.receipt-folio{background:var(--bg) !important;}
.sn-ledger{width:100%;font-size:.9375rem;border-collapse:collapse;}
.sn-ledger td{padding:10px 0;border-bottom:1px solid var(--border);}
.sn-ledger td:last-child{text-align:right;}
</style>

<section class="sn-section section receipt-folio" style="padding-top:110px;background:var(--bg);min-height:80vh;">
    <div class="container" style="max-width:640px;">
        <div class="sn-center" style="margin-bottom:28px;">
            <span class="sn-eyebrow">Velasquez Funeral Service — Official Record</span>
            <h2 class="sn-title" style="font-size:2rem;">Official <em>Receipt</em></h2>
            <p class="sn-sub" style="font-size:.9375rem;">A quiet sanctuary record of your payment, kept for your family.</p>
        </div>
        <?php if (!$receipt || !$allowed): ?>
        <div class="sn-card topline sn-center" style="padding:48px 24px;">
            <span class="sn-chip" style="margin-bottom:12px;">Not available</span>
            <h4 style="color:var(--text-strong);">Receipt Not Available</h4>
            <p class="sn-sub">This receipt does not exist or belongs to another account.</p>
            <a href="dashboard.php#payments" class="btn btn-gold">Back to Dashboard</a>
        </div>
        <?php else: ?>
        <div class="text-center mb-3 no-print">
            <button class="btn btn-gold" onclick="window.print();">Print / Save as PDF</button>
            <a href="dashboard.php#payments" class="btn btn-outline">Back</a>
        </div>
        <div class="sn-card topline card" id="receiptCard" style="padding:0;overflow:hidden;">
            <div style="background:var(--surface-navy);color:var(--ink-on-navy);padding:14px 32px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <span style="font-size:.6875rem;letter-spacing:.18em;text-transform:uppercase;font-weight:700;color:#CCB877;">No. <?= htmlspecialchars($receipt['receipt_number'] ?: ('OR-' . str_pad((string)$receipt['id'], 6, '0', STR_PAD_LEFT))) ?></span>
                <span style="font-size:.6875rem;letter-spacing:.18em;text-transform:uppercase;font-weight:700;color:var(--ink-on-navy);">Official Receipt</span>
            </div>
            <div style="padding:40px;">
                <div class="sn-center" style="margin-bottom:24px;border-bottom:2px solid #1A2634;padding-bottom:20px;">
                    <img src="/velasquez-lifeplan/img/logo.svg" alt="Velasquez Funeral Service" width="56" height="56" style="border-radius:12px;margin:0 auto 12px;border:1px solid var(--border);">
                    <h3 class="sn-title" style="margin-bottom:2px;font-size:1.6rem;">Velasquez Funeral Service</h3>
                    <p class="sn-sub text-sm" style="margin-bottom:0;font-size:.875rem;">Sto. Cristo, Guagua, Pampanga &bull; +63 917 123 4567</p>
                    <div style="margin-top:12px;"><span class="sn-chip">Official Receipt</span></div>
                    <h4 style="margin-top:14px;letter-spacing:3px;color:var(--text-strong);">OFFICIAL RECEIPT</h4>
                    <p style="font-family:var(--font-serif);font-size:1.5rem;font-weight:700;color:var(--accent);margin-bottom:0;letter-spacing:.04em;"><?= htmlspecialchars($receipt['receipt_number'] ?: ('OR-' . str_pad((string)$receipt['id'], 6, '0', STR_PAD_LEFT))) ?></p>
                </div>
                <table class="sn-ledger" style="width:100%;font-size:0.9375rem;">
                    <tr><td style="color:var(--text-muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase;font-weight:700;">Received from</td><td style="text-align:right;font-weight:600;color:var(--text-strong);"><?= htmlspecialchars($receipt['customer_name']) ?></td></tr>
                    <tr><td style="color:var(--text-muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase;font-weight:700;">Life plan</td><td style="text-align:right;color:var(--text-strong);"><?= htmlspecialchars($receipt['plan_name']) ?></td></tr>
                    <tr><td style="color:var(--text-muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase;font-weight:700;">Payment date</td><td style="text-align:right;color:var(--text-strong);"><?= date('F d, Y', strtotime($receipt['created_at'])) ?></td></tr>
                    <tr><td style="color:var(--text-muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase;font-weight:700;">Method</td><td style="text-align:right;text-transform:capitalize;color:var(--text-strong);"><?= htmlspecialchars($receipt['payment_method'] ?: '—') ?></td></tr>
                    <?php if (!empty($receipt['reference_number'])): ?>
                    <tr><td style="color:var(--text-muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase;font-weight:700;">Reference</td><td style="text-align:right;color:var(--text-strong);"><?= htmlspecialchars($receipt['reference_number']) ?></td></tr>
                    <?php endif; ?>
                    <tr><td colspan="2" style="border-top:2px solid #1A2634;padding:12px 0 0;border-bottom:none;"></td></tr>
                    <tr><td style="font-weight:700;font-size:1.125rem;border:none;color:var(--text-strong);">Amount Paid</td><td style="text-align:right;font-family:var(--font-serif);font-size:1.7rem;font-weight:700;color:var(--text-strong);border:none;">&#8369;<?= number_format($receipt['amount'], 2) ?></td></tr>
                </table>
                <div class="sn-center" style="margin-top:24px;margin-bottom:0;">
                    <div class="sn-band linen" style="padding:16px 20px;border-radius:20px;">
                        <span class="sn-chip" style="background:var(--surface);">Status: <?= ucfirst(htmlspecialchars($receipt['status'])) ?> — Acknowledged</span>
                        <p class="text-muted text-xs text-center" style="margin-top:10px;margin-bottom:0;">Status: <?= ucfirst(htmlspecialchars($receipt['status'])) ?> &bull; This serves as your official acknowledgment of payment.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
@media print {
    .no-print, .navbar, .footer, .floating-emergency, #pageTransition, #pageProgress { display: none !important; }
    body { animation: none; background: #fff; }
    .section { padding: 0 !important; background: #fff !important; }
    .card { box-shadow: none !important; border: 1px solid #999 !important; }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>

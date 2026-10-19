<?php
$pageTitle = 'File Emergency Claim';
require_once __DIR__ . '/../includes/header.php';

// Customers file claims on their own policies.
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
if (($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: admin.php');
    exit;
}
if (($_SESSION['user_role'] ?? '') === 'employee') {
    header('Location: staff.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$successClaimId = null;

// Policies this customer can file against (pending review ones excluded later).
$stmt = $pdo->prepare("SELECT s.*, p.name AS plan_name FROM subscriptions s JOIN plans p ON s.plan_id = p.id WHERE s.user_id = ? AND s.status IN ('active','pending') ORDER BY s.created_at DESC");
$stmt->execute([$userId]);
$policies = $stmt->fetchAll();

// Chapels + hearses available for booking.
$chapels = $pdo->query("SELECT item_name FROM logistics_inventory WHERE category = 'chapel' AND status = 'available' ORDER BY item_name")->fetchAll();
$hearses = $pdo->query("SELECT item_name FROM logistics_inventory WHERE category = 'hearse' AND status = 'available' ORDER BY item_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deceased_name'])) {
    $subscriptionId = (int)($_POST['subscription_id'] ?? 0);
    $deceasedName = trim($_POST['deceased_name'] ?? '');
    $dateOfDeath = $_POST['date_of_death'] ?? '';
    $chapel = trim($_POST['chapel_reserved'] ?? '');
    $hearse = trim($_POST['hearse_assigned'] ?? '');
    $pickupNotes = trim($_POST['pickup_notes'] ?? '');

    // Ownership check: policy must belong to this customer.
    $stmt = $pdo->prepare("SELECT id FROM subscriptions WHERE id = ? AND user_id = ?");
    $stmt->execute([$subscriptionId, $userId]);
    if (!$stmt->fetch()) {
        $error = 'Please select one of your own policies.';
    } elseif ($deceasedName === '' || $dateOfDeath === '') {
        $error = 'Deceased name and date of death are required.';
    } elseif ($dateOfDeath > date('Y-m-d')) {
        $error = 'Date of death cannot be in the future.';
    } else {
        // One open claim per policy at a time.
        $stmt = $pdo->prepare("SELECT id FROM claims WHERE subscription_id = ? AND status IN ('pending','processing','approved') LIMIT 1");
        $stmt->execute([$subscriptionId]);
        if ($stmt->fetch()) {
            $error = 'This policy already has an open claim. Our team will contact you shortly.';
        }
    }

    // Death certificate upload (required): jpg/png/pdf, max 5MB.
    $certPath = null;
    if ($error === '') {
        if (!isset($_FILES['death_certificate']) || $_FILES['death_certificate']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Please attach the death certificate (JPG, PNG, or PDF).';
        } else {
            $ext = strtolower(pathinfo($_FILES['death_certificate']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
                $error = 'Death certificate must be a JPG, PNG, or PDF file.';
            } elseif ($_FILES['death_certificate']['size'] > 5 * 1024 * 1024) {
                $error = 'Death certificate must be smaller than 5MB.';
            } else {
                $uploadDir = __DIR__ . '/../uploads/claims/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $safeName = 'deathcert_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['death_certificate']['tmp_name'], $uploadDir . $safeName)) {
                    $certPath = 'uploads/claims/' . $safeName;
                } else {
                    $error = 'Could not save the uploaded file. Please try again.';
                }
            }
        }
    }

    if ($error === '') {
        $stmt = $pdo->prepare("INSERT INTO claims (subscription_id, user_id, claim_type, status, notes, deceased_name, date_of_death, death_certificate_path, chapel_reserved, hearse_assigned) VALUES (?, ?, 'funeral', 'pending', ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$subscriptionId, $userId, $pickupNotes, $deceasedName, $dateOfDeath, $certPath, $chapel !== '' ? $chapel : null, $hearse !== '' ? $hearse : null]);
        $successClaimId = (int)$pdo->lastInsertId();
    }
}
?>

<style>
/* Sanctuary claim folio — Harbor & Linen only #1A2634/#2E4A6B/#6C8BAA/#BFAE8E/#CCB877/#F6F4EF/#E1DDD5 */
.claim-folio{background:var(--bg) !important;}
.sn-fieldset{border:1px solid var(--border);border-radius:16px;padding:22px;background:var(--surface);margin-bottom:18px;}
.sn-fieldset legend{font-size:.6875rem;letter-spacing:.14em;text-transform:uppercase;font-weight:700;color:var(--text-strong);padding:0 8px;}
</style>

<section class="sn-section claim-folio" style="padding-top:110px;min-height:80vh;background:var(--bg);">
    <div class="container" style="max-width:760px;">
        <div class="sn-center" style="margin-bottom:32px;">
            <span class="sn-eyebrow">At-Need Emergency — Sanctuary</span>
            <h2 class="sn-title" style="margin:0 0 10px;font-size:2.3rem;">File an <em>Emergency Claim</em></h2>
            <p class="sn-sub">Our team is notified the moment you submit. For immediate help, call <a href="tel:+639171234567" style="color:var(--accent);font-weight:700;text-decoration:underline;text-decoration-color:#CCB877;">+63 917 123 4567</a> any time.</p>
            <div style="margin-top:12px;"><span class="sn-chip">24/7 response team</span></div>
        </div>

        <?php if ($successClaimId): ?>
        <div class="sn-card topline sn-center" style="padding:48px 32px;">
            <span class="sn-eyebrow">Received — Response Team Notified</span>
            <div class="sn-icon" style="margin:0 auto 16px;">✓</div>
            <h3>Claim Received</h3>
            <p class="text-muted">Reference number: <strong style="color:var(--navy);">CLM-<?= str_pad((string)$successClaimId, 6, '0', STR_PAD_LEFT) ?></strong></p>
            <p class="text-muted">A coordinator will call you shortly to arrange the chapel viewing and hearse pickup. You can track this claim from your dashboard.</p>
            <a href="dashboard.php#notifications" class="btn btn-gold">Back to Dashboard</a>
        </div>
        <?php else: ?>

        <?php if (empty($policies)): ?>
        <div class="sn-card topline sn-center" style="padding:48px 24px;">
            <span class="sn-eyebrow">No Record Found</span>
            <div class="sn-icon" style="margin:0 auto 16px;">◈</div>
            <h4 class="sn-title" style="font-size:1.5rem;">No Active Policy</h4>
            <p class="sn-sub">You need an active life plan before filing a claim.</p>
            <a href="plans.php" class="btn btn-gold">Browse Life Plans</a>
        </div>
        <?php else: ?>

        <?php if ($error): ?>
        <div style="padding:14px 20px;border-radius:var(--radius);background:var(--red-light);color:var(--red);border:1px solid var(--red);margin-bottom:20px;font-size:0.9375rem;font-weight:500;">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="sn-card topline">
            <div style="margin-bottom:20px;">
                <span class="sn-eyebrow">Claim Form — Three Sections</span>
                <h4 style="font-family:var(--font-serif);color:var(--text-strong);margin:8px 0 4px;">Emergency Details</h4>
                <p class="sn-sub" style="font-size:.8125rem;">Complete each section in order. Required fields are marked *.</p>
            </div>
            <div style="padding:0;">
                <form method="POST" enctype="multipart/form-data">
                    <fieldset class="sn-fieldset">
                        <legend>I — Policy</legend>
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;"><span class="sn-stepnum" style="margin-bottom:0;width:40px;height:40px;font-size:.95rem;">1</span><span class="sn-chip">Your policy</span></div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Policy <span style="color:var(--red);">*</span></label>
                            <select name="subscription_id" class="form-control" required>
                                <?php foreach ($policies as $po): ?>
                                <option value="<?= $po['id'] ?>"><?= htmlspecialchars($po['plan_name']) ?> — since <?= htmlspecialchars($po['start_date']) ?> (<?= ucfirst($po['status']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="sn-fieldset">
                        <legend>II — Departed &amp; Certificate</legend>
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;"><span class="sn-stepnum" style="margin-bottom:0;width:40px;height:40px;font-size:.95rem;">2</span><span class="sn-chip">Departed details</span></div>
                        <div class="grid-2" style="gap:16px;">
                            <div class="form-group">
                                <label>Deceased Full Name <span style="color:var(--red);">*</span></label>
                                <input type="text" name="deceased_name" class="form-control" placeholder="Full name of the departed" required value="<?= htmlspecialchars($_POST['deceased_name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Date of Death <span style="color:var(--red);">*</span></label>
                                <input type="date" name="date_of_death" class="form-control" max="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($_POST['date_of_death'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Death Certificate (JPG, PNG, or PDF — max 5MB) <span style="color:var(--red);">*</span></label>
                            <input type="file" name="death_certificate" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                    </fieldset>
                    <fieldset class="sn-fieldset">
                        <legend>III — Chapel, Hearse &amp; Pickup</legend>
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;"><span class="sn-stepnum" style="margin-bottom:0;width:40px;height:40px;font-size:.95rem;">3</span><span class="sn-chip">Arrangements</span></div>
                        <div class="grid-2" style="gap:16px;">
                            <div class="form-group">
                                <label>Chapel Viewing Room</label>
                                <select name="chapel_reserved" class="form-control">
                                    <option value="">— To be arranged —</option>
                                    <?php foreach ($chapels as $ch): ?>
                                    <option value="<?= htmlspecialchars($ch['item_name']) ?>"><?= htmlspecialchars($ch['item_name']) ?></option>
                                    <?php endforeach; ?>
                                    <option value="Family home">Family home / other venue</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Hearse for Pickup</label>
                                <select name="hearse_assigned" class="form-control">
                                    <option value="">— To be arranged —</option>
                                    <?php foreach ($hearses as $he): ?>
                                    <option value="<?= htmlspecialchars($he['item_name']) ?>"><?= htmlspecialchars($he['item_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Pickup Address & Notes</label>
                            <textarea name="pickup_notes" class="form-control" rows="3" placeholder="Where should the hearse go? Any special requests?"><?= htmlspecialchars($_POST['pickup_notes'] ?? '') ?></textarea>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-gold btn-block btn-lg">Submit Emergency Claim</button>
                    <p class="text-muted text-xs text-center" style="margin-top:12px;">Submitting notifies our 24/7 response team immediately.</p>
                </form>
            </div>
        </div>
        <div class="sn-band linen" style="padding:28px;margin-top:20px;">
            <span class="sn-chip" style="background:var(--surface);">A quiet sanctuary</span>
            <p class="sn-sub" style="font-size:.875rem;margin:10px 0 0;">Take your time. You may save your certificate file nearby and complete the three sections above in order.</p>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

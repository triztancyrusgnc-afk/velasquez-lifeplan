<?php
$pageTitle = 'Checkout';
require_once 'includes/header.php';

// When the admin has paused the life plan catalogue, stop here - before any
// session checkout state is touched or any order is written.
require_once __DIR__ . '/includes/under_construction.php';
guardPlansSection($siteSettings);

if (!isset($_SESSION['checkout'])) {
    $_SESSION['checkout'] = ['step' => 1];
}

$step = $_SESSION['checkout']['step'] ?? 1;
$error = '';

$plans = $pdo->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY price ASC")->fetchAll();

// Preselect plan from URL (e.g. "Select & Buy" buttons link here with ?plan_id=)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['plan_id'])) {
    $preselectId = (int)$_GET['plan_id'];
    $checkStmt = $pdo->prepare("SELECT id FROM plans WHERE id = ? AND is_active = 1");
    $checkStmt->execute([$preselectId]);
    if ($checkStmt->fetch()) {
        $_SESSION['checkout']['plan_id'] = $preselectId;
        $_SESSION['checkout']['step'] = 1;
        $step = 1;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step'])) {
        $currentStep = (int)$_POST['step'];

        if ($currentStep === 1) {
            $_SESSION['checkout']['plan_id'] = (int)($_POST['plan_id'] ?? 0);
            $_SESSION['checkout']['payment_term'] = $_POST['payment_term'] ?? '5yr';
            $_SESSION['checkout']['step'] = 2;
        } elseif ($currentStep === 2) {
            $_SESSION['checkout']['holder_name'] = trim($_POST['holder_name'] ?? '');
            $_SESSION['checkout']['holder_email'] = trim($_POST['holder_email'] ?? '');
            $_SESSION['checkout']['holder_phone'] = trim($_POST['holder_phone'] ?? '');
            $_SESSION['checkout']['holder_address'] = trim($_POST['holder_address'] ?? '');
            $_SESSION['checkout']['step'] = 3;
        } elseif ($currentStep === 3) {
            $_SESSION['checkout']['step'] = 4;
        } elseif ($currentStep === 4) {
            $_SESSION['checkout']['signature'] = $_POST['signature_data'] ?? '';
            $_SESSION['checkout']['step'] = 5;
        } elseif ($currentStep === 5) {
            $_SESSION['checkout']['payment_method'] = $_POST['payment_method'] ?? 'cash';
            $data = $_SESSION['checkout'];

            $planStmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
            $planStmt->execute([$data['plan_id']]);
            $plan = $planStmt->fetch();

            if (!$plan) {
                $error = 'Invalid plan selected.';
                $_SESSION['checkout']['step'] = 1;
            } else {
                // Reuse the existing account when the email is already registered
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$data['holder_email']]);
                $existingUser = $stmt->fetch();
                if ($existingUser) {
                    $userId = $existingUser['id'];
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
                    $stmt->execute([$data['holder_name'], $data['holder_phone'], $data['holder_address'], $userId]);
                } else {
                    $tempPass = password_hash('customer123', PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'customer')");
                    $stmt->execute([$data['holder_name'], $data['holder_email'], $tempPass, $data['holder_phone'], $data['holder_address']]);
                    $userId = $pdo->lastInsertId();
                }

                $totalAmount = (float)$plan['price'];
                $downpayment = (float)$plan['downpayment'];
                $paidAmount = $downpayment;
                $remaining = $totalAmount - $downpayment;
                $termMonths = $data['payment_term'] === '10yr' ? 120 : 60;
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d', strtotime("+$termMonths months"));

                $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, status, payment_method, total_amount, paid_amount, remaining_balance, downpayment, start_date, end_date) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $data['plan_id'], $data['payment_method'], $totalAmount, $paidAmount, $remaining, $downpayment, $startDate, $endDate]);
                $subId = $pdo->lastInsertId();

                $installmentAmount = $remaining / $termMonths;
                for ($i = 1; $i <= $termMonths; $i++) {
                    $dueDate = date('Y-m-d', strtotime("+$i months", strtotime($startDate)));
                    $stmt = $pdo->prepare("INSERT INTO payment_schedule (subscription_id, installment_no, due_date, amount, status) VALUES (?, ?, ?, ?, 'pending')");
                    $stmt->execute([$subId, $i, $dueDate, $installmentAmount]);
                }

                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $data['holder_name'];
                $_SESSION['user_role'] = 'customer';
                $_SESSION['user_email'] = $data['holder_email'];
                unset($_SESSION['checkout']);
                header('Location: pages/dashboard.php');
                exit;
            }
        }
        $step = $_SESSION['checkout']['step'];
    }

    if (isset($_POST['prev_step'])) {
        $prev = max(1, (int)$_POST['prev_step'] - 1);
        $_SESSION['checkout']['step'] = $prev;
        $step = $prev;
    }
}

$selectedPlan = null;
if (!empty($_SESSION['checkout']['plan_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->execute([$_SESSION['checkout']['plan_id']]);
    $selectedPlan = $stmt->fetch();
}
?>

<style>
.co-term-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.co-actions{display:flex;justify-content:space-between;gap:12px;margin-top:24px;flex-wrap:wrap;}
.sn-steps{display:flex;justify-content:center;gap:8px;margin-bottom:36px;flex-wrap:wrap;}
.sn-step{display:flex;flex-direction:column;align-items:center;gap:8px;min-width:110px;text-align:center;}
.sn-step-label{font-size:0.8rem;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:var(--text-muted);}
.sn-step.active .sn-step-label{color:var(--text-strong);}
@media(max-width:640px){
  .co-term-grid{grid-template-columns:1fr;}
  .co-actions .btn{flex:1 1 100%;}
  .sn-steps{gap:12px;}
  .sn-step{min-width:88px;}
}
</style>

<section class="sn-section" style="background:var(--bg);padding-top:130px;">
    <div class="container" style="max-width: 860px;">

        <div class="sn-center">
            <span class="sn-eyebrow">Secure checkout</span>
            <h2 class="sn-title">Complete your <em>application</em></h2>
            <p class="sn-sub">Five short steps — your progress is saved as you go.</p>
        </div>

        <?php if ($error): ?>
            <div style="padding: 14px 24px; border-radius: var(--radius); font-size: 0.9375rem; font-weight: 500; background: var(--red-light); color: var(--red); border: 1px solid var(--red); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="checkout-steps sn-steps mb-4">
            <div class="checkout-step sn-step <?= $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' ?>">
                <div class="checkout-step-num sn-stepnum"><?= $step > 1 ? '&#10003;' : '1' ?></div>
                <span class="checkout-step-label sn-step-label">Plan</span>
            </div>
            <div class="checkout-step sn-step <?= $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' ?>">
                <div class="checkout-step-num sn-stepnum"><?= $step > 2 ? '&#10003;' : '2' ?></div>
                <span class="checkout-step-label sn-step-label">Info</span>
            </div>
            <div class="checkout-step sn-step <?= $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' ?>">
                <div class="checkout-step-num sn-stepnum"><?= $step > 3 ? '&#10003;' : '3' ?></div>
                <span class="checkout-step-label sn-step-label">ID</span>
            </div>
            <div class="checkout-step sn-step <?= $step >= 4 ? ($step > 4 ? 'completed' : 'active') : '' ?>">
                <div class="checkout-step-num sn-stepnum"><?= $step > 4 ? '&#10003;' : '4' ?></div>
                <span class="checkout-step-label sn-step-label">Signature</span>
            </div>
            <div class="checkout-step sn-step <?= $step >= 5 ? 'active' : '' ?>">
                <div class="checkout-step-num sn-stepnum">5</div>
                <span class="checkout-step-label sn-step-label">Payment</span>
            </div>
        </div>

        <div class="sn-card topline" style="padding: 40px;">

                <?php if ($step === 1): ?>
                <div class="ed-kicker">Step 1 of 5 — Plan</div>
                <h3 class="mb-3">Select Plan & Term</h3>
                <form method="POST">
                    <input type="hidden" name="step" value="1">
                    <div class="form-group">
                        <label>Life Plan</label>
                        <select name="plan_id" class="form-control" required>
                            <option value="">-- Select a Plan --</option>
                            <?php foreach ($plans as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($_SESSION['checkout']['plan_id']) && $_SESSION['checkout']['plan_id'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['name']) ?> - &#8369;<?= number_format($p['price'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Term</label>
                        <div class="co-term-grid">
                            <label style="display:flex;align-items:center;gap:12px;padding:16px;border:2px solid var(--gray-200);border-radius:var(--radius);cursor:pointer;background:var(--surface);">
                                <input type="radio" name="payment_term" value="5yr" <?= (($_SESSION['checkout']['payment_term'] ?? '') === '5yr') ? 'checked' : 'checked' ?> style="accent-color:var(--gold);">
                                <div>
                                    <div style="font-weight:600;">5-Year Term</div>
                                    <div class="text-muted text-sm">60 monthly installments</div>
                                </div>
                            </label>
                            <label style="display:flex;align-items:center;gap:12px;padding:16px;border:2px solid var(--gray-200);border-radius:var(--radius);cursor:pointer;background:var(--surface);">
                                <input type="radio" name="payment_term" value="10yr" <?= (($_SESSION['checkout']['payment_term'] ?? '') === '10yr') ? 'checked' : '' ?> style="accent-color:var(--gold);">
                                <div>
                                    <div style="font-weight:600;">10-Year Term</div>
                                    <div class="text-muted text-sm">120 monthly installments</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div style="text-align:right;margin-top:24px;">
                        <button type="submit" class="btn btn-gold btn-lg">Continue</button>
                    </div>
                </form>


                <?php elseif ($step === 2): ?>
                <div class="ed-kicker">Step 2 of 5 — Details</div>
                <h3 class="mb-3">Policyholder Information</h3>
                <form method="POST">
                    <input type="hidden" name="step" value="2">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="holder_name" class="form-control" value="<?= htmlspecialchars($_SESSION['checkout']['holder_name'] ?? '') ?>" required placeholder="Juan Dela Cruz">
                    </div>
                    <div class="grid-2" style="gap:16px;">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="holder_email" class="form-control" value="<?= htmlspecialchars($_SESSION['checkout']['holder_email'] ?? '') ?>" required placeholder="you@example.com">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="holder_phone" class="form-control" value="<?= htmlspecialchars($_SESSION['checkout']['holder_phone'] ?? '') ?>" required placeholder="+63 917 123 4567">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="holder_address" class="form-control" rows="3" required placeholder="Complete address"><?= htmlspecialchars($_SESSION['checkout']['holder_address'] ?? '') ?></textarea>
                    </div>
                    <div class="co-actions">
                        <button type="submit" name="prev_step" value="2" class="btn btn-outline btn-lg">Back</button>
                        <button type="submit" class="btn btn-gold btn-lg">Continue</button>
                    </div>
                </form>

                <?php elseif ($step === 3): ?>
                <div class="ed-kicker">Step 3 of 5 — Verify</div>
                <h3 class="mb-3">ID Verification</h3>
                <form method="POST">
                    <input type="hidden" name="step" value="3">
                    <div class="text-center" style="padding:40px 20px;border:2px dashed var(--gray-300);border-radius:var(--radius-md);background:var(--bg);margin-bottom:24px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="1.5" style="margin:0 auto 16px;display:block;"><rect x="2" y="3" width="20" height="18" rx="2"/><circle cx="8" cy="10" r="2"/><path d="M16 9h2"/><path d="M16 13h2"/><path d="M6 17h8"/></svg>
                        <h4 style="color:var(--gray-600);margin-bottom:8px;">Upload Valid Government ID</h4>
                        <p class="text-muted text-sm">Accepted: Passport, Driver's License, PhilSys ID, SSS ID</p>
                        <input type="file" name="id_file" class="form-control" accept="image/*,.pdf" style="max-width:400px;margin:16px auto 0;background:var(--surface);">
                    </div>
                    <p class="text-muted text-sm text-center">You may proceed without uploading. Our team will verify your identity during processing.</p>
                    <div class="co-actions">
                        <button type="submit" name="prev_step" value="3" class="btn btn-outline btn-lg">Back</button>
                        <button type="submit" class="btn btn-gold btn-lg">Continue</button>
                    </div>
                </form>

                <?php elseif ($step === 4): ?>
                <div class="ed-kicker">Step 4 of 5 — Confirm</div>
                <h3 class="mb-3">Digital Signature</h3>
                <form method="POST" id="signatureForm">
                    <input type="hidden" name="step" value="4">
                    <input type="hidden" name="signature_data" id="signatureData" value="">
                    <div class="text-center mb-3">
                        <p class="text-muted">Please sign below to confirm your application.</p>
                    </div>
                    <div style="border:2px solid var(--gray-300);border-radius:var(--radius-md);background:var(--white);margin-bottom:16px;position:relative;">
                        <canvas id="signaturePad" width="720" height="200" style="width:100%;height:200px;cursor:crosshair;"></canvas>
                        <button type="button" class="btn btn-outline btn-sm" style="position:absolute;top:8px;right:8px;" onclick="clearSignature()">Clear</button>
                    </div>
                    <p class="text-muted text-xs text-center">By signing, you agree to the terms and conditions of the Velasquez Life Plan.</p>
                    <div class="co-actions">
                        <button type="submit" name="prev_step" value="4" class="btn btn-outline btn-lg">Back</button>
                        <button type="submit" class="btn btn-gold btn-lg">Continue</button>
                    </div>
                </form>

                <?php elseif ($step === 5): ?>
                <div class="ed-kicker">Step 5 of 5 — Pay</div>
                <h3 class="mb-3">Downpayment & Payment Method</h3>

                <?php if ($selectedPlan): ?>
                <div class="sn-band linen" style="padding:28px;margin-bottom:24px;">
                    <div class="d-flex justify-between align-center mb-1 flex-wrap gap-2">
                        <h4 style="margin-bottom:0;"><?= htmlspecialchars($selectedPlan['name']) ?></h4>
                        <span class="badge badge-gold" style="text-transform:capitalize;"><?= htmlspecialchars($_SESSION['checkout']['payment_term'] ?? '5yr') ?> Term</span>
                    </div>
                    <div class="d-flex gap-3 flex-wrap" style="margin-top:12px;">
                        <div class="text-sm"><span class="text-muted">Total:</span> <strong>&#8369;<?= number_format($selectedPlan['price'], 2) ?></strong></div>
                        <div class="text-sm"><span class="text-muted">Downpayment:</span> <strong>&#8369;<?= number_format($selectedPlan['downpayment'], 2) ?></strong></div>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="step" value="5">
                    <div class="form-group">
                        <label>Payment Method for Downpayment</label>
                        <div style="display:grid;gap:10px;">
                            <label style="display:flex;align-items:center;gap:12px;padding:16px;border:2px solid var(--gray-200);border-radius:var(--radius);cursor:pointer;transition:all 0.2s;background:var(--surface);">
                                <input type="radio" name="payment_method" value="gcash" style="accent-color:var(--gold);" checked>
                                <div>
                                    <div style="font-weight:600;">GCash</div>
                                    <div class="text-muted text-sm">Send to 09171234567 (Velasquez Funeral)</div>
                                </div>
                            </label>
                            <label style="display:flex;align-items:center;gap:12px;padding:16px;border:2px solid var(--gray-200);border-radius:var(--radius);cursor:pointer;transition:all 0.2s;background:var(--surface);">
                                <input type="radio" name="payment_method" value="bank_transfer" style="accent-color:var(--gold);">
                                <div>
                                    <div style="font-weight:600;">Bank Transfer</div>
                                    <div class="text-muted text-sm">BDO Savings Acct: 1234-5678-9012</div>
                                </div>
                            </label>
                            <label style="display:flex;align-items:center;gap:12px;padding:16px;border:2px solid var(--gray-200);border-radius:var(--radius);cursor:pointer;transition:all 0.2s;background:var(--surface);">
                                <input type="radio" name="payment_method" value="cash" style="accent-color:var(--gold);">
                                <div>
                                    <div style="font-weight:600;">Cash</div>
                                    <div class="text-muted text-sm">Pay at Velasquez Funeral Service, 123 Memorial Ave</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="co-actions">
                        <button type="submit" name="prev_step" value="5" class="btn btn-outline btn-lg">Back</button>
                        <button type="submit" class="btn btn-gold btn-lg" style="min-width:200px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Submit Application
                        </button>
                    </div>
                </form>
                <?php endif; ?>

        </div>
    </div>
</section>


<script>
var canvas, ctx, drawing = false, lastX = 0, lastY = 0;

function initSignaturePad() {
    canvas = document.getElementById('signaturePad');
    if (!canvas) return;
    ctx = canvas.getContext('2d');

    function resizeCanvas() {
        var rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = 200;
        ctx.strokeStyle = '#1A2634';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    canvas.addEventListener('mousedown', function(e) {
        drawing = true;
        var rect = canvas.getBoundingClientRect();
        lastX = e.clientX - rect.left;
        lastY = e.clientY - rect.top;
    });

    canvas.addEventListener('mousemove', function(e) {
        if (!drawing) return;
        var rect = canvas.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.stroke();
        lastX = x;
        lastY = y;
    });

    canvas.addEventListener('mouseup', function() { drawing = false; });
    canvas.addEventListener('mouseleave', function() { drawing = false; });

    canvas.addEventListener('touchstart', function(e) {
        e.preventDefault();
        drawing = true;
        var rect = canvas.getBoundingClientRect();
        var touch = e.touches[0];
        lastX = touch.clientX - rect.left;
        lastY = touch.clientY - rect.top;
    });

    canvas.addEventListener('touchmove', function(e) {
        e.preventDefault();
        if (!drawing) return;
        var rect = canvas.getBoundingClientRect();
        var touch = e.touches[0];
        var x = touch.clientX - rect.left;
        var y = touch.clientY - rect.top;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.stroke();
        lastX = x;
        lastY = y;
    });

    canvas.addEventListener('touchend', function() { drawing = false; });
}

function clearSignature() {
    if (ctx && canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
}

document.addEventListener('DOMContentLoaded', initSignaturePad);
</script>

<?php include 'includes/footer.php'; ?>

<?php
$pageTitle = 'Sign In';
require_once 'includes/header.php';

// Log out FIRST — before anything that redirects logged-in users away.
if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: login.php');
    exit;
}

// Already signed in? Send users straight to their panel.
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'] ?? 'customer';
    header('Location: ' . ($role === 'admin' ? 'pages/admin.php' : ($role === 'employee' ? 'pages/staff.php' : 'pages/dashboard.php')));
    exit;
}

$error = '';
$activeTab = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login_email'])) {
        $email = trim($_POST['login_email']);
        $password = $_POST['login_password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];

                if ($user['role'] === 'admin') {
                    header('Location: pages/admin.php');
                } elseif ($user['role'] === 'employee') {
                    header('Location: pages/staff.php');
                } else {
                    header('Location: pages/dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        }
        $activeTab = 'login';
    }

    if (isset($_POST['register_name'])) {
        $name = trim($_POST['register_name']);
        $email = trim($_POST['register_email']);
        $password = $_POST['register_password'] ?? '';
        $confirm = $_POST['register_confirm'] ?? '';
        $phone = trim($_POST['register_phone'] ?? '');

        if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
            $error = 'Please fill in all required fields.';
            $activeTab = 'register';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
            $activeTab = 'register';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
            $activeTab = 'register';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered.';
                $activeTab = 'register';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
                $stmt->execute([$name, $email, $hashed, $phone]);

                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $newUser = $stmt->fetch();

                $_SESSION['user_id'] = $newUser['id'];
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'customer';
                $_SESSION['user_email'] = $email;

                header('Location: pages/dashboard.php');
                exit;
            }
        }
    }
}
?>

<style>
.auth-split{max-width:980px;margin:0 auto;display:grid;grid-template-columns:1fr 1.25fr;background:var(--surface);border:1px solid var(--border);border-radius:28px;overflow:hidden;box-shadow:0 18px 40px rgba(46,74,107,0.14);}
.auth-brand{background:linear-gradient(160deg,#1A2634 0%,#2E4A6B 100%);color:var(--ink-on-navy);padding:48px 40px;display:flex;flex-direction:column;justify-content:center;}
.auth-panel{padding:44px 40px;}
@media(max-width:900px){
  .auth-split{grid-template-columns:1fr;}
  .auth-brand{padding:36px 28px;}
  .auth-panel{padding:32px 24px;}
}
</style>

<section class="sn-section" style="background:var(--bg);padding-top:130px;">
    <div class="container">
        <div class="sn-center" style="max-width:980px;">
            <span class="sn-eyebrow">Member access</span>
            <h2 class="sn-title">Welcome <em>Back</em></h2>
            <p class="sn-sub">Sign in to your Velasquez Life Plan account</p>
        </div>
        <div class="auth-container" style="max-width:980px;padding:0;">
            <div class="auth-split">
                <div class="auth-brand">
                    <div class="sn-icon" style="margin-bottom:20px;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <img src="img/logo.svg" alt="Velasquez Funeral Service" width="72" height="72" style="margin:0 0 20px;border-radius:14px;background:var(--bg);padding:4px;">
                    <span class="badge badge-gold mb-2" style="align-self:flex-start;">Harbor Blue &amp; Linen</span>
                    <h2 style="color:var(--ink-on-navy);margin-bottom:8px;">Care, kept close.</h2>
                    <p style="color:rgba(255,255,255,0.75);margin-bottom:24px;">Manage plans, track payments, and reach our team — all in one secure place.</p>
                    <div class="ledger" style="background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.16);">
                        <div class="ledger-row accent" style="border-color:rgba(255,255,255,0.14);padding:14px 18px;">
                            <span class="text-sm" style="color:rgba(255,255,255,0.85);">Secure sign-in with 24/7 account access</span>
                        </div>
                        <div class="ledger-row" style="border-color:rgba(255,255,255,0.14);padding:14px 18px;">
                            <span class="text-sm" style="color:rgba(255,255,255,0.85);">Payments, documents &amp; support in one portal</span>
                        </div>
                    </div>
                </div>
                <div class="auth-panel">
                    <?php if ($error): ?>
                        <div style="padding: 14px 24px; border-radius: var(--radius); font-size: 0.9375rem; font-weight: 500; background: var(--red-light); color: var(--red); border: 1px solid var(--red); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="auth-box" style="border:none;box-shadow:none;padding:0;background:transparent;">
                        <div class="auth-tabs">
                            <button type="button" class="auth-tab <?= $activeTab === 'login' ? 'active' : '' ?>" data-type="login">Sign In</button>
                            <button type="button" class="auth-tab <?= $activeTab === 'register' ? 'active' : '' ?>" data-type="register">Register</button>
                        </div>

                        <form method="POST" class="auth-form <?= $activeTab === 'login' ? 'active' : '' ?>" id="loginForm">
                            <div class="form-group">
                                <label for="login_email">Email Address</label>
                                <input type="email" class="form-control" id="login_email" name="login_email" placeholder="you@example.com" required>
                            </div>

                            <div class="form-group">
                                <label for="login_password">Password</label>
                                <input type="password" class="form-control" id="login_password" name="login_password" placeholder="Enter your password" required>
                            </div>

                            <button type="submit" class="btn btn-gold btn-block btn-lg" style="margin-top: 8px;">
                                Sign In
                            </button>

                            <div class="auth-divider">Demo Accounts</div>

                            <div style="display: grid; gap: 8px;">
                                <button type="button" class="btn btn-outline btn-block" onclick="document.getElementById('login_email').value='admin@velasquez.com';document.getElementById('login_password').value='admin123';document.getElementById('loginForm').submit();">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                    Admin &mdash; admin@velasquez.com
                                </button>
                                <button type="button" class="btn btn-outline btn-block" onclick="document.getElementById('login_email').value='employee@velasquez.com';document.getElementById('login_password').value='employee123';document.getElementById('loginForm').submit();">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Employee &mdash; employee@velasquez.com
                                </button>
                                <button type="button" class="btn btn-outline btn-block" onclick="document.getElementById('login_email').value='maria.santos@gmail.com';document.getElementById('login_password').value='customer123';document.getElementById('loginForm').submit();">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Customer &mdash; maria.santos@gmail.com
                                </button>
                            </div>
                        </form>

                        <form method="POST" class="auth-form <?= $activeTab === 'register' ? 'active' : '' ?>" id="registerForm">
                            <div class="form-group">
                                <label for="register_name">Full Name</label>
                                <input type="text" class="form-control" id="register_name" name="register_name" placeholder="Juan Dela Cruz" required>
                            </div>

                            <div class="form-group">
                                <label for="register_email">Email Address</label>
                                <input type="email" class="form-control" id="register_email" name="register_email" placeholder="you@example.com" required>
                            </div>

                            <div class="form-group">
                                <label for="register_phone">Phone Number</label>
                                <input type="tel" class="form-control" id="register_phone" name="register_phone" placeholder="+63 917 123 4567">
                            </div>

                            <div class="form-group">
                                <label for="register_password">Password</label>
                                <input type="password" class="form-control" id="register_password" name="register_password" placeholder="Min. 6 characters" required minlength="6">
                            </div>

                            <div class="form-group">
                                <label for="register_confirm">Confirm Password</label>
                                <input type="password" class="form-control" id="register_confirm" name="register_confirm" placeholder="Re-enter password" required>
                            </div>

                            <button type="submit" class="btn btn-gold btn-block btn-lg" style="margin-top: 8px;">
                                Create Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

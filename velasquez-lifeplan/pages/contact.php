<?php
$pageTitle = 'Contact Us';
require_once __DIR__ . '/../includes/header.php';

$subjects = ['General Inquiry', 'Life Plan Information', 'Claims Assistance', 'Payment Concern', 'Feedback & Suggestions', 'Partnership Opportunity', 'Other'];
$success = false;
$errors = [];

// Prefill the form from links like contact.php?subject=...&message=...
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isset($_GET['subject']) && in_array($_GET['subject'], $subjects, true)) {
        $_POST['subject'] = $_GET['subject'];
    }
    if (isset($_GET['message'])) {
        $_POST['message'] = trim($_GET['message']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if ($message === '') $errors[] = 'Message is required.';

    if (empty($errors)) {
        // Store inquiry in database
        try {
            $stmt = $pdo->prepare('INSERT INTO contact_inquiries (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $email, $phone, $subject, $message]);
        } catch (PDOException $e) {
            // Table may not exist yet; silently continue
        }
        $success = true;
    }
}
?>

<style>
.contact-split{display:grid;grid-template-columns:1fr 1.15fr;gap:32px;align-items:start;}
.sn-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
.sn-cards.two{grid-template-columns:repeat(2,1fr);}
.branch-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
.hours-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:900px;margin:0 auto;}
@media(max-width:960px){
  .contact-split{grid-template-columns:1fr;gap:24px;}
  .branch-grid{grid-template-columns:1fr;}
  .hours-grid{grid-template-columns:1fr;}
  .sn-cards{grid-template-columns:1fr;}
  .sn-cards.two{grid-template-columns:1fr;}
}
</style>

<section class="ed-hero" style="background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);color:var(--ink-on-navy);">
    <div class="container">
        <span class="badge badge-gold mb-2">Get In Touch</span>
        <h1 class="ed-title" style="color:var(--ink-on-navy);">Contact <span style="color:#CCB877;">Us</span></h1>
        <p class="ed-lede" style="color:rgba(255,255,255,0.78);">We're here to help you 24/7. Reach out to us anytime — your concerns matter to us.</p>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Direct lines</span>
            <h2 class="sn-title">Reach us, <em>day or night</em></h2>
            <p class="sn-sub">Three ways to get help fast — a 24/7 hotline, email, and our main office.</p>
        </div>
        <div class="sn-cards">
            <div class="sn-card topline text-center fade-in">
                <div class="sn-icon" style="margin-left:auto;margin-right:auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
                <h5 style="margin-bottom:4px;">Phone</h5>
                <p style="color:var(--text-strong);font-weight:600;font-size:1.1rem;margin-bottom:4px;">+63 917 123 4567</p>
                <p class="text-muted text-sm mb-0">24/7 Emergency Hotline</p>
            </div>

            <div class="sn-card topline text-center fade-in">
                <div class="sn-icon" style="margin-left:auto;margin-right:auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <h5 style="margin-bottom:4px;">Email</h5>
                <p style="color:var(--text-strong);font-weight:600;font-size:1.1rem;margin-bottom:4px;">info@velasquezfuneral.com</p>
                <p class="text-muted text-sm mb-0">We respond within 24 hours</p>
            </div>

            <div class="sn-card topline text-center fade-in">
                <div class="sn-icon" style="margin-left:auto;margin-right:auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <h5 style="margin-bottom:4px;">Address</h5>
                <p style="color:var(--text-strong);font-weight:600;font-size:1.1rem;margin-bottom:4px;">Sto. Cristo, Guagua, Pampanga</p>
                <p class="text-muted text-sm mb-0">Main Office & Chapel</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Send a message</span>
            <h2 class="sn-title">Let's <em>Talk</em></h2>
            <p class="sn-sub">Have questions about our life plans, services, or need assistance with a claim? Fill out the form and we'll get back to you promptly.</p>
        </div>
        <div class="contact-split">
            <div class="fade-in">
                <div class="sn-card" style="padding:28px;display:grid;gap:16px;">
                    <div class="d-flex align-center gap-2">
                        <div class="sn-icon" style="width:48px;height:48px;border-radius:14px;margin-bottom:0;flex-shrink:0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:0.8rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:0.5px;">Emergency Hotline</div>
                            <div style="font-weight:600;color:var(--text-strong);">+63 917 123 4567</div>
                        </div>
                    </div>
                    <div class="d-flex align-center gap-2">
                        <div class="sn-icon" style="width:48px;height:48px;border-radius:14px;margin-bottom:0;flex-shrink:0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <div style="font-size:0.8rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:0.5px;">Email Us</div>
                            <div style="font-weight:600;color:var(--text-strong);">info@velasquezfuneral.com</div>
                        </div>
                    </div>
                    <div class="d-flex align-center gap-2">
                        <div class="sn-icon" style="width:48px;height:48px;border-radius:14px;margin-bottom:0;flex-shrink:0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <div style="font-size:0.8rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:0.5px;">Response Time</div>
                            <div style="font-weight:600;color:var(--text-strong);">Within 24 Hours</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sn-card fade-in" style="padding:32px;">
                <?php if ($success): ?>
                    <div style="text-align:center;padding:32px 0;">
                        <div style="width:72px;height:72px;border-radius:50%;background:var(--green-light);color:var(--green);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <h4 style="margin-bottom:8px;">Message Sent!</h4>
                        <p class="text-muted">Thank you for reaching out. We'll get back to you within 24 hours.</p>
                        <a href="contact.php" class="btn btn-gold">Send Another Message</a>
                    </div>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div style="background:var(--red-light);border:1px solid var(--red);border-radius:var(--radius);padding:12px 16px;margin-bottom:16px;">
                            <?php foreach ($errors as $error): ?>
                                <p style="color:var(--red);margin:0;font-size:0.875rem;">&#8226; <?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Full Name <span style="color:var(--red);">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="grid-2">
                            <div class="form-group">
                                <label for="email">Email Address <span style="color:var(--red);">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+63 9XX XXX XXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span style="color:var(--red);">*</span></label>
                            <select id="subject" name="subject" class="form-control" required>
                                <option value="">Select a subject...</option>
                                <?php foreach ($subjects as $s): ?>
                                    <option value="<?= htmlspecialchars($s) ?>" <?= ($_POST['subject'] ?? '') === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message <span style="color:var(--red);">*</span></label>
                            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Tell us how we can help you..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-gold btn-block" style="margin-top:8px;">
                            <span>Send Message</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Office Hours -->
<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">When to visit</span>
            <h2 class="sn-title">Office <em>Hours</em></h2>
            <p class="sn-sub">We are available when you need us.</p>
        </div>

        <div class="hours-grid">
            <div class="sn-card text-center fade-in">
                <div class="sn-icon" style="margin-left:auto;margin-right:auto;width:56px;height:56px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h5 style="margin-bottom:4px;">Monday - Friday</h5>
                <p style="color:var(--text-strong);font-weight:700;font-size:1.1rem;margin-bottom:0;">8:00 AM - 5:00 PM</p>
            </div>

            <div class="sn-card text-center fade-in">
                <div class="sn-icon" style="margin-left:auto;margin-right:auto;width:56px;height:56px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h5 style="margin-bottom:4px;">Saturday</h5>
                <p style="color:var(--text-strong);font-weight:700;font-size:1.1rem;margin-bottom:0;">8:00 AM - 12:00 PM</p>
            </div>

            <div class="sn-card topline text-center fade-in">
                <div class="sn-icon" style="margin-left:auto;margin-right:auto;width:56px;height:56px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
                <h5 style="margin-bottom:4px;">Sunday &amp; Holidays</h5>
                <p style="color:#BFAE8E;font-weight:700;font-size:1rem;margin-bottom:0;">24/7 Emergency Services Only</p>
            </div>
        </div>
    </div>
</section>

<!-- Branch Locator -->
<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Find us</span>
            <h2 class="sn-title">Our <em>Branches</em></h2>
            <p class="sn-sub">Visit us at any of our conveniently located branches across Pampanga.</p>
        </div>

        <div class="branch-grid">
            <div class="sn-card fade-in" style="overflow:hidden;padding:0;">
                <div style="height:180px;background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);display:flex;align-items:center;justify-content:center;">
                    <div style="text-align:center;color:var(--white);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <div style="font-family:var(--font-serif);font-weight:700;font-size:1.1rem;margin-top:8px;">Main Branch</div>
                    </div>
                </div>
                <div style="padding:28px 24px;">
                    <h5 style="margin-bottom:12px;">Main Branch</h5>
                    <div class="d-flex align-center gap-2" style="margin-bottom:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="text-sm text-muted">Sto. Cristo, Guagua, Pampanga</span>
                    </div>
                    <div class="d-flex align-center gap-2" style="margin-bottom:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        <span class="text-sm text-muted">+63 917 123 4567</span>
                    </div>
                    <div class="d-flex align-center gap-2" style="margin-bottom:16px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="text-sm text-muted">Mon-Fri 8AM-5PM | Sat 8AM-12PM</span>
                    </div>
                    <a href="tel:+639171234567" class="btn btn-gold btn-block btn-sm">Call Now</a>
                </div>
            </div>

            <div class="sn-card fade-in" style="overflow:hidden;padding:0;">
                <div style="height:180px;background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);display:flex;align-items:center;justify-content:center;">
                    <div style="text-align:center;color:var(--white);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <div style="font-family:var(--font-serif);font-weight:700;font-size:1.1rem;margin-top:8px;">Angeles City Branch</div>
                    </div>
                </div>
                <div style="padding:28px 24px;">
                    <h5 style="margin-bottom:12px;">Angeles City Branch</h5>
                    <div class="d-flex align-center gap-2" style="margin-bottom:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="text-sm text-muted">Balibago, Angeles City</span>
                    </div>
                    <div class="d-flex align-center gap-2" style="margin-bottom:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        <span class="text-sm text-muted">+63 917 234 5678</span>
                    </div>
                    <div class="d-flex align-center gap-2" style="margin-bottom:16px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="text-sm text-muted">Mon-Fri 8AM-5PM | Sat 8AM-12PM</span>
                    </div>
                    <a href="tel:+639172345678" class="btn btn-gold btn-block btn-sm">Call Now</a>
                </div>
            </div>

            <div class="sn-card fade-in" style="overflow:hidden;padding:0;">
                <div style="height:180px;background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);display:flex;align-items:center;justify-content:center;">
                    <div style="text-align:center;color:var(--white);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <div style="font-family:var(--font-serif);font-weight:700;font-size:1.1rem;margin-top:8px;">Mabalacat Branch</div>
                    </div>
                </div>
                <div style="padding:28px 24px;">
                    <h5 style="margin-bottom:12px;">Mabalacat Branch</h5>
                    <div class="d-flex align-center gap-2" style="margin-bottom:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="text-sm text-muted">Dau, Mabalacat, Pampanga</span>
                    </div>
                    <div class="d-flex align-center gap-2" style="margin-bottom:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        <span class="text-sm text-muted">+63 917 345 6789</span>
                    </div>
                    <div class="d-flex align-center gap-2" style="margin-bottom:16px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="text-sm text-muted">Mon-Fri 8AM-5PM | Sat 8AM-12PM</span>
                    </div>
                    <a href="tel:+639173456789" class="btn btn-gold btn-block btn-sm">Call Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-band harbor fade-in">
            <div class="sn-center fade-in" style="margin-bottom:28px;">
                <span class="sn-eyebrow" style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.2);color:#CCB877;">Urgent help</span>
                <h2 class="sn-title" style="color:var(--ink-on-navy);">Need Immediate <em style="color:#CCB877;">Assistance?</em></h2>
                <p class="sn-sub" style="color:rgba(255,255,255,0.72);">Our team is available around the clock for emergencies. Don't hesitate to call us.</p>
            </div>
            <div style="text-align:center;">
                <a href="tel:+639171234567" class="btn btn-gold" style="font-size:1.1rem;padding:14px 40px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;vertical-align:middle;"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    Call +63 917 123 4567
                </a>
            </div>
        </div>
    </div>
</section>

<script>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>

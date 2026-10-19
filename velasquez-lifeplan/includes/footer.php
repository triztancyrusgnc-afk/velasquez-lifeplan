<?php $appBase = '/velasquez-lifeplan'; ?>
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-cta fade-in">
                <div>
                    <h3>Planning ahead, or need help right now?</h3>
                    <p>Talk to our family care team — free consultation, day or night.</p>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="<?= $appBase ?>/pages/plans.php" class="btn btn-gold">Explore Plans</a>
                    <a href="tel:+639171234567" class="btn btn-white">Call 0917-123-4567</a>
                </div>
            </div>
            <div class="footer-grid">
                <div class="footer-col footer-brand">
                    <img src="<?= $appBase ?>/img/logo.svg" alt="CSV" width="48" height="48" style="border-radius:10px;margin-bottom:12px;">
                    <p class="footer-tagline">Honoring lives, comforting families. Dignified funeral services and pre-need life plans for every Filipino family since 1990.</p>
                    <div class="footer-socials">
                        <a href="<?= $appBase ?>/pages/contact.php" aria-label="Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
                        <a href="<?= $appBase ?>/pages/contact.php" aria-label="Instagram"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg></a>
                        <a href="<?= $appBase ?>/pages/contact.php" aria-label="Twitter"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="<?= $appBase ?>/pages/plans.php">Life Plans</a></li>
                        <li><a href="<?= $appBase ?>/pages/chapels.php">Chapels & Facilities</a></li>
                        <li><a href="<?= $appBase ?>/pages/caskets.php">Caskets & Urns</a></li>
                        <li><a href="<?= $appBase ?>/pages/eservices.php">eServices</a></li>
                        <li><a href="<?= $appBase ?>/pages/grief.php">Grief Support</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="<?= $appBase ?>/pages/about.php">About Us</a></li>
                        <li><a href="<?= $appBase ?>/pages/about.php#officers">Our Team</a></li>
                        <li><a href="<?= $appBase ?>/pages/about.php#timeline">Our History</a></li>
                        <li><a href="<?= $appBase ?>/pages/contact.php">Contact Us</a></li>
                        <li><a href="<?= $appBase ?>/pages/compare.php">Compare Plans</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contact</h4>
                    <ul class="footer-contact">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Sto. Cristo, Guagua, Pampanga, Philippines
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            <a href="tel:+639171234567" style="color:var(--gold);font-weight:600;">+63 917 123 4567</a>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            info@velasquezfuneral.com
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Mon - Sat: 8:00 AM - 5:00 PM
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> Velasquez Funeral Service. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <?php if (trim($siteSettings['ai_enabled'] ?? '1') === '1'): ?>
    <?php require __DIR__ . '/ai_widget.php'; ?>
<?php endif; ?>

    <script src="/velasquez-lifeplan/js/app.js?v=<?= filemtime(__DIR__ . '/../js/app.js') ?>"></script>
</body>
</html>

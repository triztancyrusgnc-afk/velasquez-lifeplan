<?php
$pageTitle = 'eServices';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.sn-cards{display:grid;grid-template-columns:repeat(2,1fr);gap:24px;}
.sn-cards.four{grid-template-columns:repeat(4,1fr);}
.sn-check{margin:0;padding:0;list-style:none;display:grid;grid-template-columns:1fr 1fr;gap:4px 16px;margin:14px 0 20px;}
.sn-check li{padding:4px 0;font-size:0.875rem;color:var(--text);display:flex;align-items:center;gap:8px;}
.sn-check li span{color:#3F6B4F;font-weight:700;}
.sn-split{display:grid;grid-template-columns:1.1fr .9fr;gap:36px;align-items:center;}
@media(max-width:900px){
  .sn-cards{grid-template-columns:1fr;}
  .sn-cards.four{grid-template-columns:repeat(2,1fr);}
  .sn-split{grid-template-columns:1fr;}
}
@media(max-width:600px){.sn-cards.four{grid-template-columns:1fr;}.sn-check{grid-template-columns:1fr;}}
</style>

<section class="ed-hero" style="background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);color:var(--ink-on-navy);">
    <div class="container">
        <span class="badge badge-gold mb-2">Digital Solutions</span>
        <h1 class="ed-title" style="color:var(--ink-on-navy);">Electronic <span style="color:#CCB877;">Services</span></h1>
        <p class="ed-lede" style="color:rgba(255,255,255,0.78);">Modernizing funeral care with technology-driven services designed for your convenience and peace of mind. Access everything from anywhere.</p>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Our eServices</span>
            <h2 class="sn-title">Care, available on <em>every screen</em></h2>
            <p class="sn-sub">Access our services anytime, anywhere through our digital platforms. Built with compassion and convenience in mind.</p>
        </div>

        <div class="sn-cards fade-in">
            <div class="sn-card topline fade-in">
                <div class="sn-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                </div>
                <div>
                    <h4 style="margin-bottom:6px;">eBurol</h4>
                    <p class="text-muted" style="font-size:0.9375rem;margin-bottom:12px;">Online funeral streaming that allows family members and friends from around the world to participate in real-time. Never miss a moment, no matter where you are.</p>
                    <ul class="sn-check">
                        <li>
                            <span>&#10003;</span> HD Live Streaming
                        </li>
                        <li>
                            <span>&#10003;</span> Private Viewing Link
                        </li>
                        <li>
                            <span>&#10003;</span> Recording for 30 Days
                        </li>
                        <li>
                            <span>&#10003;</span> Interactive Guest Book
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="contact.php?subject=General%20Inquiry&message=I%20would%20like%20a%20demo%20of%20eBurol%20online%20streaming." class="btn btn-outline btn-block">Learn More</a>
                </div>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div>
                    <h4 style="margin-bottom:6px;">Online Payment</h4>
                    <p class="text-muted" style="font-size:0.9375rem;margin-bottom:12px;">Convenient and secure payment processing for life plan installments and services. Pay from anywhere using your preferred payment method.</p>
                    <ul class="sn-check">
                        <li>
                            <span>&#10003;</span> GCash &amp; Maya
                        </li>
                        <li>
                            <span>&#10003;</span> Bank Transfer
                        </li>
                        <li>
                            <span>&#10003;</span> Credit/Debit Card
                        </li>
                        <li>
                            <span>&#10003;</span> Instant Receipt Generation
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="../login.php" class="btn btn-outline btn-block">Learn More</a>
                </div>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div>
                    <h4 style="margin-bottom:6px;">Digital Policy Contract</h4>
                    <p class="text-muted" style="font-size:0.9375rem;margin-bottom:12px;">Access your life plan policy anytime with our digital document management system. E-sign contracts and keep everything secure in the cloud.</p>
                    <ul class="sn-check">
                        <li>
                            <span>&#10003;</span> E-Signed Documents
                        </li>
                        <li>
                            <span>&#10003;</span> Digital Policy Copy
                        </li>
                        <li>
                            <span>&#10003;</span> Payment History Tracking
                        </li>
                        <li>
                            <span>&#10003;</span> Secure Cloud Storage
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="../login.php" class="btn btn-outline btn-block">Learn More</a>
                </div>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="14" height="11" rx="2" ry="2"/><line x1="6" y1="18" x2="10" y2="18"/><line x1="4" y1="21" x2="12" y2="21"/><rect x="16" y="8" width="6" height="12" rx="2" ry="2"/><line x1="19" y1="18" x2="19.01" y2="18"/></svg>
                </div>
                <div>
                    <h4 style="margin-bottom:6px;">Any-Device Access</h4>
                    <p class="text-muted" style="font-size:0.9375rem;margin-bottom:12px;">Access every service on any screen — phone, tablet, laptop, or desktop. This website adapts automatically, so there is nothing to download or install.</p>
                    <ul class="sn-check">
                        <li>
                            <span>&#10003;</span> Works on Phones &amp; Tablets
                        </li>
                        <li>
                            <span>&#10003;</span> No Download Needed
                        </li>
                        <li>
                            <span>&#10003;</span> Same Features Everywhere
                        </li>
                        <li>
                            <span>&#10003;</span> Always Up to Date
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="#download" class="btn btn-outline btn-block">Learn More</a>
                </div>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                </div>
                <div>
                    <h4 style="margin-bottom:6px;">Virtual Tour</h4>
                    <p class="text-muted" style="font-size:0.9375rem;margin-bottom:12px;">Explore our chapels and facilities from the comfort of your home through immersive 360-degree virtual tours. See every detail before you visit.</p>
                    <ul class="sn-check">
                        <li>
                            <span>&#10003;</span> 360-Degree Walkthrough
                        </li>
                        <li>
                            <span>&#10003;</span> All 6 Chapels Included
                        </li>
                        <li>
                            <span>&#10003;</span> Mobile-Friendly Experience
                        </li>
                        <li>
                            <span>&#10003;</span> No Download Required
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="chapels.php" class="btn btn-outline btn-block">Start Tour</a>
                </div>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c4-4 8-7.58 8-12a8 8 0 10-16 0c0 4.418 4 8 8 12z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div>
                    <h4 style="margin-bottom:6px;">Memorial Wall</h4>
                    <p class="text-muted" style="font-size:0.9375rem;margin-bottom:12px;">Create a lasting digital tribute for your loved one with photos, videos, and messages from family and friends. A beautiful space to remember.</p>
                    <ul class="sn-check">
                        <li>
                            <span>&#10003;</span> Photo &amp; Video Gallery
                        </li>
                        <li>
                            <span>&#10003;</span> Guestbook Messages
                        </li>
                        <li>
                            <span>&#10003;</span> Shareable Memorial Link
                        </li>
                        <li>
                            <span>&#10003;</span> Perpetual Online Tribute
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="contact.php?subject=General%20Inquiry&message=I%20would%20like%20to%20create%20an%20online%20memorial." class="btn btn-outline btn-block">Create Memorial</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="download" class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-band linen fade-in">
            <div class="sn-center fade-in" style="margin-bottom:36px;">
                <span class="sn-eyebrow">Any device, no install</span>
                <h2 class="sn-title">One Website, <em>Every Device</em></h2>
                <p class="sn-sub">No app to install and no storage used. Open this website on any phone, tablet, laptop, or desktop to manage your life plan, make payments, and use every eService.</p>
            </div>
            <div class="sn-split">
                <div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="../login.php" class="btn btn-gold" style="gap:10px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Open Client Portal
                        </a>
                        <a href="plans.php" class="btn btn-navy" style="gap:10px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="14" height="11" rx="2" ry="2"/><line x1="6" y1="18" x2="10" y2="18"/><line x1="4" y1="21" x2="12" y2="21"/><rect x="16" y="8" width="6" height="12" rx="2" ry="2"/><line x1="19" y1="18" x2="19.01" y2="18"/></svg>
                            Browse Life Plans
                        </a>
                    </div>
                </div>
                <div style="display:flex;justify-content:center;">
                    <img src="/velasquez-lifeplan/img/esvc-phone.jpg" alt="Velasquez website on a phone" style="width:220px;height:280px;object-fit:cover;border-radius:16px;border:3px solid #CCB877;">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Why digital</span>
            <h2 class="sn-title">Why Go <em>Digital?</em></h2>
            <p class="sn-sub">Embracing technology helps us serve you better, faster, and with greater transparency.</p>
        </div>

        <div class="sn-cards four">
            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h5>Save Time</h5>
                <p class="text-muted text-sm mb-0">Complete transactions, sign documents, and manage your plan from home without multiple office visits.</p>
            </div>

            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h5>Enhanced Security</h5>
                <p class="text-muted text-sm mb-0">Bank-level encryption and secure cloud storage protect your sensitive documents and personal information.</p>
            </div>

            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                </div>
                <h5>Accessible Anywhere</h5>
                <p class="text-muted text-sm mb-0">Access your policy, make payments, or watch services from any device with an internet connection.</p>
            </div>

            <div class="sn-card fade-in" style="text-align:left;">
                <div class="sn-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c4-4 8-7.58 8-12a8 8 0 10-16 0c0 4.418 4 8 8 12z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <h5>Eco-Friendly</h5>
                <p class="text-muted text-sm mb-0">Reduce paper waste with digital documents and electronic signatures while maintaining full legal compliance.</p>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Good to know</span>
            <h2 class="sn-title">Frequently Asked <em>Questions</em></h2>
            <p class="sn-sub">Answers to common questions about our electronic services and digital tools.</p>
        </div>

        <div style="max-width:800px;margin:0 auto;display:grid;gap:12px;">
            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">How does the eBurol streaming service work?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">After booking, we send a private link to all designated viewers. The service streams in HD quality with multiple camera angles. Family members and friends from around the world can watch in real-time, leave messages in the guest book, and the recording is available for 30 days after the service.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Is online payment secure?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, all transactions are encrypted with bank-level security. We partner with established payment providers like GCash, Maya, and major banks to ensure your financial information is fully protected. You will receive an instant digital receipt for every payment.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can I access my policy documents on my phone?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Absolutely. Our website gives you 24/7 access to your policy documents, payment history, and account details. All documents are securely stored in the cloud and can be accessed from any device with an internet connection.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">What devices are supported for virtual tours?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Our virtual tours work on any modern web browser — no app download required. They are fully responsive and work on desktops, laptops, tablets, and smartphones. For the best experience, we recommend using a stable internet connection.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">How do I create a Memorial Wall for my loved one?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Contact our team here on the website to get started. You can upload photos and videos, write tribute messages, and share a unique link with family and friends. Your loved one's memorial page stays online permanently as a lasting digital tribute.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Is there a fee for using eServices?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Many of our eServices are included with your life plan. eBurol streaming and memorial wall creation may have additional fees depending on the package you choose. Contact our team for a complete breakdown of available services and pricing.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                    <h5 style="margin-bottom:8px;">Do I need to install anything to use eServices?</h5>
                    <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">No. Everything runs right in your web browser — just open this website on any phone, tablet, or computer and sign in. There is nothing to download, install, or update, and you always get the latest version automatically.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can I share the eBurol stream with extended family?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, after the initial private link is sent to your designated viewers, you can request additional links for extended family members. Each link is unique and secure, ensuring only authorized viewers can access the live stream of your loved one's service.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">What happens to my digital documents if I cancel my policy?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">All your documents remain accessible for a specified period after cancellation, giving you time to download and save copies. We will notify you of the exact timeline and assist with the transition. Your data is handled with the same confidentiality standards throughout.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can I set up automatic payment reminders?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, your online account lets you set up customizable payment reminders via SMS or email. You can choose when and how often you receive reminders to help you stay on top of your installment schedule.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">How many people can view an eBurol stream simultaneously?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Our streaming infrastructure supports hundreds of simultaneous viewers, so even large extended families can watch together. Each private link can accommodate unlimited concurrent viewers within your authorized list, ensuring no one is left out.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Is technical support available if I have issues?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, our technical support team is available during business hours and can be reached via phone, email, or live chat. We also provide step-by-step guides and video tutorials to help you get the most out of every eService.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can I use eBurol for memorial anniversaries?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Absolutely. Many families use our streaming service for death anniversary masses and memorial gatherings. The recorded service can also be shared as a keepsake video during these occasions to honor and remember your loved one.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">How do I update my contact information in the system?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Sign in to your account on this website and open your profile settings. You can update your phone number, email address, and mailing address. Changes take effect immediately across all our systems.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can multiple family members access the same account?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, you can designate authorized family members who can access the policy account. Each person receives their own login credentials while viewing the same shared policy information, making it easy for families to manage arrangements together.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                    <h5 style="margin-bottom:8px;">Is the website available in Filipino?</h5>
                    <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Currently, the website is in English, with Filipino support planned in a future update. Our customer service team is fully bilingual and can assist you in either language via phone, chat, or email.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can I schedule payments in advance through the app?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, your online account lets you schedule future payments up to 12 months in advance. You can set specific dates and amounts, and the system will process them automatically on schedule, giving you complete control over your payment plan.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">How do I report an issue with my eService?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">You can report issues through the contact form on this website, via email at support@velasquezfuneral.com, or by calling our hotline. Our technical team typically responds within 2 hours during business days and works to resolve issues promptly.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can I download my policy documents as PDF?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, all policy documents can be downloaded as PDF files from your online account on this website. This allows you to keep offline copies, print them when needed, or share them with family members who may not have digital access.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">What internet speed is recommended for eBurol streaming?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">For viewers, a stable connection of at least 5 Mbps is recommended for HD quality streaming. Our team handles the production end with professional equipment to ensure the best possible broadcast quality regardless of your connection speed.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">Can I use the Memorial Wall to raise funds for charity?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">Yes, our Memorial Wall platform supports donation links for families who wish to honor their loved one by raising funds for a chosen charity or cause. Donors can contribute directly through the memorial page, and all transactions are securely processed.</p>
            </div>

            <div class="sn-card fade-in" style="padding:24px;margin-bottom:0;">
                <h5 style="margin-bottom:8px;">How do I contact support if I need help outside business hours?</h5>
                <p style="margin:0;color:var(--gray-600);font-size:0.9375rem;">For urgent issues, our main hotline is available 24/7. For non-urgent technical support, you can submit a ticket through the app or website at any time, and our team will respond during the next business day. Our comprehensive FAQ section also addresses most common questions.</p>
            </div>
        </div>

        <div class="d-flex justify-center mt-4">
            <a href="tel:+639171234567" class="btn btn-gold btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                Call Us for More Information
            </a>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-band harbor fade-in">
            <div class="sn-split">
                <div>
                    <span class="sn-eyebrow" style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.2);color:#CCB877;">Get started</span>
                    <h2 class="sn-title" style="color:var(--ink-on-navy);margin-bottom:12px;">Ready to Get <em style="color:#CCB877;">Started?</em></h2>
                    <p class="sn-sub" style="color:rgba(255,255,255,0.72);">Experience the convenience of our electronic services. Our team is ready to guide you through every digital tool we offer.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap" style="justify-content:flex-end;">
                    <a href="tel:+639171234567" class="btn btn-gold btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                        +63 917 123 4567
                    </a>
                    <a href="contact.php?subject=General%20Inquiry&message=I%20would%20like%20to%20request%20an%20eServices%20demo." class="btn btn-white btn-lg">Request a Demo</a>
                </div>
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

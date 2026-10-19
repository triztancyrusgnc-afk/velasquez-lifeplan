<?php
$pageTitle = 'Our Caskets';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.ed-cols{display:grid;grid-template-columns:1fr 250px;gap:24px;align-items:start;}
.ed-spec{margin:16px 0 0;padding:0;list-style:none;}
.ed-spec li{padding:6px 0;border-bottom:1px solid var(--border);font-size:0.875rem;color:var(--text-strong);}
.ed-spec li:last-child{border-bottom:none;}
.ed-meta{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:10px;}
.ed-price{font-family:var(--font-serif);font-size:1.6rem;font-weight:700;color:var(--text-strong);margin-bottom:16px;}
.ed-side{background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:20px;text-align:left;}
@media(max-width:768px){.ed-cols{grid-template-columns:1fr;}}
</style>

<section class="ed-hero" style="background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);">
    <div class="container">
        <span class="badge badge-gold mb-2">Quality Selection</span>
        <h1 class="ed-title" style="color:var(--ink-on-navy);">Our <span style="color:#CCB877;">Caskets</span></h1>
        <p class="ed-lede" style="color:rgba(255,255,255,0.78);">Thoughtfully crafted caskets that honor your loved one with dignity, elegance, and enduring quality. Browse our complete collection below.</p>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Timeless Craftsmanship</span>
            <h2 class="sn-title">Wood <em>Caskets</em></h2>
            <p class="sn-sub">Timeless craftsmanship using premium hardwoods, hand-finished to the highest standards of artistry and reverence.</p>
        </div>

        <div class="grid-3">
            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="10" rx="2"/><path d="M3 12h18"/><path d="M7 8V6h10v2"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Heritage Pine</h4>
                    <span class="badge badge-green">Classic</span>
                </div>
                <p class="card-text">Solid pine wood with a warm, natural finish. A dignified choice that reflects timeless elegance and enduring respect for your loved one.</p>
                <ul class="ed-spec">
                    <li>Material: Solid Pine Wood</li>
                    <li>Finish: Natural Satin</li>
                    <li>Interior: Crepe Fabric Lining</li>
                    <li>Viewing Days: 3-Day Standard</li>
                    <li>Hardware: Swing Bar Handles</li>
                </ul>
                <div class="ed-price">&#8369;35,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Heritage%20Pine%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="10" rx="2"/><path d="M3 12h18"/><path d="M7 8V6h10v2"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Oak Memorial</h4>
                    <span class="badge badge-gold">Popular</span>
                </div>
                <p class="card-text">Premium oak construction with detailed craftsmanship. A symbol of strength and endurance, chosen by families who value tradition.</p>
                <ul class="ed-spec">
                    <li>Material: Solid Oak Wood</li>
                    <li>Finish: Hand-Rubbed Satin</li>
                    <li>Interior: Velvet Fabric Lining</li>
                    <li>Viewing Days: 3-Day Standard</li>
                    <li>Hardware: Wire Loop Handles</li>
                </ul>
                <div class="ed-price">&#8369;42,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Oak%20Memorial%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="10" rx="2"/><path d="M3 12h18"/><path d="M7 8V6h10v2"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Walnut Prestige</h4>
                    <span class="badge badge-navy">Premium</span>
                </div>
                <p class="card-text">Rich walnut wood with exquisite detailing. The finest choice for an unforgettable farewell that reflects a life well-lived.</p>
                <ul class="ed-spec">
                    <li>Material: Solid Walnut Wood</li>
                    <li>Finish: High-Gloss Polish</li>
                    <li>Interior: Premium Satin Lining</li>
                    <li>Viewing Days: 5-Day Extended</li>
                    <li>Hardware: Brushed Brass Swing Bar</li>
                </ul>
                <div class="ed-price">&#8369;55,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Walnut%20Prestige%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Lasting Protection</span>
            <h2 class="sn-title">Metal <em>Caskets</em></h2>
            <p class="sn-sub">Steel caskets built with precision for lasting protection and refined appearance, designed to stand the test of time.</p>
        </div>

        <div class="grid-3">
            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h16v9H4z"/><path d="M4 13h16"/><path d="M8 9V7h8v2"/><circle cx="12" cy="15.5" r="1.2"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Steel Classic</h4>
                    <span class="badge badge-green">Value</span>
                </div>
                <p class="card-text">20-gauge steel construction with a clean, polished finish. Durable and affordable without compromising on dignity.</p>
                <ul class="ed-spec">
                    <li>Material: 20-Gauge Steel</li>
                    <li>Finish: Powder Coated</li>
                    <li>Interior: Crepe Fabric Lining</li>
                    <li>Viewing Days: 3-Day Standard</li>
                    <li>Hardware: Twin Swing Bar Handles</li>
                </ul>
                <div class="ed-price">&#8369;28,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Steel%20Classic%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h16v9H4z"/><path d="M4 13h16"/><path d="M8 9V7h8v2"/><circle cx="12" cy="15.5" r="1.2"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Steel Deluxe</h4>
                    <span class="badge badge-gold">Popular</span>
                </div>
                <p class="card-text">18-gauge steel with a bronze-tone finish and swing bar handles for an elegant presentation that families appreciate.</p>
                <ul class="ed-spec">
                    <li>Material: 18-Gauge Steel</li>
                    <li>Finish: Bronze-Tone Enamel</li>
                    <li>Interior: Velvet Fabric Lining</li>
                    <li>Viewing Days: 3-Day Standard</li>
                    <li>Hardware: Crescent Bar Handles</li>
                </ul>
                <div class="ed-price">&#8369;42,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Steel%20Deluxe%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h16v9H4z"/><path d="M4 13h16"/><path d="M8 9V7h8v2"/><circle cx="12" cy="15.5" r="1.2"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Steel Prestige</h4>
                    <span class="badge badge-navy">Premium</span>
                </div>
                <p class="card-text">16-gauge premium steel with double-reinforced corners and luxury interior appointment for the highest level of protection.</p>
                <ul class="ed-spec">
                    <li>Material: 16-Gauge Steel</li>
                    <li>Finish: High-Gloss Enamel</li>
                    <li>Interior: Premium Satin Lining</li>
                    <li>Viewing Days: 5-Day Extended</li>
                    <li>Hardware: Fluted Bar Handles</li>
                </ul>
                <div class="ed-price">&#8369;58,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Steel%20Prestige%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Distinguished Memorials</span>
            <h2 class="sn-title">Imported <em>Caskets</em></h2>
            <p class="sn-sub">Exquisite imported craftsmanship with premium materials sourced from around the world for the most distinguished memorials.</p>
        </div>

        <div class="grid-3">
            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l9-4 9 4v8l-9 4-9-4z"/><path d="M3 8l9 4 9-4"/><path d="M12 12v8"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Imperial Hardwood</h4>
                    <span class="badge badge-gold">Luxury</span>
                </div>
                <p class="card-text">Imported Italian hardwood with hand-carved accents. A masterpiece of artistry and reverence for those who desire the finest.</p>
                <ul class="ed-spec">
                    <li>Material: Imported Italian Hardwood</li>
                    <li>Finish: Hand-Carved Details</li>
                    <li>Interior: Italian Velvet Lining</li>
                    <li>Viewing Days: 5-Day Extended</li>
                    <li>Hardware: Polished Brass Fixed Bar</li>
                </ul>
                <div class="ed-price">&#8369;85,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Imperial%20Hardwood%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l9-4 9 4v8l-9 4-9-4z"/><path d="M3 8l9 4 9-4"/><path d="M12 12v8"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Cherry Prestige</h4>
                    <span class="badge badge-navy">Elite</span>
                </div>
                <p class="card-text">American cherry wood with dovetail joinery and mirror-finish polish. Exquisite in every detail for an extraordinary farewell.</p>
                <ul class="ed-spec">
                    <li>Material: Imported Cherry Wood</li>
                    <li>Finish: Mirror Polish</li>
                    <li>Interior: Plush Velvet Lining</li>
                    <li>Viewing Days: 7-Day Premium</li>
                    <li>Hardware: Solid Brass Swing Bar</li>
                </ul>
                <div class="ed-price">&#8369;95,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Cherry%20Prestige%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>

            <div class="sn-card topline fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l9-4 9 4v8l-9 4-9-4z"/><path d="M3 8l9 4 9-4"/><path d="M12 12v8"/></svg>
                </div>
                <div class="ed-meta">
                    <h4 class="card-title" style="margin-bottom:0;">Royal Mahogany</h4>
                    <span class="badge badge-gold">Exclusive</span>
                </div>
                <p class="card-text">Genuine Philippine mahogany with brass hardware. The ultimate expression of honor, respect, and enduring legacy.</p>
                <ul class="ed-spec">
                    <li>Material: Philippine Mahogany</li>
                    <li>Finish: French Polish</li>
                    <li>Interior: Custom Silk Lining</li>
                    <li>Viewing Days: 7-Day Premium</li>
                    <li>Hardware: Engraved Brass Bar</li>
                </ul>
                <div class="ed-price">&#8369;120,000</div>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20inquire%20about%20the%20Royal%20Mahogany%20casket." class="btn btn-gold btn-block">Inquire</a>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);padding-top:0;">
    <div class="container">
        <div class="sn-band linen fade-in">
            <div class="sn-center fade-in">
                <span class="sn-eyebrow">Excellence &amp; Care</span>
                <h2 class="sn-title">Our Quality <em>Promise</em></h2>
                <p class="sn-sub">Every casket we provide is backed by our commitment to excellence and compassionate service.</p>
            </div>
            <div class="grid-3">
                <div class="sn-card fade-in">
                    <div class="sn-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <h4 style="margin-bottom:8px;">Quality Assured</h4>
                    <p class="text-muted text-sm mb-0">Every casket undergoes rigorous quality inspection before delivery, ensuring it meets our exacting standards.</p>
                </div>
                <div class="sn-card fade-in">
                    <div class="sn-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18v10H3z"/><path d="M7 7V5h10v2"/><path d="M7 11h10"/></svg>
                    </div>
                    <h4 style="margin-bottom:8px;">Standard Sizes</h4>
                    <p class="text-muted text-sm mb-0">All caskets meet standard burial dimensions and cemetery requirements for a seamless arrangement process.</p>
                </div>
                <div class="sn-card fade-in">
                    <div class="sn-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-4.5-7-11a4 4 0 017-2.5A4 4 0 0119 10c0 6.5-7 11-7 11z"/><circle cx="12" cy="10" r="1.5"/></svg>
                    </div>
                    <h4 style="margin-bottom:8px;">Dignified Handling</h4>
                    <p class="text-muted text-sm mb-0">White-glove delivery and professional handling included with every purchase for complete peace of mind.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Good To Know</span>
            <h2 class="sn-title">Frequently Asked <em>Questions</em></h2>
            <p class="sn-sub">Answers to common questions about selecting the right casket for your loved one.</p>
        </div>

        <div style="max-width:800px;margin:0 auto;display:grid;gap:16px;">
            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What is the difference between wood and metal caskets?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Wood caskets offer a warm, natural aesthetic with unique grain patterns, while metal caskets provide superior structural integrity and are sealed to protect against outside elements. Both options are dignified choices — the best choice depends on your family's preferences, the burial environment, and your budget.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">How do I choose the right casket size?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Our standard size of 84" x 28" accommodates most adults comfortably. For larger individuals, we offer extended sizes up to 88" x 32". Our staff will help you select the appropriate size during your consultation to ensure a proper and respectful fit.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I view the casket before purchasing?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Absolutely. We encourage families to visit our showroom to see, touch, and compare our caskets in person. Our experienced staff will guide you through each option, explain the differences in materials and construction, and help you make an informed decision without any pressure.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you offer casket delivery and setup?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, white-glove delivery and professional setup are included with every casket purchase. Our team will deliver the casket to the chapel or place of service, set it up with care, and ensure everything is perfect for the viewing. We handle all logistics so your family can focus on what matters most.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What payment options are available?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">We accept cash, credit/debit cards, GCash, Maya, and bank transfers. For life plan policyholders, the casket cost may already be covered under your plan. We also offer flexible installment arrangements for families who need them. Contact us to discuss what works best for your situation.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">How far in advance can I purchase a casket?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Many families choose to pre-purchase caskets as part of their life plan to lock in current prices and relieve their loved ones of the burden of making this decision during a difficult time. We can store your selected casket at our facility at no additional cost until it is needed.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What makes imported caskets different?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Imported caskets are crafted from exotic hardwoods not locally available, such as Italian hardwood and Philippine mahogany. They feature superior joinery techniques, hand-carved details, and premium hardware that set them apart in both appearance and durability.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I personalize or engrave the casket?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, we offer personalization options including name plates, engraved emblems, and custom interior fabric selections. Our team can discuss all available options during your consultation to help you create a truly meaningful tribute.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What is the difference between 16-gauge and 20-gauge steel?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Gauge refers to the thickness of the steel — a lower gauge number means thicker, more durable metal. 16-gauge steel is our thickest option, providing superior protection and longevity. 20-gauge is thinner but still offers reliable quality at a more accessible price point.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you offer casket packages with other services?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, we offer bundled packages that include casket selection, chapel rental, embalming, and other funeral services at a combined price. These packages provide better value and simplify the arrangement process. Ask our staff about available bundle options.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">How should I care for a casket before use?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">If you pre-purchase a casket, we recommend storing it in a cool, dry place away from direct sunlight. However, most families choose to let us store it at our facility where it is kept in climate-controlled conditions until needed, at no extra charge.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Are your caskets environmentally friendly?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Our wood caskets are sourced from sustainably managed forests. We also offer biodegradable lining options for families who prefer an eco-conscious choice. Ask our staff about our green burial options for more information.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What is your return or exchange policy?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">If you have pre-purchased a casket and wish to exchange it for a different model, we are happy to accommodate within 30 days of purchase. Unused caskets stored at our facility can be exchanged for another model from our collection.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you offer international shipping for overseas families?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">For families arranging services from abroad, we coordinate with international logistics partners to ensure timely delivery. Our team handles all documentation, customs requirements, and coordination to simplify the process for overseas family members.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I see the casket before it is delivered to the chapel?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, you are always welcome to visit our showroom to inspect the casket before delivery. We can also send photos and videos of the specific casket for your approval if an in-person visit is not possible.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you offer casket insurance coordination?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, our staff can assist with insurance claim paperwork and coordinate directly with insurance providers to streamline the reimbursement process. We will guide you through every step to ensure you receive the benefits you are entitled to.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What casket size is best for cremation?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">For cremation, we offer specially designed cremation caskets that are more lightweight and do not contain metal components. These are available at lower price points while still maintaining dignity and quality. Ask our staff about our cremation-specific options.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">How do I choose between a viewing and a closed casket service?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">This is a personal decision based on family preference and cultural traditions. If you choose a viewing, we recommend caskets with higher-quality finishes and interiors. For closed casket services, you may prioritize construction quality and structural integrity. Our staff can guide you based on your specific needs.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you offer casket accessories such as interior pillows and tufted panels?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, we offer a complete selection of casket accessories including matching pillows, tufted lid interiors, memory drawers, and keepsake trays. These details add a personal touch and enhance the overall presentation during the viewing.</p>
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
        <div class="sn-band harbor text-center fade-in">
            <span class="badge badge-gold mb-2">Guidance</span>
            <h2 style="margin-bottom:16px;color:var(--ink-on-navy);">Need Help Choosing?</h2>
            <p style="font-size:1.125rem;max-width:560px;margin:0 auto 32px;color:rgba(255,255,255,0.7);">Our experienced staff is here to guide you through every option and help you find the perfect casket to honor your loved one with the dignity they deserve.</p>
            <div class="d-flex justify-center gap-2 flex-wrap">
                <a href="tel:+639171234567" class="btn btn-gold btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    +63 917 123 4567
                </a>
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20request%20a%20casket%20consultation." class="btn btn-white btn-lg">Request a Consultation</a>
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

<?php
$pageTitle = 'Chapels & Facilities';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/under_construction.php';
guardSection($siteSettings, 'chapels');
?>

<style>
.ed-cols{display:grid;grid-template-columns:1fr 250px;gap:24px;align-items:start;}
.ed-tags{display:flex;flex-wrap:wrap;gap:8px;list-style:none;margin:0 0 18px;padding:0;}
.ed-tags li{border:1px solid var(--border);background:var(--bg);border-radius:100px;padding:5px 13px;font-size:0.78rem;color:var(--text-strong);}
.ed-num{font-family:var(--font-serif);font-size:2rem;font-weight:800;color:var(--text-muted);line-height:1;min-width:54px;}
.ed-meta{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:10px;}
@media(max-width:768px){.ed-cols{grid-template-columns:1fr;}}
</style>

<section class="ed-hero" style="background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);">
    <div class="container">
        <span class="badge badge-gold mb-2">Our Facilities</span>
        <h1 class="ed-title" style="color:var(--ink-on-navy);">Our <span style="color:#CCB877;">Chapels</span></h1>
        <p class="ed-lede" style="color:rgba(255,255,255,0.78);">5 Floors &middot; 6 Chapels &mdash; A serene and dignified space to honor and celebrate the lives of your loved ones.</p>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Premier Spaces</span>
            <h2 class="sn-title">Featured <em>Chapels</em></h2>
            <p class="sn-sub">Our premier chapels designed for grand and memorable services that leave a lasting impression.</p>
        </div>

        <div style="display:grid;gap:24px;">
            <div class="sn-card topline fade-in">
                <div class="ed-cols">
                    <div>
                        <div class="sn-icon" aria-hidden="true">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-4h6v4"/><path d="M12 7v3"/></svg>
                        </div>
                        <div class="ed-meta">
                            <h3 style="margin-bottom:0;">St. Anne Chapel</h3>
                            <span class="badge badge-gold">Featured</span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                            <span class="sn-chip">3rd Floor</span>
                            <span class="sn-chip">200+ Guests</span>
                            <span class="sn-chip">Air-Conditioned</span>
                        </div>
                        <p style="color:var(--text-strong);font-size:0.9375rem;opacity:0.82;margin-bottom:16px;">Our most sought-after chapel with soaring ceilings, elegant chandeliers, and a capacity for 200+ guests. Ideal for large memorial services, it features a grand stage with professional sound system, LED displays, and a private family viewing room for intimate moments before the service.</p>
                        <ul class="ed-tags">
                            <li>Grand Stage with Sound System</li>
                            <li>LED Display &amp; Projector</li>
                            <li>Private Family Viewing Room</li>
                            <li>Full Air-Conditioning</li>
                        </ul>
                    </div>
                    <div>
                        <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20schedule%20a%20viewing%20at%20St.%20Anne%20Chapel." class="btn btn-gold">Schedule a Viewing</a>
                    </div>
                </div>
            </div>

            <div class="sn-card topline fade-in">
                <div class="ed-cols">
                    <div>
                        <div class="sn-icon" aria-hidden="true">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/><path d="M12 12v3"/><path d="M10 13.5h4"/></svg>
                        </div>
                        <div class="ed-meta">
                            <h3 style="margin-bottom:0;">Executive Vault</h3>
                            <span class="badge badge-navy">Exclusive</span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                            <span class="sn-chip">4th Floor</span>
                            <span class="sn-chip">80+ Guests</span>
                            <span class="sn-chip">VIP Suite</span>
                        </div>
                        <p style="color:var(--text-strong);font-size:0.9375rem;opacity:0.82;margin-bottom:16px;">An intimate, exclusive space with premium furnishing and private viewing rooms. Designed for VIP and executive-level memorial services, this chapel offers a private kitchen for catering preparation, dedicated concierge service, and exclusive parking for the family's convenience.</p>
                        <ul class="ed-tags">
                            <li>Private Family Room &amp; VIP Suite</li>
                            <li>Premium Audio-Visual Setup</li>
                            <li>Private Kitchen for Catering</li>
                            <li>Exclusive Reserved Parking</li>
                        </ul>
                    </div>
                    <div>
                        <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20schedule%20a%20viewing%20at%20Executive%20Vault." class="btn btn-gold">Schedule a Viewing</a>
                    </div>
                </div>
            </div>

            <div class="sn-card topline fade-in">
                <div class="ed-cols">
                    <div>
                        <div class="sn-icon" aria-hidden="true">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M6 21V10l6-6 6 6v11"/><path d="M10 21v-5h4v5"/><circle cx="12" cy="9" r="1.5"/></svg>
                        </div>
                        <div class="ed-meta">
                            <h3 style="margin-bottom:0;">Gold Memorial Hall</h3>
                            <span class="badge badge-gold">Grand</span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                            <span class="sn-chip">5th Floor</span>
                            <span class="sn-chip">300+ Guests</span>
                            <span class="sn-chip">Grand Ballroom</span>
                        </div>
                        <p style="color:var(--text-strong);font-size:0.9375rem;opacity:0.82;margin-bottom:16px;">Our grandest hall with a panoramic view, accommodating 300+ guests. Perfect for large-scale memorial events and celebrations of life. Features a full concert-grade sound system, a raised stage for performances and eulogies, and an on-site catering-ready kitchen for receptions.</p>
                        <ul class="ed-tags">
                            <li>Panoramic City View</li>
                            <li>Full Concert Sound System</li>
                            <li>Raised Performance Stage</li>
                            <li>Catering-Ready Kitchen</li>
                        </ul>
                    </div>
                    <div>
                        <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20schedule%20a%20viewing%20at%20Gold%20Memorial%20Hall." class="btn btn-gold">Schedule a Viewing</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);padding-top:0;">
    <div class="container">
        <div class="sn-band linen fade-in">
            <div class="sn-center fade-in">
                <span class="sn-eyebrow">Intimate Gatherings</span>
                <h2 class="sn-title">Other <em>Chapels</em></h2>
                <p class="sn-sub">Additional spaces available for intimate gatherings and meaningful services.</p>
            </div>

            <div class="grid-3">
                <div class="sn-card fade-in">
                    <div class="sn-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V8l7-5 7 5v13"/><path d="M9 21v-3h6v3"/></svg>
                    </div>
                    <h4 style="margin-bottom:8px;">St. Joseph Chapel</h4>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                        <span class="sn-chip">1st Floor</span>
                        <span class="sn-chip">120+ Guests</span>
                    </div>
                    <p class="card-text">A warm and welcoming chapel on the ground floor, ideal for medium-sized services with easy accessibility for elderly guests.</p>
                    <details class="expandable" style="margin-top:8px;">
                        <summary class="btn btn-outline btn-sm w-full">View Details</summary>
                        <div class="expandable-body"><p>St. Joseph Chapel — 1st Floor, 120+ Guests. A warm and welcoming chapel on the ground floor, ideal for medium-sized services with easy accessibility for elderly guests.</p></div>
                    </details>
                </div>

                <div class="sn-card fade-in">
                    <div class="sn-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V8l7-5 7 5v13"/><path d="M9 21v-3h6v3"/></svg>
                    </div>
                    <h4 style="margin-bottom:8px;">St. Peter Chapel</h4>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                        <span class="sn-chip">2nd Floor</span>
                        <span class="sn-chip">150+ Guests</span>
                    </div>
                    <p class="card-text">A beautifully appointed chapel with elegant interior design and soft ambient lighting for a comforting atmosphere.</p>
                    <details class="expandable" style="margin-top:8px;">
                        <summary class="btn btn-outline btn-sm w-full">View Details</summary>
                        <div class="expandable-body"><p>St. Peter Chapel — 2nd Floor, 150+ Guests. A beautifully appointed chapel with elegant interior design and soft ambient lighting for a comforting atmosphere.</p></div>
                    </details>
                </div>

                <div class="sn-card fade-in">
                    <div class="sn-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V8l7-5 7 5v13"/><path d="M9 21v-3h6v3"/></svg>
                    </div>
                    <h4 style="margin-bottom:8px;">Heritage Chapel</h4>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                        <span class="sn-chip">2nd Floor</span>
                        <span class="sn-chip">100+ Guests</span>
                    </div>
                    <p class="card-text">A classic chapel with traditional Filipino design elements, perfect for families who want a culturally meaningful service.</p>
                    <details class="expandable" style="margin-top:8px;">
                        <summary class="btn btn-outline btn-sm w-full">View Details</summary>
                        <div class="expandable-body"><p>Heritage Chapel — 2nd Floor, 100+ Guests. A classic chapel with traditional Filipino design elements, perfect for families who want a culturally meaningful service.</p></div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Comfort &amp; Convenience</span>
            <h2 class="sn-title">Our <em>Facilities</em></h2>
            <p class="sn-sub">Designed for comfort and convenience during your time with us.</p>
        </div>

        <div class="grid-3">
            <div class="sn-card fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M4 6l8-4 8 4"/><path d="M4 6v12l8 4 8-4V6"/><path d="M9 11a3 3 0 006 0"/></svg>
                </div>
                <h5 style="margin-bottom:8px;">Air-Conditioned Chapels</h5>
                <p class="text-muted text-sm mb-0">All chapels are fully climate-controlled for year-round comfort regardless of the weather outside.</p>
            </div>
            <div class="sn-card fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                </div>
                <h5 style="margin-bottom:8px;">24/7 Service</h5>
                <p class="text-muted text-sm mb-0">Our facilities and staff are available around the clock, day or night, whenever you need us.</p>
            </div>
            <div class="sn-card fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="10" width="13" height="8" rx="2"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/><path d="M16 10V7h4l-1 3"/></svg>
                </div>
                <h5 style="margin-bottom:8px;">Valet Parking</h5>
                <p class="text-muted text-sm mb-0">Complimentary valet parking for all chapel guests ensures a hassle-free arrival experience.</p>
            </div>
            <div class="sn-card fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21c-4 0-7-3-7-7 0-5 4-8 7-11 3 3 7 6 7 11 0 4-3 7-7 7z"/><path d="M12 21v-8"/></svg>
                </div>
                <h5 style="margin-bottom:8px;">On-Site Flower Shop</h5>
                <p class="text-muted text-sm mb-0">Fresh flower arrangements and custom floral tributes available for ordering right on the premises.</p>
            </div>
            <div class="sn-card fade-in">
                <div class="sn-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 9h8"/><path d="M8 13h8"/><path d="M8 17h5"/></svg>
                </div>
                <h5 style="margin-bottom:8px;">Memorial Wall</h5>
                <p class="text-muted text-sm mb-0">A permanent tribute wall in our lobby to honor and remember the lives of those we have served.</p>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Preview From Home</span>
            <h2 class="sn-title">Virtual <em>Tour</em></h2>
            <p class="sn-sub">An immersive, interactive experience to help you choose the perfect space for your loved one's service.</p>
        </div>
        <div class="sn-band harbor fade-in">
            <div style="text-align:left;">
                <span class="badge badge-gold mb-2">360&deg; Preview</span>
                <h2 style="color:var(--ink-on-navy);margin-bottom:12px;">360&deg; Virtual Tour Coming Soon</h2>
                <p style="color:rgba(255,255,255,0.7);max-width:560px;margin:0 0 24px;font-size:1.0625rem;">Explore all six of our chapels from the comfort of your home. An immersive, interactive experience to help you choose the perfect space for your loved one's service.</p>
                <a href="contact.php?subject=General%20Inquiry&message=Please%20notify%20me%20when%20the%20virtual%20tour%20is%20available." class="btn btn-gold btn-lg">Get Notified When Available</a>
            </div>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--bg);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Good To Know</span>
            <h2 class="sn-title">Frequently Asked <em>Questions</em></h2>
            <p class="sn-sub">Helpful answers to common questions about our chapels and facilities.</p>
        </div>

        <div style="max-width:800px;margin:0 auto;display:grid;gap:16px;">
            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">How far in advance should I book a chapel?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">We recommend booking as soon as possible, especially for our featured chapels like St. Anne and Gold Memorial Hall. However, we understand that arrangements are often made on short notice. Contact us immediately and we will do our best to accommodate your family's needs.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What is included in the chapel rental?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">All chapel rentals include air-conditioning, basic sound system, podium and seating arrangement, cleaning after the service, and on-site staff assistance. Premium chapels include additional amenities like LED displays, private family rooms, and dedicated concierge service.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I bring my own caterer or floral arrangements?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, you are welcome to use external caterers and florists. We also have an on-site flower shop and can recommend trusted catering partners if you prefer. Our staff will coordinate with your chosen vendors to ensure a seamless event.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Is parking available for guests?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, we offer complimentary valet parking for all chapel guests. Our parking facility can accommodate a large number of vehicles, and our valet team ensures a smooth arrival experience for elderly and disabled guests.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I visit the chapels before booking?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Absolutely. We encourage families to schedule a viewing to walk through our chapels in person. Our staff will show you all available options, explain the features of each space, and help you choose the one that best suits your family's needs and preferences.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you offer virtual tours for remote families?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, we are launching 360-degree virtual tours of all six chapels soon. This will allow family members who cannot visit in person to explore our facilities from anywhere in the world. Sign up to be notified when this feature becomes available.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">How long can we use the chapel for the service?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Standard bookings include a 24-hour window for the service. Extended use can be arranged for multi-day vigils or special ceremonies. Our staff will work with you to accommodate your family's traditions and schedule.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Are your chapels accessible for elderly and disabled guests?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, all our chapels are equipped with wheelchair ramps, elevators, and accessible restrooms. Our staff is trained to assist elderly and mobility-impaired guests to ensure they can attend the service comfortably and with dignity.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I hold a reception at the chapel after the service?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, our Gold Memorial Hall and Executive Vault have dedicated reception areas where families can host post-service gatherings. We can coordinate with your caterer or provide recommendations from our trusted partners to make the reception seamless.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What audio-visual equipment is provided?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">All chapels include a basic sound system with microphones. Featured chapels come with professional-grade audio, LED displays, projectors, and multiple screen options for photo and video presentations. Additional AV equipment can be arranged upon request.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Is there a dress code for chapel services?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">There is no strict dress code, but we recommend wearing respectful, modest attire appropriate for a solemn occasion. Dark or neutral colors are traditional. Our staff can provide guidance on cultural or religious dress expectations if needed.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I decorate the chapel for the service?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, families are welcome to bring personal decorations, flowers, and memorial displays. Our staff will assist with setup and ensure everything is arranged beautifully before guests arrive. We can also coordinate with your decorator or florist for a cohesive look.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What is the cancellation policy for chapel bookings?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">We understand that circumstances change. Cancellations made at least 48 hours before the scheduled date receive a full refund. For last-minute cancellations, we work with families on a case-by-case basis to find the best resolution.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you accommodate religious or cultural service requirements?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Absolutely. Our chapels are designed to accommodate all faiths and cultural traditions. Whether you need a Catholic mass setup, an ecumenical arrangement, or a secular celebration of life, our team will ensure the space is configured to meet your specific requirements.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">What happens if there is a power outage during a service?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Our facility is equipped with backup generators that activate immediately during power interruptions. The service will continue without disruption, ensuring a seamless and dignified experience for you and your guests.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I bring live musicians or a choir to the chapel?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, live music is welcome in all our chapels. We have designated performance areas and can provide amplification equipment upon request. Our staff will coordinate logistics with your musicians to ensure smooth setup and sound quality.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Are there hotels nearby for out-of-town guests?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, there are several hotels and accommodations within walking distance and a short drive from our facility. Our concierge team can provide recommendations and even assist with booking arrangements for your out-of-town guests.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Can I request a specific chapel layout or seating arrangement?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, our team will work with you to create the ideal seating layout for your service. Whether you prefer traditional rows, theater-style seating, or a more intimate arrangement, we will configure the space to match your vision and accommodate your guest count.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Do you offer memorial video production services?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">Yes, our in-house multimedia team can create a personalized memorial video using photos and video clips provided by the family. This tribute video can be played during the service and a digital copy provided to the family as a lasting keepsake.</p>
            </div>

            <div class="sn-card fade-in" style="padding:28px;">
                <h5 style="margin-bottom:8px;">Is there a chapel available for last-minute arrangements?</h5>
                <p style="margin:0;color:var(--text-strong);opacity:0.82;font-size:0.9375rem;">We understand that death can occur unexpectedly. While we recommend booking in advance when possible, we do our best to accommodate last-minute arrangements. Contact us immediately and our team will work to secure an available chapel for your family's needs.</p>
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
            <span class="badge badge-gold mb-2">Visit Us</span>
            <h2 style="margin-bottom:16px;color:var(--ink-on-navy);">Schedule a Viewing</h2>
            <p style="font-size:1.125rem;max-width:560px;margin:0 auto 32px;color:rgba(255,255,255,0.7);">Visit our memorial center and see our chapels in person. Our staff will be happy to guide you through every option and answer all of your questions.</p>
            <div class="d-flex justify-center gap-2 flex-wrap">
                <a href="contact.php?subject=Life%20Plan%20Information&message=I%20would%20like%20to%20book%20a%20chapel%20tour." class="btn btn-gold btn-lg">Book a Tour</a>
                <a href="tel:+639171234567" class="btn btn-white btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    +63 917 123 4567
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

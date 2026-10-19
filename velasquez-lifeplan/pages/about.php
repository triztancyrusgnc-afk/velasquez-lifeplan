<?php
$pageTitle = 'About Us';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section style="background:linear-gradient(135deg,#1A2634 0%,#2E4A6B 100%);padding:150px 0 90px;position:relative;overflow:hidden;">
    <div style="position:absolute;bottom:-100px;right:-100px;width:380px;height:380px;border-radius:50%;background:rgba(204,184,119,0.08);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="fade-in" style="max-width:760px;">
            <span class="sn-eyebrow" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);color:var(--gold);">Since 1990</span>
            <h1 class="sn-title" style="color:var(--ink-on-navy);font-size:clamp(2.4rem,5vw,3.4rem);">About <em style="color:var(--gold);">Velasquez</em> Funeral Service</h1>
            <p class="sn-sub" style="color:rgba(255,255,255,0.78);">Over three decades of compassionate service, honoring lives and comforting families across the Philippines. We believe every life deserves to be remembered with dignity.</p>
            <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:32px;">
                <a href="#our-story" class="btn btn-gold btn-lg" style="min-width:180px;">Our Story</a>
                <a href="#officers" class="btn btn-white btn-lg" style="min-width:180px;">Meet the Team</a>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="sn-section" id="our-story">
    <div class="container">
        <div class="grid-2 align-center" style="gap: 64px;">
            <div class="fade-in">
                <span class="sn-eyebrow">Our Story</span>
                <h2 class="sn-title">A Legacy Built on <em>Compassion</em></h2>
                <img class="cover-img tall" style="border-radius:16px;margin-bottom:20px;" src="/velasquez-lifeplan/img/about-building.jpg" alt="Velasquez Funeral Service building">
                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--gray-500); margin-bottom: 20px;">Founded in 1990 by Roberto Velasquez, Velasquez Funeral Service began with a simple yet profound mission: to provide dignified, compassionate, and affordable funeral services to Filipino families during their most difficult moments. What started as a small, family-operated memorial home in Manila quickly earned the trust of the community through its unwavering commitment to treating every family with genuine care and respect.</p>
                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--gray-500); margin-bottom: 20px;">Over the decades, we have grown from a single chapel into one of the most trusted funeral service providers in Central Luzon and across the Philippines. Our expansion was driven not by ambition alone, but by the growing need of families seeking reliable, heartfelt end-of-life services. Today, Velasquez Funeral Service stands as a beacon of trust, combining traditional Filipino values of family and community with modern innovations in funeral care and life planning.</p>
                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--gray-500); margin-bottom: 0;">From our state-of-the-art chapels to our comprehensive pre-need life plans, every aspect of our service is designed to bring comfort, peace, and assurance. We are more than a funeral service provider &mdash; we are a partner in honoring the lives of those you love.</p>
            </div>
            <div class="fade-in">
                <div style="background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%); border-radius: 28px; padding: 56px 48px; text-align: center; border: 3px solid var(--gold); position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; border-radius: 50%; background: rgba(212,175,55,0.1);"></div>
                    <div style="position: absolute; bottom: -20px; left: -20px; width: 80px; height: 80px; border-radius: 50%; background: rgba(212,175,55,0.08);"></div>
                    <div style="position: relative; z-index: 1;">
                        <div style="font-family: var(--font-serif); font-size: 4rem; font-weight: 700; color: var(--gold); line-height: 1;">35+</div>
                        <div style="font-size: 1.1rem; color: rgba(255,255,255,0.8); margin-top: 8px;">Years of Dedicated Service</div>
                        <div class="gold-line" style="margin: 20px auto;"></div>
                        <div style="font-family: var(--font-serif); font-size: 4rem; font-weight: 700; color: var(--gold); line-height: 1;">15,000+</div>
                        <div style="font-size: 1.1rem; color: rgba(255,255,255,0.8); margin-top: 8px;">Families Served Nationwide</div>
                        <div class="gold-line" style="margin: 20px auto;"></div>
                        <div style="font-family: var(--font-serif); font-size: 4rem; font-weight: 700; color: var(--gold); line-height: 1;">6</div>
                        <div style="font-size: 1.1rem; color: rgba(255,255,255,0.8); margin-top: 8px;">Chapels Across Luzon</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission, Vision, Promise Section -->
<section class="sn-section" style="background:var(--cream);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">What Guides Us</span>
            <h2 class="sn-title">Mission, Vision &amp; <em>Promise</em></h2>
            <p class="sn-sub">The guiding principles that define who we are and how we serve.</p>
        </div>

        <div class="grid-3" style="margin-top: 12px;">
            <!-- Mission Card -->
            <div class="sn-card topline hover-lift text-center fade-in">
                <div class="card-body" style="padding: 48px 32px;">
                    <div class="sn-icon" style="margin:0 auto 24px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v4l3 3"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 16px; color: var(--navy);">Our Mission</h3>
                    <div class="gold-line" style="margin: 0 auto 20px;"></div>
                    <p style="font-size: 0.95rem; line-height: 1.8; color: var(--gray-500); margin-bottom: 0;">To provide dignified, compassionate, and affordable funeral services that honor the lives of loved ones while supporting families through their journey of grief and healing. We strive to make quality funeral care accessible to every Filipino family, regardless of their financial circumstances, through flexible payment plans and comprehensive pre-need programs.</p>
                </div>
            </div>

            <!-- Vision Card -->
            <div class="sn-card topline hover-lift text-center fade-in">
                <div class="card-body" style="padding: 48px 32px;">
                    <div class="sn-icon" style="margin:0 auto 24px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 16px; color: var(--navy);">Our Vision</h3>
                    <div class="gold-line" style="margin: 0 auto 20px;"></div>
                    <p style="font-size: 0.95rem; line-height: 1.8; color: var(--gray-500); margin-bottom: 0;">To be the most trusted funeral service provider in Central Luzon and across the Philippines, setting the standard for excellence in memorial care, life planning, and family support services. We envision a future where every Filipino family has access to dignified funeral services through innovative, technology-driven solutions that honor both tradition and progress.</p>
                </div>
            </div>

            <!-- Promise Card -->
            <div class="sn-card topline hover-lift text-center fade-in">
                <div class="card-body" style="padding: 48px 32px;">
                    <div class="sn-icon" style="margin:0 auto 24px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 16px; color: var(--navy);">Our Promise</h3>
                    <div class="gold-line" style="margin: 0 auto 20px;"></div>
                    <p style="font-size: 0.95rem; line-height: 1.8; color: var(--gray-500); margin-bottom: 0;">We promise to treat every family with the utmost respect, empathy, and professionalism. From the first phone call to the memorial service and beyond, we stand beside you with transparent pricing, personalized care, and unwavering support. Your loved one will be treated as our own, and your family will never feel alone in your journey of remembrance and healing.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="sn-section">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">How We Serve</span>
            <h2 class="sn-title">Our Core <em>Values</em></h2>
            <p class="sn-sub">The principles that guide every aspect of our service and define who we are as an organization.</p>
        </div>

        <div class="grid-3" style="margin-top: 12px;">
            <!-- Dignity -->
            <div class="sn-card hover-lift fade-in" style="padding: 36px 28px;">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div class="sn-icon" style="flex-shrink:0;margin:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--navy); font-size: 1.1rem;">Dignity</h5>
                        <p style="font-size: 0.9rem; line-height: 1.7; color: var(--gray-500); margin-bottom: 0;">We uphold the inherent worth of every individual. From the moment we are entrusted with your loved one, we ensure that every ritual, every ceremony, and every detail reflects the dignity and honor they deserve in their final journey.</p>
                    </div>
                </div>
            </div>

            <!-- Compassion -->
            <div class="sn-card hover-lift fade-in" style="padding: 36px 28px;">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div class="sn-icon" style="flex-shrink:0;margin:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                        </svg>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--navy); font-size: 1.1rem;">Compassion</h5>
                        <p style="font-size: 0.9rem; line-height: 1.7; color: var(--gray-500); margin-bottom: 0;">We approach every family with genuine empathy and understanding during their time of need. Our team is trained to listen with patience, speak with kindness, and act with heartfelt sincerity, because we know that grief requires not just service, but soul.</p>
                    </div>
                </div>
            </div>

            <!-- Excellence -->
            <div class="sn-card hover-lift fade-in" style="padding: 36px 28px;">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div class="sn-icon" style="flex-shrink:0;margin:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--navy); font-size: 1.1rem;">Excellence</h5>
                        <p style="font-size: 0.9rem; line-height: 1.7; color: var(--gray-500); margin-bottom: 0;">We pursue the highest standards in everything we do, from our modern, well-maintained facilities to the training and development of our staff. Continuous improvement is woven into our culture, ensuring that every service exceeds expectations.</p>
                    </div>
                </div>
            </div>

            <!-- Integrity -->
            <div class="sn-card hover-lift fade-in" style="padding: 36px 28px;">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div class="sn-icon" style="flex-shrink:0;margin:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--navy); font-size: 1.1rem;">Integrity</h5>
                        <p style="font-size: 0.9rem; line-height: 1.7; color: var(--gray-500); margin-bottom: 0;">Transparent pricing and honest dealings are the foundation of our relationship with every family we serve. We believe that trust is earned through consistency, and we never compromise our ethical standards, even in the most challenging circumstances.</p>
                    </div>
                </div>
            </div>

            <!-- Family -->
            <div class="sn-card hover-lift fade-in" style="padding: 36px 28px;">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div class="sn-icon" style="flex-shrink:0;margin:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                            <path d="M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--navy); font-size: 1.1rem;">Family</h5>
                        <p style="font-size: 0.9rem; line-height: 1.7; color: var(--gray-500); margin-bottom: 0;">At the heart of Filipino culture is family, and at the heart of our service is treating every client as our own. We believe that compassion is strongest when we see ourselves in the families we serve, and that bond of kinship guides our every decision.</p>
                    </div>
                </div>
            </div>

            <!-- Service -->
            <div class="sn-card hover-lift fade-in" style="padding: 36px 28px;">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div class="sn-icon" style="flex-shrink:0;margin:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="16"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--navy); font-size: 1.1rem;">Service</h5>
                        <p style="font-size: 0.9rem; line-height: 1.7; color: var(--gray-500); margin-bottom: 0;">Above all, we are defined by our unwavering dedication to service. From the first consultation to post-service support, our team is available around the clock to ensure that every detail is handled with care, professionalism, and the personal touch that sets us apart.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- History Timeline Section -->
<section class="section bg-cream" id="timeline">
    <div class="container">
        <div class="section-header text-center fade-in">
            <h2>Our History</h2>
            <p>Key milestones that have shaped our journey of service and dedication.</p>
            <span class="gold-line"></span>
        </div>

        <div style="position: relative; max-width: 860px; margin: 56px auto 0;">
            <!-- Timeline center line -->
            <div style="position: absolute; left: 50%; top: 0; bottom: 0; width: 3px; background: linear-gradient(to bottom, var(--gold), var(--gold-dark, #b8962e)); transform: translateX(-50%); border-radius: 2px;"></div>

            <!-- 1990 -->
            <div class="d-flex align-center fade-in" style="position: relative; margin-bottom: 56px;">
                <div style="flex: 1; text-align: right; padding-right: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; text-align: left; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">1990</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">Company Founded</h5>
                        <p class="text-muted text-sm mb-0">Roberto Velasquez established Velasquez Funeral Service with the opening of our first memorial chapel in Manila, starting with a vision to provide dignified care to Filipino families.</p>
                    </div>
                </div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;"></div>
            </div>

            <!-- 1995 -->
            <div class="d-flex align-center fade-in" style="position: relative; margin-bottom: 56px;">
                <div style="flex: 1; text-align: right; padding-right: 48px;"></div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">1995</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">First Chapel in Guagua</h5>
                        <p class="text-muted text-sm mb-0">Opened our first full-service chapel in Guagua, Pampanga, marking our expansion to Central Luzon and establishing our presence in the region.</p>
                    </div>
                </div>
            </div>

            <!-- 2000 -->
            <div class="d-flex align-center fade-in" style="position: relative; margin-bottom: 56px;">
                <div style="flex: 1; text-align: right; padding-right: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; text-align: left; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">2000</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">Angeles City Expansion</h5>
                        <p class="text-muted text-sm mb-0">Expanded our operations to Angeles City, bringing our compassionate funeral services to a wider community in Pampanga and the surrounding provinces.</p>
                    </div>
                </div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;"></div>
            </div>

            <!-- 2005 -->
            <div class="d-flex align-center fade-in" style="position: relative; margin-bottom: 56px;">
                <div style="flex: 1; text-align: right; padding-right: 48px;"></div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">2005</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">Gold Memorial Hall Opens</h5>
                        <p class="text-muted text-sm mb-0">Unveiled the flagship Gold Memorial Hall, our premium facility featuring state-of-the-art sound systems, digital displays, and capacity for 500+ guests.</p>
                    </div>
                </div>
            </div>

            <!-- 2010 -->
            <div class="d-flex align-center fade-in" style="position: relative; margin-bottom: 56px;">
                <div style="flex: 1; text-align: right; padding-right: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; text-align: left; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">2010</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">eServices Platform Launched</h5>
                        <p class="text-muted text-sm mb-0">Introduced our digital platform for online policy management, payment processing, and service consultations, making funeral planning accessible from anywhere.</p>
                    </div>
                </div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;"></div>
            </div>

            <!-- 2015 -->
            <div class="d-flex align-center fade-in" style="position: relative; margin-bottom: 56px;">
                <div style="flex: 1; text-align: right; padding-right: 48px;"></div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">2015</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">15,000th Family Served</h5>
                        <p class="text-muted text-sm mb-0">Reached the milestone of serving 15,000 families, a testament to the trust and confidence placed in us by communities across the Philippines.</p>
                    </div>
                </div>
            </div>

            <!-- 2020 -->
            <div class="d-flex align-center fade-in" style="position: relative; margin-bottom: 56px;">
                <div style="flex: 1; text-align: right; padding-right: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; text-align: left; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">2020</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">Virtual Funeral Streaming</h5>
                        <p class="text-muted text-sm mb-0">Adapted to the global pandemic by launching live-streaming funeral services, allowing families overseas to participate in memorial services in real time.</p>
                    </div>
                </div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;"></div>
            </div>

            <!-- 2025 -->
            <div class="d-flex align-center fade-in" style="position: relative;">
                <div style="flex: 1; text-align: right; padding-right: 48px;"></div>
                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--gold); border: 4px solid var(--white); box-shadow: 0 2px 12px rgba(212,175,55,0.4); position: relative; z-index: 1; flex-shrink: 0;"></div>
                <div style="flex: 1; padding-left: 48px;">
                    <div class="card hover-lift" style="padding: 24px 28px; display: inline-block; border-left: 3px solid var(--gold);">
                        <span class="badge badge-gold mb-2">2025</span>
                        <h5 style="margin-bottom: 6px; color: var(--navy);">30+ Years of Service</h5>
                        <p class="text-muted text-sm mb-0">Celebrating over three decades of compassionate service with 6 chapels, 15,000+ families served, and a continued commitment to innovation and excellence in funeral care.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Officers Section -->
<section class="sn-section" id="officers">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Leadership</span>
            <h2 class="sn-title">Our <em>Officers</em></h2>
            <p class="sn-sub">The dedicated leadership team behind Velasquez Funeral Service.</p>
        </div>

        <div class="grid-3" style="margin-top: 12px;">
            <!-- Roberto Velasquez -->
            <div class="sn-card hover-lift text-center fade-in">
                <div class="card-body" style="padding: 40px 32px;">
                    <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; border: 3px solid var(--gold); box-shadow: 0 8px 24px rgba(26,38,52,0.3); position: relative; overflow: hidden;">
                        <span style="font-family: var(--font-serif); font-size: 2.2rem; font-weight: 700; color: var(--gold); position: relative; z-index: 1;">RV</span>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 30%; background: rgba(204,184,119,0.12);"></div>
                    </div>
                    <h5 style="margin-bottom: 4px; font-size: 1.1rem; color: var(--navy);">Roberto Velasquez</h5>
                    <p class="text-gold text-sm" style="font-weight: 600; margin-bottom: 12px;">Chairman &amp; CEO</p>
                    <p class="text-muted text-sm mb-0">Founder and visionary leader with over 35 years of experience in funeral service management. Roberto's passion for dignified care has shaped the company's legacy.</p>
                </div>
            </div>

            <!-- Maria Teresa Velasquez -->
            <div class="sn-card hover-lift text-center fade-in">
                <div class="card-body" style="padding: 40px 32px;">
                    <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; border: 3px solid var(--gold); box-shadow: 0 8px 24px rgba(26,38,52,0.3); position: relative; overflow: hidden;">
                        <span style="font-family: var(--font-serif); font-size: 2.2rem; font-weight: 700; color: var(--gold); position: relative; z-index: 1;">MV</span>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 30%; background: rgba(204,184,119,0.12);"></div>
                    </div>
                    <h5 style="margin-bottom: 4px; font-size: 1.1rem; color: var(--navy);">Maria Teresa Velasquez</h5>
                    <p class="text-gold text-sm" style="font-weight: 600; margin-bottom: 12px;">President</p>
                    <p class="text-muted text-sm mb-0">Oversees the company's strategic direction and ensures that every family receives the highest standard of compassionate service across all operations.</p>
                </div>
            </div>

            <!-- Jose Antonio Velasquez -->
            <div class="sn-card hover-lift text-center fade-in">
                <div class="card-body" style="padding: 40px 32px;">
                    <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; border: 3px solid var(--gold); box-shadow: 0 8px 24px rgba(26,38,52,0.3); position: relative; overflow: hidden;">
                        <span style="font-family: var(--font-serif); font-size: 2.2rem; font-weight: 700; color: var(--gold); position: relative; z-index: 1;">JV</span>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 30%; background: rgba(204,184,119,0.12);"></div>
                    </div>
                    <h5 style="margin-bottom: 4px; font-size: 1.1rem; color: var(--navy);">Jose Antonio Velasquez</h5>
                    <p class="text-gold text-sm" style="font-weight: 600; margin-bottom: 12px;">VP Operations</p>
                    <p class="text-muted text-sm mb-0">Manages day-to-day operations across all chapel locations, ensuring consistent service quality, efficient workflows, and exceptional family experiences.</p>
                </div>
            </div>

            <!-- Ana Katrina Velasquez-Santos -->
            <div class="sn-card hover-lift text-center fade-in">
                <div class="card-body" style="padding: 40px 32px;">
                    <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; border: 3px solid var(--gold); box-shadow: 0 8px 24px rgba(26,38,52,0.3); position: relative; overflow: hidden;">
                        <span style="font-family: var(--font-serif); font-size: 2.2rem; font-weight: 700; color: var(--gold); position: relative; z-index: 1;">AS</span>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 30%; background: rgba(204,184,119,0.12);"></div>
                    </div>
                    <h5 style="margin-bottom: 4px; font-size: 1.1rem; color: var(--navy);">Ana Katrina Velasquez-Santos</h5>
                    <p class="text-gold text-sm" style="font-weight: 600; margin-bottom: 12px;">Treasurer &amp; CFO</p>
                    <p class="text-muted text-sm mb-0">Leads financial planning and management, ensuring affordable service options and sustainable growth while maintaining the company's fiscal integrity.</p>
                </div>
            </div>

            <!-- Pedro Mendoza -->
            <div class="sn-card hover-lift text-center fade-in">
                <div class="card-body" style="padding: 40px 32px;">
                    <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; border: 3px solid var(--gold); box-shadow: 0 8px 24px rgba(26,38,52,0.3); position: relative; overflow: hidden;">
                        <span style="font-family: var(--font-serif); font-size: 2.2rem; font-weight: 700; color: var(--gold); position: relative; z-index: 1;">PM</span>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 30%; background: rgba(204,184,119,0.12);"></div>
                    </div>
                    <h5 style="margin-bottom: 4px; font-size: 1.1rem; color: var(--navy);">Pedro Mendoza</h5>
                    <p class="text-gold text-sm" style="font-weight: 600; margin-bottom: 12px;">Director, Sales &amp; Marketing</p>
                    <p class="text-muted text-sm mb-0">Drives the company's growth through strategic marketing initiatives and client acquisition, building relationships that extend the Velasquez legacy to new families.</p>
                </div>
            </div>

            <!-- Carmen Reyes -->
            <div class="sn-card hover-lift text-center fade-in">
                <div class="card-body" style="padding: 40px 32px;">
                    <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; border: 3px solid var(--gold); box-shadow: 0 8px 24px rgba(26,38,52,0.3); position: relative; overflow: hidden;">
                        <span style="font-family: var(--font-serif); font-size: 2.2rem; font-weight: 700; color: var(--gold); position: relative; z-index: 1;">CR</span>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 30%; background: rgba(204,184,119,0.12);"></div>
                    </div>
                    <h5 style="margin-bottom: 4px; font-size: 1.1rem; color: var(--navy);">Carmen Reyes</h5>
                    <p class="text-gold text-sm" style="font-weight: 600; margin-bottom: 12px;">Director, Human Resources</p>
                    <p class="text-muted text-sm mb-0">Oversees talent acquisition, employee development, and organizational culture, ensuring that the Velasquez team embodies the values of compassion and excellence.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Affiliations Section -->
<section class="sn-section" style="background:var(--cream);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Trusted &amp; Accredited</span>
            <h2 class="sn-title">Our <em>Affiliations</em></h2>
            <p class="sn-sub">Proud members of trusted organizations in the funeral service industry.</p>
        </div>

        <div class="grid-4" style="margin-top: 12px; max-width: 1000px; margin-left: auto; margin-right: auto;">
            <!-- ICCF -->
            <div class="sn-card text-center hover-lift fade-in" style="padding: 32px 20px;">
                <div class="sn-icon" style="margin:0 auto 16px;border:2px solid var(--gold);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                    </svg>
                </div>
                <h5 style="font-size: 0.95rem; color: var(--navy); margin-bottom: 4px;">ICCF</h5>
                <p class="text-xs text-muted" style="margin-bottom: 0;">International Cemetery, Cremation &amp; Funeral Association</p>
            </div>

            <!-- NFDA -->
            <div class="sn-card text-center hover-lift fade-in" style="padding: 32px 20px;">
                <div class="sn-icon" style="margin:0 auto 16px;border:2px solid var(--gold);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h5 style="font-size: 0.95rem; color: var(--navy); margin-bottom: 4px;">NFDA</h5>
                <p class="text-xs text-muted" style="margin-bottom: 0;">National Funeral Directors Association</p>
            </div>

            <!-- CANA -->
            <div class="sn-card text-center hover-lift fade-in" style="padding: 32px 20px;">
                <div class="sn-icon" style="margin:0 auto 16px;border:2px solid var(--gold);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <h5 style="font-size: 0.95rem; color: var(--navy); margin-bottom: 4px;">CANA</h5>
                <p class="text-xs text-muted" style="margin-bottom: 0;">Cremation Association of North America</p>
            </div>

            <!-- PCFI -->
            <div class="sn-card text-center hover-lift fade-in" style="padding: 32px 20px;">
                <div class="sn-icon" style="margin:0 auto 16px;border:2px solid var(--gold);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <h5 style="font-size: 0.95rem; color: var(--navy); margin-bottom: 4px;">PCFI</h5>
                <p class="text-xs text-muted" style="margin-bottom: 0;">Philippine Cemetery &amp; Funeral Association</p>
            </div>
        </div>
        <img class="cover-img" style="height:260px;border-radius:16px;" src="/velasquez-lifeplan/img/about-handshake.jpg" alt="Serving families with trust">
    </div>
</section>

<!-- CTA Section -->
<section class="sn-section">
    <div class="container">
        <div class="sn-band harbor text-center fade-in" style="max-width:860px;margin:0 auto;">
            <span class="badge badge-gold mb-3">Join the Velasquez Family</span>
            <h2 style="font-size: 2.2rem; margin-bottom: 16px; color: var(--ink-on-navy);">Secure Your Family's <em style="color:var(--gold);">Peace of Mind</em></h2>
            <p style="font-size: 1.1rem; color: rgba(255,255,255,0.75); line-height: 1.8; margin-bottom: 32px; max-width:640px; margin-left:auto; margin-right:auto;">Join the thousands of Filipino families who have chosen Velasquez Funeral Service for their life planning needs. Our pre-need plans offer affordable, flexible options that protect your loved ones when they need it most.</p>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <a href="../pages/plans.php" class="btn btn-gold btn-lg" style="min-width: 200px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0022 16z"/>
                    </svg>
                    Explore Plans
                </a>
                <a href="tel:09171234567" class="btn btn-white btn-lg" style="min-width: 200px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                    Call Us Now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Scroll Animation Script -->
<script>
(function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-in').forEach(function(el) {
        observer.observe(el);
    });
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

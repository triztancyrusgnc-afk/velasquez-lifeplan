<?php
/**
 * Knowledge base for the AI assistant.
 *
 * Built fresh on each question from the live database and the admin's current
 * settings, so the assistant never quotes a stale price or tells a visitor to
 * go buy a plan that is currently paused.
 *
 * PRIVACY: only public/catalogue data is included. No customer names, emails,
 * payments, claims or any other personal record is ever sent to the model.
 */

/**
 * Bump this whenever the wording or content of the knowledge base changes. It is
 * folded into the cache key by ai_cache_fingerprint(), so answers written by an
 * older version of this file expire the moment the edit lands.
 */
define('AI_CONTEXT_VERSION', 2);

function ai_context(PDO $pdo, array $settings) {
    $parts = [];

    $parts[] = "SITE IDENTITY
Business: Velasquez Funeral Service, a pre-need life plan and funeral services provider based in Guagua, Pampanga, Philippines. Established 1990.
Emergency line: 0917-123-4567 (24/7 hearse, chapel and full funeral assistance).
Office hours: Monday to Saturday, 8:00 AM - 5:00 PM. Emergency help is 24/7.
Email: info@velasquezfuneral.com";

    // ---- Live plan catalogue -------------------------------------------------
    $plans = [];
    try {
        $rows = $pdo->query('SELECT * FROM plans WHERE is_active = 1 ORDER BY category, price ASC')->fetchAll();
        foreach ($rows as $r) {
            $features = json_decode($r['features'] ?? '[]', true);
            $plans[] = [
                'name'        => $r['name'],
                'category'    => ucfirst($r['category']),
                'price'       => (float)$r['price'],
                'spot_cash'   => $r['spot_cash'] !== null ? (float)$r['spot_cash'] : null,
                'monthly_5yr' => $r['monthly_5yr'] !== null ? (float)$r['monthly_5yr'] : null,
                'monthly_10yr'=> $r['monthly_10yr'] !== null ? (float)$r['monthly_10yr'] : null,
                'downpayment' => $r['downpayment'] !== null ? (float)$r['downpayment'] : null,
                'casket'      => $r['casket_type'] ?: null,
                'viewing'     => isset($r['chapel_viewing_days']) ? (int)$r['chapel_viewing_days'] : null,
                'features'    => is_array($features) ? array_slice($features, 0, 8) : [],
            ];
        }
    } catch (Throwable $e) {
        // leave $plans empty; the model will say it cannot quote prices
    }

    if ($plans) {
        $lines = [];
        foreach ($plans as $p) {
            $bits = ['single cash price ' . ai_peso($p['price'])];
            if ($p['spot_cash'] !== null) { $bits[] = 'spot cash ' . ai_peso($p['spot_cash']); }
            if ($p['monthly_5yr'] !== null) { $bits[] = 'about ' . ai_peso($p['monthly_5yr']) . '/month over 5 years'; }
            if ($p['monthly_10yr'] !== null) { $bits[] = 'about ' . ai_peso($p['monthly_10yr']) . '/month over 10 years'; }
            if ($p['downpayment'] !== null) { $bits[] = 'downpayment ' . ai_peso($p['downpayment']); }
            $bits[] = $p['viewing'] !== null ? $p['viewing'] . ' chapel viewing days' : null;
            $bits = array_filter($bits);
            $extra = $p['features'] ? ' Includes: ' . implode('; ', $p['features']) . '.' : '';
            if ($p['casket']) { $extra .= ' Casket: ' . $p['casket'] . '.'; }
            $lines[] = '- ' . $p['name'] . ' (' . $p['category'] . '): ' . implode('; ', $bits) . '.' . $extra;
        }
        $parts[] = "LIFE PLAN CATALOGUE (live prices)\n" . implode("\n", $lines);
    } else {
        $parts[] = "LIFE PLAN CATALOGUE\nCurrently unavailable - no active plans are published.";
    }

    // ---- What is reachable right now ----------------------------------------
    $status = [];
    if (sectionPausedSafe($settings, 'plans')) {
        $status[] = '- Life plan browsing, comparison and online enrolment are PAUSED ("Under Construction"). Visitors cannot buy a plan online at the moment. Do not send them to buy one. Direct them to the contact page or the phone line for a quote.';
    } else {
        $status[] = '- Life plans: available. Visitors can browse plans, compare them and enrol online via checkout.';
    }
    if (sectionPausedSafe($settings, 'chapels')) {
        $status[] = '- Chapels and facilities are PAUSED ("Under Construction"). The chapel is still being built and is not open to the public. Do not promise a chapel viewing.';
    } else {
        $status[] = '- Chapels: available to view.';
    }
    $status[] = '- Caskets and urns, grief support, eServices, about, contact and the emergency claim wizard are all available.';
    $status[] = '- Customers can always sign in to their own dashboard.';
    $parts[] = "CURRENT SITE STATUS (as of right now)\n" . implode("\n", $status);

    $parts[] = "PAGES AND WHAT THEY DO
- Home: hero, trust badges, life plan section, payment calculator, chapel teaser, testimonials, quick contact form.
- Life Plans: browse the catalogue with category filters and a details modal per plan.
- Compare Plans: pick up to three plans and view them side by side.
- Checkout: five-step enrolment wizard - choose plan and payment term, enter holder details, review, confirm and pay.
- Chapels & Facilities: chapel accommodations, viewing halls and event venues.
- Caskets & Urns: casket and urn selections with filters.
- Grief Support: free guidance and bereavement resources.
- eServices: online payments, documents and scheduling.
- About: company story, officers and timeline since 1990.
- Contact: enquiry form, map details and office hours.
- Emergency Claim Wizard (customer login): file a claim at-need, attach the death certificate, and see the reserved chapel and assigned hearse.
- Printable Receipt (from the customer dashboard): official receipt for a verified payment.";

    $parts[] = "HOW PAYING AND POLICIES WORK
- A customer signs up, picks a plan and a payment term, then completes checkout.
- Each enrolment creates a subscription with a generated payment schedule.
- They upload proof of payment (an image or PDF) against an installment.
- Staff verify the payment; verifying it marks the installment paid, updates the paid amount and the remaining balance, and stamps an official receipt number (format OR-000000).
- Customers can download that receipt any time from their dashboard.
- A customer can nominate beneficiaries on their policy.
- At need, a policy holder (or their representative) files a claim from the emergency wizard.
- Plans are transferable and assignable; some include cash back and insurance coverage - the per-plan details are listed above.";

    $parts[] = "WHAT A VISITOR AND A CUSTOMER CAN DO
- Nobody needs an account to browse the site, use the payment calculator, ask questions here, or get in touch.
- A customer signs in to their dashboard to see their policy details, payment history and receipts, upload proof of payment, manage beneficiaries, and file a claim with the emergency claim wizard.
- Behind the scenes our staff handle the rest: payments are checked and verified by a person, a receipt is issued, and claims are processed by the team. The customer never has to do anything but upload and wait for confirmation.";

    $parts[] = "TONE AND SCOPE
You are the assistant for Velasquez Funeral Service. Be warm, calm, plain-spoken and respectful - this is a service people turn to at a difficult time. Use short paragraphs and bullet lists. You are speaking to visitors and customers, so answer their questions about the plans, the prices, payments, claims and how to use this website. Do not describe our staff or admin panels, internal tooling, back-office permissions or how the company runs internally - that is not what anyone here is asking you about. Never invent prices, dates, plan names, features or policies; if it is not in the information above, say you do not know and offer to have the team follow up. Never give medical, legal or financial advice. For anything urgent or personal, point to the 24/7 line (0917-123-4567) or the contact page. You may explain how the website and its accounts work, but never reveal or guess login credentials, and never disclose any customer's personal records.";

    return implode("\n\n", $parts);
}

function ai_peso($n) {
    return 'PHP ' . number_format((float)$n, 0);
}

/**
 * sectionPausedSafe() - same as sectionPaused() but degrades quietly if
 * includes/under_construction.php was not loaded on the current page.
 */
function sectionPausedSafe(array $settings, $section) {
    if (function_exists('sectionPaused')) {
        return sectionPaused($settings, $section);
    }
    $flag = $section . '_under_construction';
    return trim($settings[$flag] ?? '0') === '1';
}

/**
 * Answers the common questions locally, without any model call.
 *
 * The free tier allows only a couple of dozen questions a day. When it is empty
 * the widget must not dead-end, so this recognises the handful of intents that
 * cover most visitor traffic - prices, monthly terms, what is currently open,
 * how to pay, how to claim, and how to reach the office - and answers them from
 * the same live data the model would use.
 *
 * Returns null when nothing confidently matches, so the caller can fall back to
 * a plain "call us" message rather than guessing.
 */
function ai_local_answer(PDO $pdo, array $settings, $question) {
    $q = ' ' . mb_strtolower(trim($question)) . ' ';
    $has = function ($needles) use ($q) {
        foreach ((array)$needles as $n) {
            if (mb_strpos($q, $n) !== false) {
                return true;
            }
        }
        return false;
    };

    $contact = 'Phone: **0917-123-4567** (24/7 emergency) | Email: info@velasquezfuneral.com | '
        . 'Office: Monday to Saturday, 8:00 AM - 5:00 PM, Guagua, Pampanga.';

    // ---- Live plan rows (shared by the price answers) ----------------------
    $plans = [];
    try {
        $rows = $pdo->query('SELECT * FROM plans WHERE is_active = 1 ORDER BY category, price ASC')->fetchAll();
        foreach ($rows as $r) {
            $plans[] = $r;
        }
    } catch (Throwable $e) {
        $plans = [];
    }

    $plansPaused = sectionPausedSafe($settings, 'plans');
    $chapelsPaused = sectionPausedSafe($settings, 'chapels');

    $planLines = function ($withTerms = false) use ($plans) {
        $out = [];
        foreach ($plans as $p) {
            $bits = ['cash ' . ai_peso($p['price'])];
            if ($withTerms) {
                if ($p['monthly_5yr'] !== null)   { $bits[] = ai_peso($p['monthly_5yr']) . '/mo (5 yrs)'; }
                if ($p['monthly_10yr'] !== null)  { $bits[] = ai_peso($p['monthly_10yr']) . '/mo (10 yrs)'; }
                if ($p['downpayment'] !== null)   { $bits[] = 'downpayment ' . ai_peso($p['downpayment']); }
            }
            $out[] = '- **' . $p['name'] . '** (' . ucfirst($p['category']) . '): ' . implode(', ', $bits);
        }
        return implode("\n", $out);
    };

    // ---- 1. Chapel status (checked first: it is a firm, current fact) ------
    if ($has(['chapel', 'viewing', 'wake', 'funeral home', 'venue', 'facility', 'facilities'])) {
        if ($chapelsPaused) {
            return "### Chapel and facilities\n\n"
                . "The chapels are still being built and are **not open to the public** yet, so we cannot book a viewing or a wake through the website at the moment.\n\n"
                . "For a wake, funeral arrangement or hearse service, please call **0917-123-4567** - that line is answered 24/7.";
        }
        return "### Chapels and facilities\n\n"
            . "The chapels and viewing halls are open. You can see the full list on the **Chapels & Facilities** page.\n\n"
            . "To reserve a date, please call **0917-123-4567** so the team can confirm availability.";
    }

    // ---- 2. Prices / catalogue --------------------------------------------
    if ($has(['price', 'prices', 'cost', 'how much', 'magkano', 'magkano ang', 'package', 'plan list',
             'life plan', 'lifeplan', 'what plan', 'which plan', 'affordable', 'rate'])) {
        if (!$plans) {
            return "I do not have the current price list in front of me just now. "
                . "Please call **0917-123-4567** or use the contact page and the team will send you the full catalogue.";
        }
        $out = "### Current life plan prices\n\n" . $planLines(true) . "\n";
        if ($plansPaused) {
            $out .= "\n> Online enrolment is paused for now, so these cannot be bought through the website today. "
                . "Call **0917-123-4567** and the team will take care of it for you.";
        } else {
            $out .= "\nYou can browse, compare and enrol online from the **Life Plans** page.";
        }
        return $out;
    }

    // ---- 3. Monthly / instalment terms -------------------------------------
    if ($has(['monthly', 'month', 'instal', 'install', 'hulugan', 'payment term', 'downpayment',
             'down payment', 'spot cash', 'how do i pay', 'how to pay', 'pabayad'])) {
        if (!$plans) {
            return "I cannot see the payment terms right now. Please call **0917-123-4567** and the team will explain the options.";
        }
        $out = "### Payment terms\n\nEvery plan can be paid in full or in instalments:\n\n" . $planLines(true) . "\n\n";
        $out .= "How the process works:\n"
            . "1. Choose a plan and a term during checkout.\n"
            . "2. Upload proof of payment against your instalment.\n"
            . "3. Our staff verify it, mark it paid, and issue an official receipt you can download any time.\n";
        if ($plansPaused) {
            $out .= "\n> Online enrolment is paused at the moment - please call **0917-123-4567** to get started.";
        }
        return $out;
    }

    // ---- 4. Can I buy / enrol right now? -----------------------------------
    // Deliberately no bare "available" / "open" - those belong to "are you open
    // today?" and should reach the contact answer below, not this one.
    if ($has(['buy', 'purchase', 'enrol', 'enroll', 'sign up', 'register', 'checkout', 'get a plan',
             'apply for', 'under construction', 'not working', 'can i buy',
             'plan available', 'plans available', 'plan open', 'plans open',
             'pages open', 'is the site back'])) {
        if ($plansPaused) {
            return "### Online enrolment is paused for now\n\n"
                . "The life plan pages are being worked on, so you **cannot buy a plan online today**. Nothing has been lost - it will be back.\n\n"
                . "In the meantime the team can enrol you over the phone: **0917-123-4567** (Monday to Saturday, 8 AM - 5 PM), "
                . "or leave your details on the contact page and they will call you back.";
        }
        return "### You can enrol online now\n\n"
            . "1. Open the **Life Plans** page and pick a plan.\n"
            . "2. Compare it on **Compare Plans** if you would like to see them side by side.\n"
            . "3. Start checkout - it takes you through plan and term, your details, a review, and payment.\n\n"
            . "Any questions along the way, call **0917-123-4567**.";
    }

    // ---- 5. Claims / at-need ------------------------------------------------
    // Only claim-specific wording. Generic "how do I ..." belongs to whichever
    // intent actually matches the object of the question.
    if ($has(['claim', 'death certificate', 'at-need', 'bereave', 'died', 'patay',
             'hearse', 'funeral arrangement'])) {
        return "### Filing a claim\n\n"
            . "When a plan holder passes away:\n"
            . "1. Sign in with your account.\n"
            . "2. Open the **Emergency Claim Wizard**.\n"
            . "3. Attach the death certificate and submit.\n"
            . "4. You will see the reserved chapel and the assigned hearse on that same page.\n\n"
            . "Our staff then move the claim through pending, processing, approved and completed.\n\n"
            . "For anything immediate, call **0917-123-4567** - that line is staffed 24/7.";
    }

    // ---- 6. Receipts / proof of payment ------------------------------------
    if ($has(['receipt', 'proof of payment', 'verified', 'official receipt', 'or number', 'paid'])) {
        return "### Receipts and proof of payment\n\n"
            . "- Upload your proof of payment from your dashboard against the relevant instalment (image or PDF).\n"
            . "- Staff verify it; that marks the instalment paid and updates your remaining balance.\n"
            . "- An official receipt number (format OR-000000) is issued, and you can download the receipt any time from your dashboard.\n\n"
            . "If something looks wrong, call **0917-123-4567**.";
    }

    // ---- 7. Internal / staff questions: decline politely, on scope ---------
    // Someone asking what the admin or staff can do is not a customer question.
    // Stay helpful, say what we do cover, and hand them to the team.
    if ($has(['staff', 'admin', 'administrator', 'employee', 'worker', 'agent', 'role',
             'back office', 'internal', 'dashboard of', 'permission', 'who approves'])) {
        return "### I only cover the customer side\n\n"
            . "I'm here to help with life plans, prices, payments, claims, receipts and using this website - "
            . "that's what I'm built for, and I'd rather get that right than guess about anything else.\n\n"
            . "For anything beyond that, our team will help you directly: **0917-123-4567** "
            . "(Monday to Saturday, 8:00 AM - 5:00 PM) or the contact page.";
    }

    // ---- 8. Accounts, login -------------------------------------------------
    if ($has(['login', 'log in', 'sign in', 'password', 'account', 'dashboard',
             'forgot password', 'locked out', 'my account'])) {
        return "### Your account\n\n"
            . "Sign in and your dashboard shows your policy, payment history, receipts and beneficiaries, "
            . "plus upload of proof of payment and the emergency claim wizard.\n\n"
            . "I cannot view or reset passwords for security reasons - please call **0917-123-4567** "
            . "and the team will help you regain access.";
    }

    // ---- 9. Caskets / urns / grief ------------------------------------------
    if ($has(['casket', 'urn', 'grief', 'bereave', 'condolence', 'aftercare'])) {
        if ($has(['casket', 'urn'])) {
            return "### Caskets and urns\n\n"
                . "We carry a range of caskets and urns, shown with filters on the **Caskets & Urns** page. "
                . "Many life plans already include a casket - the inclusions are listed on each plan.\n\n"
                . "To see them or ask about a specific one, call **0917-123-4567**.";
        }
        return "### Grief support\n\n"
            . "The **Grief Support** page has free guidance on coping with a loss, and our team is here to talk "
            . "through arrangements with you at your own pace.\n\n"
            . "For an immediate need, call **0917-123-4567** - 24 hours a day.";
    }

    // ---- 10. Contact / where are you ----------------------------------------
    if ($has(['contact', 'phone', 'number', 'email', 'address', 'where', 'office', 'hours',
             'location', 'guagua', 'reach', 'call', 'open', 'what time', 'when are you',
             'closing', 'closed', 'today'])) {
        return "### How to reach us\n\n" . $contact . "\n\n"
            . "You can also leave a message on the **Contact** page and the team will get back to you.";
    }

    return null; // no confident match - let the caller give the plain fallback
}
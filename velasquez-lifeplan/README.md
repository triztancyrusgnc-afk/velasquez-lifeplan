# Velasquez Funeral Service — Life Plan System

A complete funeral service management website that runs on **XAMPP only**.
No Node.js, no npm, no build step. Just copy, start XAMPP, and open in browser.

## What's inside

| Page | Address |
|------|---------|
| Setup wizard (run first) | `http://localhost/velasquez-lifeplan/` |
| Homepage | `http://localhost/velasquez-lifeplan/home.php` |
| Life Plans | `.../pages/plans.php` |
| Chapels | `.../pages/chapels.php` |
| Caskets | `.../pages/caskets.php` |
| Grief Support | `.../pages/grief.php` |
| eServices | `.../pages/eservices.php` |
| Compare Plans | `.../pages/compare.php` |
| About Us | `.../pages/about.php` |
| Contact | `.../pages/contact.php` |
| Sign In / Register | `.../login.php` |
| Checkout (5 steps) | `.../checkout.php` |
| Emergency Claim Wizard | `.../pages/claim.php` (customer login: death certificate upload, chapel + hearse booking) |
| Printable Receipt | `.../pages/receipt.php?id=...` (print / save as PDF) |
| Client Dashboard | `.../pages/dashboard.php` (customer login required) |
| Staff Panel | `.../pages/staff.php` (employee login: verify payments, view customers/policies/inventory — no approvals) |
| Admin Panel | `.../pages/admin.php` (admin login required) |
| Admin Website Settings | `.../pages/admin_settings.php` (admin login required) |

## What was changed recently

- **Homepage is admin-editable** — hero photo, hero copy, trust badges and the
  life-plan "under construction" switch, all from *Website Settings*.
- **Dark mode on every page** — light and dark themes, toggle in the top bar,
  remembers the visitor's choice.
- **Panels unified** — admin, staff and customer dashboards now share one
  sidebar, one set of KPI tiles, one card style and one empty state.

## Setup on a new PC (5 minutes)

### Step 1 — Install XAMPP
1. Download XAMPP for Windows (PHP 8.x): https://www.apachefriends.org
2. Install it to the default location `C:\xampp`.

### Step 2 — Copy this folder
1. Copy the `velasquez-lifeplan` folder into `C:\xampp\htdocs\`
2. The path must be exactly: `C:\xampp\htdocs\velasquez-lifeplan\`

### Step 3 — Start Apache + MySQL
1. Open **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Both must show green "Running".

### Step 4 — Set up the database (pick ONE option)

**Option A — Step files (easiest, recommended):**
Just open these 4 pages in order. Each one prints green checkmarks
and links you to the next step:
1. `http://localhost/velasquez-lifeplan/Step1.php` — creates the database,
   users + plans tables, and 5 demo accounts
2. `.../Step2.php` — creates 6 life plans, subscriptions, payment schedule
3. `.../Step3.php` — creates beneficiaries, claims, inventory, contact table
4. `.../Step4.php` — connects the system, verifies everything, then links
   you to the homepage
5. `.../Step5.php` — upgrades existing installs (claim fields, receipt
   numbers, agent role, upload folders). Safe to re-run any time.

The wizard seeds a full demo office: 7 users, 6 life plans, 5 policies
(2 awaiting approval), payable schedules with overdue demos, 3 payments
queued for verification, 1 pending claim, and 11 inventory items
including low-stock alerts.

**Option B — Setup wizard:**
1. Open a browser and go to: `http://localhost/velasquez-lifeplan/`
2. **Step 1 — Welcome:** checks PHP version and extensions. All should show green.
3. **Step 2 — Database:** keep the defaults (host `localhost`, user `root`,
   empty password, database `velasquez_lifeplan`) and click Install.
   - If your XAMPP MySQL has a root password, type it in the password field.
4. **Step 3 — Install:** creates the database, all tables, demo accounts,
   and 6 life plans automatically.
5. **Step 4 — Complete:** click **Go to Homepage**.

That's it. The site is ready to use.

## View on other devices (same WiFi)

The site is named **velasquez** on the network:

| Device | Address | Setup needed |
|--------|---------|--------------|
| This PC | `http://velasquez/` | Run once as admin: `echo 127.0.0.1 velasquez >> C:\Windows\System32\drivers\etc\hosts` |
| Other computers | `http://velasquez/` | Same WiFi + add `<this-PC-IP> velasquez` to their hosts file (one-time, needs admin) |
| Phones / tablets | `http://192.168.1.4/velasquez-lifeplan/` | Nothing — just join the same WiFi (see firewall note) |

Requirements: this PC stays on, XAMPP Apache + MySQL running, all devices
on the same WiFi. If a phone can't connect, allow Apache through Windows
Firewall once (as admin): Windows Settings → Firewall → Allow an app →
Apache HTTP Server → check Private network. Or in admin PowerShell:
`New-NetFirewallRule -DisplayName "Velasquez XAMPP HTTP" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow -Profile Private`

Note: this PC's current address is `192.168.1.4` — if the WiFi changes and
the address changes, use the new address (find it with `ipconfig`).

## Put it on the internet (no same-WiFi needed)

Double-click **`go-online.bat`** (needs Node.js + internet on this PC, XAMPP
running). It publishes the site at:

**`https://velasquez.loca.lt`**

Anyone, anywhere can open that link — no setup on their side. First-time
visitors may see a one-time reminder page; they just enter the Tunnel
Password shown on that page and continue.

Rules of the road:
- Keep this PC on, XAMPP running, and the black tunnel window OPEN.
  Closing it (or sleeping the PC) takes the site offline.
- The link stays the same as long as nobody else claims the name first.
  If it ever says the name is taken, edit `go-online.bat` and change
  `--subdomain velasquez` to another name.
- The whole XAMPP web folder is reachable through the tunnel, so don't
  keep private files in `htdocs` while online.

## Demo accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@velasquez.com | admin123 |
| Employee | employee@velasquez.com | employee123 |
| Customer | maria.santos@gmail.com | customer123 |
| Customer | juan.dela.cruz@gmail.com | customer123 |
| Customer | ana.reyes@yahoo.com | customer123 |

Role powers: **customers** manage their own policies, receipts, and
beneficiaries; **employees** verify payments and view records;
**admins** approve policies, decide claims, edit inventory, and control what
the public website says.
(Agents can be assigned via the `agent` role for future sales use.)

## Editing the website (admin only)

Sign in as admin, then open **Admin Panel → Website Settings**
(`pages/admin_settings.php`). No code editing needed for any of this.

- **Hero photo** — upload a JPG/PNG/WEBP/GIF (max 5MB). Replacing it deletes
  the old upload; *Remove uploaded photo* restores the bundled
  `img/hero-chapel.jpg`.
- **Hero text** — the small label, both headline lines, the paragraph, the two
  button labels, the initials, and the line under them.
- **Trust badges** — the four counters under the hero. Clear a label to hide
  that badge.
- **Sections Under Construction** — independent switches that pause parts of
  the site without touching any code, each with its own editable headline,
  message and button label:

  | Switch | What it pauses when on |
  |---|---|
  | Life Plan Pages | `plans.php`, `compare.php`, `checkout.php`, and on the homepage the plan grid + category tabs + plan modal (replaced by a notice). The payment calculator stays but its button becomes "Talk to Our Team". |
  | Chapel Pages | `chapels.php`, and on the homepage the chapel teaser (replaced by a notice). |

  Caskets, grief support, eServices, contact and the claim wizard are never
  affected. Customers can always sign in to their own dashboard.

  The holding page's "everything else stays open" tiles automatically drop any
  section that is itself paused, so the page never links into another notice.

  To pause a **new** section: add `'<name>_under_construction'`,
  `'<name>_title'`, `'<name>_message'` and `'<name>_cta_label'` to
  `defaultSettings()` in `config.php`, add the section to `sectionDefaults()`
  and `$pausableSections` in `includes/under_construction.php` and
  `pages/admin_settings.php`, then call
  `guardSection($siteSettings, '<name>')` after the header include. The admin
  toggle and its fields then appear automatically.

  Both switches ship **on**. Flip them off when the catalogue and the chapel
  are ready and everything returns to normal.

Content lives in the `site_settings` key/value table (created automatically the
first time it is needed). Every key has a built-in default in `defaultSettings()`
in `config.php`, so deleting a row simply falls back to the shipped text —
handy for undoing a change. `home.php` reads it via `$siteSettings`, which
`includes/header.php` loads on every page.

> Custom hero uploads go to `uploads/site/`. `uploads/.htaccess` blocks PHP
> execution in every upload folder — delete it only if your host disables
> `.htaccess` and you have another protection in place.

## Dark mode

Every page has a light and a dark theme. The moon/sun button sits in the top
bar next to *Buy Now*.

- First visit follows the operating system preference, so dark-mode devices get
  dark automatically.
- Once someone taps the button their choice is remembered in `localStorage` and
  wins over the OS setting from then on.
- An inline script in `<head>` applies the stored theme before first paint, so
  the page never flashes light on a dark device.

**How the colours work.** `css/style.css` keeps the raw brand palette
(`--navy`, `--gold`, `--cream`, the greys) and adds a layer of *semantic*
tokens on top: `--bg`, `--surface`, `--text`, `--text-strong`, `--border`,
`--accent`, `--surface-navy`, and so on. Rules use the semantic token that
says what they *mean*, so the `[data-theme="dark"]` block can flip the whole
site by changing values in one place.

Two tokens deliberately do **not** flip: `--ink-on-gold` and `--ink-on-navy`.
Gold and navy are the brand surfaces and stay identical in both themes, so text
sitting on them must not change either.

To add a themed colour, use a semantic token. If nothing fits, add a new token
to **both** the `:root` and the `[data-theme="dark"]` blocks — never hardcode a
hex, because a hardcoded hex will not follow the theme.

## Admin / staff / customer panels

All three signed-in panels share one shell, so they no longer drift apart:

- `includes/panel_sidebar.php` — the sidebar. Each panel declares a
  `$panelMenu` array (groups of items with `label`, `href`, `icon`, optional
  `badge` and `active`) and includes this file. Add a nav item by adding one
  array entry; there is a single copy of the markup to maintain.
- `css/panel.css` — the shared panel styles, loaded only on panels via
  `$panelStyles = true` set *before* `includes/header.php`.
  - `.panel-topbar` — sticky header with breadcrumb, page title and actions
  - `.kpi-grid` / `.kpi` — the counter tiles (use `data-tone="warn|good|bad"`)
  - `.panel-card` + `.panel-card-head` / `.panel-card-body` — section cards
  - `.empty-state` — replaces a bare blank area when a list has nothing in it
  - `.panel-flash` — the confirmation banner

> `css/panel.css` uses `color-mix()` for the sticky header's translucent
> background. That is supported in current browsers; if you need to support
> very old ones, replace it with a plain `var(--bg)`.

## AI assistant

A floating **"Ask Vela"** button appears on every public page (bottom-left, clear
of the red Emergency 24/7 button). Visitors can ask about plans, prices,
payment terms, how claims work, and how the site itself is organised.

Admins control it from **Website Settings → AI Assistant**: an on/off switch,
the assistant's name, the opening message, and an optional "extra guidance" box
for anything that changes often (branch hours, a promotion, a closure). The
base instructions are automatic.

| File | Role |
|---|---|
| `includes/ai_widget.php` | The button, panel and chat JS. Included by `includes/footer.php`. |
| `includes/ai_chat.php` | POST-only endpoint. Calls Gemini from the server, returns JSON. |
| `includes/ai_context.php` | Builds the knowledge base from live data. |
| `includes/ai_cache.php` | Answer cache, quota backoff marker, see below. |
| `css/ai.css` | Widget styles (theme-aware). |
| `config.php` | `GEMINI_API_KEY`, `GEMINI_MODELS`, rate limits. |

**The key never reaches the browser.** `ai_chat.php` holds it server-side; only
the endpoint URL goes into the page. Verified: no page contains the key, and
requesting `config.php` directly returns an empty body because PHP executes it
rather than serving the source.

### How it stays accurate

The knowledge base is rebuilt on every question from the **live** database and
the **current** settings, so the assistant will not quote a stale price or send
someone to buy a plan that is currently paused. If life plans or the chapel are
switched off, it says so and offers the phone line instead.

Only public catalogue data is sent. Customer names, emails, payments and claims
are **never** sent to the model.

### Quota - please read

Gemini's **free tier is very small**: `gemini-3.8-flash` allows about **20
requests per day** for the whole key. That is the real constraint on this
feature, so the code works hard to spend those requests only where they count:

- **Model list.** `GEMINI_MODELS` in `config.php` is tried in order
  (`gemini-3.5-flash-lite`, `gemini-3.1-flash-lite`, `gemini-3.7-flash`). Free
  quota is counted **per model**, so when one bucket empties the request quietly
  moves to the next instead of failing. To see what a key may use, call
  `https://generativelanguage.googleapis.com/v1beta/models?key=YOUR_KEY`.
  (`GEMINI_MODEL` still exists as the alias for the first entry.)
- **Answer cache** in `cache/ai/`, one hour. Repeat questions cost nothing, and
  the cache key includes a fingerprint of the prices and the pause switches, so
  changing either invalidates stale answers automatically. The cache is also
  cleared whenever the admin saves Website Settings.
- **Session cap:** 6 questions per 5 minutes (`AI_RATE_LIMIT` /
  `AI_RATE_WINDOW`), so nobody can drain the quota in one sitting.
- **Local fallback.** If every model returns 429, a marker is written
  (`AI_QUOTA_BACKOFF`, 5 minutes) so the next questions skip the API entirely
  and `ai_local_answer()` in `ai_context.php` answers them from the same live
  data — prices, payment terms, whether enrolment/chapels are open, claims,
  receipts, accounts and contact details. The chat labels these with a
  *"Quick answer - the assistant is at its daily limit"* line, and answers it
  cannot match confidently say so plainly with the phone number rather than
  guessing. This is what keeps the widget useful around the clock.
- If nothing matches **and** the quota is empty, the reply is a plain 503
  pointing at 0917-123-4567.

If the assistant is public and gets real traffic, the daily allowance will not
be enough. Options: upgrade the Gemini plan, restrict the assistant to logged-in
users, or keep it switched off until then.

> **Rotate the API key.** The one in `config.php` was pasted into a chat and is
> therefore exposed in logs. Create a new key in Google AI Studio, replace
> `GEMINI_API_KEY`, and delete the old one.

## Good to know

- **Dark / light mode:** moon/sun button in the top bar. It follows the
  device setting automatically and remembers the visitor's choice.
  See [Dark mode](#dark-mode) for how the theming works.
- **AI assistant:** a floating "Ask Vela" chat button answers questions about
  plans, prices, payments, claims and how the site works. See
  [AI assistant](#ai-assistant).
- **Color palette (single):** Harbor Blue `#2E4A6B` (primary), Dusty Blue
  `#6C8BAA` (secondary), Linen Gold `#BFAE8E` (accent), Soft Linen
  `#F6F4EF` (background), Dark Harbor `#1A2634` (text). Status badges
  use muted sage / subtle bronze / slate gray; the logo only ever sits
  on cream, white, or dark charcoal.
- **No database?** Opening any page before setup shows a "Database Not
  Found" screen with a button that goes straight to Step 1. (Same thing
  happens if MySQL isn't running — start it first.)
- **Photos:** all pictures live in the `img/` folder (free Unsplash photos,
  saved locally) so the site works even without internet. Photo credits:
  Unsplash contributors.
- **Fresh start:** run `Step1.php` again any time. It rebuilds everything
  from zero.

## Troubleshooting

**"Database connection failed" or setup keeps reappearing**
- MySQL is not running. Start it in XAMPP Control Panel.
- If you copied this folder from another PC, just run the wizard again
  (`http://localhost/velasquez-lifeplan/`) — it detects the broken setup
  and reinstalls the database.

**Pages show but have no styling**
- Hard-refresh the browser: `Ctrl + F5`.
- Make sure the folder is named exactly `velasquez-lifeplan`.

**Apache won't start (port 80 in use)**
- Close Skype, IIS, or other web servers, then Start Apache again.

**"Port 3306 in use" for MySQL**
- Another MySQL is running. Stop it, then Start MySQL in XAMPP.

**Contact form / checkout errors**
- The wizard creates every needed table. If you set up the database manually
  instead of using the wizard, some tables may be missing — easiest fix is to
  run the wizard (it recreates everything).

## Reinstalling from scratch
1. Delete `config.php` from this folder (or just visit the wizard — it will
   detect the problem on its own).
2. Optionally drop the `velasquez_lifeplan` database in phpMyAdmin.
3. Visit `http://localhost/velasquez-lifeplan/` and follow the wizard.

## Requirements
- Windows 10/11
- XAMPP with PHP 7.4+ (8.x recommended), Apache, MySQL/MariaDB
- A modern browser (Chrome, Edge, Firefox)

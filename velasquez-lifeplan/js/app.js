/* ============================================
   VELASQUEZ FUNERAL SERVICE - MAIN JAVASCRIPT
   ============================================ */

(function () {
    'use strict';

    /* ----------------------------------------
       NAVBAR SCROLL EFFECT
       ---------------------------------------- */
    var navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    /* ----------------------------------------
       MOBILE MENU TOGGLE
       ---------------------------------------- */
    var hamburger = document.getElementById('hamburger');
    var navMenu = document.getElementById('navMenu');
    var navLinks = document.getElementById('navLinks');
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            var open = navMenu.classList.toggle('open');
            hamburger.classList.toggle('active', open);
            hamburger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        navMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navMenu.classList.remove('open');
                hamburger.classList.remove('active');
                hamburger.setAttribute('aria-expanded', 'false');
            });
        });
    } else if (hamburger && navLinks) {
        hamburger.addEventListener('click', function () {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('open');
        });
    }

    /* ----------------------------------------
       ACTIVE NAV LINK
       ---------------------------------------- */
    (function highlightActiveNav() {
        var current = window.location.pathname.replace(/\/$/, '');
        if (current === '') current = '/';
        document.querySelectorAll('.navbar-links a').forEach(function (a) {
            var href = a.getAttribute('href');
            if (!href || href.charAt(0) === '#') return;
            var linkPath;
            try {
                linkPath = new URL(href, window.location.origin).pathname.replace(/\/$/, '');
            } catch (err) {
                return;
            }
            if (linkPath === current ||
                (linkPath.endsWith('/home.php') && (current === '/' || current.endsWith('/index.php')))) {
                a.classList.add('active');
            }
        });
    })();

    /* ----------------------------------------
       PROFILE DROPDOWN
       ---------------------------------------- */
    var profileBtn = document.getElementById('profileBtn');
    var profileMenu = profileBtn && profileBtn.closest ? profileBtn.closest('.profile-menu') : null;
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = profileMenu.classList.toggle('open');
            profileBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            if (!profileMenu.contains(e.target)) {
                profileMenu.classList.remove('open');
                profileBtn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                profileMenu.classList.remove('open');
                profileBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* ----------------------------------------
       PAGE TRANSITIONS (fade overlay + progress)
       ---------------------------------------- */
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var transitionOverlay = document.getElementById('pageTransition');
    var progressBar = document.getElementById('pageProgress');

    function startTransition(url) {
        if (reduceMotion || !transitionOverlay) {
            window.location.href = url;
            return;
        }
        transitionOverlay.classList.add('active');
        if (progressBar) {
            progressBar.classList.add('active');
            progressBar.style.width = '0';
            requestAnimationFrame(function () {
                progressBar.style.width = '75%';
            });
        }
        setTimeout(function () {
            window.location.href = url;
        }, 280);
    }

    document.addEventListener('click', function (e) {
        var anchor = e.target.closest ? e.target.closest('a') : null;
        if (!anchor) return;
        var href = anchor.getAttribute('href');
        if (!href || href.charAt(0) === '#' || anchor.target === '_blank' || anchor.hasAttribute('download')) return;
        if (/^(tel:|mailto:|javascript:)/i.test(href)) return;
        var url;
        try {
            url = new URL(href, window.location.href);
        } catch (err) {
            return;
        }
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.hash) return;
        e.preventDefault();
        startTransition(url.href);
    });

    /* ----------------------------------------
       SMOOTH SCROLLING FOR ANCHOR LINKS
       ---------------------------------------- */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                var navbarHeight = navbar ? navbar.offsetHeight : 0;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight - 20;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });

    /* ----------------------------------------
       FORM VALIDATION HELPERS
       ---------------------------------------- */
    window.validateField = function (field) {
        var value = field.value.trim();
        var type = field.getAttribute('type');
        var required = field.hasAttribute('required');
        var errorEl = field.parentNode.querySelector('.form-error');
        var isValid = true;
        var message = '';

        if (required && !value) {
            isValid = false;
            message = 'This field is required.';
        } else if (type === 'email' && value) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Please enter a valid email address.';
            }
        } else if (type === 'tel' && value) {
            var phoneRegex = /^[\d\s\-\+()]{7,15}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                message = 'Please enter a valid phone number.';
            }
        } else if (field.tagName === 'SELECT' && required && !value) {
            isValid = false;
            message = 'Please select an option.';
        }

        if (errorEl) {
            errorEl.textContent = message;
        }

        if (isValid) {
            field.style.borderColor = '';
        } else {
            field.style.borderColor = '#DC2626';
        }

        return isValid;
    };

    window.validateForm = function (form) {
        var fields = form.querySelectorAll('[required]');
        var allValid = true;
        fields.forEach(function (field) {
            if (!validateField(field)) {
                allValid = false;
            }
        });
        return allValid;
    };

    document.querySelectorAll('form').forEach(function (form) {
        form.querySelectorAll('[required]').forEach(function (field) {
            field.addEventListener('blur', function () {
                validateField(field);
            });
            field.addEventListener('input', function () {
                if (field.style.borderColor === 'rgb(220, 38, 38)') {
                    validateField(field);
                }
            });
        });
    });

    /* ----------------------------------------
       MODAL OPEN / CLOSE
       ---------------------------------------- */
    window.openModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    document.querySelectorAll('.modal-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var modal = btn.closest('.modal-overlay');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(function (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
    });

    /* ----------------------------------------
       FLASH MESSAGE AUTO-DISMISS
       ---------------------------------------- */
    document.querySelectorAll('.flash-message').forEach(function (msg) {
        setTimeout(function () {
            msg.style.opacity = '0';
            msg.style.transform = 'translateX(100%)';
            msg.style.transition = 'all 0.3s ease';
            setTimeout(function () {
                msg.remove();
            }, 300);
        }, 3000);
    });

    /* ----------------------------------------
       TAB SWITCHING (CASKETS PAGE)
       ---------------------------------------- */
    window.switchTab = function (tabGroupId, tabId) {
        var group = document.getElementById(tabGroupId);
        if (!group) return;

        group.querySelectorAll('.tab-btn').forEach(function (btn) {
            btn.classList.remove('active');
        });

        group.querySelectorAll('.tab-content').forEach(function (content) {
            content.classList.remove('active');
        });

        var activeBtn = group.querySelector('[data-tab="' + tabId + '"]');
        var activeContent = document.getElementById(tabId);
        if (activeBtn) activeBtn.classList.add('active');
        if (activeContent) activeContent.classList.add('active');
    };

    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tabId = btn.getAttribute('data-tab');
            var tabGroup = btn.closest('.tabs');
            if (tabGroup) {
                var groupId = tabGroup.id;
                if (groupId) {
                    switchTab(groupId, tabId);
                } else {
                    tabGroup.querySelectorAll('.tab-btn').forEach(function (b) {
                        b.classList.remove('active');
                    });
                    btn.classList.add('active');
                }
            }
        });
    });

    /* ----------------------------------------
       FAQ ACCORDIONS (JS-enhanced, details fallback)
       ---------------------------------------- */
    document.querySelectorAll('.faq-item').forEach(function (item) {
        var question = item.querySelector('.faq-q');
        var answer = item.querySelector('.faq-a');
        if (!question || !answer) return;
        question.setAttribute('role', 'button');
        question.setAttribute('tabindex', '0');
        question.addEventListener('click', function () {
            var isOpen = item.classList.toggle('open');
            answer.style.display = isOpen ? 'block' : 'none';
            question.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        question.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter' || ev.key === ' ') {
                ev.preventDefault();
                question.click();
            }
        });
    });

    /* ----------------------------------------
       PLAN COMPARISON CHECKBOX LOGIC
       ---------------------------------------- */
    document.querySelectorAll('.plan-compare').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var checked = document.querySelectorAll('.plan-compare:checked');
            if (checked.length > 3) {
                this.checked = false;
                alert('You can compare up to 3 plans at a time.');
            }
            updateCompareButton();
        });
    });

    function updateCompareButton() {
        var checked = document.querySelectorAll('.plan-compare:checked');
        var compareBtn = document.getElementById('compareBtn');
        if (compareBtn) {
            if (checked.length >= 2) {
                compareBtn.style.display = 'inline-flex';
                compareBtn.textContent = 'Compare (' + checked.length + ')';
            } else {
                compareBtn.style.display = 'none';
            }
        }
    }

    /* ----------------------------------------
       CHECKOUT STEP NAVIGATION
       ---------------------------------------- */
    var currentCheckoutStep = 1;

    window.goToCheckoutStep = function (step) {
        var steps = document.querySelectorAll('.checkout-step');
        var panels = document.querySelectorAll('.checkout-panel');

        steps.forEach(function (s, i) {
            s.classList.remove('active', 'completed');
            if (i + 1 < step) {
                s.classList.add('completed');
            } else if (i + 1 === step) {
                s.classList.add('active');
            }
        });

        panels.forEach(function (p, i) {
            p.classList.remove('active');
            if (i + 1 === step) {
                p.classList.add('active');
            }
        });

        currentCheckoutStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    window.nextCheckoutStep = function () {
        if (currentCheckoutStep < 4) {
            goToCheckoutStep(currentCheckoutStep + 1);
        }
    };

    window.prevCheckoutStep = function () {
        if (currentCheckoutStep > 1) {
            goToCheckoutStep(currentCheckoutStep - 1);
        }
    };

    /* ----------------------------------------
       LOGIN / REGISTER FORM SWITCHING
       ---------------------------------------- */
    window.switchAuthForm = function (formType) {
        document.querySelectorAll('.auth-tab').forEach(function (tab) {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.auth-form').forEach(function (form) {
            form.classList.remove('active');
        });

        var activeTab = document.querySelector('.auth-tab[data-type="' + formType + '"]');
        var activeForm = document.getElementById(formType + 'Form');
        if (activeTab) activeTab.classList.add('active');
        if (activeForm) activeForm.classList.add('active');
    };

    document.querySelectorAll('.auth-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            switchAuthForm(tab.getAttribute('data-type'));
        });
    });

    /* ----------------------------------------
       DASHBOARD SIDEBAR TOGGLE
       ---------------------------------------- */
    var sidebar = document.getElementById('sidebar');
    var sidebarToggle = document.getElementById('sidebarToggle');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== sidebarToggle) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* ----------------------------------------
       INTERSECTION OBSERVER - SCROLL ANIMATIONS
       ---------------------------------------- */
    if ('IntersectionObserver' in window) {
        var animElements = document.querySelectorAll('.fade-in, .slide-up');
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -40px 0px'
        });

        animElements.forEach(function (el) {
            observer.observe(el);
        });
    } else {
        document.querySelectorAll('.fade-in, .slide-up').forEach(function (el) {
            el.classList.add('visible');
        });
    }

    /* ----------------------------------------
       FILTER TABS (CASKETS GALLERY)
       ---------------------------------------- */
    document.querySelectorAll('.filter-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var group = tab.closest('.filter-tabs');
            if (group) {
                group.querySelectorAll('.filter-tab').forEach(function (t) {
                    t.classList.remove('active');
                });
            }
            tab.classList.add('active');

            var filter = tab.getAttribute('data-filter');
            var items = document.querySelectorAll('.gallery-item');
            items.forEach(function (item) {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    /* ----------------------------------------
       QUANTITY INCREMENT / DECREMENT
       ---------------------------------------- */
    window.changeQty = function (inputId, delta) {
        var input = document.getElementById(inputId);
        if (input) {
            var val = parseInt(input.value) || 1;
            var min = parseInt(input.getAttribute('min')) || 1;
            var max = parseInt(input.getAttribute('max')) || 99;
            val = Math.min(max, Math.max(min, val + delta));
            input.value = val;
        }
    };

    /* ----------------------------------------
       BACK TO TOP BUTTON
       ---------------------------------------- */
    var backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                backToTop.style.opacity = '1';
                backToTop.style.pointerEvents = 'auto';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.pointerEvents = 'none';
            }
        });

        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ----------------------------------------
       DARK MODE
       The chosen theme is read by an inline script in <head> before first
       paint, so this block only handles toggling and keeps the button icon
       in sync. localStorage is guarded because it throws in some privacy
       modes - there we fall back to per-session state.
       ---------------------------------------- */
    var THEME_KEY = 'vls-theme';

    function readStoredTheme() {
        try {
            return localStorage.getItem(THEME_KEY);
        } catch (e) {
            return null;
        }
    }

    function storeTheme(theme) {
        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (e) { /* ignore - theme still applies for this page view */ }
    }

    function activeTheme() {
        return document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
    }

    function paintToggle(btn) {
        var dark = activeTheme() === 'dark';
        btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
        btn.setAttribute('aria-label', dark ? 'Switch to light theme' : 'Switch to dark theme');
        btn.setAttribute('title', dark ? 'Switch to light theme' : 'Switch to dark theme');
        btn.innerHTML = dark
            ? '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>'
            : '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/></svg>';
    }

    var themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        paintToggle(themeToggle);
        themeToggle.addEventListener('click', function () {
            var next = activeTheme() === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;
            storeTheme(next);
            paintToggle(themeToggle);
        });
    }

    // Follow the OS only while the visitor has never made an explicit choice.
    var stored = readStoredTheme();
    if (!stored && window.matchMedia) {
        var osDark = window.matchMedia('(prefers-color-scheme: dark)');
        var onOsChange = function (e) {
            document.documentElement.dataset.theme = e.matches ? 'dark' : 'light';
            if (themeToggle) paintToggle(themeToggle);
        };
        if (osDark.addEventListener) {
            osDark.addEventListener('change', onOsChange);
        } else if (osDark.addListener) {
            osDark.addListener(onOsChange);
        }
    }

})();

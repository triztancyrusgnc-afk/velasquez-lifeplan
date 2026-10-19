<?php
/**
 * Floating AI assistant widget.
 *
 * Included from includes/footer.php, so it appears on every public page that
 * renders the normal footer (the "under construction" holding pages include
 * the footer too, so they get it as well).
 *
 * Only the endpoint URL and display copy reach the browser - never the API key.
 */

$aiName    = trim($siteSettings['ai_name'] ?? 'Vela') ?: 'Vela';
$aiWelcome = trim($siteSettings['ai_welcome'] ?? '');
if ($aiWelcome === '') {
    $aiWelcome = "Hello. I'm Vela, the Velasquez assistant. Ask me about life plans, prices, how payments and claims work, or how to use this website.";
}
$aiInitial = mb_strtoupper(mb_substr($aiName, 0, 1));
$aiEndpoint = $appBase . '/includes/ai_chat.php';

// Suggestions stay deliberately general so they remain correct whether or not
// the life plan / chapel sections are currently paused. Customers only - no
// internal or staff-facing questions.
$aiSuggestions = [
    'What life plans do you offer?',
    'Can I pay monthly?',
    'How do I file a claim?',
    'How do I get my receipt?',
];
?>
<button type="button" class="ai-launcher" id="aiLauncher" aria-haspopup="dialog" aria-expanded="false" aria-controls="aiPanel">
    <span class="ai-launcher-icon" aria-hidden="true">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.5 8.5 0 01-3.8-.9L3 21l1.9-5A8.4 8.4 0 0112 3a8.4 8.4 0 019 8.5z"/></svg>
    </span>
    <span class="ai-launcher-text">Ask <?= settingText($aiName) ?></span>
</button>

<div class="ai-panel" id="aiPanel" role="dialog" aria-modal="false" aria-label="<?= settingText($aiName) ?> assistant">
    <div class="ai-head">
        <div class="ai-avatar" aria-hidden="true"><?= settingText($aiInitial) ?></div>
        <div class="ai-head-titles">
            <div class="ai-head-name"><?= settingText($aiName) ?></div>
            <div class="ai-head-status"><span class="ai-dot"></span> Usually replies instantly</div>
        </div>
        <button type="button" class="ai-close" id="aiClose" aria-label="Close assistant">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="ai-log" id="aiLog" aria-live="polite">
        <div class="ai-msg ai-msg-bot"><?= settingText($aiWelcome) ?></div>
    </div>

    <div class="ai-suggest" id="aiSuggest">
        <?php foreach ($aiSuggestions as $s): ?>
            <button type="button" data-q="<?= settingText($s) ?>"><?= settingText($s) ?></button>
        <?php endforeach; ?>
    </div>

    <form class="ai-form" id="aiForm" autocomplete="off">
        <textarea class="ai-input" id="aiInput" rows="1" maxlength="1000" placeholder="Ask about plans, prices, claims..." aria-label="Your question"></textarea>
        <button type="submit" class="ai-send" id="aiSend" aria-label="Send question">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
    </form>

    <div class="ai-footnote">Answers are automated and can be wrong. For anything urgent, call 0917-123-4567.</div>
</div>

<script>
(function () {
    'use strict';

    var ENDPOINT = <?= json_encode($aiEndpoint, JSON_UNESCAPED_SLASHES) ?>;
    var launcher = document.getElementById('aiLauncher');
    var panel    = document.getElementById('aiPanel');
    var closeBtn = document.getElementById('aiClose');
    var log      = document.getElementById('aiLog');
    var suggest  = document.getElementById('aiSuggest');
    var form     = document.getElementById('aiForm');
    var input    = document.getElementById('aiInput');
    var sendBtn  = document.getElementById('aiSend');

    if (!launcher || !panel || !log || !form || !input) return;

    var busy = false;

    function openPanel() {
        panel.classList.add('is-open');
        launcher.setAttribute('aria-expanded', 'true');
        input.focus();
    }

    function closePanel() {
        panel.classList.remove('is-open');
        launcher.setAttribute('aria-expanded', 'false');
        launcher.focus();
    }

    launcher.addEventListener('click', function () {
        if (panel.classList.contains('is-open')) { closePanel(); } else { openPanel(); }
    });
    closeBtn.addEventListener('click', closePanel);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && panel.classList.contains('is-open')) { closePanel(); }
    });

    // Grow the composer with its content, up to the CSS max-height.
    input.addEventListener('input', function () {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 110) + 'px';
    });

    function scrollToEnd() {
        log.scrollTop = log.scrollHeight;
    }

    function addMessage(text, cls) {
        var el = document.createElement('div');
        el.className = 'ai-msg ' + cls;
        el.textContent = text;          // textContent, never innerHTML
        log.appendChild(el);
        scrollToEnd();
        return el;
    }

    /*
     * The model answers in Markdown. Everything is HTML-escaped FIRST, and the
     * only tags that can exist in the result are the <strong>/<em> this
     * function adds itself, so assigning the result with innerHTML is safe.
     */
    function formatAnswer(raw) {
        var safe = String(raw)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Strip a leading blockquote marker.
        safe = safe.replace(/^[ \t]*&gt;[ \t]?/gm, '');

        // ATX headings -> a bold line (the message style has no heading scale).
        safe = safe.replace(/^[ \t]*#{1,6}[ \t]*(.+?)[ \t]*#*$/gm, '<strong>$1</strong>');
        // Horizontal rules carry no meaning in a chat bubble.
        safe = safe.replace(/^[ \t]*(?:-{3,}|_{3,}|\*{3,})[ \t]*$/gm, '');

        // **bold** then *italic*.
        safe = safe.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
        safe = safe.replace(/(^|[\s(])\*([^*\n]+)\*/g, '$1<em>$2</em>');

        // Bullet and numbered lines. Bullets become a literal character so the
        // message keeps its existing pre-wrap formatting without nested lists.
        safe = safe.replace(/^[ \t]*[*+-][ \t]+/gm, '• ');
        safe = safe.replace(/^[ \t]*(\d+)\.[ \t]+/gm, '$1. ');

        return safe;
    }

    function addAnswer(raw, opts) {
        opts = opts || {};
        var el = document.createElement('div');
        el.className = 'ai-msg ai-msg-bot';
        el.innerHTML = formatAnswer(raw);
        // Being honest when the answer came from the local responder rather than
        // the model: the visitor gets a useful reply, but knows it is a quick
        // canned one and that calling us will give them a full answer.
        if (opts.fallback) {
            var tag = document.createElement('span');
            tag.className = 'ai-note';
            tag.textContent = 'Quick answer - the assistant is at its daily limit';
            el.appendChild(tag);
        }
        log.appendChild(el);
        scrollToEnd();
        return el;
    }

    function showTyping() {
        var el = document.createElement('div');
        el.className = 'ai-typing';
        el.innerHTML = '<span></span><span></span><span></span>';  // static markup only
        log.appendChild(el);
        scrollToEnd();
        return el;
    }

    function ask(question) {
        if (busy || !question.trim()) return;
        busy = true;
        sendBtn.disabled = true;

        addMessage(question, 'ai-msg-me');
        input.value = '';
        input.style.height = 'auto';
        if (suggest) { suggest.style.display = 'none'; }

        var typing = showTyping();

        fetch(ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question: question })
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
        .then(function (res) {
            typing.remove();
            if (res.ok && res.body && res.body.ok) {
                addAnswer(res.body.answer, { fallback: !!res.body.fallback });
            } else {
                addMessage((res.body && res.body.error) ? res.body.error : 'Something went wrong. Please try again.', 'ai-msg-error');
            }
        })
        .catch(function () {
            typing.remove();
            addMessage('Could not reach the assistant. Please check your connection.', 'ai-msg-error');
        })
        .then(function () {
            busy = false;
            sendBtn.disabled = false;
            input.focus();
            scrollToEnd();
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        ask(input.value);
    });

    if (suggest) {
        suggest.addEventListener('click', function (e) {
            var btn = e.target.closest('button[data-q]');
            if (!btn) return;
            ask(btn.getAttribute('data-q'));
        });
    }
})();
</script>
<?php
// Shared Plan Details modal.
// Each page must define BEFORE including this file:
//   - PLAN_DETAILS (JS object keyed by plan id; values: id,name,category,
//     description,price,spot,m5,m10,down,casket,viewing,features[])
//   - PLAN_CHECKOUT_BASE (e.g. 'checkout.php' or '../checkout.php')
//   - PLAN_COMPARE_LINK  (e.g. 'pages/compare.php' or 'compare.php')
// Open/close animations + overlay/Esc handling come from the global
// .modal-overlay handlers in js/app.js; this file only fills content.
?>
<div class="modal-overlay" id="planModal">
    <div class="modal" style="max-width:580px;">
        <div class="modal-header">
            <div style="text-align:left;">
                <span id="pmBadge" class="badge badge-navy">Plan</span>
                <h3 id="pmName" style="margin:6px 0 0;">Life Plan</h3>
            </div>
            <button class="modal-close" onclick="closePlanModal()" aria-label="Close details">&times;</button>
        </div>
        <div class="modal-body" id="pmBody" style="padding-top:20px;"></div>
        <div class="modal-footer" id="pmFooter" style="flex-direction:column;align-items:stretch;"></div>
    </div>
</div>
<script>
var PLAN_DETAILS = (typeof PLAN_DETAILS !== 'undefined') ? PLAN_DETAILS : {};
var PLAN_CHECKOUT_BASE = (typeof PLAN_CHECKOUT_BASE !== 'undefined') ? PLAN_CHECKOUT_BASE : 'checkout.php';
var PLAN_COMPARE_LINK = (typeof PLAN_COMPARE_LINK !== 'undefined') ? PLAN_COMPARE_LINK : 'pages/compare.php';

function pmPeso(n) {
    if (n === null || n === undefined || n === '' || isNaN(Number(n))) return null;
    return '\u20B1' + Number(n).toLocaleString('en-PH', { maximumFractionDigits: 0 });
}
function pmEsc(s) {
    return String(s === null || s === undefined ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
function pmStatBox(label, value, hero) {
    return '<div style="background:' + (hero ? 'var(--navy)' : 'var(--cream)') + ';border-radius:var(--radius);padding:14px;text-align:center;">' +
        '<div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;color:' + (hero ? 'rgba(255,255,255,0.6)' : 'var(--gray-500)') + ';margin-bottom:4px;">' + pmEsc(label) + '</div>' +
        '<div style="font-weight:700;font-family:var(--font-serif);font-size:1.05rem;color:' + (hero ? 'var(--gold)' : 'var(--navy)') + ';">' + pmEsc(value) + '</div></div>';
}
function openPlanModal(id) {
    var p = PLAN_DETAILS[id];
    if (!p) return;
    var badge = document.getElementById('pmBadge');
    var cat = p.category ? String(p.category) : 'plan';
    badge.textContent = cat.charAt(0).toUpperCase() + cat.slice(1);
    document.getElementById('pmName').textContent = p.name || 'Life Plan';

    var h = '';
    if (p.description) {
        h += '<p class="text-muted" style="margin-top:0;line-height:1.7;">' + pmEsc(p.description) + '</p>';
    }
    h += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:16px 0;">';
    h += pmStatBox('Total Contract Price', pmPeso(p.price) || 'Contact us', true);
    if (p.spot !== null && p.spot !== undefined && p.spot !== '') {
        var save = (Number(p.price) || 0) - (Number(p.spot) || 0);
        h += pmStatBox('Spot Cash' + (save > 0 ? ' (Save ' + pmPeso(save) + ')' : ''), pmPeso(p.spot), false);
    }
    if (p.m5) h += pmStatBox('Monthly \u00B7 5 Years', pmPeso(p.m5) + '/mo', false);
    if (p.m10) h += pmStatBox('Monthly \u00B7 10 Years', pmPeso(p.m10) + '/mo', false);
    h += '</div>';

    var meta = [];
    if (p.down) meta.push(['Downpayment to start', pmPeso(p.down)]);
    if (p.casket) meta.push(['Casket', p.casket]);
    if (p.viewing) meta.push(['Chapel Viewing', p.viewing + ' day(s)']);
    if (meta.length) {
        h += '<div style="background:var(--cream);border-radius:var(--radius);padding:12px 16px;margin-bottom:16px;">';
        meta.forEach(function (m) {
            h += '<div style="display:flex;justify-content:space-between;gap:12px;padding:6px 0;font-size:0.9rem;">' +
                '<span class="text-muted">' + pmEsc(m[0]) + '</span>' +
                '<strong style="color:var(--navy);text-align:right;">' + pmEsc(m[1]) + '</strong></div>';
        });
        h += '</div>';
    }

    if (p.features && p.features.length) {
        h += '<h5 style="margin:0 0 8px;">What\u2019s Included</h5>';
        h += '<ul style="margin:0;padding:0;list-style:none;">';
        p.features.forEach(function (f) {
            h += '<li style="padding:7px 0;border-bottom:1px solid var(--gray-100);font-size:0.9rem;color:var(--gray-600);">\u2713 ' + pmEsc(f) + '</li>';
        });
        h += '</ul>';
    }

    document.getElementById('pmBody').innerHTML = h;
    document.getElementById('pmFooter').innerHTML =
        '<a class="btn btn-gold btn-block btn-lg" href="' + pmEsc(PLAN_CHECKOUT_BASE) + '?plan_id=' + encodeURIComponent(p.id) + '">Proceed to Checkout \u2192</a>' +
        '<a class="btn btn-outline btn-block" href="' + pmEsc(PLAN_COMPARE_LINK) + '">Compare All Plans</a>';

    document.getElementById('planModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closePlanModal() {
    document.getElementById('planModal').classList.remove('active');
    document.body.style.overflow = '';
}
</script>

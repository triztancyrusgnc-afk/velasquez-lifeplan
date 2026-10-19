<?php
$pageTitle = 'Compare Life Plans';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/under_construction.php';
guardPlansSection($siteSettings);

try {
    $stmt = $pdo->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY price ASC");
    $plans = $stmt->fetchAll();
} catch (PDOException $e) {
    $plans = [];
}
?>

<section class="sn-section" style="background:var(--bg);padding:60px 0 40px;">
    <div class="container">
        <div class="sn-band harbor fade-in" style="text-align:center;padding:56px 48px;">
        <span class="sn-eyebrow" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.18);color:#CCB877;">Make an Informed Choice</span>
        <h1 class="sn-title" style="color:var(--ink-on-navy);">Compare <em style="color:#CCB877;">Life Plans</em></h1>
        <p class="sn-sub" style="color:rgba(255,255,255,0.75);max-width:640px;margin:0 auto;">Select up to 3 plans to compare side by side and find the best fit for your needs.</p>
        </div>
    </div>
</section>

<section class="sn-section" style="background:var(--surface);">
    <div class="container">
        <div class="sn-center fade-in">
            <span class="sn-eyebrow">Step One</span>
            <h2 class="sn-title">Select Plans to <em>Compare</em></h2>
            <p class="sn-sub">Choose 2 or 3 plans from the list below to see a detailed comparison.</p>
        </div>

        <div class="fade-in" id="planCheckboxes" style="max-width:900px;margin-left:auto;margin-right:auto;display:grid;gap:16px;">
            <?php foreach ($plans as $i => $plan): ?>
                <label class="sn-card topline" style="cursor:pointer;display:grid;grid-template-columns:1fr auto;gap:16px;text-align:left;align-items:center;padding:24px 28px;" id="label-<?= $plan['id'] ?>">
                    <input type="checkbox" name="compare_plan" value="<?= $plan['id'] ?>" data-name="<?= htmlspecialchars($plan['name']) ?>" data-price="<?= $plan['price'] ?>" style="display:none;" onchange="handlePlanSelect(this)">
                    <div>
                        <span class="sn-chip" style="margin-bottom:8px;text-transform:capitalize;"><?= htmlspecialchars($plan['category']) ?></span>
                        <div style="font-weight:600;color:var(--text-strong);font-size:0.9375rem;"><?= htmlspecialchars($plan['name']) ?></div>
                        <div style="color:var(--accent);font-weight:700;font-family:var(--font-serif);margin-top:4px;">&#8369;<?= number_format($plan['price'], 0) ?></div>
                    </div>
                    <span class="badge badge-gold">Select</span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="text-center mb-4 fade-in">
            <button class="btn btn-gold btn-lg" id="compareBtn" onclick="generateComparison()" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Compare Selected Plans
            </button>
            <p class="text-sm text-muted mt-1" id="selectCount">0 of 3 plans selected</p>
        </div>

        <div id="comparisonResult" style="display:none;">
            <div class="sn-center fade-in" style="margin-top:48px;margin-bottom:24px;">
                <span class="sn-eyebrow">Step Two</span>
                <h2 class="sn-title" style="font-size:1.8rem;">Side-by-Side <em>Results</em></h2>
                <p class="sn-sub">Best values are marked automatically for easy reading.</p>
            </div>
            <div class="sn-card topline" style="padding:0;overflow:hidden;">
                <div class="table-wrapper" style="border:none;border-radius:0;">
                    <table id="comparisonTable">
                        <thead>
                            <tr>
                                <th style="min-width:180px;">Feature</th>
                            </tr>
                        </thead>
                        <tbody id="comparisonBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
var allPlans = <?php echo json_encode($plans); ?>;
var selectedIds = [];

function handlePlanSelect(checkbox) {
    if (checkbox.checked) {
        if (selectedIds.length >= 3) {
            checkbox.checked = false;
            return;
        }
        selectedIds.push(parseInt(checkbox.value));
    } else {
        selectedIds = selectedIds.filter(function(id) { return id !== parseInt(checkbox.value); });
    }
    updateUI();
}

function updateUI() {
    document.getElementById('selectCount').textContent = selectedIds.length + ' of 3 plans selected';
    document.getElementById('compareBtn').disabled = selectedIds.length < 2;

    allPlans.forEach(function(plan) {
        var label = document.getElementById('label-' + plan.id);
        if (!label) return;
        if (selectedIds.indexOf(plan.id) !== -1) {
            label.classList.add('is-selected');
        } else {
            label.classList.remove('is-selected');
        }
    });
}

function generateComparison() {
    var selected = allPlans.filter(function(p) { return selectedIds.indexOf(p.id) !== -1; });
    if (selected.length < 2) return;

    var thead = document.querySelector('#comparisonTable thead tr');
    thead.innerHTML = '<th style="min-width:180px;">Feature</th>';
    selected.forEach(function(plan) {
        var th = document.createElement('th');
        th.style.textAlign = 'center';
        th.innerHTML = '<div style="font-size:1rem;">' + escapeHtml(plan.name) + '</div><div style="font-size:0.75rem;text-transform:capitalize;color:var(--gray-500);font-weight:400;">' + escapeHtml(plan.category) + '</div>';
        thead.appendChild(th);
    });

    var rows = [
        { label: 'Total Price', key: 'price', format: 'currency', best: 'lowest' },
        { label: 'Spot Cash Discount', key: 'spot_cash', format: 'currency', best: 'lowest' },
        { label: 'Monthly (5-Year)', key: 'monthly_5yr', format: 'currency', best: 'lowest' },
        { label: 'Casket Type', key: 'casket_type', format: 'text', best: null },
        { label: 'Viewing Days', key: 'chapel_viewing_days', format: 'number', best: 'highest' },
        { label: 'Hearse Service', key: 'hearse_service', format: 'boolean', best: null },
        { label: 'Flowers Included', key: 'flowers', format: 'boolean', best: null },
        { label: 'Transferable', key: 'transferable', format: 'boolean', best: null },
        { label: 'Assignable', key: 'assignable', format: 'boolean', best: null },
        { label: 'Cash Back', key: 'cash_back', format: 'currency', best: 'highest' },
        { label: 'Insurance Coverage', key: 'insurance_coverage', format: 'currency', best: 'highest' }
    ];

    var tbody = document.getElementById('comparisonBody');
    tbody.innerHTML = '';

    rows.forEach(function(row) {
        var tr = document.createElement('tr');
        var tdLabel = document.createElement('td');
        tdLabel.style.fontWeight = '600';
        tdLabel.style.color = 'var(--navy)';
        tdLabel.textContent = row.label;
        tr.appendChild(tdLabel);

        var bestIdx = -1;
        if (row.best) {
            var bestVal = row.best === 'lowest' ? Infinity : -Infinity;
            selected.forEach(function(plan, idx) {
                var val = parseFloat(plan[row.key]) || 0;
                if (row.best === 'lowest' && val < bestVal) { bestVal = val; bestIdx = idx; }
                if (row.best === 'highest' && val > bestVal) { bestVal = val; bestIdx = idx; }
            });
        }

        selected.forEach(function(plan, idx) {
            var td = document.createElement('td');
            td.style.textAlign = 'center';
            var val = plan[row.key];
            var display = '';

            if (row.format === 'currency') {
                display = '&#8369;' + Number(val).toLocaleString();
            } else if (row.format === 'boolean') {
                display = val == 1
                    ? '<span style="color:var(--green);font-weight:600;">&#10003; Yes</span>'
                    : '<span style="color:var(--red);">&#10007; No</span>';
            } else if (row.format === 'number') {
                display = val + ' days';
            } else {
                display = escapeHtml(val || 'N/A');
            }

            if (bestIdx === idx) {
                td.classList.add('is-best');
                td.innerHTML = display + ' <span class="badge badge-gold" style="font-size:0.6rem;margin-left:4px;">Best</span>';
            } else {
                td.innerHTML = display;
            }
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });

    var actionRow = document.createElement('tr');
    var emptyTd = document.createElement('td');
    emptyTd.style.borderTop = '2px solid var(--gray-200)';
    actionRow.appendChild(emptyTd);

    selected.forEach(function(plan) {
        var td = document.createElement('td');
        td.style.textAlign = 'center';
        td.style.borderTop = '2px solid var(--gray-200)';
        td.innerHTML = '<a href="../checkout.php?plan_id=' + plan.id + '" class="btn btn-gold btn-sm" style="margin-top:8px;">Buy This Plan</a>';
        actionRow.appendChild(td);
    });
    tbody.appendChild(actionRow);

    document.getElementById('comparisonResult').style.display = 'block';
    document.getElementById('comparisonResult').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

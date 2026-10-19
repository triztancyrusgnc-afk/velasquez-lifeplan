<?php
/**
 * Shared sidebar for every signed-in panel (admin, staff, customer, settings).
 *
 * The three panels had drifted into three near-identical copies of this
 * markup. They now all declare a $panelMenu array and render from here, so a
 * change to the shell lands everywhere at once.
 *
 * Expected before include:
 *   $panelMenu = [
 *       ['title' => 'Overview', 'items' => [
 *           ['label' => 'Dashboard', 'href' => '#overview', 'icon' => 'grid',
 *            'active' => true, 'badge' => null],
 *       ]],
 *   ];
 *
 * Optional:
 *   $panelMenuFooter = [ ['label'=>..,'href'=>..,'icon'=>..,'muted'=>true], .. ]
 *   $panelRoleLabel  = 'Administrator'   (default 'Signed in')
 */

if (!function_exists('panel_icon')) {
    /**
     * 18x18 stroke icons, keyed by short name. Keeping them here (rather than
     * inline in each panel) is what stops the sidebars drifting apart.
     */
    function panel_icon($name) {
        $icons = [
            'grid'     => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
            'alert'    => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
            'users'    => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
            'user'     => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'card'     => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
            'file'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
            'box'      => '<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>',
            'chart'    => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
            'cog'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.6a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/>',
            'eye'      => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
            'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            'wallet'   => '<path d="M21 12V7H5a2 2 0 010-4h14v4"/><path d="M3 5v14a2 2 0 002 2h16v-5"/><path d="M18 12a2 2 0 000 4h4v-4h-4z"/>',
            'heart'    => '<path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 00-7.8 7.8l1 1.1L12 21.2l7.8-7.7 1-1.1a5.5 5.5 0 000-7.8z"/>',
            'bell'     => '<path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/>',
            'logout'   => '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
            'clock'    => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            'check'    => '<path d="M20 6L9 17l-5-5"/>',
        ];
        $path = $icons[$name] ?? $icons['grid'];
        return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
    }
}

if (!function_exists('panel_menu_html')) {
    /** Renders one $panelMenu group. */
    function panel_menu_html(array $groups, bool $first = false) {
        $out = '';
        foreach ($groups as $group) {
            $out .= '<div class="sidebar-section-title"' . ($first ? '' : ' style="margin-top:8px;"') . '>'
                . htmlspecialchars($group['title']) . '</div>';
            foreach ($group['items'] as $item) {
                $hasBadge = !empty($item['badge']);
                $out .= '<a href="' . htmlspecialchars($item['href']) . '"'
                    . (!empty($item['active']) ? ' class="sidebar-menu-item active"' : ' class="sidebar-menu-item"')
                    . (!empty($item['muted']) ? ' style="color:rgba(255,255,255,0.4);"' : '')
                    . (!empty($item['title']) ? ' title="' . htmlspecialchars($item['title']) . '"' : '')
                    . '>' . panel_icon($item['icon'] ?? 'grid');
                $out .= '<span>' . htmlspecialchars($item['label']) . '</span>';
                if ($hasBadge) {
                    $out .= '<span class="badge badge-gold sidebar-badge">' . (int)$item['badge'] . '</span>';
                }
                $out .= '</a>';
            }
            $first = false;
        }
        return $out;
    }
}

$panelMenu = $panelMenu ?? [];
$panelMenuFooter = $panelMenuFooter ?? [];
$panelRoleLabel = $panelRoleLabel ?? 'Signed in';
$panelInitial = isset($_SESSION['user_name']) && $_SESSION['user_name'] !== ''
    ? strtoupper(substr($_SESSION['user_name'], 0, 1))
    : '?';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <div class="sidebar-identity">
            <div class="sidebar-avatar" aria-hidden="true"><?= htmlspecialchars($panelInitial) ?></div>
            <div>
                <div class="sidebar-identity-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?></div>
                <div class="sidebar-identity-role"><?= htmlspecialchars($panelRoleLabel) ?></div>
            </div>
        </div>

        <?= panel_menu_html($panelMenu, true) ?>

        <?php if ($panelMenuFooter): ?>
            <?= panel_menu_html([['title' => 'Session', 'items' => $panelMenuFooter]]) ?>
        <?php endif; ?>
    </div>
</aside>
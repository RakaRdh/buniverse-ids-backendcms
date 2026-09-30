<div class="top-action-bar">
    <div>
        <h1 class="page-title">Navbar & Header Settings</h1>
        <div class="page-subtitle">Manage branding logos, navigation links, and CTA button styling with custom section colors.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="submit" form="navbarForm" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Navbar Settings
        </button>
    </div>
</div>

<?php /* Full-Width Sticky Live Navbar Preview at Top */ ?>
<div class="cms-card" style="margin-bottom: 24px; border: 1px solid rgba(56, 189, 248, 0.25); background: linear-gradient(180deg, rgba(7, 44, 64, 0.7) 0%, rgba(1, 10, 45, 0.95) 100%); overflow: hidden;">
    <?php /*
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: rgba(0, 0, 0, 0.3);">
        <span class="card-title-text" style="font-size: 13px; letter-spacing: 0.5px; color: #38bdf8; display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></span>
            LIVE NAVBAR PREVIEW (TOP FIXED BANNER)
        </span>
        <span style="font-size: 11.5px; color: var(--text-muted);">Real-time synchronized header replica</span>
    </div>
    */ ?>
    <div style="padding: 16px 24px; background: rgba(1, 10, 45, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 24px; max-width: 1360px; margin: 0 auto;">
            <?php /* Brand Logos Lockup */ ?>
            <?php 
                $placementsMap = [];
                if (!empty($placements)) {
                    foreach ($placements as $p) {
                        $placementsMap[$p['slot_key']] = $p;
                    }
                }
                $pSlot1 = $placementsMap['navbar_slot_1'] ?? null;
                $pSlot2 = $placementsMap['navbar_slot_2'] ?? null;

                $pSlot1Active = !isset($pSlot1['is_active']) || !empty($pSlot1['is_active']);
                $pSlot2Active = !isset($pSlot2['is_active']) || !empty($pSlot2['is_active']);

                // Resolve Preview Logo 1
                if (!empty($pSlot1['logo_url']) || !empty($pSlot1['logo_white_url'])) {
                    $previewLogoA = ($pSlot1['variant'] === 'color') 
                        ? (!empty($pSlot1['logo_url']) ? base_url($pSlot1['logo_url']) : base_url($pSlot1['logo_white_url']))
                        : (!empty($pSlot1['logo_white_url']) ? base_url($pSlot1['logo_white_url']) : base_url($pSlot1['logo_url']));
                } else {
                    $previewLogoA = base_url('assets/img/logo_ids26_white.webp');
                }

                // Resolve Preview Logo 2
                if (!empty($pSlot2['logo_url']) || !empty($pSlot2['logo_white_url'])) {
                    $previewLogoB = ($pSlot2['variant'] === 'color') 
                        ? (!empty($pSlot2['logo_url']) ? base_url($pSlot2['logo_url']) : base_url($pSlot2['logo_white_url']))
                        : (!empty($pSlot2['logo_white_url']) ? base_url($pSlot2['logo_white_url']) : base_url($pSlot2['logo_url']));
                } else {
                    $previewLogoB = base_url('assets/img/logo_id_white.webp');
                }
            ?>
            <div style="display: flex; align-items: center; gap: 14px;">
                <?php if ($pSlot1Active): ?>
                    <img id="navPreviewLogoA" src="<?= esc($previewLogoA) ?>" alt="Logo A" style="height: 36px; width: auto; object-fit: contain;">
                <?php endif; ?>
                <?php if ($pSlot1Active && $pSlot2Active): ?>
                    <div style="width: 1.5px; height: 26px; background-color: rgba(255, 255, 255, 0.35);"></div>
                <?php endif; ?>
                <?php if ($pSlot2Active): ?>
                    <img id="navPreviewLogoB" src="<?= esc($previewLogoB) ?>" alt="Logo B" style="height: 26px; width: auto; object-fit: contain;">
                <?php endif; ?>
            </div>

            <?php /* Navigation Links & CTA */ ?>
            <div style="display: flex; align-items: center; gap: 24px;">
                <div id="navPreviewMenuLinks" style="display: flex; align-items: center; gap: 20px; font-size: 14px; font-weight: 500; color: #ffffff;">
                    <span style="color: #38bdf8; font-weight: 600;">Home</span>
                    <span style="opacity: 0.85;">About Us</span>
                    <span style="opacity: 0.85;">Schedule</span>
                    <span style="opacity: 0.85;">Gallery</span>
                    <span style="opacity: 0.85;">News</span>
                    <span style="opacity: 0.85;">Sponsor</span>
                </div>
                
                <a href="javascript:void(0)" id="live_cta_preview" class="btn-ticket-preview">
                    <?= esc($settings['cta_label'] ?? 'Get Tickets') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<form id="navbarForm" action="<?= base_url('admin/navbar/update') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="form-grid-2" style="gap: 24px; align-items: start;">
        
        <?php /* Column 1: Branding Logos & Navigation Links */ ?>
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <?php /* Card 1: Brand Logos Centralized Notice & Target URLs */ ?>
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="card-title-text">Brand Logos Management</span>
                    <a href="<?= base_url('admin/master-logos') ?>" class="btn btn-primary btn-sm" style="font-size: 11.5px; padding: 4px 12px;">
                        Manage in Master Logos Hub &rarr;
                    </a>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <div style="display: flex; gap: 14px; background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.25); padding: 14px 16px; border-radius: 8px; margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" style="width: 22px; height: 22px; color: #38bdf8; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <div style="font-size: 12.5px; line-height: 1.5; color: var(--text-primary);">
                            <strong>Terpusat di Master Logos Hub</strong><br>
                            Semua aset gambar logo, varian warna/putih, dan aktivasi slot navbar kini dikelola langsung dan tersinkronisasi di menu <a href="<?= base_url('admin/master-logos') ?>" style="color: #38bdf8; text-decoration: underline; font-weight: 600;">Master Logos</a>.
                        </div>
                    </div>

                    <?php /* Target URLs only */ ?>
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Logo 1 Target URL (Klik Logo Utama)</label>
                        <input type="text" name="logo_a_target_url" class="form-control" value="<?= esc($settings['logo_a_target_url'] ?? '#home') ?>" placeholder="#home or https://...">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Default: <code>#home</code> (scroll ke hero banner)</div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Logo 2 Target URL (Klik Logo Media Partner)</label>
                        <input type="text" name="logo_b_target_url" class="form-control" value="<?= esc($settings['logo_b_target_url'] ?? 'https://investor.id') ?>" placeholder="https://investor.id">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Default: <code>https://investor.id</code> (portal media)</div>
                    </div>
                </div>
            </div>

            <?php /* Card 2: Navigation Links */ ?>
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="card-title-text">Navbar Menu Links</span>
                    <button type="button" class="btn btn-secondary" onclick="addNavItem()" style="padding: 4px 10px; font-size: 12px;">
                        + Add Link
                    </button>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <div id="nav-items-container" style="display: flex; flex-direction: column; gap: 10px;">
                        <?php
                        $navItems = !empty($settings['nav_items_json']) ? json_decode($settings['nav_items_json'], true) : [];
                        if (empty($navItems)) {
                            $navItems = [
                                ['label' => 'Home', 'url' => '#home'],
                                ['label' => 'About Us', 'url' => '#about-us'],
                                ['label' => 'Schedule', 'url' => '#schedule'],
                                ['label' => 'Gallery', 'url' => '#gallery'],
                                ['label' => 'News', 'url' => '#news'],
                                ['label' => 'Sponsor', 'url' => '#sponsor'],
                            ];
                        }
                        foreach ($navItems as $idx => $item):
                        ?>
                            <div class="nav-item-row" style="display: flex; gap: 8px; align-items: center;">
                                <span style="font-size: 11px; color: var(--text-muted); width: 18px; text-align: center;"><?= $idx + 1 ?></span>
                                <input type="text" name="nav_label[]" value="<?= esc($item['label']) ?>" placeholder="Label" class="form-control" style="flex: 1;" required>
                                <input type="text" name="nav_url[]" value="<?= esc($item['url']) ?>" placeholder="#section or URL" class="form-control" style="flex: 1;" required>
                                <button type="button" class="btn btn-danger" onclick="this.closest('.nav-item-row').remove()" style="padding: 6px 10px; font-size: 12px;" title="Remove link">&times;</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <?php /* Column 2: CTA Button & Section Colors & Floating Bar */ ?>
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <?php /* Card 3: CTA Button & Styling */ ?>
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="card-title-text">CTA Ticket Button & Styling</span>
                    <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 600; color: #4ade80;">
                        <input type="checkbox" name="cta_ticket_active" value="1" <?= (!isset($settings['cta_ticket_active']) || !empty($settings['cta_ticket_active'])) ? 'checked' : '' ?> onchange="toggleCtaVisibility(this.checked)">
                        Show Ticket Button
                    </label>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <?php /* CTA Label & URL */ ?>
                    <div class="form-grid-2" style="gap: 14px; margin-bottom: 20px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600;">Button Text / Label</label>
                            <input type="text" id="cta_label_input" name="cta_label" value="<?= esc($settings['cta_label'] ?? 'Get Tickets') ?>" class="form-control" oninput="updateLivePreview()">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600;">Target Ticket URL</label>
                            <input type="text" name="ticket_url" value="<?= esc($settings['ticket_url'] ?? '') ?>" placeholder="https://..." class="form-control">
                        </div>
                    </div>

                    <h3 style="font-size: 13.5px; font-weight: 700; color: var(--text-strong); margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--line);">
                        Normal State Colors
                    </h3>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--navbar-cta-bg]',
                        'fieldId'      => 'color_navbar_cta_bg',
                        'currentValue' => $navbarColors['--navbar-cta-bg'] ?? '#ffffff',
                        'label'        => 'Button Background Color',
                        'palette'      => $palette,
                        'cssProp'      => '--navbar-cta-bg',
                    ]) ?>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--navbar-cta-text]',
                        'fieldId'      => 'color_navbar_cta_text',
                        'currentValue' => $navbarColors['--navbar-cta-text'] ?? '#052431',
                        'label'        => 'Button Text Color',
                        'palette'      => $palette,
                        'cssProp'      => '--navbar-cta-text',
                    ]) ?>

                    <h3 style="font-size: 13.5px; font-weight: 700; color: var(--text-strong); margin-top: 20px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--line);">
                        Hover State Colors
                    </h3>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--navbar-cta-hover-bg]',
                        'fieldId'      => 'color_navbar_cta_hover_bg',
                        'currentValue' => $navbarColors['--navbar-cta-hover-bg'] ?? '#ffffff',
                        'label'        => 'Hover Background Color',
                        'palette'      => $palette,
                        'cssProp'      => '--navbar-cta-hover-bg',
                    ]) ?>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--navbar-cta-hover-text]',
                        'fieldId'      => 'color_navbar_cta_hover_text',
                        'currentValue' => $navbarColors['--navbar-cta-hover-text'] ?? '#041a24',
                        'label'        => 'Hover Text Color',
                        'palette'      => $palette,
                        'cssProp'      => '--navbar-cta-hover-text',
                    ]) ?>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--navbar-cta-hover-outline]',
                        'fieldId'      => 'color_navbar_cta_hover_outline',
                        'currentValue' => $navbarColors['--navbar-cta-hover-outline'] ?? '#ffffff',
                        'label'        => 'Hover Border / Glow Outline Color',
                        'palette'      => $palette,
                        'cssProp'      => '--navbar-cta-hover-outline',
                    ]) ?>

                </div>
            </div>

            <?php /* Card 4: Floating Bottom Bar Settings */ ?>
            <div class="cms-card" style="margin-bottom: 0; border: 1px solid rgba(217, 119, 6, 0.25);">
                <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="card-title-text" style="color: var(--gold); display: flex; align-items: center; gap: 8px;">
                        <svg class="icon" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><rect x="2" y="16" width="20" height="6" rx="2"/><path d="M6 12h12"/></svg>
                        Floating Bottom Bar (Mobile & Desktop)
                    </span>
                    <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 600; color: #4ade80;">
                        <input type="checkbox" name="floating_bar_enabled" value="1" <?= (!isset($settings['floating_bar_enabled']) || !empty($settings['floating_bar_enabled'])) ? 'checked' : '' ?>>
                        Enable Floating Bar
                    </label>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">Typewriter Teaser Text</label>
                        <input type="text" name="floating_bar_text" class="form-control" value="<?= esc($settings['floating_bar_text'] ?? 'Get Your Seat Now!') ?>" placeholder="e.g. Get Your Seat Now!">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Animasi teks ketik berjalan di bagian tengah floating bar.</div>
                    </div>

                    <div class="form-grid-2" style="gap: 14px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600;">Floating Button Label</label>
                            <input type="text" name="floating_bar_btn_label" class="form-control" value="<?= esc($settings['floating_bar_btn_label'] ?? 'Get Tickets') ?>" placeholder="e.g. Get Tickets">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600;">Override URL (Optional)</label>
                            <input type="text" name="floating_bar_btn_url" class="form-control" value="<?= esc($settings['floating_bar_btn_url'] ?? '') ?>" placeholder="Kosongkan jika sama dengan ticket URL">
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div style="margin-top: 24px; padding: 18px 24px; background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-card); display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Navbar Settings
        </button>
    </div>
</form>

<style>
.btn-ticket-preview {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    padding: 10px 24px;
    border-radius: 9999px;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.25s ease, color 0.25s ease, outline 0.25s ease, box-shadow 0.25s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
</style>

<script>
function switchColorMode(fieldId, mode) {
    const pScheme = document.getElementById('panel_scheme_' + fieldId);
    const pCustom = document.getElementById('panel_custom_' + fieldId);
    const bScheme = document.getElementById('btn_mode_scheme_' + fieldId);
    const bCustom = document.getElementById('btn_mode_custom_' + fieldId);

    if (mode === 'scheme') {
        pScheme.style.display = 'flex';
        pCustom.style.display = 'none';
        bScheme.style.background = 'var(--primary-1-6, #0b516c)';
        bScheme.style.color = '#ffffff';
        bCustom.style.background = 'transparent';
        bCustom.style.color = 'var(--text-muted, #64748b)';
    } else {
        pScheme.style.display = 'none';
        pCustom.style.display = 'flex';
        bCustom.style.background = 'var(--primary-1-6, #0b516c)';
        bCustom.style.color = '#ffffff';
        bScheme.style.background = 'transparent';
        bScheme.style.color = 'var(--text-muted, #64748b)';
    }
}

function applySchemeColor(fieldId, val) {
    if (!val) return;
    document.getElementById(fieldId).value = val;
    document.getElementById('swatch_preview_' + fieldId).style.background = val;
    document.getElementById('hex_' + fieldId).value = val;
    if (val.match(/^#[0-9a-fA-F]{3,8}$/)) {
        document.getElementById('picker_' + fieldId).value = val;
    }
    updateLivePreview();
}

function syncFromPicker(fieldId, val) {
    document.getElementById(fieldId).value = val;
    document.getElementById('hex_' + fieldId).value = val;
    document.getElementById('swatch_preview_' + fieldId).style.background = val;
    updateLivePreview();
}

function syncFromHex(fieldId, val) {
    document.getElementById(fieldId).value = val;
    document.getElementById('swatch_preview_' + fieldId).style.background = val;
    if (val.match(/^#[0-9a-fA-F]{3,8}$/)) {
        document.getElementById('picker_' + fieldId).value = val;
    }
    updateLivePreview();
}

function updateLivePreview() {
    const btn = document.getElementById('live_cta_preview');
    const labelInput = document.getElementById('cta_label_input');
    if (!btn) return;

    btn.innerText = (labelInput && labelInput.value.trim()) ? labelInput.value.trim() : 'Get Tickets';

    const bg = document.getElementById('color_navbar_cta_bg').value || '#ffffff';
    const text = document.getElementById('color_navbar_cta_text').value || '#052431';
    const hoverBg = document.getElementById('color_navbar_cta_hover_bg').value || '#ffffff';
    const hoverText = document.getElementById('color_navbar_cta_hover_text').value || '#041a24';
    const hoverOutline = document.getElementById('color_navbar_cta_hover_outline').value || '#ffffff';

    btn.style.backgroundColor = bg;
    btn.style.color = text;
    btn.style.outline = 'none';

    btn.onmouseenter = function() {
        btn.style.backgroundColor = hoverBg;
        btn.style.color = hoverText;
        btn.style.outline = '3px solid ' + hoverOutline;
        btn.style.outlineOffset = '4px';
    };
    btn.onmouseleave = function() {
        btn.style.backgroundColor = bg;
        btn.style.color = text;
        btn.style.outline = 'none';
    };
}

function addNavItem() {
    const container = document.getElementById('nav-items-container');
    const count = container.querySelectorAll('.nav-item-row').length + 1;
    const row = document.createElement('div');
    row.className = 'nav-item-row';
    row.style = 'display: flex; gap: 8px; align-items: center;';
    row.innerHTML = `
        <span style="font-size: 11px; color: var(--text-muted); width: 18px; text-align: center;">${count}</span>
        <input type="text" name="nav_label[]" value="" placeholder="Label" class="form-control" style="flex: 1;" required>
        <input type="text" name="nav_url[]" value="#" placeholder="#section or URL" class="form-control" style="flex: 1;" required>
        <button type="button" class="btn btn-danger" onclick="this.closest('.nav-item-row').remove()" style="padding: 6px 10px; font-size: 12px;" title="Remove link">&times;</button>
    `;
    container.appendChild(row);
}

function toggleCtaVisibility(visible) {
    const btn = document.getElementById('live_cta_preview');
    if (btn) {
        btn.style.display = visible ? 'inline-flex' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateLivePreview();
    const ctaCheckbox = document.querySelector('input[name="cta_ticket_active"]');
    if (ctaCheckbox) {
        toggleCtaVisibility(ctaCheckbox.checked);
    }
});
</script>

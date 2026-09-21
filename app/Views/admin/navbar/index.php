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

<form id="navbarForm" action="<?= base_url('admin/navbar/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="form-grid-2" style="gap: 24px; align-items: start;">
        
        <!-- Column 1: Branding Logos & Navigation Links -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Card 1: Brand Logos -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar">
                    <span class="card-title-text">Brand Logos</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <!-- Logo A -->
                    <div class="form-group" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--line);">
                        <label class="form-label" style="font-weight: 700;">Logo 1 (Summit Event Logo)</label>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 10px;">
                            <div>
                                <label class="form-label" style="font-size: 11.5px;">Color Version (Default)</label>
                                <?php if (!empty($settings['logo_a_url'])): ?>
                                    <div style="margin-bottom: 6px; background: #072c40; padding: 10px; border-radius: 6px; text-align: center;">
                                        <img src="<?= base_url($settings['logo_a_url']) ?>" alt="Logo A Color" style="max-height: 36px; max-width: 100%; display: inline-block;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="logo_a_file" class="form-control" accept="image/*">
                            </div>

                            <div>
                                <label class="form-label" style="font-size: 11.5px;">White Version (Monochrome)</label>
                                <?php if (!empty($settings['logo_a_white'])): ?>
                                    <div style="margin-bottom: 6px; background: #072c40; padding: 10px; border-radius: 6px; text-align: center;">
                                        <img src="<?= base_url($settings['logo_a_white']) ?>" alt="Logo A White" style="max-height: 36px; max-width: 100%; display: inline-block;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="logo_a_white_file" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Logo 1 Target URL (Operan Link)</label>
                            <input type="text" name="logo_a_target_url" class="form-control" value="<?= esc($settings['logo_a_target_url'] ?? '#home') ?>" placeholder="#home or https://...">
                            <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Default: #home (scroll to hero)</div>
                        </div>
                    </div>

                    <!-- Logo B -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 700;">Logo 2 (Investor Daily Indonesia)</label>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 10px;">
                            <div>
                                <label class="form-label" style="font-size: 11.5px;">Color Version (Default)</label>
                                <?php if (!empty($settings['logo_b_url'])): ?>
                                    <div style="margin-bottom: 6px; background: #072c40; padding: 10px; border-radius: 6px; text-align: center;">
                                        <img src="<?= base_url($settings['logo_b_url']) ?>" alt="Logo B Color" style="max-height: 36px; max-width: 100%; display: inline-block;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="logo_b_file" class="form-control" accept="image/*">
                            </div>

                            <div>
                                <label class="form-label" style="font-size: 11.5px;">White Version (Monochrome)</label>
                                <?php if (!empty($settings['logo_b_white'])): ?>
                                    <div style="margin-bottom: 6px; background: #072c40; padding: 10px; border-radius: 6px; text-align: center;">
                                        <img src="<?= base_url($settings['logo_b_white']) ?>" alt="Logo B White" style="max-height: 36px; max-width: 100%; display: inline-block;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="logo_b_white_file" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Logo 2 Target URL (Operan Link)</label>
                            <input type="text" name="logo_b_target_url" class="form-control" value="<?= esc($settings['logo_b_target_url'] ?? 'https://investor.id') ?>" placeholder="https://investor.id">
                            <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Default: https://investor.id (external portal)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Navigation Links -->
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

        <!-- Column 2: CTA Button & Section Colors -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Card 3: CTA Button & Styling -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar">
                    <span class="card-title-text">CTA Ticket Button & Styling</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <!-- CTA Label & URL -->
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

                    <!-- Live CTA Preview Box -->
                    <div style="background: #072c40; border-radius: 8px; padding: 24px; text-align: center; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Live Button Preview (Hover to test)</div>
                        <a href="javascript:void(0)" id="live_cta_preview" class="btn-ticket-preview">
                            <?= esc($settings['cta_label'] ?? 'Get Tickets') ?>
                        </a>
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

document.addEventListener('DOMContentLoaded', updateLivePreview);
</script>

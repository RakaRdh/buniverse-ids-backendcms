<div class="top-action-bar">
    <div>
        <h1 class="page-title">Footer & Social Links Settings</h1>
        <div class="page-subtitle">Configure footer brand logo, copyright notice, social media channels, and section color theme.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="submit" form="footerForm" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Footer Settings
        </button>
    </div>
</div>

<form id="footerForm" action="<?= base_url('admin/footer/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">

        <!-- Column 1: Logo, Copyright & Social Links -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Card 1: Footer Brand & Copyright -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar">
                    <span class="card-title-text">Footer Brand & Copyright</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <!-- Footer Logo -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label" style="font-weight: 600;">Footer Logo (Investor Daily)</label>
                        <?php if (!empty($settings['logo_footer_url'])): ?>
                            <div style="margin-bottom: 10px; background: #072c40; padding: 12px; border-radius: 6px; display: inline-block;">
                                <img src="<?= base_url($settings['logo_footer_url']) ?>" alt="Footer Logo" style="max-height: 38px; display: block;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo_footer_file" class="form-control" accept="image/*">
                        <div class="form-hint" style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px;">Recommended: Transparent PNG or SVG (~180x40px).</div>
                    </div>

                    <!-- Copyright text -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 600;">Copyright Notice</label>
                        <input type="text" 
                               id="copyright_input" 
                               name="copyright_text" 
                               value="<?= esc($settings['copyright_text'] ?? 'Investor Daily Summit.') ?>" 
                               class="form-control"
                               oninput="updateFooterPreview()">
                        <div class="form-hint" style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px;">Example: <code>Investor Daily Summit. All rights reserved.</code></div>
                    </div>
                </div>
            </div>

            <!-- Card 1.5: Contact Support Details -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar">
                    <span class="card-title-text">Contact Information (Left Footer Box)</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">Contact Label</label>
                        <input type="text" name="contact_label" class="form-control" value="<?= esc($settings['contact_label'] ?? 'Phone') ?>" placeholder="e.g. Phone or Call Us">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Header text above the phone number in footer.</div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 600;">Telephone / Hotline Number</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?= esc($settings['contact_phone'] ?? '+62 855-1235-000') ?>" placeholder="e.g. +62 855-1235-000">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Clicking on the icon/number opens tel: prompt.</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Social Media Channels -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar">
                    <span class="card-title-text">Social Media Channels</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">Instagram URL</label>
                        <input type="url" name="instagram_url" value="<?= esc($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/..." class="form-control">
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">YouTube URL</label>
                        <input type="url" name="youtube_url" value="<?= esc($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/..." class="form-control">
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">Facebook URL</label>
                        <input type="url" name="facebook_url" value="<?= esc($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/..." class="form-control">
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="<?= esc($settings['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/..." class="form-control">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 600;">Twitter / X URL</label>
                        <input type="url" name="twitter_url" value="<?= esc($settings['twitter_url'] ?? '') ?>" placeholder="https://x.com/..." class="form-control">
                    </div>
                </div>
            </div>

        </div>

        <!-- Column 2: Footer Colors & Live Preview -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Card 3: Footer Styling & Colors -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar">
                    <span class="card-title-text">Footer Colors & Styling</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    
                    <!-- Live Footer Preview Box -->
                    <div id="footer_preview_box" style="border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 24px; transition: background 0.3s ease;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px; opacity: 0.7;" id="footer_preview_label">
                            Live Footer Preview
                        </div>
                        <div id="footer_preview_inner" style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px;">
                            <div style="font-weight: 700; font-size: 13px; opacity: 0.9;" id="footer_preview_logo">Investor Daily</div>
                            <div style="font-size: 11.5px;" id="footer_preview_copy">
                                &copy; <?= date('Y') ?>. <?= esc($settings['copyright_text'] ?? 'Investor Daily Summit.') ?>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <div id="footer_preview_soc_1" class="soc-btn-preview">IG</div>
                                <div id="footer_preview_soc_2" class="soc-btn-preview">YT</div>
                            </div>
                        </div>
                    </div>

                    <h3 style="font-size: 13.5px; font-weight: 700; color: var(--text-strong); margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--line);">
                        Footer Colors
                    </h3>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--footer-bg]',
                        'fieldId'      => 'color_footer_bg',
                        'currentValue' => $footerColors['--footer-bg'] ?? '#052431',
                        'label'        => 'Footer Background Color',
                        'palette'      => $palette,
                        'cssProp'      => '--footer-bg',
                    ]) ?>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--footer-text-color]',
                        'fieldId'      => 'color_footer_text_color',
                        'currentValue' => $footerColors['--footer-text-color'] ?? '#ffffff',
                        'label'        => 'Copyright & Text Color',
                        'palette'      => $palette,
                        'cssProp'      => '--footer-text-color',
                    ]) ?>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--footer-social-bg]',
                        'fieldId'      => 'color_footer_social_bg',
                        'currentValue' => $footerColors['--footer-social-bg'] ?? '#064057',
                        'label'        => 'Social Icon Background (Circular)',
                        'palette'      => $palette,
                        'cssProp'      => '--footer-social-bg',
                    ]) ?>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--footer-social-color]',
                        'fieldId'      => 'color_footer_social_color',
                        'currentValue' => $footerColors['--footer-social-color'] ?? '#ffffff',
                        'label'        => 'Social Icon Glyph Color',
                        'palette'      => $palette,
                        'cssProp'      => '--footer-social-color',
                    ]) ?>

                    <?= view('admin/partials/color_picker_field', [
                        'fieldName'    => 'colors[--footer-border-color]',
                        'fieldId'      => 'color_footer_border_color',
                        'currentValue' => $footerColors['--footer-border-color'] ?? 'rgba(255, 255, 255, 0.12)',
                        'label'        => 'Footer Top Divider Border Line',
                        'palette'      => $palette,
                        'cssProp'      => '--footer-border-color',
                    ]) ?>

                </div>
            </div>

        </div>

    </div>

    <div style="margin-top: 24px; padding: 18px 24px; background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-card); display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Footer Settings
        </button>
    </div>
</form>

<style>
.soc-btn-preview {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.25s ease, color 0.25s ease;
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
    updateFooterPreview();
}

function syncFromPicker(fieldId, val) {
    document.getElementById(fieldId).value = val;
    document.getElementById('hex_' + fieldId).value = val;
    document.getElementById('swatch_preview_' + fieldId).style.background = val;
    updateFooterPreview();
}

function syncFromHex(fieldId, val) {
    document.getElementById(fieldId).value = val;
    document.getElementById('swatch_preview_' + fieldId).style.background = val;
    if (val.match(/^#[0-9a-fA-F]{3,8}$/)) {
        document.getElementById('picker_' + fieldId).value = val;
    }
    updateFooterPreview();
}

function updateFooterPreview() {
    const box = document.getElementById('footer_preview_box');
    const inner = document.getElementById('footer_preview_inner');
    const copy = document.getElementById('footer_preview_copy');
    const lbl = document.getElementById('footer_preview_label');
    const logo = document.getElementById('footer_preview_logo');
    const copyInput = document.getElementById('copyright_input');
    const soc1 = document.getElementById('footer_preview_soc_1');
    const soc2 = document.getElementById('footer_preview_soc_2');

    if (!box) return;

    const bg = document.getElementById('color_footer_bg').value || '#052431';
    const textColor = document.getElementById('color_footer_text_color').value || '#ffffff';
    const socBg = document.getElementById('color_footer_social_bg').value || '#064057';
    const socColor = document.getElementById('color_footer_social_color').value || '#ffffff';
    const borderTop = document.getElementById('color_footer_border_color').value || 'rgba(255, 255, 255, 0.12)';

    box.style.backgroundColor = bg;
    inner.style.borderTop = '1px solid ' + borderTop;
    copy.style.color = textColor;
    lbl.style.color = textColor;
    logo.style.color = textColor;

    if (copyInput && copyInput.value.trim()) {
        copy.innerText = '© <?= date('Y') ?>. ' + copyInput.value.trim();
    }

    if (soc1) {
        soc1.style.backgroundColor = socBg;
        soc1.style.color = socColor;
    }
    if (soc2) {
        soc2.style.backgroundColor = socBg;
        soc2.style.color = socColor;
    }
}

document.addEventListener('DOMContentLoaded', updateFooterPreview);
</script>

<div class="top-action-bar">
    <div>
        <h1 class="page-title">Footer & Social Links Settings</h1>
        <div class="page-subtitle">Configure footer brand logo, copyright notice, contact channels, social media channels, and section color theme.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="submit" form="footerForm" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Footer Settings
        </button>
    </div>
</div>

<!-- Full-Width Live Footer Preview at Top (Faithful 3-Column Frontend Replica) -->
<div class="cms-card" style="margin-bottom: 24px; border: 1px solid rgba(56, 189, 248, 0.25); background: #052431; overflow: hidden;">
    <!-- <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: rgba(0, 0, 0, 0.3);">
        <span class="card-title-text" style="font-size: 13px; letter-spacing: 0.5px; color: #38bdf8; display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></span>
            LIVE FOOTER PREVIEW (3-COLUMN FRONTEND REPLICA)
        </span>
        <span style="font-size: 11.5px; color: var(--text-muted);">Real-time layout preview</span>
    </div> -->
    <div id="footer_preview_box" style="padding: 36px 32px 32px 32px; background: #052431; transition: background 0.3s ease;">
        <div id="footer_preview_inner" style="display: flex; align-items: center; justify-content: space-between; gap: 24px; max-width: 1360px; margin: 0 auto; border-top: 1px solid rgba(255, 255, 255, 0.12); padding-top: 24px; flex-wrap: wrap;">
            
            <!-- Left: Logo & Social Circles -->
            <div style="flex: 1; min-width: 220px; display: flex; flex-direction: column; align-items: flex-start; gap: 16px;">
                <?php 
                    $placementsMap = [];
                    if (!empty($placements)) {
                        foreach ($placements as $p) {
                            $placementsMap[$p['slot_key']] = $p;
                        }
                    }
                    $pFooter = $placementsMap['footer'] ?? null;
                    $pFooterActive = !isset($pFooter['is_active']) || !empty($pFooter['is_active']);

                    if (!empty($pFooter['logo_white_url']) || !empty($pFooter['logo_url'])) {
                        $footerLogoSrc = ($pFooter['variant'] === 'color')
                            ? (!empty($pFooter['logo_url']) ? base_url($pFooter['logo_url']) : base_url($pFooter['logo_white_url']))
                            : (!empty($pFooter['logo_white_url']) ? base_url($pFooter['logo_white_url']) : base_url($pFooter['logo_url']));
                    } else {
                        $footerLogoSrc = base_url('assets/img/logo_id_white.webp');
                    }
                ?>
                <div style="display: flex; align-items: center; margin: 0; padding: 0;">
                    <?php if ($pFooterActive): ?>
                        <img id="footer_preview_logo_img" src="<?= esc($footerLogoSrc) ?>" alt="Footer Logo" style="height: 38px; width: auto; object-fit: contain; display: block;">
                    <?php endif; ?>
                </div>
                <div style="display: flex; gap: 12px; align-items: center; margin: 0; padding: 0;">
                    <div id="footer_preview_soc_1" class="soc-btn-preview" title="Instagram">
                        <svg viewBox="0 0 24 24" style="width:18px; height:18px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </div>
                    <div id="footer_preview_soc_2" class="soc-btn-preview" title="YouTube">
                        <svg viewBox="0 0 24 24" style="width:18px; height:18px;" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </div>
                    <div id="footer_preview_soc_3" class="soc-btn-preview" title="Facebook">
                        <svg viewBox="0 0 24 24" style="width:18px; height:18px;" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Center: Copyright -->
            <div id="footer_preview_copy" style="flex: 1.5; text-align: center; color: #ffffff; font-size: 13px; font-weight: 500; min-width: 260px; letter-spacing: 0.2px;">
                &copy; Copyright <?= date('Y') ?>. <?= esc($settings['copyright_text'] ?? 'Investor Daily Summit.') ?>
            </div>

            <!-- Right: Contact Us (Matching Frontend Perfectly) -->
            <div style="flex: 1; min-width: 220px; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                <div style="font-size: 16px; font-weight: 700; color: #ffffff; width: 100%; max-width: 220px; text-align: left; letter-spacing: 0.3px;">Contact Us:</div>
                <div style="display: inline-flex; align-items: center; gap: 14px; width: 100%; max-width: 220px;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #1e3347; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; transform: rotate(-10deg);" fill="currentColor">
                            <path d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2z"/>
                        </svg>
                    </div>
                    <div style="display: flex; flex-direction: column; text-align: left; align-items: flex-start;">
                        <span id="footer_preview_contact_label" style="font-size: 14px; font-weight: 500; color: #ffffff; line-height: 1.2;"><?= esc($settings['contact_label'] ?? 'Phone') ?></span>
                        <span id="footer_preview_contact_phone" style="font-size: 15px; font-weight: 700; color: #ffffff; letter-spacing: 0.3px; line-height: 1.3;"><?= esc($settings['contact_phone'] ?? '+62 812-1267-8789') ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="footerForm" action="<?= base_url('admin/footer/update') ?>" method="POST">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">

        <!-- Column 1: Logo, Copyright & Social Links -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Card 1: Footer Brand Notice & Copyright -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="card-title-text">Footer Brand & Copyright</span>
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
                            Logo footer kini dikelola langsung dan tersinkronisasi di slot <strong>Footer</strong> pada menu <a href="<?= base_url('admin/master-logos') ?>" style="color: #38bdf8; text-decoration: underline; font-weight: 600;">Master Logos</a>.
                        </div>
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
                    <span class="card-title-text">Contact Information (Right Footer Box)</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">Contact Label</label>
                        <input type="text" id="contact_label_input" name="contact_label" class="form-control" value="<?= esc($settings['contact_label'] ?? 'Phone') ?>" placeholder="e.g. Phone, Hotline, WhatsApp" oninput="updateFooterPreview()">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Label kecil di atas nomor telepon (default: Phone).</div>
                    </div>
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="font-weight: 600;">Telephone / WhatsApp Hotline Number</label>
                        <input type="text" id="contact_phone_input" name="contact_phone" class="form-control" value="<?= esc($settings['contact_phone'] ?? '+62 812-1267-8789') ?>" placeholder="e.g. +62 812-1267-8789" oninput="updateFooterPreview()">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Nomor resmi yang ditampilkan kepada pengunjung.</div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 600;">Custom Contact Target URL (WhatsApp / External Link)</label>
                        <input type="text" name="contact_url" class="form-control" value="<?= esc($settings['contact_url'] ?? '') ?>" placeholder="e.g. https://wa.me/6281212678789">
                        <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Jika diisi (misal link WhatsApp), klik footer contact akan membuka URL ini di tab baru. Jika kosong, otomatis fallback ke <code>tel:nomor</code>.</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Social Media Channels (Instagram, YouTube, Facebook only) -->
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

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-weight: 600;">TikTok URL</label>
                        <input type="url" name="tiktok_url" value="<?= esc($settings['tiktok_url'] ?? '') ?>" placeholder="https://www.tiktok.com/@..." class="form-control">
                    </div>
                </div>
            </div>

        </div>

        <!-- Column 2: Footer Colors & Styling -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Card 3: Footer Styling & Colors -->
            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar">
                    <span class="card-title-text">Footer Colors & Styling</span>
                </div>
                <div class="cms-card-body" style="padding: 20px;">
                    
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
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.15);
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
    const copyInput = document.getElementById('copyright_input');
    const soc1 = document.getElementById('footer_preview_soc_1');
    const soc2 = document.getElementById('footer_preview_soc_2');
    const soc3 = document.getElementById('footer_preview_soc_3');
    const contactLabel = document.getElementById('footer_preview_contact_label');
    const contactPhone = document.getElementById('footer_preview_contact_phone');
    const contactLabelInput = document.getElementById('contact_label_input');
    const contactPhoneInput = document.getElementById('contact_phone_input');

    if (!box) return;

    const bg = document.getElementById('color_footer_bg').value || '#052431';
    const textColor = document.getElementById('color_footer_text_color').value || '#ffffff';
    const socBg = document.getElementById('color_footer_social_bg').value || '#064057';
    const socColor = document.getElementById('color_footer_social_color').value || '#ffffff';
    const borderTop = document.getElementById('color_footer_border_color').value || 'rgba(255, 255, 255, 0.12)';

    box.style.backgroundColor = bg;
    inner.style.borderTop = '1px solid ' + borderTop;
    if (copy) copy.style.color = textColor;

    if (copyInput && copyInput.value.trim() && copy) {
        copy.innerText = '© Copyright <?= date('Y') ?>. ' + copyInput.value.trim();
    }

    [soc1, soc2, soc3].forEach(el => {
        if (el) {
            el.style.backgroundColor = socBg;
            el.style.color = socColor;
        }
    });

    if (contactLabel && contactLabelInput) {
        contactLabel.innerText = contactLabelInput.value.trim() || 'Phone';
    }
    if (contactPhone && contactPhoneInput) {
        contactPhone.innerText = contactPhoneInput.value.trim() || '+62 812-1267-8789';
    }
}

document.addEventListener('DOMContentLoaded', updateFooterPreview);
</script>

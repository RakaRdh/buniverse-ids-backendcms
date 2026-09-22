<div class="top-action-bar">
    <div>
        <h1 class="page-title">Master Logos & Placements</h1>
        <div class="page-subtitle">Centralized brand asset library and placement assignment across website sections.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="button" class="btn btn-primary" onclick="openNewLogoModal()">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Upload New Logo
        </button>
    </div>
</div>

<!-- Tabs Navigation -->
<div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">
    <button type="button" id="tabBtnPlacements" onclick="switchTab('placements')" class="btn btn-sm" style="font-weight: 700; background: var(--primary-1-6, #0b516c); color: #fff;">
        Section Placements
    </button>
    <button type="button" id="tabBtnLibrary" onclick="switchTab('library')" class="btn btn-sm btn-secondary" style="font-weight: 600;">
        Logo Assets Library (<?= count($logos) ?>)
    </button>
</div>

<!-- TAB 1: SECTION PLACEMENTS -->
<div id="tabContentPlacements">
    <form action="<?= base_url('admin/master-logos/placements/save') ?>" method="POST">
        <?= csrf_field() ?>

        <div style="display: flex; flex-direction: column; gap: 18px;">
            <?php foreach ($slots as $slotKey => $slotDef): ?>
                <?php 
                    $current = $placements[$slotKey] ?? [];
                    $selectedLogoId = (int)($current['logo_id'] ?? 0);
                    $selectedVariant = $current['variant'] ?? 'auto';
                    $targetUrl = $current['target_url'] ?? $slotDef['default_url'];
                    $isActive = !isset($current['is_active']) || !empty($current['is_active']);
                ?>
                <div class="cms-card" style="margin-bottom: 0;">
                    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="card-title-text"><?= esc($slotDef['label']) ?></span>
                            <span class="badge" style="margin-left: 8px; font-size: 11px; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); padding: 2px 8px; border-radius: 4px;">
                                <?= esc($slotDef['section']) ?>
                            </span>
                        </div>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: var(--text-strong); margin-bottom: 0;">
                            <input type="checkbox" name="slot_active[<?= esc($slotKey) ?>]" value="1" <?= $isActive ? 'checked' : '' ?>>
                            Active on Site
                        </label>
                    </div>

                    <div class="cms-card-body" style="padding: 20px;">
                        <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 0; margin-bottom: 16px;">
                            <?= esc($slotDef['description']) ?>
                        </p>

                        <div style="display: grid; grid-template-columns: 2fr 1fr 2fr; gap: 16px; align-items: end;">
                            <!-- Choose Logo from Library -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-weight: 600; font-size: 12px;">Assigned Logo Asset</label>
                                <select name="slot_logo[<?= esc($slotKey) ?>]" class="form-control" style="font-size: 13px;" onchange="previewSlotSelection('<?= esc($slotKey) ?>', this)">
                                    <option value="">-- Inherit System Default / Event Settings --</option>
                                    <?php foreach ($logos as $lg): ?>
                                        <option value="<?= esc($lg['id']) ?>" 
                                                data-color="<?= esc(base_url($lg['logo_url'])) ?>" 
                                                data-white="<?= !empty($lg['logo_white_url']) ? esc(base_url($lg['logo_white_url'])) : '' ?>"
                                                <?= ($selectedLogoId === (int)$lg['id']) ? 'selected' : '' ?>>
                                            <?= esc($lg['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Variant to use -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-weight: 600; font-size: 12px;">Color Variant</label>
                                <select name="slot_variant[<?= esc($slotKey) ?>]" class="form-control" style="font-size: 13px;">
                                    <option value="auto" <?= $selectedVariant === 'auto' ? 'selected' : '' ?>>Auto (Both Color & White)</option>
                                    <option value="color" <?= $selectedVariant === 'color' ? 'selected' : '' ?>>Color Only</option>
                                    <option value="white" <?= $selectedVariant === 'white' ? 'selected' : '' ?>>White Monochrome Only</option>
                                </select>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-weight: 600; font-size: 12px;">Target Redirect URL (Link)</label>
                                <input type="text" name="slot_url[<?= esc($slotKey) ?>]" class="form-control" value="<?= esc($targetUrl) ?>" placeholder="<?= esc($slotDef['default_url']) ?>" style="font-size: 13px;">
                            </div>
                        </div>

                        <!-- Live preview thumbnail strip -->
                        <div id="slotPreview_<?= esc($slotKey) ?>" style="margin-top: 14px; background: #072c40; padding: 10px 16px; border-radius: 6px; display: flex; gap: 20px; align-items: center;">
                            <span style="font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase;">Live Slot Preview:</span>
                            <div id="slotPreviewThumb_<?= esc($slotKey) ?>" style="display: flex; gap: 14px; align-items: center;">
                                <span style="font-size: 12px; color: rgba(255,255,255,0.8);">Using default system fallback asset</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 24px; padding: 18px 24px; background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-card); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save All Section Placements
            </button>
        </div>
    </form>
</div>

<!-- TAB 2: LOGO ASSETS LIBRARY -->
<div id="tabContentLibrary" style="display: none;">
    <div class="cms-card">
        <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
            <span class="card-title-text">Registered Brand Logo Assets (<?= count($logos) ?>)</span>
            <button type="button" class="btn btn-sm btn-primary" onclick="openNewLogoModal()">+ Add New Logo</button>
        </div>
        <div class="cms-card-body" style="padding: 20px;">
            <?php if (empty($logos)): ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <p style="margin-bottom: 12px;">No logos uploaded to repository yet.</p>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openNewLogoModal()">Upload First Logo</button>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($logos as $lg): ?>
                        <div style="border: 1px solid var(--line); border-radius: 8px; padding: 16px; background: var(--bg-page); display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="font-weight: 700; font-size: 14px; color: var(--text-strong); margin-bottom: 12px;"><?= esc($lg['name']) ?></div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; text-align: center;">
                                        <span style="display: block; font-size: 10px; color: #64748b; margin-bottom: 4px; font-weight: 600;">COLOR</span>
                                        <img src="<?= esc(base_url($lg['logo_url'])) ?>" alt="Color" style="max-height: 38px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <div style="background: #072c40; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 10px; text-align: center;">
                                        <span style="display: block; font-size: 10px; color: rgba(255,255,255,0.6); margin-bottom: 4px; font-weight: 600;">WHITE</span>
                                        <?php if (!empty($lg['logo_white_url'])): ?>
                                            <img src="<?= esc(base_url($lg['logo_white_url'])) ?>" alt="White" style="max-height: 38px; max-width: 100%; object-fit: contain;">
                                        <?php else: ?>
                                            <span style="font-size: 11px; color: rgba(255,255,255,0.4); line-height: 38px;">None</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div style="font-size: 11.5px; color: var(--text-muted); margin-bottom: 14px;">
                                    Default URL: <code><?= esc($lg['default_target_url'] ?: '#') ?></code>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid var(--line); padding-top: 12px;">
                                <button type="button" class="btn btn-sm btn-secondary" onclick='editLogo(<?= json_encode($lg) ?>)'>Edit</button>
                                <a href="<?= base_url('admin/master-logos/delete/' . $lg['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this logo from library?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Dialog: Add / Edit Logo -->
<div id="logoModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border-radius: 12px; width: 100%; max-width: 520px; border: 1px solid var(--line); box-shadow: 0 20px 40px rgba(0,0,0,0.4); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="logoModalTitle" style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text-strong);">Upload Logo Asset</h3>
            <button type="button" onclick="closeLogoModal()" style="border: none; background: transparent; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="<?= base_url('admin/master-logos/save') ?>" method="POST" enctype="multipart/form-data" style="padding: 20px;">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="modalLogoId" value="">

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600;">Logo Identifier / Brand Name</label>
                <input type="text" name="name" id="modalLogoName" class="form-control" placeholder="e.g. Investor Daily Indonesia" required>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600;">Color Logo Image (Default)</label>
                <input type="file" name="logo_file" id="modalLogoFile" class="form-control" accept="image/*">
                <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Transparent PNG or SVG recommended.</div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600;">White Monochrome Logo Image (Optional)</label>
                <input type="file" name="logo_white_file" id="modalLogoWhiteFile" class="form-control" accept="image/*">
                <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Used for dark theme headers & footers.</div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="font-weight: 600;">Default Target URL</label>
                <input type="text" name="default_target_url" id="modalLogoTargetUrl" class="form-control" placeholder="e.g. https://investor.id or #home">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeLogoModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Logo</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    const cPlacements = document.getElementById('tabContentPlacements');
    const cLibrary = document.getElementById('tabContentLibrary');
    const bPlacements = document.getElementById('tabBtnPlacements');
    const bLibrary = document.getElementById('tabBtnLibrary');

    if (tab === 'placements') {
        cPlacements.style.display = 'block';
        cLibrary.style.display = 'none';
        bPlacements.style.background = 'var(--primary-1-6, #0b516c)';
        bPlacements.style.color = '#fff';
        bLibrary.style.background = 'transparent';
        bLibrary.style.color = 'var(--text-strong)';
    } else {
        cPlacements.style.display = 'none';
        cLibrary.style.display = 'block';
        bLibrary.style.background = 'var(--primary-1-6, #0b516c)';
        bLibrary.style.color = '#fff';
        bPlacements.style.background = 'transparent';
        bPlacements.style.color = 'var(--text-strong)';
    }
}

function openNewLogoModal() {
    document.getElementById('logoModalTitle').innerText = 'Upload New Logo Asset';
    document.getElementById('modalLogoId').value = '';
    document.getElementById('modalLogoName').value = '';
    document.getElementById('modalLogoTargetUrl').value = '';
    document.getElementById('modalLogoFile').required = true;
    document.getElementById('logoModal').style.display = 'flex';
}

function closeLogoModal() {
    document.getElementById('logoModal').style.display = 'none';
}

function editLogo(data) {
    document.getElementById('logoModalTitle').innerText = 'Edit Logo Asset #' + data.id;
    document.getElementById('modalLogoId').value = data.id;
    document.getElementById('modalLogoName').value = data.name || '';
    document.getElementById('modalLogoTargetUrl').value = data.default_target_url || '';
    document.getElementById('modalLogoFile').required = false;
    document.getElementById('logoModal').style.display = 'flex';
}

function previewSlotSelection(slotKey, selectEl) {
    const selectedOpt = selectEl.options[selectEl.selectedIndex];
    const thumbWrap = document.getElementById('slotPreviewThumb_' + slotKey);
    if (!thumbWrap) return;

    if (!selectEl.value) {
        thumbWrap.innerHTML = '<span style="font-size: 12px; color: rgba(255,255,255,0.8);">Using default system fallback asset</span>';
        return;
    }

    const colorUrl = selectedOpt.getAttribute('data-color');
    const whiteUrl = selectedOpt.getAttribute('data-white');

    let html = '';
    if (colorUrl) {
        html += `<div style="background:#ffffff; border: 1px solid #cbd5e1; padding:6px 12px; border-radius:6px; display:inline-flex; align-items:center; gap:6px;">
                    <span style="font-size:9.5px; font-weight:700; color:#475569; text-transform:uppercase;">Color:</span>
                    <img src="${colorUrl}" alt="Color" style="max-height:28px; max-width:140px; object-fit:contain;">
                 </div>`;
    }
    if (whiteUrl) {
        html += `<div style="background:#072c40; border:1px solid rgba(255,255,255,0.25); padding:6px 12px; border-radius:6px; display:inline-flex; align-items:center; gap:6px;">
                    <span style="font-size:9.5px; font-weight:700; color:rgba(255,255,255,0.6); text-transform:uppercase;">White:</span>
                    <img src="${whiteUrl}" alt="White" style="max-height:28px; max-width:140px; object-fit:contain;">
                 </div>`;
    }
    thumbWrap.innerHTML = html;
}

// Initialize preview thumbnails on load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select[name^="slot_logo"]').forEach(sel => {
        const slotKey = sel.name.replace('slot_logo[', '').replace(']', '');
        previewSlotSelection(slotKey, sel);
    });
});
</script>

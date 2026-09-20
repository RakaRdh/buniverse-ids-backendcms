<div class="page-header">
    <div>
        <h1 class="page-title">Section Decorations (Cakram / Disc)</h1>
        <p class="page-subtitle">Configure floating decorative PNG elements (cakram) across frontend sections for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
    <div style="display:flex; gap:10px;">
        <button type="button" onclick="openAddModal()" class="btn btn-gold">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Decoration
        </button>
    </div>
</div>

<div class="cms-card">
    <div class="card-title-bar" style="display:flex; justify-content:space-between; align-items:center;">
        <span class="card-title-text">Active Section Decorations (<?= count($decorations) ?>)</span>
    </div>
    <div class="cms-card-body" style="padding:0;">
        <?php if (!empty($decorations)): ?>
            <div style="overflow-x:auto;">
                <table class="data-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); text-align:left; font-size:12px; color:var(--text-muted); text-transform:uppercase;">
                            <th style="padding:14px 16px;">Preview</th>
                            <th style="padding:14px 16px;">Target Section</th>
                            <th style="padding:14px 16px;">Mode & Position</th>
                            <th style="padding:14px 16px;">Dimensions & Style</th>
                            <th style="padding:14px 16px;">Rotation</th>
                            <th style="padding:14px 16px;">Mobile</th>
                            <th style="padding:14px 16px;">Status</th>
                            <th style="padding:14px 16px; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($decorations as $d): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); font-size:13px;">
                                <td style="padding:12px 16px;">
                                    <div style="width:48px; height:48px; background:rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.1); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                        <img src="<?= base_url(esc($d['image_url'])) ?>" alt="disc" style="max-width:40px; max-height:40px; object-fit:contain; opacity:<?= esc($d['opacity']) ?>;">
                                    </div>
                                </td>
                                <td style="padding:12px 16px; font-weight:600; color:var(--text-strong);">
                                    <?= esc($sections[$d['section_key']] ?? $d['section_key']) ?>
                                    <div style="font-size:11px; color:var(--text-muted); font-weight:normal;">#<?= esc($d['section_key']) ?></div>
                                </td>
                                <td style="padding:12px 16px;">
                                    <?php if ($d['layout_mode'] === 'manual'): ?>
                                        <span class="status-pill" style="background:rgba(59,130,246,0.15); color:#60a5fa; border:1px solid rgba(59,130,246,0.3);">
                                            Manual: <?= esc(ucwords(str_replace('-', ' ', $d['position']))) ?>
                                        </span>
                                        <?php if (($d['edge_offset_mode'] ?? 'fixed') === 'custom'): ?>
                                            <span style="font-size:11px; color:var(--gold); display:block; margin-top:3px;">Offset: <?= esc($d['edge_offset_percent'] ?? 50) ?>%</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-pill" style="background:rgba(168,85,247,0.15); color:#c084fc; border:1px solid rgba(168,85,247,0.3);">
                                            Auto (<?= esc($d['auto_count']) ?>x zigzag, <?= esc($d['auto_gap_px']) ?><?= esc($d['auto_gap_unit'] ?? 'px') ?>, <?= ucfirst($d['auto_start_side'] ?? 'left') ?>)
                                        </span>
                                        <?php if (($d['edge_offset_mode'] ?? 'fixed') === 'custom'): ?>
                                            <span style="font-size:11px; color:var(--gold); display:block; margin-top:3px;">Offset: <?= esc($d['edge_offset_percent'] ?? 50) ?>%</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 16px; color:var(--text-muted);">
                                    <div>Width: <strong style="color:var(--text-strong);"><?= esc($d['size_px']) ?>px</strong></div>
                                    <div style="font-size:11px;">Opacity: <?= (int)($d['opacity'] * 100) ?>%</div>
                                </td>
                                <td style="padding:12px 16px;">
                                    <?php if (!empty($d['is_rotated'])): ?>
                                        <span style="display:inline-flex; align-items:center; gap:4px; font-size:12px; color:#34d399;">
                                            <svg class="icon" style="width:14px; height:14px;" viewBox="0 0 24 24"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                            <?= $d['rotation_direction'] === 'counter-clockwise' ? 'CCW' : 'CW' ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="font-size:12px; color:var(--text-muted);">Static</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 16px;">
                                    <?php if (!empty($d['show_on_mobile'])): ?>
                                        <span style="font-size:11px; color:#60a5fa;">Visible</span>
                                    <?php else: ?>
                                        <span style="font-size:11px; color:var(--text-muted);">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 16px;">
                                    <form action="<?= base_url('admin/decorations/toggle-active/' . $d['id']) ?>" method="POST" style="margin:0;">
                                        <?= csrf_field() ?>
                                        <?php if (!empty($d['is_active'])): ?>
                                            <button type="submit" class="status-pill status-live" style="cursor:pointer; border:none; background:rgba(34,197,94,0.15); color:#4ade80;">Active</button>
                                        <?php else: ?>
                                            <button type="submit" class="status-pill status-draft" style="cursor:pointer; border:none; background:rgba(148,163,184,0.15); color:#94a3b8;">Inactive</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td style="padding:12px 16px; text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <button type="button" class="btn btn-outline btn-sm" onclick='openEditModal(<?= json_encode($d) ?>)' style="padding:4px 8px; font-size:12px;">Edit</button>
                                        <form action="<?= base_url('admin/decorations/delete/' . $d['id']) ?>" method="POST" onsubmit="return confirm('Delete this decoration?');" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger-outline btn-sm" style="padding:4px 8px; font-size:12px; color:#ff8a80; border-color:rgba(255,138,128,0.3);">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="padding:48px; text-align:center; color:var(--text-muted);">
                <p style="margin-bottom:12px;">No section decorations created yet.</p>
                <button type="button" onclick="openAddModal()" class="btn btn-gold btn-sm">Add Your First Decoration</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Add / Edit Decoration -->
<div id="decorationModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.7); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:var(--bg-surface); width:540px; max-width:95vw; max-height:90vh; overflow-y:auto; border-radius:var(--radius-lg); padding:24px; border:1px solid var(--line); box-shadow:0 20px 40px rgba(0,0,0,0.5);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--line); padding-bottom:12px;">
            <h3 id="modalTitle" style="font-size:16px; font-weight:700; color:var(--text-strong); margin:0;">Add Section Decoration</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:18px;">&times;</button>
        </div>

        <form action="<?= base_url('admin/decorations/save') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="decId" value="">

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-weight:600; color:var(--text-strong);">Target Frontend Section *</label>
                <select name="section_key" id="decSectionKey" class="form-control" required>
                    <?php foreach ($sections as $key => $label): ?>
                        <option value="<?= esc($key) ?>"><?= esc($label) ?> (#<?= esc($key) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-weight:600; color:var(--text-strong);">Layout Mode *</label>
                <div style="display:flex; gap:16px; margin-top:6px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--text-strong);">
                        <input type="radio" name="layout_mode" value="manual" id="modeManual" checked onchange="toggleLayoutMode('manual')">
                        Manual Position (Corner / Edge)
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--text-strong);">
                        <input type="radio" name="layout_mode" value="auto" id="modeAuto" onchange="toggleLayoutMode('auto')">
                        Auto Layout (Zigzag Alternating)
                    </label>
                </div>
            </div>

            <!-- Manual Position Selector -->
            <div id="manualFields" class="form-group" style="margin-bottom:14px; padding:12px; background:rgba(0,0,0,0.25); border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.06);">
                <label class="form-label" style="font-weight:600; color:var(--text-strong);">Position on Section Edge *</label>
                <select name="position" id="decPosition" class="form-control">
                    <option value="top-left">Top - Left</option>
                    <option value="top-right">Top - Right</option>
                    <option value="middle-left" selected>Middle - Left</option>
                    <option value="middle-right">Middle - Right</option>
                    <option value="bottom-left">Bottom - Left</option>
                    <option value="bottom-right">Bottom - Right</option>
                </select>
            </div>

            <!-- Auto Layout Config -->
            <div id="autoFields" style="display:none; margin-bottom:14px; padding:12px; background:rgba(0,0,0,0.25); border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.06);">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label" style="font-weight:600; color:var(--text-strong);">Zigzag Quantity</label>
                        <input type="number" name="auto_count" id="decAutoCount" class="form-control" value="2" min="1" max="10">
                        <small style="color:var(--text-muted);">Number of discs along section</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label" style="font-weight:600; color:var(--text-strong);">Vertical Gap</label>
                        <div style="display:flex; gap:6px;">
                            <input type="number" name="auto_gap_px" id="decAutoGap" class="form-control" value="120" min="1" max="1000" style="flex:1;">
                            <select name="auto_gap_unit" id="decAutoGapUnit" class="form-control" style="width:90px;">
                                <option value="px">px</option>
                                <option value="vh">vh</option>
                                <option value="rem">rem</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                        <small style="color:var(--text-muted);">Spacing between discs</small>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-6 form-group">
                        <label class="form-label" style="font-weight:600; color:var(--text-strong);">Zigzag Start Side</label>
                        <select name="auto_start_side" id="decAutoStartSide" class="form-control">
                            <option value="left">Left (Top-Left first)</option>
                            <option value="right">Right (Top-Right first)</option>
                        </select>
                        <small style="color:var(--text-muted);">Side for the first disc</small>
                    </div>
                </div>
            </div>

            <!-- Edge Offset / Tuck Settings -->
            <div class="form-group" style="margin-bottom:14px; padding:12px; background:rgba(0,0,0,0.25); border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.06);">
                <label class="form-label" style="font-weight:600; color:var(--text-strong); display:block; margin-bottom:6px;">Edge Tuck / Offset</label>
                <div style="display:flex; gap:16px;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--text-strong);">
                        <input type="radio" name="edge_offset_mode" value="fixed" id="edgeFixed" checked onchange="toggleEdgeOffset('fixed')">
                        Fixed (-50% standard tuck)
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--text-strong);">
                        <input type="radio" name="edge_offset_mode" value="custom" id="edgeCustom" onchange="toggleEdgeOffset('custom')">
                        Custom Offset %
                    </label>
                </div>
                <div id="customOffsetWrap" style="display:none; margin-top:10px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <input type="range" name="edge_offset_percent" id="decEdgeOffset" min="0" max="100" value="50" oninput="document.getElementById('edgeOffsetVal').innerText = this.value + '%'" style="flex:1;">
                        <span id="edgeOffsetVal" style="font-weight:700; color:var(--gold); font-size:13px; min-width:40px;">50%</span>
                    </div>
                    <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:4px;">0% = inside section, 50% = half-tucked outside, 100% = fully outside.</small>
                </div>
            </div>

            <!-- Image Selection -->
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-weight:600; color:var(--text-strong);">Decoration Image</label>
                <div style="display:flex; gap:12px; align-items:center;">
                    <div style="width:52px; height:52px; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                        <img id="modalPreviewImg" src="<?= base_url('assets/img/disc.png') ?>" alt="disc" style="max-width:44px; max-height:44px; object-fit:contain;">
                    </div>
                    <div style="flex:1;">
                        <input type="file" name="image_file" class="form-control" accept="image/*" onchange="previewModalImage(this)">
                        <small style="color:var(--text-muted); display:block; margin-top:4px;">Leave empty to use default <code>assets/img/disc.png</code></small>
                    </div>
                </div>
            </div>

            <!-- Size & Opacity -->
            <div class="row" style="margin-bottom:14px;">
                <div class="col-md-6 form-group">
                    <label class="form-label" style="font-weight:600; color:var(--text-strong);">Size (Width px)</label>
                    <input type="number" name="size_px" id="decSizePx" class="form-control" value="220" min="50" max="1000">
                    <small style="color:var(--text-muted);">Height scales proportionally</small>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" style="font-weight:600; color:var(--text-strong);">Opacity (0.1 - 1.0)</label>
                    <input type="number" name="opacity" id="decOpacity" class="form-control" value="1.00" step="0.05" min="0.1" max="1.0">
                    <small style="color:var(--text-muted);">1.00 is fully opaque</small>
                </div>
            </div>

            <!-- Rotation Settings -->
            <div class="form-group" style="margin-bottom:14px; padding:12px; background:rgba(0,0,0,0.25); border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.06);">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600; color:var(--text-strong); font-size:13px;">
                    <input type="checkbox" name="is_rotated" id="decIsRotated" value="1" onchange="toggleRotationFields(this.checked)">
                    Enable Continuous Rotation Animation
                </label>
                <div id="rotationDirContainer" style="display:none; margin-top:10px;">
                    <label class="form-label" style="font-size:12px; color:var(--text-muted); margin-bottom:4px; display:block;">Rotation Direction</label>
                    <select name="rotation_direction" id="decRotationDirection" class="form-control">
                        <option value="clockwise">Clockwise (Searah jarum jam)</option>
                        <option value="counter-clockwise">Counter-Clockwise (Berlawanan arah jarum jam)</option>
                    </select>
                </div>
            </div>

            <!-- Mobile & Active Flags -->
            <div style="display:flex; gap:20px; margin-bottom:20px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:var(--text-strong);">
                    <input type="checkbox" name="show_on_mobile" id="decShowOnMobile" value="1">
                    Show on Mobile Devices
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:var(--text-strong);">
                    <input type="checkbox" name="is_active" id="decIsActive" value="1" checked>
                    Active
                </label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:16px;">
                <button type="button" onclick="closeModal()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-gold">Save Decoration</button>
            </div>
        </form>
    </div>
</div>

<script>
const defaultDiscUrl = "<?= base_url('assets/img/disc.png') ?>";

function toggleLayoutMode(mode) {
    const manualFields = document.getElementById('manualFields');
    const autoFields = document.getElementById('autoFields');
    if (mode === 'auto') {
        manualFields.style.display = 'none';
        autoFields.style.display = 'block';
    } else {
        manualFields.style.display = 'block';
        autoFields.style.display = 'none';
    }
}

function toggleRotationFields(enabled) {
    const dir = document.getElementById('rotationDirContainer');
    dir.style.display = enabled ? 'block' : 'none';
}

function previewModalImage(input) {
    const preview = document.getElementById('modalPreviewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleEdgeOffset(mode) {
    const customWrap = document.getElementById('customOffsetWrap');
    customWrap.style.display = mode === 'custom' ? 'block' : 'none';
}

function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Add Section Decoration';
    document.getElementById('decId').value = '';
    document.getElementById('decSectionKey').value = 'gallery';
    document.getElementById('modeManual').checked = true;
    toggleLayoutMode('manual');
    document.getElementById('decPosition').value = 'middle-left';
    document.getElementById('decAutoCount').value = '2';
    document.getElementById('decAutoGap').value = '120';
    document.getElementById('decAutoGapUnit').value = 'px';
    document.getElementById('decAutoStartSide').value = 'left';
    document.getElementById('edgeFixed').checked = true;
    toggleEdgeOffset('fixed');
    document.getElementById('decEdgeOffset').value = '50';
    document.getElementById('edgeOffsetVal').innerText = '50%';
    document.getElementById('decSizePx').value = '220';
    document.getElementById('decOpacity').value = '1.00';
    document.getElementById('decIsRotated').checked = false;
    toggleRotationFields(false);
    document.getElementById('decRotationDirection').value = 'clockwise';
    document.getElementById('decShowOnMobile').checked = false;
    document.getElementById('decIsActive').checked = true;
    document.getElementById('modalPreviewImg').src = defaultDiscUrl;

    document.getElementById('decorationModal').style.display = 'flex';
}

function openEditModal(data) {
    document.getElementById('modalTitle').innerText = 'Edit Section Decoration';
    document.getElementById('decId').value = data.id;
    document.getElementById('decSectionKey').value = data.section_key;

    if (data.layout_mode === 'auto') {
        document.getElementById('modeAuto').checked = true;
        toggleLayoutMode('auto');
    } else {
        document.getElementById('modeManual').checked = true;
        toggleLayoutMode('manual');
    }

    if (data.position) {
        document.getElementById('decPosition').value = data.position;
    }
    document.getElementById('decAutoCount').value = data.auto_count || 1;
    document.getElementById('decAutoGap').value = data.auto_gap_px || 100;
    document.getElementById('decAutoGapUnit').value = data.auto_gap_unit || 'px';
    document.getElementById('decAutoStartSide').value = data.auto_start_side || 'left';

    const isCustomOffset = (data.edge_offset_mode === 'custom');
    if (isCustomOffset) {
        document.getElementById('edgeCustom').checked = true;
        toggleEdgeOffset('custom');
    } else {
        document.getElementById('edgeFixed').checked = true;
        toggleEdgeOffset('fixed');
    }
    const offsetVal = data.edge_offset_percent !== undefined && data.edge_offset_percent !== null ? data.edge_offset_percent : 50;
    document.getElementById('decEdgeOffset').value = offsetVal;
    document.getElementById('edgeOffsetVal').innerText = offsetVal + '%';

    document.getElementById('decSizePx').value = data.size_px || 200;
    document.getElementById('decOpacity').value = data.opacity || '1.00';

    const isRot = parseInt(data.is_rotated) === 1;
    document.getElementById('decIsRotated').checked = isRot;
    toggleRotationFields(isRot);
    document.getElementById('decRotationDirection').value = data.rotation_direction || 'clockwise';

    document.getElementById('decShowOnMobile').checked = parseInt(data.show_on_mobile) === 1;
    document.getElementById('decIsActive').checked = parseInt(data.is_active) === 1;

    let imgUrl = data.image_url;
    if (imgUrl && !imgUrl.startsWith('http')) {
        imgUrl = "<?= base_url() ?>" + '/' + imgUrl;
    }
    document.getElementById('modalPreviewImg').src = imgUrl || defaultDiscUrl;

    document.getElementById('decorationModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('decorationModal').style.display = 'none';
}
</script>

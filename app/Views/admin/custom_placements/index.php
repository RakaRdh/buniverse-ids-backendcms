<div class="page-container" style="max-width:1400px; margin:0 auto; padding:24px 20px;">
    
    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                <span class="badge" style="background:rgba(217,119,6,0.15); color:var(--gold); border:1px solid rgba(217,119,6,0.3); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; padding:2px 8px; border-radius:4px;">Freeform Coordinates</span>
                <span style="color:var(--text-muted); font-size:13px;">Precision Layout</span>
            </div>
            <h1 style="font-size:24px; font-weight:800; color:var(--text-strong); margin:0; letter-spacing:-0.02em;">Custom Placement Engine</h1>
            <p style="color:var(--text-muted); margin-top:4px; font-size:14px; max-width:680px;">
                Drag and drop the decorative cakram disc directly on the live section preview canvas to coordinate its exact position with pixel-perfect percentage coordinates.
            </p>
        </div>
        <div>
            <button type="button" onclick="startNewPlacement()" class="btn btn-gold" style="display:inline-flex; align-items:center; gap:8px;">
                <svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                <span>New Custom Placement</span>
            </button>
        </div>
    </div>

    <!-- Interactive Visual Editor Section -->
    <div class="card" style="margin-bottom:32px; border:1px solid rgba(217,119,6,0.25); background:linear-gradient(180deg, rgba(30,41,59,0.7) 0%, rgba(15,23,42,0.85) 100%); box-shadow:0 12px 32px rgba(0,0,0,0.4);">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-bottom:1px solid rgba(255,255,255,0.08); padding:16px 20px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:32px; height:32px; border-radius:8px; background:rgba(217,119,6,0.2); display:flex; align-items:center; justify-content:center; color:var(--gold);">
                    <svg class="icon" style="width:18px; height:18px;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v3m0 14v3M2 12h3m14 0h3"></path></svg>
                </div>
                <div>
                    <h2 id="editorHeading" style="font-size:16px; font-weight:700; color:var(--text-strong); margin:0;">Visual Coordinate Studio</h2>
                    <span id="editorSubheading" style="font-size:12px; color:var(--text-muted);">Click anywhere on the preview canvas or drag the disc marker</span>
                </div>
            </div>

            <!-- Section Selector for Preview Canvas -->
            <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:12px; color:var(--text-muted); font-weight:600;">Target Section:</label>
                <select id="previewSectionSelect" onchange="changeActiveSection(this.value)" class="form-control" style="width:auto; min-width:200px; padding:6px 12px; font-size:13px; font-weight:600;">
                    <?php foreach ($sections as $key => $label): ?>
                        <option value="<?= esc($key) ?>"><?= esc($label) ?> (#<?= esc($key) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card-body" style="padding:20px;">
            <div class="row" style="display:flex; flex-wrap:wrap; margin:-12px;">
                
                <!-- Left: Interactive Canvas -->
                <div class="col-lg-7" style="flex:1 1 560px; min-width:320px; padding:12px;">
                    <div style="position:relative; width:100%; height:440px; background:#070b13; border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.1); overflow:hidden; box-shadow:inset 0 2px 10px rgba(0,0,0,0.6);" id="canvasContainer">
                        
                        <!-- Grid overlay for visual guideline -->
                        <div style="position:absolute; inset:0; pointer-events:none; background-image:radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px); background-size:24px 24px; z-index:1;"></div>
                        
                        <!-- Section Badge in Canvas -->
                        <div style="position:absolute; top:12px; left:12px; z-index:10; pointer-events:none; background:rgba(15,23,42,0.85); border:1px solid rgba(255,255,255,0.15); border-radius:6px; padding:4px 10px; font-size:11px; font-weight:700; color:var(--gold); backdrop-filter:blur(6px); display:flex; align-items:center; gap:6px;">
                            <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#22c55e;"></span>
                            <span id="canvasSectionLabel">Gallery (#gallery)</span>
                        </div>

                        <!-- Live Coordinate HUD -->
                        <div style="position:absolute; top:12px; right:12px; z-index:10; pointer-events:none; background:rgba(15,23,42,0.85); border:1px solid rgba(217,119,6,0.3); border-radius:6px; padding:4px 10px; font-size:12px; font-weight:700; color:#fff; backdrop-filter:blur(6px); font-family:monospace;">
                            X: <span id="hudX" style="color:var(--gold);">50.0%</span> | Y: <span id="hudY" style="color:var(--gold);">50.0%</span>
                        </div>

                        <!-- Realistic Simulated Frontend Section Background -->
                        <div id="simulatedSectionBg" style="position:absolute; inset:0; padding:48px 24px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; pointer-events:none; z-index:2; opacity:0.35;">
                            <div id="simulatedSectionTitle" style="font-size:26px; font-weight:800; color:#fff; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:8px;">GALLERY MOMENTS</div>
                            <div style="width:60px; height:3px; background:var(--gold); margin-bottom:16px;"></div>
                            <p id="simulatedSectionDesc" style="color:#94a3b8; font-size:13px; max-width:440px;">Interactive canvas representation for freeform decorative placement positioning.</p>
                        </div>

                        <!-- Scaled Frontend Iframe (if reachable) -->
                        <iframe id="fePreviewIframe" src="<?= esc($frontendUrl) ?>/#gallery" style="position:absolute; top:0; left:0; width:1440px; height:900px; transform-origin:0 0; transform:scale(0.48); border:none; pointer-events:none; z-index:3; opacity:0.25;" onerror="this.style.display='none'"></iframe>

                        <!-- Crosshair guide lines -->
                        <div id="crosshairH" style="position:absolute; left:0; right:0; top:50%; height:1px; background:rgba(217,119,6,0.3); pointer-events:none; z-index:5;"></div>
                        <div id="crosshairV" style="position:absolute; top:0; bottom:0; left:50%; width:1px; background:rgba(217,119,6,0.3); pointer-events:none; z-index:5;"></div>

                        <!-- Draggable & Interactive Disc Element Marker -->
                        <div id="dragMarker" style="position:absolute; left:50%; top:50%; transform:translate(-50%, -50%); cursor:grab; z-index:20; touch-action:none; user-select:none; filter:drop-shadow(0 0 16px rgba(217,119,6,0.6));">
                            <img id="markerImg" src="<?= base_url('assets/img/disc.png') ?>" alt="" style="width:110px; height:auto; display:block; pointer-events:none;">
                            <div style="position:absolute; inset:0; border:2px dashed var(--gold); border-radius:50%; pointer-events:none;"></div>
                            <div style="position:absolute; bottom:-20px; left:50%; transform:translateX(-50%); background:var(--gold); color:#000; font-size:10px; font-weight:800; padding:1px 6px; border-radius:4px; white-space:nowrap; pointer-events:none;">DRAG ME</div>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; font-size:12px; color:var(--text-muted);">
                        <span>💡 Tip: Click anywhere on the box or drag the disc directly to adjust.</span>
                        <button type="button" onclick="centerMarker()" class="btn btn-outline btn-sm" style="padding:2px 8px; font-size:11px;">Center Disc (50%, 50%)</button>
                    </div>
                </div>

                <!-- Right: Property Controls Form -->
                <div class="col-lg-5" style="flex:1 1 380px; min-width:300px; padding:12px;">
                    <form action="<?= base_url('admin/custom-placements/save') ?>" method="POST" enctype="multipart/form-data" id="placementForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" id="formId" value="">
                        <input type="hidden" name="section_key" id="formSectionKey" value="gallery">

                        <div class="form-group" style="margin-bottom:12px;">
                            <label class="form-label" style="font-weight:600; color:var(--text-strong); font-size:13px;">Placement Label / Name</label>
                            <input type="text" name="name" id="formName" class="form-control" placeholder="e.g. Gallery Center Highlight" style="font-size:13px;">
                            <small style="color:var(--text-muted); font-size:11px;">Optional identifier for CMS reference</small>
                        </div>

                        <!-- Coordinate Inputs (Synchronized with Canvas) -->
                        <div class="row" style="margin-bottom:12px;">
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:700; color:var(--gold); font-size:12px;">Horizontal Position X (%)</label>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <input type="number" name="pos_x_percent" id="formPosX" class="form-control" value="50.0" step="0.1" min="0" max="100" required oninput="updateMarkerFromInputs()">
                                    <span style="font-weight:700; color:var(--text-muted); font-size:13px;">%</span>
                                </div>
                                <small style="color:var(--text-muted); font-size:11px;">0% (Left) to 100% (Right)</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:700; color:var(--gold); font-size:12px;">Vertical Position Y (%)</label>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <input type="number" name="pos_y_percent" id="formPosY" class="form-control" value="50.0" step="0.1" min="0" max="100" required oninput="updateMarkerFromInputs()">
                                    <span style="font-weight:700; color:var(--text-muted); font-size:13px;">%</span>
                                </div>
                                <small style="color:var(--text-muted); font-size:11px;">0% (Top) to 100% (Bottom)</small>
                            </div>
                        </div>

                        <!-- Image File / Custom Image -->
                        <div class="form-group" style="margin-bottom:12px;">
                            <label class="form-label" style="font-weight:600; color:var(--text-strong); font-size:13px;">Disc Image Asset</label>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <div style="width:44px; height:44px; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                                    <img id="formPreviewThumb" src="<?= base_url('assets/img/disc.png') ?>" alt="disc" style="max-width:36px; max-height:36px; object-fit:contain;">
                                </div>
                                <div style="flex:1;">
                                    <input type="file" name="image_file" class="form-control" accept="image/*" onchange="previewUploadImage(this)" style="font-size:12px;">
                                    <small style="color:var(--text-muted); font-size:11px;">Default: <code>disc.png</code></small>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Color Tinting via Mask-Image -->
                        <div class="form-group" style="margin-bottom:12px; padding:10px 12px; background:rgba(0,0,0,0.2); border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.06);">
                            <label class="form-label" style="font-weight:600; color:var(--text-strong); font-size:12px; display:flex; justify-content:space-between;">
                                <span>Color Tinting (CSS mask)</span>
                                <span style="font-size:10px; color:var(--gold);">Optional</span>
                            </label>
                            <div style="display:flex; gap:8px; align-items:center; margin-top:4px;">
                                <input type="color" id="formTintColorPicker" value="#c5a880" oninput="document.getElementById('formTintColor').value = this.value" style="width:34px; height:32px; padding:2px; border-radius:4px; border:1px solid var(--line); background:none; cursor:pointer;">
                                <input type="text" name="tint_color" id="formTintColor" class="form-control" placeholder="e.g. #ff5733" style="font-size:12px;" oninput="if(this.value.startsWith('#') && this.value.length===7) document.getElementById('formTintColorPicker').value = this.value">
                                <button type="button" class="btn btn-sm btn-secondary" style="padding:4px 8px; font-size:11px;" onclick="document.getElementById('formTintColor').value='';">Clear</button>
                            </div>
                            <small style="color:var(--text-muted); font-size:10.5px; display:block; margin-top:4px;">
                                Applies color over <code>decoration_white.png</code> mask.
                            </small>
                        </div>

                        <!-- Size & Opacity -->
                        <div class="row" style="margin-bottom:12px;">
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:600; color:var(--text-strong); font-size:12px;">Size (Width px)</label>
                                <input type="number" name="size_px" id="formSizePx" class="form-control" value="220" min="50" max="1000" oninput="updateMarkerStyles()">
                                <small style="color:var(--text-muted); font-size:11px;">Rendered width</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:600; color:var(--text-strong); font-size:12px;">Opacity (0.1 - 1.0)</label>
                                <input type="number" name="opacity" id="formOpacity" class="form-control" value="1.00" step="0.05" min="0.1" max="1.0" oninput="updateMarkerStyles()">
                                <small style="color:var(--text-muted); font-size:11px;">1.00 = solid</small>
                            </div>
                        </div>

                        <!-- Rotation & Layering -->
                        <div style="padding:10px 12px; background:rgba(0,0,0,0.2); border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.06); margin-bottom:12px;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600; color:var(--text-strong); font-size:12px;">
                                <input type="checkbox" name="is_rotated" id="formIsRotated" value="1" onchange="toggleFormRotation(this.checked)">
                                Continuous Rotation Animation
                            </label>
                            <div id="formRotationDirWrap" style="display:none; margin-top:8px;">
                                <select name="rotation_direction" id="formRotationDir" class="form-control" style="font-size:12px;" onchange="updateMarkerStyles()">
                                    <option value="clockwise">Clockwise (Searah Jarum Jam)</option>
                                    <option value="counter-clockwise">Counter-Clockwise (Berlawanan Jarum Jam)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Mobile, Active & Z-Index -->
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; color:var(--text-strong);">
                                <input type="checkbox" name="show_on_mobile" id="formShowOnMobile" value="1">
                                Visible on Mobile
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; color:var(--text-strong);">
                                <input type="checkbox" name="is_active" id="formIsActive" value="1" checked>
                                Active
                            </label>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <label style="font-size:12px; color:var(--text-muted);">Z-Index:</label>
                                <input type="number" name="z_index" id="formZIndex" class="form-control" value="1" min="0" max="99" style="width:60px; padding:4px 8px; font-size:12px;">
                            </div>
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid rgba(255,255,255,0.08); padding-top:14px;">
                            <button type="button" onclick="startNewPlacement()" class="btn btn-outline btn-sm">Reset</button>
                            <button type="submit" class="btn btn-gold" id="btnSubmit">Save Placement</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Existing Custom Placements Table -->
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:16px; font-weight:700; color:var(--text-strong); margin:0;">Active Custom Placements (<?= count($placements) ?>)</h2>
        </div>
        
        <?php if (!empty($placements)): ?>
            <div class="table-responsive">
                <table class="table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--line); text-align:left; font-size:12px; color:var(--text-muted); text-transform:uppercase;">
                            <th style="padding:12px 16px;">Disc</th>
                            <th style="padding:12px 16px;">Label / Name</th>
                            <th style="padding:12px 16px;">Section</th>
                            <th style="padding:12px 16px;">Coordinates (X, Y)</th>
                            <th style="padding:12px 16px;">Size / Opacity</th>
                            <th style="padding:12px 16px;">Animation</th>
                            <th style="padding:12px 16px;">Mobile</th>
                            <th style="padding:12px 16px;">Status</th>
                            <th style="padding:12px 16px; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($placements as $p): ?>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04); font-size:13px;">
                                <td style="padding:12px 16px;">
                                    <div style="width:40px; height:40px; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); border-radius:6px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                        <img src="<?= esc(base_url($p['image_url'])) ?>" alt="" style="max-width:32px; max-height:32px; object-fit:contain;">
                                    </div>
                                </td>
                                <td style="padding:12px 16px; font-weight:600; color:var(--text-strong);">
                                    <?= esc($p['name'] ?: 'Custom Placement #' . $p['id']) ?>
                                </td>
                                <td style="padding:12px 16px;">
                                    <span class="badge" style="background:rgba(59,130,246,0.15); color:#60a5fa; border:1px solid rgba(59,130,246,0.3); font-size:11px; padding:3px 8px; border-radius:4px;">
                                        <?= esc($sections[$p['section_key']] ?? $p['section_key']) ?>
                                    </span>
                                </td>
                                <td style="padding:12px 16px; font-family:monospace; color:var(--gold); font-weight:700;">
                                    X: <?= esc($p['pos_x_percent']) ?>%, Y: <?= esc($p['pos_y_percent']) ?>%
                                </td>
                                <td style="padding:12px 16px; color:var(--text-muted);">
                                    <div><?= esc($p['size_px']) ?>px</div>
                                    <div style="font-size:11px;"><?= (int)($p['opacity'] * 100) ?>% opacity</div>
                                </td>
                                <td style="padding:12px 16px;">
                                    <?php if (!empty($p['is_rotated'])): ?>
                                        <span style="color:#34d399; font-size:12px; display:inline-flex; align-items:center; gap:4px;">
                                            <svg class="icon" style="width:12px; height:12px;" viewBox="0 0 24 24"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                            <?= $p['rotation_direction'] === 'counter-clockwise' ? 'CCW' : 'CW' ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted); font-size:12px;">Static</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 16px;">
                                    <?php if (!empty($p['show_on_mobile'])): ?>
                                        <span style="color:#60a5fa; font-size:11px;">Yes</span>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted); font-size:11px;">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 16px;">
                                    <form action="<?= base_url('admin/custom-placements/toggle/' . $p['id']) ?>" method="POST" style="margin:0;">
                                        <?= csrf_field() ?>
                                        <?php if (!empty($p['is_active'])): ?>
                                            <button type="submit" class="status-pill status-live" style="cursor:pointer; border:none; background:rgba(34,197,94,0.15); color:#4ade80;">Active</button>
                                        <?php else: ?>
                                            <button type="submit" class="status-pill status-draft" style="cursor:pointer; border:none; background:rgba(148,163,184,0.15); color:#94a3b8;">Inactive</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td style="padding:12px 16px; text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <button type="button" class="btn btn-outline btn-sm" onclick='loadPlacementIntoStudio(<?= json_encode($p) ?>)' style="padding:4px 8px; font-size:12px;">
                                            Edit in Studio
                                        </button>
                                        <form action="<?= base_url('admin/custom-placements/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Delete this custom placement?');" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger-outline btn-sm" style="padding:4px 8px; font-size:12px; color:#ff8a80; border-color:rgba(255,138,128,0.3);">
                                                Delete
                                            </button>
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
                <p style="margin-bottom:12px;">No custom placements created yet.</p>
                <button type="button" onclick="startNewPlacement()" class="btn btn-gold btn-sm">Create Your First Placement</button>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Interactive Studio JavaScript -->
<script>
const defaultDiscUrl = "<?= base_url('assets/img/disc.png') ?>";
const feBaseUrl = "<?= esc($frontendUrl) ?>";

const sectionLabels = {
    'home': 'Hero / Home (#home)',
    'about-us': 'About Us / Overview (#about-us)',
    'schedule': 'Schedule (#schedule)',
    'gallery': 'Gallery (#gallery)',
    'news': 'Related News (#news)',
    'sponsor': 'Sponsors & Partnerships (#sponsor)'
};

let currentX = 50.0;
let currentY = 50.0;
let isDragging = false;

const canvasContainer = document.getElementById('canvasContainer');
const dragMarker = document.getElementById('dragMarker');
const hudX = document.getElementById('hudX');
const hudY = document.getElementById('hudY');
const crosshairH = document.getElementById('crosshairH');
const crosshairV = document.getElementById('crosshairV');

function updateMarkerPosition(xPct, yPct) {
    currentX = Math.max(0, Math.min(100, parseFloat(xPct.toFixed(1))));
    currentY = Math.max(0, Math.min(100, parseFloat(yPct.toFixed(1))));

    dragMarker.style.left = currentX + '%';
    dragMarker.style.top = currentY + '%';

    crosshairH.style.top = currentY + '%';
    crosshairV.style.left = currentX + '%';

    hudX.innerText = currentX.toFixed(1) + '%';
    hudY.innerText = currentY.toFixed(1) + '%';

    document.getElementById('formPosX').value = currentX.toFixed(1);
    document.getElementById('formPosY').value = currentY.toFixed(1);
}

function updateMarkerFromInputs() {
    const x = parseFloat(document.getElementById('formPosX').value) || 0;
    const y = parseFloat(document.getElementById('formPosY').value) || 0;
    updateMarkerPosition(x, y);
}

function centerMarker() {
    updateMarkerPosition(50.0, 50.0);
}

function changeActiveSection(secKey) {
    document.getElementById('previewSectionSelect').value = secKey;
    document.getElementById('formSectionKey').value = secKey;
    document.getElementById('canvasSectionLabel').innerText = sectionLabels[secKey] || secKey;

    // Update simulated text
    document.getElementById('simulatedSectionTitle').innerText = (sectionLabels[secKey] || secKey).split('(')[0].trim();

    // Update iframe source
    const iframe = document.getElementById('fePreviewIframe');
    if (iframe) {
        iframe.src = feBaseUrl + '/#' + secKey;
    }
}

function updateMarkerStyles() {
    const size = parseInt(document.getElementById('formSizePx').value) || 220;
    const opacity = parseFloat(document.getElementById('formOpacity').value) || 1.0;
    const isRot = document.getElementById('formIsRotated').checked;
    const rotDir = document.getElementById('formRotationDir').value;

    const scaledWidth = Math.max(40, Math.min(220, Math.round(size * 0.45)));
    const img = document.getElementById('markerImg');
    img.style.width = scaledWidth + 'px';
    dragMarker.style.opacity = opacity;

    if (isRot) {
        img.style.animation = (rotDir === 'counter-clockwise' ? 'spin-ccw' : 'spin-cw') + ' 25s linear infinite';
    } else {
        img.style.animation = 'none';
    }
}

function toggleFormRotation(checked) {
    document.getElementById('formRotationDirWrap').style.display = checked ? 'block' : 'none';
    updateMarkerStyles();
}

function previewUploadImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('markerImg').src = e.target.result;
            document.getElementById('formPreviewThumb').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function startNewPlacement() {
    document.getElementById('editorHeading').innerText = 'Visual Coordinate Studio — New Placement';
    document.getElementById('formId').value = '';
    document.getElementById('formName').value = '';
    changeActiveSection('gallery');
    updateMarkerPosition(50.0, 50.0);
    document.getElementById('formSizePx').value = '220';
    document.getElementById('formOpacity').value = '1.00';
    document.getElementById('formTintColor').value = '';
    document.getElementById('formIsRotated').checked = false;
    toggleFormRotation(false);
    document.getElementById('formRotationDir').value = 'clockwise';
    document.getElementById('formShowOnMobile').checked = false;
    document.getElementById('formIsActive').checked = true;
    document.getElementById('formZIndex').value = '1';
    document.getElementById('markerImg').src = defaultDiscUrl;
    document.getElementById('formPreviewThumb').src = defaultDiscUrl;
    document.getElementById('btnSubmit').innerText = 'Save Placement';
    updateMarkerStyles();
    canvasContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function loadPlacementIntoStudio(data) {
    document.getElementById('editorHeading').innerText = 'Edit Custom Placement #' + data.id + ' (' + (data.name || 'Untitled') + ')';
    document.getElementById('formId').value = data.id;
    document.getElementById('formName').value = data.name || '';
    changeActiveSection(data.section_key || 'gallery');
    updateMarkerPosition(parseFloat(data.pos_x_percent) || 50.0, parseFloat(data.pos_y_percent) || 50.0);
    document.getElementById('formSizePx').value = data.size_px || 220;
    document.getElementById('formOpacity').value = data.opacity || '1.00';
    document.getElementById('formTintColor').value = data.tint_color || '';
    if (data.tint_color && data.tint_color.startsWith('#') && data.tint_color.length === 7) {
        document.getElementById('formTintColorPicker').value = data.tint_color;
    }
    
    const isRot = parseInt(data.is_rotated) === 1;
    document.getElementById('formIsRotated').checked = isRot;
    toggleFormRotation(isRot);
    document.getElementById('formRotationDir').value = data.rotation_direction || 'clockwise';

    document.getElementById('formShowOnMobile').checked = parseInt(data.show_on_mobile) === 1;
    document.getElementById('formIsActive').checked = parseInt(data.is_active) === 1;
    document.getElementById('formZIndex').value = data.z_index || 1;

    let imgUrl = data.image_url;
    if (imgUrl && !imgUrl.startsWith('http')) {
        imgUrl = "<?= base_url() ?>" + '/' + imgUrl;
    }
    document.getElementById('markerImg').src = imgUrl || defaultDiscUrl;
    document.getElementById('formPreviewThumb').src = imgUrl || defaultDiscUrl;
    document.getElementById('btnSubmit').innerText = 'Update Placement';
    updateMarkerStyles();
    canvasContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Canvas Mouse & Touch Drag Engine
function handlePointerMove(e) {
    if (!isDragging) return;
    const rect = canvasContainer.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;

    const x = ((clientX - rect.left) / rect.width) * 100;
    const y = ((clientY - rect.top) / rect.height) * 100;

    updateMarkerPosition(x, y);
}

dragMarker.addEventListener('mousedown', function(e) {
    isDragging = true;
    dragMarker.style.cursor = 'grabbing';
    e.preventDefault();
});

dragMarker.addEventListener('touchstart', function(e) {
    isDragging = true;
    e.preventDefault();
}, { passive: false });

window.addEventListener('mousemove', handlePointerMove);
window.addEventListener('touchmove', handlePointerMove, { passive: false });

window.addEventListener('mouseup', function() {
    if (isDragging) {
        isDragging = false;
        dragMarker.style.cursor = 'grab';
    }
});
window.addEventListener('touchend', function() {
    isDragging = false;
});

// Click anywhere on canvas to move marker directly
canvasContainer.addEventListener('click', function(e) {
    if (e.target === dragMarker || dragMarker.contains(e.target)) return;
    const rect = canvasContainer.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    updateMarkerPosition(x, y);
});

// Initialize Studio on load
document.addEventListener('DOMContentLoaded', function() {
    changeActiveSection('gallery');
    updateMarkerPosition(50.0, 50.0);
    updateMarkerStyles();
});
</script>

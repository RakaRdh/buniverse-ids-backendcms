<div class="page-header">
    <div>
        <h1 class="page-title">Dedicated Sponsors</h1>
        <p class="page-subtitle">Configure tiered sponsors (Platinum, Gold, Silver, Bronze, Collaborations, Partnerships) for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
    <div style="display:flex; gap:10px;">
        <button type="button" onclick="document.getElementById('addTierModal').style.display='flex'" class="btn btn-outline">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Tier
        </button>
        <button type="button" onclick="document.getElementById('addSponsorModal').style.display='flex'" class="btn btn-gold">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Sponsor Logo
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<!-- Sponsor Display Mode Switch Banner -->
<div class="cms-card" style="margin-bottom: 24px; border: 1px solid <?= ($sponsorDisplayMode ?? 'dedicated') === 'dedicated' ? '#3e8127' : 'var(--line)' ?>; background: <?= ($sponsorDisplayMode ?? 'dedicated') === 'dedicated' ? 'rgba(73, 152, 46, 0.04)' : '#ffffff' ?>;">
    <div class="cms-card-body" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-weight: 700; font-size: 14px; color: var(--text-strong); display: flex; align-items: center; gap: 8px;">
                <span>Active Frontend Sponsor Mode:</span>
                <span class="status-pill" style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 4px; <?= ($sponsorDisplayMode ?? 'dedicated') === 'dedicated' ? 'background: #2e7d64; color: #ffffff;' : 'background: #9a6b1f; color: #ffffff;' ?>">
                    <?= ($sponsorDisplayMode ?? 'dedicated') === 'dedicated' ? 'DEDICATED SPONSORS (Active)' : 'SINGLE PACKAGE BANNER (Active)' ?>
                </span>
            </div>
            <p style="font-size: 12px; color: var(--text-muted); margin: 4px 0 0 0;">
                <?= ($sponsorDisplayMode ?? 'dedicated') === 'dedicated' 
                    ? 'Frontend saat ini menampilkan tier-based dedicated sponsors (grids & zig-zag marquees).' 
                    : 'Frontend saat ini menampilkan single package banner satu kesatuan gambar.' ?>
            </p>
        </div>
        <form action="<?= base_url('admin/dedicated-sponsors/set-mode') ?>" method="POST" style="margin: 0; display: flex; align-items: center; gap: 8px;">
            <?= csrf_field() ?>
            <div style="display: inline-flex; border: 1px solid var(--line); border-radius: 6px; overflow: hidden; background: var(--bg-page);">
                <label style="padding: 6px 14px; font-size: 12px; font-weight: 600; cursor: pointer; margin: 0; display: flex; align-items: center; gap: 6px; <?= ($sponsorDisplayMode ?? 'dedicated') === 'dedicated' ? 'background: #3e8127; color: #ffffff;' : 'color: var(--text-strong);' ?>">
                    <input type="radio" name="sponsor_display_mode" value="dedicated" onchange="this.form.submit()" <?= ($sponsorDisplayMode ?? 'dedicated') === 'dedicated' ? 'checked' : '' ?> style="margin: 0; display: none;">
                    Dedicated Sponsors
                </label>
                <label style="padding: 6px 14px; font-size: 12px; font-weight: 600; cursor: pointer; margin: 0; display: flex; align-items: center; gap: 6px; <?= ($sponsorDisplayMode ?? 'dedicated') === 'package' ? 'background: #004b84; color: #ffffff;' : 'color: var(--text-strong);' ?>">
                    <input type="radio" name="sponsor_display_mode" value="package" onchange="this.form.submit()" <?= ($sponsorDisplayMode ?? 'dedicated') === 'package' ? 'checked' : '' ?> style="margin: 0; display: none;">
                    Single Package Banner
                </label>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($tiers)): ?>
    <?php foreach ($tiers as $tier): ?>
        <div class="cms-card" style="margin-bottom: 24px;">
            <div class="card-title-bar" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <span class="card-title-text" style="font-weight: 700;"><?= esc($tier['name']) ?></span>
                    <span class="status-pill status-draft" style="font-size:12px; text-transform:uppercase; background: rgba(0,0,0,0.06); color: var(--text-strong); padding: 2px 8px; border-radius: 4px;">
                        <?= esc($tier['group_label'] ?? $tier['group_key']) ?>
                    </span>
                    <span style="font-size:12px; color:var(--text-muted); font-variant-numeric: tabular-nums;">Scale: <?= esc(strtoupper($tier['logo_size'])) ?></span>
                    <span style="font-size:12px; color:var(--text-muted);">(Total: <?= count($tier['sponsors'] ?? []) ?> logos)</span>
                </div>
                <form action="<?= base_url('admin/dedicated-sponsors/tier/delete/' . $tier['id']) ?>" method="POST" onsubmit="return confirm('Delete tier <?= esc($tier['name']) ?> and its sponsors?');" style="margin:0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size:11px; padding:3px 8px;">Delete Tier</button>
                </form>
            </div>
            <div class="cms-card-body">
                <?php if (!empty($tier['sponsors'])): ?>
                    <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: stretch;">
                        <?php foreach ($tier['sponsors'] as $sp): ?>
                            <div style="background: #ffffff; border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px; display: flex; flex-direction: column; align-items: center; justify-content: space-between; width: 190px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                <div style="height: 68px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; width: 100%; background: #2f3653; border-radius: 6px; padding: 6px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);">
                                    <?php if (!empty($sp['logo_url'])): ?>
                                        <img src="<?= base_url(esc($sp['logo_url'])) ?>" alt="<?= esc($sp['name']) ?>" style="max-height: 52px; max-width: 155px; object-fit: contain;">
                                    <?php else: ?>
                                        <span style="font-size:12px; color:rgba(255,255,255,0.7);">No Logo</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 13px; font-weight: 600; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; color: var(--text-strong);" title="<?= esc($sp['name']) ?>">
                                    <?= esc($sp['name']) ?>
                                </div>
                                <?php if (!empty($sp['link_url'])): ?>
                                    <a href="<?= esc($sp['link_url']) ?>" target="_blank" style="font-size: 11px; color: var(--action); text-decoration: none; margin-top: 4px;">
                                        <?= esc(parse_url($sp['link_url'], PHP_URL_HOST) ?: 'Website') ?>
                                    </a>
                                <?php endif; ?>
                                <form action="<?= base_url('admin/dedicated-sponsors/delete/' . $sp['id']) ?>" method="POST" onsubmit="return confirm('Delete sponsor <?= esc($sp['name']) ?>?');" style="margin-top: 12px; width: 100%;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size:11px; padding:3px 8px; width: 100%;">Remove</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-size: 13px; margin: 0;">No sponsor logos attached to this tier yet. Use "Add Sponsor Logo" to populate.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="cms-card">
        <div class="cms-card-body" style="text-align:center; padding: 40px; color:var(--text-muted);">
            No sponsor tiers registered for this event yet. Click "Add Tier" to create tiers like Platinum, Gold, Silver, Bronze, Collaborations, or Partnerships.
        </div>
    </div>
<?php endif; ?>

<!-- Modal: Add Tier -->
<div id="addTierModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.7); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:480px; max-width:90vw; border-radius:var(--radius-lg); padding:24px; border:1px solid var(--line); box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
        <h3 style="font-size:16px; margin-bottom:16px; font-weight:700; color:var(--text-strong);">Add Sponsor Tier</h3>
        <form action="<?= base_url('admin/dedicated-sponsors/tier/save') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Tier Name *</label>
                <input type="text" name="name" id="tierNameInput" class="form-control" required placeholder="e.g. Platinum, Gold, Silver, Bronze, Collaboration, Official Partnerships">
                <div style="display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="setTierQuick('Platinum', 'xl', 'sponsored_by', 'SPONSORED BY :');">Platinum</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setTierQuick('Gold', 'lg', 'sponsored_by', 'SPONSORED BY :');">Gold</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setTierQuick('Silver', 'md', 'sponsored_by', 'SPONSORED BY :');">Silver</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setTierQuick('Bronze', 'sm', 'sponsored_by', 'SPONSORED BY :');">Bronze</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setTierQuick('Collaboration', 'sm', 'collaboration', 'COLLABORATION WITH :');">Collaboration</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="setTierQuick('Official Partnerships', 'sm', 'partnerships', 'PARTNERSHIP :');">Partnership</button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Section Group Type</label>
                <select name="group_key" id="tierGroupInput" class="form-control" onchange="onTierGroupChange(this.value)">
                    <option value="sponsored_by">SPONSORED BY (Commercial Sponsors)</option>
                    <option value="collaboration">COLLABORATION WITH (Partner Kolaborasi / Lembaga)</option>
                    <option value="partnerships">PARTNERSHIP (Official Institutional Partners)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Group Badge Label (Frontend Display)</label>
                <input type="text" name="group_label" id="tierGroupLabelInput" class="form-control" value="SPONSORED BY :">
            </div>

            <div class="form-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Logo Scale</label>
                    <select name="logo_size" id="tierScaleInput" class="form-control">
                        <option value="xl">XL (Platinum / Highest)</option>
                        <option value="lg">LG (Gold / Secondary)</option>
                        <option value="md" selected>MD (Silver / Medium)</option>
                        <option value="sm">SM (Bronze / Collab / Partner)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="1">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--line); padding-top:14px;">
                <button type="button" onclick="document.getElementById('addTierModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Tier</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Sponsor Logo -->
<div id="addSponsorModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.7); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:460px; max-width:90vw; border-radius:var(--radius-lg); padding:24px; border:1px solid var(--line); box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
        <h3 style="font-size:16px; margin-bottom:16px; font-weight:700; color:var(--text-strong);">Add Sponsor Company Logo</h3>
        <form action="<?= base_url('admin/dedicated-sponsors/save') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Target Tier *</label>
                <select name="tier_id" class="form-control" required>
                    <option value="">-- Select Tier --</option>
                    <?php if (!empty($tiers)): ?>
                        <?php foreach ($tiers as $tier): ?>
                            <option value="<?= esc($tier['id']) ?>"><?= esc($tier['name']) ?> (<?= esc($tier['group_label'] ?? $tier['group_key']) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Company / Brand Name *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Bank Mandiri, Telkomsel, Pertamina">
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Logo File (PNG/SVG/WEBP) (Optional, default placeholder if empty)</label>
                <input type="file" name="logo_file" class="form-control" accept="image/*">
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Company Link URL (Optional)</label>
                <input type="url" name="link_url" class="form-control" placeholder="https://example.com">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--line); padding-top:14px;">
                <button type="button" onclick="document.getElementById('addSponsorModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-gold">Save Sponsor</button>
            </div>
        </form>
    </div>
</div>

<script>
function setTierQuick(name, scale, groupKey, label) {
    document.getElementById('tierNameInput').value = name;
    document.getElementById('tierScaleInput').value = scale;
    document.getElementById('tierGroupInput').value = groupKey;
    document.getElementById('tierGroupLabelInput').value = label;
}

function onTierGroupChange(val) {
    const labelInput = document.getElementById('tierGroupLabelInput');
    if (val === 'collaboration' || val === 'collaborations') {
        labelInput.value = 'COLLABORATION WITH :';
    } else if (val === 'partnerships' || val === 'partner') {
        labelInput.value = 'PARTNERSHIP :';
    } else {
        labelInput.value = 'SPONSORED BY :';
    }
}
</script>

<div class="page-header">
    <div>
        <h1 class="page-title">Sponsors & Partnerships</h1>
        <p class="page-subtitle">Configure sponsor tiers, badge groupings (SPONSORED BY vs PARTNERSHIP), and corporate logos for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
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

<?php if (!empty($tiers)): ?>
    <?php foreach ($tiers as $tier): ?>
        <div class="cms-card">
            <div class="card-title-bar" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <span class="card-title-text"><?= esc($tier['name']) ?></span>
                    <span class="status-pill status-draft" style="font-size:12px; text-transform:uppercase;">
                        <?= esc($tier['group_label']) ?>
                    </span>
                    <span style="font-size:12px; color:var(--text-muted); font-variant-numeric: tabular-nums;">Size: <?= esc(strtoupper($tier['logo_size'])) ?></span>
                    <span style="font-size:12px; color:var(--text-muted);">(Order: <?= esc($tier['sort_order']) ?>)</span>
                </div>
                <form action="<?= base_url('admin/sponsors/tier/delete/' . $tier['id']) ?>" method="POST" onsubmit="return confirm('Delete tier <?= esc($tier['name']) ?> and its sponsors?');" style="margin:0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size:11px; padding:3px 8px; color:#ff8a80; border-color:rgba(255,138,128,0.3);">Delete Tier</button>
                </form>
            </div>
            <div class="cms-card-body">
                <?php if (!empty($tier['sponsors'])): ?>
                    <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
                        <?php foreach ($tier['sponsors'] as $sp): ?>
                            <div style="background: var(--primary-1-6); border: 1px solid rgba(255,255,255,0.15); border-radius: var(--radius-md); padding: 16px; display: flex; flex-direction: column; align-items: center; width: 180px; box-shadow: 0 4px 12px rgba(11,81,108,0.2);">
                                <div style="height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; width: 100%;">
                                    <?php if (!empty($sp['logo_url'])): ?>
                                        <img src="<?= base_url(esc($sp['logo_url'])) ?>" alt="<?= esc($sp['name']) ?>" style="max-height: 52px; max-width: 148px; object-fit: contain;">
                                    <?php else: ?>
                                        <span style="font-size:12px; color:rgba(255,255,255,0.7);">No Logo</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 12px; font-weight: 600; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; color: #ffffff;">
                                    <?= esc($sp['name']) ?>
                                </div>
                                <?php if (!empty($sp['link_url'])): ?>
                                    <a href="<?= esc($sp['link_url']) ?>" target="_blank" style="font-size: 12px; color: var(--primary-1-1); text-decoration: none; margin-top: 4px; opacity: 0.9;">
                                        <?= esc(parse_url($sp['link_url'], PHP_URL_HOST) ?: 'Website') ?>
                                    </a>
                                <?php endif; ?>
                                <form action="<?= base_url('admin/sponsors/delete/' . $sp['id']) ?>" method="POST" onsubmit="return confirm('Delete sponsor?');" style="margin-top: 12px;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size:12px; padding:4px 8px; color:#ff8a80; border-color:rgba(255,138,128,0.4);">Remove</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-size: 12px;">No sponsor logos attached to this tier yet.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="cms-card">
        <div class="cms-card-body" style="text-align:center; padding: 40px; color:var(--text-muted);">
            No sponsor tiers registered. Click "Add Tier" to create tiers like Platinum, Gold, or Official Partnership.
        </div>
    </div>
<?php endif; ?>

<!-- Modal: Add Tier -->
<div id="addTierModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:450px; border-radius:var(--radius-lg); padding:20px; border:1px solid var(--line);">
        <h3 style="font-size:15px; margin-bottom:14px; font-weight:700; color:var(--text-strong);">Add Sponsor Tier</h3>
        <form action="<?= base_url('admin/sponsors/tier/save') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Tier Name *</label>
                <input type="text" name="name" id="tierNameInput" class="form-control" required placeholder="e.g. Platinum, Gold, Silver, Bronze">
                <div style="display: flex; gap: 6px; margin-top: 6px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('tierNameInput').value='Platinum';">Platinum</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('tierNameInput').value='Gold';">Gold</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('tierNameInput').value='Silver';">Silver</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('tierNameInput').value='Bronze';">Bronze</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Section Group Type</label>
                <select name="group_key" class="form-control">
                    <option value="sponsored_by">SPONSORED BY (Commercial Sponsors)</option>
                    <option value="partnerships">PARTNERSHIP (Official Institutional Partners)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Group Badge Label (Frontend Display)</label>
                <input type="text" name="group_label" class="form-control" value="SPONSORED BY :">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Logo Display Scale</label>
                    <select name="logo_size" class="form-control">
                        <option value="xl">XL (Platinum / Large)</option>
                        <option value="lg">LG (Gold / Medium)</option>
                        <option value="md" selected>MD (Silver / Regular)</option>
                        <option value="sm">SM (Bronze / Partnership)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= count($tiers) + 1 ?>">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px; border-top:1px solid var(--line); padding-top:12px;">
                <button type="button" onclick="document.getElementById('addTierModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Tier</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Sponsor Logo -->
<div id="addSponsorModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:450px; border-radius:var(--radius-lg); padding:20px; border:1px solid var(--line);">
        <h3 style="font-size:15px; margin-bottom:14px; font-weight:700; color:var(--text-strong);">Add Sponsor Company Logo</h3>
        <form action="<?= base_url('admin/sponsors/save') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Target Tier *</label>
                <select name="tier_id" class="form-control" required>
                    <?php foreach ($tiers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?> (<?= esc($t['group_label']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Company / Brand Name *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Bank Mandiri, Telkomsel">
            </div>

            <div class="form-group">
                <label class="form-label">Logo File (PNG/SVG with transparent background) *</label>
                <input type="file" name="logo_file" class="form-control" required accept="image/*">
            </div>

            <div class="form-group">
                <label class="form-label">Company Link URL (Optional)</label>
                <input type="url" name="link_url" class="form-control" placeholder="https://...">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px; border-top:1px solid var(--line); padding-top:12px;">
                <button type="button" onclick="document.getElementById('addSponsorModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Sponsor</button>
            </div>
        </form>
    </div>
</div>


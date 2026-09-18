<div class="page-header">
    <div>
        <h1 class="page-title">Hero Banner & Venue</h1>
        <p class="page-subtitle">Manage summit venue location and hero carousel banner slides for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
</div>

<!-- VENUE CARD -->
<div class="cms-card">
    <div class="card-title-bar">
        <span class="card-title-text">Hero Venue Location</span>
    </div>
    <div class="cms-card-body">
        <form action="<?= base_url('admin/hero/venue') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Summit Physical Venue</label>
                <input type="text" name="venue" class="form-control" value="<?= esc($hero['venue'] ?? '') ?>" placeholder="e.g. Raffles Hotel, Jakarta">
                <div class="help-text">Displays in the hero metadata header alongside the event dates.</div>
            </div>
            <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--line);">
                <button type="submit" class="btn btn-primary">Save Venue</button>
            </div>
        </form>
    </div>
</div>

<!-- CAROUSEL SLIDES CARD -->
<div class="cms-card" style="margin-top: 24px;">
    <div class="card-title-bar">
        <span class="card-title-text">Hero Banner Carousel Slides</span>
        <button type="button" onclick="document.getElementById('uploadHeroModal').style.display='flex'" class="btn btn-outline btn-sm">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Upload Slide Image
        </button>
    </div>
    <div class="cms-card-body">
        <?php if (!empty($heroImages)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                <?php foreach ($heroImages as $img): ?>
                    <div style="border: 1px solid var(--line); border-radius: var(--radius-md); overflow: hidden; background: var(--bg-surface);">
                        <div style="height: 160px; overflow: hidden; background: var(--primary-1-7); display: flex; align-items: center; justify-content: center;">
                            <img src="<?= base_url(esc($img['image_url'])) ?>" alt="<?= esc($img['alt_text']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-size: 12px; font-weight: 600; color: var(--text-strong);"><?= esc($img['alt_text']) ?></div>
                                <div style="font-size: 12px; color: var(--text-muted);">Slide #<?= esc($img['sort_order']) ?></div>
                            </div>
                            <form action="<?= base_url('admin/hero/delete-image/' . $img['id']) ?>" method="POST" onsubmit="return confirm('Delete this hero slide?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); font-size: 12px;">No hero images uploaded yet. Default theme imagery will be displayed.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Upload Hero Slide -->
<div id="uploadHeroModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:480px; border-radius:var(--radius-lg); padding:24px; border:1px solid var(--line);">
        <h3 style="font-size:16px; margin-bottom:16px; font-weight:700; color:var(--text-strong);">Upload Hero Banner Slide</h3>
        <form action="<?= base_url('admin/hero/upload-image') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Banner Image File *</label>
                <input type="file" name="hero_image_file" class="form-control" required accept="image/*">
                <div class="help-text">Recommended resolution: 1920x1080 (16:9 ratio), max 5MB.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Alt Text / Slide Title</label>
                <input type="text" name="alt_text" class="form-control" placeholder="e.g. Investor Daily Summit Main Stage">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="document.getElementById('uploadHeroModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload Banner</button>
            </div>
        </form>
    </div>
</div>

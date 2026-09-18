<div class="page-header">
    <div>
        <h1 class="page-title">Summit Gallery</h1>
        <p class="page-subtitle">Upload and manage accordion photo highlights for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="openGalleryModal()">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            + Add Photo
        </button>
    </div>
</div>

<!-- Gallery Grid -->
<div class="cms-card">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
        <span class="card-title-text">All Gallery Photos (<?= count($galleryImages) ?>)</span>
        <span style="font-size: 11px; color: var(--text-muted);">* Exactly 1 photo can be marked as primary featured (default expanded on website).</span>
    </div>
    <div class="cms-card-body">
        <?php if (!empty($galleryImages)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;">
                <?php foreach ($galleryImages as $img): ?>
                    <div style="background: var(--bg-surface); border: 1px solid <?= $img['is_featured'] ? 'var(--primary-2-6)' : 'var(--line)' ?>; border-radius: var(--radius-card); overflow: hidden; display:flex; flex-direction:column; box-shadow: <?= $img['is_featured'] ? '0 0 0 1px var(--primary-2-6)' : 'none' ?>;">
                        <div style="position:relative; height: 160px; background: var(--primary-1-7);">
                            <img src="<?= base_url(esc($img['image_url'])) ?>" alt="<?= esc($img['caption'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php if ($img['is_featured']): ?>
                                <span style="position:absolute; top:8px; left:8px; background: var(--primary-2-6); color: #ffffff; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: var(--radius-control); letter-spacing: 0.5px;">
                                    PRIMARY FEATURED
                                </span>
                            <?php endif; ?>
                        </div>
                        <div style="padding: 14px; flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--text-strong); margin-bottom: 4px;">
                                    <?= esc($img['caption'] ?: 'Untitled Photo') ?>
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    Order: #<?= esc($img['sort_order']) ?>
                                </div>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px; padding-top:10px; border-top:1px solid var(--line-soft);">
                                <form action="<?= base_url('admin/gallery/toggle-featured/' . $img['id']) ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn <?= $img['is_featured'] ? 'btn-primary' : 'btn-outline' ?> btn-sm" title="Toggle Single Featured Photo">
                                        <?= $img['is_featured'] ? 'Featured (Active)' : 'Make Featured' ?>
                                    </button>
                                </form>
                                <form action="<?= base_url('admin/gallery/delete/' . $img['id']) ?>" method="POST" onsubmit="return confirm('Delete photo?');" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; color: var(--text-muted); padding: 40px;">
                <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" fill="none" stroke-width="1.5" style="margin-bottom: 12px; color: var(--text-faint);"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <div style="font-weight: 600; color: var(--text-strong); margin-bottom: 4px;">No gallery photos uploaded yet</div>
                <div style="font-size: 12px; margin-bottom: 16px;">Click the button below to upload highlight photos for the accordion carousel.</div>
                <button type="button" class="btn btn-primary btn-sm" onclick="openGalleryModal()">+ Add Photo</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Upload Photo -->
<div id="galleryModal" style="display: none; position: fixed; inset: 0; background: rgba(6, 46, 62, 0.4); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); border-radius: var(--radius-card); width: 100%; max-width: 480px; box-shadow: var(--shadow-pop); border: 1px solid var(--line); overflow: hidden; margin: 20px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 15px; font-weight: 600; color: var(--text-strong); margin: 0;">Upload Gallery Photo</h3>
            <button type="button" onclick="closeGalleryModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 20px; line-height: 1;">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('admin/gallery/upload') ?>" enctype="multipart/form-data" style="padding: 20px;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Photo File (JPG, PNG, WebP) *</label>
                <input type="file" name="image_file" class="form-control" required accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Caption / Description</label>
                <input type="text" name="caption" class="form-control" placeholder="e.g. Opening Plenary Session">
            </div>
            <div class="form-group">
                <label class="form-label">Display Order</label>
                <input type="number" name="sort_order" class="form-control" value="<?= count($galleryImages) + 1 ?>">
            </div>
            <div class="form-group" style="margin-top: 10px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 12.5px; color: var(--text-body);">
                    <input type="checkbox" name="is_featured" value="1">
                    <span>Set as primary featured photo (auto-expands in accordion)</span>
                </label>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeGalleryModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload Photo</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGalleryModal() {
    document.getElementById('galleryModal').style.display = 'flex';
}

function closeGalleryModal() {
    document.getElementById('galleryModal').style.display = 'none';
}
</script>

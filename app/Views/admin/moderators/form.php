<div class="page-header">
    <div>
        <h1 class="page-title"><?= $moderator ? 'Edit Moderator' : 'Add New Moderator' ?></h1>
        <p class="page-subtitle"><?= $moderator ? 'Update moderator profile information' : 'Register a new session moderator / discussion facilitator' ?></p>
    </div>
    <a href="<?= base_url('admin/moderators') ?>" class="btn btn-outline">&larr; Back to Moderators</a>
</div>

<div class="cms-card" style="max-width: 800px;">
    <div class="card-title-bar">
        <span class="card-title-text">Moderator Profile Information</span>
    </div>
    <div class="cms-card-body">
        <form action="<?= $moderator ? base_url('admin/moderators/update/' . $moderator['id']) : base_url('admin/moderators/save') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Full Name & Title *</label>
                <input type="text" name="name" class="form-control" required value="<?= esc($moderator['name'] ?? '') ?>" placeholder="e.g. Timothy Marbun">
            </div>

            <div class="form-group">
                <label class="form-label">Designation / Anchor Title *</label>
                <textarea name="role_text" class="form-control" rows="3" required placeholder="e.g. Lead News Anchor, BTV&#10;Editor In Chief - Investor Daily"><?= esc($moderator['role_text'] ?? '') ?></textarea>
                <div class="help-text">Can span multiple lines if the moderator holds multiple designations.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Portrait Photo</label>
                <?php if (!empty($moderator['photo_url'])): ?>
                    <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 16px;">
                        <img src="<?= base_url(esc($moderator['photo_url'])) ?>" alt="Current Photo" style="width: 80px; height: 106px; border-radius: var(--radius-card); object-fit: cover; border: 1.5px solid var(--line); background: var(--primary-1-7); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-strong);">Current Portrait Photo</div>
                            <span style="font-size: 11.5px; color: var(--text-muted);">Upload a new file below to replace it.</span>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="photo_file" class="form-control" accept="image/*">
                <div class="help-text">Square ratio portrait photo recommended (PNG/JPG).</div>
            </div>

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border-light); display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary"><?= $moderator ? 'Update Moderator' : 'Create Moderator' ?></button>
                <a href="<?= base_url('admin/moderators') ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

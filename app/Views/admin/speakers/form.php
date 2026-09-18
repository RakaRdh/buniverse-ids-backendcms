<div class="page-header">
    <div>
        <h1 class="page-title"><?= $speaker ? 'Edit Speaker' : 'Add New Speaker' ?></h1>
        <p class="page-subtitle"><?= $speaker ? 'Update speaker profile details' : 'Register a new confirmed speaker for the summit' ?></p>
    </div>
    <a href="<?= base_url('admin/speakers') ?>" class="btn btn-outline">&larr; Back to Speakers</a>
</div>

<div class="cms-card" style="max-width: 800px;">
    <div class="card-title-bar">
        <span class="card-title-text">Speaker Profile Information</span>
    </div>
    <div class="cms-card-body">
        <form action="<?= $speaker ? base_url('admin/speakers/update/' . $speaker['id']) : base_url('admin/speakers/save') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Full Name & Honorifics *</label>
                <input type="text" name="name" class="form-control" required value="<?= esc($speaker['name'] ?? '') ?>" placeholder="e.g. Prabowo Subianto">
            </div>

            <div class="form-group">
                <label class="form-label">Title / Organization / Designation *</label>
                <textarea name="role_text" class="form-control" rows="3" required placeholder="e.g. Presiden Republik Indonesia Terpilih&#10;Chairman of Danantara"><?= esc($speaker['role_text'] ?? '') ?></textarea>
                <div class="help-text">Can span multiple lines if the speaker holds multiple titles or designations.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Speaker Portrait Photo</label>
                <?php if (!empty($speaker['photo_url'])): ?>
                    <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 16px;">
                        <img src="<?= base_url(esc($speaker['photo_url'])) ?>" alt="Current Photo" style="width: 80px; height: 106px; border-radius: var(--radius-card); object-fit: cover; border: 1.5px solid var(--line); background: var(--primary-1-7); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
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
                <button type="submit" class="btn btn-primary"><?= $speaker ? 'Update Speaker' : 'Create Speaker' ?></button>
                <a href="<?= base_url('admin/speakers') ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

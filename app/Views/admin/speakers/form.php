<div class="page-header">
    <div>
        <h1 class="page-title"><?= $speaker ? 'Edit Speaker' : 'Add New Speaker' ?></h1>
        <p class="page-subtitle"><?= $speaker ? 'Update speaker profile details' : 'Register a new confirmed speaker for the summit' ?></p>
    </div>
    <?php
    $backUrl = base_url('admin/speakers');
    if (!empty($speaker['id'])) {
        $backUrl .= '#speaker-' . $speaker['id'];
    }
    ?>
    <a href="<?= $backUrl ?>" class="btn btn-outline">&larr; Back to Speakers</a>
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
                    <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 16px; background: rgba(255, 255, 255, 0.04); padding: 12px; border-radius: var(--radius-card); border: 1px solid var(--line);">
                        <img src="<?= base_url(esc($speaker['photo_url'])) ?>" alt="Current Photo" style="width: 70px; height: 92px; border-radius: var(--radius-card); object-fit: cover; border: 1.5px solid var(--line); background: var(--primary-1-7); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-strong);">Current Portrait Photo</div>
                            <span style="font-size: 11.5px; color: var(--text-muted); display: block; margin-bottom: 8px;">Upload a new file below to replace it, or check the box below to remove it.</span>
                            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #ef4444; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" name="remove_photo" value="1" style="cursor: pointer;">
                                Hapus Foto (Balikkan ke Placeholder)
                            </label>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 12px;">
                        <img src="<?= base_url('assets/img/placeholder.png') ?>" alt="Default Placeholder" style="width: 50px; height: 66px; border-radius: var(--radius-card); object-fit: cover; opacity: 0.6; border: 1px solid var(--line);">
                        <span style="font-size: 12px; color: var(--text-muted);">Saat ini menggunakan default placeholder.</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="photo_file" class="form-control">
                <div class="help-text">Square ratio portrait photo recommended (PNG/JPG).</div>
            </div>

            <div class="form-group" style="background: rgba(255, 255, 255, 0.03); padding: 14px 16px; border-radius: var(--radius-card); border: 1px solid var(--line);">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" name="is_active" value="1" <?= (!isset($speaker['is_active']) || !empty($speaker['is_active'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--accent-gold);">
                    <div>
                        <strong style="color: var(--text-strong); font-size: 14px;">Status Aktif (Tampilkan di Frontend)</strong>
                        <div style="font-size: 12px; color: var(--text-muted); font-weight: normal; margin-top: 2px;">Jika dinonaktifkan, pembicara ini tidak akan muncul di tab Speakers maupun jadwal sesi di Frontend.</div>
                    </div>
                </label>
            </div>

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border-light); display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary"><?= $speaker ? 'Update Speaker' : 'Create Speaker' ?></button>
                <a href="<?= base_url('admin/speakers') ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

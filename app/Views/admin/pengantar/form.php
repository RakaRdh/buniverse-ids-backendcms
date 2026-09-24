<?php
$backUrl = base_url('admin/pengantar');
if (!empty($pengantar['id'])) {
    $backUrl .= '#pengantar-' . $pengantar['id'];
}
?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title"><?= $pengantar ? 'Edit Pengantar' : 'Add New Pengantar' ?></h1>
        <p class="page-subtitle"><?= $pengantar ? 'Update pengantar profile information' : 'Register a new session pengantar' ?></p>
    </div>
    <a href="<?= $backUrl ?>" class="btn btn-outline">&larr; Back to Pengantar</a>
</div>

<div class="cms-card" style="max-width: 720px;">
    <div class="card-title-bar">
        <span class="card-title-text"><?= $pengantar ? 'Edit Pengantar Details' : 'New Pengantar Form' ?></span>
    </div>
    <div class="cms-card-body">
        <form action="<?= $pengantar ? base_url('admin/pengantar/update/' . $pengantar['id']) : base_url('admin/pengantar/save') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-group mb-3">
                <label class="form-label">Full Name & Title <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= esc($pengantar['name'] ?? '') ?>" placeholder="e.g. Enggartiasto Lukita">
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Role / Organization / Designation <span class="text-danger">*</span></label>
                <textarea name="role_text" class="form-control" rows="3" required placeholder="e.g. Executive Chairman B-Universe"><?= esc($pengantar['role_text'] ?? '') ?></textarea>
                <div class="help-text">Dapat ditulis bertingkat bila memiliki beberapa jabatan.</div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Photo Profile</label>
                <?php if (!empty($pengantar['photo_url'])): ?>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 10px;">
                        <img src="<?= base_url($pengantar['photo_url']) ?>" alt="<?= esc($pengantar['name']) ?>" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                        <div>
                            <label style="font-size: 13px; color: var(--color-danger); cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="checkbox" name="remove_photo" value="1">
                                Hapus foto saat ini
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="photo_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                <div class="help-text">Format: JPG, PNG, atau WebP. Foto akan otomatis di-resize dan dikompresi agar loading cepat.</div>
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="<?= esc($pengantar['sort_order'] ?? '0') ?>" style="width: 120px;">
                <div class="help-text">Urutan tampilan prioritas bila terdapat lebih dari 1 pengantar.</div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-gold"><?= $pengantar ? 'Update Pengantar' : 'Save Pengantar' ?></button>
                <a href="<?= base_url('admin/pengantar') ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

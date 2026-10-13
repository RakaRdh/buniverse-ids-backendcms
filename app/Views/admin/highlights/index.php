<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 class="page-title">Highlights Studio</h1>
        <p class="page-subtitle">Kelola & urutkan konten Highlights (YouTube, TikTok, & Instagram) untuk <?= esc($activeEvent['title'] ?? 'Investor Daily Summit 2026') ?></p>
    </div>
    <div>
        <span id="reorderStatus" style="font-size: 13px; font-weight: 600; color: #22c55e; display: none;">Urutan Disimpan!</span>
    </div>
</div>

<!-- Form Tambah / Edit Konten Highlights (Top Clean Card) -->
<div class="cms-card" style="margin-bottom: 32px;" id="formCard">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="card-title-text"><?= !empty($editHighlight) ? 'Edit Konten Highlight' : 'Tambah Konten Highlight Baru' ?></span>
            <span id="fetchLoadingBadge" class="badge" style="display: none; background: rgba(201, 148, 0, 0.15); color: var(--accent-gold); border: 1px solid rgba(201, 148, 0, 0.3); font-size: 11px;">
                Mengambil Judul/Caption Otomatis...
            </span>
        </div>
        <?php if (!empty($editHighlight)): ?>
            <a href="<?= base_url('admin/highlights') ?>" class="btn btn-outline btn-sm" style="font-size: 11px;">+ Batal & Tambah Baru</a>
        <?php endif; ?>
    </div>
    <div class="cms-card-body">
        <form action="<?= !empty($editHighlight) ? base_url('admin/highlights/update/' . $editHighlight['id']) : base_url('admin/highlights/save') ?>" method="POST" enctype="multipart/form-data" id="formHighlight">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label class="form-label" style="font-weight: 600; color: var(--text-strong); margin: 0;">
                                1. URL Konten (YouTube / TikTok / Instagram) *
                            </label>
                            <button type="button" id="btnFetchMeta" class="btn btn-sm btn-outline" style="font-size: 11px; padding: 2px 10px; color: var(--accent-gold); border-color: rgba(201, 148, 0, 0.4); cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" onclick="manualFetchCaption()">
                                ⚡ Ambil Judul/Caption
                            </button>
                        </div>
                        <input type="url" name="youtube_url" id="inputMediaUrl" class="form-control" placeholder="Tempel link video YouTube, TikTok, atau Instagram Reel..." required value="<?= esc(old('youtube_url', $editHighlight['youtube_url'] ?? '')) ?>">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 11px;">
                            Judul atau caption akan ter-generate otomatis saat Anda menempelkan link atau menekan tombol di atas.
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" style="font-weight: 600; color: var(--text-strong); margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                            <span>2. Judul / Caption Konten</span>
                            <span style="font-size: 11px; font-weight: normal; color: var(--text-muted);">(Auto-generated & Tetap Editable)</span>
                        </label>
                        <input type="text" name="title" id="inputTitle" class="form-control" placeholder="Akan terisi otomatis dari link, atau ketik judul custom..." value="<?= esc(old('title', $editHighlight['title'] ?? '')) ?>">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 11px;">
                            Teks pada kartu frontend otomatis truncate (...) jika melebihi lebar kartu.
                        </small>
                    </div>
                </div>
            </div>

            <div class="row" style="align-items: flex-end;">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" style="font-weight: 600; color: var(--text-strong); margin-bottom: 6px; display: block;">
                            3. Cover / Thumbnail Image (Opsional)
                        </label>
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 11px;">
                            Cover YouTube, TikTok & IG ter-generate otomatis. Upload di sini jika ingin custom cover.
                        </small>
                        <?php if (!empty($editHighlight['has_custom_thumb'])): ?>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                <img src="<?= esc($editHighlight['custom_thumb_url']) ?>" style="height: 36px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 11px; color: var(--accent-gold);">Custom cover tersimpan</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; padding-top: 6px;">
                            <input type="checkbox" name="is_active" value="1" <?= (!isset($editHighlight) || !empty($editHighlight['is_active'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--accent-gold);">
                            <span style="font-size: 14px; font-weight: 500; color: var(--text-strong);">Status Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="col-md-3" style="text-align: right; margin-bottom: 16px;">
                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                        <?php if (!empty($editHighlight)): ?>
                            <a href="<?= base_url('admin/highlights') ?>" class="btn btn-outline">Batal</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-gold" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px;">
                            <?php if (!empty($editHighlight)): ?>
                                <svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                Perbarui
                            <?php else: ?>
                                <svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Simpan ke Highlights
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>

            <?php /* === LIVE PREVIEW COMMENTED OUT PER USER REQUEST ===
            <div class="form-group" style="margin-top: 10px;">
                <label class="form-label" style="font-size: 12px; text-transform: uppercase;">Live Preview</label>
                <div id="previewWrapper" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; background: rgba(0,0,0,0.5);">
                    <iframe id="previewIframe" src="" style="position: absolute; top:0; left: 0; width: 100%; height: 100%; border: 0; display: none;"></iframe>
                </div>
            </div>
            === END LIVE PREVIEW === */ ?>
        </form>
    </div>
</div>

<!-- Tip Drag & Drop Reordering -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h2 style="font-size: 16px; font-weight: 700; color: var(--text-strong); margin: 0;">Daftar Konten Highlights per Platform</h2>
    <span style="font-size: 12px; color: var(--text-muted);">
        Tip: Drag & drop baris menggunakan ikon <strong>☰</strong> untuk mengatur urutan tampil pada masing-masing tabel.
    </span>
</div>

<!-- ==========================================
     TABEL 1: YOUTUBE HIGHLIGHTS
     ========================================== -->
<div class="cms-card" style="margin-bottom: 28px;">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #ef4444; flex-wrap: wrap; gap: 8px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="#ef4444"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            <span class="card-title-text" style="color: #f87171;">YouTube Highlights</span>
            <span style="font-size: 12px; color: var(--text-muted);">(<?= count($youtubeHighlights ?? []) ?> Video)</span>
        </div>
        <?php if (!empty($youtubeHighlights)): ?>
            <div style="display: flex; align-items: center; gap: 8px;">
                <form action="<?= base_url('admin/highlights/toggle-platform/youtube/1') ?>" method="POST" onsubmit="return confirm('Aktifkan semua konten YouTube?');" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm" style="font-size: 11.5px; padding: 3px 10px; display: inline-flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Enable All
                    </button>
                </form>
                <form action="<?= base_url('admin/highlights/toggle-platform/youtube/0') ?>" method="POST" onsubmit="return confirm('Nonaktifkan semua konten YouTube?');" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size: 11.5px; padding: 3px 10px; display: inline-flex; align-items: center; gap: 5px;">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        Disable All
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <div class="cms-card-body" style="padding: 0;">
        <?php if (empty($youtubeHighlights)): ?>
            <div style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px;">
                Belum ada konten YouTube terdaftar.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="cms-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">☰</th>
                            <th style="width: 110px;">Thumbnail</th>
                            <th>Judul Video</th>
                            <th style="width: 80px; text-align: center;">Status</th>
                            <th style="width: 120px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sortableYoutube" class="sortable-tbody" data-platform="youtube">
                        <?php foreach ($youtubeHighlights as $item): ?>
                            <tr data-id="<?= esc($item['id']) ?>" style="<?= (!empty($editHighlight) && $editHighlight['id'] == $item['id']) ? 'background: rgba(201, 148, 0, 0.08);' : '' ?>">
                                <td style="text-align: center; color: var(--text-muted); cursor: grab; font-size: 16px;" class="drag-handle" title="Drag untuk mengubah urutan">
                                    ☰
                                </td>
                                <td>
                                    <a href="<?= esc($item['youtube_url']) ?>" target="_blank" rel="noopener noreferrer" style="display: block; position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 16/9; background: #000; border: 1px solid rgba(255,255,255,0.1);">
                                        <img src="<?= esc($item['display_thumb']) ?>" alt="<?= esc($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='<?= base_url('assets/img/image1.png') ?>'">
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-strong); font-size: 13.5px; line-height: 1.4; margin-bottom: 4px;">
                                        <?= esc($item['title']) ?>
                                    </div>
                                    <div style="font-size: 11px; font-family: monospace; color: var(--accent-gold);">
                                        ID: <?= esc($item['video_id']) ?>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (!empty($item['is_active'])): ?>
                                        <span class="badge" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); font-size: 11px; padding: 2px 8px; border-radius: 4px;">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); font-size: 11px; padding: 2px 8px; border-radius: 4px;">Off</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="<?= base_url('admin/highlights?edit_id=' . $item['id']) ?>#formCard" class="btn btn-sm btn-icon" title="Edit Highlight" style="color: var(--text-strong);">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </a>
                                        <form action="<?= base_url('admin/highlights/toggle/' . $item['id']) ?>" method="POST" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="<?= !empty($item['is_active']) ? 'Nonaktifkan' : 'Aktifkan' ?>" style="color: <?= !empty($item['is_active']) ? '#fbbf24' : '#94a3b8' ?>;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                            </button>
                                        </form>
                                        <form action="<?= base_url('admin/highlights/delete/' . $item['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Hapus item ini dari daftar highlights?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="Hapus" style="color: #ef4444;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==========================================
     TABEL 2: TIKTOK HIGHLIGHTS
     ========================================== -->
<div class="cms-card" style="margin-bottom: 28px;">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #00f2fe; flex-wrap: wrap; gap: 8px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="#00f2fe"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
            <span class="card-title-text" style="color: #00f2fe;">TikTok Highlights</span>
            <span style="font-size: 12px; color: var(--text-muted);">(<?= count($tiktokHighlights ?? []) ?> Video)</span>
        </div>
        <?php if (!empty($tiktokHighlights)): ?>
            <div style="display: flex; align-items: center; gap: 8px;">
                <form action="<?= base_url('admin/highlights/toggle-platform/tiktok/1') ?>" method="POST" onsubmit="return confirm('Aktifkan semua konten TikTok?');" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm" style="font-size: 11.5px; padding: 3px 10px; display: inline-flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Enable All
                    </button>
                </form>
                <form action="<?= base_url('admin/highlights/toggle-platform/tiktok/0') ?>" method="POST" onsubmit="return confirm('Nonaktifkan semua konten TikTok?');" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size: 11.5px; padding: 3px 10px; display: inline-flex; align-items: center; gap: 5px;">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        Disable All
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <div class="cms-card-body" style="padding: 0;">
        <?php if (empty($tiktokHighlights)): ?>
            <div style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px;">
                Belum ada konten TikTok terdaftar.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="cms-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">☰</th>
                            <th style="width: 70px;">Cover</th>
                            <th>Judul / Caption TikTok</th>
                            <th style="width: 80px; text-align: center;">Status</th>
                            <th style="width: 120px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sortableTiktok" class="sortable-tbody" data-platform="tiktok">
                        <?php foreach ($tiktokHighlights as $item): ?>
                            <tr data-id="<?= esc($item['id']) ?>" style="<?= (!empty($editHighlight) && $editHighlight['id'] == $item['id']) ? 'background: rgba(201, 148, 0, 0.08);' : '' ?>">
                                <td style="text-align: center; color: var(--text-muted); cursor: grab; font-size: 16px;" class="drag-handle" title="Drag untuk mengubah urutan">
                                    ☰
                                </td>
                                <td>
                                    <a href="<?= esc($item['youtube_url']) ?>" target="_blank" rel="noopener noreferrer" style="display: block; position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 9/14; background: #000; border: 1px solid rgba(255,255,255,0.1); width: 48px;">
                                        <img src="<?= esc($item['display_thumb']) ?>" alt="<?= esc($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='<?= base_url('assets/img/image1.png') ?>'">
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-strong); font-size: 13.5px; line-height: 1.4; margin-bottom: 4px;">
                                        <?= esc($item['title']) ?>
                                    </div>
                                    <div style="font-size: 11px; font-family: monospace; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; max-width: 320px; white-space: nowrap;">
                                        <?= esc($item['youtube_url']) ?>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (!empty($item['is_active'])): ?>
                                        <span class="badge" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); font-size: 11px; padding: 2px 8px; border-radius: 4px;">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); font-size: 11px; padding: 2px 8px; border-radius: 4px;">Off</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="<?= base_url('admin/highlights?edit_id=' . $item['id']) ?>#formCard" class="btn btn-sm btn-icon" title="Edit Highlight" style="color: var(--text-strong);">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </a>
                                        <form action="<?= base_url('admin/highlights/toggle/' . $item['id']) ?>" method="POST" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="<?= !empty($item['is_active']) ? 'Nonaktifkan' : 'Aktifkan' ?>" style="color: <?= !empty($item['is_active']) ? '#fbbf24' : '#94a3b8' ?>;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                            </button>
                                        </form>
                                        <form action="<?= base_url('admin/highlights/delete/' . $item['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Hapus item ini dari daftar highlights?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="Hapus" style="color: #ef4444;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==========================================
     TABEL 3: INSTAGRAM HIGHLIGHTS
     ========================================== -->
<div class="cms-card" style="margin-bottom: 28px;">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #e1306c; flex-wrap: wrap; gap: 8px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="#e1306c"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            <span class="card-title-text" style="color: #e1306c;">Instagram Highlights</span>
            <span style="font-size: 12px; color: var(--text-muted);">(<?= count($instagramHighlights ?? []) ?> Post/Reel)</span>
        </div>
        <?php if (!empty($instagramHighlights)): ?>
            <div style="display: flex; align-items: center; gap: 8px;">
                <form action="<?= base_url('admin/highlights/toggle-platform/instagram/1') ?>" method="POST" onsubmit="return confirm('Aktifkan semua konten Instagram?');" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm" style="font-size: 11.5px; padding: 3px 10px; display: inline-flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Enable All
                    </button>
                </form>
                <form action="<?= base_url('admin/highlights/toggle-platform/instagram/0') ?>" method="POST" onsubmit="return confirm('Nonaktifkan semua konten Instagram?');" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size: 11.5px; padding: 3px 10px; display: inline-flex; align-items: center; gap: 5px;">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        Disable All
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <div class="cms-card-body" style="padding: 0;">
        <?php if (empty($instagramHighlights)): ?>
            <div style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px;">
                Belum ada konten Instagram terdaftar.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="cms-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">☰</th>
                            <th style="width: 70px;">Cover</th>
                            <th>Judul / Caption Instagram</th>
                            <th style="width: 80px; text-align: center;">Status</th>
                            <th style="width: 120px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sortableInstagram" class="sortable-tbody" data-platform="instagram">
                        <?php foreach ($instagramHighlights as $item): ?>
                            <tr data-id="<?= esc($item['id']) ?>" style="<?= (!empty($editHighlight) && $editHighlight['id'] == $item['id']) ? 'background: rgba(201, 148, 0, 0.08);' : '' ?>">
                                <td style="text-align: center; color: var(--text-muted); cursor: grab; font-size: 16px;" class="drag-handle" title="Drag untuk mengubah urutan">
                                    ☰
                                </td>
                                <td>
                                    <a href="<?= esc($item['youtube_url']) ?>" target="_blank" rel="noopener noreferrer" style="display: block; position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 9/14; background: #000; border: 1px solid rgba(255,255,255,0.1); width: 48px;">
                                        <img src="<?= esc($item['display_thumb']) ?>" alt="<?= esc($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='<?= base_url('assets/img/image1.png') ?>'">
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-strong); font-size: 13.5px; line-height: 1.4; margin-bottom: 4px;">
                                        <?= esc($item['title']) ?>
                                    </div>
                                    <div style="font-size: 11px; font-family: monospace; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; max-width: 320px; white-space: nowrap;">
                                        <?= esc($item['youtube_url']) ?>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (!empty($item['is_active'])): ?>
                                        <span class="badge" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); font-size: 11px; padding: 2px 8px; border-radius: 4px;">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); font-size: 11px; padding: 2px 8px; border-radius: 4px;">Off</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="<?= base_url('admin/highlights?edit_id=' . $item['id']) ?>#formCard" class="btn btn-sm btn-icon" title="Edit Highlight" style="color: var(--text-strong);">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </a>
                                        <form action="<?= base_url('admin/highlights/toggle/' . $item['id']) ?>" method="POST" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="<?= !empty($item['is_active']) ? 'Nonaktifkan' : 'Aktifkan' ?>" style="color: <?= !empty($item['is_active']) ? '#fbbf24' : '#94a3b8' ?>;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                            </button>
                                        </form>
                                        <form action="<?= base_url('admin/highlights/delete/' . $item['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Hapus item ini dari daftar highlights?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-icon" title="Hapus" style="color: #ef4444;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- SortableJS library for smooth drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
// Helper to synchronize CSRF tokens across all form inputs & meta tags
function updateAllCsrfTokens(tokenName, newHash) {
    if (!newHash) return;
    const csrfMetaHash = document.querySelector('meta[name="csrf-hash"]');
    if (csrfMetaHash) {
        csrfMetaHash.setAttribute('content', newHash);
    }
    const name = tokenName || document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
    document.querySelectorAll('input[type="hidden"]').forEach(input => {
        if (input.name === name || input.name === 'csrf_token_name' || input.name === '<?= csrf_token() ?>') {
            input.value = newHash;
        }
    });
}

// 1. Auto-Fetch Judul/Caption saat URL ditempel / diketik / ditekan tombol
let fetchTimeout = null;

function manualFetchCaption() {
    const inputMedia = document.getElementById('inputMediaUrl');
    if (!inputMedia || !inputMedia.value.trim()) {
        alert('Silakan masukkan atau tempel link video/konten terlebih dahulu.');
        inputMedia?.focus();
        return;
    }
    triggerAutoFetch(inputMedia.value.trim(), true);
}

function triggerAutoFetch(url, isManual = false) {
    if (!url || url.trim().length < 8) return;
    url = url.trim();

    const titleInput = document.getElementById('inputTitle');
    const badge = document.getElementById('fetchLoadingBadge');
    const fetchBtn = document.getElementById('btnFetchMeta');

    if (badge) {
        badge.style.display = 'inline-block';
        badge.style.background = 'rgba(201, 148, 0, 0.15)';
        badge.style.color = 'var(--accent-gold)';
        badge.style.borderColor = 'rgba(201, 148, 0, 0.3)';
        badge.textContent = '⏳ Mengambil Judul/Caption...';
    }
    if (fetchBtn) {
        fetchBtn.disabled = true;
        fetchBtn.textContent = '⏳ Mengambil...';
    }

    const csrfMetaName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
    const csrfMetaHash = document.querySelector('meta[name="csrf-hash"]');
    const csrfMetaHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';
    const currentHash = csrfMetaHash ? csrfMetaHash.getAttribute('content') : (document.querySelector('input[name="' + csrfMetaName + '"]')?.value || '<?= csrf_hash() ?>');

    const formData = new FormData();
    if (csrfMetaName && currentHash) {
        formData.append(csrfMetaName, currentHash);
    }
    formData.append('url', url);

    fetch('<?= base_url('admin/highlights/fetch-metadata') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            [csrfMetaHeader]: currentHash
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (fetchBtn) {
            fetchBtn.disabled = false;
            fetchBtn.textContent = '⚡ Ambil Judul/Caption';
        }
        if (data) {
            if (data.csrf_hash) {
                updateAllCsrfTokens(data.csrf_name, data.csrf_hash);
            }
            if (data.status === 'success' && data.title && titleInput) {
                titleInput.value = data.title;
                if (badge) {
                    badge.style.display = 'inline-block';
                    badge.style.background = 'rgba(34, 197, 94, 0.15)';
                    badge.style.color = '#4ade80';
                    badge.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                    badge.textContent = '✓ Judul Berhasil Diambil!';
                    setTimeout(() => { badge.style.display = 'none'; }, 3000);
                }
            } else {
                if (badge) {
                    if (isManual) {
                        badge.style.display = 'inline-block';
                        badge.style.background = 'rgba(239, 68, 68, 0.15)';
                        badge.style.color = '#f87171';
                        badge.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                        badge.textContent = '⚠️ Caption tidak ditemukan otomatis, silakan ketik manual.';
                        setTimeout(() => { badge.style.display = 'none'; }, 4000);
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        }
    })
    .catch(err => {
        if (fetchBtn) {
            fetchBtn.disabled = false;
            fetchBtn.textContent = '⚡ Ambil Judul/Caption';
        }
        if (badge) badge.style.display = 'none';
        console.error('Metadata fetch error:', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const inputMedia = document.getElementById('inputMediaUrl');
    if (inputMedia) {
        inputMedia.addEventListener('input', function() {
            clearTimeout(fetchTimeout);
            const val = this.value;
            fetchTimeout = setTimeout(() => {
                triggerAutoFetch(val);
            }, 450);
        });

        inputMedia.addEventListener('change', function() {
            triggerAutoFetch(this.value);
        });

        inputMedia.addEventListener('paste', function() {
            setTimeout(() => {
                triggerAutoFetch(inputMedia.value);
            }, 50);
        });
    }

    // 2. Inisialisasi Drag and Drop terpisah per tabel (tidak bisa lintas tabel)
    const tables = [
        { id: 'sortableYoutube', group: 'youtube_group' },
        { id: 'sortableTiktok', group: 'tiktok_group' },
        { id: 'sortableInstagram', group: 'instagram_group' }
    ];

    const statusMsg = document.getElementById('reorderStatus');

    tables.forEach(t => {
        const tbody = document.getElementById(t.id);
        if (tbody && typeof Sortable !== 'undefined') {
            Sortable.create(tbody, {
                group: t.group, // Isolated group per platform
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    const rows = tbody.querySelectorAll('tr[data-id]');
                    const order = [];
                    rows.forEach(r => {
                        const id = r.getAttribute('data-id');
                        if (id) order.push(id);
                    });

                    const csrfMetaName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
                    const csrfMetaHash = document.querySelector('meta[name="csrf-hash"]');
                    const csrfMetaHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';
                    const currentHash = csrfMetaHash ? csrfMetaHash.getAttribute('content') : (document.querySelector('input[name="' + csrfMetaName + '"]')?.value || '<?= csrf_hash() ?>');

                    const formData = new FormData();
                    if (csrfMetaName && currentHash) {
                        formData.append(csrfMetaName, currentHash);
                    }
                    order.forEach((id, idx) => {
                        formData.append('order[' + idx + ']', id);
                    });

                    if (statusMsg) {
                        statusMsg.style.display = 'inline-block';
                        statusMsg.style.color = '#e5a900';
                        statusMsg.textContent = 'Menyimpan urutan...';
                    }

                    fetch('<?= base_url('admin/highlights/reorder') ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            [csrfMetaHeader]: currentHash
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data) {
                            if (data.csrf_hash) {
                                updateAllCsrfTokens(data.csrf_name, data.csrf_hash);
                            }
                            if (data.status === 'success') {
                                if (statusMsg) {
                                    statusMsg.style.color = '#22c55e';
                                    statusMsg.textContent = 'Urutan Disimpan!';
                                    setTimeout(() => {
                                        statusMsg.style.display = 'none';
                                    }, 2500);
                                }
                            } else {
                                if (statusMsg) {
                                    statusMsg.style.color = '#ef4444';
                                    statusMsg.textContent = data.message || 'Gagal menyimpan urutan';
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Reorder error:', err);
                        if (statusMsg) {
                            statusMsg.style.color = '#ef4444';
                            statusMsg.textContent = 'Gagal koneksi ke server';
                        }
                    });
                }
            });
        }
    });
});
</script>

<div class="page-header">
    <div>
        <h1 class="page-title">Highlights Studio</h1>
        <p class="page-subtitle">Kelola daftar video Event Highlights (Zero YouTube Quota via Database) untuk <?= esc($activeEvent['title'] ?? 'Investor Daily Summit 2026') ?></p>
    </div>
</div>

<div class="row">
    <!-- Form Tambah / Edit Video -->
    <div class="col-md-5">
        <div class="cms-card">
            <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                <span class="card-title-text"><?= !empty($editHighlight) ? 'Edit Video Highlights' : 'Tambah Video Highlights' ?></span>
                <?php if (!empty($editHighlight)): ?>
                    <a href="<?= base_url('admin/highlights') ?>" class="btn btn-outline btn-sm" style="font-size: 11px;">+ Tambah Baru</a>
                <?php endif; ?>
            </div>
            <div class="cms-card-body">
                <form action="<?= !empty($editHighlight) ? base_url('admin/highlights/update/' . $editHighlight['id']) : base_url('admin/highlights/save') ?>" method="POST" id="formHighlight">
                    <?= csrf_field() ?>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" style="font-weight: 600; color: var(--text-strong); margin-bottom: 6px; display: block;">Judul Video *</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Jakarta Tawarkan 37 proyek senilai US$7 M" required value="<?= esc(old('title', $editHighlight['title'] ?? '')) ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" style="font-weight: 600; color: var(--text-strong); margin-bottom: 6px; display: block;">URL YouTube Resmi *</label>
                        <input type="text" name="youtube_url" id="inputYoutubeUrl" class="form-control" placeholder="https://www.youtube.com/watch?v=BCq3ycyxkf4" required value="<?= esc(old('youtube_url', $editHighlight['youtube_url'] ?? '')) ?>" oninput="updateYoutubePreview(this.value)">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Mendukung format full URL, shortlink youtu.be, atau 11-digit Video ID.</small>
                    </div>

                    <!-- Live Iframe Preview 16:9 -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 6px; display: block;">Live Preview (16:9)</label>
                        <div id="previewWrapper" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; background: rgba(0,0,0,0.5); border: 1px dashed rgba(255,255,255,0.15); border-radius: var(--radius-md);">
                            <iframe id="previewIframe" src="" style="position: absolute; top:0; left: 0; width: 100%; height: 100%; border: 0; display: none;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <div id="previewPlaceholder" style="position: absolute; top:0; left:0; width:100%; height:100%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 13px;">
                                Masukkan link YouTube untuk melihat preview
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 600; color: var(--text-strong); margin-bottom: 6px; display: block;">Urutan Tampil</label>
                                <input type="number" name="sort_order" class="form-control" value="<?= esc(old('sort_order', $editHighlight['sort_order'] ?? 0)) ?>" min="0">
                            </div>
                        </div>
                        <div class="col-6" style="display: flex; align-items: center; padding-top: 24px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none;">
                                <input type="checkbox" name="is_active" value="1" <?= (!isset($editHighlight) || !empty($editHighlight['is_active'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--accent-gold);">
                                <span style="font-size: 14px; font-weight: 500; color: var(--text-strong);">Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-gold" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <?php if (!empty($editHighlight)): ?>
                                <svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                Perbarui Highlight
                            <?php else: ?>
                                <svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Simpan ke Highlights
                            <?php endif; ?>
                        </button>
                        <?php if (!empty($editHighlight)): ?>
                            <a href="<?= base_url('admin/highlights') ?>" class="btn btn-outline">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Video Terdaftar -->
    <div class="col-md-7">
        <div class="cms-card">
            <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <span class="card-title-text">Daftar Video Highlights</span>
                    <span style="font-size: 12px; color: var(--text-muted); margin-left: 8px;">(<?= count($highlights) ?> Video)</span>
                </div>
                <?php if (!empty($highlights)): ?>
                    <form action="<?= base_url('admin/highlights/inactive-all') ?>" method="POST" onsubmit="return confirm('Nonaktifkan SEMUA video highlights sekaligus? Highlights tidak akan tampil di frontend sampai diaktifkan kembali.');" style="margin: 0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger-outline btn-sm" style="font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                            Disable / Inactive All
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="cms-card-body" style="padding: 0;">
                <?php if (empty($highlights)): ?>
                    <div style="padding: 40px 20px; text-align: center; color: var(--text-muted);">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px; opacity: 0.5;">
                            <polygon points="23 7 16 12 23 17 23 7"></polygon>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                        </svg>
                        <p style="margin: 0; font-size: 14px;">Belum ada video highlights. Tambahkan video pertama Anda di panel kiri.</p>
                        <small style="color: var(--text-muted); display: block; margin-top: 6px;">
                            (Catatan: Jika daftar highlights kosong, section #highlights di Frontend otomatis tersembunyi total dari DOM).
                        </small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="cms-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Thumbnail</th>
                                    <th>Judul & Video ID</th>
                                    <th style="width: 60px; text-align: center;">Urut</th>
                                    <th style="width: 80px; text-align: center;">Status</th>
                                    <th style="width: 130px; text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($highlights as $item): ?>
                                    <tr style="<?= (!empty($editHighlight) && $editHighlight['id'] == $item['id']) ? 'background: rgba(201, 148, 0, 0.08);' : '' ?>">
                                        <td>
                                            <a href="<?= esc($item['youtube_url']) ?>" target="_blank" rel="noopener noreferrer" style="display: block; position: relative; border-radius: 4px; overflow: hidden; aspect-ratio: 16/9; background: #000;">
                                                <img src="https://i.ytimg.com/vi/<?= esc($item['video_id']) ?>/hqdefault.jpg" alt="<?= esc($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            </a>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: var(--text-strong); font-size: 13px; line-height: 1.4; margin-bottom: 4px;">
                                                <?= esc($item['title']) ?>
                                            </div>
                                            <div style="font-size: 11px; font-family: monospace; color: var(--accent-gold);">
                                                ID: <?= esc($item['video_id']) ?>
                                            </div>
                                        </td>
                                        <td style="text-align: center; color: var(--text-muted); font-size: 13px;">
                                            <?= (int)$item['sort_order'] ?>
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
                                                <a href="<?= base_url('admin/highlights?edit_id=' . $item['id']) ?>" class="btn btn-sm btn-icon" title="Edit Highlight" style="color: var(--text-strong);">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </a>
                                                <form action="<?= base_url('admin/highlights/toggle/' . $item['id']) ?>" method="POST" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-icon" title="<?= !empty($item['is_active']) ? 'Nonaktifkan' : 'Aktifkan' ?>" style="color: <?= !empty($item['is_active']) ? '#fbbf24' : '#94a3b8' ?>;">
                                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                                    </button>
                                                </form>
                                                <form action="<?= base_url('admin/highlights/delete/' . $item['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Hapus video ini dari daftar highlights?');">
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
    </div>
</div>

<script>
function extractYouTubeId(url) {
    if (!url) return '';
    url = url.trim();
    var regExp = /(?:youtube(?:-nocookie)?\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|live)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i;
    var match = url.match(regExp);
    if (match && match[1]) {
        return match[1];
    }
    if (url.length === 11 && url.indexOf('/') === -1) {
        return url;
    }
    return '';
}

function updateYoutubePreview(val) {
    var videoId = extractYouTubeId(val);
    var iframe = document.getElementById('previewIframe');
    var placeholder = document.getElementById('previewPlaceholder');

    if (videoId) {
        iframe.src = 'https://www.youtube.com/embed/' + videoId;
        iframe.style.display = 'block';
        placeholder.style.display = 'none';
    } else {
        iframe.src = '';
        iframe.style.display = 'none';
        placeholder.style.display = 'flex';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var initialUrl = document.getElementById('inputYoutubeUrl').value;
    if (initialUrl) {
        updateYoutubePreview(initialUrl);
    }
});
</script>

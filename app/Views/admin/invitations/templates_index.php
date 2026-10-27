<div class="content-header">
    <div class="header-left">
        <h1 class="page-title"><?= esc($pageTitle) ?></h1>
        <p class="page-desc">Kelola template tampilan e-undangan interaktif untuk event <?= esc($activeEvent['title'] ?? 'Malam Apresiasi 2026') ?>.</p>
    </div>
    <div class="header-right">
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addTemplateModal').style.display='flex'">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Tambah Template Baru</span>
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; margin-top: 24px;">
    <?php if (empty($templates)): ?>
        <div style="grid-column: 1 / -1; padding: 48px; text-align: center; background: var(--bg-card); border-radius: var(--radius-md); border: 1px dashed var(--border-color); color: var(--text-muted);">
            Belum ada template yang terdaftar. Silakan tambahkan template baru.
        </div>
    <?php else: ?>
        <?php foreach ($templates as $tpl): ?>
            <div style="background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid <?= $tpl['is_default'] ? 'var(--primary-color)' : 'var(--border-color)' ?>; overflow: hidden; display: flex; flex-direction: column; box-shadow: var(--shadow-sm); position: relative;">
                <?php if ($tpl['is_default']): ?>
                    <div style="position: absolute; top: 12px; left: 12px; background: var(--action); color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px; z-index: 2;">
                        Default Event
                    </div>
                <?php endif; ?>

                <div style="height: 180px; background: #0c1424; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if (!empty($tpl['thumbnail_url'])): ?>
                        <img src="<?= esc($tpl['thumbnail_url']) ?>" alt="<?= esc($tpl['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="text-align: center; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <div style="font-size: 12px; margin-top: 6px;">No Preview Thumbnail</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-strong); margin: 0;"><?= esc($tpl['name']) ?></h3>
                        <span class="status-pill status-<?= $tpl['is_active'] ? 'published' : 'draft' ?>">
                            <?= $tpl['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>

                    <div style="font-family: monospace; font-size: 12px; color: var(--action); margin-bottom: 10px; background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px; display: inline-block;">
                        key: <?= esc($tpl['template_key']) ?>
                    </div>

                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin: 0 0 16px; flex: 1;">
                        <?= esc($tpl['description'] ?: 'Tidak ada deskripsi.') ?>
                    </p>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap; border-top: 1px solid var(--line); padding-top: 14px;">
                        <button type="button" class="btn btn-outline" style="font-size: 12px; padding: 6px 12px; display:inline-flex; align-items:center; gap:6px;" onclick="openPreviewModal('<?= esc($tpl['template_key']) ?>', '<?= esc($tpl['name']) ?>')">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <span>Live Preview</span>
                        </button>

                        <?php if (!$tpl['is_default']): ?>
                            <form action="<?= base_url('admin/invitation-templates/set-default/' . $tpl['id']) ?>" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline" style="font-size: 12px; padding: 6px 12px;" title="Set as default template for MAP guests">
                                    Set Default
                                </button>
                            </form>
                        <?php endif; ?>

                        <form action="<?= base_url('admin/invitation-templates/toggle/' . $tpl['id']) ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline" style="font-size: 12px; padding: 6px 12px;">
                                <?= $tpl['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                            </button>
                        </form>

                        <?php if (!$tpl['is_default']): ?>
                            <form action="<?= base_url('admin/invitation-templates/delete/' . $tpl['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Hapus template ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger" style="font-size: 12px; padding: 6px 10px;" title="Hapus Template">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Tambah Template Baru -->
<div id="addTemplateModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#ffffff; width:100%; max-width:500px; border-radius:12px; padding:24px 28px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); border:1px solid #e2e8f0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
            <h2 style="font-size:18px; font-weight:700; margin:0; color:var(--text-strong);">Tambah Template E-Undangan</h2>
            <button type="button" onclick="document.getElementById('addTemplateModal').style.display='none'" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>
        <form action="<?= base_url('admin/invitation-templates/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Nama Template *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Royal Gold Luxury (VIP)">
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Template Key (Sesuai File View di Frontend) *</label>
                <input type="text" name="template_key" class="form-control" required placeholder="e.g. malam_apresiasi_luxury" pattern="[a-zA-Z0-9_-]+">
                <small style="color:var(--text-muted); font-size:11px;">Hanya huruf, angka, strip (-), dan underscore (_). Harus sama dengan nama file view di FrontendMAP.</small>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Deskripsi</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Catatan tema / nuansa tampilan..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Screenshot Thumbnail (Opsional)</label>
                <input type="file" name="thumbnail_file" class="form-control" accept="image/*">
            </div>
            <div class="form-group" style="margin-bottom:18px;">
                <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
                    <input type="checkbox" name="is_default" value="1">
                    <span>Jadikan sebagai Template Default Acara</span>
                </label>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="document.getElementById('addTemplateModal').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Template</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Interactive Phone Live Preview -->
<div id="phonePreviewModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.75); z-index:99999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="position:relative; display:flex; flex-direction:column; align-items:center;">
        <div style="display:flex; justify-content:space-between; align-items:center; width:380px; margin-bottom:12px; color:#fff;">
            <strong id="previewTitle" style="font-size:15px;">Preview Template</strong>
            <button type="button" onclick="closePreviewModal()" style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:30px; height:30px; border-radius:50%; font-size:16px; cursor:pointer;">&times;</button>
        </div>
        <!-- iPhone / Android Phone Mockup Frame -->
        <div style="width:380px; height:680px; background:#000; border-radius:44px; border:8px solid #2d3748; box-shadow:0 25px 60px rgba(0,0,0,0.8); overflow:hidden; position:relative;">
            <div style="position:absolute; top:8px; left:50%; transform:translateX(-50%); width:120px; height:20px; background:#2d3748; border-radius:12px; z-index:10;"></div>
            <iframe id="previewIframe" src="" style="width:100%; height:100%; border:none; padding-top:20px; box-sizing:border-box;"></iframe>
        </div>
    </div>
</div>

<script>
function openPreviewModal(key, name) {
    document.getElementById('previewTitle').textContent = 'Live Preview: ' + name;
    // Panggil preview di FrontendMAP (port 8082 aktif)
    const frontendBaseUrl = '<?= env('app.frontendMapURL', 'http://localhost:8082') ?>';
    document.getElementById('previewIframe').src = frontendBaseUrl.replace(/\/$/, '') + '/invitation/preview?template=' + encodeURIComponent(key);
    document.getElementById('phonePreviewModal').style.display = 'flex';
}
function closePreviewModal() {
    document.getElementById('phonePreviewModal').style.display = 'none';
    document.getElementById('previewIframe').src = '';
}
</script>

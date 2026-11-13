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

<div style="background:var(--bg-surface); border-radius:var(--radius-card); border:1px solid var(--line); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-top:20px;">
    <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
            <thead>
                <tr style="background:var(--bg-subtle); border-bottom:1px solid var(--line); color:var(--text-muted); font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">
                    <th style="padding:14px 16px; width:50px; text-align:center;">#</th>
                    <th style="padding:14px 16px; min-width:220px;">Nama Template</th>
                    <th style="padding:14px 16px; width:180px;">Template Key</th>
                    <th style="padding:14px 16px; min-width:260px;">Deskripsi</th>
                    <th style="padding:14px 16px; width:110px; text-align:center;">Status</th>
                    <th style="padding:14px 16px; width:260px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($templates)): ?>
                    <tr>
                        <td colspan="6" style="padding:48px 16px; text-align:center; color:var(--text-muted);">
                            Belum ada template yang terdaftar. Silakan klik <strong>Tambah Template Baru</strong>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($templates as $tpl): ?>
                        <tr style="border-bottom:1px solid var(--line-soft); transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 16px; text-align:center; color:var(--text-muted); font-weight:600;">
                                <?= $no++ ?>
                            </td>
                            <td style="padding:14px 16px;">
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <span style="font-weight:700; color:var(--text-strong); font-size:14px;">
                                        <?= esc($tpl['name']) ?>
                                    </span>
                                    <?php if ($tpl['is_default']): ?>
                                        <span style="background:var(--action); color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:50px; text-transform:uppercase; letter-spacing:0.5px;">
                                            Default Event
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding:14px 16px;">
                                <code style="font-family:monospace; font-size:12px; color:var(--action); background:rgba(0,0,0,0.05); padding:3px 8px; border-radius:4px; display:inline-block;">
                                    <?= esc($tpl['template_key']) ?>
                                </code>
                            </td>
                            <td style="padding:14px 16px; color:var(--text-muted); line-height:1.4;">
                                <?= esc($tpl['description'] ?: '—') ?>
                            </td>
                            <td style="padding:14px 16px; text-align:center;">
                                <span class="status-pill status-<?= $tpl['is_active'] ? 'published' : 'draft' ?>">
                                    <?= $tpl['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td style="padding:14px 16px; text-align:right;">
                                <div style="display:inline-flex; align-items:center; justify-content:flex-end; gap:6px; flex-wrap:wrap;">
                                    <button type="button" class="btn btn-outline" style="font-size:12px; padding:6px 11px; display:inline-flex; align-items:center; gap:5px;" onclick="openPreviewModal('<?= esc($tpl['template_key']) ?>', '<?= esc($tpl['name']) ?>')">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        <span>Preview</span>
                                    </button>

                                    <?php if (!$tpl['is_default']): ?>
                                        <form action="<?= base_url('admin/invitation-templates/set-default/' . $tpl['id']) ?>" method="POST" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline" style="font-size:12px; padding:6px 10px;" title="Jadikan template default">
                                                Set Default
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form action="<?= base_url('admin/invitation-templates/toggle/' . $tpl['id']) ?>" method="POST" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline" style="font-size:12px; padding:6px 10px;">
                                            <?= $tpl['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>

                                    <?php if (!$tpl['is_default']): ?>
                                        <form action="<?= base_url('admin/invitation-templates/delete/' . $tpl['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Hapus template ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger" style="font-size:12px; padding:6px 9px;" title="Hapus Template">
                                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Template Baru -->
<div id="addTemplateModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#ffffff; width:100%; max-width:500px; border-radius:12px; padding:24px 28px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); border:1px solid #e2e8f0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
            <h2 style="font-size:18px; font-weight:700; margin:0; color:var(--text-strong);">Tambah Template E-Undangan</h2>
            <button type="button" onclick="document.getElementById('addTemplateModal').style.display='none'" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>
        <form action="<?= base_url('admin/invitation-templates/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Nama Template *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Apresiasi Template 2 / ESG Template 2">
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Template Key (Sesuai File View di Frontend) *</label>
                <input type="text" name="template_key" class="form-control" required placeholder="e.g. malam_apresiasi_2 / esg_template_2" pattern="[a-zA-Z0-9_-]+">
                <small style="color:var(--text-muted); font-size:11px;">Hanya huruf, angka, strip (-), dan underscore (_). Harus sama dengan nama view template di frontend.</small>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Deskripsi</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Catatan tema / nuansa tampilan..."></textarea>
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
    // Dynamic frontend URL resolved based on active event (e.g. ESG: 8083, MAP: 8082)
    const frontendBaseUrl = '<?= esc($frontendBaseUrl ?? env('app.frontendMapURL', 'http://localhost:8082')) ?>';
    document.getElementById('previewIframe').src = frontendBaseUrl.replace(/\/$/, '') + '/invitation/preview?template=' + encodeURIComponent(key);
    document.getElementById('phonePreviewModal').style.display = 'flex';
}
function closePreviewModal() {
    document.getElementById('phonePreviewModal').style.display = 'none';
    document.getElementById('previewIframe').src = '';
}
</script>

<div class="content-header" style="margin-bottom:16px;">
    <div class="header-left">
        <h1 class="page-title"><?= esc($pageTitle) ?></h1>
        <p class="page-desc">Kelola daftar penerima e-undangan VIP, generate link undangan unik, dan rekapitulasi konfirmasi kehadiran (RSVP).</p>
    </div>
</div>

<!-- Summary Metrics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin: 20px 0;">
    <div class="stat-card" style="background:var(--bg-card); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-color); box-shadow:var(--shadow-sm);">
        <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; font-weight:600;">Total Tamu</div>
        <div style="font-size:26px; font-weight:700; color:var(--text-strong); margin-top:4px;"><?= number_format($totalGuests) ?></div>
    </div>
    <div class="stat-card" style="background:var(--bg-card); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-color); box-shadow:var(--shadow-sm);">
        <div style="font-size:12px; color:#10b981; text-transform:uppercase; font-weight:600;">Hadir (Attending)</div>
        <div style="font-size:26px; font-weight:700; color:#10b981; margin-top:4px;"><?= number_format($totalAttending) ?></div>
    </div>
    <div class="stat-card" style="background:var(--bg-card); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-color); box-shadow:var(--shadow-sm);">
        <div style="font-size:12px; color:#ef4444; text-transform:uppercase; font-weight:600;">Berhalangan (Declined)</div>
        <div style="font-size:26px; font-weight:700; color:#ef4444; margin-top:4px;"><?= number_format($totalDeclined) ?></div>
    </div>
    <div class="stat-card" style="background:var(--bg-card); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-color); box-shadow:var(--shadow-sm);">
        <div style="font-size:12px; color:#f59e0b; text-transform:uppercase; font-weight:600;">Belum Respons</div>
        <div style="font-size:26px; font-weight:700; color:#f59e0b; margin-top:4px;"><?= number_format($totalPending) ?></div>
    </div>
</div>

<!-- Action & Filter Bar -->
<div style="background:var(--bg-card); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-color); margin-bottom:20px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
    <!-- Left: Search & Filter Form -->
    <form action="<?= base_url('admin/invitations') ?>" method="GET" style="display:flex; flex-wrap:wrap; align-items:center; gap:10px; flex:1; min-width:320px;">
        <input type="hidden" name="sort" value="<?= esc($sortCol) ?>">
        <input type="hidden" name="dir" value="<?= esc($sortDir) ?>">

        <div style="position:relative; flex:1; min-width:200px; max-width:320px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, instansi, kode..." value="<?= esc($search) ?>" style="padding-left:36px; height:38px; font-size:13px;">
            <svg style="position:absolute; left:12px; top:11px; color:var(--text-muted);" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>

        <select name="rsvp_status" class="form-control" style="width:150px; height:38px; font-size:13px;" onchange="this.form.submit()">
            <option value="">Semua RSVP</option>
            <option value="pending" <?= $filterRsvp === 'pending' ? 'selected' : '' ?>>Belum Respons</option>
            <option value="attending" <?= $filterRsvp === 'attending' ? 'selected' : '' ?>>Hadir (Attending)</option>
            <option value="declined" <?= $filterRsvp === 'declined' ? 'selected' : '' ?>>Berhalangan</option>
        </select>

        <button type="submit" class="btn btn-outline" style="height:38px;">Filter</button>
        <?php if (!empty($search) || !empty($filterRsvp)): ?>
            <a href="<?= base_url('admin/invitations') ?>" class="btn btn-outline" style="height:38px; color:var(--text-muted);">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Right: Import, Export, Add Guest Buttons -->
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <button type="button" class="btn btn-outline" onclick="document.getElementById('importGuestModal').style.display='flex'" style="height:38px; font-size:13px;" title="Import Tamu dari File CSV">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Import CSV</span>
        </button>
        <a href="<?= base_url('admin/invitations/export') ?>" class="btn btn-outline" style="height:38px; font-size:13px;" title="Download Excel/CSV Rekapitulasi RSVP & Link Undangan">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>    
            <span>Export CSV</span>
        </a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addGuestModal').style.display='flex'" style="height:38px; font-size:13px;">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Tambah Tamu</span>
        </button>
    </div>
</div>

<!-- Interactive Guests Data Table with ASC/DESC Sorting -->
<?php
    function getSortLink($col, $currentCol, $currentDir, $baseUrl, $params) {
        $dir = ($currentCol === $col && $currentDir === 'ASC') ? 'DESC' : 'ASC';
        $params['sort'] = $col;
        $params['dir']  = $dir;
        $icon = '';
        if ($currentCol === $col) {
            $icon = $currentDir === 'ASC' ? ' &#9650;' : ' &#9660;';
        }
        return '<a href="' . $baseUrl . '?' . http_build_query($params) . '" style="color:inherit; text-decoration:none; display:inline-flex; align-items:center;">' . $icon . '</a>';
    }
    $queryParams = $_GET;
    $tableBaseUrl = base_url('admin/invitations');
    $feBaseUrl = $frontendBaseUrl ?? env('app.frontendMapURL', 'http://localhost:8082');
?>

<div style="background:var(--bg-surface); border-radius:var(--radius-card); border:1px solid var(--line); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
            <thead>
                <tr style="background:var(--bg-subtle); border-bottom:1px solid var(--line); color:var(--text-muted); font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">
                    <th style="padding:14px 16px; min-width:220px;">
                        Nama Tamu <?= getSortLink('name', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; min-width:180px;">
                        Instansi / Jabatan <?= getSortLink('institution', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; width:130px;">
                        Status RSVP <?= getSortLink('rsvp_status', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; min-width:200px;">
                        Catatan / Ucapan
                    </th>
                    <th style="padding:14px 16px; width:90px; text-align:center;">
                        Views <?= getSortLink('view_count', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; width:120px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($guests)): ?>
                    <tr>
                        <td colspan="6" style="padding:48px 16px; text-align:center; color:var(--text-muted);">
                            Tidak ada data tamu ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                        foreach ($guests as $g): 
                            $guestUrl = rtrim($feBaseUrl, '/') . '/invitation/' . $g['slug'];
                    ?>
                        <tr style="border-bottom:1px solid var(--line-soft); transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:12px 16px;">
                                <div style="font-weight:700; color:var(--text-strong); font-size:14px;"><?= esc($g['name']) ?></div>
                                <div style="color:var(--text-muted); font-size:12px; margin-top:2px;">
                                    <?= esc($g['email']) ?> <?= !empty($g['phone']) ? '• ' . esc($g['phone']) : '' ?>
                                </div>
                                <div style="font-family:monospace; font-size:11px; color:var(--action); margin-top:2px;">
                                    Code: <?= esc($g['invitation_code']) ?>
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="color:var(--text-body); font-weight:500;"><?= esc($g['institution'] ?: '—') ?></div>
                                <div style="color:var(--text-muted); font-size:12px;"><?= esc($g['position'] ?: '—') ?></div>
                            </td>
                            <td style="padding:12px 16px;">
                                <?php if ($g['rsvp_status'] === 'attending'): ?>
                                    <span style="background:var(--pos-bg); color:var(--pos); font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;">
                                        Hadir
                                    </span>
                                    <?php if (!empty($g['rsvp_at'])): ?>
                                        <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">
                                            <?= date('d/m H:i', strtotime($g['rsvp_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif ($g['rsvp_status'] === 'declined'): ?>
                                    <span style="background:var(--neg-bg); color:var(--neg); font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;">
                                        Berhalangan
                                    </span>
                                    <?php if (!empty($g['rsvp_at'])): ?>
                                        <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">
                                            <?= date('d/m H:i', strtotime($g['rsvp_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="background:var(--warn-bg); color:var(--warn); font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;">
                                        Belum Respons
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px 16px;">
                                <?php if (!empty($g['notes'])): ?>
                                    <div style="font-size:12px; color:var(--text-body); font-style:italic; max-width:280px; line-height:1.4;" title="<?= esc($g['notes']) ?>">
                                        "<?= esc($g['notes']) ?>"
                                    </div>
                                <?php else: ?>
                                    <span style="color:var(--text-muted); font-size:12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px 16px; text-align:center; font-weight:600; color:var(--text-strong);">
                                <?= number_format($g['view_count']) ?>
                            </td>
                            <td style="padding:12px 16px; text-align:right;">
                                <div style="display:inline-flex; gap:6px; align-items:center;">
                                    <a href="<?= esc($guestUrl) ?>" target="_blank" class="btn btn-outline" style="padding:4px 8px; font-size:12px;" title="Lihat E-Undangan Tamu">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                    <form id="deleteForm_<?= $g['id'] ?>" action="<?= base_url('admin/invitations/delete/' . $g['id']) ?>" method="POST" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="button" class="btn btn-danger" style="padding:4px 8px; font-size:12px;" onclick="confirmDeleteGuest(<?= $g['id'] ?>, '<?= esc($g['name']) ?>')" title="Hapus Tamu">
                                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Tamu -->
<div id="addGuestModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#ffffff; width:100%; max-width:540px; border-radius:12px; padding:24px 28px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2), 0 10px 10px -5px rgba(0,0,0,0.1); border:1px solid #e2e8f0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
            <h2 style="font-size:18px; font-weight:700; margin:0; color:#0f172a;">Tambah Tamu Undangan VIP</h2>
            <button type="button" onclick="document.getElementById('addGuestModal').style.display='none'" style="background:none; border:none; font-size:22px; cursor:pointer; color:#64748b; line-height:1;">&times;</button>
        </div>
        <form action="<?= base_url('admin/invitations/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Nama Lengkap & Gelar *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Prof. Dr. Ir. Budi Santoso, M.Sc.">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div class="form-group">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Alamat Email *</label>
                    <input type="email" name="email" class="form-control" required placeholder="budi@perusahaan.co.id">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Nomor WhatsApp</label>
                    <input type="text" name="phone" class="form-control" placeholder="0812-xxxx-xxxx">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div class="form-group">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Instansi / Lembaga</label>
                    <input type="text" name="institution" class="form-control" placeholder="Kementerian ESDM / PT Astra">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Jabatan</label>
                    <input type="text" name="position" class="form-control" placeholder="Direktur Utama / Menteri">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Assign Template Spesifik (Opsional)</label>
                <select name="template_id" class="form-control">
                    <option value="">Gunakan Template Default Acara</option>
                    <?php foreach ($activeTemplates as $tpl): ?>
                        <option value="<?= $tpl['id'] ?>"><?= esc($tpl['name']) ?> <?= $tpl['is_default'] ? '(Default)' : '' ?></option>
                    <?php endforeach; ?>
                </select>
                <small style="color:var(--text-muted); font-size:11px;">Jika dikosongkan, tamu akan otomatis menggunakan template default.</small>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="document.getElementById('addGuestModal').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Tamu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="importGuestModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#ffffff; width:100%; max-width:520px; border-radius:12px; padding:24px 28px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2), 0 10px 10px -5px rgba(0,0,0,0.1); border:1px solid #e2e8f0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:18px; font-weight:700; margin:0; color:#0f172a;">Import Tamu via CSV</h2>
            <button type="button" onclick="document.getElementById('importGuestModal').style.display='none'" style="background:none; border:none; font-size:22px; cursor:pointer; color:#64748b; line-height:1;">&times;</button>
        </div>
        
        <p style="font-size:13px; color:#64748b; line-height:1.5; margin:0 0 16px;">
            Unggah file CSV berisi data tamu undangan. Sistem akan otomatis men-generate kode dan slug unik untuk setiap tamu baru. Jika email sudah ada, data profil akan diperbarui tanpa mengubah link undangan atau status RSVP.
        </p>

        <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:12px 16px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:12px; color:#475569;">
                <strong>Gunakan format resmi:</strong> name, email, phone, institution, position
            </div>
            <a href="<?= base_url('admin/invitations/template-csv') ?>" class="btn btn-outline" style="padding:4px 10px; font-size:12px; white-space:nowrap;">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download Template
            </a>
        </div>

        <form action="<?= base_url('admin/invitations/import') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label" style="font-size:13px; font-weight:600;">Pilih File CSV *</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" required style="padding:8px 12px; height:auto;">
                <small style="color:var(--text-muted); font-size:11px;">Mendukung pemisah koma (,) dan titik koma (;).</small>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="document.getElementById('importGuestModal').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Mulai Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Modal: Confirmation Dialog -->
<div id="confirmActionModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:99999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#ffffff; width:100%; max-width:420px; border-radius:12px; padding:24px 28px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); border:1px solid #e2e8f0; text-align:center;">
        <div style="width:48px; height:48px; border-radius:50%; background:#e6edf3; color:#004b84; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <h3 id="confirmModalTitle" style="margin:0 0 8px; font-size:17px; font-weight:700; color:#0f172a;">Konfirmasi Aksi</h3>
        <p id="confirmModalMessage" style="font-size:13px; color:#64748b; line-height:1.5; margin:0 0 20px;">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
        <div style="display:flex; justify-content:center; gap:10px;">
            <button type="button" id="btnConfirmCancel" class="btn btn-outline" style="min-width:90px;" onclick="closeConfirmModal()">Batal</button>
            <button type="button" id="btnConfirmOk" class="btn btn-primary" style="min-width:90px;">Lanjutkan</button>
        </div>
    </div>
</div>

<!-- Custom Modal: Result Alert Dialog -->
<div id="alertActionModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:99999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#ffffff; width:100%; max-width:400px; border-radius:12px; padding:24px 28px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); border:1px solid #e2e8f0; text-align:center;">
        <div id="alertModalIcon" style="width:48px; height:48px; border-radius:50%; background:#e6f2ee; color:#2e7d64; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <h3 id="alertModalTitle" style="margin:0 0 8px; font-size:17px; font-weight:700; color:#0f172a;">Informasi</h3>
        <p id="alertModalMessage" style="font-size:13px; color:#64748b; line-height:1.5; margin:0 0 20px;"></p>
        <div style="display:flex; justify-content:center;">
            <button type="button" id="btnAlertOk" class="btn btn-primary" style="min-width:100px;" onclick="closeAlertModal()">OK</button>
        </div>
    </div>
</div>

<script>
let onConfirmCallback = null;
let onAlertCallback = null;

function showConfirmModal(title, message, okText, onConfirm) {
    document.getElementById('confirmModalTitle').textContent = title || 'Konfirmasi';
    document.getElementById('confirmModalMessage').textContent = message || '';
    const okBtn = document.getElementById('btnConfirmOk');
    okBtn.textContent = okText || 'Lanjutkan';
    onConfirmCallback = onConfirm;

    okBtn.onclick = function() {
        closeConfirmModal();
        if (typeof onConfirmCallback === 'function') onConfirmCallback();
    };

    document.getElementById('confirmActionModal').style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirmActionModal').style.display = 'none';
    onConfirmCallback = null;
}

function showAlertModal(title, message, isSuccess, onClose) {
    document.getElementById('alertModalTitle').textContent = title || 'Informasi';
    document.getElementById('alertModalMessage').textContent = message || '';
    const iconBox = document.getElementById('alertModalIcon');

    if (isSuccess) {
        iconBox.style.background = '#e6f2ee';
        iconBox.style.color = '#2e7d64';
        iconBox.innerHTML = '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
    } else {
        iconBox.style.background = '#f8eae9';
        iconBox.style.color = '#b5473d';
        iconBox.innerHTML = '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
    }

    onAlertCallback = onClose;
    document.getElementById('alertActionModal').style.display = 'flex';
}

function closeAlertModal() {
    document.getElementById('alertActionModal').style.display = 'none';
    if (typeof onAlertCallback === 'function') {
        const cb = onAlertCallback;
        onAlertCallback = null;
        cb();
    }
}

function confirmDeleteGuest(id, name) {
    showConfirmModal(
        'Hapus Data Tamu',
        'Apakah Anda yakin ingin menghapus data tamu ' + name + '?',
        'Hapus',
        function() {
            const form = document.getElementById('deleteForm_' + id);
            if (form) form.submit();
        }
    );
}
</script>

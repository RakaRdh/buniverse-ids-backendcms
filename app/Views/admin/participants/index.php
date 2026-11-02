<div class="page-header">
    <div>
        <h1 class="page-title">Participant Management</h1>
        <p class="page-subtitle">Daftar pendaftar, pencarian cepat, status kehadiran, dan verifikasi tiket</p>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <a href="<?= base_url('gate/portal_desk_ops_access_verify') ?>" target="_blank" class="btn btn-gold" style="display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"></path><rect x="7" y="7" width="10" height="10" rx="1"></rect></svg>
            <span>Buka QR Scanner</span>
        </a>
        <a href="<?= base_url('admin/participants/export') ?>" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Export CSV</span>
        </a>
    </div>
</div>

<!-- Stats Row -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px;">
    <div class="cms-card" style="padding:16px 20px; margin:0;">
        <div style="font-size:12px; font-weight:600; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:0.04em;">Total Pendaftar</div>
        <div style="font-size:24px; font-weight:800; color:var(--color-text-primary); margin-top:4px;"><?= number_format($totalRegistered) ?></div>
        <?php if (!empty($activeEvent['quota']) && (int)$activeEvent['quota'] > 0): ?>
            <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">Kuota: <?= number_format($activeEvent['quota']) ?></div>
        <?php endif; ?>
    </div>
    <div class="cms-card" style="padding:16px 20px; margin:0; border-left:4px solid var(--color-green);">
        <div style="font-size:12px; font-weight:600; color:var(--color-green); text-transform:uppercase; letter-spacing:0.04em;">Sudah Hadir (Visited)</div>
        <div style="font-size:24px; font-weight:800; color:var(--color-green); margin-top:4px;"><?= number_format($totalCheckedIn) ?></div>
    </div>
    <div class="cms-card" style="padding:16px 20px; margin:0; border-left:4px solid #f59e0b;">
        <div style="font-size:12px; font-weight:600; color:#f59e0b; text-transform:uppercase; letter-spacing:0.04em;">Belum Hadir</div>
        <div style="font-size:24px; font-weight:800; color:#f59e0b; margin-top:4px;"><?= number_format($unattended) ?></div>
    </div>
</div>

<!-- Filter & Search Toolbar -->
<div class="cms-card" style="margin-bottom:16px; padding:14px 18px;">
    <form method="GET" action="<?= base_url('admin/participants') ?>" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
        <div style="flex:1; min-width:240px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, no telp, instansi, atau kode tiket..." value="<?= esc($search ?? '') ?>">
        </div>
        <div style="width:160px;">
            <select name="is_checked_in" class="form-control">
                <option value="">Semua Kehadiran</option>
                <option value="1" <?= ($filterCheckedIn === '1') ? 'selected' : '' ?>>Sudah Hadir</option>
                <option value="0" <?= ($filterCheckedIn === '0') ? 'selected' : '' ?>>Belum Hadir</option>
            </select>
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if (!empty($search) || $filterCheckedIn !== null && $filterCheckedIn !== ''): ?>
                <a href="<?= base_url('admin/participants') ?>" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="cms-card">
    <div class="card-title-bar">
        <span class="card-title-text">Daftar Partisipan (<?= count($participants) ?>)</span>
    </div>
    <div class="table-responsive">
        <table class="cms-table">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Kode Tiket</th>
                    <th>Nama & Kontak</th>
                    <th>Jabatan & Instansi</th>
                    <th>PIC (Opsional)</th>
                    <th>Status Kehadiran</th>
                    <th>Waktu Regis</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($participants)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--color-text-muted);">
                            Belum ada partisipan terdaftar.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($participants as $p): ?>
                        <tr>
                            <td style="color:var(--color-text-muted); font-size:12px;"><?= $no++ ?></td>
                            <td>
                                <code style="font-weight:700; font-size:12.5px;"><?= esc($p['ticket_code']) ?></code>
                            </td>
                            <td>
                                <div style="font-weight:700; color:var(--color-text-primary);"><?= esc($p['full_name']) ?></div>
                                <div style="font-size:12px; color:var(--color-text-muted);"><?= esc($p['email']) ?></div>
                                <div style="font-size:11.5px; color:var(--color-text-muted);"><?= esc($p['phone']) ?></div>
                            </td>
                            <td>
                                <div style="font-weight:600;"><?= esc($p['company_name'] ?: '—') ?></div>
                                <div style="font-size:12px; color:var(--color-text-muted);"><?= esc($p['job_title'] ?: '—') ?></div>
                                <?php if (!empty($p['company_email'])): ?>
                                    <div style="font-size:11px; color:#15803d; font-weight:600;" title="Email Perusahaan">✉ <?= esc($p['company_email']) ?></div>
                                <?php endif; ?>
                                <div style="font-size:11px; color:var(--color-text-muted); max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= esc($p['address'] ?? '') ?>">
                                    <?= esc($p['address'] ?: '') ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($p['pic_name'])): ?>
                                    <div style="font-size:12px; font-weight:600;"><?= esc($p['pic_name']) ?></div>
                                    <div style="font-size:11.5px; color:var(--color-text-muted);"><?= esc($p['pic_phone'] ?: $p['pic_email']) ?></div>
                                <?php else: ?>
                                    <span style="color:var(--color-text-muted); font-size:12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['is_checked_in']): ?>
                                    <span class="status-pill status-published" style="display:inline-block; margin-bottom:4px;">
                                        Visited
                                    </span>
                                    <div style="font-size:11px; color:var(--color-text-muted);">
                                        <?= date('H:i, d M Y', strtotime($p['checked_in_at'])) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="status-pill status-draft">
                                        Not Yet
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px; color:var(--color-text-muted);">
                                <?= !empty($p['created_at']) ? date('d M Y H:i', strtotime($p['created_at'])) : '—' ?>
                            </td>
                            <td style="text-align: right; white-space:nowrap;">
                                <form action="<?= base_url('admin/participants/checkin/' . $p['id']) ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <?php if ($p['is_checked_in']): ?>
                                        <button type="submit" class="btn btn-outline btn-sm" title="Batalkan Check-in" onclick="return confirm('Batalkan status check-in peserta ini?');">Cancel In</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-gold btn-sm" title="Tandai Hadir">Mark Visited</button>
                                    <?php endif; ?>
                                </form>

                                <?php if ($currentAdmin['role'] !== 'receptionist'): ?>
                                    <form action="<?= base_url('admin/participants/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Hapus data partisipan ini?');" style="display:inline; margin-left:4px;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

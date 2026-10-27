<div class="content-header">
    <div class="header-left">
        <h1 class="page-title"><?= esc($pageTitle) ?></h1>
        <p class="page-desc">Kelola daftar penerima e-undangan VIP, konfirmasi kehadiran (RSVP), dan blast email resmi.</p>
    </div>
    <div class="header-right" style="display:flex; gap:8px;">
        <a href="<?= base_url('admin/invitations/export') ?>" class="btn btn-outline" title="Download Excel/CSV Rekapitulasi RSVP">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Export CSV</span>
        </a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addGuestModal').style.display='flex'">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Tambah Tamu</span>
        </button>
    </div>
</div>

<!-- Summary Metrics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin: 20px 0;">
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
    <div class="stat-card" style="background:var(--bg-card); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-color); box-shadow:var(--shadow-sm);">
        <div style="font-size:12px; color:#3b82f6; text-transform:uppercase; font-weight:600;">Email Terkirim</div>
        <div style="font-size:26px; font-weight:700; color:#3b82f6; margin-top:4px;"><?= number_format($totalSent) ?> / <?= number_format($totalGuests) ?></div>
    </div>
</div>

<!-- Filter Bar & Search -->
<div style="background:var(--bg-card); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-color); margin-bottom:20px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
    <form action="<?= base_url('admin/invitations') ?>" method="GET" style="display:flex; flex-wrap:wrap; align-items:center; gap:12px; flex:1;">
        <input type="hidden" name="sort" value="<?= esc($sortCol) ?>">
        <input type="hidden" name="dir" value="<?= esc($sortDir) ?>">

        <div style="position:relative; flex:1; min-width:240px; max-width:360px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, instansi, kode..." value="<?= esc($search) ?>" style="padding-left:36px; height:38px; font-size:13px;">
            <svg style="position:absolute; left:12px; top:11px; color:var(--text-muted);" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>

        <select name="rsvp_status" class="form-control" style="width:160px; height:38px; font-size:13px;" onchange="this.form.submit()">
            <option value="">Semua RSVP</option>
            <option value="pending" <?= $filterRsvp === 'pending' ? 'selected' : '' ?>>Belum Respons</option>
            <option value="attending" <?= $filterRsvp === 'attending' ? 'selected' : '' ?>>Hadir (Attending)</option>
            <option value="declined" <?= $filterRsvp === 'declined' ? 'selected' : '' ?>>Berhalangan</option>
        </select>

        <select name="blast_status" class="form-control" style="width:160px; height:38px; font-size:13px;" onchange="this.form.submit()">
            <option value="">Semua Status Blast</option>
            <option value="unsend" <?= $filterBlast === 'unsend' ? 'selected' : '' ?>>Belum Dikirim</option>
            <option value="sent" <?= $filterBlast === 'sent' ? 'selected' : '' ?>>Sudah Terkirim</option>
            <option value="failed" <?= $filterBlast === 'failed' ? 'selected' : '' ?>>Gagal Terkirim</option>
        </select>

        <button type="submit" class="btn btn-outline" style="height:38px;">Filter</button>
        <?php if (!empty($search) || !empty($filterRsvp) || !empty($filterBlast)): ?>
            <a href="<?= base_url('admin/invitations') ?>" class="btn btn-outline" style="height:38px; color:var(--text-muted);">Reset</a>
        <?php endif; ?>
    </form>

    <div style="display:flex; gap:8px;">
        <button type="button" class="btn btn-primary" id="btnBlastSelected" onclick="blastSelectedGuests()" style="font-size:13px; height:38px; background:linear-gradient(135deg, #10b981, #059669); border:none;" disabled>
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:4px;"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            Blast Terpilih (<span id="selectedCount">0</span>)
        </button>
    </div>
</div>

<!-- Interactive Guests Data Table with ASC/DESC Sorting -->
<?php
    // Helper to generate sort link
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
?>

<div style="background:var(--bg-surface); border-radius:var(--radius-card); border:1px solid var(--line); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; text-align:left; font-size:13px;">
            <thead>
                <tr style="background:var(--bg-subtle); border-bottom:1px solid var(--line); color:var(--text-muted); font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">
                    <th style="padding:14px 16px; width:40px;">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                    </th>
                    <th style="padding:14px 16px; min-width:200px;">
                        Nama Tamu <?= getSortLink('name', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; min-width:180px;">
                        Instansi / Jabatan <?= getSortLink('institution', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; width:130px;">
                        Status RSVP <?= getSortLink('rsvp_status', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; width:130px;">
                        Status Blast <?= getSortLink('blast_status', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; width:80px; text-align:center;">
                        Views <?= getSortLink('view_count', $sortCol, $sortDir, $tableBaseUrl, $queryParams) ?>
                    </th>
                    <th style="padding:14px 16px; width:150px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($guests)): ?>
                    <tr>
                        <td colspan="7" style="padding:48px 16px; text-align:center; color:var(--text-muted);">
                            Tidak ada data tamu ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                        $feBaseUrl = env('app.frontendMapURL', 'http://localhost:8082');
                        foreach ($guests as $g): 
                            $guestUrl = rtrim($feBaseUrl, '/') . '/invitation/' . $g['slug'];
                    ?>
                        <tr style="border-bottom:1px solid var(--line-soft); transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:12px 16px;">
                                <input type="checkbox" class="guest-checkbox" value="<?= $g['id'] ?>" onchange="updateSelectedCount()">
                            </td>
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
                                <?php elseif ($g['rsvp_status'] === 'declined'): ?>
                                    <span style="background:var(--neg-bg); color:var(--neg); font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;">
                                        Berhalangan
                                    </span>
                                <?php else: ?>
                                    <span style="background:var(--warn-bg); color:var(--warn); font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;">
                                        Belum Respons
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($g['notes'])): ?>
                                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= esc($g['notes']) ?>">
                                        "<?= esc($g['notes']) ?>"
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px 16px;">
                                <?php if ($g['blast_status'] === 'sent'): ?>
                                    <span style="background:#e6edf3; color:#004b84; font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;">
                                        Terkirim
                                    </span>
                                    <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">
                                        <?= date('d/m H:i', strtotime($g['blast_at'])) ?>
                                    </div>
                                <?php elseif ($g['blast_status'] === 'failed'): ?>
                                    <span style="background:var(--neg-bg); color:var(--neg); font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;" title="<?= esc($g['blast_error']) ?>">
                                        Gagal
                                    </span>
                                <?php else: ?>
                                    <span style="background:var(--bg-subtle); color:var(--text-muted); font-weight:600; padding:4px 10px; border-radius:50px; font-size:11px; display:inline-block;">
                                        Belum
                                    </span>
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
                                    <button type="button" class="btn btn-outline" style="padding:4px 8px; font-size:12px;" onclick="sendSingleGuestEmail(<?= $g['id'] ?>, '<?= esc($g['name']) ?>')" title="Kirim Ulang Email Undangan">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </button>
                                    <form action="<?= base_url('admin/invitations/delete/' . $g['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Hapus data tamu <?= esc($g['name']) ?>?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:12px;" title="Hapus Tamu">
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

<!-- Modal Progress Blast Batch -->
<div id="blastProgressModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:99999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#ffffff; width:100%; max-width:460px; border-radius:12px; padding:28px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); border:1px solid #e2e8f0; text-align:center;">
        <h3 style="margin:0 0 8px; font-size:18px; color:#0f172a;">Mengirimkan Email Undangan</h3>
        <p id="blastStatusText" style="font-size:13px; color:#64748b; margin-bottom:20px;">Sedang memproses pengiriman batch...</p>
        
        <div style="width:100%; height:10px; background:#f1f5f9; border-radius:10px; overflow:hidden; margin-bottom:16px;">
            <div id="blastProgressBar" style="width:0%; height:100%; background:linear-gradient(90deg, #10b981, #059669); transition:width 0.3s;"></div>
        </div>

        <div id="blastLogCount" style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:20px;">0 / 0 Selesai</div>
        <button type="button" id="btnBlastDone" class="btn btn-primary" style="display:none; margin:0 auto;" onclick="window.location.reload()">Selesai & Muat Ulang</button>
    </div>
</div>

<script>
function toggleSelectAll(master) {
    document.querySelectorAll('.guest-checkbox').forEach(cb => {
        cb.checked = master.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.guest-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('btnBlastSelected').disabled = (checked === 0);
}

function sendSingleGuestEmail(id, name) {
    if (!confirm('Kirim email undangan resmi ke ' + name + '?')) return;
    
    fetch('<?= base_url('admin/invitations/send-email/') ?>' + id, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') {
            window.location.reload();
        }
    })
    .catch(err => alert('Gagal mengirim email: ' + err));
}

async function blastSelectedGuests() {
    const checkedBoxes = Array.from(document.querySelectorAll('.guest-checkbox:checked'));
    const total = checkedBoxes.length;
    if (total === 0) return;

    if (!confirm('Kirim email blast ke ' + total + ' tamu terpilih?')) return;

    const guestIds = checkedBoxes.map(cb => parseInt(cb.value, 10));
    const chunkSize = 5; // Chunk size per batch
    let processedTotal = 0;

    const modal = document.getElementById('blastProgressModal');
    const bar = document.getElementById('blastProgressBar');
    const log = document.getElementById('blastLogCount');
    const doneBtn = document.getElementById('btnBlastDone');
    const statusText = document.getElementById('blastStatusText');

    modal.style.display = 'flex';
    doneBtn.style.display = 'none';

    for (let i = 0; i < guestIds.length; i += chunkSize) {
        const chunk = guestIds.slice(i, i + chunkSize);
        statusText.textContent = `Mengirim batch ${Math.floor(i / chunkSize) + 1} (${chunk.length} email)...`;

        try {
            const resp = await fetch('<?= base_url('admin/invitations/blast-batch') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ guest_ids: chunk })
            });
            const data = await resp.json();
            processedTotal += chunk.length;
        } catch (e) {
            console.error('Batch error', e);
            processedTotal += chunk.length;
        }

        const pct = Math.round((processedTotal / total) * 100);
        bar.style.width = pct + '%';
        log.textContent = `${processedTotal} / ${total} Selesai (${pct}%)`;
    }

    statusText.textContent = 'Seluruh proses blast email selesai!';
    doneBtn.style.display = 'inline-block';
}
</script>

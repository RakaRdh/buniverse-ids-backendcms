<div class="page-header">
    <div>
        <h1 class="page-title">Session Q&A Questions</h1>
        <p class="page-subtitle">Daftar pertanyaan yang dilontarkan oleh peserta melalui link/QR form QnA pada sesi acara <?= esc($activeEvent['name'] ?? $activeEvent['title'] ?? '') ?></p>
    </div>
</div>

<div class="cms-card">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="card-title-text">All Submitted Questions (<span id="qnaCount"><?= count($questions) ?></span>)</span>
        </div>
        <!-- Filter Bar -->
        <form method="GET" action="<?= base_url('admin/qna') ?>" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <select name="day_id" class="form-control" style="font-size: 13px; height: 36px; min-width: 150px;" onchange="this.form.submit()">
                <option value="">Semua Hari (All Days)</option>
                <?php if (!empty($days)): ?>
                    <?php foreach ($days as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= (!empty($filterDay) && $filterDay == $d['id']) ? 'selected' : '' ?>>
                            Day <?= esc($d['day_number']) ?> - <?= esc($d['label']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <select name="session_id" class="form-control" style="font-size: 13px; height: 36px; min-width: 200px;" onchange="this.form.submit()">
                <option value="">Semua Sesi (All Sessions)</option>
                <?php if (!empty($sessions)): ?>
                    <?php foreach ($sessions as $s): ?>
                        <?php if ($s['session_type'] === 'break') continue; ?>
                        <option value="<?= $s['id'] ?>" <?= (!empty($filterSession) && $filterSession == $s['id']) ? 'selected' : '' ?>>
                            <?= substr($s['start_time'], 0, 5) ?> - <?= esc($s['title']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <?php if (!empty($filterDay) || !empty($filterSession)): ?>
                <a href="<?= base_url('admin/qna') ?>" class="btn btn-outline btn-sm" style="height: 36px; display: inline-flex; align-items: center;">Reset Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="cms-table" id="qnaTable">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">#</th>
                    <th style="width: 170px;">Waktu & Hari</th>
                    <th style="width: 220px;">Sesi Acara</th>
                    <th style="width: 180px;">Nama Penanya</th>
                    <th>Isi Pertanyaan</th>
                    <th style="text-align: right; width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $idx => $q): ?>
                        <tr>
                            <td style="text-align: center; color: var(--text-muted); font-size: 13px;">
                                <?= $idx + 1 ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 13px; color: var(--text-strong);">
                                    <?= date('d M Y, H:i', strtotime($q['created_at'])) ?>
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    <?= !empty($q['day_number']) ? 'Day ' . esc($q['day_number']) . ' (' . esc($q['day_label']) . ')' : '—' ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 13px; color: var(--accent-gold); line-height: 1.4;">
                                    <?= esc($q['session_title'] ?: 'Sesi #' . $q['schedule_session_id']) ?>
                                </div>
                                <?php if (!empty($q['start_time'])): ?>
                                    <span style="font-size: 11px; color: var(--text-muted);">
                                        <?= substr($q['start_time'], 0, 5) ?> - <?= substr($q['end_time'], 0, 5) ?> WIB
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: var(--text-strong); font-size: 14px;">
                                    <?= esc($q['asker_name']) ?>
                                </strong>
                            </td>
                            <td>
                                <div style="color: var(--text-body); font-size: 13.5px; line-height: 1.5; white-space: pre-line; background: rgba(255, 255, 255, 0.02); padding: 8px 12px; border-radius: 6px; border: 1px solid var(--line);">
                                    <?= esc($q['question']) ?>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <form action="<?= base_url('admin/qna/delete/' . $q['id']) ?>" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?');" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger-outline btn-sm" title="Hapus pertanyaan spam/tes">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                            <div style="font-size: 15px; margin-bottom: 6px;">Belum ada pertanyaan yang masuk.</div>
                            <div style="font-size: 12px;">Pertanyaan dari peserta yang mengisi form QnA akan otomatis muncul di halaman ini.</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

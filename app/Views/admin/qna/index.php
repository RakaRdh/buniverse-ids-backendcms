<style>
    /* Desktop Table View & Mobile Card View switcher */
    .qna-desktop-view {
        display: block;
    }
    .qna-mobile-view {
        display: none;
    }

    @media (max-width: 768px) {
        .qna-desktop-view {
            display: none;
        }
        .qna-mobile-view {
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding: 12px 14px 18px 14px;
        }
    }

    .qna-card-item {
        background: var(--surface-card, #ffffff);
        border: 1px solid var(--line, #e2e8f0);
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .qna-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
    }

    .qna-card-asker {
        font-weight: 700;
        font-size: 14.5px;
        color: var(--text-strong, #0f172a);
        line-height: 1.3;
    }

    .qna-card-meta {
        font-size: 11.5px;
        color: var(--text-muted, #64748b);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 3px;
        flex-wrap: wrap;
    }

    .qna-card-session {
        background: rgba(14, 116, 144, 0.08);
        border-left: 3px solid var(--accent-gold, #0284c7);
        padding: 7px 10px;
        border-radius: 0 6px 6px 0;
        font-size: 12.5px;
    }

    .qna-card-session-title {
        font-weight: 600;
        color: var(--accent-gold, #0284c7);
        line-height: 1.35;
    }

    .qna-card-session-time {
        font-size: 11px;
        color: var(--text-muted, #64748b);
        margin-top: 2px;
    }

    .qna-card-question {
        color: var(--text-body, #334155);
        font-size: 13.5px;
        line-height: 1.55;
        white-space: pre-line;
        word-break: break-word;
    }

    .qna-card-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-top: 6px;
        border-top: 1px dashed var(--line, #e2e8f0);
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Session Q&A Questions</h1>
        <p class="page-subtitle">Daftar pertanyaan yang dilontarkan oleh peserta melalui link/QR form QnA pada sesi acara <?= esc($activeEvent['name'] ?? $activeEvent['title'] ?? '') ?></p>
    </div>
</div>

<div class="cms-card" style="padding: 0; overflow: hidden;">
    <div class="card-title-bar" style="padding: 16px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="card-title-text">All Submitted Questions (<span id="qnaCount"><?= count($questions) ?></span>)</span>
        </div>
        <!-- Filter & Search Bar Full Width / Responsive -->
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; width: 100%; margin-top: 4px;">
            <!-- Live Search Input (Full-Width Flex) -->
            <div style="position: relative; flex: 1 1 280px; min-width: 220px; width: 100%;">
                <input type="text" id="qnaSearchInput" class="form-control" placeholder="Search pertanyaan, nama penanya, atau sesi..." style="width: 100%; padding-left: 34px; font-size: 13px; height: 38px; border-radius: var(--radius-sm, 6px);">
                <svg style="position: absolute; left: 11px; top: 11px; width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>

            <!-- Server Filter Form (Full-Width Flex) -->
            <form method="GET" action="<?= base_url('admin/qna') ?>" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; flex: 2 1 400px; width: 100%; margin: 0;">
                <select name="day_id" class="form-control" style="flex: 1 1 150px; font-size: 13px; height: 38px; min-width: 140px; width: 100%;" onchange="this.form.submit()">
                    <option value="">Semua Hari (All Days)</option>
                    <?php if (!empty($days)): ?>
                        <?php foreach ($days as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= (!empty($filterDay) && $filterDay == $d['id']) ? 'selected' : '' ?>>
                                Day <?= esc($d['day_number']) ?> - <?= esc($d['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <select name="session_id" class="form-control" style="flex: 2 1 240px; font-size: 13px; height: 38px; min-width: 200px; width: 100%;" onchange="this.form.submit()">
                    <option value="">Semua Sesi (All Sessions)</option>
                    <?php if (!empty($sessions)): ?>
                        <?php 
                            // Group sessions by day_number and room_name
                            $groupedByDayAndRoom = [];
                            foreach ($sessions as $s) {
                                $dNum = $s['day_number'] ?? 1;
                                $rKey = !empty(trim($s['room_name'] ?? '')) ? trim($s['room_name']) : '';
                                $groupedByDayAndRoom[$dNum][$rKey][] = $s;
                            }
                        ?>
                        <?php foreach ($groupedByDayAndRoom as $dNum => $roomGroups): ?>
                            <?php foreach ($roomGroups as $rName => $sessList): ?>
                                <?php 
                                    $groupLabel = 'Day ' . $dNum . (!empty($rName) ? (' — ' . esc($rName)) : '');
                                ?>
                                <optgroup label="<?= $groupLabel ?>">
                                    <?php foreach ($sessList as $s): ?>
                                        <?php 
                                            $val = $s['id'] . (!empty($s['item_id']) ? ':' . $s['item_id'] : '');
                                        ?>
                                        <option value="<?= $val ?>" <?= (!empty($filterSession) && $filterSession == $val) ? 'selected' : '' ?>>
                                            [<?= esc($s['start_time']) ?>] <?= esc($s['title']) ?><?= !empty($s['subtitle']) ? ' — ' . esc($s['subtitle']) : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <?php if (!empty($filterDay) || !empty($filterSession)): ?>
                    <a href="<?= base_url('admin/qna') ?>" class="btn btn-outline btn-sm" style="height: 38px; display: inline-flex; align-items: center; white-space: nowrap;">Reset Filter</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- 1. DESKTOP VIEW: Table -->
    <div class="qna-desktop-view">
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
                <tbody id="qnaTableBody">
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $idx => $q): ?>
                            <tr class="qna-row-item">
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
                                    <div style="color: var(--text-body); font-size: 13.5px; line-height: 1.5; white-space: pre-line; word-break: break-word;"><?= esc(trim($q['question'])) ?></div>
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
                        <tr class="qna-empty-state">
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

    <!-- 2. MOBILE VIEW: Card List -->
    <div class="qna-mobile-view" id="qnaMobileContainer">
        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $idx => $q): ?>
                <div class="qna-card-item">
                    <div class="qna-card-header">
                        <div>
                            <div class="qna-card-asker">
                                <?= esc($q['asker_name']) ?>
                            </div>
                            <div class="qna-card-meta">
                                <span><?= date('d M Y, H:i', strtotime($q['created_at'])) ?></span>
                                <?php if (!empty($q['day_number'])): ?>
                                    <span>• Day <?= esc($q['day_number']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); padding: 2px 8px; background: rgba(0,0,0,0.04); border-radius: 4px;">
                            #<?= $idx + 1 ?>
                        </span>
                    </div>

                    <div class="qna-card-session">
                        <div class="qna-card-session-title">
                            <?= esc($q['session_title'] ?: 'Sesi #' . $q['schedule_session_id']) ?>
                        </div>
                        <?php if (!empty($q['start_time'])): ?>
                            <div class="qna-card-session-time">
                                <?= substr($q['start_time'], 0, 5) ?> - <?= substr($q['end_time'], 0, 5) ?> WIB
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="qna-card-question">
                        <?= esc(trim($q['question'])) ?>
                    </div>

                    <div class="qna-card-actions">
                        <form action="<?= base_url('admin/qna/delete/' . $q['id']) ?>" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?');" style="display:inline; margin: 0;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger-outline btn-sm" style="padding: 4px 12px; font-size: 12px;">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="qna-empty-state" style="text-align: center; color: var(--text-muted); padding: 40px 16px;">
                <div style="font-size: 14px; margin-bottom: 4px;">Belum ada pertanyaan yang masuk.</div>
                <div style="font-size: 12px;">Pertanyaan dari peserta yang mengisi form QnA akan otomatis muncul di sini.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('qnaSearchInput');
    const countDisplay = document.getElementById('qnaCount');
    const tableRows = document.querySelectorAll('#qnaTableBody tr.qna-row-item');
    const mobileCards = document.querySelectorAll('#qnaMobileContainer .qna-card-item');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            // Filter Desktop Table Rows
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (!query || text.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter Mobile Cards
            mobileCards.forEach(card => {
                const text = card.innerText.toLowerCase();
                if (!query || text.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            if (countDisplay) {
                countDisplay.textContent = visibleCount;
            }
        });
    }
});
</script>



<div class="page-header">
    <div>
        <h1 class="page-title">Summit Speakers</h1>
        <p class="page-subtitle">Drag and drop to rearrange speaker display order, or edit speaker details for <?= esc($activeEvent['name'] ?? '') ?></p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <span id="reorderStatus" style="font-size: 13px; font-weight: 600; color: #22c55e; display: none;">Order Saved!</span>
        <a href="<?= base_url('admin/speakers/new') ?>" class="btn btn-gold">+ Add New Speaker</a>
    </div>
</div>

<div class="cms-card">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span class="card-title-text">All Speakers (<span id="speakersCount"><?= count($speakers) ?></span>)</span>
            <span style="font-size: 12px; color: var(--text-muted);">Tip: Drag rows via &#9776; icon to reorder</span>
        </div>
        <div style="position: relative; width: 280px; max-width: 100%;">
            <input type="text" id="speakerSearchInput" class="form-control" placeholder="Search speaker or role..." style="padding-left: 32px; font-size: 13px; height: 36px; border-radius: var(--radius-sm);">
            <svg style="position: absolute; left: 10px; top: 10px; width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
    </div>
    <div class="table-responsive">
        <table class="cms-table" id="speakersTable">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th style="width: 70px;">Photo</th>
                    <th style="width: 220px;">Name</th>
                    <th style="width: 220px;">Role / Title</th>
                    <th>Assigned Session(s)</th>
                    <th style="width: 90px; text-align: center;">Status</th>
                    <th style="text-align: right; width: 170px;">Actions</th>
                </tr>
            </thead>
            <tbody id="sortableSpeakers">
                <?php if (!empty($speakers)): ?>
                    <?php foreach ($speakers as $spk): ?>
                        <tr id="speaker-<?= $spk['id'] ?>" data-id="<?= $spk['id'] ?>" class="draggable-speaker-row" style="cursor: move; scroll-margin-top: 80px; <?= empty($spk['is_active']) ? 'opacity: 0.65;' : '' ?>">
                            <td style="text-align: center; color: var(--text-muted); cursor: grab;" class="drag-handle" title="Drag to reorder">
                                <span style="font-size: 18px; line-height: 1;">&#9776;</span>
                            </td>
                            <td style="padding: 12px 16px">
                                <?php if (!empty($spk['photo_url'])): ?>
                                    <img src="<?= base_url(esc($spk['photo_url'])) ?>" alt="<?= esc($spk['name']) ?>" style="width: 64px; height: 85px; border-radius: var(--radius-card); object-fit: cover; background: var(--primary-1-7); border: 1.5px solid var(--line); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <?php else: ?>
                                    <div style="width: 64px; height: 85px; border-radius: var(--radius-card); background: var(--primary-1-7); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: #ffffff; border: 1.5px solid var(--line);">
                                        <img src="<?= base_url(esc('assets/img/placeholder.png')) ?>" alt="<?= esc($spk['name']) ?>" style="width: 64px; height: 85px; border-radius: var(--radius-card); object-fit: cover; background: var(--primary-1-7); border: 1.5px solid var(--line); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color: var(--text-strong); font-size: 14px;"><?= esc($spk['name']) ?></strong></td>
                            <td style="color: var(--text-body); font-size: 13px; line-height: 1.5; white-space: pre-line;"><?= esc($spk['role_text'] ?: '—') ?></td>
                            <td style="font-size: 12.5px; line-height: 1.45;">
                                <?php $assigned = $speakerSessions[$spk['id']] ?? []; ?>
                                <?php if (!empty($assigned)): ?>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <?php foreach ($assigned as $as): ?>
                                            <div style="display: inline-flex; align-items: baseline; flex-wrap: wrap; gap: 6px; background: rgba(1, 10, 45, 0.4); padding: 4px 8px; border-radius: 6px; border: 1px solid var(--line);">
                                                <span style="font-size: 10px; font-weight: 700; color: #38bdf8; background: rgba(56, 189, 248, 0.15); padding: 1px 5px; border-radius: 3px; white-space: nowrap;">
                                                    <?= esc($as['day_text'] ?: 'Session') ?>
                                                </span>
                                                <span style="color: var(--text-strong); font-weight: 600;">
                                                    <?= esc($as['session_title']) ?>
                                                    <?php if (!empty($as['item_title'])): ?>
                                                        <span style="color: var(--text-muted); font-weight: 400;"> • <?= esc($as['item_title']) ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($as['room_name'])): ?>
                                                        <span style="color: #c99400; font-size: 11px;"> (<?= esc($as['room_name']) ?>)</span>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-style: italic; font-size: 12px;">— Belum di-assign ke sesi</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if (!empty($spk['is_active'])): ?>
                                    <span class="badge" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600;">Aktif</span>
                                <?php else: ?>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600;">Off</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <form action="<?= base_url('admin/speakers/toggle/' . $spk['id']) ?>" method="POST" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-icon" title="<?= !empty($spk['is_active']) ? 'Nonaktifkan dari Frontend' : 'Aktifkan di Frontend' ?>" style="color: <?= !empty($spk['is_active']) ? '#fbbf24' : '#94a3b8' ?>; border: 1px solid var(--line); padding: 5px 8px; border-radius: 4px;">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                        </button>
                                    </form>
                                    <a href="<?= base_url('admin/speakers/edit/' . $spk['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="<?= base_url('admin/speakers/delete/' . $spk['id']) ?>" method="POST" onsubmit="return confirm('Delete this speaker?');" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No speakers found for this event. Click "+ Add New Speaker" to create one.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SortableJS library for smooth drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('sortableSpeakers');
    const statusMsg = document.getElementById('reorderStatus');
    if (!tbody || typeof Sortable === 'undefined') return;

    Sortable.create(tbody, {
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

            // Extract CSRF details from layout meta tags
            const csrfMetaName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
            const csrfMetaHash = document.querySelector('meta[name="csrf-hash"]');
            const csrfMetaHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';
            const currentHash = csrfMetaHash ? csrfMetaHash.getAttribute('content') : '<?= csrf_hash() ?>';

            // Post reordered array to backend
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
                statusMsg.textContent = 'Saving order...';
            }

            fetch('<?= base_url('admin/speakers/reorder') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfMetaHeader]: currentHash
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'success') {
                    if (data.csrf_hash && csrfMetaHash) {
                        csrfMetaHash.setAttribute('content', data.csrf_hash);
                    }
                    if (statusMsg) {
                        statusMsg.style.color = '#22c55e';
                        statusMsg.textContent = 'Order Saved!';
                        setTimeout(() => {
                            statusMsg.style.display = 'none';
                        }, 2500);
                    }
                } else {
                    if (statusMsg) {
                        statusMsg.style.color = '#ef4444';
                        statusMsg.textContent = (data && data.message) ? data.message : 'Failed to save order';
                    }
                }
            })
            .catch(err => {
                console.error('Reorder error:', err);
                if (statusMsg) {
                    statusMsg.style.color = '#ef4444';
                    statusMsg.textContent = 'Network error saving order';
                }
            });
        }
    });

    // Live search filter
    const searchInput = document.getElementById('speakerSearchInput');
    const countDisplay = document.getElementById('speakersCount');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr.draggable-speaker-row');
            let visibleCount = 0;

            rows.forEach(r => {
                const text = r.innerText.toLowerCase();
                if (!query || text.includes(query)) {
                    r.style.display = '';
                    visibleCount++;
                } else {
                    r.style.display = 'none';
                }
            });

            if (countDisplay) {
                countDisplay.textContent = visibleCount;
            }
        });
    }
});
</script>

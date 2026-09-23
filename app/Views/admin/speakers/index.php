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
                    <th style="width: 280px;">Name</th>
                    <th>Role / Title</th>
                    <th style="text-align: right; width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody id="sortableSpeakers">
                <?php if (!empty($speakers)): ?>
                    <?php foreach ($speakers as $spk): ?>
                        <tr data-id="<?= $spk['id'] ?>" class="draggable-speaker-row" style="cursor: move;">
                            <td style="text-align: center; color: var(--text-muted); cursor: grab;" class="drag-handle" title="Drag to reorder">
                                <span style="font-size: 18px; line-height: 1;">&#9776;</span>
                            </td>
                            <td>
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
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
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
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
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

            // Post reordered array to backend
            const formData = new FormData();
            order.forEach((id, idx) => {
                formData.append('order[' + idx + ']', id);
            });

            fetch('<?= base_url('admin/speakers/reorder') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'success') {
                    if (statusMsg) {
                        statusMsg.style.display = 'inline-block';
                        statusMsg.textContent = 'Order saved!';
                        setTimeout(() => {
                            statusMsg.style.display = 'none';
                        }, 2500);
                    }
                }
            })
            .catch(err => {
                console.error('Reorder error:', err);
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

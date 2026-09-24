<div class="page-header">
    <div>
        <h1 class="page-title">Summit Moderators</h1>
        <p class="page-subtitle">Separate master management for session moderators, facilitators, and news anchors for <?= esc($activeEvent['name'] ?? '') ?></p>
    </div>
    <a href="<?= base_url('admin/moderators/new') ?>" class="btn btn-gold">+ Add New Moderator</a>
</div>

<div class="cms-card">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <span class="card-title-text">All Moderators (<span id="moderatorsCount"><?= count($moderators) ?></span>)</span>
        <div style="position: relative; width: 280px; max-width: 100%;">
            <input type="text" id="moderatorSearchInput" class="form-control" placeholder="Search moderator or role..." style="padding-left: 32px; font-size: 13px; height: 36px; border-radius: var(--radius-sm);">
            <svg style="position: absolute; left: 10px; top: 10px; width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
    </div>
    <div class="table-responsive">
        <table class="cms-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th style="width: 280px;">Name</th>
                    <th>Role / Designation</th>
                    <th style="text-align: right; width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody id="moderatorsTableBody">
                <?php if (!empty($moderators)): ?>
                    <?php foreach ($moderators as $mod): ?>
                        <tr class="moderator-row-item">
                            <td style="padding: 12px 16px">
                                <?php if (!empty($mod['photo_url'])): ?>
                                    <img src="<?= base_url(esc($mod['photo_url'])) ?>" alt="<?= esc($mod['name']) ?>" style="width: 64px; height: 85px; border-radius: var(--radius-card); object-fit: cover; background: var(--primary-1-7); border: 1.5px solid var(--line); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <?php else: ?>
                                    <img src="<?= base_url(esc('assets/img/placeholder.png')) ?>" alt="<?= esc($mod['name']) ?>" style="width: 64px; height: 85px; border-radius: var(--radius-card); object-fit: cover; background: var(--primary-1-7); border: 1.5px solid var(--line); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <?php endif; ?>
                            </td>
                            <td><strong style="color: var(--text-strong); font-size: 14px;"><?= esc($mod['name']) ?></strong></td>
                            <td style="color: var(--text-body); font-size: 13px; line-height: 1.5; white-space: pre-line;"><?= esc($mod['role_text'] ?: '—') ?></td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="<?= base_url('admin/moderators/edit/' . $mod['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="<?= base_url('admin/moderators/delete/' . $mod['id']) ?>" method="POST" onsubmit="return confirm('Delete this moderator?');" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No moderators registered yet. Click "+ Add New Moderator" to register.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('moderatorSearchInput');
    const tbody = document.getElementById('moderatorsTableBody');
    const countDisplay = document.getElementById('moderatorsCount');
    if (!searchInput || !tbody) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = tbody.querySelectorAll('tr.moderator-row-item');
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
});
</script>

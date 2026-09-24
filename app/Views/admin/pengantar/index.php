<div class="page-header">
    <div>
        <h1 class="page-title">Master Pengantar</h1>
        <p class="page-subtitle">Separate master management for session introductory speakers (Pengantar) for <?= esc($activeEvent['name'] ?? $activeEvent['title'] ?? '') ?></p>
    </div>
    <a href="<?= base_url('admin/pengantar/new') ?>" class="btn btn-gold">+ Add New Pengantar</a>
</div>

<div class="cms-card">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <span class="card-title-text">All Pengantar (<span id="pengantarCount"><?= count($pengantar) ?></span>)</span>
        <div style="position: relative; width: 280px; max-width: 100%;">
            <input type="text" id="pengantarSearchInput" class="form-control" placeholder="Search pengantar or role..." style="padding-left: 32px; font-size: 13px; height: 36px; border-radius: var(--radius-sm);">
            <svg style="position: absolute; left: 10px; top: 10px; width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
    </div>
    <div class="table-responsive">
        <table class="cms-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th>Name</th>
                    <th>Role / Designation</th>
                    <th style="width: 90px; text-align: center;">Order</th>
                    <th style="width: 140px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody id="pengantarTableBody">
                <?php if (!empty($pengantar)): ?>
                    <?php foreach ($pengantar as $p): ?>
                        <tr class="pengantar-row-item">
                            <td style="padding: 12px 16px">
                                <?php if (!empty($p['photo_url'])): ?>
                                    <img src="<?= base_url($p['photo_url']) ?>" alt="<?= esc($p['name']) ?>" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                                <?php else: ?>
                                    <div style="width: 64px; height: 85px; border-radius: 50%; background: var(--bg-hover); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--text-muted); font-size: 14px;">
                                        <img src="<?= base_url(esc('assets/img/placeholder.png')) ?>" alt="<?= esc($p['name']) ?>" style="width: 64px; height: 85px; border-radius: var(--radius-card); object-fit: cover; background: var(--primary-1-7); border: 1.5px solid var(--line); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong class="pengantar-item-name" style="font-size: 14px;"><?= esc($p['name']) ?></strong>
                            </td>
                            <td>
                                <div class="pengantar-item-role" style="color: var(--text-muted); font-size: 12.5px; white-space: pre-line; line-height: 1.35;"><?= esc($p['role_text']) ?></div>
                            </td>
                            <td style="text-align: center; color: var(--text-muted); font-size: 13px;">
                                <?= (int)$p['sort_order'] ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="btn-group">
                                    <a href="<?= base_url('admin/pengantar/edit/' . $p['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="<?= base_url('admin/pengantar/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Delete this pengantar?');" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" style="margin-left: 4px;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="pengantarEmptyRow">
                        <td colspan="5" class="text-center" style="padding: 32px; color: var(--text-muted);">
                            No pengantar registered yet. Click "+ Add New Pengantar" to register.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('pengantarSearchInput');
    const tbody = document.getElementById('pengantarTableBody');
    const countDisplay = document.getElementById('pengantarCount');

    if (searchInput && tbody) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr.pengantar-row-item');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = (row.querySelector('.pengantar-item-name')?.textContent || '').toLowerCase();
                const role = (row.querySelector('.pengantar-item-role')?.textContent || '').toLowerCase();
                if (name.includes(query) || role.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (countDisplay) {
                countDisplay.textContent = visibleCount;
            }
        });
    }
});
</script>

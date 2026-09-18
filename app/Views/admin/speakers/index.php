<div class="page-header">
    <div>
        <h1 class="page-title">Summit Speakers</h1>
        <p class="page-subtitle">Manage speakers, keynote panelists, profile photos, and track ordering for <?= esc($activeEvent['name'] ?? '') ?></p>
    </div>
    <a href="<?= base_url('admin/speakers/new') ?>" class="btn btn-gold">+ Add New Speaker</a>
</div>

<div class="cms-card">
    <div class="card-title-bar">
        <span class="card-title-text">All Speakers (<?= count($speakers) ?>)</span>
    </div>
    <div class="table-responsive">
        <table class="cms-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th style="width: 280px;">Name</th>
                    <th>Role / Title</th>
                    <th style="text-align: right; width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($speakers)): ?>
                    <?php foreach ($speakers as $spk): ?>
                        <tr>
                            <td>
                                <?php if (!empty($spk['photo_url'])): ?>
                                    <img src="<?= base_url(esc($spk['photo_url'])) ?>" alt="<?= esc($spk['name']) ?>" style="width: 64px; height: 85px; border-radius: var(--radius-card); object-fit: cover; background: var(--primary-1-7); border: 1.5px solid var(--line); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <?php else: ?>
                                    <div style="width: 64px; height: 85px; border-radius: var(--radius-card); background: var(--primary-1-7); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: #ffffff; border: 1.5px solid var(--line);">
                                        <?= strtoupper(substr($spk['name'], 0, 1)) ?>
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
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No speakers found for this event. Click "+ Add New Speaker" to create one.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

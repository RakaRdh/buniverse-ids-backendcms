<div class="page-header">
    <div>
        <h1 class="page-title">Breakout & Stage Rooms</h1>
        <p class="page-subtitle">Manage session rooms and capacity tracks for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="openCreateRoomModal()">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Room
        </button>
    </div>
</div>

<div class="cms-card">
    <div class="card-title-bar">
        <span class="card-title-text">All Event Rooms (<?= count($rooms) ?>)</span>
    </div>
    <div class="cms-card-body" style="padding: 0;">
        <?php if (empty($rooms)): ?>
            <div style="padding: 40px; text-align: center; color: var(--text-muted);">
                <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" fill="none" stroke-width="1.5" style="margin-bottom: 12px; color: var(--text-faint);"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <div style="font-weight: 600; color: var(--text-strong); margin-bottom: 4px;">No rooms configured yet</div>
                <div style="font-size: 12px; margin-bottom: 16px;">Create breakout and plenary rooms to assign sessions and tracks.</div>
                <button type="button" class="btn btn-primary btn-sm" onclick="openCreateRoomModal()">+ Add First Room</button>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); background: var(--bg-subtle);">
                            <th style="padding: 10px 16px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600;">#</th>
                            <th style="padding: 10px 16px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600;">Room Name</th>
                            <th style="padding: 10px 16px; text-align: right; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $idx => $r): ?>
                            <tr style="border-bottom: 1px solid var(--line-soft);">
                                <td style="padding: 12px 16px; font-size: 13px; color: var(--text-muted);"><?= $idx + 1 ?></td>
                                <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--text-strong);">
                                    <?= esc($r['name']) ?>
                                </td>
                                <td style="padding: 12px 16px; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <button type="button" class="btn btn-outline btn-sm" onclick="openEditRoomModal(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') ?>)">
                                            Edit
                                        </button>
                                        <form action="<?= base_url('admin/rooms/delete/' . $r['id']) ?>" method="POST" onsubmit="return confirm('Delete room <?= esc($r['name']) ?>?');" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Add / Edit Room -->
<div id="roomModal" style="display: none; position: fixed; inset: 0; background: rgba(6, 46, 62, 0.4); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); border-radius: var(--radius-card); width: 100%; max-width: 440px; box-shadow: var(--shadow-pop); border: 1px solid var(--line); overflow: hidden; margin: 20px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between;">
            <h3 id="roomModalTitle" style="font-size: 15px; font-weight: 600; color: var(--text-strong); margin: 0;">Add Room</h3>
            <button type="button" onclick="closeRoomModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 20px; line-height: 1;">&times;</button>
        </div>
        <form id="roomModalForm" method="POST" action="<?= base_url('admin/rooms/save') ?>" style="padding: 20px;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Room Name *</label>
                <input type="text" name="name" id="roomNameInput" class="form-control" placeholder="e.g. Plenary Hall, Room A, Ballroom" required>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="closeRoomModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="roomModalSubmit">Save Room</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateRoomModal() {
    document.getElementById('roomModalTitle').innerText = 'Add New Room';
    document.getElementById('roomModalForm').action = '<?= base_url('admin/rooms/save') ?>';
    document.getElementById('roomNameInput').value = '';
    document.getElementById('roomModal').style.display = 'flex';
}

function openEditRoomModal(room) {
    document.getElementById('roomModalTitle').innerText = 'Edit Room';
    document.getElementById('roomModalForm').action = '<?= base_url('admin/rooms/update/') ?>' + room.id;
    document.getElementById('roomNameInput').value = room.name || '';
    document.getElementById('roomModal').style.display = 'flex';
}

function closeRoomModal() {
    document.getElementById('roomModal').style.display = 'none';
}
</script>

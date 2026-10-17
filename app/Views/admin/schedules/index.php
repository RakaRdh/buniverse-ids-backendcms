<div class="page-header">
    <div>
        <h1 class="page-title">Summit Schedule Tracks</h1>
        <p class="page-subtitle">Manage daily itineraries, breakout rooms, plenary sessions, and special session tracks for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
    <?php if ($selectedDayId): ?>
        <a href="<?= base_url('admin/schedules/session/new?day_id=' . $selectedDayId) ?>" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Session
        </a>
    <?php endif; ?>
</div>

<!-- Day Selector Bar -->
<div class="cms-card" style="margin-bottom: 20px;">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
        <span class="card-title-text">Event Days</span>
        <button type="button" onclick="document.getElementById('addDayModal').style.display='flex'" class="btn btn-outline btn-sm">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Day
        </button>
    </div>
    <div class="cms-card-body" style="padding: 14px 18px;">
        <?php if (!empty($days)): ?>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <?php foreach ($days as $d): 
                    $rawDate = $d['event_date'] ?? $d['date'] ?? null;
                    $formattedDate = $rawDate ? date('d M Y', strtotime($rawDate)) : 'TBD';
                    $rawDateInput = $rawDate ? date('Y-m-d', strtotime($rawDate)) : '';
                ?>
                    <div style="display: inline-flex; align-items: center; background: <?= $selectedDayId == $d['id'] ? 'var(--primary-1-1)' : 'var(--bg-subtle)' ?>; border: 1px solid <?= $selectedDayId == $d['id'] ? 'var(--primary-1-6)' : 'var(--line)' ?>; border-radius: var(--radius-control); overflow: hidden;">
                        <a href="<?= base_url('admin/schedules?day_id=' . $d['id']) ?>" class="btn <?= $selectedDayId == $d['id'] ? 'btn-primary' : 'btn-ghost' ?>" style="padding: 8px 14px; border-radius: 0; border: none; font-size: 13px;">
                            <strong>Day <?= esc($d['day_number']) ?></strong>: <?= esc($d['label']) ?> (<?= $formattedDate ?>)
                        </a>
                        <button type="button" 
                                onclick="openEditDayModal(<?= (int)$d['id'] ?>, <?= (int)$d['day_number'] ?>, '<?= esc($rawDateInput, 'js') ?>', '<?= esc($d['label'], 'js') ?>')" 
                                class="btn btn-ghost" 
                                title="Edit Day" 
                                style="padding: 8px 8px; border-radius: 0; border-left: 1px solid var(--line); color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <form action="<?= base_url('admin/schedules/day/delete/' . $d['id']) ?>" method="POST" onsubmit="return confirm('Delete this event day and all its sessions?');" style="display:inline; margin:0;">
                            <?= csrf_field() ?>
                            <button type="submit" 
                                    class="btn btn-ghost" 
                                    title="Delete Day" 
                                    style="padding: 8px 8px; border-radius: 0; border-left: 1px solid var(--line); color: var(--danger-text, #ef4444);">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); font-size: 12.5px;">No days added yet. Click "Add Day" to begin scheduling.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Sessions List for Selected Day -->
<div class="cms-card">
    <div class="card-title-bar">
        <span class="card-title-text">Sessions Itinerary (<?= count($sessions) ?> time blocks)</span>
    </div>
    <div class="cms-card-body" style="padding: 0;">
        <?php if (!empty($sessions)): ?>
            <div style="display: flex; flex-direction: column;">
                <?php foreach ($sessions as $s): ?>
                    <div id="session-<?= $s['id'] ?>" style="padding: 18px 20px; border-bottom: 1px solid var(--line); display: flex; gap: 24px; align-items: flex-start; scroll-margin-top: 80px; <?= ($s['session_type'] ?? '') === 'break' ? 'background: var(--bg-subtle);' : '' ?>">
                        <!-- Time & Room Badge Column (Fixed width so all rows align perfectly) -->
                        <div style="width: 220px; min-width: 220px; max-width: 220px; flex-shrink: 0; display: flex; flex-direction: column; gap: 6px;">
                            <div style="display: flex; width: 100%; box-sizing: border-box; align-items: center; justify-content: center; background: var(--primary-1-1); color: var(--primary-1-10); border: 1px solid var(--line); border-radius: var(--radius-control); padding: 6px 10px; font-size: 12.5px; font-weight: 700; font-variant-numeric: tabular-nums; text-align: center; white-space: nowrap;">
                                <?= format_schedule_time(null, $s['start_time'], $s['end_time']) ?>
                            </div>
                            <!-- Room Badge -->
                            <div style="display: flex; width: 100%; box-sizing: border-box; align-items: center; justify-content: center; gap: 5px; background: var(--bg-surface); color: var(--primary-1-7); border: 1px solid var(--line); border-radius: var(--radius-control); padding: 5px 10px; font-size: 11px; font-weight: 600; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <svg class="icon" style="width: 12px; height: 12px; flex-shrink: 0; stroke: currentColor;" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php if (!empty($s['session_rooms']) || !empty($s['children']) || ($s['session_type'] ?? '') === 'special_session'): ?>
                                        Special Session
                                    <?php else: ?>
                                        <?= esc($s['room_name'] ?: 'Main Stage') ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Session Info -->
                        <div style="flex: 1;">
                            <?php if (!empty($s['session_rooms'])): ?>
                                <!-- Multi-Room Breakout Tracks (Session -> Room -> Items Hierarchy) -->
                                <div style="font-size: 15px; font-weight: 700; color: var(--text-strong); margin-bottom: 12px;">
                                    <?= esc($s['title']) ?>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 14px;">
                                    <?php foreach ($s['session_rooms'] as $sRoom): ?>
                                        <div style="background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-card); padding: 16px 18px; box-shadow: var(--shadow-sm);">
                                            <!-- Room Pill Header matching left column badge -->
                                            <div style="margin-bottom: 12px;">
                                                <span style="display: inline-flex; align-items: center; gap: 6px; background: var(--bg-subtle); color: var(--primary-1-7); border: 1px solid var(--line); border-radius: var(--radius-control); padding: 4px 10px; font-size: 11.5px; font-weight: 700;">
                                                    <svg class="icon" style="width: 12px; height: 12px;" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                                    <?= esc($sRoom['room_name'] ?: 'Room Track') ?>
                                                </span>
                                            </div>

                                            <?php if (!empty($sRoom['items'])): ?>
                                                <div style="display: flex; flex-direction: column; gap: 14px;">
                                                    <?php foreach ($sRoom['items'] as $itemIdx => $it): ?>
                                                        <div style="<?= $itemIdx > 0 ? 'padding-top: 12px; border-top: 1px solid var(--line);' : '' ?>">
                                                            <!-- Title matching normal session -->
                                                            <div style="font-size: 14.5px; font-weight: 700; color: var(--text-strong); line-height: 1.4;">
                                                                <?= esc($it['title']) ?>
                                                            </div>

                                                            <?php if (!empty($it['subtitle'])): ?>
                                                                <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">
                                                                    <?= esc($it['subtitle']) ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- Moderator & Speakers matching normal session -->
                                                            <div style="display: flex; gap: 20px; margin-top: 8px; flex-wrap: wrap;">
                                                                <?php if (!empty($it['moderators'])): ?>
                                                                    <div style="font-size: 12px; color: var(--text-body);">
                                                                        <strong>Moderator:</strong> <?= implode(', ', array_column($it['moderators'], 'name')) ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (!empty($it['speakers'])): ?>
                                                                    <div style="font-size: 12px; color: var(--text-muted);">
                                                                        <strong>Speakers:</strong> <?= implode(', ', array_column($it['speakers'], 'name')) ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div style="font-size: 12px; color: var(--text-muted); font-style: italic;">No items in this room yet.</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif (!empty($s['children'])): ?>
                                <!-- Legacy Fallback Multi-Room Breakout Tracks -->
                                <div style="font-size: 15px; font-weight: 700; color: var(--text-strong); margin-bottom: 12px;">
                                    <?= esc($s['title']) ?>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 14px;">
                                    <?php foreach ($s['children'] as $child): ?>
                                        <div style="background: var(--bg-page); border: 1px solid var(--line); border-radius: var(--radius-control); padding: 14px;">
                                            <div style="font-size: 11px; font-weight: 700; color: var(--primary-1-7); text-transform: uppercase; display: flex; align-items: center; gap: 5px; margin-bottom: 6px;">
                                                <svg class="icon" style="width: 12px; height: 12px;" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                                <?= esc($child['room_name'] ?: 'Room Track') ?>
                                            </div>
                                            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-strong); margin-bottom: 4px;">
                                                <?= esc($child['title']) ?>
                                            </div>
                                            <?php if (!empty($child['subtitle'])): ?>
                                                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">
                                                    <?= esc($child['subtitle']) ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Child Moderators -->
                                            <?php if (!empty($child['moderators'])): ?>
                                                <div style="font-size: 11.5px; color: var(--text-body); margin-top: 6px;">
                                                    <strong>Moderator:</strong> <?= implode(', ', array_column($child['moderators'], 'name')) ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Child Speakers -->
                                            <?php if (!empty($child['speakers'])): ?>
                                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px;">
                                                    <strong>Speakers:</strong> <?= implode(', ', array_column($child['speakers'], 'name')) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <!-- Plenary / Keynote Session -->
                                <div style="display: flex; align-items: baseline; gap: 8px;">
                                    <div style="font-size: 15px; font-weight: 700; color: var(--text-strong);">
                                        <?= esc($s['title']) ?>
                                    </div>
                                </div>

                                <?php if (!empty($s['subtitle']) || !empty($s['description'])): ?>
                                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">
                                        <?= esc($s['subtitle'] ?? $s['description'] ?? '') ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Pengantar, Moderator & Speakers -->
                                <div style="display: flex; gap: 20px; margin-top: 10px; flex-wrap: wrap;">
                                    <?php if (!empty($s['pengantar'])): ?>
                                        <div style="font-size: 12px; color: var(--text-body);">
                                            <strong>Pengantar:</strong> <?= implode(', ', array_column($s['pengantar'], 'name')) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($s['moderators'])): ?>
                                        <div style="font-size: 12px; color: var(--text-body);">
                                            <strong>Moderator:</strong> <?= implode(', ', array_column($s['moderators'], 'name')) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($s['speakers'])): ?>
                                        <div style="font-size: 12px; color: var(--text-muted);">
                                            <strong>Speakers:</strong> <?= implode(', ', array_column($s['speakers'], 'name')) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Actions Column -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <a href="<?= base_url('admin/schedules/session/edit/' . $s['id']) ?>" class="btn btn-outline btn-sm">
                                Edit
                            </a>
                            <form action="<?= base_url('admin/schedules/session/delete/' . $s['id']) ?>" method="POST" onsubmit="return confirm('Delete this session block?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; color: var(--text-muted); padding: 40px;">
                No sessions scheduled for this day yet. Click "Add Session" to create keynote or panel tracks.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Add Day -->
<div id="addDayModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:400px; border-radius:var(--radius-lg); padding:20px; border:1px solid var(--line);">
        <h3 style="font-size:15px; margin-bottom:14px; color:var(--text-strong);">Add Event Day</h3>
        <form action="<?= base_url('admin/schedules/day/save') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Day Number (e.g. 1, 2)</label>
                <input type="number" name="day_number" class="form-control" required value="<?= count($days) + 1 ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Label (e.g. DAY 1)</label>
                <input type="text" name="label" class="form-control" required placeholder="DAY 1">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px; border-top:1px solid var(--line); padding-top:12px;">
                <button type="button" onclick="document.getElementById('addDayModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Day</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Day -->
<div id="editDayModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:400px; border-radius:var(--radius-lg); padding:20px; border:1px solid var(--line);">
        <h3 style="font-size:15px; margin-bottom:14px; color:var(--text-strong);">Edit Event Day</h3>
        <form id="editDayForm" action="" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Day Number</label>
                <input type="number" id="editDayNumber" name="day_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" id="editDayDate" name="event_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Label (e.g. DAY 1)</label>
                <input type="text" id="editDayLabel" name="label" class="form-control" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px; border-top:1px solid var(--line); padding-top:12px;">
                <button type="button" onclick="document.getElementById('editDayModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Day</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditDayModal(id, dayNumber, eventDate, label) {
    var form = document.getElementById('editDayForm');
    form.action = '<?= base_url('admin/schedules/day/update') ?>/' + id;
    document.getElementById('editDayNumber').value = dayNumber;
    document.getElementById('editDayDate').value = eventDate;
    document.getElementById('editDayLabel').value = label;
    document.getElementById('editDayModal').style.display = 'flex';
}
</script>

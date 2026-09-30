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
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <?php foreach ($days as $d): 
                    $rawDate = $d['event_date'] ?? $d['date'] ?? null;
                    $formattedDate = $rawDate ? date('d M Y', strtotime($rawDate)) : 'TBD';
                ?>
                    <a href="<?= base_url('admin/schedules?day_id=' . $d['id']) ?>" class="btn <?= $selectedDayId == $d['id'] ? 'btn-primary' : 'btn-outline' ?>" style="padding: 8px 16px;">
                        <strong>Day <?= esc($d['day_number']) ?></strong>: <?= esc($d['label']) ?> (<?= $formattedDate ?>)
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); font-size: 12.5px;">No days added yet. Click "Add Day" to begin scheduling.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Sessions List for Selected Day -->
<div class="cms-card">
    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="card-title-text">Sessions Itinerary (<?= count($sessions) ?> time blocks)</span>
            <span id="reorderStatus" style="font-size: 12px; font-weight: 600; display: none;"></span>
        </div>
        <?php if (!empty($sessions)): ?>
            <span style="font-size: 11.5px; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                <svg class="icon" style="width: 13px; height: 13px;" viewBox="0 0 24 24"><circle cx="9" cy="5" r="1"></circle><circle cx="9" cy="12" r="1"></circle><circle cx="9" cy="19" r="1"></circle><circle cx="15" cy="5" r="1"></circle><circle cx="15" cy="12" r="1"></circle><circle cx="15" cy="19" r="1"></circle></svg>
                Drag handle to reorder
            </span>
        <?php endif; ?>
    </div>
    <div class="cms-card-body" style="padding: 0;">
        <?php if (!empty($sessions)): ?>
            <div id="sortableSessions" style="display: flex; flex-direction: column;">
                <?php foreach ($sessions as $s): ?>
                    <div id="session-<?= $s['id'] ?>" data-id="<?= $s['id'] ?>" style="padding: 18px 20px; border-bottom: 1px solid var(--line); display: flex; gap: 16px; align-items: flex-start; scroll-margin-top: 80px; <?= ($s['session_type'] ?? '') === 'break' ? 'background: var(--bg-subtle);' : '' ?>">
                        <!-- Drag Handle Icon -->
                        <div class="drag-handle" style="cursor: grab; padding: 6px 2px; color: var(--text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none;" title="Drag to reorder">
                            <svg class="icon" style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="5" r="1"></circle><circle cx="9" cy="12" r="1"></circle><circle cx="9" cy="19" r="1"></circle>
                                <circle cx="15" cy="5" r="1"></circle><circle cx="15" cy="12" r="1"></circle><circle cx="15" cy="19" r="1"></circle>
                            </svg>
                        </div>

                        <!-- Time & Room Badge Column (Fixed width so all rows align perfectly) -->
                        <div style="width: 200px; min-width: 200px; max-width: 200px; flex-shrink: 0; display: flex; flex-direction: column; gap: 6px;">
                            <div style="display: flex; width: 100%; box-sizing: border-box; align-items: center; justify-content: center; background: var(--primary-1-1); color: var(--primary-1-10); border: 1px solid var(--line); border-radius: var(--radius-control); padding: 6px 10px; font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums; text-align: center; white-space: nowrap;">
                                <?= substr($s['start_time'], 0, 5) ?> - <?= substr($s['end_time'], 0, 5) ?> WIB
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

<!-- SortableJS library for smooth drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sessionContainer = document.getElementById('sortableSessions');
    const statusMsg = document.getElementById('reorderStatus');
    const selectedDayId = '<?= (int)($selectedDayId ?? 0) ?>';

    if (!sessionContainer || typeof Sortable === 'undefined' || !selectedDayId) return;

    Sortable.create(sessionContainer, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        onEnd: function() {
            const rows = sessionContainer.querySelectorAll('div[data-id]');
            const order = [];
            rows.forEach(r => {
                const id = r.getAttribute('data-id');
                if (id) order.push(id);
            });

            if (order.length === 0) return;

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
            formData.append('day_id', selectedDayId);
            order.forEach((id, idx) => {
                formData.append('order[' + idx + ']', id);
            });

            if (statusMsg) {
                statusMsg.style.display = 'inline-block';
                statusMsg.style.color = '#e5a900';
                statusMsg.textContent = 'Saving order...';
            }

            fetch('<?= base_url('admin/schedules/reorder') ?>', {
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
                console.error('Schedule reorder error:', err);
                if (statusMsg) {
                    statusMsg.style.color = '#ef4444';
                    statusMsg.textContent = 'Network error saving order';
                }
            });
        }
    });
});
</script>

<div class="page-header">
    <div>
        <h1 class="page-title"><?= $session ? 'Edit Schedule Session' : 'Add Schedule Session' ?></h1>
        <p class="page-subtitle">Configure session timing, track type (Plenary vs Special Session), and speakers/moderators</p>
    </div>
    <?php
    $backUrl = base_url('admin/schedules?day_id=' . ($selectedDayId ?? $dayId ?? ''));
    if (!empty($session['id'])) {
        $backUrl .= '#session-' . $session['id'];
    }
    ?>
    <a href="<?= $backUrl ?>" class="btn btn-outline" id="btnBackToSchedule">
        <svg class="icon" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Back to Schedules
    </a>
</div>

<?php
$isEdit = !empty($session);
$isSpecial = $isEdit && (($session['session_type'] ?? '') === 'special_session' || !empty($session['children']));
$formAction = $isEdit ? base_url('admin/schedules/session/update/' . $session['id']) : base_url('admin/schedules/session/save');

// Preloaded values
$selectedDayId = $session['schedule_day_id'] ?? $dayId ?? ($days[0]['id'] ?? '');
$startTimeVal = !empty($session['start_time']) ? substr($session['start_time'], 0, 5) : '09:00';
$endTimeVal   = !empty($session['end_time']) ? substr($session['end_time'], 0, 5) : '10:30';

// Current speakers, moderators & pengantar for plenary
$currentSpeakers = !empty($session['speakers']) ? array_map(function($s) {
    return (int)($s['speaker_id'] ?? $s['id'] ?? 0);
}, $session['speakers']) : [];
$currentModerators = !empty($session['moderators']) ? array_map(function($m) {
    return (int)($m['moderator_id'] ?? $m['id'] ?? 0);
}, $session['moderators']) : [];
$currentPengantar = !empty($session['pengantar']) ? array_map(function($p) {
    return (int)($p['pengantar_id'] ?? $p['id'] ?? 0);
}, $session['pengantar']) : [];

$currentSpeakers   = array_values(array_filter($currentSpeakers));
$currentModerators = array_values(array_filter($currentModerators));
$currentPengantar  = array_values(array_filter($currentPengantar));

// Candidate lookup JSON
$speakersList   = is_array($speakers ?? null) ? $speakers : [];
$moderatorsList = is_array($moderators ?? null) ? $moderators : [];
$pengantarList  = is_array($pengantar ?? null) ? $pengantar : [];
$roomsList      = is_array($rooms ?? null) ? $rooms : [];

$speakerListJson = json_encode(array_map(function($s) {
    return ['id' => $s['id'], 'name' => $s['name'], 'role' => $s['role_text'] ?? ''];
}, $speakersList));

$moderatorListJson = json_encode(array_map(function($m) {
    return ['id' => $m['id'], 'name' => $m['name'], 'role' => $m['role_text'] ?? ''];
}, $moderatorsList));

$pengantarListJson = json_encode(array_map(function($p) {
    return ['id' => $p['id'], 'name' => $p['name'], 'role' => $p['role_text'] ?? ''];
}, $pengantarList));

$roomsListJson = json_encode(array_map(function($r) {
    return ['id' => $r['id'], 'name' => $r['name']];
}, $roomsList));
?>

<div class="cms-card" style="max-width: 960px;">
    <div class="card-title-bar">
        <span class="card-title-text"><?= $isEdit ? 'Edit Session Block' : 'New Session Configuration' ?></span>
    </div>
    <div class="cms-card-body">
        <form action="<?= $formAction ?>" method="POST">
            <?= csrf_field() ?>

            <?php /* Session Type Switcher: Plenary vs Special Session */ ?>
            <div style="background: var(--bg-subtle); border: 1px solid var(--line); border-radius: var(--radius-card); padding: 16px; margin-bottom: 24px;">
                <label class="form-label" style="margin-bottom: 10px;">Select Session Track Type:</label>
                <div style="display: flex; gap: 32px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600; color:var(--text-strong);">
                        <input type="radio" name="flow_type" value="plenary" <?= !$isSpecial ? 'checked' : '' ?> onchange="toggleFlow('plenary')">
                        <span>Plenary Session</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600; color:var(--text-strong);">
                        <input type="radio" name="flow_type" value="special_session" <?= $isSpecial ? 'checked' : '' ?> onchange="toggleFlow('special_session')">
                        <span>Special Session</span>
                    </label>
                </div>
            </div>

            <?php /* Common Timing & Day */ ?>
            <div class="form-grid-3" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Event Day *</label>
                    <select name="schedule_day_id" class="form-control" required>
                        <?php foreach ($days as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($selectedDayId == $d['id']) ? 'selected' : '' ?>>
                                Day <?= esc($d['day_number']) ?> (<?= esc($d['label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Start Time (WIB) *</label>
                    <input type="time" name="start_time" class="form-control" required value="<?= esc($startTimeVal) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">End Time (WIB) *</label>
                    <input type="time" name="end_time" class="form-control" required value="<?= esc($endTimeVal) ?>">
                </div>
            </div>

            <?php /* ================= PLENARY SESSION CONTAINER ================= */ ?>
            <div id="plenaryContainer" style="<?= $isSpecial ? 'display:none;' : '' ?>">
                <div class="form-grid" style="margin-bottom: 14px;">
                    <div class="form-group">
                        <label class="form-label">Assigned Room</label>
                        <select name="room_id" class="form-control">
                            <option value="">-- No Room (Optional) --</option>
                            <?php foreach ($rooms as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= (($session['room_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                    <?= esc($r['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Session Title *</label>
                        <input type="text" name="title" class="form-control" value="<?= esc($session['title'] ?? '') ?>" placeholder="e.g. Plenary 1: Acceleration of Industrial Decarbonization">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Subtitle / Description</label>
                    <textarea name="subtitle" class="form-control" rows="3" placeholder="Session overview, keynote topics..."><?= esc($session['subtitle'] ?? $session['description'] ?? '') ?></textarea>
                </div>

                <?php /* Plenary Moderator Autocomplete Chips */ ?>
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="margin-bottom: 0;">Moderators</label>
                        <button type="button" class="btn btn-outline btn-sm" onclick="openQuickAddModal('moderator', 'plenaryModBox')" style="padding: 2px 8px; font-size: 11.5px;">+ Quick Add Moderator</button>
                    </div>
                    <div class="autocomplete-chips-container" id="plenaryModContainer">
                        <div class="autocomplete-chips-box" id="plenaryModBox" onclick="focusChipInput('plenaryModInput')">
                            <?php /* Chips will render here */ ?>
                            <input type="text" id="plenaryModInput" class="autocomplete-chips-input" placeholder="Type to search and add moderator...">
                        </div>
                        <div class="autocomplete-dropdown" id="plenaryModDropdown"></div>
                    </div>
                    <div class="help-text">Search moderator name. Drag and drop badge untuk ubah urutan.</div>
                </div>

                <?php /* Plenary Pengantar Autocomplete Chips */ ?>
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="margin-bottom: 0;">Pengantar (Optional)</label>
                        <button type="button" class="btn btn-outline btn-sm" onclick="openQuickAddModal('pengantar', 'plenaryPengantarBox')" style="padding: 2px 8px; font-size: 11.5px;">+ Quick Add Pengantar</button>
                    </div>
                    <div class="autocomplete-chips-container" id="plenaryPengantarContainer">
                        <div class="autocomplete-chips-box" id="plenaryPengantarBox" onclick="focusChipInput('plenaryPengantarInput')">
                            <?php /* Chips will render here */ ?>
                            <input type="text" id="plenaryPengantarInput" class="autocomplete-chips-input" placeholder="Type to search and add pengantar...">
                        </div>
                        <div class="autocomplete-dropdown" id="plenaryPengantarDropdown"></div>
                    </div>
                    <div class="help-text">Search pengantar name. Drag and drop badge untuk ubah urutan. Jika diisi, styling jadwal sesi akan menampilkan label MOD, Pengantar, dan Speaker.</div>
                </div>

                <?php /* Plenary Speakers Autocomplete Chips */ ?>
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="margin-bottom: 0;">Speakers</label>
                        <button type="button" class="btn btn-outline btn-sm" onclick="openQuickAddModal('speaker', 'plenarySpkBox')" style="padding: 2px 8px; font-size: 11.5px;">+ Quick Add Speaker</button>
                    </div>
                    <div class="autocomplete-chips-container" id="plenarySpkContainer">
                        <div class="autocomplete-chips-box" id="plenarySpkBox" onclick="focusChipInput('plenarySpkInput')">
                            <?php /* Chips will render here */ ?>
                            <input type="text" id="plenarySpkInput" class="autocomplete-chips-input" placeholder="Type to search and add speaker...">
                        </div>
                        <div class="autocomplete-dropdown" id="plenarySpkDropdown"></div>
                    </div>
                    <div class="help-text">Search speaker name. Drag and drop badge untuk ubah urutan.</div>
                </div>
            </div>

            <?php /* ================= SPECIAL SESSION CONTAINER (Session -> Room -> Items) ================= */ ?>
            <div id="specialSessionContainer" style="<?= !$isSpecial ? 'display:none;' : '' ?>">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Special Session Group Title *</label>
                    <input type="text" name="parent_title" class="form-control" value="<?= esc($session['title'] ?? 'Special Session / Multi-Track Breakouts') ?>" placeholder="e.g. Special Session A">
                </div>

                <div style="margin: 24px 0 16px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--line); padding-top: 18px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 700; color: var(--text-strong);">Active Rooms & Itineraries</h3>
                        <p style="font-size: 12px; color: var(--text-muted);">Add rooms active in this time slot, then add ordered keynote or panel items inside each room.</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addRoomCard()">
                        <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        + Add Room
                    </button>
                </div>

                <div id="roomsContainer" style="display: flex; flex-direction: column; gap: 18px;">
                    <?php /* Dynamic Room Cards Rendered via JS */ ?>
                </div>
            </div>

            <?php /* Action Buttons */ ?>
            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--line); display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" id="btnSubmitSession"><?= $isEdit ? 'Update Session' : 'Save Session Schedule' ?></button>
                <a href="<?= $backUrl ?>" class="btn btn-outline" id="btnCancelSession">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php /* ================= QUICK-ADD SPEAKER / MODERATOR MODAL ================= */ ?>
<div id="quickAddModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1050; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:var(--bg-surface); border:1px solid var(--line); border-radius:var(--radius-card); width:95%; max-width:480px; box-shadow:0 12px 36px rgba(0,0,0,0.5); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center;">
            <h3 id="quickAddTitle" style="font-size:15px; font-weight:700; color:var(--text-strong); margin:0;">Quick Add</h3>
            <button type="button" onclick="closeQuickAddModal()" style="background:none; border:none; font-size:20px; color:var(--text-muted); cursor:pointer;">&times;</button>
        </div>
        <form id="quickAddForm" onsubmit="handleQuickAddSubmit(event)" style="padding:20px;">
            <input type="hidden" id="quickAddType" value="speaker">
            <input type="hidden" id="quickAddTargetBoxId" value="">

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-weight:600; color:var(--text-strong);">Full Name *</label>
                <input type="text" id="quickAddName" class="form-control" required placeholder="e.g. John Doe, M.B.A">
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="font-weight:600; color:var(--text-strong);">Role / Title / Designation *</label>
                <textarea id="quickAddRole" class="form-control" rows="2" required placeholder="e.g. Minister of Finance / Executive Director"></textarea>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label" style="font-weight:600; color:var(--text-strong);">Photo (Optional)</label>
                <input type="file" id="quickAddPhoto" class="form-control" accept="image/*">
            </div>

            <div id="quickAddError" style="display:none; color:var(--danger); font-size:12.5px; margin-bottom:12px; font-weight:600;"></div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeQuickAddModal()" class="btn btn-outline btn-sm">Cancel</button>
                <button type="submit" id="quickAddSubmitBtn" class="btn btn-primary btn-sm">Save & Insert</button>
            </div>
        </form>
    </div>
</div>

<?php /* ================= DIRTY FORM EXIT CONFIRMATION MODAL ================= */ ?>
<div id="exitConfirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1060; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:var(--bg-surface); border:1px solid var(--line); border-radius:var(--radius-card); width:95%; max-width:440px; box-shadow:0 12px 36px rgba(0,0,0,0.5); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:15px; font-weight:700; color:var(--text-strong); margin:0;">Unsaved Changes</h3>
            <button type="button" onclick="closeExitConfirmModal()" style="background:none; border:none; font-size:20px; color:var(--text-muted); cursor:pointer;">&times;</button>
        </div>
        <div style="padding:20px;">
            <p style="font-size:13.5px; color:var(--text-body); line-height:1.5; margin:0 0 16px 0;">
                You have unsaved changes in this session form. Leaving now will discard all your edits. Are you sure you want to proceed?
            </p>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeExitConfirmModal()" class="btn btn-outline btn-sm">Stay on Form</button>
                <button type="button" id="btnConfirmLeave" class="btn btn-danger btn-sm">Leave Page</button>
            </div>
        </div>
    </div>
</div>

<script>
// Candidate Data
const allSpeakers = <?= $speakerListJson ?>;
const allModerators = <?= $moderatorListJson ?>;
const allPengantar = <?= $pengantarListJson ?>;
const allRooms = <?= $roomsListJson ?>;

// Existing plenary selections
const initialPlenarySpeakers = <?= json_encode($currentSpeakers) ?>;
const initialPlenaryModerators = <?= json_encode($currentModerators) ?>;
const initialPlenaryPengantar = <?= json_encode($currentPengantar) ?>;

// Existing special session rooms if edit mode
const initialSessionRooms = <?= json_encode(!empty($session['session_rooms']) ? array_map(function($sr) {
    return [
        'id'      => (int)($sr['id'] ?? 0),
        'room_id' => $sr['room_id'],
        'items'   => array_map(function($it) {
            return [
                'id'          => (int)($it['id'] ?? 0),
                'title'       => $it['title'],
                'subtitle'    => $it['subtitle'] ?? '',
                'moderators'  => array_values(array_filter(array_map(function($m) {
                    return (int)($m['moderator_id'] ?? $m['id'] ?? 0);
                }, $it['moderators'] ?? []))),
                'speakers'    => array_values(array_filter(array_map(function($s) {
                    return (int)($s['speaker_id'] ?? $s['id'] ?? 0);
                }, $it['speakers'] ?? []))),
            ];
        }, $sr['items'] ?? [])
    ];
}, $session['session_rooms']) : []) ?>;

function toggleFlow(type) {
    if (type === 'special_session') {
        document.getElementById('plenaryContainer').style.display = 'none';
        document.getElementById('specialSessionContainer').style.display = 'block';
        if (roomIndex === 0) {
            addRoomCard();
        }
    } else {
        document.getElementById('plenaryContainer').style.display = 'block';
        document.getElementById('specialSessionContainer').style.display = 'none';
    }
}

function focusChipInput(inputId) {
    const input = document.getElementById(inputId);
    if (input) input.focus();
}

// Global registry of chips instances so Quick Add can push newly created records
const chipsRegistry = {};

function setupAutocompleteChips(config) {
    const {
        boxId,
        inputId,
        dropdownId,
        inputName,
        items,
        initialIds = [],
    } = config;

    const box = document.getElementById(boxId);
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    if (!box || !input || !dropdown) return;

    // Ordered list of selected IDs (preserves order)
    let selectedIds = [...initialIds.map(Number)];

    // Register instance
    chipsRegistry[boxId] = {
        addId: function(newId) {
            newId = Number(newId);
            if (!selectedIds.includes(newId)) {
                selectedIds.push(newId);
                renderChips();
                markDirty();
            }
        },
        refreshItems: function(newItems) {
            config.items = newItems;
        }
    };

    // Drag & Drop tracking
    let draggedIndex = null;

    function renderChips() {
        box.querySelectorAll('.autocomplete-chip, input[type="hidden"]').forEach(el => el.remove());

        selectedIds.forEach((id, idx) => {
            const item = items.find(x => Number(x.id) === Number(id));
            if (!item) return;

            const chip = document.createElement('span');
            chip.className = 'autocomplete-chip';
            chip.title = (item.role ? item.role : item.name) + ' (Drag to reorder)';
            chip.setAttribute('draggable', 'true');
            chip.dataset.idx = idx;
            chip.style.cursor = 'grab';

            chip.innerHTML = `
                <span style="display:inline-flex; align-items:center; gap:4px; pointer-events:none;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6; flex-shrink:0;">
                        <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                        <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                        <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                    </svg>
                    <span>${escapeHtml(item.name)}</span>
                </span>
                <span class="chip-actions">
                    <button type="button" class="remove-chip" data-id="${item.id}" title="Remove">&times;</button>
                </span>
            `;

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = inputName;
            hiddenInput.value = item.id;

            // HTML5 Drag and Drop events
            chip.addEventListener('dragstart', (e) => {
                draggedIndex = idx;
                chip.style.opacity = '0.4';
                chip.style.cursor = 'grabbing';
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', idx);
            });

            chip.addEventListener('dragend', () => {
                chip.style.opacity = '1';
                chip.style.cursor = 'grab';
                box.querySelectorAll('.autocomplete-chip').forEach(c => {
                    c.style.borderLeft = '';
                    c.style.borderRight = '';
                });
            });

            chip.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                const rect = chip.getBoundingClientRect();
                const midX = rect.left + rect.width / 2;
                if (e.clientX < midX) {
                    chip.style.borderLeft = '2px solid var(--gold)';
                    chip.style.borderRight = '';
                } else {
                    chip.style.borderRight = '2px solid var(--gold)';
                    chip.style.borderLeft = '';
                }
            });

            chip.addEventListener('dragleave', () => {
                chip.style.borderLeft = '';
                chip.style.borderRight = '';
            });

            chip.addEventListener('drop', (e) => {
                e.preventDefault();
                chip.style.borderLeft = '';
                chip.style.borderRight = '';
                if (draggedIndex === null || draggedIndex === idx) return;

                const rect = chip.getBoundingClientRect();
                const midX = rect.left + rect.width / 2;
                let targetIdx = idx;
                if (e.clientX >= midX && draggedIndex < idx) {
                    targetIdx = idx;
                } else if (e.clientX >= midX && draggedIndex > idx) {
                    targetIdx = idx + 1;
                }

                const movedId = selectedIds.splice(draggedIndex, 1)[0];
                if (targetIdx > selectedIds.length) {
                    selectedIds.push(movedId);
                } else {
                    selectedIds.splice(targetIdx > draggedIndex ? targetIdx - 1 : targetIdx, 0, movedId);
                }

                draggedIndex = null;
                renderChips();
                markDirty();
            });

            chip.querySelector('.remove-chip').addEventListener('click', (e) => {
                e.stopPropagation();
                selectedIds.splice(idx, 1);
                renderChips();
                markDirty();
            });

            box.insertBefore(chip, input);
            box.appendChild(hiddenInput);
        });
    }

    function renderDropdown(filterText = '') {
        const query = filterText.toLowerCase().trim();
        const matches = items.filter(item => {
            if (selectedIds.includes(Number(item.id))) return false;
            if (!query) return true;
            return item.name.toLowerCase().includes(query) || (item.role && item.role.toLowerCase().includes(query));
        });

        if (matches.length === 0) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }

        dropdown.innerHTML = matches.map(item => `
            <div class="autocomplete-item" data-id="${item.id}">
                <div>
                    <strong>${escapeHtml(item.name)}</strong>
                    ${item.role ? `<div class="autocomplete-item-role">${escapeHtml(item.role)}</div>` : ''}
                </div>
                <span style="font-size: 11px; color: var(--text-muted);">&plus; Add</span>
            </div>
        `).join('');

        dropdown.style.display = 'block';

        dropdown.querySelectorAll('.autocomplete-item').forEach(el => {
            el.addEventListener('mousedown', (e) => {
                e.preventDefault();
                const id = Number(el.dataset.id);
                if (!selectedIds.includes(id)) {
                    selectedIds.push(id);
                }
                input.value = '';
                dropdown.style.display = 'none';
                renderChips();
                markDirty();
                input.focus();
            });
        });
    }

    input.addEventListener('input', () => {
        renderDropdown(input.value);
    });

    input.addEventListener('focus', () => {
        renderDropdown(input.value);
    });

    input.addEventListener('blur', () => {
        setTimeout(() => {
            dropdown.style.display = 'none';
        }, 200);
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && input.value === '' && selectedIds.length > 0) {
            selectedIds.pop();
            renderChips();
            markDirty();
        }
    });

    renderChips();
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>"'`=\/]/g, function (s) {
        return ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        })[s];
    });
}

// ================= HIERARCHICAL BUILDER (Room -> Items) =================
let roomIndex = 0;

function addRoomCard(roomData = null) {
    const rIdx = roomIndex++;
    const container = document.getElementById('roomsContainer');

    const card = document.createElement('div');
    card.className = 'breakthrough-track-card';
    card.id = `roomCard_${rIdx}`;
    card.style.background = 'var(--bg-surface)';
    card.style.border = '1px solid var(--line)';
    card.style.borderRadius = 'var(--radius-card)';
    card.style.padding = '18px';

    const roomOptions = allRooms.map(r => `
        <option value="${r.id}" ${roomData && Number(roomData.room_id) === Number(r.id) ? 'selected' : ''}>${escapeHtml(r.name)}</option>
    `).join('');

    card.innerHTML = `
        <input type="hidden" name="rooms[${rIdx}][id]" value="${roomData && roomData.id ? roomData.id : ''}">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; margin-bottom: 14px; border-bottom: 1px solid var(--line);">
            <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                <label style="font-weight: 700; color: var(--primary-1-7); font-size: 13.5px; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                    <svg class="icon" style="width: 16px; height: 16px;" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Room Card:
                </label>
                <select name="rooms[${rIdx}][room_id]" class="form-control" style="max-width: 320px;" required>
                    <option value="">-- Select Room Track --</option>
                    ${roomOptions}
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn btn-outline btn-sm" onclick="addItemToRoom(${rIdx})">
                    <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    + Add Item
                </button>
                <button type="button" class="btn btn-danger-outline btn-sm" onclick="removeRoomCard(${rIdx})">Remove Room</button>
            </div>
        </div>

        <!-- Room Items List -->
        <div id="roomItemsContainer_${rIdx}" style="display: flex; flex-direction: column; gap: 12px;">
            <!-- Items added here -->
        </div>
    `;

    container.appendChild(card);

    // If preloaded items exist
    if (roomData && roomData.items && roomData.items.length > 0) {
        roomData.items.forEach(it => addItemToRoom(rIdx, it));
    } else {
        // Default 1 empty item
        addItemToRoom(rIdx);
    }
}

function removeRoomCard(rIdx) {
    const card = document.getElementById(`roomCard_${rIdx}`);
    if (card) {
        const items = card.querySelectorAll('.room-item-row');
        if (items.length > 1) {
            if (!confirm('This room contains multiple items. Are you sure you want to remove it?')) {
                return;
            }
        }
        card.remove();
    }
}

let itemCounters = {};

function addItemToRoom(rIdx, itemData = null) {
    if (!itemCounters[rIdx]) {
        itemCounters[rIdx] = 0;
    }
    const itIdx = itemCounters[rIdx]++;
    const container = document.getElementById(`roomItemsContainer_${rIdx}`);
    if (!container) return;

    const itemRow = document.createElement('div');
    itemRow.className = 'room-item-row';
    itemRow.id = `roomItem_${rIdx}_${itIdx}`;
    itemRow.style.background = 'var(--bg-page)';
    itemRow.style.border = '1px solid var(--line)';
    itemRow.style.borderRadius = 'var(--radius-control)';
    itemRow.style.padding = '14px';

    itemRow.innerHTML = `
        <input type="hidden" name="rooms[${rIdx}][items][${itIdx}][id]" value="${itemData && itemData.id ? itemData.id : ''}">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-strong); display: flex; align-items: center; gap: 5px;">
                <span style="color: var(--primary-1-6);">&bull;</span> Item Content #${itIdx + 1}
            </span>
            <button type="button" class="btn btn-link btn-sm" style="color: var(--danger); padding: 0;" onclick="removeRoomItem(${rIdx}, ${itIdx})">&times; Delete Item</button>
        </div>

        <div class="form-grid" style="margin-bottom: 10px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Item Title *</label>
                <input type="text" name="rooms[${rIdx}][items][${itIdx}][title]" class="form-control" required value="${itemData ? escapeHtml(itemData.title) : ''}" placeholder="e.g. Keynote Speech A.1 / Panel Session A.1">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Subtitle / Focus (Optional)</label>
                <input type="text" name="rooms[${rIdx}][items][${itIdx}][subtitle]" class="form-control" value="${itemData && itemData.subtitle ? escapeHtml(itemData.subtitle) : ''}" placeholder="e.g. Anchoring Indonesia's Long-Term Development Ambitions">
            </div>
        </div>

        <!-- Moderators Autocomplete -->
        <div class="form-group" style="margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label class="form-label" style="margin-bottom: 0;">Moderator (Optional)</label>
                <button type="button" class="btn btn-outline btn-sm" onclick="openQuickAddModal('moderator', 'modBox_${rIdx}_${itIdx}')" style="padding: 2px 8px; font-size: 11px;">+ Quick Add Moderator</button>
            </div>
            <div class="autocomplete-chips-container" id="modCont_${rIdx}_${itIdx}">
                <div class="autocomplete-chips-box" id="modBox_${rIdx}_${itIdx}" onclick="focusChipInput('modInp_${rIdx}_${itIdx}')">
                    <input type="text" id="modInp_${rIdx}_${itIdx}" class="autocomplete-chips-input" placeholder="Type to search and add moderator...">
                </div>
                <div class="autocomplete-dropdown" id="modDrop_${rIdx}_${itIdx}"></div>
            </div>
        </div>

        <!-- Speakers Autocomplete -->
        <div class="form-group" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label class="form-label" style="margin-bottom: 0;">Speakers</label>
                <button type="button" class="btn btn-outline btn-sm" onclick="openQuickAddModal('speaker', 'spkBox_${rIdx}_${itIdx}')" style="padding: 2px 8px; font-size: 11px;">+ Quick Add Speaker</button>
            </div>
            <div class="autocomplete-chips-container" id="spkCont_${rIdx}_${itIdx}">
                <div class="autocomplete-chips-box" id="spkBox_${rIdx}_${itIdx}" onclick="focusChipInput('spkInp_${rIdx}_${itIdx}')">
                    <input type="text" id="spkInp_${rIdx}_${itIdx}" class="autocomplete-chips-input" placeholder="Type to search and add speakers...">
                </div>
                <div class="autocomplete-dropdown" id="spkDrop_${rIdx}_${itIdx}"></div>
            </div>
        </div>
    `;

    container.appendChild(itemRow);

    // Initialize Autocomplete Chips for this item
    setupAutocompleteChips({
        boxId: `modBox_${rIdx}_${itIdx}`,
        inputId: `modInp_${rIdx}_${itIdx}`,
        dropdownId: `modDrop_${rIdx}_${itIdx}`,
        inputName: `rooms[${rIdx}][items][${itIdx}][moderator_id][]`,
        items: allModerators,
        initialIds: itemData ? itemData.moderators : [],
    });

    setupAutocompleteChips({
        boxId: `spkBox_${rIdx}_${itIdx}`,
        inputId: `spkInp_${rIdx}_${itIdx}`,
        dropdownId: `spkDrop_${rIdx}_${itIdx}`,
        inputName: `rooms[${rIdx}][items][${itIdx}][speakers][]`,
        items: allSpeakers,
        initialIds: itemData ? itemData.speakers : [],
    });
}

function removeRoomItem(rIdx, itIdx) {
    const item = document.getElementById(`roomItem_${rIdx}_${itIdx}`);
    if (item) item.remove();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Plenary Autocomplete
    setupAutocompleteChips({
        boxId: 'plenaryModBox',
        inputId: 'plenaryModInput',
        dropdownId: 'plenaryModDropdown',
        inputName: 'moderators[]',
        items: allModerators,
        initialIds: initialPlenaryModerators,
    });

    setupAutocompleteChips({
        boxId: 'plenaryPengantarBox',
        inputId: 'plenaryPengantarInput',
        dropdownId: 'plenaryPengantarDropdown',
        inputName: 'pengantar[]',
        items: allPengantar,
        initialIds: initialPlenaryPengantar,
    });

    setupAutocompleteChips({
        boxId: 'plenarySpkBox',
        inputId: 'plenarySpkInput',
        dropdownId: 'plenarySpkDropdown',
        inputName: 'speakers[]',
        items: allSpeakers,
        initialIds: initialPlenarySpeakers,
    });

    // If edit mode with rooms, render existing rooms & items
    if (initialSessionRooms && initialSessionRooms.length > 0) {
        initialSessionRooms.forEach(sr => addRoomCard(sr));
    }

    // Attach dirty form detectors
    const form = document.querySelector('form[action*="schedules/session"]');
    if (form) {
        form.addEventListener('input', markDirty);
        form.addEventListener('change', markDirty);
        form.addEventListener('submit', () => {
            isFormDirty = false;
        });
    }

    // Intercept Back & Cancel links
    const backBtn = document.getElementById('btnBackToSchedule');
    const cancelBtn = document.getElementById('btnCancelSession');
    [backBtn, cancelBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', (e) => {
                if (isFormDirty) {
                    e.preventDefault();
                    pendingExitUrl = btn.getAttribute('href');
                    openExitConfirmModal();
                }
            });
        }
    });

    // Also prompt browser on tab close if dirty
    window.addEventListener('beforeunload', (e) => {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});

// ================= DIRTY FORM STATE =================
let isFormDirty = false;
let pendingExitUrl = null;

function markDirty() {
    isFormDirty = true;
}

function openExitConfirmModal() {
    const modal = document.getElementById('exitConfirmModal');
    if (modal) modal.style.display = 'flex';
}

function closeExitConfirmModal() {
    const modal = document.getElementById('exitConfirmModal');
    if (modal) modal.style.display = 'none';
    pendingExitUrl = null;
}

document.addEventListener('DOMContentLoaded', () => {
    const leaveBtn = document.getElementById('btnConfirmLeave');
    if (leaveBtn) {
        leaveBtn.addEventListener('click', () => {
            isFormDirty = false;
            if (pendingExitUrl) {
                window.location.href = pendingExitUrl;
            } else {
                closeExitConfirmModal();
            }
        });
    }
});

// ================= QUICK-ADD AJAX LOGIC =================
function openQuickAddModal(type, targetBoxId) {
    const modal = document.getElementById('quickAddModal');
    const title = document.getElementById('quickAddTitle');
    const typeInp = document.getElementById('quickAddType');
    const targetBoxInp = document.getElementById('quickAddTargetBoxId');
    const errorDiv = document.getElementById('quickAddError');

    if (!modal) return;

    typeInp.value = type;
    targetBoxInp.value = targetBoxId;
    if (type === 'speaker') {
        title.textContent = 'Quick Add New Speaker';
    } else if (type === 'pengantar') {
        title.textContent = 'Quick Add New Pengantar';
    } else {
        title.textContent = 'Quick Add New Moderator';
    }
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';
    document.getElementById('quickAddForm').reset();
    typeInp.value = type;
    targetBoxInp.value = targetBoxId;

    modal.style.display = 'flex';
    setTimeout(() => {
        document.getElementById('quickAddName').focus();
    }, 100);
}

function closeQuickAddModal() {
    const modal = document.getElementById('quickAddModal');
    if (modal) modal.style.display = 'none';
}

function handleQuickAddSubmit(e) {
    e.preventDefault();
    const type = document.getElementById('quickAddType').value;
    const targetBoxId = document.getElementById('quickAddTargetBoxId').value;
    const name = document.getElementById('quickAddName').value.trim();
    const role = document.getElementById('quickAddRole').value.trim();
    const photoFile = document.getElementById('quickAddPhoto').files[0];
    const submitBtn = document.getElementById('quickAddSubmitBtn');
    const errorDiv = document.getElementById('quickAddError');

    if (!name || !role) {
        errorDiv.textContent = 'Name and Role are required.';
        errorDiv.style.display = 'block';
        return;
    }

    let endpoint = '<?= base_url('admin/moderators/quick-save') ?>';
    if (type === 'speaker') {
        endpoint = '<?= base_url('admin/speakers/quick-save') ?>';
    } else if (type === 'pengantar') {
        endpoint = '<?= base_url('admin/pengantar/quick-save') ?>';
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('role_text', role);
    if (photoFile) {
        formData.append('photo_file', photoFile);
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    errorDiv.style.display = 'none';

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save & Insert';

        if (data && data.success) {
            const newRecord = data.speaker || data.moderator || data.pengantar;
            if (type === 'speaker') {
                allSpeakers.push(newRecord);
            } else if (type === 'pengantar') {
                allPengantar.push(newRecord);
            } else {
                allModerators.push(newRecord);
            }

            // If target chip container is registered, insert the new chip immediately
            if (targetBoxId && chipsRegistry[targetBoxId]) {
                chipsRegistry[targetBoxId].addId(newRecord.id);
            }

            closeQuickAddModal();
        } else {
            errorDiv.textContent = data.message || 'Failed to save record.';
            errorDiv.style.display = 'block';
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save & Insert';
        errorDiv.textContent = 'An error occurred while communicating with the server.';
        errorDiv.style.display = 'block';
        console.error('Quick Add error:', err);
    });
}
</script>

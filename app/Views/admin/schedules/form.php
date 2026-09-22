<div class="page-header">
    <div>
        <h1 class="page-title"><?= $session ? 'Edit Schedule Session' : 'Add Schedule Session' ?></h1>
        <p class="page-subtitle">Configure session timing, track type (Plenary vs Special Session), and speakers/moderators</p>
    </div>
    <a href="<?= base_url('admin/schedules?day_id=' . ($dayId ?? '')) ?>" class="btn btn-outline">
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

// Current speakers & moderators for plenary
$currentSpeakers = !empty($session['speakers']) ? array_column($session['speakers'], 'id') : [];
$currentModerators = !empty($session['moderators']) ? array_column($session['moderators'], 'id') : [];

// Candidate lookup JSON
$speakersList = is_array($speakers ?? null) ? $speakers : [];
$moderatorsList = is_array($moderators ?? null) ? $moderators : [];
$roomsList = is_array($rooms ?? null) ? $rooms : [];

$speakerListJson = json_encode(array_map(function($s) {
    return ['id' => $s['id'], 'name' => $s['name'], 'role' => $s['role_text'] ?? ''];
}, $speakersList));

$moderatorListJson = json_encode(array_map(function($m) {
    return ['id' => $m['id'], 'name' => $m['name'], 'role' => $m['role_text'] ?? ''];
}, $moderatorsList));

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

            <!-- Session Type Switcher: Plenary vs Special Session -->
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

            <!-- Common Timing & Day -->
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

            <!-- ================= PLENARY SESSION CONTAINER ================= -->
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

                <!-- Plenary Moderator Autocomplete Chips -->
                <div class="form-group">
                    <label class="form-label">Moderators</label>
                    <div class="autocomplete-chips-container" id="plenaryModContainer">
                        <div class="autocomplete-chips-box" id="plenaryModBox" onclick="focusChipInput('plenaryModInput')">
                            <!-- Chips will render here -->
                            <input type="text" id="plenaryModInput" class="autocomplete-chips-input" placeholder="Type to search and add moderator...">
                        </div>
                        <div class="autocomplete-dropdown" id="plenaryModDropdown"></div>
                    </div>
                    <div class="help-text">Search moderator name. Click or press enter to add tag chip.</div>
                </div>

                <!-- Plenary Speakers Autocomplete Chips -->
                <div class="form-group">
                    <label class="form-label">Speakers</label>
                    <div class="autocomplete-chips-container" id="plenarySpkContainer">
                        <div class="autocomplete-chips-box" id="plenarySpkBox" onclick="focusChipInput('plenarySpkInput')">
                            <!-- Chips will render here -->
                            <input type="text" id="plenarySpkInput" class="autocomplete-chips-input" placeholder="Type to search and add speaker...">
                        </div>
                        <div class="autocomplete-dropdown" id="plenarySpkDropdown"></div>
                    </div>
                    <div class="help-text">Search speaker name. Add multiple speakers as tag chips.</div>
                </div>
            </div>

            <!-- ================= SPECIAL SESSION CONTAINER (Session -> Room -> Items) ================= -->
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
                    <!-- Dynamic Room Cards Rendered via JS -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--line); display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Session' : 'Save Session Schedule' ?></button>
                <a href="<?= base_url('admin/schedules?day_id=' . ($selectedDayId ?? '')) ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Candidate Data
const allSpeakers = <?= $speakerListJson ?>;
const allModerators = <?= $moderatorListJson ?>;
const allRooms = <?= $roomsListJson ?>;

// Existing plenary selections
const initialPlenarySpeakers = <?= json_encode($currentSpeakers) ?>;
const initialPlenaryModerators = <?= json_encode($currentModerators) ?>;

// Existing special session rooms if edit mode
const initialSessionRooms = <?= json_encode(!empty($session['session_rooms']) ? array_map(function($sr) {
    return [
        'room_id' => $sr['room_id'],
        'items'   => array_map(function($it) {
            return [
                'title'       => $it['title'],
                'subtitle'    => $it['subtitle'] ?? '',
                'moderators'  => array_column($it['moderators'] ?? [], 'id'),
                'speakers'    => array_column($it['speakers'] ?? [], 'id'),
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

/**
 * Autocomplete Chip Component Builder
 */
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

    let selectedIds = new Set(initialIds.map(Number));

    function renderChips() {
        box.querySelectorAll('.autocomplete-chip, input[type="hidden"]').forEach(el => el.remove());

        selectedIds.forEach(id => {
            const item = items.find(x => Number(x.id) === Number(id));
            if (!item) return;

            const chip = document.createElement('span');
            chip.className = 'autocomplete-chip';
            chip.innerHTML = `
                <span>${escapeHtml(item.name)}</span>
                <button type="button" class="remove-chip" data-id="${item.id}">&times;</button>
            `;

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = inputName;
            hiddenInput.value = item.id;

            chip.querySelector('.remove-chip').addEventListener('click', (e) => {
                e.stopPropagation();
                selectedIds.delete(Number(item.id));
                renderChips();
            });

            box.insertBefore(chip, input);
            box.appendChild(hiddenInput);
        });
    }

    function renderDropdown(filterText = '') {
        const query = filterText.toLowerCase().trim();
        const matches = items.filter(item => {
            if (selectedIds.has(Number(item.id))) return false;
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
                selectedIds.add(id);
                input.value = '';
                dropdown.style.display = 'none';
                renderChips();
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
        if (e.key === 'Backspace' && input.value === '' && selectedIds.size > 0) {
            const arr = Array.from(selectedIds);
            selectedIds.delete(arr[arr.length - 1]);
            renderChips();
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
            <label class="form-label">Moderator (Optional)</label>
            <div class="autocomplete-chips-container" id="modCont_${rIdx}_${itIdx}">
                <div class="autocomplete-chips-box" id="modBox_${rIdx}_${itIdx}" onclick="focusChipInput('modInp_${rIdx}_${itIdx}')">
                    <input type="text" id="modInp_${rIdx}_${itIdx}" class="autocomplete-chips-input" placeholder="Type to search and add moderator...">
                </div>
                <div class="autocomplete-dropdown" id="modDrop_${rIdx}_${itIdx}"></div>
            </div>
        </div>

        <!-- Speakers Autocomplete -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Speakers</label>
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
});
</script>

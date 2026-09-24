<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\ModeratorModel;
use App\Models\ScheduleDayModel;
use App\Models\ScheduleRoomModel;
use App\Models\ScheduleSessionModel;
use App\Models\SpeakerModel;

class ScheduleController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event.');
        }

        $dayModel     = new ScheduleDayModel();
        $roomModel    = new ScheduleRoomModel();
        $sessionModel = new ScheduleSessionModel();

        $days  = $dayModel->getByEvent($eventId);
        $rooms = $roomModel->getByEvent($eventId);

        $selectedDayId = $this->request->getGet('day_id') ?: ($days[0]['id'] ?? null);
        $sessions = [];

        if ($selectedDayId) {
            $sessions = $sessionModel->getSessionsByDay((int)$selectedDayId);
        }

        $this->data['days']          = $days;
        $this->data['rooms']         = $rooms;
        $this->data['selectedDayId'] = $selectedDayId;
        $this->data['sessions']      = $sessions;
        $this->data['pageTitle']     = 'Summit Schedule Tracks';
        $this->data['activeMenu']    = 'schedules';

        return $this->render('admin/schedules/index', $this->data);
    }

    public function saveDay()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $dayModel = new ScheduleDayModel();
        $userId   = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;
        $dayModel->insert([
            'event_id'   => $eventId,
            'day_number' => (int)$this->request->getPost('day_number'),
            'event_date' => $this->request->getPost('event_date') ?: $this->request->getPost('date'),
            'label'      => $this->request->getPost('label'),
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return redirect()->back()->with('success', 'Schedule day added.');
    }

    public function createSession()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event.');
        }

        $dayModel       = new ScheduleDayModel();
        $roomModel      = new ScheduleRoomModel();
        $speakerModel   = new SpeakerModel();
        $moderatorModel = new ModeratorModel();
        $pengantarModel = new \App\Models\PengantarModel();

        $this->data['session']    = null;
        $this->data['days']       = $dayModel->getByEvent($eventId);
        $this->data['rooms']      = $roomModel->getByEvent($eventId);
        $this->data['speakers']   = $speakerModel->getByEvent($eventId);
        $this->data['moderators'] = $moderatorModel->getByEvent($eventId);
        $this->data['pengantar']  = $pengantarModel->getByEvent($eventId);
        $this->data['dayId']      = $this->request->getGet('day_id');
        $this->data['pageTitle']  = 'Create Schedule Session';
        $this->data['activeMenu'] = 'schedules';

        return $this->render('admin/schedules/form', $this->data);
    }

    public function editSession($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event.');
        }

        $sessionModel   = new ScheduleSessionModel();
        $dayModel       = new ScheduleDayModel();
        $roomModel      = new ScheduleRoomModel();
        $speakerModel   = new SpeakerModel();
        $moderatorModel = new ModeratorModel();
        $pengantarModel = new \App\Models\PengantarModel();

        $session = $sessionModel->find((int)$id);
        if (!$session) {
            return redirect()->to(base_url('admin/schedules'))->with('error', 'Session not found.');
        }

        // Fetch session_rooms with nested items
        $sessionRooms = $sessionModel->getSessionRoomsWithItems((int)$session['id']);

        // Backwards compatibility fallback if previously stored as flat children
        if (empty($sessionRooms)) {
            $children = $sessionModel->where('parent_session_id', $session['id'])->orderBy('sort_order', 'ASC')->findAll();
            if (!empty($children)) {
                $sessionRooms = [];
                $roomMap = [];
                foreach ($children as $child) {
                    $rId = (int)$child['room_id'];
                    $roomObj = $roomModel->find($rId);
                    if (!isset($roomMap[$rId])) {
                        $roomMap[$rId] = [
                            'room_id'   => $rId,
                            'room_name' => $roomObj['name'] ?? 'Room',
                            'items'     => [],
                        ];
                    }
                    $roomMap[$rId]['items'][] = [
                        'title'      => $child['title'],
                        'subtitle'   => $child['subtitle'] ?? '',
                        'moderators' => $sessionModel->getSessionModerators($child['id']),
                        'speakers'   => $sessionModel->getSessionSpeakers($child['id']),
                    ];
                }
                $sessionRooms = array_values($roomMap);
            }
        }

        $session['session_rooms'] = $sessionRooms;
        $session['speakers']      = $sessionModel->getSessionSpeakers($session['id']);
        $session['moderators']    = $sessionModel->getSessionModerators($session['id']);
        $session['pengantar']     = $sessionModel->getSessionPengantar($session['id']);

        $this->data['session']    = $session;
        $this->data['days']       = $dayModel->getByEvent($eventId);
        $this->data['rooms']      = $roomModel->getByEvent($eventId);
        $this->data['speakers']   = $speakerModel->getByEvent($eventId);
        $this->data['moderators'] = $moderatorModel->getByEvent($eventId);
        $this->data['pengantar']  = $pengantarModel->getByEvent($eventId);
        $this->data['dayId']      = $session['schedule_day_id'];
        $this->data['pageTitle']  = 'Edit Schedule Session';
        $this->data['activeMenu'] = 'schedules';

        return $this->render('admin/schedules/form', $this->data);
    }

    public function saveSession()
    {
        $db = \Config\Database::connect();
        $sessionModel = new ScheduleSessionModel();

        $dayId     = (int)$this->request->getPost('schedule_day_id');
        $flowType  = $this->request->getPost('flow_type'); // 'plenary' or 'special_session'
        $startTime = $this->request->getPost('start_time');
        $endTime   = $this->request->getPost('end_time');
        $userId    = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        if ($flowType === 'special_session') {
            // Special Session Parent
            $parentId = $sessionModel->insert([
                'schedule_day_id'   => $dayId,
                'room_id'           => null,
                'parent_session_id' => null,
                'title'             => $this->request->getPost('parent_title') ?: 'Special Session / Multi-Track Breakouts',
                'subtitle'          => null, // Removed per request
                'start_time'        => $startTime,
                'end_time'          => $endTime,
                'session_type'      => 'special_session',
                'sort_order'        => (int)$this->request->getPost('sort_order') ?: 0,
                'created_by'        => $userId,
                'updated_by'        => $userId,
            ]);

            // Save session_rooms and session_items
            $roomsData = $this->request->getPost('rooms');
            if (is_array($roomsData)) {
                $roomSort = 1;
                foreach ($roomsData as $rData) {
                    $roomId = !empty($rData['room_id']) ? (int)$rData['room_id'] : null;
                    if (!$roomId) {
                        continue;
                    }

                    $db->table('schedule_session_rooms')->insert([
                        'session_id' => $parentId,
                        'room_id'    => $roomId,
                        'sort_order' => $roomSort++,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $sessionRoomId = $db->insertID();

                    if (!empty($rData['items']) && is_array($rData['items'])) {
                        $itemSort = 1;
                        foreach ($rData['items'] as $it) {
                            if (empty($it['title'])) {
                                continue;
                            }

                            $db->table('schedule_session_items')->insert([
                                'session_room_id' => $sessionRoomId,
                                'title'           => $it['title'],
                                'subtitle'        => !empty($it['subtitle']) ? $it['subtitle'] : null,
                                'sort_order'      => $itemSort++,
                                'created_at'      => date('Y-m-d H:i:s'),
                                'updated_at'      => date('Y-m-d H:i:s'),
                            ]);
                            $itemId = $db->insertID();

                            // Item moderator (can be single or array)
                            if (!empty($it['moderator_id'])) {
                                $modIds = is_array($it['moderator_id']) ? $it['moderator_id'] : [$it['moderator_id']];
                                foreach ($modIds as $mId) {
                                    if (!empty($mId)) {
                                        $db->table('session_item_moderators')->insert([
                                            'item_id'      => $itemId,
                                            'moderator_id' => (int)$mId,
                                        ]);
                                    }
                                }
                            }

                            // Item speakers
                            if (!empty($it['speakers']) && is_array($it['speakers'])) {
                                $spkOrder = 1;
                                foreach ($it['speakers'] as $sId) {
                                    if (!empty($sId)) {
                                        $db->table('session_item_speakers')->insert([
                                            'item_id'    => $itemId,
                                            'speaker_id' => (int)$sId,
                                            'sort_order' => $spkOrder++,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return redirect()->to(base_url('admin/schedules?day_id=' . $dayId))->with('success', 'Special session tracks created.');
        } else {
            // Plenary Session
            $roomId = $this->request->getPost('room_id') ? (int)$this->request->getPost('room_id') : null;

            $sessionId = $sessionModel->insert([
                'schedule_day_id'   => $dayId,
                'room_id'           => $roomId,
                'parent_session_id' => null,
                'title'             => $this->request->getPost('title'),
                'subtitle'          => $this->request->getPost('subtitle'),
                'start_time'        => $startTime,
                'end_time'          => $endTime,
                'session_type'      => 'plenary',
                'sort_order'        => (int)$this->request->getPost('sort_order') ?: 0,
                'created_by'        => $userId,
                'updated_by'        => $userId,
            ]);

            // Assign speakers
            $speakers = $this->request->getPost('speakers');
            if (is_array($speakers)) {
                foreach ($speakers as $sort => $speakerId) {
                    if (!empty($speakerId)) {
                        $db->table('session_speakers')->insert([
                            'session_id' => $sessionId,
                            'speaker_id' => (int)$speakerId,
                            'sort_order' => $sort + 1,
                            'created_by' => $userId,
                        ]);
                    }
                }
            }

            // Assign moderators
            $moderators = $this->request->getPost('moderators');
            if (is_array($moderators)) {
                foreach ($moderators as $sort => $modId) {
                    if (!empty($modId)) {
                        $db->table('session_moderators')->insert([
                            'session_id'   => $sessionId,
                            'moderator_id' => (int)$modId,
                            'sort_order'   => $sort + 1,
                            'created_by'   => $userId,
                        ]);
                    }
                }
            }

            // Assign pengantar
            $pengantar = $this->request->getPost('pengantar');
            if (is_array($pengantar)) {
                foreach ($pengantar as $sort => $pId) {
                    if (!empty($pId)) {
                        $db->table('session_pengantar')->insert([
                            'session_id'   => $sessionId,
                            'pengantar_id' => (int)$pId,
                            'sort_order'   => $sort + 1,
                            'created_at'   => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            return redirect()->to(base_url('admin/schedules?day_id=' . $dayId))->with('success', 'Plenary session created successfully.');
        }
    }

    public function updateSession($id)
    {
        $db = \Config\Database::connect();
        $sessionModel = new ScheduleSessionModel();

        $session = $sessionModel->find((int)$id);
        if (!$session) {
            return redirect()->to(base_url('admin/schedules'))->with('error', 'Session not found.');
        }

        $dayId     = (int)$this->request->getPost('schedule_day_id');
        $flowType  = $this->request->getPost('flow_type');
        $startTime = $this->request->getPost('start_time');
        $endTime   = $this->request->getPost('end_time');
        $userId    = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        if ($flowType === 'special_session') {
            $sessionModel->update($id, [
                'schedule_day_id' => $dayId,
                'room_id'         => null,
                'title'           => $this->request->getPost('parent_title') ?: 'Special Session / Multi-Track Breakouts',
                'subtitle'        => null, // Removed per request
                'start_time'      => $startTime,
                'end_time'        => $endTime,
                'session_type'    => 'special_session',
                'updated_by'      => $userId,
            ]);

            // Clear old session_rooms and their items (CASCADE will clear items, moderators, speakers)
            $oldRooms = $db->table('schedule_session_rooms')->where('session_id', $id)->get()->getResultArray();
            foreach ($oldRooms as $or) {
                $oldItems = $db->table('schedule_session_items')->where('session_room_id', $or['id'])->get()->getResultArray();
                foreach ($oldItems as $oi) {
                    $db->table('session_item_moderators')->where('item_id', $oi['id'])->delete();
                    $db->table('session_item_speakers')->where('item_id', $oi['id'])->delete();
                }
                $db->table('schedule_session_items')->where('session_room_id', $or['id'])->delete();
            }
            $db->table('schedule_session_rooms')->where('session_id', $id)->delete();

            // Also clean any legacy children in schedule_sessions
            $oldChildren = $sessionModel->where('parent_session_id', $id)->findAll();
            foreach ($oldChildren as $oc) {
                $db->table('session_speakers')->where('session_id', $oc['id'])->delete();
                $db->table('session_moderators')->where('session_id', $oc['id'])->delete();
                $sessionModel->delete($oc['id']);
            }

            // Insert new session_rooms and items
            $roomsData = $this->request->getPost('rooms');
            if (is_array($roomsData)) {
                $roomSort = 1;
                foreach ($roomsData as $rData) {
                    $roomId = !empty($rData['room_id']) ? (int)$rData['room_id'] : null;
                    if (!$roomId) {
                        continue;
                    }

                    $db->table('schedule_session_rooms')->insert([
                        'session_id' => $id,
                        'room_id'    => $roomId,
                        'sort_order' => $roomSort++,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $sessionRoomId = $db->insertID();

                    if (!empty($rData['items']) && is_array($rData['items'])) {
                        $itemSort = 1;
                        foreach ($rData['items'] as $it) {
                            if (empty($it['title'])) {
                                continue;
                            }

                            $db->table('schedule_session_items')->insert([
                                'session_room_id' => $sessionRoomId,
                                'title'           => $it['title'],
                                'subtitle'        => !empty($it['subtitle']) ? $it['subtitle'] : null,
                                'sort_order'      => $itemSort++,
                                'created_at'      => date('Y-m-d H:i:s'),
                                'updated_at'      => date('Y-m-d H:i:s'),
                            ]);
                            $itemId = $db->insertID();

                            // Moderator
                            if (!empty($it['moderator_id'])) {
                                $modIds = is_array($it['moderator_id']) ? $it['moderator_id'] : [$it['moderator_id']];
                                foreach ($modIds as $mId) {
                                    if (!empty($mId)) {
                                        $db->table('session_item_moderators')->insert([
                                            'item_id'      => $itemId,
                                            'moderator_id' => (int)$mId,
                                        ]);
                                    }
                                }
                            }

                            // Speakers
                            if (!empty($it['speakers']) && is_array($it['speakers'])) {
                                $spkOrder = 1;
                                foreach ($it['speakers'] as $sId) {
                                    if (!empty($sId)) {
                                        $db->table('session_item_speakers')->insert([
                                            'item_id'    => $itemId,
                                            'speaker_id' => (int)$sId,
                                            'sort_order' => $spkOrder++,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return redirect()->to(base_url('admin/schedules?day_id=' . $dayId . '#session-' . $id))->with('success', 'Special session updated successfully.');
        } else {
            // Plenary update
            $roomId = $this->request->getPost('room_id') ? (int)$this->request->getPost('room_id') : null;

            $sessionModel->update($id, [
                'schedule_day_id' => $dayId,
                'room_id'         => $roomId,
                'title'           => $this->request->getPost('title'),
                'subtitle'        => $this->request->getPost('subtitle'),
                'start_time'      => $startTime,
                'end_time'        => $endTime,
                'session_type'    => 'plenary',
                'updated_by'      => $userId,
            ]);

            // Clear old speakers & moderators & pengantar
            $db->table('session_speakers')->where('session_id', $id)->delete();
            $db->table('session_moderators')->where('session_id', $id)->delete();
            $db->table('session_pengantar')->where('session_id', $id)->delete();

            // Re-assign speakers
            $speakers = $this->request->getPost('speakers');
            if (is_array($speakers)) {
                foreach ($speakers as $sort => $speakerId) {
                    if (!empty($speakerId)) {
                        $db->table('session_speakers')->insert([
                            'session_id' => $id,
                            'speaker_id' => (int)$speakerId,
                            'sort_order' => $sort + 1,
                            'created_by' => $userId,
                        ]);
                    }
                }
            }

            // Re-assign moderators
            $moderators = $this->request->getPost('moderators');
            if (is_array($moderators)) {
                foreach ($moderators as $sort => $modId) {
                    if (!empty($modId)) {
                        $db->table('session_moderators')->insert([
                            'session_id'   => $id,
                            'moderator_id' => (int)$modId,
                            'sort_order'   => $sort + 1,
                            'created_by'   => $userId,
                        ]);
                    }
                }
            }

            // Re-assign pengantar
            $pengantar = $this->request->getPost('pengantar');
            if (is_array($pengantar)) {
                foreach ($pengantar as $sort => $pId) {
                    if (!empty($pId)) {
                        $db->table('session_pengantar')->insert([
                            'session_id'   => $id,
                            'pengantar_id' => (int)$pId,
                            'sort_order'   => $sort + 1,
                            'created_at'   => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            return redirect()->to(base_url('admin/schedules?day_id=' . $dayId . '#session-' . $id))->with('success', 'Plenary session updated successfully.');
        }
    }

    public function deleteSession($id)
    {
        $sessionModel = new ScheduleSessionModel();
        $session = $sessionModel->find($id);
        $dayId = $session['schedule_day_id'] ?? null;

        // If parent session, delete all children too
        $children = $sessionModel->where('parent_session_id', $id)->findAll();
        foreach ($children as $ch) {
            $sessionModel->delete($ch['id']);
        }

        $sessionModel->delete($id);
        return redirect()->to(base_url('admin/schedules?day_id=' . $dayId))->with('success', 'Session deleted.');
    }
}

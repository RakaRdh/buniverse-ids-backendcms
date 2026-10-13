<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\QnaModel;
use App\Models\ScheduleDayModel;
use App\Models\ScheduleSessionModel;

class QnaController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $qnaModel = new QnaModel();
        $dayModel = new ScheduleDayModel();
        $sessionModel = new ScheduleSessionModel();

        $filterDay     = $this->request->getGet('day_id') ? (int)$this->request->getGet('day_id') : null;
        $rawSession    = $this->request->getGet('session_id');
        $filterSession = null;
        $filterItem    = $this->request->getGet('item_id') ? (int)$this->request->getGet('item_id') : null;

        if (!empty($rawSession)) {
            if (is_string($rawSession) && strpos($rawSession, ':') !== false) {
                $parts = explode(':', $rawSession, 2);
                $filterSession = (int)$parts[0];
                if (empty($filterItem) && !empty($parts[1])) {
                    $filterItem = (int)$parts[1];
                }
            } else {
                $filterSession = (int)$rawSession;
            }
        }

        // Fetch days for event
        $days = $dayModel->getByEvent($eventId);

        // Build detailed sessions list matching QnA Dashboard (expanding special sessions to room items)
        $detailedSessions = [];
        foreach ($days as $d) {
            $sessRows = $sessionModel->select('schedule_sessions.*, schedule_rooms.name as room_name')
                ->join('schedule_rooms', 'schedule_rooms.id = schedule_sessions.room_id', 'left')
                ->where('schedule_sessions.schedule_day_id', $d['id'])
                ->orderBy('schedule_sessions.start_time', 'ASC')
                ->orderBy('schedule_sessions.sort_order', 'ASC')
                ->findAll();

            foreach ($sessRows as $s) {
                // Filter non-QnA
                $sType = strtolower($s['session_type'] ?? '');
                if (in_array($sType, ['break', 'registration', 'closing'], true)) {
                    continue;
                }
                $tLower = strtolower(trim($s['title']));
                if (in_array($tLower, ['registration', 'menyanyikan lagu nasional', 'welcoming speech', 'apresiasi investasi', 'opening speech', 'keynote speech', 'opening ceremony', 'opening remarks', 'closing remarks'], true)) {
                    continue;
                }
                if (str_starts_with($tLower, 'closing remarks')) {
                    continue;
                }

                if ($s['session_type'] === 'special_session') {
                    $roomsWithItems = $sessionModel->getSessionRoomsWithItems((int)$s['id']);
                    $hasItems = false;
                    if (!empty($roomsWithItems)) {
                        foreach ($roomsWithItems as $sr) {
                            if (!empty($sr['items'])) {
                                foreach ($sr['items'] as $it) {
                                    $itTitleLower = strtolower(trim($it['title']));
                                    if (str_starts_with($itTitleLower, 'mou sign')) {
                                        continue;
                                    }
                                    $hasItems = true;
                                    $detailedSessions[] = [
                                        'id'              => (int)$s['id'],
                                        'item_id'         => (int)$it['id'],
                                        'schedule_day_id' => (int)$d['id'],
                                        'day_number'      => (int)$d['day_number'],
                                        'title'           => $s['title'] . ' - ' . $it['title'],
                                        'subtitle'        => $it['subtitle'] ?? '',
                                        'room_name'       => $sr['room_name'] ?? ($s['room_name'] ?? ''),
                                        'start_time'      => substr($s['start_time'], 0, 5),
                                        'end_time'        => substr($s['end_time'], 0, 5),
                                    ];
                                }
                            }
                        }
                    }
                    if (!$hasItems) {
                        $detailedSessions[] = [
                            'id'              => (int)$s['id'],
                            'item_id'         => null,
                            'schedule_day_id' => (int)$d['id'],
                            'day_number'      => (int)$d['day_number'],
                            'title'           => $s['title'],
                            'subtitle'        => $s['subtitle'] ?? '',
                            'room_name'       => $s['room_name'] ?? '',
                            'start_time'      => substr($s['start_time'], 0, 5),
                            'end_time'        => substr($s['end_time'], 0, 5),
                        ];
                    }
                } else {
                    $detailedSessions[] = [
                        'id'              => (int)$s['id'],
                        'item_id'         => null,
                        'schedule_day_id' => (int)$d['id'],
                        'day_number'      => (int)$d['day_number'],
                        'title'           => $s['title'],
                        'subtitle'        => $s['subtitle'] ?? '',
                        'room_name'       => $s['room_name'] ?? '',
                        'start_time'      => substr($s['start_time'], 0, 5),
                        'end_time'        => substr($s['end_time'], 0, 5),
                    ];
                }
            }
        }

        $this->data['questions']     = $qnaModel->getQuestions($eventId, $filterDay, $filterSession, $filterItem);
        $this->data['days']          = $days;
        $this->data['sessions']      = $detailedSessions;
        $this->data['filterDay']     = $filterDay;
        $this->data['filterSession'] = !empty($filterItem) ? ($filterSession . ':' . $filterItem) : $filterSession;
        $this->data['pageTitle']     = 'Q&A Questions';
        $this->data['activeMenu']    = 'qna';

        return $this->render('admin/qna/index', $this->data);
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $qnaModel = new QnaModel();
        $question = $qnaModel->where('id', (int)$id)->where('event_id', $eventId)->first();

        if (!$question) {
            return redirect()->to(base_url('admin/qna'))->with('error', 'Question not found.');
        }

        $qnaModel->delete($id);
        return redirect()->to(base_url('admin/qna'))->with('success', 'Question deleted successfully.');
    }
}

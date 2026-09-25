<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\QnaModel;
use App\Models\ScheduleDayModel;
use App\Models\ScheduleSessionModel;
use CodeIgniter\API\ResponseTrait;

class QnaApiController extends BaseController
{
    use ResponseTrait;

    protected function getActiveEvent()
    {
        $eventModel = new EventModel();
        return $eventModel->getActiveEvent();
    }

    public function create()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('No active event found.');
        }

        $rawBody = $this->request->getBody();
        $json = (!empty($rawBody) && is_string($rawBody)) ? json_decode($rawBody, true) : [];
        if (!is_array($json)) {
            $json = [];
        }

        $askerName = trim(strip_tags((string)($json['asker_name'] ?? $this->request->getPost('asker_name') ?? '')));
        $question  = trim(strip_tags((string)($json['question'] ?? $this->request->getPost('question') ?? '')));
        $rawSessionId = $json['schedule_session_id'] ?? $this->request->getPost('schedule_session_id') ?? $json['session_id'] ?? $this->request->getPost('session_id') ?? 0;
        $dayId     = (int)($json['schedule_day_id'] ?? $this->request->getPost('schedule_day_id') ?? $json['day_id'] ?? $this->request->getPost('day_id') ?? 0);
        $itemId    = (int)($json['session_item_id'] ?? $this->request->getPost('session_item_id') ?? $json['item_id'] ?? $this->request->getPost('item_id') ?? 0);

        // Handle composite sessionId (e.g., "244:5")
        if (is_string($rawSessionId) && strpos($rawSessionId, ':') !== false) {
            $parts = explode(':', $rawSessionId, 2);
            $sessionId = (int)$parts[0];
            if (empty($itemId) && !empty($parts[1])) {
                $itemId = (int)$parts[1];
            }
        } else {
            $sessionId = (int)$rawSessionId;
        }

        if (empty($askerName)) {
            return $this->failValidationErrors('Nama penanya wajib diisi.');
        }

        if (empty($question)) {
            return $this->failValidationErrors('Pertanyaan wajib diisi.');
        }

        if (mb_strlen($askerName) > 150) {
            return $this->failValidationErrors('Nama penanya maksimal 150 karakter.');
        }

        if (mb_strlen($question) > 3000) {
            return $this->failValidationErrors('Pertanyaan maksimal 3000 karakter.');
        }

        if (empty($sessionId)) {
            return $this->failValidationErrors('Sesi acara (session_id) tidak valid.');
        }

        // Validate session exists in this event
        $sessionModel = new ScheduleSessionModel();
        $session = $sessionModel->find($sessionId);
        if (!$session) {
            return $this->failNotFound('Sesi acara tidak ditemukan.');
        }

        // Strict day validation if dayId was provided
        if (!empty($dayId)) {
            $dayModel = new ScheduleDayModel();
            $dObj = $dayModel->find($dayId);
            if (!$dObj) {
                $dObj = $dayModel->where('event_id', $event['id'])->where('day_number', $dayId)->first();
            }
            if ($dObj && (int)$session['schedule_day_id'] !== (int)$dObj['id']) {
                return $this->failValidationErrors('Sesi acara ini tidak terdaftar pada hari yang dipilih.');
            }
            if (!$dObj && (int)$session['schedule_day_id'] !== $dayId) {
                return $this->failValidationErrors('Sesi acara ini tidak terdaftar pada hari yang dipilih.');
            }
            $dayId = (int)$session['schedule_day_id'];
        } else if (!empty($session['schedule_day_id'])) {
            $dayId = (int)$session['schedule_day_id'];
        }

        // Strict item validation if itemId was provided
        if (!empty($itemId)) {
            $db = \Config\Database::connect();
            $itemRow = $db->table('schedule_session_items')
                ->join('schedule_session_rooms', 'schedule_session_rooms.id = schedule_session_items.session_room_id')
                ->where('schedule_session_items.id', $itemId)
                ->where('schedule_session_rooms.session_id', $sessionId)
                ->get()->getRowArray();
            if (!$itemRow) {
                return $this->failValidationErrors('Materi sesi yang dipilih tidak sesuai dengan sesi acara.');
            }
        }

        $qnaModel = new QnaModel();
        $insertData = [
            'event_id'            => (int)$event['id'],
            'schedule_day_id'     => $dayId ?: null,
            'schedule_session_id' => $sessionId,
            'session_item_id'     => $itemId ?: null,
            'asker_name'          => $askerName,
            'question'            => $question,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        $id = $qnaModel->insert($insertData);
        if (!$id) {
            return $this->failServerError('Gagal menyimpan pertanyaan. Silakan coba lagi.');
        }

        $inserted = $qnaModel->find($id);

        return $this->respondCreated([
            'success' => true,
            'message' => 'Pertanyaan Anda berhasil dikirimkan.',
            'data'    => $inserted,
        ]);
    }

    public function list()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('No active event found.');
        }

        $dayId     = $this->request->getGet('day_id') ? (int)$this->request->getGet('day_id') : null;
        $sessionId = $this->request->getGet('session_id') ? (int)$this->request->getGet('session_id') : null;
        $itemId    = $this->request->getGet('item_id') ? (int)$this->request->getGet('item_id') : null;

        $qnaModel = new QnaModel();
        $questions = $qnaModel->getQuestions((int)$event['id'], $dayId, $sessionId, $itemId);

        // Format relative time or readable time
        $formatted = array_map(function ($item) {
            $timeAgo = $this->timeAgo($item['created_at']);
            
            $sessionTitle = $item['session_title'] ?? '';
            if (!empty($item['item_title'])) {
                $sessionTitle = $sessionTitle . ' - ' . $item['item_title'];
            }

            return [
                'id'                  => (int)$item['id'],
                'asker_name'          => $item['asker_name'],
                'question'            => $item['question'],
                'schedule_day_id'     => (int)($item['schedule_day_id'] ?? 0),
                'schedule_session_id' => (int)$item['schedule_session_id'],
                'session_item_id'     => !empty($item['session_item_id']) ? (int)$item['session_item_id'] : null,
                'session_title'       => $sessionTitle,
                'session_subtitle'    => $item['item_subtitle'] ?? '',
                'session_time'        => !empty($item['start_time']) ? substr($item['start_time'], 0, 5) . ' - ' . substr($item['end_time'], 0, 5) : '',
                'day_number'          => $item['day_number'] ?? null,
                'day_label'           => $item['day_label'] ?? '',
                'created_at'          => $item['created_at'],
                'time_ago'            => $timeAgo,
            ];
        }, $questions);

        return $this->respond([
            'success' => true,
            'count'   => count($formatted),
            'data'    => $formatted,
        ]);
    }

    public function sessions()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('No active event found.');
        }

        $dayModel = new ScheduleDayModel();
        $sessionModel = new ScheduleSessionModel();

        $days = $dayModel->getByEvent((int)$event['id']);
        $result = [];

        foreach ($days as $d) {
            $sessions = $sessionModel->where('schedule_day_id', $d['id'])
                ->orderBy('start_time', 'ASC')
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            $formattedSessions = [];
            foreach ($sessions as $s) {
                if (!$this->isQnaEligible($s['title'], $s['session_type'])) {
                    continue; // Skip non-QnA / ceremonial sessions
                }

                // If Special Session, expand its room items so each track is uniquely selectable
                if ($s['session_type'] === 'special_session') {
                    $roomsWithItems = $sessionModel->getSessionRoomsWithItems((int)$s['id']);
                    $hasItems = false;

                    if (!empty($roomsWithItems)) {
                        foreach ($roomsWithItems as $sr) {
                            if (!empty($sr['items'])) {
                                foreach ($sr['items'] as $it) {
                                    if (!$this->isQnaEligibleItem($it['title'])) {
                                        continue;
                                    }
                                    $hasItems = true;
                                    $formattedSessions[] = [
                                        'id'           => (int)$s['id'],
                                        'item_id'      => (int)$it['id'],
                                        'title'        => $s['title'] . ' - ' . $it['title'],
                                        'subtitle'     => $it['subtitle'] ?? '',
                                        'room_name'    => $sr['room_name'] ?? '',
                                        'start_time'   => substr($s['start_time'], 0, 5),
                                        'end_time'     => substr($s['end_time'], 0, 5),
                                        'session_type' => 'special_session',
                                    ];
                                }
                            }
                        }
                    }

                    if (!$hasItems) {
                        $formattedSessions[] = [
                            'id'           => (int)$s['id'],
                            'item_id'      => null,
                            'title'        => $s['title'],
                            'subtitle'     => $s['subtitle'] ?? '',
                            'start_time'   => substr($s['start_time'], 0, 5),
                            'end_time'     => substr($s['end_time'], 0, 5),
                            'session_type' => $s['session_type'],
                        ];
                    }
                } else {
                    // Standard plenary / keynote sessions
                    $formattedSessions[] = [
                        'id'           => (int)$s['id'],
                        'item_id'      => null,
                        'title'        => $s['title'],
                        'subtitle'     => $s['subtitle'] ?? '',
                        'start_time'   => substr($s['start_time'], 0, 5),
                        'end_time'     => substr($s['end_time'], 0, 5),
                        'session_type' => $s['session_type'],
                    ];
                }
            }

            $formattedDate = !empty($d['event_date']) ? date('F j, Y', strtotime($d['event_date'])) : $d['label'];

            $result[] = [
                'day_id'         => (int)$d['id'],
                'day_number'     => (int)$d['day_number'],
                'label'          => $d['label'],
                'event_date'     => $d['event_date'],
                'formatted_date' => $formattedDate,
                'sessions'       => $formattedSessions,
            ];
        }

        return $this->respond([
            'success' => true,
            'event'   => [
                'id'    => (int)$event['id'],
                'title' => $event['name'] ?? $event['title'] ?? 'Investor Daily Summit 2026',
            ],
            'days'    => $result,
        ]);
    }

    protected function timeAgo($datetime)
    {
        if (empty($datetime)) return '';
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 10) return 'Baru saja';
        if ($diff < 60) return $diff . ' detik lalu';
        if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
        return date('d M H:i', $timestamp);
    }

    protected function isQnaEligible(string $title, string $type = ''): bool
    {
        $type = strtolower($type);
        if (in_array($type, ['break', 'registration', 'closing'], true)) {
            return false;
        }

        $titleLower = strtolower(trim($title));

        $excludedExact = [
            'registration',
            'menyanyikan lagu nasional',
            'welcoming speech',
            'apresiasi investasi',
            'opening speech',
            'keynote speech', // Day 1 single keynote
            'opening ceremony',
            'opening remarks',
            'closing remarks',
        ];

        if (in_array($titleLower, $excludedExact, true)) {
            return false;
        }

        if (str_starts_with($titleLower, 'closing remarks')) {
            return false;
        }

        return true;
    }

    protected function isQnaEligibleItem(string $title): bool
    {
        $titleLower = strtolower(trim($title));
        if (str_starts_with($titleLower, 'mou sign')) {
            return false;
        }
        return true;
    }
}

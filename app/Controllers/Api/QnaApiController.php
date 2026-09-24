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

        $askerName = trim((string)($json['asker_name'] ?? $this->request->getPost('asker_name') ?? ''));
        $question  = trim((string)($json['question'] ?? $this->request->getPost('question') ?? ''));
        $sessionId = (int)($json['schedule_session_id'] ?? $this->request->getPost('schedule_session_id') ?? $json['session_id'] ?? $this->request->getPost('session_id') ?? 0);
        $dayId     = (int)($json['schedule_day_id'] ?? $this->request->getPost('schedule_day_id') ?? $json['day_id'] ?? $this->request->getPost('day_id') ?? 0);

        if (empty($askerName)) {
            return $this->failValidationError('Nama penanya wajib diisi.');
        }

        if (empty($question)) {
            return $this->failValidationError('Pertanyaan wajib diisi.');
        }

        if (mb_strlen($askerName) > 150) {
            return $this->failValidationError('Nama penanya maksimal 150 karakter.');
        }

        if (mb_strlen($question) > 3000) {
            return $this->failValidationError('Pertanyaan maksimal 3000 karakter.');
        }

        if (empty($sessionId)) {
            return $this->failValidationError('Sesi acara (session_id) tidak valid.');
        }

        // Validate session exists in this event
        $sessionModel = new ScheduleSessionModel();
        $session = $sessionModel->find($sessionId);
        if (!$session) {
            return $this->failNotFound('Sesi acara tidak ditemukan.');
        }

        if (empty($dayId) && !empty($session['schedule_day_id'])) {
            $dayId = (int)$session['schedule_day_id'];
        }

        $qnaModel = new QnaModel();
        $insertData = [
            'event_id'            => (int)$event['id'],
            'schedule_day_id'     => $dayId ?: null,
            'schedule_session_id' => $sessionId,
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

        $qnaModel = new QnaModel();
        $questions = $qnaModel->getQuestions((int)$event['id'], $dayId, $sessionId);

        // Format relative time or readable time
        $formatted = array_map(function ($item) {
            $timeAgo = $this->timeAgo($item['created_at']);
            return [
                'id'                  => (int)$item['id'],
                'asker_name'          => $item['asker_name'],
                'question'            => $item['question'],
                'schedule_day_id'     => (int)($item['schedule_day_id'] ?? 0),
                'schedule_session_id' => (int)$item['schedule_session_id'],
                'session_title'       => $item['session_title'] ?? '',
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
                if ($s['session_type'] === 'break') {
                    continue; // Skip breaks for QnA
                }
                $formattedSessions[] = [
                    'id'           => (int)$s['id'],
                    'title'        => $s['title'],
                    'subtitle'     => $s['subtitle'] ?? '',
                    'start_time'   => substr($s['start_time'], 0, 5),
                    'end_time'     => substr($s['end_time'], 0, 5),
                    'session_type' => $s['session_type'],
                ];
            }

            $result[] = [
                'day_id'     => (int)$d['id'],
                'day_number' => (int)$d['day_number'],
                'label'      => $d['label'],
                'event_date' => $d['event_date'],
                'sessions'   => $formattedSessions,
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
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class QnaModel extends Model
{
    protected $table            = 'qna';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'schedule_day_id',
        'schedule_session_id',
        'session_item_id',
        'asker_name',
        'question',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getQuestions(int $eventId, ?int $dayId = null, ?int $sessionId = null, ?int $itemId = null)
    {
        $builder = $this->select('qna.*, schedule_sessions.title as session_title, schedule_session_items.title as item_title, schedule_session_items.subtitle as item_subtitle, schedule_sessions.start_time, schedule_sessions.end_time, schedule_days.day_number, schedule_days.label as day_label')
            ->join('schedule_sessions', 'schedule_sessions.id = qna.schedule_session_id', 'left')
            ->join('schedule_session_items', 'schedule_session_items.id = qna.session_item_id', 'left')
            ->join('schedule_days', 'schedule_days.id = qna.schedule_day_id', 'left')
            ->where('qna.event_id', $eventId);

        if (!empty($dayId)) {
            $builder->groupStart()
                ->where('qna.schedule_day_id', $dayId)
                ->orWhere('schedule_days.day_number', $dayId)
                ->groupEnd();
        }

        if (!empty($sessionId)) {
            $builder->where('qna.schedule_session_id', $sessionId);
        }

        if (!empty($itemId)) {
            $builder->where('qna.session_item_id', $itemId);
        }

        return $builder->orderBy('qna.created_at', 'DESC')->findAll();
    }
}

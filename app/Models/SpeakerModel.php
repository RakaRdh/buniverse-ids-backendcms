<?php

namespace App\Models;

use CodeIgniter\Model;

class SpeakerModel extends Model
{
    protected $table            = 'speakers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'slug',
        'name',
        'role_text',
        'photo_url',
        'sort_order',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByEvent(int $eventId, bool $activeOnly = true)
    {
        $builder = $this->where('event_id', $eventId);
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        return $builder->orderBy('sort_order', 'ASC')->findAll();
    }

    /**
     * Fetch all sessions where speakers are assigned in this event
     */
    public function getAssignedSessionsByEvent(int $eventId): array
    {
        $db = \Config\Database::connect();

        // 1. Direct session_speakers
        $direct = $db->table('session_speakers')
            ->select('session_speakers.speaker_id, schedule_sessions.id as session_id, schedule_sessions.title as session_title, schedule_sessions.session_type, schedule_rooms.name as room_name, schedule_days.day_number, schedule_days.label as day_label')
            ->join('schedule_sessions', 'schedule_sessions.id = session_speakers.session_id')
            ->join('schedule_rooms', 'schedule_rooms.id = schedule_sessions.room_id', 'left')
            ->join('schedule_days', 'schedule_days.id = schedule_sessions.schedule_day_id', 'inner')
            ->where('schedule_days.event_id', $eventId)
            ->get()->getResultArray();

        // 2. Child track session_item_speakers
        $items = $db->table('session_item_speakers')
            ->select('session_item_speakers.speaker_id, schedule_session_items.id as item_id, schedule_session_items.title as item_title, schedule_rooms.name as room_name, schedule_sessions.id as session_id, schedule_sessions.title as session_title, schedule_days.day_number, schedule_days.label as day_label')
            ->join('schedule_session_items', 'schedule_session_items.id = session_item_speakers.item_id')
            ->join('schedule_session_rooms', 'schedule_session_rooms.id = schedule_session_items.session_room_id')
            ->join('schedule_rooms', 'schedule_rooms.id = schedule_session_rooms.room_id', 'left')
            ->join('schedule_sessions', 'schedule_sessions.id = schedule_session_rooms.session_id')
            ->join('schedule_days', 'schedule_days.id = schedule_sessions.schedule_day_id', 'inner')
            ->where('schedule_days.event_id', $eventId)
            ->get()->getResultArray();

        $speakerSessions = [];

        foreach ($direct as $row) {
            $spkId = (int)$row['speaker_id'];
            $dayText = !empty($row['day_number']) ? ('Day ' . $row['day_number']) : ($row['day_label'] ?? '');
            $speakerSessions[$spkId][] = [
                'session_id'    => (int)$row['session_id'],
                'session_title' => $row['session_title'],
                'day_text'      => $dayText,
                'room_name'     => $row['room_name'] ?? null,
                'item_title'    => null,
                'label'         => ($dayText ? "{$dayText}: " : "") . $row['session_title'],
            ];
        }

        foreach ($items as $row) {
            $spkId = (int)$row['speaker_id'];
            $dayText = !empty($row['day_number']) ? ('Day ' . $row['day_number']) : ($row['day_label'] ?? '');
            $itemDetail = $row['item_title'] . (!empty($row['room_name']) ? ' (' . $row['room_name'] . ')' : '');
            $speakerSessions[$spkId][] = [
                'session_id'    => (int)$row['session_id'],
                'session_title' => $row['session_title'],
                'day_text'      => $dayText,
                'room_name'     => $row['room_name'],
                'item_title'    => $row['item_title'],
                'label'         => ($dayText ? "{$dayText}: " : "") . $row['session_title'] . ' — ' . $itemDetail,
            ];
        }

        return $speakerSessions;
    }
}

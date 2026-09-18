<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleSessionModel extends Model
{
    protected $table            = 'schedule_sessions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'schedule_day_id',
        'parent_session_id',
        'room_id',
        'start_time',
        'end_time',
        'title',
        'subtitle',
        'session_type',
        'sort_order',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSessionsByDay(int $scheduleDayId)
    {
        $db = \Config\Database::connect();

        // 1. Get top-level sessions (parent_session_id IS NULL)
        $sessions = $this->select('schedule_sessions.*, schedule_rooms.name as room_name')
            ->join('schedule_rooms', 'schedule_rooms.id = schedule_sessions.room_id', 'left')
            ->where('schedule_sessions.schedule_day_id', $scheduleDayId)
            ->where('schedule_sessions.parent_session_id', null)
            ->orderBy('schedule_sessions.start_time', 'ASC')
            ->orderBy('schedule_sessions.sort_order', 'ASC')
            ->findAll();

        foreach ($sessions as &$session) {
            // Check if special session has rooms & items via new hierarchy tables
            $session['session_rooms'] = $this->getSessionRoomsWithItems((int)$session['id']);

            // Also check old children for backwards compatibility if any
            $children = $this->select('schedule_sessions.*, schedule_rooms.name as room_name')
                ->join('schedule_rooms', 'schedule_rooms.id = schedule_sessions.room_id', 'left')
                ->where('schedule_sessions.parent_session_id', $session['id'])
                ->orderBy('schedule_sessions.sort_order', 'ASC')
                ->findAll();

            foreach ($children as &$child) {
                $child['speakers']   = $this->getSessionSpeakers($child['id']);
                $child['moderators'] = $this->getSessionModerators($child['id']);
            }
            $session['children']   = $children;
            $session['speakers']   = $this->getSessionSpeakers($session['id']);
            $session['moderators'] = $this->getSessionModerators($session['id']);
        }

        return $sessions;
    }

    public function getSessionRoomsWithItems(int $sessionId)
    {
        $db = \Config\Database::connect();

        $sessionRooms = $db->table('schedule_session_rooms')
            ->select('schedule_session_rooms.*, schedule_rooms.name as room_name')
            ->join('schedule_rooms', 'schedule_rooms.id = schedule_session_rooms.room_id')
            ->where('schedule_session_rooms.session_id', $sessionId)
            ->orderBy('schedule_session_rooms.sort_order', 'ASC')
            ->get()->getResultArray();

        foreach ($sessionRooms as &$sr) {
            $items = $db->table('schedule_session_items')
                ->where('session_room_id', $sr['id'])
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();

            foreach ($items as &$item) {
                // Item moderators
                $item['moderators'] = $db->table('session_item_moderators')
                    ->select('session_item_moderators.*, moderators.name, moderators.role_text, moderators.photo_url')
                    ->join('moderators', 'moderators.id = session_item_moderators.moderator_id')
                    ->where('session_item_moderators.item_id', $item['id'])
                    ->get()->getResultArray();

                // Item speakers
                $item['speakers'] = $db->table('session_item_speakers')
                    ->select('session_item_speakers.*, speakers.slug, speakers.name, speakers.role_text, speakers.photo_url')
                    ->join('speakers', 'speakers.id = session_item_speakers.speaker_id')
                    ->where('session_item_speakers.item_id', $item['id'])
                    ->orderBy('session_item_speakers.sort_order', 'ASC')
                    ->get()->getResultArray();
            }

            $sr['items'] = $items;
        }

        return $sessionRooms;
    }

    public function getSessionSpeakers(int $sessionId)
    {
        $db = \Config\Database::connect();
        return $db->table('session_speakers')
            ->select('session_speakers.*, speakers.slug, speakers.name, speakers.role_text, speakers.photo_url')
            ->join('speakers', 'speakers.id = session_speakers.speaker_id')
            ->where('session_speakers.session_id', $sessionId)
            ->orderBy('session_speakers.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    public function getSessionModerators(int $sessionId)
    {
        $db = \Config\Database::connect();
        return $db->table('session_moderators')
            ->select('session_moderators.*, moderators.name, moderators.role_text, moderators.photo_url')
            ->join('moderators', 'moderators.id = session_moderators.moderator_id')
            ->where('session_moderators.session_id', $sessionId)
            ->orderBy('session_moderators.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    public function checkRoomConflict(int $roomId, int $scheduleDayId, string $startTime, string $endTime, ?int $excludeId = null)
    {
        $builder = $this->where('room_id', $roomId)
            ->where('schedule_day_id', $scheduleDayId)
            ->where('start_time <', $endTime)
            ->where('end_time >', $startTime);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->first();
    }
}

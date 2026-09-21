<?php

namespace App\Models;

use CodeIgniter\Model;

class EventLivestreamModel extends Model
{
    protected $table            = 'event_livestreams';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'title',
        'youtube_url',
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

    public function getActiveByEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
                    ->where('is_active', 1)
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    public function getByEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class EventHighlightModel extends Model
{
    protected $table            = 'event_highlights';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'title',
        'youtube_url',
        'video_id',
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

    public function getByEventId(int $eventId, bool $onlyActive = false)
    {
        $builder = $this->where('event_id', $eventId);
        if ($onlyActive) {
            $builder->where('is_active', 1);
        }
        return $builder->orderBy('sort_order', 'ASC')
                       ->orderBy('id', 'ASC')
                       ->findAll();
    }
}

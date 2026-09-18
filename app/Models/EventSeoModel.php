<?php

namespace App\Models;

use CodeIgniter\Model;

class EventSeoModel extends Model
{
    protected $table            = 'event_seo';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'meta_title',
        'meta_description',
        'og_image_url',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByEventId(int $eventId)
    {
        return $this->where('event_id', $eventId)->first();
    }
}

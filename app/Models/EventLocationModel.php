<?php

namespace App\Models;

use CodeIgniter\Model;

class EventLocationModel extends Model
{
    protected $table            = 'event_locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'hotel_name',
        'hotel_address',
        'gmaps_iframe',
        'layout_image_url',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByEventId(int $eventId): ?array
    {
        return $this->where('event_id', $eventId)->first();
    }
}

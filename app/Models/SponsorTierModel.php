<?php

namespace App\Models;

use CodeIgniter\Model;

class SponsorTierModel extends Model
{
    protected $table            = 'sponsor_tiers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'name',
        'logo_size',
        'group_key',
        'group_label',
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

    public function getByEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
}

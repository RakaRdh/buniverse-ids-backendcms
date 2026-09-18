<?php

namespace App\Models;

use CodeIgniter\Model;

class SponsorModel extends Model
{
    protected $table            = 'sponsors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'tier_id',
        'name',
        'logo_url',
        'link_url',
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

    public function getByTier(int $tierId)
    {
        return $this->where('tier_id', $tierId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function getByEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
}

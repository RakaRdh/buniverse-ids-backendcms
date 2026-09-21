<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsPortalModel extends Model
{
    protected $table            = 'news_portals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'portal_key',
        'portal_name',
        'portal_url',
        'is_active',
        'sort_order',
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
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    public function getActiveByEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
                    ->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }
}

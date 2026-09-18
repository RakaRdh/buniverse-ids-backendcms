<?php

namespace App\Models;

use CodeIgniter\Model;

class ModeratorModel extends Model
{
    protected $table            = 'moderators';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
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
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomPlacementModel extends Model
{
    protected $table            = 'custom_placements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'name',
        'section_key',
        'image_url',
        'pos_x_percent',
        'pos_y_percent',
        'size_px',
        'opacity',
        'tint_color',
        'is_rotated',
        'rotation_direction',
        'show_on_mobile',
        'is_active',
        'z_index',
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
            ->orderBy('section_key', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getActiveByEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
            ->where('is_active', 1)
            ->orderBy('section_key', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getActiveBySection(int $eventId, string $sectionKey)
    {
        return $this->where('event_id', $eventId)
            ->where('section_key', $sectionKey)
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}

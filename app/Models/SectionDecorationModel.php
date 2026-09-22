<?php

namespace App\Models;

use CodeIgniter\Model;

class SectionDecorationModel extends Model
{
    protected $table            = 'section_decorations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'section_key',
        'layout_mode',
        'position',
        'auto_count',
        'auto_gap_px',
        'auto_start_side',
        'auto_gap_unit',
        'edge_offset_mode',
        'edge_offset_percent',
        'image_url',
        'size_px',
        'opacity',
        'tint_color',
        'is_rotated',
        'rotation_direction',
        'perspective_enabled',
        'perspective_value',
        'rotate_x',
        'rotate_y',
        'rotate_z',
        'show_on_mobile',
        'device_visibility',
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
            ->findAll();
    }

    public function hasDuplicateManualPosition(int $eventId, string $sectionKey, string $position, ?int $excludeId = null): bool
    {
        $builder = $this->where('event_id', $eventId)
            ->where('section_key', $sectionKey)
            ->where('layout_mode', 'manual')
            ->where('position', $position)
            ->where('is_active', 1);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}

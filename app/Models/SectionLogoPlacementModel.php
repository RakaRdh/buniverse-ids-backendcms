<?php

namespace App\Models;

use CodeIgniter\Model;

class SectionLogoPlacementModel extends Model
{
    protected $table            = 'section_logo_placements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'slot_key',
        'logo_id',
        'variant',
        'target_url',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPlacementsWithLogos(int $eventId)
    {
        return $this->select('section_logo_placements.*, event_logos.name as logo_name, event_logos.logo_url, event_logos.logo_white_url')
            ->join('event_logos', 'event_logos.id = section_logo_placements.logo_id', 'left')
            ->where('section_logo_placements.event_id', $eventId)
            ->findAll();
    }

    public function upsertPlacement(int $eventId, string $slotKey, ?int $logoId, string $variant = 'auto', ?string $targetUrl = null, int $isActive = 1)
    {
        $existing = $this->where('event_id', $eventId)->where('slot_key', $slotKey)->first();
        $data = [
            'event_id'   => $eventId,
            'slot_key'   => $slotKey,
            'logo_id'    => $logoId ?: null,
            'variant'    => $variant,
            'target_url' => $targetUrl,
            'is_active'  => $isActive,
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data);
    }
}

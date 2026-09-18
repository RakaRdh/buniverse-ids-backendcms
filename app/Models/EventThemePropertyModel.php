<?php

namespace App\Models;

use CodeIgniter\Model;

class EventThemePropertyModel extends Model
{
    protected $table            = 'event_theme_properties';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'css_property',
        'css_value',
        'value_type',
        'category',
        'label',
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
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getGroupedByEvent(int $eventId): array
    {
        $rows = $this->getByEvent($eventId);
        $grouped = [];
        foreach ($rows as $r) {
            $cat = $r['category'] ?: 'other';
            $grouped[$cat][] = $r;
        }
        return $grouped;
    }

    public function upsertProperty(int $eventId, string $cssProp, string $value, string $category, string $label, ?int $userId = 1): void
    {
        $existing = $this->where('event_id', $eventId)->where('css_property', $cssProp)->first();
        if ($existing) {
            $this->update($existing['id'], [
                'css_value'  => $value,
                'category'   => $category,
                'label'      => $label,
                'updated_by' => $userId,
            ]);
        } else {
            $this->insert([
                'event_id'     => $eventId,
                'css_property' => $cssProp,
                'css_value'    => $value,
                'value_type'   => 'solid',
                'category'     => $category,
                'label'        => $label,
                'sort_order'   => 99,
                'created_by'   => $userId,
                'updated_by'   => $userId,
            ]);
        }
    }

    public function getPaletteForPicker(int $eventId): array
    {
        $rows = $this->where('event_id', $eventId)->findAll();
        $palette = [];
        foreach ($rows as $r) {
            $val = trim($r['css_value']);
            if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $val) || preg_match('/^rgba?\(.+?\)$/i', $val)) {
                $palette[] = [
                    'label'    => ($r['label'] ?: $r['css_property']) . ' (' . $val . ')',
                    'token'    => $r['css_property'],
                    'value'    => $val,
                    'category' => $r['category'],
                ];
            }
        }
        return $palette;
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class InvitationTemplateModel extends Model
{
    protected $table            = 'invitation_templates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'template_key',
        'name',
        'description',
        'thumbnail_url',
        'is_active',
        'is_default',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveForEvent(int $eventId)
    {
        return $this->where('event_id', $eventId)
            ->where('is_active', 1)
            ->orderBy('is_default', 'DESC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getDefaultTemplate(int $eventId)
    {
        $default = $this->where('event_id', $eventId)
            ->where('is_active', 1)
            ->where('is_default', 1)
            ->first();

        if (!$default) {
            $default = $this->where('event_id', $eventId)
                ->where('is_active', 1)
                ->first();
        }

        return $default;
    }
}

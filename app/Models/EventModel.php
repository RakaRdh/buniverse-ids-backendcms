<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table            = 'events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'slug',
        'year',
        'title',
        'theme',
        'event_date_start',
        'event_date_end',
        'status',
        'news_tag_slug',
        'template_version',
        'quota',
        'gate_passcode',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveEvent()
    {
        $event = $this->where('status', 'published')->orderBy('year', 'DESC')->first();
        if (!$event) {
            $event = $this->where('status', 'draft')->orderBy('year', 'DESC')->first();
        }
        if (!$event) {
            $event = $this->orderBy('year', 'DESC')->first();
        }
        return $event;
    }
}

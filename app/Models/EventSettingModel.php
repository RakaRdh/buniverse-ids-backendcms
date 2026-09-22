<?php

namespace App\Models;

use CodeIgniter\Model;

class EventSettingModel extends Model
{
    protected $table            = 'event_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'ticket_url',
        'cta_label',
        'copyright_text',
        'nav_items_json',
        'logo_a_url',
        'logo_a_white_url',
        'logo_a_target_url',
        'logo_b_url',
        'logo_b_white_url',
        'logo_b_target_url',
        'logo_footer_url',
        'contact_phone',
        'contact_label',
        'contact_url',
        'cta_ticket_active',
        'floating_bar_enabled',
        'floating_bar_text',
        'floating_bar_btn_label',
        'floating_bar_btn_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'linkedin_url',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByEventId(int $eventId)
    {
        return $this->where('event_id', $eventId)->first();
    }
}

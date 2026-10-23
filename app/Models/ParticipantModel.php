<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table            = 'participants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'ticket_code',
        'order_id',
        'ticket_index',
        'visit_day',
        'ticket_quantity',
        'participant_type',
        'salutation',
        'full_name',
        'nickname',
        'gender',
        'identity_type',
        'identity_number',
        'photo_profile',
        'email',
        'phone',
        'mobile_phone',
        'company_name',
        'job_title',
        'job_level',
        'department',
        'industry_sector',
        'website',
        'pic_name',
        'pic_email',
        'pic_phone',
        'pic_relationship',
        'country',
        'province',
        'city',
        'postal_code',
        'address',
        'purpose_of_visit',
        'interests',
        'source_info',
        'status',
        'is_checked_in',
        'checked_in_at',
        'checked_in_by',
        'custom_fields',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique 16-digit numeric ticket code
     */
    public function generateUniqueTicketCode(int $eventId): string
    {
        $prefix = str_pad((string)($eventId % 100), 2, '0', STR_PAD_LEFT);
        
        do {
            // e.g. 26 + 14 random digits = 16 digits
            $randomDigits = '';
            for ($i = 0; $i < 14; $i++) {
                $randomDigits .= mt_rand(0, 9);
            }
            $code = $prefix . $randomDigits;
            $exists = $this->where('ticket_code', $code)->first();
        } while ($exists);

        return $code;
    }

    /**
     * Get active participants count for an event
     */
    public function getActiveCount(int $eventId): int
    {
        return $this->where('event_id', $eventId)
                    ->whereIn('status', ['confirmed'])
                    ->countAllResults();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class InvitationListModel extends Model
{
    protected $table            = 'invitation_lists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'template_id',
        'invitation_code',
        'slug',
        'name',
        'email',
        'phone',
        'institution',
        'position',
        'rsvp_status',
        'rsvp_pax',
        'rsvp_at',
        'notes',
        'blast_status',
        'blast_at',
        'blast_error',
        'view_count',
        'last_viewed_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique 6-character token & slug
     */
    public static function generateCodeAndSlug(string $name, int $eventId): array
    {
        // 6 characters random uppercase alphanumeric (e.g. X9K2M7)
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $maxIndex = strlen($characters) - 1;
        
        do {
            $token = '';
            for ($i = 0; $i < 6; $i++) {
                $token .= $characters[random_int(0, $maxIndex)];
            }
            $invitationCode = 'MAP-' . $token;
            
            // Generate clean slug from name
            $cleanName = preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower(trim($name)));
            $cleanName = trim($cleanName, '-');
            if (empty($cleanName)) {
                $cleanName = 'guest';
            }
            $slug = substr($cleanName, 0, 40) . '-' . strtolower($token);

            // Ensure uniqueness
            $db = \Config\Database::connect();
            $exists = $db->table('invitation_lists')
                ->where('invitation_code', $invitationCode)
                ->orWhere('slug', $slug)
                ->countAllResults();
        } while ($exists > 0);

        return [
            'invitation_code' => $invitationCode,
            'slug'            => $slug,
        ];
    }
}

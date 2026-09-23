<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterLogoSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $event = $db->table('events')->where('status', 'published')->orderBy('id', 'DESC')->get()->getRowArray();
        if (!$event) {
            $event = $db->table('events')->orderBy('id', 'DESC')->get()->getRowArray();
        }
        if (!$event) {
            echo "No event found to seed master logos.\n";
            return;
        }

        $eventId = (int)$event['id'];
        $admin = $db->table('admin_users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = $admin ? (int)$admin['id'] : 1;

        // Check if logos already seeded
        $existingCount = $db->table('event_logos')->where('event_id', $eventId)->countAllResults();
        if ($existingCount > 0) {
            echo "Event logos already seeded ($existingCount logos).\n";
            return;
        }

        $logos = [
            [
                'event_id'           => $eventId,
                'name'               => 'Investor Daily Summit 2026',
                'logo_url'           => "uploads/events/{$eventId}/logos/logo_ids26_color.webp",
                'logo_white_url'     => "uploads/events/{$eventId}/logos/logo_ids26_white.webp",
                'default_target_url' => '#home',
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'event_id'           => $eventId,
                'name'               => 'Investor Daily Indonesia',
                'logo_url'           => "uploads/events/{$eventId}/logos/logo_id_color.webp",
                'logo_white_url'     => "uploads/events/{$eventId}/logos/logo_id_white.webp",
                'default_target_url' => 'https://investor.id',
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
        ];

        $logoIds = [];
        foreach ($logos as $lg) {
            $db->table('event_logos')->insert($lg);
            $logoIds[] = $db->insertID();
        }

        $idsLogoId = $logoIds[0];
        $idLogoId  = $logoIds[1];

        // Seed 5 slots in section_logo_placements
        $placements = [
            [
                'event_id'    => $eventId,
                'slot_key'    => 'navbar_slot_1',
                'logo_id'     => $idsLogoId,
                'variant'     => 'auto',
                'target_url'  => '#home',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'event_id'    => $eventId,
                'slot_key'    => 'navbar_slot_2',
                'logo_id'     => $idLogoId,
                'variant'     => 'auto',
                'target_url'  => 'https://investor.id',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'event_id'    => $eventId,
                'slot_key'    => 'overview',
                'logo_id'     => $idsLogoId,
                'variant'     => 'auto',
                'target_url'  => '#home',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'event_id'    => $eventId,
                'slot_key'    => 'floating_bottom',
                'logo_id'     => $idsLogoId,
                'variant'     => 'auto',
                'target_url'  => '#home',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'event_id'    => $eventId,
                'slot_key'    => 'footer',
                'logo_id'     => $idLogoId,
                'variant'     => 'auto',
                'target_url'  => 'https://investor.id',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($placements as $plc) {
            $db->table('section_logo_placements')->insert($plc);
        }

        echo "MasterLogoSeeder completed successfully with 5 section logo placements!\n";
    }
}

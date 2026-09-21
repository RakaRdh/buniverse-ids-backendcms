<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\EventModel;
use App\Models\NewsPortalModel;
use App\Models\EventLivestreamModel;
use App\Models\EventSettingModel;

class NewFeaturesSeeder extends Seeder
{
    public function run()
    {
        $eventModel = new EventModel();
        $active = $eventModel->getActiveEvent();
        if (!$active) {
            echo "No active event found.\n";
            return;
        }

        $eventId = (int)$active['id'];

        // 1. Seed default news portals if empty
        $portalModel = new NewsPortalModel();
        if ($portalModel->where('event_id', $eventId)->countAllResults() == 0) {
            $portalModel->insert(['event_id' => $eventId, 'portal_key' => 'investor-daily', 'portal_name' => 'Investor Daily', 'portal_url' => 'https://investor.id', 'sort_order' => 1]);
            $portalModel->insert(['event_id' => $eventId, 'portal_key' => 'beritasatu', 'portal_name' => 'BeritaSatu', 'portal_url' => 'https://www.beritasatu.com', 'sort_order' => 2]);
            $portalModel->insert(['event_id' => $eventId, 'portal_key' => 'jakartaglobe', 'portal_name' => 'JakartaGlobe', 'portal_url' => 'https://jakartaglobe.id', 'sort_order' => 3]);
            echo "Seeded 3 default news portals.\n";
        }

        // 2. Seed default livestream if empty
        $streamModel = new EventLivestreamModel();
        if ($streamModel->where('event_id', $eventId)->countAllResults() == 0) {
            $streamModel->insert([
                'event_id' => $eventId,
                'title' => 'Investor Daily Summit 2026 Live Stream',
                'youtube_url' => 'https://www.youtube.com/watch?v=rFZHOHl-L8A',
                'is_active' => 1
            ]);
            echo "Seeded default livestream.\n";
        }

        // 3. Ensure event_settings has default contact & target urls
        $settingModel = new EventSettingModel();
        $st = $settingModel->getByEventId($eventId);
        if ($st) {
            $settingModel->update($st['id'], [
                'logo_a_target_url' => !empty($st['logo_a_target_url']) ? $st['logo_a_target_url'] : '#home',
                'logo_b_target_url' => !empty($st['logo_b_target_url']) ? $st['logo_b_target_url'] : 'https://investor.id',
                'contact_phone'     => !empty($st['contact_phone']) ? $st['contact_phone'] : '+62 855-1235-000',
                'contact_label'     => !empty($st['contact_label']) ? $st['contact_label'] : 'Phone',
            ]);
            echo "Updated event settings defaults.\n";
        }
    }
}

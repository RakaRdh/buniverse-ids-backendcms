<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EsgEventSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $slug = 'esg-2026';
        $existing = $db->table('events')->where('slug', $slug)->get()->getRowArray();

        $admin = $db->table('admin_users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = $admin ? (int)$admin['id'] : 1;

        if ($existing) {
            $eventId = (int)$existing['id'];
            echo "Event with slug '{$slug}' already exists with ID: {$eventId}. Updating core data...\n";

            $db->table('events')->where('id', $eventId)->update([
                'title'            => 'B-Universe ESG Awards 2026',
                'theme'            => 'MENDORONG KEBERLANJUTAN MELALUI TRANSFORMASI HIJAU DAN TATA KELOLA BERSIH',
                'event_date_start' => '2026-12-10',
                'event_date_end'   => '2026-12-10',
                'status'           => 'published',
                'news_tag_slug'    => 'esg-2026',
                'template_version' => 'v1',
                'updated_by'       => $adminId,
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        } else {
            $newEventData = [
                'slug'             => $slug,
                'year'             => 2026,
                'title'            => 'B-Universe ESG Awards 2026',
                'theme'            => 'MENDORONG KEBERLANJUTAN MELALUI TRANSFORMASI HIJAU DAN TATA KELOLA BERSIH',
                'event_date_start' => '2026-12-10',
                'event_date_end'   => '2026-12-10',
                'status'           => 'published',
                'news_tag_slug'    => 'esg-2026',
                'template_version' => 'v1',
                'created_by'       => $adminId,
                'updated_by'       => $adminId,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $db->table('events')->insert($newEventData);
            $eventId = (int)$db->insertID();
            echo "Created new event '{$slug}' with ID: {$eventId}.\n";
        }

        // 1. Event Hero
        $hero = $db->table('event_hero')->where('event_id', $eventId)->get()->getRowArray();
        if (!$hero) {
            $db->table('event_hero')->insert([
                'event_id'   => $eventId,
                'venue'      => 'Grand Ballroom, Hotel Mulia Senayan Jakarta',
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_hero for event {$eventId}.\n";
        } else {
            $db->table('event_hero')->where('id', $hero['id'])->update([
                'venue'      => 'Grand Ballroom, Hotel Mulia Senayan Jakarta',
                'updated_by' => $adminId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 2. Event Hero Images
        $heroImgCount = $db->table('event_hero_images')->where('event_id', $eventId)->countAllResults();
        if ($heroImgCount === 0) {
            $db->table('event_hero_images')->insert([
                'event_id'   => $eventId,
                'image_url'  => 'assets/img/image1.png',
                'alt_text'   => 'B-Universe ESG Awards 2026',
                'sort_order' => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_hero_images for event {$eventId}.\n";
        }

        // 3. Event Overview
        $overview = $db->table('event_overview')->where('event_id', $eventId)->get()->getRowArray();
        $introText = "Di era transisi menuju ekonomi rendah karbon dan inklusif, penerapan prinsip Lingkungan, Sosial, dan Tata Kelola (ESG) telah menjadi tolok ukur fundamental keberhasilan korporasi dan institusi di Indonesia.";
        $bodyText = "B-Universe ESG Awards 2026 diselenggarakan sebagai bentuk apresiasi tertinggi bagi korporasi, institusi keuangan, dan pemimpin industri yang menunjukkan komitmen nyata dalam dekarbonisasi, keadilan sosial, dan tata kelola perusahaan yang berintegritas.\n\nAjang penghargaan ini menghadirkan berbagai kategori penilaian independen yang mengukur dampak riil inisiatif keberlanjutan bagi ekosistem bisnis dan masyarakat luas.\n\nMelalui ajang ini, diharapkan lahir inspirasi dan standar baru praktik bisnis berkelanjutan yang mempercepat tercapainya target Net Zero Emission dan pertumbuhan ekonomi inklusif nasional.";

        if (!$overview) {
            $db->table('event_overview')->insert([
                'event_id'   => $eventId,
                'heading'    => 'B-Universe ESG Awards 2026',
                'intro_text' => $introText,
                'body_text'  => $bodyText,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_overview for event {$eventId}.\n";
        } else {
            $db->table('event_overview')->where('id', $overview['id'])->update([
                'heading'    => 'B-Universe ESG Awards 2026',
                'intro_text' => $introText,
                'body_text'  => $bodyText,
                'updated_by' => $adminId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 4. Event Settings
        $settings = $db->table('event_settings')->where('event_id', $eventId)->get()->getRowArray();
        $navItems = json_encode([
            ['label' => 'Home', 'url' => '#home'],
            ['label' => 'Overview', 'url' => '#overview'],
            ['label' => 'About Us', 'url' => '#about-us'],
            ['label' => 'Categories', 'url' => '#categories'],
            ['label' => 'Schedule', 'url' => '#schedule'],
        ]);

        if (!$settings) {
            $db->table('event_settings')->insert([
                'event_id'              => $eventId,
                'ticket_url'            => '#',
                'cta_ticket_active'     => 0,
                'floating_bar_enabled'  => 0,
                'floating_bar_text'     => 'Saksikan B-Universe ESG Awards 2026',
                'floating_bar_btn_label'=> 'Tonton Live',
                'floating_bar_btn_url'  => 'https://www.youtube.com/@BeritaSatuChannel',
                'logo_a_url'            => 'assets/img/buniverse.png',
                'logo_a_white_url'      => 'assets/img/buniverse.png',
                'logo_a_target_url'     => '#home',
                'logo_b_url'            => 'assets/img/logo_beritasatu.png',
                'logo_b_white_url'      => 'assets/img/logo_beritasatu.png',
                'logo_b_target_url'     => 'https://www.beritasatu.com',
                'youtube_url'           => 'https://www.youtube.com/@BeritaSatuChannel',
                'cta_label'             => 'Live Stream',
                'copyright_text'        => 'B-Universe ESG Awards 2026.',
                'nav_items_json'        => $navItems,
                'created_by'            => $adminId,
                'updated_by'            => $adminId,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_settings for event {$eventId}.\n";
        } else {
            $db->table('event_settings')->where('id', $settings['id'])->update([
                'nav_items_json' => $navItems,
                'youtube_url'    => 'https://www.youtube.com/@BeritaSatuChannel',
                'copyright_text' => 'B-Universe ESG Awards 2026.',
                'updated_by'     => $adminId,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        // 5. Event SEO
        $seo = $db->table('event_seo')->where('event_id', $eventId)->get()->getRowArray();
        if (!$seo) {
            $db->table('event_seo')->insert([
                'event_id'         => $eventId,
                'meta_title'       => 'B-Universe ESG Awards 2026: Mendorong Keberlanjutan Melalui Transformasi Hijau',
                'meta_description' => 'B-Universe ESG Awards 2026 menghargai dedikasi korporasi dalam praktik Lingkungan, Sosial, dan Tata Kelola terbaik di Grand Ballroom Hotel Mulia Senayan.',
                'og_image_url'     => 'assets/img/image1.png',
                'created_by'       => $adminId,
                'updated_by'       => $adminId,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_seo for event {$eventId}.\n";
        }

        // 6. Theme Properties (Salin palette dari MAP atau event 4)
        $themeCount = $db->table('event_theme_properties')->where('event_id', $eventId)->countAllResults();
        if ($themeCount === 0) {
            $sourceThemes = $db->table('event_theme_properties')->where('event_id', 4)->get()->getResultArray();
            if (empty($sourceThemes)) {
                $sourceThemes = $db->table('event_theme_properties')->where('event_id', 1)->get()->getResultArray();
            }
            $sort = 1;
            foreach ($sourceThemes as $tp) {
                unset($tp['id']);
                $tp['event_id']   = $eventId;
                $tp['sort_order'] = $sort++;
                $tp['created_by'] = $adminId;
                $tp['updated_by'] = $adminId;
                $tp['created_at'] = date('Y-m-d H:i:s');
                $tp['updated_at'] = date('Y-m-d H:i:s');
                $db->table('event_theme_properties')->insert($tp);
            }
            echo "Copied " . count($sourceThemes) . " theme properties for event {$eventId}.\n";
        }

        // 7. Schedule Rooms
        $room = $db->table('schedule_rooms')->where('event_id', $eventId)->orderBy('id', 'ASC')->get()->getRowArray();
        if (!$room) {
            $db->table('schedule_rooms')->insert([
                'event_id'   => $eventId,
                'name'       => 'Grand Ballroom, Hotel Mulia Senayan',
                'sort_order' => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $roomId = (int)$db->insertID();
            echo "Initialized schedule_rooms for event {$eventId}.\n";
        } else {
            $roomId = (int)$room['id'];
        }

        // 8. Schedule Days & Sessions
        $day = $db->table('schedule_days')->where('event_id', $eventId)->orderBy('id', 'ASC')->get()->getRowArray();
        if (!$day) {
            $db->table('schedule_days')->insert([
                'event_id'   => $eventId,
                'day_number' => 1,
                'label'      => 'Kamis, 10 Desember 2026',
                'event_date' => '2026-12-10',
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $dayId = (int)$db->insertID();
        } else {
            $dayId = (int)$day['id'];
        }

        // 9. Speakers
        $speakersData = [
            'enggartiasto-lukita' => [
                'name'      => 'Enggartiasto Lukita',
                'role_text' => "Komisaris Utama\nB-Universe",
                'photo_url' => 'assets/img/placeholder.png',
            ],
            'menko-perekonomian' => [
                'name'      => 'Airlangga Hartarto',
                'role_text' => "Menteri Koordinator Bidang Perekonomian\nRepublik Indonesia",
                'photo_url' => 'assets/img/placeholder.png',
            ],
            'menteri-lh' => [
                'name'      => 'Hanif Faisol Nurofiq',
                'role_text' => "Menteri Lingkungan Hidup / Kepala BPLH\nRepublik Indonesia",
                'photo_url' => 'assets/img/placeholder.png',
            ],
        ];

        $speakerIdMap = [];
        foreach ($speakersData as $spkSlug => $spk) {
            $existSpk = $db->table('speakers')->where('event_id', $eventId)->where('slug', $spkSlug)->get()->getRowArray();
            if ($existSpk) {
                $db->table('speakers')->where('id', $existSpk['id'])->update([
                    'name'      => $spk['name'],
                    'role_text' => $spk['role_text'],
                    'photo_url' => $spk['photo_url'],
                    'updated_at'=> date('Y-m-d H:i:s'),
                ]);
                $speakerIdMap[$spkSlug] = (int)$existSpk['id'];
            } else {
                $db->table('speakers')->insert([
                    'event_id'   => $eventId,
                    'slug'       => $spkSlug,
                    'name'       => $spk['name'],
                    'role_text'  => $spk['role_text'],
                    'photo_url'  => $spk['photo_url'],
                    'sort_order' => count($speakerIdMap) + 1,
                    'is_active'  => 1,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $speakerIdMap[$spkSlug] = (int)$db->insertID();
            }
        }

        // Rundown Sessions for ESG
        $rundownList = [
            [
                'time_s'   => '18:00:00',
                'time_e'   => '19:00:00',
                'title'    => 'Registrasi, Gala Dinner & Welcome Performance',
                'subtitle' => 'Penyambutan Tamu Undangan dan Delegasi Korporasi',
                'speaker'  => null,
            ],
            [
                'time_s'   => '19:00:00',
                'time_e'   => '19:10:00',
                'title'    => 'Opening Ceremony & Menyanyikan Lagu Indonesia Raya',
                'subtitle' => 'Pembukaan Resmi oleh MC',
                'speaker'  => null,
            ],
            [
                'time_s'   => '19:10:00',
                'time_e'   => '19:25:00',
                'title'    => 'Welcoming Remarks: Komitmen Media Mengawal Praktik Berkelanjutan',
                'subtitle' => null,
                'speaker'  => 'enggartiasto-lukita',
            ],
            [
                'time_s'   => '19:25:00',
                'time_e'   => '19:45:00',
                'title'    => 'Keynote Speech: Akselerasi Investasi Hijau & Ketahanan Ekonomi Nasional',
                'subtitle' => null,
                'speaker'  => 'menko-perekonomian',
            ],
            [
                'time_s'   => '19:45:00',
                'time_e'   => '20:05:00',
                'title'    => 'Keynote Speech: Regulasi Lingkungan & Transisi Rendah Karbon Indonesia',
                'subtitle' => null,
                'speaker'  => 'menteri-lh',
            ],
            [
                'time_s'   => '20:05:00',
                'time_e'   => '20:25:00',
                'title'    => 'Penghargaan Kategori 1: Environmental Excellence (E-Pillar)',
                'subtitle' => 'Transisi Energi, Manajemen Limbah, & Pengurangan Emisi Karbon',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:25:00',
                'time_e'   => '20:45:00',
                'title'    => 'Penghargaan Kategori 2: Social Impact & Empowerment (S-Pillar)',
                'subtitle' => 'Pemberdayaan Masyarakat, Keselamatan Kerja, & Inklusi Sosial',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:45:00',
                'time_e'   => '21:05:00',
                'title'    => 'Penghargaan Kategori 3: Good Corporate Governance (G-Pillar)',
                'subtitle' => 'Transparansi Korporasi, Integritas Bisnis, & Manajemen Risiko',
                'speaker'  => null,
            ],
            [
                'time_s'   => '21:05:00',
                'time_e'   => '21:30:00',
                'title'    => 'Special Recognition: Best of the Best ESG Corporate of the Year',
                'subtitle' => 'Penganugerahan Utama Dewan Juri',
                'speaker'  => null,
            ],
            [
                'time_s'   => '21:30:00',
                'time_e'   => '22:00:00',
                'title'    => 'Closing, Foto Bersama, Networking & Entertainment',
                'subtitle' => null,
                'speaker'  => null,
            ],
        ];

        // Clean and re-insert sessions for ESG day
        $oldSessions = $db->table('schedule_sessions')->where('schedule_day_id', $dayId)->get()->getResultArray();
        foreach ($oldSessions as $os) {
            $db->table('session_speakers')->where('session_id', $os['id'])->delete();
            $db->table('schedule_sessions')->where('id', $os['id'])->delete();
        }

        $sortOrder = 1;
        foreach ($rundownList as $rd) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $dayId,
                'room_id'         => $roomId,
                'start_time'      => $rd['time_s'],
                'end_time'        => $rd['time_e'],
                'title'           => $rd['title'],
                'subtitle'        => $rd['subtitle'],
                'session_type'    => 'plenary',
                'sort_order'      => $sortOrder++,
                'created_by'      => $adminId,
                'updated_by'      => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $insertedSessId = (int)$db->insertID();

            if (!empty($rd['speaker']) && isset($speakerIdMap[$rd['speaker']])) {
                $db->table('session_speakers')->insert([
                    'session_id' => $insertedSessId,
                    'speaker_id' => $speakerIdMap[$rd['speaker']],
                    'role'       => 'speaker',
                    'sort_order' => 1,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        echo "Seeded " . count($rundownList) . " rundown sessions for event {$eventId}.\n";

        // 10. Invitation Template
        $template = $db->table('invitation_templates')->where('event_id', $eventId)->get()->getRowArray();
        if (!$template) {
            $db->table('invitation_templates')->insert([
                'event_id'      => $eventId,
                'template_key'  => 'esg_invitation',
                'name'          => 'ESG Template 1',
                'description'   => 'Tema portrait elegan gelap dengan nuansa kehormatan ESG, countdown, particle network, dan animasi cover lock.',
                'thumbnail_url' => null,
                'is_active'     => 1,
                'is_default'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $templateId = (int)$db->insertID();
            echo "Initialized invitation_templates for event {$eventId}.\n";
        } else {
            $templateId = (int)$template['id'];
        }

        // 11. Invitation Lists (Tamu Contoh untuk Testing Undangan)
        $sampleGuests = [
            [
                'name'            => 'Dr. Ir. Budi Santoso, M.M.',
                'institution'     => 'PT Pertamina Geothermal Energy Tbk',
                'position'        => 'Direktur Utama',
                'invitation_code' => 'ESG-BS001',
                'slug'            => 'budi-santoso-esg01',
                'email'           => 'budi.santoso@pertamina.com',
                'phone'           => '081234567890',
                'rsvp_status'     => 'pending',
                'rsvp_pax'        => 1,
            ],
            [
                'name'            => 'Sri Rahayu, S.E., M.B.A.',
                'institution'     => 'PT Astra International Tbk',
                'position'        => 'Chief of Corporate Affairs & ESG',
                'invitation_code' => 'ESG-SR002',
                'slug'            => 'sri-rahayu-esg02',
                'email'           => 'sri.rahayu@astra.co.id',
                'phone'           => '081298765432',
                'rsvp_status'     => 'pending',
                'rsvp_pax'        => 1,
            ],
        ];

        foreach ($sampleGuests as $sg) {
            $existGuest = $db->table('invitation_lists')->where('event_id', $eventId)->where('slug', $sg['slug'])->get()->getRowArray();
            if (!$existGuest) {
                $db->table('invitation_lists')->insert([
                    'event_id'          => $eventId,
                    'template_id'       => $templateId,
                    'invitation_code'   => $sg['invitation_code'],
                    'name'              => $sg['name'],
                    'institution'       => $sg['institution'],
                    'position'          => $sg['position'],
                    'slug'              => $sg['slug'],
                    'email'             => $sg['email'],
                    'phone'             => $sg['phone'],
                    'rsvp_status'       => $sg['rsvp_status'],
                    'rsvp_pax'          => $sg['rsvp_pax'],
                    'view_count'        => 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);
            }
        }
        echo "Seeded sample invitation_lists for event {$eventId}.\n";

        // 12. News Portals
        $npCount = $db->table('news_portals')->where('event_id', $eventId)->countAllResults();
        if ($npCount === 0) {
            $sourceNewsPortals = $db->table('news_portals')->where('event_id', 4)->get()->getResultArray();
            if (empty($sourceNewsPortals)) {
                $sourceNewsPortals = $db->table('news_portals')->where('event_id', 1)->get()->getResultArray();
            }
            foreach ($sourceNewsPortals as $np) {
                unset($np['id']);
                $np['event_id']   = $eventId;
                $np['created_at'] = date('Y-m-d H:i:s');
                $np['updated_at'] = date('Y-m-d H:i:s');
                $db->table('news_portals')->insert($np);
            }
            echo "Initialized news_portals for event {$eventId}.\n";
        }

        // 13. Section Logo Placements
        $placementCount = $db->table('section_logo_placements')->where('event_id', $eventId)->countAllResults();
        if ($placementCount === 0) {
            $sourcePlacements = $db->table('section_logo_placements')->where('event_id', 4)->get()->getResultArray();
            if (empty($sourcePlacements)) {
                $sourcePlacements = $db->table('section_logo_placements')->where('event_id', 1)->get()->getResultArray();
            }
            foreach ($sourcePlacements as $sp) {
                unset($sp['id']);
                $sp['event_id']   = $eventId;
                $sp['created_at'] = date('Y-m-d H:i:s');
                $sp['updated_at'] = date('Y-m-d H:i:s');
                $db->table('section_logo_placements')->insert($sp);
            }
            echo "Initialized section_logo_placements for event {$eventId}.\n";
        }

        // 14. Livestream
        $lsCount = $db->table('event_livestreams')->where('event_id', $eventId)->countAllResults();
        if ($lsCount === 0) {
            $db->table('event_livestreams')->insert([
                'event_id'     => $eventId,
                'title'        => 'Live Streaming B-Universe ESG Awards 2026',
                'youtube_url'  => 'https://www.youtube.com/@BeritaSatuChannel',
                'is_active'    => 1,
                'created_by'   => $adminId,
                'updated_by'   => $adminId,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_livestreams for event {$eventId}.\n";
        }

        echo "\n>>> Seeding completed successfully for '{$slug}' (Event ID: {$eventId})! <<<\n";
    }
}

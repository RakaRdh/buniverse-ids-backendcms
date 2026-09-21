<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FrontendDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get or create Admin user for audit trail
        $admin = $db->table('admin_users')->where('username', 'rakaherdika')->get()->getRowArray();
        $adminId = $admin['id'] ?? 1;

        // 2. Create Event: Investor Daily Summit 2026
        $eventData = [
            'slug'             => 'investor-daily-summit-2026',
            'year'             => 2026,
            'title'            => 'Investor Daily Summit 2026',
            'theme'            => "INDONESIA'S ECONOMIC MODEL",
            'event_date_start' => '2026-10-07',
            'event_date_end'   => '2026-10-08',
            'status'           => 'published',
            'news_tag_slug'    => 'ids2026',
            'template_version' => 'v1',
            'created_by'       => $adminId,
            'updated_by'       => $adminId,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $existingEvent = $db->table('events')->where('year', 2026)->get()->getRowArray();
        if ($existingEvent) {
            $eventId = $existingEvent['id'];
            $db->table('events')->where('id', $eventId)->update($eventData);
        } else {
            $db->table('events')->insert($eventData);
            $eventId = $db->insertID();
        }

        // 3. Event Hero (1:1 with event per Brief §1.2)
        $db->table('event_hero')->where('event_id', $eventId)->delete();
        $db->table('event_hero')->insert([
            'event_id'   => $eventId,
            'venue'      => 'Raffles Hotel, Jakarta',
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 4. Event Hero Images (1:N per Brief §1.2 - Carousel slides in Frontend hero.php)
        $db->table('event_hero_images')->where('event_id', $eventId)->delete();
        $heroSlides = [
            ['image_url' => 'assets/img/image1.png', 'alt_text' => 'Investor Daily Summit 2026 Stage 1', 'sort_order' => 1],
            ['image_url' => 'assets/img/image2.jpg', 'alt_text' => 'Investor Daily Summit 2026 Stage 2', 'sort_order' => 2],
            ['image_url' => 'assets/img/image3.jpg', 'alt_text' => 'Investor Daily Summit 2026 Stage 3', 'sort_order' => 3],
        ];
        foreach ($heroSlides as $slide) {
            $db->table('event_hero_images')->insert([
                'event_id'   => $eventId,
                'image_url'  => $slide['image_url'],
                'alt_text'   => $slide['alt_text'],
                'sort_order' => $slide['sort_order'],
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 5. Event Overview / Economic Model (1:1 per Brief §1.3)
        $introText = "Investor Daily Summit 2026, Indonesia’s largest investment forum is monumental gathering of power and potential. Against global uncertainty, the event became a masterclass in resilience";
        $bodyParagraphs = [
            "Indonesia is shifting toward an expansive state-capitalist model by consolidating strategic state assets under Danantara, enforcing natural resource downstreaming, and funding redistributive initiatives like Makan Bergizi Gratis (MBG) and Koperasi Desa Merah Putih (KDMP).",
            "This institutional transformation builds upon policies from previous administrations to pursue an ambitious 8% growth target, supported by initial growth rates above 5% in early 2026.",
            "To ensure inclusive and sustainable growth, policymakers must navigate significant structural risks, including Danantara's internal governance conflicts, the limited labor absorption of capital-intensive downstream industries, and workforce disruptions from automation and artificial intelligence."
        ];
        $bodyText = implode("\n\n", $bodyParagraphs);

        $db->table('event_overview')->where('event_id', $eventId)->delete();
        $db->table('event_overview')->insert([
            'event_id'   => $eventId,
            'heading'    => "INDONESIA'S ECONOMIC MODEL",
            'intro_text' => $introText,
            'body_text'  => $bodyText,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 6. Event Settings (1:1 per Brief §1.4 - Brand Assets & Links)
        $db->table('event_settings')->where('event_id', $eventId)->delete();
        $db->table('event_settings')->insert([
            'event_id'      => $eventId,
            'ticket_url'    => '#tickets',
            'logo_a_url'    => 'assets/img/logo_ids26.png',
            'logo_b_url'    => 'assets/img/logo_id.png',
            'facebook_url'  => 'https://facebook.com/investordaily',
            'instagram_url' => 'https://instagram.com/investordaily',
            'twitter_url'   => 'https://twitter.com/investordaily',
            'youtube_url'   => 'https://youtube.com/@InvestorDaily',
            'linkedin_url'  => 'https://linkedin.com/company/investordaily',
            'created_by'    => $adminId,
            'updated_by'    => $adminId,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        // 7. Event SEO (1:1 per Brief §1.5)
        $db->table('event_seo')->where('event_id', $eventId)->delete();
        $db->table('event_seo')->insert([
            'event_id'         => $eventId,
            'meta_title'       => 'Investor Daily Summit 2026 — INDONESIA\'S ECONOMIC MODEL',
            'meta_description' => substr($introText, 0, 160),
            'og_image_url'     => 'assets/img/image1.png',
            'created_by'       => $adminId,
            'updated_by'       => $adminId,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        // 8. Master Speakers (Ordered by Default: Presiden, Menteri, Petinggi lainnya)
        $db->table('speakers')->where('event_id', $eventId)->delete();
        $mockSpeakers = [
            // 1. Presiden
            'prabowo-subianto' => ['name' => 'Prabowo Subianto', 'role' => 'President of The Republic of Indonesia'],

            // 2. Menteri & Utusan Khusus Presiden (Cabinet Level)
            'rosan-roeslani' => ['name' => 'Rosan Roeslani', 'role' => 'Minister of Investment and Downstream Industry/Head of Investment Coordinating Board'],
            'bahlil-lahadalia' => ['name' => 'Bahlil Lahadalia', 'role' => 'Minister of Energy and Mineral Resources of the Republic of Indonesia'],
            'airlangga-hartarto' => ['name' => 'Airlangga Hartarto', 'role' => 'Coordinating Minister for Economic Affairs'],
            'agus-harimurti' => ['name' => 'Agus Harimurti Yudhoyono', 'role' => 'Coordinating Minister for Infrastructure & Regional Development'],
            'zulkifli-hasan' => ['name' => 'Zulkifli Hasan', 'role' => 'Coordinating Minister for Food Affairs'],
            'sri-mulyani' => ['name' => 'Sri Mulyani Indrawati', 'role' => 'Minister of Finance of The Republic of Indonesia'],
            'erick-thohir' => ['name' => 'Erick Thohir', 'role' => 'Minister of State-Owned Enterprises'],
            'hashim-djojohadikusumo' => ['name' => 'Hashim Djojohadikusumo', 'role' => 'Special Envoy of the President of The Republic of Indonesia for Climate and Energy'],
            'luhut-binsar' => ['name' => 'Luhut Binsar Pandjaitan', 'role' => 'Chairman of the National Economic Council'],
            'suahasil-nazara' => ['name' => 'Suahasil Nazara', 'role' => 'Vice Minister of Finance of Indonesia'],
            'thomas-djiwandono' => ['name' => 'Thomas Djiwandono', 'role' => 'Vice Minister of Finance of Indonesia'],

            // 3. Petinggi Negara, Regulator, & Petinggi Lainnya
            'perry-warjiyo' => ['name' => 'Perry Warjiyo', 'role' => 'Governor of Bank Indonesia'],
            'mahendra-siregar' => ['name' => 'Mahendra Siregar', 'role' => 'Chairman of Board of Commissioners Otoritas Jasa Keuangan (OJK)'],
            'purbaya-yudhi-sadewa' => ['name' => 'Purbaya Yudhi Sadewa', 'role' => 'Chairman of the Board of Commissioners LPS'],
            'anggito-abimanyu' => ['name' => 'Anggito Abimanyu', 'role' => 'Chairman of the Board of Commissioners Indonesia Deposit Insurance Corporation'],
            'pramono-anung' => ['name' => 'Pramono Anung', 'role' => 'Governor of Jakarta'],
            'enggartiasto-lukita' => ['name' => 'Enggartiasto Lukita', 'role' => 'Executive Chairman B-Universe'],
            'anindya-bakrie' => ['name' => 'Anindya Bakrie', 'role' => 'Chairman of the Indonesian Chamber of Commerce and Industry (KADIN)'],
            'shinta-kamdani' => ['name' => 'Shinta Widjaja Kamdani', 'role' => 'Chairwoman of the Indonesian Employers Association (APINDO)'],
            'iman-rachman' => ['name' => 'Iman Rachman', 'role' => 'President Director Indonesia Stock Exchange (IDX)'],
            'simon-aloysius-mantiri' => ['name' => 'Simon Aloysius Mantiri', 'role' => 'President Director PT Pertamina (Persero)'],
            'darmawan-prasodjo' => ['name' => 'Darmawan Prasodjo', 'role' => 'President Director PT PLN (Persero)'],
            'bernardus-imanto' => ['name' => 'Bernardus Imanto', 'role' => 'President Director PT Vale Indonesia Tbk'],
            'tony-wenas' => ['name' => 'Tony Wenas', 'role' => 'President Director PT Freeport Indonesia'],
            'rachmat-makkasau' => ['name' => 'Rachmat Makkasau', 'role' => 'President Director PT Amman Mineral Nusa Tenggara'],
            'iwan-dewono-budiyuwono' => ['name' => 'Iwan Dewono Budiyuwono', 'role' => 'President Director PT Alamtri Resources Indonesia Tbk'],
            'arsjad-rasjid' => ['name' => 'Arsjad Rasjid', 'role' => 'President Director PT Indika Energy Tbk'],
            'garibaldi-thohir' => ['name' => 'Garibaldi Thohir', 'role' => 'President Director PT Adaro Energy Indonesia Tbk'],
            'pandu-sjahrir' => ['name' => 'Pandu Patria Sjahrir', 'role' => 'Founding Partner AC Ventures & Chairman AFTECH'],
            'ngurah-wirawan' => ['name' => 'Ngurah Wirawan', 'role' => 'President Director Danantara Industrial Estate'],
            'suryo-pratomo' => ['name' => 'Suryo Pratomo', 'role' => 'President Director PT Gajah Tunggal Tbk'],
            'setyono-djuandi-darmono' => ['name' => 'Setyono Djuandi Darmono', 'role' => 'President Director PT Kawasan Industri Jababeka Tbk (KIJA)'],
            'indrieffouny-indra' => ['name' => 'Indrieffouny Indra', 'role' => 'President Director PT Semen Indonesia (Persero) Tbk'],
            'edi-rivai' => ['name' => "Edi Riva'i", 'role' => 'Director of Legal, External Affairs & Circular Economy PT Chandra Asri Pacific Tbk'],
            'rizki-nauli-siregar' => ['name' => 'Dr. Rizki Nauli Siregar', 'role' => 'Economist, Institute for Economic and Social Research (LPEM FEB UI)'],
            'kiki-verico' => ['name' => 'Kiki Verico ,S.E., MRI., Ph.D', 'role' => 'Associate Professor of International Economics & Vice Dean FEB UI'],
            'talitha-chairunissa' => ['name' => 'Talitha Chairunissa', 'role' => 'Economist and Public Policy Expert'],
        ];

        $speakerIdMap = [];
        $order = 1;
        foreach ($mockSpeakers as $slug => $s) {
            $db->table('speakers')->insert([
                'event_id'   => $eventId,
                'slug'       => $slug,
                'name'       => $s['name'],
                'role_text'  => $s['role'],
                'photo_url'  => 'assets/img/speakers.png',
                'sort_order' => $order++,
                'is_active'  => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $speakerIdMap[$slug] = $db->insertID();
        }

        // 9. Master Moderators (Separate master table per Brief §1.6b)
        $db->table('moderators')->where('event_id', $eventId)->delete();
        $db->table('moderators')->insert([
            'event_id'   => $eventId,
            'name'       => 'Djaka Susila',
            'role_text'  => 'Editor In Chief - Integrated Investor',
            'photo_url'  => 'assets/img/wowo.png',
            'sort_order' => 1,
            'is_active'  => 1,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $moderatorId = $db->insertID();

        $db->table('moderators')->insert([
            'event_id'   => $eventId,
            'name'       => 'Nasori Ahmad',
            'role_text'  => 'Managing Editor Investor Daily',
            'photo_url'  => 'assets/img/wowo.png',
            'sort_order' => 2,
            'is_active'  => 1,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $moderatorNasoriId = $db->insertID();

        // 10. Schedule Days (Day 1 & Day 2 per Brief §1.7)
        $db->table('schedule_days')->where('event_id', $eventId)->delete();
        $db->table('schedule_days')->insert([
            'event_id'   => $eventId,
            'day_number' => 1,
            'label'      => 'Day 1',
            'event_date' => '2026-10-07',
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $day1Id = $db->insertID();

        $db->table('schedule_days')->insert([
            'event_id'   => $eventId,
            'day_number' => 2,
            'label'      => 'Day 2',
            'event_date' => '2026-10-08',
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $day2Id = $db->insertID();

        // 11. Schedule Rooms (Brief §1.7)
        $db->table('schedule_rooms')->where('event_id', $eventId)->delete();
        $db->table('schedule_rooms')->insert([
            'event_id'   => $eventId,
            'name'       => 'Investor Daily Plenary Room',
            'sort_order' => 1,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $plenaryRoomId = $db->insertID();

        $db->table('schedule_rooms')->insert([
            'event_id'   => $eventId,
            'name'       => 'Investor Daily Room',
            'sort_order' => 2,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $investorDailyRoomId = $db->insertID();

        // 12. Schedule Sessions for Day 1 and Day 2 (MockData schedule)
        $day1Items = [
            ['time_s' => '08:30:00', 'time_e' => '09:30:00', 'title' => 'Registration', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => []],
            ['time_s' => '09:30:00', 'time_e' => '09:35:00', 'title' => 'Menyanyikan Lagu Nasional', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => []],
            ['time_s' => '09:35:00', 'time_e' => '09:50:00', 'title' => 'Welcoming Speech', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['enggartiasto-lukita']],
            ['time_s' => '09:50:00', 'time_e' => '10:20:00', 'title' => 'Apresiasi Investasi', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => []],
            ['time_s' => '10:20:00', 'time_e' => '10:50:00', 'title' => 'Opening Speech', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['rosan-roeslani']],
            ['time_s' => '10:50:00', 'time_e' => '11:20:00', 'title' => 'Keynote Speech', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['prabowo-subianto']],
            ['time_s' => '11:20:00', 'time_e' => '11:30:00', 'title' => 'Opening Ceremony', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => []],
            ['time_s' => '11:30:00', 'time_e' => '12:00:00', 'title' => "Fireside Chat 1:", 'subtitle' => 'Indonesia’s Economic Model', 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['hashim-djojohadikusumo']],
            ['time_s' => '12:00:00', 'time_e' => '13:30:00', 'title' => 'Lunch Break', 'subtitle' => null, 'type' => 'plenary', 'room' => null, 'speakers' => []],
            ['time_s' => '13:30:00', 'time_e' => '14:00:00', 'title' => "Keynote Speech 1:", 'subtitle' => 'Powering Tomorrow: Integrating Sustainable Energy and Minerals for Economic Leap', 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['bahlil-lahadalia']],
            ['time_s' => '14:00:00', 'time_e' => '15:15:00', 'title' => "Plenary Session 1:", 'subtitle' => 'Powering Tomorrow: Integrating Sustainable Energy and Minerals for Economic Leap', 'type' => 'plenary', 'room' => $plenaryRoomId, 'mod' => $moderatorId, 'speakers' => ['simon-aloysius-mantiri', 'darmawan-prasodjo', 'bernardus-imanto', 'tony-wenas', 'rachmat-makkasau', 'iwan-dewono-budiyuwono']],
            ['time_s' => '15:15:00', 'time_e' => '15:45:00', 'title' => "Special Remarks:", 'subtitle' => 'Five Centuries of Legacy: Shaping Jakarta as Inclusive World Megacity', 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['pramono-anung']],
            ['time_s' => '15:45:00', 'time_e' => '16:00:00', 'title' => 'Coffee Break', 'subtitle' => null, 'type' => 'plenary', 'room' => null, 'speakers' => []],
        ];

        $sort = 1;
        foreach ($day1Items as $item) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $day1Id,
                'room_id'         => $item['room'],
                'start_time'      => $item['time_s'],
                'end_time'        => $item['time_e'],
                'title'           => $item['title'],
                'subtitle'        => $item['subtitle'],
                'session_type'    => $item['type'],
                'sort_order'      => $sort++,
                'created_by'      => $adminId,
                'updated_by'      => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $sessId = $db->insertID();

            if (!empty($item['mod'])) {
                $db->table('session_moderators')->insert([
                    'session_id'   => $sessId,
                    'moderator_id' => $item['mod'],
                    'sort_order'   => 1,
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }

            $spkOrder = 1;
            foreach ($item['speakers'] as $slug) {
                if (isset($speakerIdMap[$slug])) {
                    $db->table('session_speakers')->insert([
                        'session_id' => $sessId,
                        'speaker_id' => $speakerIdMap[$slug],
                        'role'       => 'speaker',
                        'sort_order' => $spkOrder++,
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // Special Session A (Multi-room / Breakout session for Day 1 per MockData & Spec)
        $db->table('schedule_sessions')->insert([
            'schedule_day_id'   => $day1Id,
            'parent_session_id' => null,
            'room_id'           => null,
            'start_time'        => '15:15:00',
            'end_time'          => '15:45:00',
            'title'             => 'Special Session A',
            'subtitle'          => null,
            'session_type'      => 'special_session',
            'sort_order'        => $sort++,
            'created_by'        => $adminId,
            'updated_by'        => $adminId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        $specialSessionAId = $db->insertID();

        // 1. Session Room: Investor Daily Room
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialSessionAId,
            'room_id'    => $investorDailyRoomId,
            'sort_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $sessRoomId = $db->insertID();

        // Item 1: Keynote Speech Special Session A.1
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $sessRoomId,
            'title'           => "Keynote Speech Special Session A.1:",
            'subtitle'        => "Anchoring Indonesia's Long-Term Development Ambitions",
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $item1Id = $db->insertID();
        if (isset($speakerIdMap['anggito-abimanyu'])) {
            $db->table('session_item_speakers')->insert([
                'item_id'    => $item1Id,
                'speaker_id' => $speakerIdMap['anggito-abimanyu'],
                'sort_order' => 1,
            ]);
        }

        // Item 2: Special Session A.1 (Moderator + 5 Speakers)
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $sessRoomId,
            'title'           => "Special Session A.1:",
            'subtitle'        => "Rebalancing Growth For Stronger Indonesia",
            'sort_order'      => 2,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $item2Id = $db->insertID();

        $db->table('session_item_moderators')->insert([
            'item_id'      => $item2Id,
            'moderator_id' => $moderatorNasoriId,
        ]);

        $sub2Speakers = [
            'ngurah-wirawan',
            'suryo-pratomo',
            'setyono-djuandi-darmono',
            'edi-rivai',
            'indrieffouny-indra',
        ];
        $sub2SpkOrder = 1;
        foreach ($sub2Speakers as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $item2Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sub2SpkOrder++,
                ]);
            }
        }

        // Day 2 sessions
        $day2Items = [
            ['time_s' => '08:30:00', 'time_e' => '09:30:00', 'title' => 'Registration & Morning Networking', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => []],
            ['time_s' => '09:30:00', 'time_e' => '10:15:00', 'title' => "Keynote Speech 2:", 'subtitle' => 'Accelerating Digital Infrastructure and AI Sovereignty in Indonesia', 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['rosan-roeslani']],
            ['time_s' => '10:15:00', 'time_e' => '12:00:00', 'title' => "Plenary Session 2:", 'subtitle' => 'Capital Market Evolution & Financial Inclusivity in Transition Economy', 'type' => 'plenary', 'room' => $plenaryRoomId, 'mod' => $moderatorId, 'speakers' => ['enggartiasto-lukita', 'darmawan-prasodjo', 'tony-wenas', 'rachmat-makkasau']],
            ['time_s' => '12:00:00', 'time_e' => '13:30:00', 'title' => 'Networking Lunch Break', 'subtitle' => null, 'type' => 'plenary', 'room' => null, 'speakers' => []],
            ['time_s' => '13:30:00', 'time_e' => '15:00:00', 'title' => "Fireside Chat 2:", 'subtitle' => 'Sustainable Downstream Industrialization & Green Financing', 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['bahlil-lahadalia', 'bernardus-imanto']],
            ['time_s' => '15:00:00', 'time_e' => '15:30:00', 'title' => 'Closing Remarks & Communique Declaration', 'subtitle' => null, 'type' => 'plenary', 'room' => $plenaryRoomId, 'speakers' => ['enggartiasto-lukita']],
        ];

        $sort2 = 1;
        foreach ($day2Items as $item) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $day2Id,
                'room_id'         => $item['room'],
                'start_time'      => $item['time_s'],
                'end_time'        => $item['time_e'],
                'title'           => $item['title'],
                'subtitle'        => $item['subtitle'],
                'session_type'    => $item['type'],
                'sort_order'      => $sort2++,
                'created_by'      => $adminId,
                'updated_by'      => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $sessId = $db->insertID();

            if (!empty($item['mod'])) {
                $db->table('session_moderators')->insert([
                    'session_id'   => $sessId,
                    'moderator_id' => $item['mod'],
                    'sort_order'   => 1,
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }

            $spkOrder = 1;
            foreach ($item['speakers'] as $slug) {
                if (isset($speakerIdMap[$slug])) {
                    $db->table('session_speakers')->insert([
                        'session_id' => $sessId,
                        'speaker_id' => $speakerIdMap[$slug],
                        'role'       => 'speaker',
                        'sort_order' => $spkOrder++,
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 13. Gallery Images (7 accordion photos from Frontend gallery.php with gallery4.svg featured)
        $db->table('gallery_images')->where('event_id', $eventId)->delete();
        for ($i = 1; $i <= 7; $i++) {
            $db->table('gallery_images')->insert([
                'event_id'    => $eventId,
                'image_url'   => "assets/img/gallery{$i}.svg",
                'caption'     => "Investor Daily Summit Highlight {$i}",
                'is_featured' => ($i === 4) ? 1 : 0,
                'sort_order'  => $i,
                'created_by'  => $adminId,
                'updated_by'  => $adminId,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        // 14. Sponsor Tiers and Sponsors (Brief §1.9 and Frontend sponsors.php)
        $db->table('sponsors')->where('event_id', $eventId)->delete();
        $db->table('sponsor_tiers')->where('event_id', $eventId)->delete();

        // Tier 1: XL
        $db->table('sponsor_tiers')->insert([
            'event_id'    => $eventId,
            'name'        => 'Platinum',
            'logo_size'   => 'xl',
            'group_key'   => 'sponsor',
            'group_label' => 'SPONSORED BY:',
            'sort_order'  => 1,
            'created_by'  => $adminId,
            'updated_by'  => $adminId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $tierXLId = $db->insertID();
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierXLId, 'name' => 'Sponsor 1', 'logo_url' => 'assets/img/sponsor1.png', 'sort_order' => 1, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierXLId, 'name' => 'Sponsor 2', 'logo_url' => 'assets/img/sponsor2.png', 'sort_order' => 2, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);

        // Tier 2: LG
        $db->table('sponsor_tiers')->insert([
            'event_id'    => $eventId,
            'name'        => 'Gold',
            'logo_size'   => 'lg',
            'group_key'   => 'sponsor',
            'group_label' => 'SPONSORED BY:',
            'sort_order'  => 2,
            'created_by'  => $adminId,
            'updated_by'  => $adminId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $tierLGId = $db->insertID();
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierLGId, 'name' => 'Sponsor 3', 'logo_url' => 'assets/img/sponsor3.png', 'sort_order' => 1, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierLGId, 'name' => 'Sponsor 4', 'logo_url' => 'assets/img/sponsor4.png', 'sort_order' => 2, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);

        // Tier 3: Silver
        $db->table('sponsor_tiers')->insert([
            'event_id'    => $eventId,
            'name'        => 'Silver',
            'logo_size'   => 'md',
            'group_key'   => 'sponsor',
            'group_label' => 'SPONSORED BY:',
            'sort_order'  => 3,
            'created_by'  => $adminId,
            'updated_by'  => $adminId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $tierRegId = $db->insertID();
        for ($s = 1; $s <= 6; $s++) {
            $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierRegId, 'name' => "Silver Sponsor {$s}", 'logo_url' => "assets/img/sponsor{$s}.png", 'sort_order' => $s, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);
        }

        // Tier 4: Bronze
        $db->table('sponsor_tiers')->insert([
            'event_id'    => $eventId,
            'name'        => 'Bronze',
            'logo_size'   => 'sm',
            'group_key'   => 'sponsor',
            'group_label' => 'SPONSORED BY:',
            'sort_order'  => 4,
            'created_by'  => $adminId,
            'updated_by'  => $adminId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $tierBronzeId = $db->insertID();
        for ($b = 1; $b <= 6; $b++) {
            $imgNum = (($b % 7) === 0) ? 7 : ($b % 7);
            $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierBronzeId, 'name' => "Bronze Sponsor {$b}", 'logo_url' => "assets/img/sponsor{$imgNum}.png", 'sort_order' => $b, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);
        }

        // Tier 5: Partnership (sm)
        $db->table('sponsor_tiers')->insert([
            'event_id'    => $eventId,
            'name'        => 'Official Partnerships',
            'logo_size'   => 'sm',
            'group_key'   => 'partner',
            'group_label' => 'PARTNERSHIP:',
            'sort_order'  => 5,
            'created_by'  => $adminId,
            'updated_by'  => $adminId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $tierPartId = $db->insertID();
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierPartId, 'name' => 'Partner 1', 'logo_url' => 'assets/img/sponsor4.png', 'sort_order' => 1, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierPartId, 'name' => 'Partner 2', 'logo_url' => 'assets/img/sponsor5.png', 'sort_order' => 2, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierPartId, 'name' => 'Partner 3', 'logo_url' => 'assets/img/sponsor6.png', 'sort_order' => 3, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('sponsors')->insert(['event_id' => $eventId, 'tier_id' => $tierPartId, 'name' => 'Partner 4', 'logo_url' => 'assets/img/sponsor7.png', 'sort_order' => 4, 'created_by' => $adminId, 'updated_by' => $adminId, 'created_at' => date('Y-m-d H:i:s')]);

        echo "Frontend mock content seeded into idscms2026 successfully.\n";
    }
}

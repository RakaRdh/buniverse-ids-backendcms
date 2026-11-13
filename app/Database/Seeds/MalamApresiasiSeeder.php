<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MalamApresiasiSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $slug = 'malam-apresiasi-2026';
        $existing = $db->table('events')->where('slug', $slug)->get()->getRowArray();

        $admin = $db->table('admin_users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = $admin ? (int)$admin['id'] : 1;

        if ($existing) {
            $eventId = (int)$existing['id'];
            echo "Event with slug '{$slug}' already exists with ID: {$eventId}. Updating core data...\n";

            $db->table('events')->where('id', $eventId)->update([
                'title'            => 'BeritaSatu Malam Apresiasi 2026',
                'theme'            => 'MENGGERAKKAN PERUBAHAN MELALUI PRESTASI NYATA',
                'event_date_start' => '2026-11-20',
                'event_date_end'   => '2026-11-20',
                'status'           => 'published',
                'news_tag_slug'    => 'malam-apresiasi-2026',
                'template_version' => 'v1',
                'updated_by'       => $adminId,
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        } else {
            $newEventData = [
                'slug'             => $slug,
                'year'             => 2026,
                'title'            => 'BeritaSatu Malam Apresiasi 2026',
                'theme'            => 'MENGGERAKKAN PERUBAHAN MELALUI PRESTASI NYATA',
                'event_date_start' => '2026-11-20',
                'event_date_end'   => '2026-11-20',
                'status'           => 'published',
                'news_tag_slug'    => 'malam-apresiasi-2026',
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
                'venue'      => 'Ballroom 1, Hotel Mulia Senayan',
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_hero for event {$eventId}.\n";
        } else {
            $db->table('event_hero')->where('id', $hero['id'])->update([
                'venue'      => 'Ballroom 1, Hotel Mulia Senayan',
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
                'alt_text'   => 'BeritaSatu Malam Apresiasi 2026',
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
        $introText = "Tahun 2025 menjadi momentum penting bagi pembangunan nasional. Di tengah tantangan global dan dinamika nasional yang terus berkembang, Pemerintah daerah serta pelaku industri terbukti memiliki peran sentral dalam menjaga stabilitas ekonomi, mendorong inovasi, sekaligus mempercepat pembangunan yang inklusif dan berkelanjutan.";
        $bodyText = "Penghargaan ini diselenggarakan sebagai bentuk apresiasi atas dedikasi, inovasi, dan pencapaian para kepala daerah, pelaku industri serta para pemimpin yang telah menunjukkan kinerja luar biasa di bidangnya. Mereka telah berhasil menciptakan dampak nyata yang dirasakan oleh masyarakat sekaligus sukses juga menjadi agen perubahan.\n\nBeritasatu.com kembali menggelar Malam Apresiasi Beritasatu. Apresiasi yang sudah digelar sejak tahun 2022 ini menjadi momentum penting bagi pembangunan nasional. Terdapat 8 kategori yang dibagikan dalam Governance (4 kategori) dan Private Sector (4 kategori). Harapannya, dengan apresiasi ini bisa menjadi suntikan motivasi bagi para penerima dan inspirasi bagi yang lain.";

        if (!$overview) {
            $db->table('event_overview')->insert([
                'event_id'   => $eventId,
                'heading'    => 'BeritaSatu Malam Apresiasi 2026',
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
                'heading'    => 'BeritaSatu Malam Apresiasi 2026',
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
                'floating_bar_text'     => 'Saksikan Malam Apresiasi 2026',
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
                'copyright_text'        => 'BeritaSatu Malam Apresiasi.',
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
                'updated_by'     => $adminId,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        // 5. Event SEO
        $seo = $db->table('event_seo')->where('event_id', $eventId)->get()->getRowArray();
        if (!$seo) {
            $db->table('event_seo')->insert([
                'event_id'         => $eventId,
                'meta_title'       => 'BeritaSatu - Malam Apresiasi 2026: Menggerakkan Perubahan Melalui Prestasi Nyata',
                'meta_description' => 'BeritaSatu - Malam Apresiasi 2026: Menggerakkan Perubahan Melalui Prestasi Nyata di Ballroom 1, Hotel Mulia Senayan.',
                'og_image_url'     => 'assets/img/image1.png',
                'created_by'       => $adminId,
                'updated_by'       => $adminId,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            echo "Initialized event_seo for event {$eventId}.\n";
        }

        // 6. Theme Properties (BSA Primary, Secondary, Secondary 2 & Black Palette)
        $blackPalette = [
            '--black-1'  => '#ffffff',
            '--black-2'  => '#fcfcfc',
            '--black-3'  => '#f5f5f5',
            '--black-4'  => '#f0f0f0',
            '--black-5'  => '#d9d9d9',
            '--black-6'  => '#bfbfbf',
            '--black-7'  => '#8c8c8c',
            '--black-8'  => '#595959',
            '--black-9'  => '#454545',
            '--black-10' => '#262626',
            '--black-11' => '#1f1f1f',
            '--black-12' => '#141414',
            '--black-13' => '#000000',
        ];

        $bsaPrimaryPalette = [
            '--bsa-primary-1'  => '#fce6e6',
            '--bsa-primary-2'  => '#f9c2c2',
            '--bsa-primary-3'  => '#f49292',
            '--bsa-primary-4'  => '#ef5f5f',
            '--bsa-primary-5'  => '#ea2f2f',
            '--bsa-primary-6'  => '#e50101',
            '--bsa-primary-7'  => '#c30101',
            '--bsa-primary-8'  => '#a30101',
            '--bsa-primary-9'  => '#830101',
            '--bsa-primary-10' => '#670000',
        ];

        $bsaSecondaryPalette = [
            '--bsa-secondary-1'  => '#f5e6e6',
            '--bsa-secondary-2'  => '#e7c2c2',
            '--bsa-secondary-3'  => '#d39192',
            '--bsa-secondary-4'  => '#bf5e5f',
            '--bsa-secondary-5'  => '#ab2e2f',
            '--bsa-secondary-6'  => '#990001',
            '--bsa-secondary-7'  => '#820001',
            '--bsa-secondary-8'  => '#6d0001',
            '--bsa-secondary-9'  => '#570001',
            '--bsa-secondary-10' => '#450000',
        ];

        $bsaSecondary2Palette = [
            '--bsa-secondary-2-1'  => '#f1e6e6',
            '--bsa-secondary-2-2'  => '#ddc2c2',
            '--bsa-secondary-2-3'  => '#c39191',
            '--bsa-secondary-2-4'  => '#a75e5e',
            '--bsa-secondary-2-5'  => '#8c2e2e',
            '--bsa-secondary-2-6'  => '#730000',
            '--bsa-secondary-2-7'  => '#620000',
            '--bsa-secondary-2-8'  => '#520000',
            '--bsa-secondary-2-9'  => '#420000',
            '--bsa-secondary-2-10' => '#340000',
        ];

        $semanticPalette = [
            '--bg-page'                   => '#000000',
            '--bg-section-alt'            => '#141414',
            '--bg-card'                   => '#1f1f1f',
            '--bg-card-gradient'          => 'linear-gradient(135deg, #1f1f1f 0%, #141414 100%)',
            '--bg-pill'                   => '#e7c2c2',
            '--bg-pill-active'            => '#ab2e2f',
            '--text-primary'              => '#ffffff',
            '--text-secondary'            => '#d9d9d9',
            '--text-muted'                => '#8c8c8c',
            '--text-dark'                 => '#000000',
            '--accent-cyan'               => '#ab2e2f',
            '--accent-light'              => '#d39192',
            '--accent-blue'               => '#ab2e2f',
            '--accent-glow'               => 'rgba(171, 46, 47, 0.45)',
            '--border-divider'            => 'rgba(255, 255, 255, 0.12)',
            '--border-subtle'             => 'rgba(171, 46, 47, 0.35)',
            '--navbar-bg'                 => 'transparent',
            '--navbar-cta-bg'             => '#e50101',
            '--navbar-cta-text'           => '#ffffff',
            '--navbar-cta-hover-bg'       => '#ab2e2f',
            '--navbar-cta-hover-text'     => '#ffffff',
            '--navbar-cta-hover-outline'  => '#e50101',
            '--footer-bg'                 => '#450000',
            '--footer-border-color'       => 'rgba(255, 255, 255, 0.12)',
            '--footer-social-bg'          => '#262626',
            '--footer-social-color'       => '#ffffff',
            '--footer-text-color'         => '#d9d9d9',
        ];

        $themeModel = new \App\Models\EventThemePropertyModel();
        $sort = 1;
        foreach ($blackPalette as $prop => $val) {
            $num = str_replace('--black-', '', $prop);
            $themeModel->upsertProperty($eventId, $prop, $val, 'black', "Black {$num}", $adminId);
            $themeModel->upsertProperty($eventId, "--neutral-{$num}", $val, 'neutral', "Neutral {$num}", $adminId);
        }
        foreach ($bsaPrimaryPalette as $prop => $val) {
            $num = str_replace('--bsa-primary-', '', $prop);
            $themeModel->upsertProperty($eventId, $prop, $val, 'bsa-primary', "BSA Primary {$num}", $adminId);
            $themeModel->upsertProperty($eventId, "--red-{$num}", $val, 'red', "Red {$num}", $adminId);
            $themeModel->upsertProperty($eventId, "--primary-1-{$num}", $val, 'primary-1', "BSA Primary {$num}", $adminId);
        }
        foreach ($bsaSecondaryPalette as $prop => $val) {
            $num = str_replace('--bsa-secondary-', '', $prop);
            $themeModel->upsertProperty($eventId, $prop, $val, 'bsa-secondary', "BSA Secondary {$num}", $adminId);
            $themeModel->upsertProperty($eventId, "--blue-{$num}", $val, 'blue', "Blue {$num}", $adminId);
            $themeModel->upsertProperty($eventId, "--primary-2-{$num}", $val, 'primary-2', "BSA Secondary {$num}", $adminId);
        }
        foreach ($bsaSecondary2Palette as $prop => $val) {
            $num = str_replace('--bsa-secondary-2-', '', $prop);
            $themeModel->upsertProperty($eventId, $prop, $val, 'bsa-secondary-2', "BSA Secondary 2-{$num}", $adminId);
        }
        foreach ($semanticPalette as $prop => $val) {
            $category = str_starts_with($prop, '--navbar') ? 'navbar' : (str_starts_with($prop, '--footer') ? 'footer' : 'semantic');
            $label = ucwords(str_replace(['--', '-'], [' ', ' '], $prop));
            $themeModel->upsertProperty($eventId, $prop, $val, $category, $label, $adminId);
        }
        echo "Synchronized BSA Primary, Secondary & Secondary 2 theme properties for event {$eventId}.\n";

        // 7. Schedule Rooms
        $roomCount = $db->table('schedule_rooms')->where('event_id', $eventId)->countAllResults();
        if ($roomCount === 0) {
            $db->table('schedule_rooms')->insert([
                'event_id'   => $eventId,
                'name'       => 'Ballroom 1, Hotel Mulia Senayan',
                'sort_order' => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $roomId = (int)$db->insertID();
            echo "Initialized schedule_rooms for event {$eventId}.\n";
        } else {
            $firstRoom = $db->table('schedule_rooms')->where('event_id', $eventId)->orderBy('id', 'ASC')->get()->getRowArray();
            $roomId = (int)$firstRoom['id'];
        }

        // 8. Schedule Days & Sessions
        // 8. Schedule Days & Sessions
        $day = $db->table('schedule_days')->where('event_id', $eventId)->orderBy('id', 'ASC')->get()->getRowArray();
        if (!$day) {
            $db->table('schedule_days')->insert([
                'event_id'   => $eventId,
                'day_number' => 1,
                'label'      => 'Kamis, 20 November 2026',
                'event_date' => '2026-11-20',
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
            'tito-karnavian' => [
                'name'      => 'JENDRAL POL. (PURN.) PROF. DRS. H. TITO KARNAVIAN, M.A., PH.D.',
                'role_text' => "Menteri Dalam Negeri\nRepublik Indonesia",
                'photo_url' => 'assets/img/placeholder.png',
            ],
            'pratikno' => [
                'name'      => 'Pratikno',
                'role_text' => "Menteri Koordinator Bidang Pembangunan Manusia dan Kebudayaan RI",
                'photo_url' => 'assets/img/placeholder.png',
            ],
        ];

        $speakerIdMap = [];
        foreach ($speakersData as $slug => $spk) {
            $existSpk = $db->table('speakers')->where('event_id', $eventId)->where('slug', $slug)->get()->getRowArray();
            if ($existSpk) {
                $db->table('speakers')->where('id', $existSpk['id'])->update([
                    'name'      => $spk['name'],
                    'role_text' => $spk['role_text'],
                    'photo_url' => $spk['photo_url'],
                    'updated_at'=> date('Y-m-d H:i:s'),
                ]);
                $speakerIdMap[$slug] = (int)$existSpk['id'];
            } else {
                $db->table('speakers')->insert([
                    'event_id'   => $eventId,
                    'slug'       => $slug,
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
                $speakerIdMap[$slug] = (int)$db->insertID();
            }
        }

        // Rundown Sessions requested by user
        $rundownList = [
            [
                'time_s'   => '18:00:00',
                'time_e'   => '19:00:00',
                'time_lbl' => '18.00 WIB',
                'title'    => 'Registrasi, Dinner & Band Performance',
                'subtitle' => null,
                'speaker'  => null,
            ],
            [
                'time_s'   => '19:00:00',
                'time_e'   => '19:10:00',
                'time_lbl' => '19.00 WIB',
                'title'    => 'Pembukaan MC & Menyanyikan Lagu Indonesia Raya',
                'subtitle' => null,
                'speaker'  => null,
            ],
            [
                'time_s'   => '19:10:00',
                'time_e'   => '19:20:00',
                'time_lbl' => '19.10 WIB',
                'title'    => 'Welcoming Speech',
                'subtitle' => null,
                'speaker'  => 'enggartiasto-lukita',
            ],
            [
                'time_s'   => '19:20:00',
                'time_e'   => '19:25:00',
                'time_lbl' => '19.20 WIB',
                'title'    => 'VT BeritaSatu Malam Inspirasi',
                'subtitle' => null,
                'speaker'  => null,
            ],
            [
                'time_s'   => '19:25:00',
                'time_e'   => '19:45:00',
                'time_lbl' => '19.25 WIB',
                'title'    => 'Keynote Speech',
                'subtitle' => null,
                'speaker'  => 'tito-karnavian',
            ],
            [
                'time_s'   => '19:45:00',
                'time_e'   => '19:48:00',
                'time_lbl' => '19.45 WIB',
                'title'    => 'Token Appreciation & Photo',
                'subtitle' => null,
                'speaker'  => null,
            ],
            [
                'time_s'   => '19:48:00',
                'time_e'   => '20:08:00',
                'time_lbl' => '19.48 WIB',
                'title'    => 'Keynote Speech',
                'subtitle' => null,
                'speaker'  => 'pratikno',
            ],
            [
                'time_s'   => '20:08:00',
                'time_e'   => '20:11:00',
                'time_lbl' => '20.08 WIB',
                'title'    => 'Token Appreciation & Photo',
                'subtitle' => null,
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:11:00',
                'time_e'   => '20:16:00',
                'time_lbl' => '20.11 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 1 - KOMUNIKASI KREATIF BERDAMPAK',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:16:00',
                'time_e'   => '20:24:00',
                'time_lbl' => '20.16 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 2 - INOVASI TEKNOLOGI & DIGITAL',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:24:00',
                'time_e'   => '20:29:00',
                'time_lbl' => '20.24 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 3 - INSPIRASI SOSIAL & KEPEMIMPINAN',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:29:00',
                'time_e'   => '20:34:00',
                'time_lbl' => '20.29 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 4 - INSPIRASI WIRAUSAHA & EKONOMI KREATIF',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:34:00',
                'time_e'   => '20:39:00',
                'time_lbl' => '20.34 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 5 - INSPIRASI KREATIF & BUDAYA',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:39:00',
                'time_e'   => '20:47:00',
                'time_lbl' => '20.39 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 6 - INOVASI TEKNOLOGI & LAYANAN PUBLIK',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:47:00',
                'time_e'   => '20:52:00',
                'time_lbl' => '20.47 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 7 - INSPIRASI SOSIAL & KESEJAHTERAAN',
                'speaker'  => null,
            ],
            [
                'time_s'   => '20:52:00',
                'time_e'   => '21:00:00',
                'time_lbl' => '20.52 WIB',
                'title'    => 'BeritaSatu Malam Apresiasi',
                'subtitle' => 'Kategori 8 - INSPIRASI WIRAUSAHA & EKONOMI KREATIF',
                'speaker'  => null,
            ],
            [
                'time_s'   => '21:00:00',
                'time_e'   => '22:00:00',
                'time_lbl' => '21.00 WIB',
                'title'    => 'Closing, Networking, & Band Performance',
                'subtitle' => null,
                'speaker'  => null,
            ],
        ];

        // Clean existing sessions for this day
        $oldSessions = $db->table('schedule_sessions')->where('schedule_day_id', $dayId)->get()->getResultArray();
        foreach ($oldSessions as $os) {
            $db->table('session_speakers')->where('session_id', $os['id'])->delete();
            $db->table('schedule_sessions')->where('id', $os['id'])->delete();
        }

        // Insert fresh sessions
        $sortOrder = 1;
        foreach ($rundownList as $rd) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $dayId,
                'room_id'         => null,
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

        // 10. News Portals
        $npCount = $db->table('news_portals')->where('event_id', $eventId)->countAllResults();
        if ($npCount === 0) {
            $sourceNewsPortals = $db->table('news_portals')->where('event_id', 1)->get()->getResultArray();
            if (!empty($sourceNewsPortals)) {
                foreach ($sourceNewsPortals as $np) {
                    unset($np['id']);
                    $np['event_id'] = $eventId;
                    $np['created_at'] = date('Y-m-d H:i:s');
                    $np['updated_at'] = date('Y-m-d H:i:s');
                    $db->table('news_portals')->insert($np);
                }
                echo "Initialized news_portals for event {$eventId}.\n";
            }
        }

        // 11. Section Logo Placements
        $placementCount = $db->table('section_logo_placements')->where('event_id', $eventId)->countAllResults();
        if ($placementCount === 0) {
            $sourcePlacements = $db->table('section_logo_placements')->where('event_id', 1)->get()->getResultArray();
            foreach ($sourcePlacements as $sp) {
                unset($sp['id']);
                $sp['event_id'] = $eventId;
                $sp['created_at'] = date('Y-m-d H:i:s');
                $sp['updated_at'] = date('Y-m-d H:i:s');
                $db->table('section_logo_placements')->insert($sp);
            }
            echo "Initialized section_logo_placements for event {$eventId}.\n";
        }

        // 12. Livestream
        $lsCount = $db->table('event_livestreams')->where('event_id', $eventId)->countAllResults();
        if ($lsCount === 0) {
            $db->table('event_livestreams')->insert([
                'event_id'     => $eventId,
                'title'        => 'Live Streaming Malam Apresiasi 2026',
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

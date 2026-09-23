<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get or create active event
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
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $event = $db->table('events')->where('year', 2026)->get()->getRowArray();
        if (!$event) {
            $db->table('events')->insert($eventData);
            $eventId = $db->insertID();
        } else {
            $eventId = (int)$event['id'];
        }

        // 1b. Seed / Update Admin Users
        $adminsToSeed = [
            [
                'name'          => 'Raka Herdika',
                'username'      => 'rakah',
                'email'         => 'rakaherdika15@gmail.com',
                'password_hash' => password_hash('adm!n123', PASSWORD_DEFAULT),
                'role'          => 'superadmin',
                'is_active'     => 1,
            ],
            [
                'name'          => 'Bryan',
                'username'      => 'bryans',
                'email'         => 'bryan@b-universe.id',
                'password_hash' => password_hash('adm!n123', PASSWORD_DEFAULT),
                'role'          => 'superadmin',
                'is_active'     => 1,
            ],
        ];

        foreach ($adminsToSeed as $adm) {
            $existingAdm = $db->table('admin_users')->where('username', $adm['username'])->orWhere('email', $adm['email'])->get()->getRowArray();
            if (!$existingAdm) {
                $db->table('admin_users')->insert(array_merge($adm, [
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]));
            } else {
                $db->table('admin_users')->where('id', $existingAdm['id'])->update([
                    'name'          => $adm['name'],
                    'username'      => $adm['username'],
                    'email'         => $adm['email'],
                    'password_hash' => $adm['password_hash'],
                    'role'          => $adm['role'],
                    'is_active'     => 1,
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Get admin ID (rakah)
        $admin = $db->table('admin_users')->where('username', 'rakah')->get()->getRowArray();
        $adminId = $admin ? (int)$admin['id'] : 1;

        // 2. Setup Schedule Rooms
        $db->table('schedule_rooms')->where('event_id', $eventId)->delete();

        $roomsData = [
            ['name' => 'Investor Daily Plenary Room', 'sort_order' => 1],
            ['name' => 'Investor Daily Room',         'sort_order' => 2],
            ['name' => 'Sponsor Room',                'sort_order' => 3], // mewakili <<Sponsor>> Room
            ['name' => 'Beritasatu Room',             'sort_order' => 4],
        ];

        $roomIdMap = [];
        foreach ($roomsData as $r) {
            $db->table('schedule_rooms')->insert([
                'event_id'   => $eventId,
                'name'       => $r['name'],
                'sort_order' => $r['sort_order'],
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $roomIdMap[$r['name']] = $db->insertID();
        }

        // 3. Setup Moderators
        // Clear old session relations first to avoid foreign key issues
        $db->table('session_moderators')->emptyTable();
        $db->table('session_item_moderators')->emptyTable();
        $db->table('session_speakers')->emptyTable();
        $db->table('session_item_speakers')->emptyTable();
        $db->table('schedule_session_items')->emptyTable();
        $db->table('schedule_session_rooms')->emptyTable();
        $db->table('schedule_sessions')->where('schedule_day_id IN (SELECT id FROM schedule_days WHERE event_id = ' . $eventId . ')')->delete();
        $db->table('moderators')->where('event_id', $eventId)->delete();

        $moderatorsData = [
            ['name' => 'Djaka Susila',         'role' => 'Editor in Chief Investor Daily & Jakarta Globe', 'slug' => 'djaka-susila'],
            ['name' => 'Nasori Ahmad',         'role' => 'Managing Editor Investor Daily',                 'slug' => 'nasori-ahmad'],
            ['name' => 'Wahyu Setyowati',      'role' => 'Deputy Editor in Chief Investor Daily & Jakarta Globe', 'slug' => 'wahyu-setyowati'],
            ['name' => 'Andhika Aryo P.',      'role' => 'Head of Programming B Universe',                'slug' => 'andhika-aryo-p'],
            ['name' => 'Iqbal Suwitamihardja', 'role' => 'Business News Anchor',                           'slug' => 'iqbal-suwitamihardja'],
            ['name' => 'Syukri Rahmatullah',   'role' => 'Editor in Chief Beritasatu.com',                 'slug' => 'syukri-rahmatullah'],
            ['name' => 'Zaki Amrullah',        'role' => 'Editor in Chief BTV',                            'slug' => 'zaki-amrullah'],
            ['name' => 'Anthony Wonsono',      'role' => 'Editorial Board B Universe',                     'slug' => 'anthony-wonsono'],
            ['name' => 'Wiendy Hapsari',       'role' => 'Head of Research B Universe',                    'slug' => 'wiendy-hapsari'],
        ];

        $moderatorIdMap = [];
        $modSort = 1;
        foreach ($moderatorsData as $mod) {
            $db->table('moderators')->insert([
                'event_id'   => $eventId,
                'name'       => $mod['name'],
                'role_text'  => $mod['role'],
                'photo_url'  => null, // placeholder default
                'sort_order' => $modSort++,
                'is_active'  => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $moderatorIdMap[$mod['slug']] = $db->insertID();
        }

        // 4. Setup Speakers
        $db->table('speakers')->where('event_id', $eventId)->delete();

        $speakersData = [
            // Tier 1: Presiden RI
            ['name' => 'Prabowo Subianto',          'role' => 'President of the Republic of Indonesia', 'slug' => 'prabowo-subianto'],

            // Tier 2: Menteri & Petinggi Lembaga Negara
            ['name' => 'Rosan Roeslani',             'role' => 'Minister of Investment and Downstream Industry / Head of Investment Coordinating Board / CEO Danantara', 'slug' => 'rosan-roeslani'],
            ['name' => 'Bahlil Lahadalia',          'role' => 'Minister of Energy and Mineral Resources', 'slug' => 'bahlil-lahadalia'],
            ['name' => 'Airlangga Hartarto',        'role' => 'Coordinating Minister for Economic Affairs', 'slug' => 'airlangga-hartarto'],
            ['name' => 'Teddy Indra Wijaya',        'role' => 'Cabinet Secretary of the Republic of Indonesia', 'slug' => 'teddy-indra-wijaya'],
            ['name' => 'Suahasil Nazara',           'role' => 'Minister of Finance of Indonesia', 'slug' => 'suahasil-nazara'],
            ['name' => 'Nezar Patria',              'role' => 'Vice Minister of Communications and Digital Affairs', 'slug' => 'nezar-patria'],
            ['name' => 'Dony Oskaria',              'role' => 'COO Danantara / Head of State-Owned Enterprises Regulatory Agency, BP BUMN', 'slug' => 'dony-oskaria'],
            ['name' => 'Hasan Fawzi',               'role' => 'Chief Executive of Capital Market, Financial Derivative and Carbon, OJK', 'slug' => 'hasan-fawzi'],
            ['name' => 'Anggito Abimanyu',          'role' => 'Chairman of the Board of Commissioners, LPS', 'slug' => 'anggito-abimanyu'],
            ['name' => 'Muhammad Qodari',           'role' => 'Head of the Government Communication Agency / Bakom RI', 'slug' => 'muhammad-qodari'],
            ['name' => 'Mohammad Mahfud MD',        'role' => 'Coordinating Minister for Political, Legal, and Security Affairs 2019–2024', 'slug' => 'mohammad-mahfud-md'],
            ['name' => 'Pramono Anung',             'role' => 'Governor of Jakarta', 'slug' => 'pramono-anung'],
            ['name' => 'Hashim Djojohadikusumo',    'role' => 'Special Envoy of the President for Climate and Energy', 'slug' => 'hashim-djojohadikusumo'],
            ['name' => 'Filianingsih Hendarta',     'role' => 'Deputy Governor, Bank Indonesia', 'slug' => 'filianingsih-hendarta'],
            ['name' => 'Iding Pardi',               'role' => 'Director of Business Development, PT Bursa Efek Indonesia', 'slug' => 'iding-pardi'],
            ['name' => 'Kamrussamad',               'role' => 'Anggota DPR-RI 2024–2029', 'slug' => 'kamrussamad'],

            // Tier 3: Penyelenggara & Media
            ['name' => 'Enggartiasto Lukita',       'role' => 'Executive Chairman B-Universe', 'slug' => 'enggartiasto-lukita'],
            ['name' => 'Najwa Shihab',              'role' => 'Senior Journalist', 'slug' => 'najwa-shihab'],

            // Tier 4: Duta Besar Negara Sahabat
            ['name' => 'H.E. Wang Lutong',          'role' => "Ambassador of the People's Republic of China", 'slug' => 'wang-lutong'],
            ['name' => 'H.E. Yoon Soon-gu',         'role' => 'Ambassador of the Republic of Korea', 'slug' => 'yoon-soon-gu'],
            ['name' => 'H.E. Dato\' Muzafar Shah Mustafa', 'role' => 'Ambassador-Designate of Malaysia', 'slug' => 'muzafar-shah-mustafa'],

            // Tier 5: Tokoh Publik & Pengamat / Pakar
            ['name' => 'Burhanuddin Muhtadi',       'role' => 'Executive Director, Indikator Politik Indonesia', 'slug' => 'burhanuddin-muhtadi'],
            ['name' => 'Yunarto Wijaya',            'role' => 'Executive Director, Charta Politika Indonesia', 'slug' => 'yunarto-wijaya'],
            ['name' => 'Prof. Mohamad Ikhsan',      'role' => 'Professor of Economics, LPEM FEB UI', 'slug' => 'mohamad-ikhsan'],
            ['name' => 'Iman Pambagyo',             'role' => 'Former Director General of International Trade Negotiations, Ministry of Trade 2012–2014', 'slug' => 'iman-pambagyo'],
            ['name' => 'Shinta W. Kamdani',         'role' => 'Chairwoman, APINDO', 'slug' => 'shinta-w-kamdani'],
            ['name' => 'Gema Goeayrdi',             'role' => 'Founder & CEO PT Astronacci International', 'slug' => 'gema-goeayrdi'],

            // Tier 6: Petinggi BUMN & Korporasi Swasta
            ['name' => 'Oki Muraza',                'role' => 'Deputy President Director & Deputy CEO, PT Pertamina', 'slug' => 'oki-muraza'],
            ['name' => 'Darmawan Prasodjo',         'role' => 'President Director, PT PLN', 'slug' => 'darmawan-prasodjo'],
            ['name' => 'Rachmat Makkasau',          'role' => 'Chairman, Indonesian Mining Association', 'slug' => 'rachmat-makkasau'],
            ['name' => 'Tony Wenas',                'role' => 'President Director, PT Freeport Indonesia', 'slug' => 'tony-wenas'],
            ['name' => 'Bernardus Irmanto',         'role' => 'President Director, PT Vale Indonesia Tbk', 'slug' => 'bernardus-irmanto'],
            ['name' => 'Iwan Dewono Budiyuwono',    'role' => 'President Director, PT Alamtri Resources Indonesia Tbk', 'slug' => 'iwan-dewono-budiyuwono'],
            ['name' => 'Ngurah Wirawan',            'role' => 'President Director, PT Danareksa', 'slug' => 'ngurah-wirawan'],
            ['name' => 'Suryopratomo',              'role' => 'President Director, PT Gajah Tunggal Tbk', 'slug' => 'suryopratomo'],
            ['name' => 'Setyono Djuandi Darmono',   'role' => 'President Director, PT KIJA', 'slug' => 'setyono-djuandi-darmono'],
            ['name' => 'Edi Riva\'i',               'role' => 'Director of Legal, External Affairs & Circular Economy, PT Chandra Asri Pacific Tbk', 'slug' => 'edi-rivai'],
            ['name' => 'Indrieffouny Indra',        'role' => 'President Director, PT Semen Indonesia', 'slug' => 'indrieffouny-indra'],
            ['name' => 'Rahmad Pribadi',            'role' => 'President Director, PT Pupuk Indonesia', 'slug' => 'rahmad-pribadi'],
            ['name' => 'Andry Asmoro',              'role' => 'Chief Economist, PT Bank Mandiri', 'slug' => 'andry-asmoro'],
            ['name' => 'Dharma Djojonegoro',        'role' => 'EVP Corporate Development, PT Freeport Indonesia Tbk', 'slug' => 'dharma-djojonegoro'],
            ['name' => 'Rudy',                      'role' => 'President Director, PT Astra International Tbk', 'slug' => 'rudy'],
            ['name' => 'Irwan Hidayat',             'role' => 'President Director, PT Sido Muncul Tbk', 'slug' => 'irwan-hidayat'],
            ['name' => 'Dr. Rudolf Tjandra',        'role' => 'CEO & President Director, Kalbe Nutritionals', 'slug' => 'rudolf-tjandra'],
            ['name' => 'Agung Laksamana',           'role' => 'General Secretary Director, Danone Indonesia', 'slug' => 'agung-laksamana'],
            ['name' => 'Dian Siswarini',            'role' => 'President Director, PT Telkom Indonesia', 'slug' => 'dian-siswarini'],
            ['name' => 'Achmad Muchtasyar',         'role' => 'President Director, PT Pelabuhan Indonesia', 'slug' => 'achmad-muchtasyar'],
            ['name' => 'Rivan A. Purwantono',       'role' => 'President Director, PT Jasa Marga', 'slug' => 'rivan-a-purwantono'],
            ['name' => 'Bani M. Mulia',             'role' => 'President Director, PT Samudera Indonesia Tbk', 'slug' => 'bani-m-mulia'],
            ['name' => 'Anggoro Eko Cahyo',         'role' => 'President Director, PT Bank Syariah Indonesia', 'slug' => 'anggoro-eko-cahyo'],
            ['name' => 'Damar Latri Setiawan',      'role' => 'CEO, PT Pegadaian', 'slug' => 'damar-latri-setiawan'],
            ['name' => 'Nursalam',                  'role' => 'President Director, ICDX', 'slug' => 'nursalam'],
            ['name' => 'Minarni Tanu',              'role' => 'Co Founder, PT Laku Emas Indonesia', 'slug' => 'minarni-tanu'],
            ['name' => 'Hexana Tri Sasongko',       'role' => 'President Director, Indonesia Financial Group', 'slug' => 'hexana-tri-sasongko'],
            ['name' => 'Putrama Wahju Setyawan',    'role' => 'Chairman, Himbara', 'slug' => 'putrama-wahju-setyawan'],
            ['name' => 'Zabrina Raissa',            'role' => 'Head of Online Trading / Corporate Finance & Strategic Analysis Expert, PT Ciptadana Sekuritas Asia', 'slug' => 'zabrina-raissa'],
            ['name' => 'Andry Hakim',               'role' => 'Founder & CIO, @hakimson.id', 'slug' => 'andry-hakim'],
            ['name' => 'Claudia Kolonas',           'role' => 'Co-Founder, Pluang', 'slug' => 'claudia-kolonas'],
            ['name' => 'Anita Bernardus',           'role' => 'VP Communication & Sustainability, PT Bumi Resources Tbk', 'slug' => 'anita-bernardus'],
            ['name' => 'Nugroho',                   'role' => 'President Director, PT Telkomsel', 'slug' => 'nugroho'],
            ['name' => 'Marlo Budiman',             'role' => 'Vice President Director, PT Dian Swastatika Sentosa', 'slug' => 'marlo-budiman'],
            ['name' => 'Ahmad Zulfikar Said',       'role' => 'President Director, NexAI', 'slug' => 'ahmad-zulfikar-said'],
        ];

        $speakerIdMap = [];
        $spkSort = 1;
        foreach ($speakersData as $spk) {
            $db->table('speakers')->insert([
                'event_id'   => $eventId,
                'slug'       => $spk['slug'],
                'name'       => $spk['name'],
                'role_text'  => $spk['role'],
                'photo_url'  => ($spk['slug'] === 'prabowo-subianto') ? 'speakers.png' : null,
                'sort_order' => $spkSort++,
                'is_active'  => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $speakerIdMap[$spk['slug']] = $db->insertID();
        }

        // 5. Setup Schedule Days
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

        // 6. Day 1 Sessions (Rabu, 7 Oktober 2026)
        $day1Sessions = [
            // 1.
            ['time_s' => '08:00:00', 'time_e' => '09:30:00', 'title' => 'Registration', 'subtitle' => 'Registrasi Tamu Undangan', 'type' => 'registration', 'room' => null, 'speakers' => []],
            // 2.
            ['time_s' => '09:30:00', 'time_e' => '09:35:00', 'title' => 'Menyanyikan Lagu Nasional', 'subtitle' => 'Lagu Indonesia Raya', 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => []],
            // 3.
            ['time_s' => '09:35:00', 'time_e' => '09:50:00', 'title' => 'Welcoming Speech', 'subtitle' => null, 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['enggartiasto-lukita']],
            // 4.
            ['time_s' => '09:50:00', 'time_e' => '10:20:00', 'title' => 'Apresiasi Investasi', 'subtitle' => null, 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => []],
            // 5.
            ['time_s' => '10:20:00', 'time_e' => '10:50:00', 'title' => 'Opening Speech', 'subtitle' => null, 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['rosan-roeslani']],
            // 6.
            ['time_s' => '10:50:00', 'time_e' => '11:20:00', 'title' => 'Keynote Speech', 'subtitle' => null, 'type' => 'keynote', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['prabowo-subianto']],
            // 7.
            ['time_s' => '11:20:00', 'time_e' => '11:30:00', 'title' => 'Opening Ceremony', 'subtitle' => null, 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => []],
            // 8.
            ['time_s' => '11:30:00', 'time_e' => '12:00:00', 'title' => 'Fireside Chat 1', 'subtitle' => "Indonesia's Economic Model", 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['hashim-djojohadikusumo']],
            // 9.
            ['time_s' => '12:00:00', 'time_e' => '13:30:00', 'title' => 'C-Suite Strategic Luncheon', 'subtitle' => 'Exclusive/Invitation Only', 'type' => 'break', 'room' => null, 'speakers' => []],
            // 10.
            ['time_s' => '13:30:00', 'time_e' => '14:00:00', 'title' => 'Keynote Speech Plenary Session 1', 'subtitle' => 'Powering Tomorrow: Integrating Sustainable Energy and Minerals for Economic Leap', 'type' => 'keynote', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['bahlil-lahadalia']],
            // 11.
            ['time_s' => '14:00:00', 'time_e' => '15:15:00', 'title' => 'Plenary Session 1', 'subtitle' => 'Powering Tomorrow: Integrating Sustainable Energy and Minerals for Economic Leap', 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'mod' => 'djaka-susila', 'speakers' => ['oki-muraza', 'darmawan-prasodjo', 'rachmat-makkasau', 'tony-wenas', 'bernardus-irmanto', 'iwan-dewono-budiyuwono']],
            // 12.
            ['time_s' => '15:15:00', 'time_e' => '15:45:00', 'title' => 'Special Remarks', 'subtitle' => 'Five Centuries of Legacy: Shaping Jakarta as Inclusive World Megacity', 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['pramono-anung']],
            // 13.
            ['time_s' => '15:45:00', 'time_e' => '16:00:00', 'title' => 'Afternoon Coffee Break & Networking', 'subtitle' => null, 'type' => 'break', 'room' => null, 'speakers' => []],
        ];

        $sort1 = 1;
        foreach ($day1Sessions as $sess) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $day1Id,
                'room_id'         => $sess['room'],
                'start_time'      => $sess['time_s'],
                'end_time'        => $sess['time_e'],
                'title'           => $sess['title'],
                'subtitle'        => $sess['subtitle'],
                'session_type'    => $sess['type'],
                'sort_order'      => $sort1++,
                'created_by'      => $adminId,
                'updated_by'      => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $sessId = $db->insertID();

            if (!empty($sess['mod']) && isset($moderatorIdMap[$sess['mod']])) {
                $db->table('session_moderators')->insert([
                    'session_id'   => $sessId,
                    'moderator_id' => $moderatorIdMap[$sess['mod']],
                    'sort_order'   => 1,
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }

            $spkOrder = 1;
            foreach ($sess['speakers'] as $slug) {
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

        // 14. Day 1: Special Sessions A (16:00–17:30, multi-room di 3 ruangan)
        $db->table('schedule_sessions')->insert([
            'schedule_day_id'   => $day1Id,
            'parent_session_id' => null,
            'room_id'           => null,
            'start_time'        => '16:00:00',
            'end_time'          => '17:30:00',
            'title'             => 'Special Sessions A',
            'subtitle'          => null,
            'session_type'      => 'special_session',
            'sort_order'        => $sort1++,
            'created_by'        => $adminId,
            'updated_by'        => $adminId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        $specialAId = $db->insertID();

        // 14.1 & 14.2: Room 1: Investor Daily Room
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialAId,
            'room_id'    => $roomIdMap['Investor Daily Room'],
            'sort_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srA1Id = $db->insertID();

        // 14.1 Keynote Speech Special Session A.1
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srA1Id,
            'title'           => 'Keynote Speech Special Session A.1',
            'subtitle'        => "Anchoring Indonesia's Long-Term Development Ambitions",
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it141Id = $db->insertID();
        if (isset($speakerIdMap['anggito-abimanyu'])) {
            $db->table('session_item_speakers')->insert([
                'item_id'    => $it141Id,
                'speaker_id' => $speakerIdMap['anggito-abimanyu'],
                'sort_order' => 1,
            ]);
        }

        // 14.2 Special Session A.1
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srA1Id,
            'title'           => 'Special Session A.1',
            'subtitle'        => 'Rebalancing Growth For Stronger Indonesia',
            'sort_order'      => 2,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it142Id = $db->insertID();
        if (isset($moderatorIdMap['nasori-ahmad'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $it142Id,
                'moderator_id' => $moderatorIdMap['nasori-ahmad'],
            ]);
        }
        $spkA1 = ['ngurah-wirawan', 'suryopratomo', 'setyono-djuandi-darmono', 'edi-rivai', 'indrieffouny-indra'];
        $sOrder = 1;
        foreach ($spkA1 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $it142Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 14.3 & 14.4: Room 2: Sponsor Room (<<Sponsor>> Room)
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialAId,
            'room_id'    => $roomIdMap['Sponsor Room'],
            'sort_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srA2Id = $db->insertID();

        // 14.3 Keynote Speech Special Session A.2
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srA2Id,
            'title'           => 'Keynote Speech Special Session A.2',
            'subtitle'        => 'Beyond Welfare: Economic Mobility In A Market-Driven vs State-Driven World',
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it143Id = $db->insertID();
        if (isset($speakerIdMap['muhammad-qodari'])) {
            $db->table('session_item_speakers')->insert([
                'item_id'    => $it143Id,
                'speaker_id' => $speakerIdMap['muhammad-qodari'],
                'sort_order' => 1,
            ]);
        }

        // 14.4 Special Session A.2
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srA2Id,
            'title'           => 'Special Session A.2',
            'subtitle'        => 'Beyond Welfare: Economic Mobility In A Market-Driven vs State-Driven World',
            'sort_order'      => 2,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it144Id = $db->insertID();
        if (isset($moderatorIdMap['wahyu-setyowati'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $it144Id,
                'moderator_id' => $moderatorIdMap['wahyu-setyowati'],
            ]);
        }
        $spkA2 = ['rahmad-pribadi', 'andry-asmoro', 'shinta-w-kamdani', 'kamrussamad'];
        $sOrder = 1;
        foreach ($spkA2 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $it144Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 14.5: Room 3: Beritasatu Room
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialAId,
            'room_id'    => $roomIdMap['Beritasatu Room'],
            'sort_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srA3Id = $db->insertID();

        // 14.5 Special Session A.3
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srA3Id,
            'title'           => 'Special Session A.3',
            'subtitle'        => "Empowering Communities, Enabling Growth: Corporate Impact for Indonesia's Next Decade",
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it145Id = $db->insertID();
        if (isset($moderatorIdMap['andhika-aryo-p'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $it145Id,
                'moderator_id' => $moderatorIdMap['andhika-aryo-p'],
            ]);
        }
        $spkA3 = ['dharma-djojonegoro', 'rudy', 'irwan-hidayat', 'rudolf-tjandra', 'agung-laksamana'];
        $sOrder = 1;
        foreach ($spkA3 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $it145Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 15. Day 1: Special Remarks
        $db->table('schedule_sessions')->insert([
            'schedule_day_id' => $day1Id,
            'room_id'         => $roomIdMap['Investor Daily Plenary Room'],
            'start_time'      => '17:30:00',
            'end_time'        => '18:00:00',
            'title'           => 'Special Remarks',
            'subtitle'        => "Understanding President's Big Plans",
            'session_type'    => 'closing',
            'sort_order'      => $sort1++,
            'created_by'      => $adminId,
            'updated_by'      => $adminId,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $sess15Id = $db->insertID();
        if (isset($speakerIdMap['teddy-indra-wijaya'])) {
            $db->table('session_speakers')->insert([
                'session_id' => $sess15Id,
                'speaker_id' => $speakerIdMap['teddy-indra-wijaya'],
                'role'       => 'speaker',
                'sort_order' => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 7. Day 2 Sessions (Kamis, 8 Oktober 2026)
        $day2SessionsPart1 = [
            // 1.
            ['time_s' => '08:00:00', 'time_e' => '09:00:00', 'title' => 'Registration', 'subtitle' => 'Registrasi Tamu Undangan', 'type' => 'registration', 'room' => null, 'speakers' => []],
            // 2.
            ['time_s' => '09:00:00', 'time_e' => '09:30:00', 'title' => 'Opening Remarks', 'subtitle' => null, 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['airlangga-hartarto']],
            // 3.
            ['time_s' => '09:30:00', 'time_e' => '10:30:00', 'title' => 'Keynote Speech Special Session 1', 'subtitle' => "Building Indonesia's Next Economic Powerhouse", 'type' => 'keynote', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['dony-oskaria']],
            // 4.
            ['time_s' => '10:30:00', 'time_e' => '10:45:00', 'title' => 'Morning Coffee Break & Networking', 'subtitle' => null, 'type' => 'break', 'room' => null, 'speakers' => []],
        ];

        $sort2 = 1;
        foreach ($day2SessionsPart1 as $sess) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $day2Id,
                'room_id'         => $sess['room'],
                'start_time'      => $sess['time_s'],
                'end_time'        => $sess['time_e'],
                'title'           => $sess['title'],
                'subtitle'        => $sess['subtitle'],
                'session_type'    => $sess['type'],
                'sort_order'      => $sort2++,
                'created_by'      => $adminId,
                'updated_by'      => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $sessId = $db->insertID();

            $spkOrder = 1;
            foreach ($sess['speakers'] as $slug) {
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

        // 5. Day 2: Special Sessions B (10:45–12:00, multi-room di 3 ruangan)
        $db->table('schedule_sessions')->insert([
            'schedule_day_id'   => $day2Id,
            'parent_session_id' => null,
            'room_id'           => null,
            'start_time'        => '10:45:00',
            'end_time'          => '12:00:00',
            'title'             => 'Special Sessions B',
            'subtitle'          => null,
            'session_type'      => 'special_session',
            'sort_order'        => $sort2++,
            'created_by'        => $adminId,
            'updated_by'        => $adminId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        $specialBId = $db->insertID();

        // 5.1 Room 1: Investor Daily Room (Special Session B.1)
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialBId,
            'room_id'    => $roomIdMap['Investor Daily Room'],
            'sort_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srB1Id = $db->insertID();

        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srB1Id,
            'title'           => 'Special Session B.1',
            'subtitle'        => 'Connecting Indonesia, Accelerating Growth',
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $itB1Id = $db->insertID();
        if (isset($moderatorIdMap['wahyu-setyowati'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $itB1Id,
                'moderator_id' => $moderatorIdMap['wahyu-setyowati'],
            ]);
        }
        $spkB1 = ['dian-siswarini', 'achmad-muchtasyar', 'rivan-a-purwantono', 'bani-m-mulia'];
        $sOrder = 1;
        foreach ($spkB1 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $itB1Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 5.2 Room 2: Sponsor Room (Special Session B.2)
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialBId,
            'room_id'    => $roomIdMap['Sponsor Room'],
            'sort_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srB2Id = $db->insertID();

        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srB2Id,
            'title'           => 'Special Session B.2',
            'subtitle'        => 'The Golden Blueprint: Building Bullion Ecosystem for National Economic Resilience',
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $itB2Id = $db->insertID();
        if (isset($moderatorIdMap['iqbal-suwitamihardja'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $itB2Id,
                'moderator_id' => $moderatorIdMap['iqbal-suwitamihardja'],
            ]);
        }
        $spkB2 = ['anggoro-eko-cahyo', 'damar-latri-setiawan', 'nursalam', 'minarni-tanu'];
        $sOrder = 1;
        foreach ($spkB2 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $itB2Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 5.3 Room 3: Beritasatu Room (Special Session B.3)
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialBId,
            'room_id'    => $roomIdMap['Beritasatu Room'],
            'sort_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srB3Id = $db->insertID();

        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srB3Id,
            'title'           => 'Special Session B.3',
            'subtitle'        => 'Financial Ecosystem & Digital Transformation',
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $itB3Id = $db->insertID();
        if (isset($moderatorIdMap['syukri-rahmatullah'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $itB3Id,
                'moderator_id' => $moderatorIdMap['syukri-rahmatullah'],
            ]);
        }
        $spkB3 = ['filianingsih-hendarta', 'hexana-tri-sasongko', 'putrama-wahju-setyawan'];
        $sOrder = 1;
        foreach ($spkB3 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $itB3Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 6, 7, 8: Day 2 Midday sessions
        $day2SessionsPart2 = [
            // 6.
            ['time_s' => '12:00:00', 'time_e' => '13:30:00', 'title' => 'C-Suite Strategic Luncheon', 'subtitle' => 'Exclusive/Invitation Only', 'type' => 'break', 'room' => null, 'speakers' => []],
            // 7.
            ['time_s' => '13:30:00', 'time_e' => '13:55:00', 'title' => 'Keynote Speech Plenary Money Lab 1', 'subtitle' => 'The Next Market Horizon: Confidence Beyond Volatility', 'type' => 'keynote', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['hasan-fawzi']],
            // 8.
            ['time_s' => '13:55:00', 'time_e' => '14:10:00', 'title' => 'Split Into Three Breakout Rooms', 'subtitle' => null, 'type' => 'break', 'room' => null, 'speakers' => []],
        ];

        foreach ($day2SessionsPart2 as $sess) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $day2Id,
                'room_id'         => $sess['room'],
                'start_time'      => $sess['time_s'],
                'end_time'        => $sess['time_e'],
                'title'           => $sess['title'],
                'subtitle'        => $sess['subtitle'],
                'session_type'    => $sess['type'],
                'sort_order'      => $sort2++,
                'created_by'      => $adminId,
                'updated_by'      => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $sessId = $db->insertID();

            $spkOrder = 1;
            foreach ($sess['speakers'] as $slug) {
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

        // 9. Day 2: Special Sessions C (14:10–15:25, multi-room di 3 ruangan)
        $db->table('schedule_sessions')->insert([
            'schedule_day_id'   => $day2Id,
            'parent_session_id' => null,
            'room_id'           => null,
            'start_time'        => '14:10:00',
            'end_time'          => '15:25:00',
            'title'             => 'Special Sessions C',
            'subtitle'          => null,
            'session_type'      => 'special_session',
            'sort_order'        => $sort2++,
            'created_by'        => $adminId,
            'updated_by'        => $adminId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        $specialCId = $db->insertID();

        // 9.1, 9.2, 9.3: Room 1: Investor Daily Room
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialCId,
            'room_id'    => $roomIdMap['Investor Daily Room'],
            'sort_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srC1Id = $db->insertID();

        // 9.1 Special Remarks
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srC1Id,
            'title'           => 'Special Remarks',
            'subtitle'        => 'The Road Ahead: Strengthening Markets for Sustainable Growth',
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it91Id = $db->insertID();
        if (isset($speakerIdMap['iding-pardi'])) {
            $db->table('session_item_speakers')->insert([
                'item_id'    => $it91Id,
                'speaker_id' => $speakerIdMap['iding-pardi'],
                'sort_order' => 1,
            ]);
        }

        // 9.2 Special Session C.1
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srC1Id,
            'title'           => 'Special Session C.1',
            'subtitle'        => 'When Market Swings, Winners Think',
            'sort_order'      => 2,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it92Id = $db->insertID();
        if (isset($moderatorIdMap['wahyu-setyowati'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $it92Id,
                'moderator_id' => $moderatorIdMap['wahyu-setyowati'],
            ]);
        }
        $spkC1 = ['zabrina-raissa', 'andry-hakim', 'claudia-kolonas', 'anita-bernardus'];
        $sOrder = 1;
        foreach ($spkC1 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $it92Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 9.3 Fireside Chat 2
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srC1Id,
            'title'           => 'Fireside Chat 2',
            'subtitle'        => 'The Hype, The Dip, and The Opportunity',
            'sort_order'      => 3,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it93Id = $db->insertID();
        if (isset($speakerIdMap['gema-goeayrdi'])) {
            $db->table('session_item_speakers')->insert([
                'item_id'    => $it93Id,
                'speaker_id' => $speakerIdMap['gema-goeayrdi'],
                'sort_order' => 1,
            ]);
        }

        // 9.4 & 9.5: Room 2: Sponsor Room (<<Sponsor>> Room)
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialCId,
            'room_id'    => $roomIdMap['Sponsor Room'],
            'sort_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srC2Id = $db->insertID();

        // 9.4 Special Session C.2
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srC2Id,
            'title'           => 'Special Session C.2',
            'subtitle'        => 'Political Coordination as Economic Capital: Why Policy Clarity Matters',
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it94Id = $db->insertID();
        if (isset($moderatorIdMap['zaki-amrullah'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $it94Id,
                'moderator_id' => $moderatorIdMap['zaki-amrullah'],
            ]);
        }
        $spkC2 = ['burhanuddin-muhtadi', 'yunarto-wijaya', 'mohamad-ikhsan', 'iman-pambagyo'];
        $sOrder = 1;
        foreach ($spkC2 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $it94Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 9.5 Special Session C.3
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srC2Id,
            'title'           => 'Special Session C.3',
            'subtitle'        => 'Strategic Partnerships for More Resilient and Future-Ready Economy',
            'sort_order'      => 2,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $it95Id = $db->insertID();
        if (isset($moderatorIdMap['anthony-wonsono'])) {
            $db->table('session_item_moderators')->insert([
                'item_id'      => $it95Id,
                'moderator_id' => $moderatorIdMap['anthony-wonsono'],
            ]);
        }
        $spkC3 = ['wang-lutong', 'yoon-soon-gu', 'muzafar-shah-mustafa'];
        $sOrder = 1;
        foreach ($spkC3 as $slug) {
            if (isset($speakerIdMap[$slug])) {
                $db->table('session_item_speakers')->insert([
                    'item_id'    => $it95Id,
                    'speaker_id' => $speakerIdMap[$slug],
                    'sort_order' => $sOrder++,
                ]);
            }
        }

        // 9.6 & 9.7: Room 3: Beritasatu Room
        $db->table('schedule_session_rooms')->insert([
            'session_id' => $specialCId,
            'room_id'    => $roomIdMap['Beritasatu Room'],
            'sort_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $srC3Id = $db->insertID();

        // 9.6 MOU Sign With China Media Group
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srC3Id,
            'title'           => 'MOU Sign With China Media Group',
            'subtitle'        => null,
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        // 9.7 MOU Sign With Kuala Lumpur Tourism
        $db->table('schedule_session_items')->insert([
            'session_room_id' => $srC3Id,
            'title'           => 'MOU Sign With Kuala Lumpur Tourism',
            'subtitle'        => null,
            'sort_order'      => 2,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        // 10, 11, 12, 13: Day 2 Afternoon Sessions
        $day2SessionsPart3 = [
            // 10.
            ['time_s' => '15:25:00', 'time_e' => '15:40:00', 'title' => 'Afternoon Coffee Break & Networking', 'subtitle' => null, 'type' => 'break', 'room' => null, 'speakers' => []],
            // 11.
            ['time_s' => '15:40:00', 'time_e' => '16:40:00', 'title' => 'Plenary Session 2', 'subtitle' => 'AI: Restructuring Economic Landscape', 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'mod' => 'wiendy-hapsari', 'speakers' => ['nezar-patria', 'nugroho', 'marlo-budiman', 'ahmad-zulfikar-said']],
            // 12.
            ['time_s' => '16:40:00', 'time_e' => '17:40:00', 'title' => 'Plenary Session 3', 'subtitle' => 'Public Trust as Economic Capital: Law, Media, and the Legitimacy of State Power', 'type' => 'plenary', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'mod' => 'djaka-susila', 'speakers' => ['mohammad-mahfud-md', 'najwa-shihab']],
            // 13.
            ['time_s' => '17:40:00', 'time_e' => '18:40:00', 'title' => 'Closing Remarks', 'subtitle' => 'Rebuilding Trust Through Fiscal Policy', 'type' => 'closing', 'room' => $roomIdMap['Investor Daily Plenary Room'], 'speakers' => ['suahasil-nazara']],
        ];

        foreach ($day2SessionsPart3 as $sess) {
            $db->table('schedule_sessions')->insert([
                'schedule_day_id' => $day2Id,
                'room_id'         => $sess['room'],
                'start_time'      => $sess['time_s'],
                'end_time'        => $sess['time_e'],
                'title'           => $sess['title'],
                'subtitle'        => $sess['subtitle'],
                'session_type'    => $sess['type'],
                'sort_order'      => $sort2++,
                'created_by'      => $adminId,
                'updated_by'      => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $sessId = $db->insertID();

            if (!empty($sess['mod']) && isset($moderatorIdMap[$sess['mod']])) {
                $db->table('session_moderators')->insert([
                    'session_id'   => $sessId,
                    'moderator_id' => $moderatorIdMap[$sess['mod']],
                    'sort_order'   => 1,
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }

            $spkOrder = 1;
            foreach ($sess['speakers'] as $slug) {
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

        echo "MasterSeeder completed successfully!\n";
    }
}

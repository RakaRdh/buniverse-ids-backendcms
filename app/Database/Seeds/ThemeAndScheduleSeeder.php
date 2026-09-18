<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ThemeAndScheduleSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get Active Event ID
        $event = $db->table('events')->orderBy('id', 'ASC')->get()->getFirstRow('array');
        if (!$event) {
            echo "No events found.\n";
            return;
        }
        $eventId = (int)$event['id'];

        $admin = $db->table('admin_users')->orderBy('id', 'ASC')->get()->getFirstRow('array');
        $adminId = $admin ? (int)$admin['id'] : 1;

        // 2. CSS Theme Properties (58 total variables from style.css :root)
        $themeProperties = [
            // Neutral Palette (13)
            ['prop' => '--neutral-1', 'val' => '#ffffff', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 1 (Pure White)'],
            ['prop' => '--neutral-2', 'val' => '#fcfcfc', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 2'],
            ['prop' => '--neutral-3', 'val' => '#f5f5f5', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 3'],
            ['prop' => '--neutral-4', 'val' => '#f0f0f0', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 4'],
            ['prop' => '--neutral-5', 'val' => '#d9d9d9', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 5'],
            ['prop' => '--neutral-6', 'val' => '#bfbfbf', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 6'],
            ['prop' => '--neutral-7', 'val' => '#8c8c8c', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 7'],
            ['prop' => '--neutral-8', 'val' => '#595959', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 8'],
            ['prop' => '--neutral-9', 'val' => '#454545', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 9'],
            ['prop' => '--neutral-10', 'val' => '#262626', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 10'],
            ['prop' => '--neutral-11', 'val' => '#1f1f1f', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 11'],
            ['prop' => '--neutral-12', 'val' => '#141414', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 12'],
            ['prop' => '--neutral-13', 'val' => '#000000', 'type' => 'solid', 'cat' => 'neutral', 'label' => 'Neutral 13 (Pure Black)'],

            // Primary 1 (Deep Teal / Brand Dark) (10)
            ['prop' => '--primary-1-1', 'val' => '#e7eef0', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Tint 1'],
            ['prop' => '--primary-1-2', 'val' => '#c4d5dc', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Tint 2'],
            ['prop' => '--primary-1-3', 'val' => '#96b4c0', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Tint 3'],
            ['prop' => '--primary-1-4', 'val' => '#6591a2', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Tint 4'],
            ['prop' => '--primary-1-5', 'val' => '#377086', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Base Light'],
            ['prop' => '--primary-1-6', 'val' => '#0b516c', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Base Teal'],
            ['prop' => '--primary-1-7', 'val' => '#09455c', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Base Medium'],
            ['prop' => '--primary-1-8', 'val' => '#083a4d', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Base Dark'],
            ['prop' => '--primary-1-9', 'val' => '#062e3e', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Deep Page BG'],
            ['prop' => '--primary-1-10', 'val' => '#052431', 'type' => 'solid', 'cat' => 'primary-1', 'label' => 'Primary 1 - Deepest Alt BG'],

            // Primary 2 (Accent Blue) (10)
            ['prop' => '--primary-2-1', 'val' => '#ebf4fa', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Tint 1'],
            ['prop' => '--primary-2-2', 'val' => '#cfe5f3', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Tint 2'],
            ['prop' => '--primary-2-3', 'val' => '#a9d0ea', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Tint 3'],
            ['prop' => '--primary-2-4', 'val' => '#81bae0', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Tint 4'],
            ['prop' => '--primary-2-5', 'val' => '#5ba6d6', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Accent Medium'],
            ['prop' => '--primary-2-6', 'val' => '#3792cd', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Accent Blue'],
            ['prop' => '--primary-2-7', 'val' => '#2f7cae', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Shade 1'],
            ['prop' => '--primary-2-8', 'val' => '#276892', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Shade 2'],
            ['prop' => '--primary-2-9', 'val' => '#1f5375', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Shade 3'],
            ['prop' => '--primary-2-10', 'val' => '#19425c', 'type' => 'solid', 'cat' => 'primary-2', 'label' => 'Primary 2 - Deepest Shade'],

            // Semantic Theme Variables (16)
            ['prop' => '--bg-page', 'val' => '#062e3e', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Page Background'],
            ['prop' => '--bg-section-alt', 'val' => '#052431', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Section Alt Background'],
            ['prop' => '--bg-card', 'val' => '#083a4d', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Card Background'],
            ['prop' => '--bg-card-gradient', 'val' => 'linear-gradient(135deg, #09455c 0%, #062e3e 100%)', 'type' => 'gradient', 'cat' => 'semantic', 'label' => 'Card Gradient'],
            ['prop' => '--bg-pill', 'val' => '#083a4d', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Pill Background'],
            ['prop' => '--bg-pill-active', 'val' => '#ffffff', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Pill Active Background'],
            ['prop' => '--text-primary', 'val' => '#ffffff', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Primary Text Color'],
            ['prop' => '--text-secondary', 'val' => 'rgba(255, 255, 255, 0.82)', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Secondary Text Color'],
            ['prop' => '--text-muted', 'val' => '#bfbfbf', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Muted Text Color'],
            ['prop' => '--text-dark', 'val' => '#052431', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Dark Text Color'],
            ['prop' => '--accent-cyan', 'val' => '#0b516c', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Accent Cyan'],
            ['prop' => '--accent-light', 'val' => '#a9d0ea', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Accent Light'],
            ['prop' => '--accent-blue', 'val' => '#3792cd', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Accent Blue'],
            ['prop' => '--accent-glow', 'val' => 'rgba(91, 166, 214, 0.35)', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Accent Glow'],
            ['prop' => '--border-divider', 'val' => 'rgba(255, 255, 255, 0.12)', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Border Divider'],
            ['prop' => '--border-subtle', 'val' => 'rgba(255, 255, 255, 0.20)', 'type' => 'solid', 'cat' => 'semantic', 'label' => 'Border Subtle'],

            // Fonts (2)
            ['prop' => '--font-sans', 'val' => "'Montserrat', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif", 'type' => 'text', 'cat' => 'fonts', 'label' => 'Font Sans Serif'],
            ['prop' => '--font-serif', 'val' => "'Playfair Display', Georgia, Cambria, 'Times New Roman', Times, serif", 'type' => 'text', 'cat' => 'fonts', 'label' => 'Font Serif'],

            // Spacing & Layout (7)
            ['prop' => '--container-max-width', 'val' => '1440px', 'type' => 'dimension', 'cat' => 'spacing', 'label' => 'Container Max Width'],
            ['prop' => '--container-padding', 'val' => '80px', 'type' => 'dimension', 'cat' => 'spacing', 'label' => 'Container Padding Desktop'],
            ['prop' => '--radius-sm', 'val' => '6px', 'type' => 'dimension', 'cat' => 'spacing', 'label' => 'Radius Small'],
            ['prop' => '--radius-md', 'val' => '10px', 'type' => 'dimension', 'cat' => 'spacing', 'label' => 'Radius Medium'],
            ['prop' => '--radius-lg', 'val' => '16px', 'type' => 'dimension', 'cat' => 'spacing', 'label' => 'Radius Large'],
            ['prop' => '--radius-pill', 'val' => '9999px', 'type' => 'dimension', 'cat' => 'spacing', 'label' => 'Radius Pill'],
            ['prop' => '--transition-normal', 'val' => '0.25s cubic-bezier(0.4, 0, 0.2, 1)', 'type' => 'text', 'cat' => 'spacing', 'label' => 'Transition Normal'],
        ];

        // Insert / Update theme properties
        $sort = 1;
        foreach ($themeProperties as $p) {
            $existing = $db->table('event_theme_properties')
                ->where('event_id', $eventId)
                ->where('css_property', $p['prop'])
                ->get()->getFirstRow('array');

            if ($existing) {
                $db->table('event_theme_properties')->where('id', $existing['id'])->update([
                    'css_value'   => $p['val'],
                    'value_type'  => $p['type'],
                    'category'    => $p['cat'],
                    'label'       => $p['label'],
                    'sort_order'  => $sort++,
                    'updated_by'  => $adminId,
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            } else {
                $db->table('event_theme_properties')->insert([
                    'event_id'     => $eventId,
                    'css_property' => $p['prop'],
                    'css_value'    => $p['val'],
                    'value_type'   => $p['type'],
                    'category'     => $p['cat'],
                    'label'        => $p['label'],
                    'sort_order'   => $sort++,
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
        echo "Seeded " . count($themeProperties) . " theme properties for event $eventId.\n";

        // 3. Update Event Settings with defaults for new columns
        $navDefault = json_encode([
            ['label' => 'Home', 'url' => '#home'],
            ['label' => 'About Us', 'url' => '#about-us'],
            ['label' => 'Schedule', 'url' => '#schedule'],
            ['label' => 'Gallery', 'url' => '#gallery'],
            ['label' => 'News', 'url' => '#news'],
            ['label' => 'Sponsor', 'url' => '#sponsor'],
        ]);

        $settings = $db->table('event_settings')->where('event_id', $eventId)->get()->getFirstRow('array');
        if ($settings) {
            $updateData = [];
            if (empty($settings['cta_label'])) {
                $updateData['cta_label'] = 'Get Tickets';
            }
            if (empty($settings['copyright_text'])) {
                $updateData['copyright_text'] = 'Investor Daily Summit.';
            }
            if (empty($settings['nav_items_json'])) {
                $updateData['nav_items_json'] = $navDefault;
            }
            if (empty($settings['logo_footer_url'])) {
                $updateData['logo_footer_url'] = 'assets/img/logo_id.png';
            }
            if (!empty($updateData)) {
                $db->table('event_settings')->where('id', $settings['id'])->update($updateData);
                echo "Updated event_settings with default nav & branding.\n";
            }
        }

        // 4. Fix Schedule Sessions:
        // Rule a: session_type must only be 'plenary' or 'special_session'
        // Any session with type NOT IN ('plenary', 'special_session') is normalized to 'plenary'
        $db->table('schedule_sessions')
            ->whereNotIn('session_type', ['plenary', 'special_session'])
            ->update([
                'session_type' => 'plenary',
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        echo "Normalized session_type in schedule_sessions to plenary/special_session.\n";

        // Rule b: Title & Subtitle split:
        // For schedule_sessions where title contains "\n" or ":\n", split title and subtitle
        $sessions = $db->table('schedule_sessions')->get()->getResultArray();
        foreach ($sessions as $s) {
            $rawTitle = $s['title'];
            if (strpos($rawTitle, "\n") !== false) {
                $parts = explode("\n", $rawTitle, 2);
                $newTitle = trim($parts[0]);
                $newSubtitle = trim($parts[1]);

                $db->table('schedule_sessions')->where('id', $s['id'])->update([
                    'title'      => $newTitle,
                    'subtitle'   => $newSubtitle,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                echo "Split session ID {$s['id']}: Title='{$newTitle}', Subtitle='{$newSubtitle}'\n";
            }
        }

        // Also check schedule_session_items for "\n"
        $items = $db->table('schedule_session_items')->get()->getResultArray();
        foreach ($items as $it) {
            $rawTitle = $it['title'];
            if (strpos($rawTitle, "\n") !== false) {
                $parts = explode("\n", $rawTitle, 2);
                $newTitle = trim($parts[0]);
                $newSubtitle = trim($parts[1]);

                $db->table('schedule_session_items')->where('id', $it['id'])->update([
                    'title'      => $newTitle,
                    'subtitle'   => $newSubtitle,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                echo "Split item ID {$it['id']}: Title='{$newTitle}', Subtitle='{$newSubtitle}'\n";
            }
        }

        // Rule c: Verify Special Session has parent assigned or hierarchy intact
        $specialSessions = $db->table('schedule_sessions')
            ->where('session_type', 'special_session')
            ->get()->getResultArray();
        foreach ($specialSessions as $sp) {
            $roomCount = $db->table('schedule_session_rooms')->where('session_id', $sp['id'])->countAllResults();
            echo "Special Session ID {$sp['id']} '{$sp['title']}': has {$roomCount} breakout rooms.\n";
        }

        echo "ThemeAndScheduleSeeder completed successfully.\n";
    }
}

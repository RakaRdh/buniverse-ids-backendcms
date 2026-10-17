<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateYellowColors extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'theme:update-yellow';
    protected $description = 'Updates yellow colors in event_theme_properties';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $yellowMap = [
            '--yellow-1'  => '#fefbed',
            '--yellow-2'  => '#fcf6d3',
            '--yellow-3'  => '#faeeb1',
            '--yellow-4'  => '#f8e68c',
            '--yellow-5'  => '#f6df6a',
            '--yellow-6'  => '#f4d849',
            '--yellow-7'  => '#cfb83e',
            '--yellow-8'  => '#ad9934',
            '--yellow-9'  => '#8b7b2a',
            '--yellow-10' => '#6e6121',
        ];

        // Also primary-1-x if present
        $primary1Map = [
            '--primary-1-1'  => '#fefbed',
            '--primary-1-2'  => '#fcf6d3',
            '--primary-1-3'  => '#faeeb1',
            '--primary-1-4'  => '#f8e68c',
            '--primary-1-5'  => '#f6df6a',
            '--primary-1-6'  => '#f4d849',
            '--primary-1-7'  => '#cfb83e',
            '--primary-1-8'  => '#ad9934',
            '--primary-1-9'  => '#8b7b2a',
            '--primary-1-10' => '#6e6121',
        ];

        $allUpdates = array_merge($yellowMap, $primary1Map);

        $events = $db->table('events')->get()->getResultArray();
        CLI::write("Found " . count($events) . " events.", 'green');

        foreach ($events as $event) {
            CLI::write("Processing event {$event['id']} ({$event['slug']})...", 'yellow');
            foreach ($allUpdates as $prop => $val) {
                $existing = $db->table('event_theme_properties')
                    ->where('event_id', $event['id'])
                    ->where('css_property', $prop)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    $db->table('event_theme_properties')
                        ->where('id', $existing['id'])
                        ->update(['css_value' => $val, 'updated_at' => date('Y-m-d H:i:s')]);
                    CLI::write("  Updated {$prop} => {$val}", 'white');
                } else {
                    // Check if category is yellow or primary
                    $category = strpos($prop, 'yellow') !== false ? 'primary_1' : 'primary_1';
                    $label = ucwords(str_replace(['--', '-'], ['', ' '], $prop));
                    $db->table('event_theme_properties')->insert([
                        'event_id'     => $event['id'],
                        'css_property' => $prop,
                        'css_value'    => $val,
                        'value_type'   => 'solid',
                        'category'     => $category,
                        'label'        => $label,
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ]);
                    CLI::write("  Inserted {$prop} => {$val}", 'cyan');
                }
            }
        }

        // Also check if navbar or footer has bg colors pointing to old yellow #fff100
        $oldYellowUpdates = $db->table('event_theme_properties')
            ->where('css_value', '#fff100')
            ->get()
            ->getResultArray();
        foreach ($oldYellowUpdates as $item) {
            $db->table('event_theme_properties')
                ->where('id', $item['id'])
                ->update(['css_value' => '#f4d849', 'updated_at' => date('Y-m-d H:i:s')]);
            CLI::write("  Replaced #fff100 with #f4d849 on property {$item['css_property']} (event {$item['event_id']})", 'green');
        }

        // Clean cache in BackendCMS
        cache()->clean();
        CLI::write("BackendCMS cache cleaned successfully!", 'green');
    }
}

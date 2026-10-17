<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DuplicateEventSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $sourceEventId = 1;
        $sourceEvent = $db->table('events')->where('id', $sourceEventId)->get()->getRowArray();

        if (!$sourceEvent) {
            echo "Source event ID 1 not found.\n";
            return;
        }

        $newSlug = 'emf';
        $existing = $db->table('events')->where('slug', $newSlug)->get()->getRowArray();
        if ($existing) {
            echo "Event with slug '{$newSlug}' already exists (ID: {$existing['id']}).\n";
            $targetEventId = (int)$existing['id'];
        } else {
            $newEventData = $sourceEvent;
            unset($newEventData['id']);
            $newEventData['slug'] = $newSlug;
            $newEventData['title'] = 'Energy & Mining Forum 2026 (EMF)';
            $newEventData['theme'] = 'SUSTAINABLE ENERGY & RESILIENT MINING';
            $newEventData['news_tag_slug'] = 'emf2026';
            $newEventData['created_at'] = date('Y-m-d H:i:s');
            $newEventData['updated_at'] = date('Y-m-d H:i:s');

            $db->table('events')->insert($newEventData);
            $targetEventId = (int)$db->insertID();
            echo "Created new event EMF with ID: {$targetEventId}\n";
        }

        // Tables that have direct event_id column
        $eventTables = [
            'event_settings',
            'event_heroes',
            'event_overviews',
            'event_seo',
            'event_theme_properties',
            'speakers',
            'moderators',
            'gallery_images',
            'sponsor_tiers',
            'sponsors',
            'sponsor_partnerships',
            'section_decorations',
            'custom_placements',
            'event_locations',
            'news_portals',
            'schedule_rooms',
            'event_livestreams',
        ];

        foreach ($eventTables as $tbl) {
            if (!$db->tableExists($tbl)) continue;

            $hasTarget = $db->table($tbl)->where('event_id', $targetEventId)->countAllResults();
            if ($hasTarget > 0) {
                echo "Table {$tbl} already has data for event {$targetEventId}, skipping.\n";
                continue;
            }

            $sourceRows = $db->table($tbl)->where('event_id', $sourceEventId)->get()->getResultArray();
            if (empty($sourceRows)) continue;

            $insertedCount = 0;
            foreach ($sourceRows as $row) {
                unset($row['id']);
                $row['event_id'] = $targetEventId;
                if (isset($row['created_at'])) $row['created_at'] = date('Y-m-d H:i:s');
                if (isset($row['updated_at'])) $row['updated_at'] = date('Y-m-d H:i:s');

                $db->table($tbl)->insert($row);
                $insertedCount++;
            }
            echo "Copied {$insertedCount} rows from {$tbl} to event {$targetEventId}.\n";
        }

        // Schedule days & Sessions (relational clone)
        $hasDays = $db->table('schedule_days')->where('event_id', $targetEventId)->countAllResults();
        if ($hasDays === 0) {
            $sourceDays = $db->table('schedule_days')->where('event_id', $sourceEventId)->get()->getResultArray();
            foreach ($sourceDays as $sDay) {
                $oldDayId = $sDay['id'];
                unset($sDay['id']);
                $sDay['event_id'] = $targetEventId;
                $db->table('schedule_days')->insert($sDay);
                $newDayId = $db->insertID();

                // Copy sessions for this day
                $sourceSessions = $db->table('schedule_sessions')->where('schedule_day_id', $oldDayId)->get()->getResultArray();
                foreach ($sourceSessions as $sSess) {
                    unset($sSess['id']);
                    $sSess['schedule_day_id'] = $newDayId;
                    $db->table('schedule_sessions')->insert($sSess);
                }
            }
            echo "Copied schedule_days and schedule_sessions for event {$targetEventId}.\n";
        }

        echo "Seeding completed successfully for EMF!\n";
    }
}

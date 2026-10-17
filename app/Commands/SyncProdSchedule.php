<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SyncProdSchedule extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'schedule:sync-prod';
    protected $description = 'Sync schedule from investordailysummit.com to emf-2026';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // 1. Find event emf-2026 or fallback
        $targetSlug = $params[0] ?? 'emf-2026';
        $event = $db->table('events')->where('slug', $targetSlug)->get()->getRowArray();
        if (!$event) {
            $event = $db->table('events')->orderBy('id', 'DESC')->get()->getRowArray();
        }

        if (!$event) {
            CLI::error("No event found for slug: {$targetSlug}");
            return;
        }

        $eventId = (int)$event['id'];
        CLI::write("Syncing schedule into event ID {$eventId} ({$event['title']} - {$event['slug']})...", 'green');

        // 2. Fetch from investordailysummit.com
        $url = 'https://investordailysummit.com/backend/api/v1/event/investor-daily-summit-2026';
        CLI::write("Fetching from {$url}...", 'yellow');

        $client = \Config\Services::curlrequest(['timeout' => 15.0, 'verify' => false]);
        try {
            $resp = $client->get($url);
            $body = $resp->getBody();
            $data = json_decode($body, true);
        } catch (\Throwable $e) {
            CLI::error("Failed to fetch API: " . $e->getMessage());
            return;
        }

        if (empty($data['data']['schedule'])) {
            CLI::error("No schedule data found in API response.");
            return;
        }

        $prodSchedule = $data['data']['schedule'];

        // Determine days: could be $prodSchedule['days'] or $prodSchedule['day_1'], etc.
        $daysToProcess = [];
        if (!empty($prodSchedule['days']) && is_array($prodSchedule['days'])) {
            $daysToProcess = $prodSchedule['days'];
        } else {
            foreach (['day_1', 'day_2', 'day_3'] as $idx => $dKey) {
                if (!empty($prodSchedule[$dKey])) {
                    $dayItem = $prodSchedule[$dKey];
                    $dayItem['day_number'] = $dayItem['day_number'] ?? ($idx + 1);
                    $daysToProcess[] = $dayItem;
                }
            }
        }

        if (empty($daysToProcess)) {
            CLI::error("No days found in schedule data.");
            return;
        }

        $adminUser = $db->table('admin_users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = $adminUser ? (int)$adminUser['id'] : 1;

        // Clean existing schedule for this event
        $existingDays = $db->table('schedule_days')->where('event_id', $eventId)->get()->getResultArray();
        foreach ($existingDays as $ed) {
            $sessRows = $db->table('schedule_sessions')->where('schedule_day_id', $ed['id'])->get()->getResultArray();
            foreach ($sessRows as $sr) {
                // Delete session rooms, items, speakers, etc.
                $sRooms = $db->table('schedule_session_rooms')->where('session_id', $sr['id'])->get()->getResultArray();
                foreach ($sRooms as $srm) {
                    $sItems = $db->table('schedule_session_items')->where('session_room_id', $srm['id'])->get()->getResultArray();
                    foreach ($sItems as $sit) {
                        $db->table('session_item_speakers')->where('item_id', $sit['id'])->delete();
                        $db->table('session_item_moderators')->where('item_id', $sit['id'])->delete();
                    }
                    $db->table('schedule_session_items')->where('session_room_id', $srm['id'])->delete();
                }
                $db->table('schedule_session_rooms')->where('session_id', $sr['id'])->delete();
                $db->table('session_speakers')->where('session_id', $sr['id'])->delete();
                $db->table('session_moderators')->where('session_id', $sr['id'])->delete();
                $db->table('session_pengantar')->where('session_id', $sr['id'])->delete();
            }
            $db->table('schedule_sessions')->where('schedule_day_id', $ed['id'])->delete();
            $db->table('schedule_days')->where('id', $ed['id'])->delete();
        }

        CLI::write("Existing schedule data cleared for event {$eventId}.", 'yellow');

        // Helper to find or create Room
        $getRoomId = function($roomName) use ($db, $eventId, $adminId) {
            $roomName = trim($roomName);
            if (empty($roomName)) return null;
            $exist = $db->table('schedule_rooms')
                ->where('event_id', $eventId)
                ->where('name', $roomName)
                ->get()->getRowArray();
            if ($exist) return (int)$exist['id'];

            $db->table('schedule_rooms')->insert([
                'event_id'   => $eventId,
                'name'       => $roomName,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return (int)$db->insertID();
        };

        // Helper to find or create Speaker
        $getSpeakerId = function($spk) use ($db, $eventId, $adminId) {
            $name = trim($spk['name'] ?? '');
            if (empty($name)) return null;
            $slug = !empty($spk['slug']) ? $spk['slug'] : url_title($name, '-', true);

            $exist = $db->table('speakers')
                ->where('event_id', $eventId)
                ->groupStart()
                    ->where('slug', $slug)
                    ->orWhere('name', $name)
                ->groupEnd()
                ->get()->getRowArray();

            $photo = $spk['photo'] ?? 'assets/img/placeholder.png';
            $roleText = $spk['role'] ?? ($spk['role_text'] ?? '');

            if ($exist) {
                return (int)$exist['id'];
            }

            $db->table('speakers')->insert([
                'event_id'   => $eventId,
                'slug'       => $slug,
                'name'       => $name,
                'role_text'  => $roleText,
                'photo_url'  => $photo,
                'sort_order' => 0,
                'is_active'  => 1,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return (int)$db->insertID();
        };

        // Helper to find or create Moderator
        $getModeratorId = function($mod) use ($db, $eventId, $adminId) {
            $name = trim($mod['name'] ?? '');
            if (empty($name)) return null;

            $exist = $db->table('moderators')
                ->where('event_id', $eventId)
                ->where('name', $name)
                ->get()->getRowArray();

            $photo = $mod['photo'] ?? 'assets/img/placeholder.png';
            $roleText = $mod['role'] ?? ($mod['role_text'] ?? '');

            if ($exist) {
                return (int)$exist['id'];
            }

            $db->table('moderators')->insert([
                'event_id'   => $eventId,
                'name'       => $name,
                'role_text'  => $roleText,
                'photo_url'  => $photo,
                'sort_order' => 0,
                'is_active'  => 1,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return (int)$db->insertID();
        };

        // Helper to find or create Pengantar
        $getPengantarId = function($png) use ($db, $eventId, $adminId) {
            $name = trim($png['name'] ?? '');
            if (empty($name)) return null;
            $slug = !empty($png['slug']) ? $png['slug'] : url_title($name, '-', true);

            $exist = $db->table('pengantar')
                ->where('event_id', $eventId)
                ->groupStart()
                    ->where('slug', $slug)
                    ->orWhere('name', $name)
                ->groupEnd()
                ->get()->getRowArray();

            $photo = $png['photo'] ?? 'assets/img/placeholder.png';
            $roleText = $png['role'] ?? ($png['role_text'] ?? '');

            if ($exist) {
                return (int)$exist['id'];
            }

            $db->table('pengantar')->insert([
                'event_id'   => $eventId,
                'slug'       => $slug,
                'name'       => $name,
                'role_text'  => $roleText,
                'photo_url'  => $photo,
                'sort_order' => 0,
                'is_active'  => 1,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return (int)$db->insertID();
        };

        // Loop and insert Days & Sessions
        foreach ($daysToProcess as $dIdx => $dayData) {
            $dayNum = (int)($dayData['day_number'] ?? ($dIdx + 1));
            $label = $dayData['label'] ?? "Day {$dayNum}";
            $dateStr = $dayData['date'] ?? null;
            $eventDate = $dateStr ? date('Y-m-d', strtotime($dateStr)) : date('Y-m-d');

            $db->table('schedule_days')->insert([
                'event_id'   => $eventId,
                'day_number' => $dayNum,
                'label'      => $label,
                'event_date' => $eventDate,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $dayId = (int)$db->insertID();
            CLI::write("Created Day ID {$dayId}: {$label} ({$eventDate})", 'green');

            $sessions = $dayData['sessions'] ?? [];
            foreach ($sessions as $sIdx => $sess) {
                $startTime = !empty($sess['start_time']) ? (strlen($sess['start_time']) === 5 ? $sess['start_time'] . ':00' : $sess['start_time']) : '09:00:00';
                $endTime = !empty($sess['end_time']) ? (strlen($sess['end_time']) === 5 ? $sess['end_time'] . ':00' : $sess['end_time']) : '10:00:00';
                $title = $sess['title'] ?? 'Session';
                $subtitle = $sess['subtitle'] ?? null;
                $sessionType = $sess['session_type'] ?? 'plenary';
                if (!in_array($sessionType, ['registration', 'plenary', 'keynote', 'special_session', 'break', 'closing'])) {
                    $sessionType = 'plenary';
                }

                $roomId = null;
                if (!empty($sess['room'])) {
                    $roomId = $getRoomId($sess['room']);
                }

                $db->table('schedule_sessions')->insert([
                    'schedule_day_id'   => $dayId,
                    'parent_session_id' => null,
                    'room_id'           => $roomId,
                    'start_time'        => $startTime,
                    'end_time'          => $endTime,
                    'title'             => $title,
                    'subtitle'          => $subtitle,
                    'session_type'      => $sessionType,
                    'sort_order'        => $sIdx + 1,
                    'created_by'        => $adminId,
                    'updated_by'        => $adminId,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);
                $sessionId = (int)$db->insertID();

                // Moderator
                if (!empty($sess['moderator'])) {
                    $modId = $getModeratorId($sess['moderator']);
                    if ($modId) {
                        $db->table('session_moderators')->insert([
                            'session_id'   => $sessionId,
                            'moderator_id' => $modId,
                            'sort_order'   => 1,
                            'created_by'   => $adminId,
                            'created_at'   => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                // Pengantar
                if (!empty($sess['pengantar'])) {
                    foreach ($sess['pengantar'] as $pIdx => $png) {
                        $pngId = $getPengantarId($png);
                        if ($pngId) {
                            $db->table('session_pengantar')->insert([
                                'session_id'   => $sessionId,
                                'pengantar_id' => $pngId,
                                'sort_order'   => $pIdx + 1,
                                'created_at'   => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }

                // Speakers
                if (!empty($sess['speakers'])) {
                    foreach ($sess['speakers'] as $spIdx => $spk) {
                        $speakerId = $getSpeakerId($spk);
                        if ($speakerId) {
                            $db->table('session_speakers')->insert([
                                'session_id' => $sessionId,
                                'speaker_id' => $speakerId,
                                'role'       => 'speaker',
                                'sort_order' => $spIdx + 1,
                                'created_by' => $adminId,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }

                // Multi room sessions
                if (!empty($sess['is_multi_room']) && !empty($sess['rooms'])) {
                    foreach ($sess['rooms'] as $rIdx => $rm) {
                        $subRoomName = $rm['room'] ?? "Room " . ($rIdx + 1);
                        $subRoomId = $getRoomId($subRoomName);
                        if (!$subRoomId) continue;

                        $db->table('schedule_session_rooms')->insert([
                            'session_id' => $sessionId,
                            'room_id'    => $subRoomId,
                            'sort_order' => $rIdx + 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $sessionRoomId = (int)$db->insertID();

                        if (!empty($rm['sessions'])) {
                            foreach ($rm['sessions'] as $itIdx => $subSess) {
                                $db->table('schedule_session_items')->insert([
                                    'session_room_id' => $sessionRoomId,
                                    'title'           => $subSess['title'] ?? 'Room Session',
                                    'subtitle'        => $subSess['subtitle'] ?? null,
                                    'sort_order'      => $itIdx + 1,
                                    'created_at'      => date('Y-m-d H:i:s'),
                                    'updated_at'      => date('Y-m-d H:i:s'),
                                ]);
                                $itemId = (int)$db->insertID();

                                if (!empty($subSess['moderator'])) {
                                    $subModId = $getModeratorId($subSess['moderator']);
                                    if ($subModId) {
                                        $db->table('session_item_moderators')->insert([
                                            'item_id'      => $itemId,
                                            'moderator_id' => $subModId,
                                        ]);
                                    }
                                }

                                if (!empty($subSess['speakers'])) {
                                    foreach ($subSess['speakers'] as $sItSpIdx => $subSpk) {
                                        $subSpkId = $getSpeakerId($subSpk);
                                        if ($subSpkId) {
                                            $db->table('session_item_speakers')->insert([
                                                'item_id'    => $itemId,
                                                'speaker_id' => $subSpkId,
                                                'sort_order' => $sItSpIdx + 1,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        CLI::write("Schedule successfully synced and populated into database for event ID {$eventId}!", 'green');
    }
}

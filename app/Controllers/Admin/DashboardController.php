<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\GalleryImageModel;
use App\Models\ModeratorModel;
use App\Models\ScheduleSessionModel;
use App\Models\SpeakerModel;
use App\Models\SponsorModel;
use App\Models\TicketClickLogModel;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $eventId = $this->activeEvent['id'] ?? null;

        $stats = [
            'total_speakers'   => 0,
            'total_moderators' => 0,
            'total_sessions'   => 0,
            'total_sponsors'   => 0,
            'total_gallery'    => 0,
            'ticket_clicks'    => 0,
        ];

        $dailyClicks = [];
        $recentSessions = [];

        if ($eventId) {
            $stats['total_speakers']   = $db->table('speakers')->where('event_id', $eventId)->countAllResults();
            $stats['total_moderators'] = $db->table('moderators')->where('event_id', $eventId)->countAllResults();
            $stats['total_gallery']    = $db->table('gallery_images')->where('event_id', $eventId)->countAllResults();
            $stats['ticket_clicks']    = $db->table('ticket_click_logs')->where('event_id', $eventId)->countAllResults();

            // Count sessions linked through schedule_day_id
            $stats['total_sessions'] = $db->table('schedule_sessions')
                ->join('schedule_days', 'schedule_days.id = schedule_sessions.schedule_day_id')
                ->where('schedule_days.event_id', $eventId)
                ->countAllResults();

            // Count sponsors linked through sponsor_tiers
            $stats['total_sponsors'] = $db->table('sponsors')
                ->join('sponsor_tiers', 'sponsor_tiers.id = sponsors.tier_id')
                ->where('sponsor_tiers.event_id', $eventId)
                ->countAllResults();

            // Daily ticket click trend from earliest click (or 14 days ago) up to event end date
            $clickRows = $db->table('ticket_click_logs')
                ->select("DATE(clicked_at) as click_date, COUNT(*) as count")
                ->where('event_id', $eventId)
                ->groupBy("DATE(clicked_at)")
                ->orderBy("click_date", "ASC")
                ->get()
                ->getResultArray();

            $clicksByDate = [];
            foreach ($clickRows as $cr) {
                $clicksByDate[$cr['click_date']] = (int)$cr['count'];
            }

            // Determine date range: from earliest click or 14 days ago up to event date
            $eventStart = !empty($this->activeEvent['event_date_start']) ? $this->activeEvent['event_date_start'] : date('Y-m-d', strtotime('+30 days'));
            $eventEnd   = !empty($this->activeEvent['event_date_end']) ? $this->activeEvent['event_date_end'] : $eventStart;
            
            $startDateStr = !empty($clickRows) ? $clickRows[0]['click_date'] : date('Y-m-d', strtotime('-14 days'));
            // If startDate is after today, clamp to 14 days ago
            if ($startDateStr > date('Y-m-d')) {
                $startDateStr = date('Y-m-d', strtotime('-14 days'));
            }

            $current = strtotime($startDateStr);
            $eventEndTime = strtotime($eventEnd);
            // Cap chart range to event end date, with max 60 intervals
            $targetEnd = min($eventEndTime, strtotime('+45 days'));
            if ($targetEnd < strtotime(date('Y-m-d'))) {
                $targetEnd = strtotime(date('Y-m-d'));
            }

            $chartCategories = [];
            $chartSeriesData = [];
            $cumulativeSeriesData = [];
            $cumCount = 0;

            while ($current <= $targetEnd) {
                $dStr = date('Y-m-d', $current);
                $dLabel = date('d M', $current);
                $cnt = $clicksByDate[$dStr] ?? 0;
                $cumCount += $cnt;

                $chartCategories[] = $dLabel;
                $chartSeriesData[] = $cnt;
                $cumulativeSeriesData[] = $cumCount;

                $current = strtotime('+1 day', $current);
                if (count($chartCategories) > 60) break; // safety cap
            }

            $chartPayload = [
                'categories' => $chartCategories,
                'series'     => [
                    [
                        'name' => 'Daily Ticket Clicks',
                        'data' => $chartSeriesData,
                    ],
                    [
                        'name' => 'Cumulative Clicks',
                        'data' => $cumulativeSeriesData,
                    ]
                ]
            ];

            // Upcoming sessions
            $recentSessions = $db->table('schedule_sessions')
                ->select('schedule_sessions.*, schedule_days.label as day_label, schedule_rooms.name as room_name')
                ->join('schedule_days', 'schedule_days.id = schedule_sessions.schedule_day_id')
                ->join('schedule_rooms', 'schedule_rooms.id = schedule_sessions.room_id', 'left')
                ->where('schedule_days.event_id', $eventId)
                ->where('schedule_sessions.parent_session_id', null)
                ->orderBy('schedule_sessions.start_time', 'ASC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        $this->data['stats']          = $stats;
        $this->data['chartPayload']   = $chartPayload ?? ['categories' => [], 'series' => []];
        $this->data['recentSessions'] = $recentSessions;
        $this->data['pageTitle']      = 'Executive Dashboard';
        $this->data['activeMenu']     = 'dashboard';

        return $this->render('admin/dashboard/index', $this->data);
    }
}

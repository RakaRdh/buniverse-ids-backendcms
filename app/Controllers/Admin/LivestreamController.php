<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventLivestreamModel;

class LivestreamController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $streamModel = new EventLivestreamModel();
        $streamRows  = $streamModel->where('event_id', $eventId)->orderBy('id', 'ASC')->findAll();

        $streams = [];
        if (!empty($streamRows)) {
            foreach ($streamRows as $row) {
                $streams[] = [
                    'id'          => $row['id'],
                    'title'       => $row['title'] ?? '',
                    'youtube_url' => $row['youtube_url'] ?? '',
                    'is_active'   => !empty($row['is_active']),
                ];
            }
        }

        // Fallback for single stream compatibility
        $stream = !empty($streams) ? $streams[0] : null;

        $this->data['stream']     = $stream;
        $this->data['streams']    = $streams;
        $this->data['pageTitle']  = 'Live Streaming Settings';
        $this->data['activeMenu'] = 'livestreams';

        return $this->render('admin/livestreams/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $streamModel = new EventLivestreamModel();
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $isActive = $this->request->getPost('is_active') ? 1 : 0;
        $postedStreams = $this->request->getPost('streams');

        // Fetch existing streams for this event
        $existingRows = $streamModel->where('event_id', $eventId)->orderBy('id', 'ASC')->findAll();

        if (is_array($postedStreams)) {
            // Normalize up to 3 entries
            $validEntries = [];
            foreach ($postedStreams as $item) {
                $rawUrl = trim($item['youtube_url'] ?? '');
                $rawTitle = trim($item['title'] ?? '');
                if ($rawUrl !== '') {
                    $validEntries[] = [
                        'title'       => $rawTitle !== '' ? $rawTitle : 'Live Stream',
                        'youtube_url' => $rawUrl,
                    ];
                }
            }

            // Sync with existing rows in event_livestreams table
            $maxCount = max(count($existingRows), count($validEntries));
            for ($i = 0; $i < $maxCount; $i++) {
                $existing = $existingRows[$i] ?? null;
                $entry    = $validEntries[$i] ?? null;

                if ($entry && $existing) {
                    // Update existing row
                    $streamModel->update($existing['id'], [
                        'title'       => $entry['title'],
                        'youtube_url' => $entry['youtube_url'],
                        'is_active'   => $isActive,
                        'updated_by'  => $userId,
                    ]);
                } elseif ($entry && !$existing) {
                    // Insert new stream row
                    $streamModel->insert([
                        'event_id'    => $eventId,
                        'title'       => $entry['title'],
                        'youtube_url' => $entry['youtube_url'],
                        'is_active'   => $isActive,
                        'created_by'  => $userId,
                        'updated_by'  => $userId,
                    ]);
                } elseif (!$entry && $existing) {
                    // Delete excess row if stream slot was cleared
                    $streamModel->delete($existing['id']);
                }
            }
        } else {
            // Legacy fallback if single stream submitted
            $title      = $this->request->getPost('title') ?: 'Investor Daily Summit 2026 Live Stream';
            $youtubeUrl = $this->request->getPost('youtube_url') ?: '';

            $existing = $existingRows[0] ?? null;
            $data = [
                'event_id'    => $eventId,
                'title'       => $title,
                'youtube_url' => $youtubeUrl,
                'is_active'   => $isActive,
                'updated_by'  => $userId,
            ];

            if ($existing) {
                $streamModel->update($existing['id'], $data);
            } elseif ($youtubeUrl !== '') {
                $data['created_by'] = $userId;
                $streamModel->insert($data);
            }
        }

        return redirect()->to(base_url('admin/livestreams'))->with('success', 'Live streaming settings updated successfully.');
    }
}

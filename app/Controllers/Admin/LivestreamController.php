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
        $stream = $streamModel->getActiveByEvent($eventId) ?? $streamModel->where('event_id', $eventId)->first();

        $this->data['stream']     = $stream;
        $this->data['pageTitle']  = 'Live Streaming Settings';
        $this->data['activeMenu'] = 'livestream';

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

        $title      = $this->request->getPost('title') ?: 'Investor Daily Summit 2026 Live Stream';
        $youtubeUrl = $this->request->getPost('youtube_url') ?: '';
        $isActive   = $this->request->getPost('is_active') ? 1 : 0;

        $existing = $streamModel->where('event_id', $eventId)->first();

        $data = [
            'event_id'    => $eventId,
            'title'       => $title,
            'youtube_url' => $youtubeUrl,
            'is_active'   => $isActive,
            'updated_by'  => $userId,
        ];

        if ($existing) {
            $streamModel->update($existing['id'], $data);
        } else {
            $data['created_by'] = $userId;
            $streamModel->insert($data);
        }

        return redirect()->to(base_url('admin/livestreams'))->with('success', 'Live streaming settings updated successfully.');
    }
}

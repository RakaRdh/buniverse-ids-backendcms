<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventHighlightModel;

class HighlightController extends BaseAdminController
{
    private function extractVideoId(string $url): string
    {
        $url = trim($url);
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m)) {
            return $m[1];
        }
        if (strlen($url) === 11 && !str_contains($url, '/')) {
            return $url;
        }
        return '';
    }

    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $highlightModel = new EventHighlightModel();
        $highlights = $highlightModel->getByEventId($eventId, false);

        $this->data['highlights'] = $highlights;
        $this->data['pageTitle']  = 'Event Highlights Studio';
        $this->data['activeMenu'] = 'highlights';

        return $this->render('admin/highlights/index', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $title      = trim($this->request->getPost('title') ?? '');
        $youtubeUrl = trim($this->request->getPost('youtube_url') ?? '');
        $sortOrder  = (int)($this->request->getPost('sort_order') ?? 0);
        $isActive   = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($title) || empty($youtubeUrl)) {
            return redirect()->back()->with('error', 'Title and YouTube URL are required.')->withInput();
        }

        $videoId = $this->extractVideoId($youtubeUrl);
        if (empty($videoId)) {
            return redirect()->back()->with('error', 'Invalid YouTube URL or Video ID.')->withInput();
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $highlightModel = new EventHighlightModel();
        $highlightModel->insert([
            'event_id'    => $eventId,
            'title'       => $title,
            'youtube_url' => $youtubeUrl,
            'video_id'    => $videoId,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive,
            'created_by'  => $userId,
            'updated_by'  => $userId,
        ]);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Highlight video added successfully.');
    }

    public function update(int $id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $highlightModel = new EventHighlightModel();
        $existing = $highlightModel->where('event_id', $eventId)->find($id);
        if (!$existing) {
            return redirect()->back()->with('error', 'Highlight video not found.');
        }

        $title      = trim($this->request->getPost('title') ?? '');
        $youtubeUrl = trim($this->request->getPost('youtube_url') ?? '');
        $sortOrder  = (int)($this->request->getPost('sort_order') ?? 0);
        $isActive   = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($title) || empty($youtubeUrl)) {
            return redirect()->back()->with('error', 'Title and YouTube URL are required.')->withInput();
        }

        $videoId = $this->extractVideoId($youtubeUrl);
        if (empty($videoId)) {
            return redirect()->back()->with('error', 'Invalid YouTube URL or Video ID.')->withInput();
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $highlightModel->update($id, [
            'title'       => $title,
            'youtube_url' => $youtubeUrl,
            'video_id'    => $videoId,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive,
            'updated_by'  => $userId,
        ]);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Highlight video updated successfully.');
    }

    public function toggle(int $id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $highlightModel = new EventHighlightModel();
        $existing = $highlightModel->where('event_id', $eventId)->find($id);
        if (!$existing) {
            return redirect()->back()->with('error', 'Highlight video not found.');
        }

        $newStatus = empty($existing['is_active']) ? 1 : 0;
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $highlightModel->update($id, [
            'is_active'  => $newStatus,
            'updated_by' => $userId,
        ]);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Highlight status updated.');
    }

    public function delete(int $id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $highlightModel = new EventHighlightModel();
        $existing = $highlightModel->where('event_id', $eventId)->find($id);
        if (!$existing) {
            return redirect()->back()->with('error', 'Highlight video not found.');
        }

        $highlightModel->delete($id);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Highlight video deleted successfully.');
    }
}

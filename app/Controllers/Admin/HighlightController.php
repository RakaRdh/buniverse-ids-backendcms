<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventHighlightModel;

class HighlightController extends BaseAdminController
{
    private function detectPlatformAndId(string $url): array
    {
        $url = trim($url);

        // 1. TikTok
        if (preg_match('%tiktok\.com%i', $url)) {
            $id = '';
            if (preg_match('%/video/(\d+)%i', $url, $m)) {
                $id = $m[1];
            } elseif (preg_match('%/v/(\d+)%i', $url, $m)) {
                $id = $m[1];
            } else {
                $id = substr(md5($url), 0, 16);
            }
            return ['platform' => 'tiktok', 'id' => substr($id, 0, 32)];
        }

        // 2. Instagram
        if (preg_match('%(?:instagram\.com|instagr\.am)/(?:p|reel|reels)/([A-Za-z0-9_-]+)%i', $url, $m)) {
            return ['platform' => 'instagram', 'id' => substr($m[1], 0, 32)];
        } elseif (preg_match('%instagram\.com%i', $url)) {
            return ['platform' => 'instagram', 'id' => substr(md5($url), 0, 16)];
        }

        // 3. YouTube
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|live|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m)) {
            return ['platform' => 'youtube', 'id' => $m[1]];
        }
        if (strlen($url) === 11 && !str_contains($url, '/')) {
            return ['platform' => 'youtube', 'id' => $url];
        }

        // Default fallback
        return ['platform' => 'youtube', 'id' => substr(md5($url), 0, 16)];
    }

    private function curlDownload(string $url, array $headers = []): ?string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 200 && !empty($res)) ? $res : null;
    }

    private function fetchAndStoreThumbnail(int $highlightId, string $url, $uploadedFile = null): bool
    {
        $targetDir = FCPATH . 'uploads/highlights/';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }
        $targetFile = $targetDir . $highlightId . '.jpg';

        // 1. If user uploaded a manual thumbnail image
        if ($uploadedFile && $uploadedFile->isValid() && !$uploadedFile->hasMoved()) {
            try {
                $uploadedFile->move($targetDir, $highlightId . '.jpg', true);
                return true;
            } catch (\Throwable $e) {
                log_message('warning', 'Failed to upload highlight thumbnail: ' . $e->getMessage());
            }
        }

        // 2. Auto-fetch for TikTok
        if (str_contains($url, 'tiktok.com')) {
            try {
                // Strip query parameters for clean oembed request
                $cleanUrl = preg_replace('/\?.*/', '', trim($url));
                $oembedUrl = 'https://www.tiktok.com/oembed?url=' . urlencode($cleanUrl);
                $json = $this->curlDownload($oembedUrl);
                if ($json) {
                    $data = json_decode($json, true);
                    if (!empty($data['thumbnail_url'])) {
                        $imgData = $this->curlDownload($data['thumbnail_url']);
                        if ($imgData) {
                            file_put_contents($targetFile, $imgData);
                            return true;
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('warning', 'TikTok oEmbed fetch failed: ' . $e->getMessage());
            }
        }

        // 3. Auto-fetch for Instagram
        if (str_contains($url, 'instagram.com') || str_contains($url, 'instagr.am')) {
            try {
                if (preg_match('%(?:instagram\.com|instagr\.am)/(?:p|reel|reels)/([A-Za-z0-9_-]+)%i', $url, $m)) {
                    $shortcode = $m[1];
                    $igOembedUrl = "https://www.instagram.com/api/v1/oembed/?url=" . urlencode("https://www.instagram.com/p/{$shortcode}/");
                    $json = $this->curlDownload($igOembedUrl, ['User-Agent: Instagram 219.0.0.12.117 Android']);
                    if ($json) {
                        $data = json_decode($json, true);
                        if (!empty($data['thumbnail_url'])) {
                            $imgData = $this->curlDownload($data['thumbnail_url']);
                            if ($imgData) {
                                file_put_contents($targetFile, $imgData);
                                return true;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Instagram thumbnail oembed failed: ' . $e->getMessage());
            }
        }

        return false;
    }

    private function fetchTitleAndThumb(string $url, array $parsed): array
    {
        $platform = $parsed['platform'] ?? 'youtube';
        $videoId = $parsed['id'] ?? '';
        $title = '';
        $thumbUrl = '';

        if ($platform === 'youtube' && !empty($videoId)) {
            $oembed = "https://www.youtube.com/oembed?url=" . urlencode("https://www.youtube.com/watch?v={$videoId}") . "&format=json";
            $json = $this->curlDownload($oembed);
            if ($json) {
                $d = json_decode($json, true);
                $title = $d['title'] ?? '';
            }
            $thumbUrl = "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg";
        } elseif ($platform === 'tiktok') {
            $cleanUrl = preg_replace('/\?.*/', '', $url);
            $oembed = "https://www.tiktok.com/oembed?url=" . urlencode($cleanUrl);
            $json = $this->curlDownload($oembed);
            if ($json) {
                $d = json_decode($json, true);
                $title = $d['title'] ?? '';
                $thumbUrl = $d['thumbnail_url'] ?? '';
            }
        } elseif ($platform === 'instagram') {
            $shortcode = $videoId;
            if (preg_match('%(?:instagram\.com|instagr\.am)/(?:p|reel|reels)/([A-Za-z0-9_-]+)%i', $url, $m)) {
                $shortcode = $m[1];
            }
            if (!empty($shortcode)) {
                $oembed = "https://www.instagram.com/api/v1/oembed/?url=" . urlencode("https://www.instagram.com/p/{$shortcode}/");
                $json = $this->curlDownload($oembed, ['User-Agent: Instagram 219.0.0.12.117 Android']);
                if ($json) {
                    $d = json_decode($json, true);
                    $title = $d['title'] ?? '';
                    $thumbUrl = $d['thumbnail_url'] ?? '';
                }
            }
        }

        return [
            'platform'      => $platform,
            'title'         => $title,
            'thumbnail_url' => $thumbUrl,
        ];
    }

    public function fetchMetadata()
    {
        $url = trim($this->request->getPost('url') ?? '');
        if (empty($url)) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'URL is required.',
                'csrf_name' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $parsed = $this->detectPlatformAndId($url);
        $meta = $this->fetchTitleAndThumb($url, $parsed);

        return $this->response->setJSON([
            'status'        => 'success',
            'platform'      => $meta['platform'],
            'title'         => $meta['title'],
            'thumbnail_url' => $meta['thumbnail_url'],
            'csrf_name'     => csrf_token(),
            'csrf_hash'     => csrf_hash(),
        ]);
    }

    public function reorder()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $order = $this->request->getPost('order');
        if (is_array($order) && !empty($order)) {
            $highlightModel = new EventHighlightModel();
            foreach ($order as $sort => $id) {
                if ($eventId) {
                    $highlightModel->where('event_id', $eventId)->update((int)$id, ['sort_order' => (int)$sort + 1]);
                } else {
                    $highlightModel->update((int)$id, ['sort_order' => (int)$sort + 1]);
                }
            }
        }
        return $this->response->setJSON([
            'status'    => 'success',
            'csrf_name' => csrf_token(),
            'csrf_hash' => csrf_hash(),
            'message'   => 'Urutan berhasil disimpan.'
        ]);
    }

    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $highlightModel = new EventHighlightModel();
        $highlights = $highlightModel->getByEventId($eventId, false);

        $youtubeList = [];
        $tiktokList = [];
        $instagramList = [];

        // Attach platform and resolved thumbnail URL for CMS list
        foreach ($highlights as &$hl) {
            $parsed = $this->detectPlatformAndId($hl['youtube_url'] ?? '');
            $hl['platform'] = $parsed['platform'];

            $localThumbPath = FCPATH . 'uploads/highlights/' . $hl['id'] . '.jpg';
            if (file_exists($localThumbPath)) {
                $hl['display_thumb'] = base_url('uploads/highlights/' . $hl['id'] . '.jpg');
            } elseif ($hl['platform'] === 'youtube') {
                $hl['display_thumb'] = "https://i.ytimg.com/vi/{$hl['video_id']}/hqdefault.jpg";
            } else {
                $hl['display_thumb'] = base_url('assets/img/image1.png');
            }

            if ($hl['platform'] === 'tiktok') {
                $tiktokList[] = $hl;
            } elseif ($hl['platform'] === 'instagram') {
                $instagramList[] = $hl;
            } else {
                $youtubeList[] = $hl;
            }
        }
        unset($hl);

        $editId = $this->request->getGet('edit_id');
        $editHighlight = null;
        if ($editId) {
            $editHighlight = $highlightModel->where('event_id', $eventId)->find((int)$editId);
            if ($editHighlight) {
                $parsed = $this->detectPlatformAndId($editHighlight['youtube_url'] ?? '');
                $editHighlight['platform'] = $parsed['platform'];
                $localThumbPath = FCPATH . 'uploads/highlights/' . $editHighlight['id'] . '.jpg';
                $editHighlight['has_custom_thumb'] = file_exists($localThumbPath);
                $editHighlight['custom_thumb_url'] = $editHighlight['has_custom_thumb'] ? base_url('uploads/highlights/' . $editHighlight['id'] . '.jpg') : '';
            }
        }

        $this->data['highlights']          = $highlights;
        $this->data['youtubeHighlights']   = $youtubeList;
        $this->data['tiktokHighlights']    = $tiktokList;
        $this->data['instagramHighlights'] = $instagramList;
        $this->data['editHighlight']       = $editHighlight;
        $this->data['pageTitle']           = 'Event Highlights Studio';
        $this->data['activeMenu']          = 'highlights';

        return $this->render('admin/highlights/index', $this->data);
    }

    public function inactiveAll()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $db = \Config\Database::connect();
        $db->table('event_highlights')
            ->where('event_id', $eventId)
            ->update([
                'is_active'  => 0,
                'updated_by' => $userId,
            ]);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Semua highlights berhasil dinonaktifkan (Disable All).');
    }

    public function enableAll()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $db = \Config\Database::connect();
        $db->table('event_highlights')
            ->where('event_id', $eventId)
            ->update([
                'is_active'  => 1,
                'updated_by' => $userId,
            ]);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Semua highlights berhasil diaktifkan (Enable All).');
    }

    public function togglePlatform(string $platform, int $status)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $platform = strtolower(trim($platform));
        $highlightModel = new EventHighlightModel();
        $highlights = $highlightModel->getByEventId($eventId, false);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $targetIds = [];
        foreach ($highlights as $hl) {
            $parsed = $this->detectPlatformAndId($hl['youtube_url'] ?? '');
            if ($parsed['platform'] === $platform) {
                $targetIds[] = (int)$hl['id'];
            }
        }

        if (!empty($targetIds)) {
            $db = \Config\Database::connect();
            $db->table('event_highlights')
                ->whereIn('id', $targetIds)
                ->update([
                    'is_active'  => $status ? 1 : 0,
                    'updated_by' => $userId,
                ]);
        }

        $actionText = $status ? 'diaktifkan (Enable All)' : 'dinonaktifkan (Disable All)';
        return redirect()->to(base_url('admin/highlights'))->with('success', 'Semua konten ' . strtoupper($platform) . ' berhasil ' . $actionText . '.');
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $title      = trim($this->request->getPost('title') ?? '');
        $mediaUrl   = trim($this->request->getPost('youtube_url') ?? $this->request->getPost('media_url') ?? '');
        $sortOrder  = (int)($this->request->getPost('sort_order') ?? 0);
        $isActive   = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($mediaUrl)) {
            return redirect()->back()->with('error', 'URL is required.')->withInput();
        }

        $parsed = $this->detectPlatformAndId($mediaUrl);
        $videoId = $parsed['id'];

        // Fallback auto-fetch title if admin didn't provide one
        if (empty($title)) {
            $meta = $this->fetchTitleAndThumb($mediaUrl, $parsed);
            $title = !empty($meta['title']) ? $meta['title'] : (strtoupper($parsed['platform']) . ' Highlight ' . date('d M Y'));
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $highlightModel = new EventHighlightModel();
        $insertId = $highlightModel->insert([
            'event_id'    => $eventId,
            'title'       => $title,
            'youtube_url' => $mediaUrl,
            'video_id'    => $videoId,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive,
            'created_by'  => $userId,
            'updated_by'  => $userId,
        ]);

        if ($insertId) {
            $coverFile = $this->request->getFile('cover_image');
            $this->fetchAndStoreThumbnail((int)$insertId, $mediaUrl, $coverFile);
        }

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Highlight (' . strtoupper($parsed['platform']) . ') berhasil ditambahkan.');
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
        $mediaUrl   = trim($this->request->getPost('youtube_url') ?? $this->request->getPost('media_url') ?? '');
        $sortOrder  = (int)($this->request->getPost('sort_order') ?? 0);
        $isActive   = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($mediaUrl)) {
            return redirect()->back()->with('error', 'URL is required.')->withInput();
        }

        $parsed = $this->detectPlatformAndId($mediaUrl);
        $videoId = $parsed['id'];

        if (empty($title)) {
            $meta = $this->fetchTitleAndThumb($mediaUrl, $parsed);
            if (!empty($meta['title'])) {
                $title = $meta['title'];
            } else {
                $title = $existing['title'] ?? (strtoupper($parsed['platform']) . ' Highlight ' . date('d M Y'));
            }
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $highlightModel->update($id, [
            'title'       => $title,
            'youtube_url' => $mediaUrl,
            'video_id'    => $videoId,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive,
            'updated_by'  => $userId,
        ]);

        $coverFile = $this->request->getFile('cover_image');
        $this->fetchAndStoreThumbnail($id, $mediaUrl, $coverFile);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Highlight (' . strtoupper($parsed['platform']) . ') berhasil diperbarui.');
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

        $localThumb = FCPATH . 'uploads/highlights/' . $id . '.jpg';
        if (file_exists($localThumb)) {
            @unlink($localThumb);
        }

        $highlightModel->delete($id);

        return redirect()->to(base_url('admin/highlights'))->with('success', 'Highlight deleted successfully.');
    }
}

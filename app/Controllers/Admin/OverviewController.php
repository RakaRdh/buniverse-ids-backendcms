<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventModel;
use App\Models\EventOverviewModel;

class OverviewController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event first.');
        }

        $overviewModel = new EventOverviewModel();
        $eventModel    = new EventModel();

        $this->data['overview']   = $overviewModel->getByEventId($eventId);
        $this->data['event']      = $eventModel->find($eventId);
        $this->data['pageTitle']  = 'Event Overview & Editorial Copy';
        $this->data['activeMenu'] = 'overview';

        return $this->render('admin/overview/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $overviewModel = new EventOverviewModel();
        $eventModel    = new EventModel();
        $userId        = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        // Theme on events table
        $theme = $this->request->getPost('theme');
        if ($theme !== null) {
            $eventModel->update($eventId, [
                'theme'      => $theme,
                'updated_by' => $userId,
            ]);
        }

        // Sanitizer helper: allow <span> and inline styling, strip script/dangerous handlers
        $sanitizeSafeHtml = function ($input) {
            if (empty($input)) return '';
            // Remove script tags and their content
            $clean = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $input);
            // Remove on* event handlers (e.g. onerror, onload, onclick)
            $clean = preg_replace('#\s*on\w+\s*=\s*(["\']).*?\1#is', '', $clean);
            $clean = preg_replace('#\s*on\w+\s*=\s*[^>\s]+#is', '', $clean);
            // Remove javascript: pseudo-protocols
            $clean = preg_replace('#javascript\s*:#is', '', $clean);
            // Allow only safe tags: span, strong, em, b, i, u, br, p
            return strip_tags($clean, '<span><strong><em><b><i><u><br><p>');
        };

        $overviewData = [
            'heading'    => trim(strip_tags((string)$this->request->getPost('heading'))),
            'intro_text' => $sanitizeSafeHtml((string)$this->request->getPost('intro_text')),
            'body_text'  => $sanitizeSafeHtml((string)$this->request->getPost('body_text')),
            'updated_by' => $userId,
        ];

        // Handle 4 collage images upload
        $targetDir = FCPATH . 'uploads/events/' . $eventId . '/overview';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        for ($i = 1; $i <= 4; $i++) {
            $file = $this->request->getFile('collage_img_' . $i);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($targetDir, $newName);
                $fullPath = $targetDir . '/' . $newName;
                \App\Libraries\ImageOptimizer::optimize($fullPath, 1200, 1400, 85);
                $overviewData['collage_img_' . $i] = 'uploads/events/' . $eventId . '/overview/' . $newName;
            }
        }

        $existing = $overviewModel->getByEventId($eventId);
        if ($existing) {
            $overviewModel->update($existing['id'], $overviewData);
        } else {
            $overviewData['event_id']   = $eventId;
            $overviewData['created_by'] = $userId;
            $overviewModel->insert($overviewData);
        }

        return redirect()->back()->with('success', 'Overview editorial narrative and collage images updated successfully.');
    }
}

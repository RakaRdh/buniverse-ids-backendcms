<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventLocationModel;
use App\Libraries\ImageOptimizer;

class LocationController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $locationModel = new EventLocationModel();
        $location = $locationModel->getByEventId($eventId);

        $this->data['location']    = $location;
        $this->data['pageTitle']   = 'Event Locations & Floor Plan: ' . ($this->activeEvent['name'] ?? '');
        $this->data['activeMenu']  = 'locations';

        return $this->render('admin/locations/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $locationModel = new EventLocationModel();
        $existing = $locationModel->getByEventId($eventId);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $gmapsIframe = trim((string)$this->request->getPost('gmaps_iframe'));

        $data = [
            'hotel_name'    => trim((string)$this->request->getPost('hotel_name')),
            'hotel_address' => trim((string)$this->request->getPost('hotel_address')),
            'gmaps_iframe'  => $gmapsIframe,
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
            'updated_by'    => $userId,
        ];

        // Check remove layout image
        if ($this->request->getPost('remove_layout_image') == '1') {
            if (!empty($existing['layout_image_url']) && file_exists(FCPATH . $existing['layout_image_url'])) {
                @unlink(FCPATH . $existing['layout_image_url']);
            }
            $data['layout_image_url'] = null;
        }

        // Upload new floor plan layout image
        $imageFile = $this->request->getFile('layout_image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            if (!empty($existing['layout_image_url']) && file_exists(FCPATH . $existing['layout_image_url'])) {
                @unlink(FCPATH . $existing['layout_image_url']);
            }
            $newName = $imageFile->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/locations';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $imageFile->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            ImageOptimizer::optimize($fullPath, 1920, 1400, 85);
            $data['layout_image_url'] = 'uploads/events/' . $eventId . '/locations/' . $newName;
        } elseif ($imageFile && $imageFile->getError() !== UPLOAD_ERR_NO_FILE) {
            return redirect()->back()->with('error', 'Gagal mengupload denah layout: ' . $imageFile->getErrorString() . ' (Error code: ' . $imageFile->getError() . ')')->withInput();
        }

        if ($existing) {
            $locationModel->update($existing['id'], $data);
        } else {
            $data['event_id']   = $eventId;
            $data['created_by'] = $userId;
            $locationModel->insert($data);
        }

        return redirect()->to(base_url('admin/locations'))->with('success', 'Location settings updated successfully.');
    }
}

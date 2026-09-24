<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventHeroImageModel;
use App\Models\EventHeroModel;
use App\Models\EventModel;

class HeroController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an active event first.');
        }

        $heroModel      = new EventHeroModel();
        $heroImageModel = new EventHeroImageModel();
        $eventModel     = new EventModel();

        $this->data['event']       = $eventModel->find($eventId);
        $this->data['hero']        = $heroModel->getByEventId($eventId);
        $this->data['heroImages']  = $heroImageModel->getByEvent($eventId);
        $this->data['pageTitle']   = 'Hero Banner & Venue';
        $this->data['activeMenu']  = 'hero';

        return $this->render('admin/hero/index', $this->data);
    }

    public function updateVenue()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;
        $heroModel = new EventHeroModel();
        $existingHero = $heroModel->getByEventId($eventId);

        $heroData = [
            'venue'      => $this->request->getPost('venue'),
            'updated_by' => $userId,
        ];

        if ($existingHero) {
            $heroModel->update($existingHero['id'], $heroData);
        } else {
            $heroData['event_id']   = $eventId;
            $heroData['created_by'] = $userId;
            $heroModel->insert($heroData);
        }

        return redirect()->to(base_url('admin/hero'))->with('success', 'Hero venue location updated successfully.');
    }

    public function uploadHeroImage()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        if (!$this->validate([
            'hero_image_file' => [
                'rules'  => 'uploaded[hero_image_file]|is_image[hero_image_file]|mime_in[hero_image_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[hero_image_file,10240]',
                'errors' => [
                    'uploaded' => 'Please select an image file to upload.',
                    'is_image' => 'Uploaded file must be a valid image.',
                    'mime_in'  => 'Only JPG, JPEG, PNG, and WebP images are allowed.',
                    'max_size' => 'Image size cannot exceed 10MB.',
                ],
            ],
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->getError('hero_image_file'));
        }

        $image = $this->request->getFile('hero_image_file');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/hero';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $image->move($uploadDir, $newName);

            $heroImageModel = new EventHeroImageModel();
            $count = $heroImageModel->where('event_id', $eventId)->countAllResults();
            $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

            $heroImageModel->insert([
                'event_id'   => $eventId,
                'image_url'  => 'uploads/events/' . $eventId . '/hero/' . $newName,
                'alt_text'   => $this->request->getPost('alt_text') ?? 'Hero Banner Image',
                'sort_order' => $count + 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            return redirect()->to(base_url('admin/hero'))->with('success', 'Hero banner image uploaded successfully.');
        }

        return redirect()->to(base_url('admin/hero'))->with('error', 'Failed to upload hero image.');
    }

    public function deleteHeroImage($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $heroImageModel = new EventHeroImageModel();
        $img = $heroImageModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if ($img) {
            if (!empty($img['image_url']) && file_exists(FCPATH . $img['image_url'])) {
                @unlink(FCPATH . $img['image_url']);
            }
            $heroImageModel->where('event_id', $eventId)->delete($id);
            return redirect()->to(base_url('admin/hero'))->with('success', 'Hero image deleted.');
        }
        return redirect()->to(base_url('admin/hero'))->with('error', 'Hero image not found in active event.');
    }
}

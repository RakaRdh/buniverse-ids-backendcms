<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\GalleryImageModel;

class GalleryController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $galleryModel = new GalleryImageModel();
        $this->data['galleryImages'] = $galleryModel->where('event_id', $eventId)->orderBy('sort_order', 'ASC')->findAll();
        $this->data['pageTitle']     = 'Summit Gallery';
        $this->data['activeMenu']    = 'gallery';

        return $this->render('admin/gallery/index', $this->data);
    }

    public function upload()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $image = $this->request->getFile('image_file');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/events/' . $eventId . '/gallery', $newName);

            $galleryModel = new GalleryImageModel();
            $isFeatured = $this->request->getPost('is_featured') ? 1 : 0;

            $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

            // If newly marked as featured, reset all other photos for this event to 0
            if ($isFeatured) {
                $galleryModel->where('event_id', $eventId)->set(['is_featured' => 0, 'updated_by' => $userId])->update();
            }

            $count = $galleryModel->where('event_id', $eventId)->countAllResults();

            $galleryModel->insert([
                'event_id'    => $eventId,
                'image_url'   => 'uploads/events/' . $eventId . '/gallery/' . $newName,
                'caption'     => $this->request->getPost('caption'),
                'is_featured' => $isFeatured,
                'sort_order'  => (int)$this->request->getPost('sort_order') ?: ($count + 1),
                'created_by'  => $userId,
                'updated_by'  => $userId,
            ]);

            return redirect()->back()->with('success', 'Gallery photo uploaded.');
        }

        return redirect()->back()->with('error', 'Failed to upload photo. Please select a valid image.');
    }

    public function delete($id)
    {
        $galleryModel = new GalleryImageModel();
        $galleryModel->delete($id);
        return redirect()->back()->with('success', 'Photo removed from gallery.');
    }

    public function toggleFeatured($id)
    {
        $galleryModel = new GalleryImageModel();
        $img = $galleryModel->find($id);
        if ($img) {
            $eventId = $img['event_id'];
            $userId  = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

            if (!$img['is_featured']) {
                // Ensure only 1 photo is featured across this event
                $galleryModel->where('event_id', $eventId)->set(['is_featured' => 0, 'updated_by' => $userId])->update();
                $galleryModel->update($id, ['is_featured' => 1, 'updated_by' => $userId]);
                $msg = 'Photo set as primary featured image (default expanded in frontend).';
            } else {
                $galleryModel->update($id, ['is_featured' => 0, 'updated_by' => $userId]);
                $msg = 'Featured state unselected.';
            }
            return redirect()->back()->with('success', $msg);
        }
        return redirect()->back()->with('error', 'Photo not found.');
    }
}

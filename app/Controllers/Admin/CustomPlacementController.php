<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\CustomPlacementModel;

class CustomPlacementController extends BaseAdminController
{
    protected array $sections = [
        'home'       => 'Hero / Home',
        'about-us'   => 'About Us / Overview',
        'livestream' => 'Live Streaming',
        'schedule'   => 'Schedule',
        'gallery'    => 'Gallery',
        'news'       => 'Related News',
        'highlights' => 'Highlights',
        'sponsor'    => 'Sponsors & Partnerships',
        'location'   => 'Locations & Floor Plan',
    ];

    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $placementModel = new CustomPlacementModel();
        $themeModel     = new \App\Models\EventThemePropertyModel();

        $placements = $placementModel->getByEvent($eventId);

        $this->data['placements']    = $placements;
        $this->data['sections']      = $this->sections;
        $this->data['palette']       = $themeModel->getPaletteForPicker($eventId);
        $this->data['defaultImage']  = 'assets/img/decoration_white.png';
        $this->data['frontendUrl']   = 'http://localhost:8080';
        $this->data['pageTitle']     = 'Custom Placement (Freeform Coordinates)';
        $this->data['activeMenu']    = 'custom-placements';

        return $this->render('admin/custom_placements/index', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $placementModel = new CustomPlacementModel();
        $id = (int)$this->request->getPost('id');
        $existing = $id ? $placementModel->find($id) : null;

        $name               = trim($this->request->getPost('name') ?? '');
        $sectionKey         = $this->request->getPost('section_key');
        $posXPercent        = (float)$this->request->getPost('pos_x_percent');
        $posYPercent        = (float)$this->request->getPost('pos_y_percent');
        $sizePx             = (int)$this->request->getPost('size_px') ?: 220;
        $opacity            = (float)$this->request->getPost('opacity') ?: 1.00;
        $isRotated          = $this->request->getPost('is_rotated') ? 1 : 0;
        $rotationDirection  = $this->request->getPost('rotation_direction') ?: 'clockwise';
        $deviceVisibility   = $this->request->getPost('device_visibility') ?: 'all';
        $showOnMobile       = ($deviceVisibility !== 'desktop_only') ? 1 : 0;
        $isActive           = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;
        $zIndexPost         = $this->request->getPost('z_index');
        $zIndex             = ($zIndexPost !== null && $zIndexPost !== '') ? (int)$zIndexPost : 1;
        $userId             = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        if (!array_key_exists($sectionKey, $this->sections)) {
            return redirect()->back()->with('error', 'Invalid section selected.');
        }

        // Image handling
        $imageUrl = $existing['image_url'] ?? 'assets/img/decoration_white.png';
        $imageFile = $this->request->getFile('image_file');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            if (!$this->validate([
                'image_file' => [
                    'rules'  => 'is_image[image_file]|mime_in[image_file,image/jpg,image/jpeg,image/png,image/webp,image/svg+xml]|max_size[image_file,4096]',
                    'errors' => [
                        'is_image' => 'Placement image must be a valid image file.',
                        'mime_in'  => 'Only JPG, PNG, WebP, and SVG images are allowed.',
                        'max_size' => 'Placement image size cannot exceed 4MB.',
                    ],
                ],
            ])) {
                return redirect()->back()->withInput()->with('error', $this->validator->getError('image_file'));
            }

            $newName = $imageFile->getRandomName();
            $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/custom_placements';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $imageFile->move($uploadDir, $newName);
            $imageUrl = 'uploads/events/' . $eventId . '/custom_placements/' . $newName;
        }

        $perspectiveEnabled = $this->request->getPost('perspective_enabled') ? 1 : 0;
        $perspectiveValue   = (int)$this->request->getPost('perspective_value') ?: 800;
        $rotateX            = (float)$this->request->getPost('rotate_x') ?: 0.0;
        $rotateY            = (float)$this->request->getPost('rotate_y') ?: 0.0;
        $rotateZ            = (float)$this->request->getPost('rotate_z') ?: 0.0;

        $data = [
            'event_id'            => $eventId,
            'name'                => $name ?: null,
            'section_key'         => $sectionKey,
            'pos_x_percent'       => $posXPercent,
            'pos_y_percent'       => $posYPercent,
            'size_px'             => $sizePx,
            'opacity'             => $opacity,
            'is_rotated'          => $isRotated,
            'rotation_direction'  => $rotationDirection,
            'perspective_enabled' => $perspectiveEnabled,
            'perspective_value'   => $perspectiveValue,
            'rotate_x'            => $rotateX,
            'rotate_y'            => $rotateY,
            'rotate_z'            => $rotateZ,
            'show_on_mobile'      => $showOnMobile,
            'device_visibility'   => $deviceVisibility,
            'is_active'           => $isActive,
            'z_index'             => $zIndex,
            'tint_color'          => $this->request->getPost('tint_color') ?: null,
            'image_url'           => $imageUrl,
            'updated_by'          => $userId,
        ];

        if ($existing) {
            $placementModel->where('event_id', $eventId)->update($id, $data);
            $message = 'Custom placement updated successfully.';
        } else {
            $data['created_by'] = $userId;
            $placementModel->insert($data);
            $message = 'Custom placement created successfully.';
        }

        $this->purgeFrontendCache();

        return redirect()->to(base_url('admin/custom-placements'))->with('success', $message);
    }

    public function toggle(int $id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $placementModel = new CustomPlacementModel();
        $item = $placementModel->where('event_id', $eventId)->find($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Decoration not found.');
        }

        $newStatus = $item['is_active'] ? 0 : 1;
        $placementModel->where('event_id', $eventId)->update($id, [
            'is_active'  => $newStatus,
            'updated_by' => $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1,
        ]);

        $this->purgeFrontendCache();

        return redirect()->to(base_url('admin/custom-placements'))->with('success', 'Status updated.');
    }

    public function delete(int $id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $placementModel = new CustomPlacementModel();
        $item = $placementModel->where('event_id', $eventId)->find($id);

        if ($item) {
            if (!empty($item['image_url']) && str_starts_with($item['image_url'], 'uploads/') && file_exists(FCPATH . $item['image_url'])) {
                @unlink(FCPATH . $item['image_url']);
            }
            $placementModel->where('event_id', $eventId)->delete($id);
            $this->purgeFrontendCache();
            return redirect()->to(base_url('admin/custom-placements'))->with('success', 'Custom placement deleted.');
        }

        return redirect()->back()->with('error', 'Item not found.');
    }
}

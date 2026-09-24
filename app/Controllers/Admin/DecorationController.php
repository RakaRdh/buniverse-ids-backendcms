<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\SectionDecorationModel;

class DecorationController extends BaseAdminController
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

        $decorationModel = new SectionDecorationModel();
        $themeModel      = new \App\Models\EventThemePropertyModel();

        $decorations = $decorationModel->getByEvent($eventId);

        $this->data['decorations']   = $decorations;
        $this->data['sections']      = $this->sections;
        $this->data['palette']       = $themeModel->getPaletteForPicker($eventId);
        $this->data['defaultImage']  = 'assets/img/cakram.webp';
        $this->data['pageTitle']     = 'Section Decorations';
        $this->data['activeMenu']    = 'decorations';

        return $this->render('admin/decorations/index', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $decorationModel = new SectionDecorationModel();
        $id = (int)$this->request->getPost('id');
        $existing = $id ? $decorationModel->find($id) : null;

        $sectionKey         = $this->request->getPost('section_key');
        $layoutMode         = $this->request->getPost('layout_mode') ?: 'manual';
        $isAuto             = ($layoutMode === 'auto');
        $position           = $isAuto ? null : ($this->request->getPost('position') ?: null);

        $autoCount          = $isAuto ? max(1, (int)$this->request->getPost('auto_count')) : null;
        $autoGapPx          = $isAuto ? max(10, (int)$this->request->getPost('auto_gap_px')) : null;
        $autoStartSide      = $isAuto ? ($this->request->getPost('auto_start_side') ?: 'left') : null;
        $autoGapUnit        = $isAuto ? ($this->request->getPost('auto_gap_unit') ?: 'px') : null;
        $edgeOffsetMode     = $this->request->getPost('edge_offset_mode') ?: 'fixed';
        $edgeOffsetPercent  = $this->request->getPost('edge_offset_percent') !== null ? (int)$this->request->getPost('edge_offset_percent') : 50;

        $sizePx             = (int)$this->request->getPost('size_px') ?: 200;
        $opacity            = (float)$this->request->getPost('opacity') ?: 1.00;
        $isRotated          = $this->request->getPost('is_rotated') ? 1 : 0;
        $rotationDirection  = $this->request->getPost('rotation_direction') ?: 'clockwise';
        $perspectiveEnabled = $this->request->getPost('perspective_enabled') ? 1 : 0;
        $perspectiveValue   = $this->request->getPost('perspective_value') !== null ? (int)$this->request->getPost('perspective_value') : 800;
        $rotateX            = $this->request->getPost('rotate_x') !== null ? (float)$this->request->getPost('rotate_x') : 0.0;
        $rotateY            = $this->request->getPost('rotate_y') !== null ? (float)$this->request->getPost('rotate_y') : 0.0;
        $rotateZ            = $this->request->getPost('rotate_z') !== null ? (float)$this->request->getPost('rotate_z') : 0.0;
        $deviceVisibility   = $this->request->getPost('device_visibility') ?: 'all';
        $showOnMobile       = ($deviceVisibility !== 'desktop_only') ? 1 : 0;
        $isActive           = $this->request->getPost('is_active') ? 1 : 0;
        $userId             = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        if (!array_key_exists($sectionKey, $this->sections)) {
            return redirect()->back()->with('error', 'Invalid section selected.');
        }

        // Validate manual mode position with device visibility check
        if ($layoutMode === 'manual') {
            if (empty($position)) {
                return redirect()->back()->with('error', 'Position is required for manual layout mode.');
            }
            if ($isActive && $decorationModel->hasDuplicateManualPosition($eventId, $sectionKey, $position, $id ?: null, $deviceVisibility)) {
                return redirect()->back()->with('error', "An active decoration already exists at position '{$position}' for this section with conflicting device visibility. Choose another position or adjust visibility.");
            }
        }

        // Image handling: default fallback is decoration_white.png
        $imageUrl = $existing['image_url'] ?? 'assets/img/decoration_white.png';
        $imageFile = $this->request->getFile('image_file');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            if (!$this->validate([
                'image_file' => [
                    'rules'  => 'is_image[image_file]|mime_in[image_file,image/jpg,image/jpeg,image/png,image/webp,image/svg+xml]|max_size[image_file,4096]',
                    'errors' => [
                        'is_image' => 'Decoration image must be a valid image file.',
                        'mime_in'  => 'Only JPG, PNG, WebP, and SVG images are allowed.',
                        'max_size' => 'Decoration image size cannot exceed 4MB.',
                    ],
                ],
            ])) {
                return redirect()->back()->withInput()->with('error', $this->validator->getError('image_file'));
            }

            $newName = $imageFile->getRandomName();
            $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/decorations';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $imageFile->move($uploadDir, $newName);
            $imageUrl = 'uploads/events/' . $eventId . '/decorations/' . $newName;
        }

        $data = [
            'event_id'            => $eventId,
            'section_key'         => $sectionKey,
            'layout_mode'         => $layoutMode,
            'position'            => $position,
            'auto_count'          => $autoCount,
            'auto_gap_px'         => $autoGapPx,
            'auto_start_side'     => $autoStartSide,
            'auto_gap_unit'       => $autoGapUnit,
            'edge_offset_mode'    => $edgeOffsetMode,
            'edge_offset_percent' => $edgeOffsetPercent,
            'image_url'           => $imageUrl,
            'size_px'             => $sizePx,
            'opacity'             => $opacity,
            'is_rotated'          => $isRotated,
            'rotation_direction'  => $rotationDirection,
            'perspective_enabled' => $perspectiveEnabled,
            'perspective_value'   => $perspectiveValue,
            'rotate_x'            => $rotateX,
            'rotate_y'            => $rotateY,
            'rotate_z'            => $rotateZ,
            'tint_color'          => $this->request->getPost('tint_color') ?: null,
            'device_visibility'   => $deviceVisibility,
            'show_on_mobile'      => $showOnMobile,
            'is_active'           => $isActive,
            'updated_by'          => $userId,
        ];

        if ($existing) {
            $decorationModel->where('event_id', $eventId)->update($id, $data);
            $message = 'Section decoration updated.';
        } else {
            $data['created_by'] = $userId;
            $decorationModel->insert($data);
            $message = 'Section decoration created.';
        }

        return redirect()->to(base_url('admin/decorations'))->with('success', $message);
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $decorationModel = new SectionDecorationModel();
        $record = $decorationModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if ($record) {
            // Delete uploaded file if stored under uploads/
            if (!empty($record['image_url']) && str_starts_with($record['image_url'], 'uploads/') && file_exists(FCPATH . $record['image_url'])) {
                @unlink(FCPATH . $record['image_url']);
            }
            $decorationModel->where('event_id', $eventId)->delete($id);
        }

        return redirect()->to(base_url('admin/decorations'))->with('success', 'Decoration removed.');
    }

    public function toggleActive($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $decorationModel = new SectionDecorationModel();
        $record = $decorationModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if ($record) {
            $newStatus = $record['is_active'] ? 0 : 1;
            // If activating a manual position, check for duplicate
            if ($newStatus === 1 && $record['layout_mode'] === 'manual') {
                if ($decorationModel->hasDuplicateManualPosition($record['event_id'], $record['section_key'], $record['position'], (int)$record['id'])) {
                    return redirect()->back()->with('error', "Cannot activate: another active decoration exists at position '{$record['position']}'.");
                }
            }
            $decorationModel->where('event_id', $eventId)->update($id, ['is_active' => $newStatus]);
        }

        return redirect()->to(base_url('admin/decorations'))->with('success', 'Decoration status updated.');
    }
}

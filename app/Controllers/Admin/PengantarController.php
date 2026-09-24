<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\PengantarModel;
use App\Libraries\ImageOptimizer;

class PengantarController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $pengantarModel = new PengantarModel();
        $this->data['pengantar']   = $pengantarModel->getByEvent($eventId);
        $this->data['pageTitle']   = 'Pengantar: ' . ($this->activeEvent['name'] ?? 'Current Event');
        $this->data['activeMenu']  = 'pengantar';

        return $this->render('admin/pengantar/index', $this->data);
    }

    public function new()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $this->data['pengantar']   = null;
        $this->data['pageTitle']   = 'Add New Pengantar';
        $this->data['activeMenu']  = 'pengantar';

        return $this->render('admin/pengantar/form', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'No active event selected.');
        }

        $name = trim((string)$this->request->getPost('name'));
        $slug = url_title($name, '-', true);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $pengantarModel = new PengantarModel();
        $count = $pengantarModel->where('event_id', $eventId)->countAllResults();

        $data = [
            'event_id'   => $eventId,
            'name'       => $name,
            'slug'       => $slug,
            'role_text'  => $this->request->getPost('role_text'),
            'sort_order' => (int)$this->request->getPost('sort_order') ?: ($count + 1),
            'is_active'  => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        // Upload photo
        $photo = $this->request->getFile('photo_file');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            if (!$this->validate([
                'photo_file' => [
                    'rules'  => 'is_image[photo_file]|mime_in[photo_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[photo_file,4096]',
                    'errors' => [
                        'is_image' => 'Uploaded file must be a valid image.',
                        'mime_in'  => 'Only JPG, PNG, and WebP images are allowed.',
                        'max_size' => 'Image size cannot exceed 4MB.',
                    ],
                ],
            ])) {
                return redirect()->back()->withInput()->with('error', $this->validator->getError('photo_file'));
            }

            $newName = $photo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/pengantar';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $eventId . '/pengantar/' . $newName;
        }

        $pengantarModel->insert($data);
        return redirect()->to(base_url('admin/pengantar'))->with('success', 'Pengantar added successfully.');
    }

    public function edit($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $pengantarModel = new PengantarModel();
        $pengantar = $pengantarModel->where('id', (int)$id)->where('event_id', $eventId)->first();

        if (!$pengantar) {
            return redirect()->to(base_url('admin/pengantar'))->with('error', 'Pengantar not found.');
        }

        $this->data['pengantar']   = $pengantar;
        $this->data['pageTitle']   = 'Edit Pengantar: ' . $pengantar['name'];
        $this->data['activeMenu']  = 'pengantar';

        return $this->render('admin/pengantar/form', $this->data);
    }

    public function update($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $pengantarModel = new PengantarModel();
        $pengantar = $pengantarModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if (!$pengantar) {
            return redirect()->to(base_url('admin/pengantar'))->with('error', 'Pengantar not found.');
        }

        $name = trim((string)$this->request->getPost('name'));
        $slug = url_title($name, '-', true);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $data = [
            'name'       => $name,
            'slug'       => $slug,
            'role_text'  => $this->request->getPost('role_text'),
            'sort_order' => (int)$this->request->getPost('sort_order'),
            'is_active'  => 1,
            'updated_by' => $userId,
        ];

        // Check remove photo request
        if ($this->request->getPost('remove_photo') == '1') {
            if (!empty($pengantar['photo_url']) && file_exists(FCPATH . $pengantar['photo_url'])) {
                @unlink(FCPATH . $pengantar['photo_url']);
            }
            $data['photo_url'] = null;
        }

        // Upload photo (if uploaded, it overrides remove_photo)
        $photo = $this->request->getFile('photo_file');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            if (!$this->validate([
                'photo_file' => [
                    'rules'  => 'is_image[photo_file]|mime_in[photo_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[photo_file,4096]',
                    'errors' => [
                        'is_image' => 'Uploaded file must be a valid image.',
                        'mime_in'  => 'Only JPG, PNG, and WebP images are allowed.',
                        'max_size' => 'Image size cannot exceed 4MB.',
                    ],
                ],
            ])) {
                return redirect()->back()->withInput()->with('error', $this->validator->getError('photo_file'));
            }

            if (!empty($pengantar['photo_url']) && file_exists(FCPATH . $pengantar['photo_url'])) {
                @unlink(FCPATH . $pengantar['photo_url']);
            }
            $newName = $photo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $pengantar['event_id'] . '/pengantar';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $pengantar['event_id'] . '/pengantar/' . $newName;
        }

        $pengantarModel->where('event_id', $eventId)->update($id, $data);
        return redirect()->to(base_url('admin/pengantar#pengantar-' . $id))->with('success', 'Pengantar updated successfully.');
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $db = \Config\Database::connect();
        $assignedCount = $db->table('session_pengantar')->where('pengantar_id', $id)->countAllResults();

        if ($assignedCount > 0) {
            return redirect()->to(base_url('admin/pengantar'))->with('error', 'Cannot delete: Pengantar is assigned to ' . $assignedCount . ' session schedule track(s). Unassign from sessions first.');
        }

        $pengantarModel = new PengantarModel();
        $pengantar = $pengantarModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if ($pengantar) {
            if (!empty($pengantar['photo_url']) && file_exists(FCPATH . $pengantar['photo_url'])) {
                @unlink(FCPATH . $pengantar['photo_url']);
            }
            $pengantarModel->where('event_id', $eventId)->delete($id);
            return redirect()->to(base_url('admin/pengantar'))->with('success', 'Pengantar deleted successfully.');
        }

        return redirect()->to(base_url('admin/pengantar'))->with('error', 'Pengantar not found in active event.');
    }

    public function quickSave()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'AJAX request required']);
        }

        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'No active event selected']);
        }

        $name = trim((string)$this->request->getPost('name'));
        $role = trim((string)$this->request->getPost('role_text'));

        if (empty($name) || empty($role)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Full Name and Role/Title are required.']);
        }

        $slug = url_title($name, '-', true);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $pengantarModel = new PengantarModel();
        $count = $pengantarModel->where('event_id', $eventId)->countAllResults();

        $data = [
            'event_id'   => $eventId,
            'name'       => $name,
            'slug'       => $slug,
            'role_text'  => $role,
            'sort_order' => $count + 1,
            'is_active'  => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        // Handle quick photo upload if present
        $photo = $this->request->getFile('photo_file');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            if (!$this->validate([
                'photo_file' => [
                    'rules' => 'is_image[photo_file]|mime_in[photo_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[photo_file,4096]',
                ],
            ])) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => $this->validator->getError('photo_file')]);
            }

            $newName = $photo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/pengantar';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $eventId . '/pengantar/' . $newName;
        }

        $insertId = $pengantarModel->insert($data);
        if (!$insertId) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to save pengantar']);
        }

        $newPengantar = $pengantarModel->find($insertId);

        return $this->response->setJSON([
            'success'   => true,
            'pengantar' => [
                'id'        => (int)$newPengantar['id'],
                'name'      => $newPengantar['name'],
                'role'      => $newPengantar['role_text'],
                'role_text' => $newPengantar['role_text'],
                'photo_url' => $newPengantar['photo_url'] ? base_url($newPengantar['photo_url']) : '',
            ],
            'message' => 'Pengantar registered successfully'
        ]);
    }
}

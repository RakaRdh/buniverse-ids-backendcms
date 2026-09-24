<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\ModeratorModel;

class ModeratorController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $moderatorModel = new ModeratorModel();
        $this->data['moderators'] = $moderatorModel->where('event_id', $eventId)->orderBy('sort_order', 'ASC')->findAll();
        $this->data['pageTitle']  = 'Summit Moderators';
        $this->data['activeMenu'] = 'moderators';

        return $this->render('admin/moderators/index', $this->data);
    }

    public function create()
    {
        $this->data['moderator']  = null;
        $this->data['pageTitle']  = 'Add New Moderator';
        $this->data['activeMenu'] = 'moderators';

        return $this->render('admin/moderators/form', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $moderatorModel = new ModeratorModel();
        $count = $moderatorModel->where('event_id', $eventId)->countAllResults();

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $data = [
            'event_id'   => $eventId,
            'name'       => $this->request->getPost('name'),
            'role_text'  => $this->request->getPost('role_text'),
            'sort_order' => ($count + 1),
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
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/moderators';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            \App\Libraries\ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $eventId . '/moderators/' . $newName;
        }

        $moderatorModel->insert($data);
        return redirect()->to(base_url('admin/moderators'))->with('success', 'Moderator added successfully.');
    }

    public function edit($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $moderatorModel = new ModeratorModel();
        $moderator = $moderatorModel->where('id', (int)$id)->where('event_id', $eventId)->first();

        if (!$moderator) {
            return redirect()->to(base_url('admin/moderators'))->with('error', 'Moderator not found.');
        }

        $this->data['moderator']  = $moderator;
        $this->data['pageTitle']  = 'Edit Moderator: ' . $moderator['name'];
        $this->data['activeMenu'] = 'moderators';

        return $this->render('admin/moderators/form', $this->data);
    }

    public function update($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $moderatorModel = new ModeratorModel();
        $moderator = $moderatorModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if (!$moderator) {
            return redirect()->to(base_url('admin/moderators'))->with('error', 'Moderator not found.');
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $data = [
            'name'       => $this->request->getPost('name'),
            'role_text'  => $this->request->getPost('role_text'),
            'is_active'  => 1,
            'updated_by' => $userId,
        ];

        // Check remove photo request
        if ($this->request->getPost('remove_photo') == '1') {
            if (!empty($moderator['photo_url']) && file_exists(FCPATH . $moderator['photo_url'])) {
                @unlink(FCPATH . $moderator['photo_url']);
            }
            $data['photo_url'] = null;
        }

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

            if (!empty($moderator['photo_url']) && file_exists(FCPATH . $moderator['photo_url'])) {
                @unlink(FCPATH . $moderator['photo_url']);
            }
            $newName = $photo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $moderator['event_id'] . '/moderators';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            \App\Libraries\ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $moderator['event_id'] . '/moderators/' . $newName;
        }

        $moderatorModel->where('event_id', $eventId)->update($id, $data);
        return redirect()->to(base_url('admin/moderators#moderator-' . $id))->with('success', 'Moderator updated successfully.');
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $db = \Config\Database::connect();
        $assignedCount = $db->table('session_moderators')->where('moderator_id', $id)->countAllResults();

        if ($assignedCount > 0) {
            return redirect()->to(base_url('admin/moderators'))->with('error', 'Cannot delete moderator: Moderator is assigned to ' . $assignedCount . ' session schedule track(s). Unassign from sessions first.');
        }

        $moderatorModel = new ModeratorModel();
        $moderator = $moderatorModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if ($moderator) {
            if (!empty($moderator['photo_url']) && file_exists(FCPATH . $moderator['photo_url'])) {
                @unlink(FCPATH . $moderator['photo_url']);
            }
            $moderatorModel->where('event_id', $eventId)->delete($id);
            return redirect()->to(base_url('admin/moderators'))->with('success', 'Moderator deleted successfully.');
        }

        return redirect()->to(base_url('admin/moderators'))->with('error', 'Moderator not found in active event.');
    }

    public function reorder()
    {
        $order = $this->request->getPost('order');
        if (is_array($order)) {
            $moderatorModel = new ModeratorModel();
            foreach ($order as $sort => $id) {
                $moderatorModel->update($id, ['sort_order' => $sort + 1]);
            }
        }
        return $this->response->setJSON(['status' => 'success']);
    }

    public function quickSave()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'No active event found.']);
        }

        $name = trim((string)$this->request->getPost('name'));
        if (empty($name)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Moderator name is required.']);
        }

        $moderatorModel = new ModeratorModel();
        $count = $moderatorModel->where('event_id', $eventId)->countAllResults();
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $roleText = trim((string)$this->request->getPost('role_text'));

        $data = [
            'event_id'   => $eventId,
            'name'       => $name,
            'role_text'  => $roleText,
            'sort_order' => ($count + 1),
            'is_active'  => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        // Optional photo
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
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/moderators';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            \App\Libraries\ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $eventId . '/moderators/' . $newName;
        }

        $id = $moderatorModel->insert($data);
        if (!$id) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to save moderator.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'moderator' => [
                'id'        => (int)$id,
                'name'      => $name,
                'role'      => $roleText,
                'role_text' => $roleText,
                'photo_url' => $data['photo_url'] ?? null,
            ],
            'message' => 'Moderator added successfully.',
        ]);
    }
}

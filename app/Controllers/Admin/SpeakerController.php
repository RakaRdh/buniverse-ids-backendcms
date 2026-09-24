<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\SpeakerModel;

class SpeakerController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $speakerModel = new SpeakerModel();
        $this->data['speakers']   = $speakerModel->where('event_id', $eventId)->orderBy('sort_order', 'ASC')->findAll();
        $this->data['pageTitle']  = 'Summit Speakers';
        $this->data['activeMenu'] = 'speakers';

        return $this->render('admin/speakers/index', $this->data);
    }

    public function create()
    {
        $this->data['speaker']    = null;
        $this->data['pageTitle']  = 'Add New Speaker';
        $this->data['activeMenu'] = 'speakers';

        return $this->render('admin/speakers/form', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $speakerModel = new SpeakerModel();
        $count = $speakerModel->where('event_id', $eventId)->countAllResults();

        $name = trim((string)$this->request->getPost('name'));
        $slug = url_title($name, '-', true);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $data = [
            'event_id'   => $eventId,
            'name'       => $name,
            'slug'       => $slug,
            'role_text'  => $this->request->getPost('role_text'),
            'bio'        => null,
            'sort_order' => ($count + 1),
            'is_active'  => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        // Upload photo
        $photo = $this->request->getFile('photo_file');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/speakers';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            \App\Libraries\ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $eventId . '/speakers/' . $newName;
        }

        $speakerModel->insert($data);
        return redirect()->to(base_url('admin/speakers'))->with('success', 'Speaker added successfully.');
    }

    public function edit($id)
    {
        $speakerModel = new SpeakerModel();
        $speaker = $speakerModel->find($id);

        if (!$speaker) {
            return redirect()->to(base_url('admin/speakers'))->with('error', 'Speaker not found.');
        }

        $this->data['speaker']    = $speaker;
        $this->data['pageTitle']  = 'Edit Speaker: ' . $speaker['name'];
        $this->data['activeMenu'] = 'speakers';

        return $this->render('admin/speakers/form', $this->data);
    }

    public function update($id)
    {
        $speakerModel = new SpeakerModel();
        $speaker = $speakerModel->find($id);
        if (!$speaker) {
            return redirect()->to(base_url('admin/speakers'))->with('error', 'Speaker not found.');
        }

        $name = trim((string)$this->request->getPost('name'));
        $slug = url_title($name, '-', true);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $data = [
            'name'       => $name,
            'slug'       => $slug,
            'role_text'  => $this->request->getPost('role_text'),
            'is_active'  => 1,
            'updated_by' => $userId,
        ];

        // Check remove photo request
        if ($this->request->getPost('remove_photo') == '1') {
            if (!empty($speaker['photo_url']) && file_exists(FCPATH . $speaker['photo_url'])) {
                @unlink(FCPATH . $speaker['photo_url']);
            }
            $data['photo_url'] = null;
        }

        // Upload photo (if uploaded, it overrides remove_photo)
        $photo = $this->request->getFile('photo_file');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            if (!empty($speaker['photo_url']) && file_exists(FCPATH . $speaker['photo_url'])) {
                @unlink(FCPATH . $speaker['photo_url']);
            }
            $newName = $photo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $speaker['event_id'] . '/speakers';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            \App\Libraries\ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $speaker['event_id'] . '/speakers/' . $newName;
        }

        $speakerModel->update($id, $data);
        return redirect()->to(base_url('admin/speakers#speaker-' . $id))->with('success', 'Speaker updated successfully.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $assignedCount = $db->table('session_speakers')->where('speaker_id', $id)->countAllResults();

        if ($assignedCount > 0) {
            return redirect()->to(base_url('admin/speakers'))->with('error', 'Cannot delete speaker: Speaker is assigned to ' . $assignedCount . ' session schedule track(s). Unassign from sessions first.');
        }

        $speakerModel = new SpeakerModel();
        $speakerModel->delete($id);
        return redirect()->to(base_url('admin/speakers'))->with('success', 'Speaker deleted successfully.');
    }

    public function reorder()
    {
        $order = $this->request->getPost('order');
        if (is_array($order)) {
            $speakerModel = new SpeakerModel();
            foreach ($order as $sort => $id) {
                $speakerModel->update($id, ['sort_order' => $sort + 1]);
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
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Speaker name is required.']);
        }

        $speakerModel = new SpeakerModel();
        $count = $speakerModel->where('event_id', $eventId)->countAllResults();
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $slug = url_title($name, '-', true);
        $roleText = trim((string)$this->request->getPost('role_text'));

        $data = [
            'event_id'   => $eventId,
            'name'       => $name,
            'slug'       => $slug,
            'role_text'  => $roleText,
            'bio'        => null,
            'sort_order' => ($count + 1),
            'is_active'  => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        // Optional photo
        $photo = $this->request->getFile('photo_file');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/speakers';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo->move($targetDir, $newName);
            $fullPath = $targetDir . '/' . $newName;
            \App\Libraries\ImageOptimizer::optimize($fullPath, 800, 800, 85);
            $data['photo_url'] = 'uploads/events/' . $eventId . '/speakers/' . $newName;
        }

        $id = $speakerModel->insert($data);
        if (!$id) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to save speaker.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'speaker' => [
                'id'        => (int)$id,
                'name'      => $name,
                'role'      => $roleText,
                'role_text' => $roleText,
                'slug'      => $slug,
                'photo_url' => $data['photo_url'] ?? null,
            ],
            'message' => 'Speaker added successfully.',
        ]);
    }
}

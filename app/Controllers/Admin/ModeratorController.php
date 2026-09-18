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
            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/events/' . $eventId . '/moderators', $newName);
            $data['photo_url'] = 'uploads/events/' . $eventId . '/moderators/' . $newName;
        }

        $moderatorModel->insert($data);
        return redirect()->to(base_url('admin/moderators'))->with('success', 'Moderator added successfully.');
    }

    public function edit($id)
    {
        $moderatorModel = new ModeratorModel();
        $moderator = $moderatorModel->find($id);

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
        $moderatorModel = new ModeratorModel();
        $moderator = $moderatorModel->find($id);
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

        // Upload photo
        $photo = $this->request->getFile('photo_file');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/events/' . $moderator['event_id'] . '/moderators', $newName);
            $data['photo_url'] = 'uploads/events/' . $moderator['event_id'] . '/moderators/' . $newName;
        }

        $moderatorModel->update($id, $data);
        return redirect()->to(base_url('admin/moderators'))->with('success', 'Moderator updated successfully.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $assignedCount = $db->table('session_moderators')->where('moderator_id', $id)->countAllResults();

        if ($assignedCount > 0) {
            return redirect()->to(base_url('admin/moderators'))->with('error', 'Cannot delete moderator: Moderator is assigned to ' . $assignedCount . ' session schedule track(s). Unassign from sessions first.');
        }

        $moderatorModel = new ModeratorModel();
        $moderatorModel->delete($id);
        return redirect()->to(base_url('admin/moderators'))->with('success', 'Moderator deleted successfully.');
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
}

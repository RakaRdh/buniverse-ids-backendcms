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

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $data = [
            'event_id'   => $eventId,
            'name'       => $this->request->getPost('name'),
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
            $photo->move(FCPATH . 'uploads/events/' . $eventId . '/speakers', $newName);
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
            $photo->move(FCPATH . 'uploads/events/' . $speaker['event_id'] . '/speakers', $newName);
            $data['photo_url'] = 'uploads/events/' . $speaker['event_id'] . '/speakers/' . $newName;
        }

        $speakerModel->update($id, $data);
        return redirect()->to(base_url('admin/speakers'))->with('success', 'Speaker updated successfully.');
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
}

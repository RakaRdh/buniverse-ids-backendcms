<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\ScheduleRoomModel;

class RoomController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event.');
        }

        $roomModel = new ScheduleRoomModel();
        $rooms = $roomModel->getByEvent((int)$eventId);

        $this->data['rooms']      = $rooms;
        $this->data['pageTitle']  = 'Breakout & Stage Rooms';
        $this->data['activeMenu'] = 'rooms';

        return $this->render('admin/rooms/index', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $roomModel = new ScheduleRoomModel();
        $name = trim((string)$this->request->getPost('name'));
        if (empty($name)) {
            return redirect()->back()->with('error', 'Room name is required.');
        }

        $count = $roomModel->where('event_id', $eventId)->countAllResults();

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $roomModel->insert([
            'event_id'   => $eventId,
            'name'       => $name,
            'sort_order' => $count + 1,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return redirect()->to(base_url('admin/rooms'))->with('success', 'Room added successfully.');
    }

    public function update($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $roomModel = new ScheduleRoomModel();
        $room = $roomModel->where('event_id', $eventId)->find((int)$id);
        if (!$room) {
            return redirect()->to(base_url('admin/rooms'))->with('error', 'Room not found.');
        }

        $name = trim((string)$this->request->getPost('name'));
        if (empty($name)) {
            return redirect()->back()->with('error', 'Room name cannot be empty.');
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $roomModel->update((int)$id, [
            'name'       => $name,
            'updated_by' => $userId,
        ]);

        return redirect()->to(base_url('admin/rooms'))->with('success', 'Room updated successfully.');
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $roomModel = new ScheduleRoomModel();
        $room = $roomModel->where('event_id', $eventId)->find((int)$id);
        if ($room) {
            $roomModel->delete((int)$id);
            return redirect()->to(base_url('admin/rooms'))->with('success', 'Room deleted successfully.');
        }

        return redirect()->to(base_url('admin/rooms'))->with('error', 'Room not found.');
    }
}

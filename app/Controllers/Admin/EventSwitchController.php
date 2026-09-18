<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventModel;

class EventSwitchController extends BaseAdminController
{
    public function switch()
    {
        $eventId = (int)$this->request->getPost('event_id');
        $eventModel = new EventModel();
        $event = $eventModel->find($eventId);

        if ($event) {
            session()->set('active_event_id', $eventId);
            $target = $this->request->getPost('redirect_dashboard') ? 'admin/dashboard' : '';
            if ($target) {
                return redirect()->to(base_url($target))->with('success', "Active workspace: IDS {$event['year']} — {$event['title']}");
            }
            return redirect()->back()->with('success', "Switched to IDS {$event['year']} — {$event['title']}.");
        }

        return redirect()->back()->with('error', 'Event not found.');
    }
}

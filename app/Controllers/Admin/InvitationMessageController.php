<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\InvitationMessageModel;

class InvitationMessageController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Silakan pilih event terlebih dahulu.');
        }

        $messageModel = new InvitationMessageModel();
        $message = $messageModel->getOrCreateForEvent($eventId, $this->activeEvent ?? []);

        $this->data['message']    = $message;
        $this->data['pageTitle']  = 'E-Invitation Blast Message Settings';
        $this->data['activeMenu'] = 'invitation_messages';

        return $this->render('admin/invitations/messages_index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'Event tidak valid.');
        }

        $messageModel = new InvitationMessageModel();
        $existing = $messageModel->where('event_id', $eventId)->first();

        $updateData = [
            'subject'      => trim((string)$this->request->getPost('subject')),
            'greeting'     => trim((string)$this->request->getPost('greeting')),
            'body_content' => trim((string)$this->request->getPost('body_content')),
            'button_text'  => trim((string)$this->request->getPost('button_text')),
            'closing_text' => trim((string)$this->request->getPost('closing_text')),
            'footer_note'  => trim((string)$this->request->getPost('footer_note')),
        ];

        if ($existing) {
            $messageModel->update($existing['id'], $updateData);
        } else {
            $updateData['event_id'] = $eventId;
            $messageModel->insert($updateData);
        }

        return redirect()->to(base_url('admin/invitation-messages'))->with('success', 'Template pesan blast email berhasil disimpan.');
    }
}

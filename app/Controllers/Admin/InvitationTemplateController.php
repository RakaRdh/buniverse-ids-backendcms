<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\InvitationTemplateModel;

class InvitationTemplateController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Silakan pilih event terlebih dahulu.');
        }

        $templateModel = new InvitationTemplateModel();
        $templates = $templateModel->where('event_id', $eventId)->orderBy('is_default', 'DESC')->orderBy('id', 'ASC')->findAll();

        // Seed initial default template if none exists
        if (empty($templates)) {
            $defaultTpl = [
                'event_id'      => $eventId,
                'template_key'  => 'malam_apresiasi_luxury',
                'name'          => 'Royal Gold Luxury (Default)',
                'description'   => 'Tema portrait elegan gelap dengan aksen emas mewah, countdown, dan animasi cover.',
                'thumbnail_url' => base_url('assets/img/template_luxury_thumb.webp'),
                'is_active'     => 1,
                'is_default'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $templateModel->insert($defaultTpl);
            $templates = $templateModel->where('event_id', $eventId)->orderBy('is_default', 'DESC')->findAll();
        }

        $this->data['templates']  = $templates;
        $this->data['pageTitle']  = 'E-Invitation Templates';
        $this->data['activeMenu'] = 'invitation_templates';

        return $this->render('admin/invitations/templates_index', $this->data);
    }

    public function store()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'Event tidak valid.');
        }

        $templateKey = trim((string)$this->request->getPost('template_key'));
        $name        = trim((string)$this->request->getPost('name'));
        $description = trim((string)$this->request->getPost('description'));
        $isDefault   = (int)$this->request->getPost('is_default');

        // Validation: template_key must be alphanumeric/underscore only
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $templateKey)) {
            return redirect()->back()->withInput()->with('error', 'Template Key hanya boleh berisi huruf, angka, strip, dan garis bawah.');
        }

        $templateModel = new InvitationTemplateModel();
        $exists = $templateModel->where('event_id', $eventId)->where('template_key', $templateKey)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Template Key sudah terdaftar untuk event ini.');
        }

        if ($isDefault === 1) {
            $templateModel->where('event_id', $eventId)->set(['is_default' => 0])->update();
        }

        $thumbnailUrl = trim((string)$this->request->getPost('thumbnail_url'));
        $thumbnailFile = $this->request->getFile('thumbnail_file');
        if ($thumbnailFile && $thumbnailFile->isValid() && !$thumbnailFile->hasMoved()) {
            $newName = $thumbnailFile->getRandomName();
            $thumbnailFile->move(FCPATH . 'uploads/invitations/', $newName);
            $thumbnailUrl = base_url('uploads/invitations/' . $newName);
        }

        $templateModel->insert([
            'event_id'      => $eventId,
            'template_key'  => $templateKey,
            'name'          => $name,
            'description'   => $description,
            'thumbnail_url' => $thumbnailUrl,
            'is_active'     => 1,
            'is_default'    => $isDefault,
            'created_by'    => $this->currentAdmin['id'] ?? null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/invitation-templates'))->with('success', 'Template baru berhasil ditambahkan.');
    }

    public function toggleActive($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $templateModel = new InvitationTemplateModel();
        $tpl = $templateModel->where('event_id', $eventId)->find($id);
        if (!$tpl) {
            return redirect()->back()->with('error', 'Template tidak ditemukan.');
        }

        $newStatus = $tpl['is_active'] ? 0 : 1;
        $templateModel->update($id, ['is_active' => $newStatus]);
        return redirect()->back()->with('success', 'Status keaktifan template berhasil diubah.');
    }

    public function setDefault($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $templateModel = new InvitationTemplateModel();
        $tpl = $templateModel->where('event_id', $eventId)->find($id);
        if (!$tpl) {
            return redirect()->back()->with('error', 'Template tidak ditemukan.');
        }

        $templateModel->where('event_id', $eventId)->set(['is_default' => 0])->update();
        $templateModel->update($id, ['is_default' => 1, 'is_active' => 1]);

        return redirect()->back()->with('success', 'Template default berhasil diperbarui.');
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $templateModel = new InvitationTemplateModel();
        $tpl = $templateModel->where('event_id', $eventId)->find($id);
        if (!$tpl) {
            return redirect()->back()->with('error', 'Template tidak ditemukan.');
        }
        if ($tpl['is_default'] == 1) {
            return redirect()->back()->with('error', 'Template default tidak dapat dihapus.');
        }

        $templateModel->delete($id);
        return redirect()->back()->with('success', 'Template berhasil dihapus.');
    }
}

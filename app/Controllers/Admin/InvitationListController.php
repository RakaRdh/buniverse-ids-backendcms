<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\InvitationListModel;
use App\Models\InvitationMessageModel;
use App\Models\InvitationTemplateModel;
use App\Services\EmailService;

class InvitationListController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Silakan pilih event terlebih dahulu.');
        }

        $invitationModel = new InvitationListModel();
        $templateModel   = new InvitationTemplateModel();

        // Filters
        $filterRsvp  = $this->request->getGet('rsvp_status');
        $filterBlast = $this->request->getGet('blast_status');
        $search      = trim((string)$this->request->getGet('search'));

        // Sorting per column (ASC / DESC) matching EMF pattern
        $allowedSort = ['name', 'institution', 'rsvp_status', 'blast_status', 'view_count', 'created_at'];
        $sortCol     = $this->request->getGet('sort');
        $sortDir     = strtoupper((string)$this->request->getGet('dir')) === 'ASC' ? 'ASC' : 'DESC';
        if (!in_array($sortCol, $allowedSort, true)) {
            $sortCol = 'created_at';
            $sortDir = 'DESC';
        }

        $builder = $invitationModel->where('event_id', $eventId);

        if (!empty($filterRsvp) && in_array($filterRsvp, ['pending', 'attending', 'declined'], true)) {
            $builder->where('rsvp_status', $filterRsvp);
        }

        if (!empty($filterBlast) && in_array($filterBlast, ['unsend', 'sent', 'failed'], true)) {
            $builder->where('blast_status', $filterBlast);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('institution', $search)
                ->orLike('position', $search)
                ->orLike('invitation_code', $search)
            ->groupEnd();
        }

        $guests = $builder->orderBy($sortCol, $sortDir)->findAll();

        // Summary counters
        $totalGuests    = $invitationModel->where('event_id', $eventId)->countAllResults();
        $totalAttending = $invitationModel->where('event_id', $eventId)->where('rsvp_status', 'attending')->countAllResults();
        $totalDeclined  = $invitationModel->where('event_id', $eventId)->where('rsvp_status', 'declined')->countAllResults();
        $totalPending   = $invitationModel->where('event_id', $eventId)->where('rsvp_status', 'pending')->countAllResults();
        $totalSent      = $invitationModel->where('event_id', $eventId)->where('blast_status', 'sent')->countAllResults();
        $totalUnsend    = $invitationModel->where('event_id', $eventId)->where('blast_status', 'unsend')->countAllResults();

        $activeTemplates = $templateModel->getActiveForEvent($eventId);

        $this->data['guests']          = $guests;
        $this->data['activeTemplates'] = $activeTemplates;
        $this->data['totalGuests']     = $totalGuests;
        $this->data['totalAttending']  = $totalAttending;
        $this->data['totalDeclined']   = $totalDeclined;
        $this->data['totalPending']    = $totalPending;
        $this->data['totalSent']       = $totalSent;
        $this->data['totalUnsend']     = $totalUnsend;
        $this->data['filterRsvp']      = $filterRsvp;
        $this->data['filterBlast']     = $filterBlast;
        $this->data['search']          = $search;
        $this->data['sortCol']         = $sortCol;
        $this->data['sortDir']         = $sortDir;
        $this->data['pageTitle']       = 'Guest Lists & E-Invitation';
        $this->data['activeMenu']      = 'invitations';

        return $this->render('admin/invitations/guests_index', $this->data);
    }

    public function store()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'Event tidak valid.');
        }

        $name        = trim((string)$this->request->getPost('name'));
        $email       = trim((string)$this->request->getPost('email'));
        $phone       = trim((string)$this->request->getPost('phone'));
        $institution = trim((string)$this->request->getPost('institution'));
        $position    = trim((string)$this->request->getPost('position'));
        $templateId  = $this->request->getPost('template_id') ? (int)$this->request->getPost('template_id') : null;

        if (empty($name) || empty($email)) {
            return redirect()->back()->withInput()->with('error', 'Nama dan Email wajib diisi.');
        }

        $invitationModel = new InvitationListModel();
        $tokens = InvitationListModel::generateCodeAndSlug($name, $eventId);

        $invitationModel->insert([
            'event_id'        => $eventId,
            'template_id'     => $templateId,
            'invitation_code' => $tokens['invitation_code'],
            'slug'            => $tokens['slug'],
            'name'            => $name,
            'email'           => $email,
            'phone'           => $phone,
            'institution'     => $institution,
            'position'        => $position,
            'rsvp_status'     => 'pending',
            'rsvp_pax'        => 1,
            'blast_status'    => 'unsend',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/invitations'))->with('success', 'Tamu undangan berhasil didaftarkan.');
    }

    public function update($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $invitationModel = new InvitationListModel();
        $guest = $invitationModel->where('event_id', $eventId)->find($id);
        if (!$guest) {
            return redirect()->back()->with('error', 'Tamu tidak ditemukan.');
        }

        $name        = trim((string)$this->request->getPost('name'));
        $email       = trim((string)$this->request->getPost('email'));
        $phone       = trim((string)$this->request->getPost('phone'));
        $institution = trim((string)$this->request->getPost('institution'));
        $position    = trim((string)$this->request->getPost('position'));
        $templateId  = $this->request->getPost('template_id') ? (int)$this->request->getPost('template_id') : null;

        $invitationModel->update($id, [
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'institution' => $institution,
            'position'    => $position,
            'template_id' => $templateId,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/invitations'))->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $invitationModel = new InvitationListModel();
        $guest = $invitationModel->where('event_id', $eventId)->find($id);
        if (!$guest) {
            return redirect()->back()->with('error', 'Tamu tidak ditemukan.');
        }

        $invitationModel->delete($id);
        return redirect()->to(base_url('admin/invitations'))->with('success', 'Tamu berhasil dihapus.');
    }

    /**
     * Send email to a single guest via AJAX or POST
     */
    public function sendSingleEmail($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $invitationModel = new InvitationListModel();
        $guest = $invitationModel->where('event_id', $eventId)->find($id);

        if (!$guest) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tamu tidak ditemukan.']);
        }

        $messageModel = new InvitationMessageModel();
        $msgConfig = $messageModel->getOrCreateForEvent($eventId, $this->activeEvent ?? []);

        // Base URL for visitor invitation: points to FrontendMAP port 8080 / 8082 or domain
        $frontendBaseUrl = env('app.frontendMapURL', 'http://localhost:8082');
        $invitationUrl = rtrim($frontendBaseUrl, '/') . '/invitation/' . $guest['slug'];

        $venueName = 'Grand Ballroom, Hotel Mulia Senayan Jakarta';
        $eventDate = date('d F Y', strtotime($this->activeEvent['event_date_start'] ?? '2026-11-20'));

        $searchPlaceholders = ['{name}', '{invitation_url}', '{event_title}', '{event_date}', '{venue}'];
        $replaceValues      = [
            esc($guest['name']),
            $invitationUrl,
            esc($this->activeEvent['title'] ?? 'BeritaSatu Malam Apresiasi 2026'),
            $eventDate,
            $venueName
        ];

        $subject     = str_replace($searchPlaceholders, $replaceValues, $msgConfig['subject']);
        $greeting    = str_replace($searchPlaceholders, $replaceValues, $msgConfig['greeting']);
        $bodyContent = str_replace($searchPlaceholders, $replaceValues, nl2br(esc($msgConfig['body_content'])));
        $closing     = str_replace($searchPlaceholders, $replaceValues, nl2br(esc($msgConfig['closing_text'])));
        $footerNote  = str_replace($searchPlaceholders, $replaceValues, esc($msgConfig['footer_note']));

        // Render HTML Email Template
        $htmlEmail = view('admin/invitations/email_template_preview', [
            'subject'       => $subject,
            'greeting'      => $greeting,
            'bodyContent'   => $bodyContent,
            'buttonText'    => $msgConfig['button_text'],
            'invitationUrl' => $invitationUrl,
            'closing'       => $closing,
            'footerNote'    => $footerNote,
            'event'         => $this->activeEvent,
        ]);

        $sendResult = EmailService::send($guest['email'], $subject, $htmlEmail);

        if ($sendResult['success']) {
            $invitationModel->update($id, [
                'blast_status' => 'sent',
                'blast_at'     => date('Y-m-d H:i:s'),
                'blast_error'  => null,
            ]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Email undangan berhasil dikirimkan ke ' . $guest['email']]);
        } else {
            $invitationModel->update($id, [
                'blast_status' => 'failed',
                'blast_error'  => $sendResult['error'] ?? 'Gagal mengirim email.',
            ]);
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengirim email: ' . ($sendResult['error'] ?? 'Unknown error')]);
        }
    }

    /**
     * AJAX Batch Blasting: Send email to chunk of guests
     */
    public function blastBatch()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Event tidak valid.']);
        }

        $guestIds = $this->request->getJSON(true)['guest_ids'] ?? [];
        if (empty($guestIds) || !is_array($guestIds)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada tamu yang dipilih.']);
        }

        $invitationModel = new InvitationListModel();
        $messageModel    = new InvitationMessageModel();
        $msgConfig       = $messageModel->getOrCreateForEvent($eventId, $this->activeEvent ?? []);

        $frontendBaseUrl = env('app.frontendMapURL', 'http://localhost:8082');
        $venueName = 'Grand Ballroom, Hotel Mulia Senayan Jakarta';
        $eventDate = date('d F Y', strtotime($this->activeEvent['event_date_start'] ?? '2026-11-20'));

        $processed = 0;
        $failed    = 0;

        foreach ($guestIds as $id) {
            $guest = $invitationModel->where('event_id', $eventId)->find($id);
            if (!$guest) continue;

            $invitationUrl = rtrim($frontendBaseUrl, '/') . '/invitation/' . $guest['slug'];
            $searchPlaceholders = ['{name}', '{invitation_url}', '{event_title}', '{event_date}', '{venue}'];
            $replaceValues      = [
                esc($guest['name']),
                $invitationUrl,
                esc($this->activeEvent['title'] ?? 'BeritaSatu Malam Apresiasi 2026'),
                $eventDate,
                $venueName
            ];

            $subject     = str_replace($searchPlaceholders, $replaceValues, $msgConfig['subject']);
            $greeting    = str_replace($searchPlaceholders, $replaceValues, $msgConfig['greeting']);
            $bodyContent = str_replace($searchPlaceholders, $replaceValues, nl2br(esc($msgConfig['body_content'])));
            $closing     = str_replace($searchPlaceholders, $replaceValues, nl2br(esc($msgConfig['closing_text'])));
            $footerNote  = str_replace($searchPlaceholders, $replaceValues, esc($msgConfig['footer_note']));

            $htmlEmail = view('admin/invitations/email_template_preview', [
                'subject'       => $subject,
                'greeting'      => $greeting,
                'bodyContent'   => $bodyContent,
                'buttonText'    => $msgConfig['button_text'],
                'invitationUrl' => $invitationUrl,
                'closing'       => $closing,
                'footerNote'    => $footerNote,
                'event'         => $this->activeEvent,
            ]);

            $sendResult = EmailService::send($guest['email'], $subject, $htmlEmail);

            if ($sendResult['success']) {
                $invitationModel->update($id, [
                    'blast_status' => 'sent',
                    'blast_at'     => date('Y-m-d H:i:s'),
                    'blast_error'  => null,
                ]);
                $processed++;
            } else {
                $invitationModel->update($id, [
                    'blast_status' => 'failed',
                    'blast_error'  => $sendResult['error'] ?? 'Gagal kirim email.',
                ]);
                $failed++;
            }
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'processed' => $processed,
            'failed'    => $failed,
            'message'   => "Batch selesai: {$processed} terkirim, {$failed} gagal.",
        ]);
    }

    /**
     * Export RSVP list to CSV
     */
    public function exportCsv()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $invitationModel = new InvitationListModel();
        $guests = $invitationModel->where('event_id', $eventId)->orderBy('name', 'ASC')->findAll();

        $filename = 'rekap_undangan_map_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        // BOM for Excel utf-8 compatibility
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['No', 'Kode Undangan', 'Nama Tamu', 'Email', 'No Telepon', 'Instansi', 'Jabatan', 'Status RSVP', 'Pax', 'Catatan / Ucapan', 'Status Blast', 'Waktu RSVP', 'Slug URL']);

        $no = 1;
        $frontendBaseUrl = env('app.frontendMapURL', 'http://localhost:8082');

        foreach ($guests as $g) {
            fputcsv($output, [
                $no++,
                $g['invitation_code'],
                $g['name'],
                $g['email'],
                $g['phone'],
                $g['institution'],
                $g['position'],
                strtoupper($g['rsvp_status']),
                $g['rsvp_pax'],
                $g['notes'],
                strtoupper($g['blast_status']),
                $g['rsvp_at'] ?: '—',
                rtrim($frontendBaseUrl, '/') . '/invitation/' . $g['slug'],
            ]);
        }

        fclose($output);
        exit;
    }
}

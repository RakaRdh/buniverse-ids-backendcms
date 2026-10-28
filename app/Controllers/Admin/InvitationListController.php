<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\InvitationListModel;
use App\Models\InvitationTemplateModel;

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
        $search      = trim((string)$this->request->getGet('search'));

        // Sorting per column (ASC / DESC)
        $allowedSort = ['name', 'institution', 'rsvp_status', 'view_count', 'created_at'];
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

        // Active templates for selection
        $activeTemplates = $templateModel->where('is_active', 1)->findAll();

        $this->data['guests']          = $guests;
        $this->data['totalGuests']     = $totalGuests;
        $this->data['totalAttending']  = $totalAttending;
        $this->data['totalDeclined']   = $totalDeclined;
        $this->data['totalPending']    = $totalPending;
        $this->data['activeTemplates'] = $activeTemplates;
        $this->data['filterRsvp']      = $filterRsvp;
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
        
        // Check if email already registered in this event
        $existing = $invitationModel->where('event_id', $eventId)->where('email', $email)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Email {$email} sudah terdaftar sebelumnya.");
        }

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
     * Download CSV Template for Import
     */
    public function downloadTemplateCsv()
    {
        $filename = 'template_import_tamu.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8 for Excel

        // Headers
        fputcsv($output, ['name', 'email', 'phone', 'institution', 'position']);

        // Sample Rows
        fputcsv($output, ['Budi Santoso', 'budi.santoso@example.com', '081234567890', 'Kementerian ESDM', 'Direktur']);
        fputcsv($output, ['Siti Aminah', 'siti.aminah@example.com', '085678901234', 'PT Astra International', 'Vice President']);

        fclose($output);
        exit;
    }

    /**
     * Import Guests from CSV
     * Rules:
     * - If email already exists in event: UPDATE profile (name, phone, institution, position) only.
     *   DO NOT alter invitation_code, slug, rsvp_status, rsvp_at, rsvp_notes, view_count!
     * - If email is new: INSERT and generate code & slug.
     */
    public function importCsv()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'Event tidak valid.');
        }

        $file = $this->request->getFile('csv_file');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File CSV tidak valid atau tidak ditemukan.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'txt'], true)) {
            return redirect()->back()->with('error', 'Format file harus berupa CSV (.csv).');
        }

        $filePath = $file->getTempName();
        $handle   = fopen($filePath, 'r');
        if (!$handle) {
            return redirect()->back()->with('error', 'Gagal membuka file CSV.');
        }

        // Detect delimiter: check first line for comma or semicolon
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        // Read header
        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            fclose($handle);
            return redirect()->back()->with('error', 'File CSV kosong.');
        }

        // Strip BOM and clean headers
        $cleanHeader = array_map(function ($h) {
            $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h);
            return strtolower(trim($h));
        }, $header);

        // Map column indices with broad Indonesian & English alias support
        $idxName = array_search('name', $cleanHeader, true);
        if ($idxName === false) $idxName = array_search('nama', $cleanHeader, true);
        if ($idxName === false) $idxName = array_search('nama lengkap', $cleanHeader, true);
        if ($idxName === false) $idxName = array_search('full name', $cleanHeader, true);

        $idxEmail = array_search('email', $cleanHeader, true);
        if ($idxEmail === false) $idxEmail = array_search('surel', $cleanHeader, true);
        if ($idxEmail === false) $idxEmail = array_search('e-mail', $cleanHeader, true);

        $idxPhone = array_search('phone', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('no telp', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('notelp', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('no telepon', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('telepon', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('telp', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('hp', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('no hp', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('whatsapp', $cleanHeader, true);
        if ($idxPhone === false) $idxPhone = array_search('wa', $cleanHeader, true);

        $idxInstitution = array_search('institution', $cleanHeader, true);
        if ($idxInstitution === false) $idxInstitution = array_search('institusi', $cleanHeader, true);
        if ($idxInstitution === false) $idxInstitution = array_search('instansi', $cleanHeader, true);
        if ($idxInstitution === false) $idxInstitution = array_search('perusahaan', $cleanHeader, true);
        if ($idxInstitution === false) $idxInstitution = array_search('company', $cleanHeader, true);
        if ($idxInstitution === false) $idxInstitution = array_search('organisasi', $cleanHeader, true);

        $idxPosition = array_search('position', $cleanHeader, true);
        if ($idxPosition === false) $idxPosition = array_search('jabatan', $cleanHeader, true);
        if ($idxPosition === false) $idxPosition = array_search('posisi', $cleanHeader, true);
        if ($idxPosition === false) $idxPosition = array_search('role', $cleanHeader, true);
        if ($idxPosition === false) $idxPosition = array_search('status', $cleanHeader, true); // match custom user columns

        if ($idxName === false || $idxEmail === false) {
            fclose($handle);
            return redirect()->back()->with('error', 'Header CSV wajib memiliki kolom "name" dan "email".');
        }

        $invitationModel = new InvitationListModel();
        $insertedCount   = 0;
        $updatedCount    = 0;
        $skippedCount    = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue; // Skip empty line
            }

            $name        = trim((string)($row[$idxName] ?? ''));
            $email       = trim((string)($row[$idxEmail] ?? ''));
            $phone       = ($idxPhone !== false) ? trim((string)($row[$idxPhone] ?? '')) : '';
            $institution = ($idxInstitution !== false) ? trim((string)($row[$idxInstitution] ?? '')) : '';
            $position    = ($idxPosition !== false) ? trim((string)($row[$idxPosition] ?? '')) : '';

            // Clean phone string format (ensure string, preserve leading 0 or +)
            $phone = preg_replace('/[^0-9+\-]/', '', $phone);

            if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                continue;
            }

            // Check if guest already exists by email in this event
            $existing = $invitationModel->where('event_id', $eventId)->where('email', $email)->first();

            if ($existing) {
                // Update profile info ONLY. Protect slug, code, rsvp_status, rsvp_at, rsvp_notes, view_count
                $invitationModel->update($existing['id'], [
                    'name'        => $name,
                    'phone'       => !empty($phone) ? $phone : $existing['phone'],
                    'institution' => !empty($institution) ? $institution : $existing['institution'],
                    'position'    => !empty($position) ? $position : $existing['position'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
                $updatedCount++;
            } else {
                // Insert new guest with generated unique code and slug
                $tokens = InvitationListModel::generateCodeAndSlug($name, $eventId);
                $invitationModel->insert([
                    'event_id'        => $eventId,
                    'template_id'     => null,
                    'invitation_code' => $tokens['invitation_code'],
                    'slug'            => $tokens['slug'],
                    'name'            => $name,
                    'email'           => $email,
                    'phone'           => $phone,
                    'institution'     => $institution,
                    'position'        => $position,
                    'rsvp_status'     => 'pending',
                    'rsvp_pax'        => 1,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $insertedCount++;
            }
        }

        fclose($handle);

        $msg = "Import selesai: {$insertedCount} tamu baru ditambahkan, {$updatedCount} tamu diperbarui";
        if ($skippedCount > 0) {
            $msg .= ", {$skippedCount} baris diabaikan (format nama/email tidak valid).";
        } else {
            $msg .= '.';
        }

        return redirect()->to(base_url('admin/invitations'))->with('success', $msg);
    }

    /**
     * Export Guest list with Invitation URLs to CSV
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

        // Headers
        fputcsv($output, [
            'No',
            'Kode Undangan',
            'Link Undangan (URL)',
            'Nama Tamu',
            'Email',
            'No Telepon',
            'Instansi',
            'Jabatan',
            'Status RSVP',
            'Pax',
            'Catatan / Ucapan',
            'Waktu RSVP',
            'Total Views'
        ]);

        $no = 1;

        // Base URL for invitation link: prioritizes INVITATION_BASE_URL from .env
        // Default: https://beritasatu.com/malam-apresiasi-2026/invitation/
        $invitationBaseUrl = env('INVITATION_BASE_URL', 'https://beritasatu.com/malam-apresiasi-2026/invitation/');
        $invitationBaseUrl = rtrim($invitationBaseUrl, '/') . '/';

        foreach ($guests as $g) {
            $fullInvitationUrl = $invitationBaseUrl . $g['slug'];

            fputcsv($output, [
                $no++,
                $g['invitation_code'],
                $fullInvitationUrl,
                $g['name'],
                $g['email'],
                $g['phone'] ? "=\"" . $g['phone'] . "\"" : '', // Preserve leading 0 in Excel
                $g['institution'],
                $g['position'],
                strtoupper($g['rsvp_status']),
                $g['rsvp_pax'],
                $g['notes'],
                $g['rsvp_at'] ?: '—',
                $g['view_count'],
            ]);
        }

        fclose($output);
        exit;
    }
}

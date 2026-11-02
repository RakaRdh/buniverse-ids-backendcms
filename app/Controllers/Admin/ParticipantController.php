<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\ParticipantModel;

class ParticipantController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event first.');
        }

        $participantModel = new ParticipantModel();
        
        $filterStatus = $this->request->getGet('status');
        $filterCheckedIn = $this->request->getGet('is_checked_in');
        $search = trim((string)$this->request->getGet('search'));

        $builder = $participantModel->where('event_id', $eventId);

        if (!empty($filterStatus)) {
            $builder->where('status', $filterStatus);
        }

        if ($filterCheckedIn !== null && $filterCheckedIn !== '') {
            $builder->where('is_checked_in', (int)$filterCheckedIn);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('full_name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('company_name', $search)
                ->orLike('company_email', $search)
                ->orLike('ticket_code', $search)
            ->groupEnd();
        }

        $participants = $builder->orderBy('id', 'DESC')->findAll();

        // Summary counts
        $totalRegistered = $participantModel->where('event_id', $eventId)->where('status', 'confirmed')->countAllResults();
        $totalCheckedIn  = $participantModel->where('event_id', $eventId)->where('status', 'confirmed')->where('is_checked_in', 1)->countAllResults();
        $unattended      = max(0, $totalRegistered - $totalCheckedIn);

        $this->data['participants']    = $participants;
        $this->data['totalRegistered'] = $totalRegistered;
        $this->data['totalCheckedIn']  = $totalCheckedIn;
        $this->data['unattended']      = $unattended;
        $this->data['filterStatus']    = $filterStatus;
        $this->data['filterCheckedIn'] = $filterCheckedIn;
        $this->data['search']          = $search;
        $this->data['pageTitle']       = 'Participant Management';
        $this->data['activeMenu']      = 'participants';

        return $this->render('admin/participants/index', $this->data);
    }

    public function checkin($id)
    {
        $participantModel = new ParticipantModel();
        $participant = $participantModel->find($id);
        if (!$participant) {
            return redirect()->back()->with('error', 'Participant not found.');
        }

        $newCheckedIn = ((int)$participant['is_checked_in'] === 1) ? 0 : 1;
        $participantModel->update($id, [
            'is_checked_in' => $newCheckedIn,
            'checked_in_at' => $newCheckedIn ? date('Y-m-d H:i:s') : null,
            'checked_in_by' => $newCheckedIn ? ($this->currentAdmin['id'] ?? null) : null,
        ]);

        $statusMsg = $newCheckedIn ? 'Participant marked as visited/checked-in.' : 'Check-in status removed.';
        return redirect()->back()->with('success', $statusMsg);
    }

    public function delete($id)
    {
        // Only superadmin and editor can delete
        if ($this->currentAdmin['role'] === 'receptionist') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $participantModel = new ParticipantModel();
        $participantModel->delete($id);
        return redirect()->back()->with('success', 'Participant record deleted.');
    }

    public function export()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event first.');
        }

        $participantModel = new ParticipantModel();
        $participants = $participantModel->where('event_id', $eventId)->orderBy('id', 'ASC')->findAll();

        $filename = 'participants_' . ($this->activeEvent['slug'] ?? 'event') . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        // BOM for Excel UTF-8
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'ID',
            'Ticket Code',
            'Full Name',
            'Email',
            'Phone',
            'Address',
            'Job Title',
            'Company / Instansi',
            'Company Email',
            'PIC Name',
            'PIC Email',
            'PIC Phone',
            'Source Info',
            'Status',
            'Visited / Checked In',
            'Checked In At',
            'Registered At',
        ]);

        foreach ($participants as $p) {
            fputcsv($output, [
                $p['id'],
                $p['ticket_code'],
                $p['full_name'],
                $p['email'],
                $p['phone'],
                $p['address'],
                $p['job_title'],
                $p['company_name'],
                $p['company_email'] ?? '',
                $p['pic_name'],
                $p['pic_email'],
                $p['pic_phone'],
                $p['source_info'],
                $p['status'],
                $p['is_checked_in'] ? 'YES' : 'NO',
                $p['checked_in_at'],
                $p['created_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}

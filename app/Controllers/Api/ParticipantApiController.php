<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\ParticipantModel;
use CodeIgniter\API\ResponseTrait;

class ParticipantApiController extends BaseController
{
    use ResponseTrait;

    protected function getActiveEvent()
    {
        $eventModel = new EventModel();
        
        $slug = $this->request->getGet('event_slug') 
            ?? $this->request->getPost('event_slug');
        if (empty($slug)) {
            $rawBody = $this->request->getBody();
            if (!empty($rawBody)) {
                $json = json_decode($rawBody, true);
                if (!empty($json['event_slug'])) {
                    $slug = $json['event_slug'];
                }
            }
        }

        if (!empty($slug)) {
            $event = $eventModel->where('slug', $slug)->first();
            if ($event) {
                return $event;
            }
        }

        return $eventModel->getActiveEvent();
    }

    /**
     * Public Registration Endpoint
     */
    public function register()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('Event not found or not active.');
        }

        $rawBody = $this->request->getBody();
        $json = (!empty($rawBody) && is_string($rawBody)) ? json_decode($rawBody, true) : [];
        if (!is_array($json)) {
            $json = [];
        }

        // 1. Ambil data input
        $fullName       = trim(strip_tags((string)($json['full_name'] ?? $this->request->getPost('full_name') ?? '')));
        $email          = trim(strip_tags((string)($json['email'] ?? $this->request->getPost('email') ?? '')));
        $phone          = trim(strip_tags((string)($json['phone'] ?? $this->request->getPost('phone') ?? '')));
        $address        = trim(strip_tags((string)($json['address'] ?? $this->request->getPost('address') ?? '')));
        $jobTitle       = trim(strip_tags((string)($json['job_title'] ?? $this->request->getPost('job_title') ?? '')));
        $companyName    = trim(strip_tags((string)($json['company_name'] ?? $this->request->getPost('company_name') ?? '')));
        $visitDay       = trim(strip_tags((string)($json['visit_day'] ?? $this->request->getPost('visit_day') ?? 'all_days')));
        $ticketQuantity = (int)($json['ticket_quantity'] ?? $this->request->getPost('ticket_quantity') ?? 1);
        if ($ticketQuantity < 1) $ticketQuantity = 1;
        if ($ticketQuantity > 10) $ticketQuantity = 10;
        
        // Opsional
        $picName      = trim(strip_tags((string)($json['pic_name'] ?? $this->request->getPost('pic_name') ?? '')));
        $picEmail     = trim(strip_tags((string)($json['pic_email'] ?? $this->request->getPost('pic_email') ?? '')));
        $picPhone     = trim(strip_tags((string)($json['pic_phone'] ?? $this->request->getPost('pic_phone') ?? '')));
        $sourceInfo   = trim(strip_tags((string)($json['source_info'] ?? $this->request->getPost('source_info') ?? '')));

        // 2. Validasi required fields
        if (empty($fullName) || empty($email) || empty($phone) || empty($address) || empty($jobTitle) || empty($companyName)) {
            return $this->failValidationErrors('Semua kolom wajib (Nama, Email, No Telp, Alamat, Jabatan, Instansi) harus diisi.');
        }

        if (mb_strlen($fullName) < 3) {
            return $this->failValidationErrors('Nama lengkap minimal harus terdiri dari 3 karakter.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->failValidationErrors('Format email tidak valid (contoh: nama@domain.com).');
        }

        $phoneCheck = \App\Helpers\PhoneHelper::validate($phone);
        if (!$phoneCheck['valid']) {
            return $this->failValidationErrors($phoneCheck['message']);
        }
        $phone = $phoneCheck['normalized'];

        $validVisitDays = ['day_1', 'day_2', 'all_days'];
        if (!in_array($visitDay, $validVisitDays)) {
            $visitDay = 'all_days';
        }

        $db = \Config\Database::connect();
        $participantModel = new ParticipantModel();

        // 3. Database Transaction & Quota Check (Anti Race-Condition dengan Pessimistic Row Lock)
        $db->transStart();

        // Lock row event dengan SELECT ... FOR UPDATE agar request konkuren antre dan tidak overselling
        $lockedEvent = $db->query('SELECT id, quota, title FROM events WHERE id = ? FOR UPDATE', [(int)$event['id']])->getRowArray();

        // Cek kuota jika ada setting quota > 0 (memperhitungkan jumlah tiket yang dipesan)
        if (!empty($lockedEvent['quota']) && (int)$lockedEvent['quota'] > 0) {
            $currentCount = $participantModel->where('event_id', $event['id'])
                ->where('status', 'confirmed')
                ->countAllResults();

            if (($currentCount + $ticketQuantity) > (int)$lockedEvent['quota']) {
                $db->transRollback();
                $sisaKuota = max(0, (int)$lockedEvent['quota'] - $currentCount);
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Mohon maaf, kuota tiket tidak mencukupi untuk ' . $ticketQuantity . ' tiket (Sisa kuota: ' . $sisaKuota . ').',
                ]);
            }
        }

        $autoCheckin = (int)($json['auto_checkin'] ?? $this->request->getPost('auto_checkin') ?? 0);
        $checkinNote = trim((string)($json['checkin_note'] ?? $this->request->getPost('checkin_note') ?? 'Registrasi On-The-Spot (OTS) Gate - Langsung Hadir'));

        $orderId = 'EMF-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $createdParticipants = [];
        $nowDateTime = date('Y-m-d H:i:s');
        $todayDate = date('Y-m-d');

        for ($i = 1; $i <= $ticketQuantity; $i++) {
            // Generate 16 digit ticket code unik per tiket
            $ticketCode = $participantModel->generateUniqueTicketCode((int)$event['id']);

            $ticketFullName = ($ticketQuantity > 1) ? $fullName . ' (Tiket #' . $i . ')' : $fullName;

            $participantData = [
                'event_id'         => (int)$event['id'],
                'ticket_code'      => $ticketCode,
                'order_id'         => $orderId,
                'ticket_index'     => $i,
                'participant_type' => 'Visitor',
                'visit_day'        => $visitDay,
                'full_name'        => $ticketFullName,
                'email'            => $email,
                'phone'            => $phone,
                'mobile_phone'     => $phone,
                'address'          => $address,
                'job_title'        => $jobTitle,
                'company_name'     => $companyName,
                'pic_name'         => $picName ?: null,
                'pic_email'        => $picEmail ?: null,
                'pic_phone'        => $picPhone ?: null,
                'source_info'      => $sourceInfo ?: null,
                'status'           => 'confirmed',
                'is_checked_in'    => ($autoCheckin === 1) ? 1 : 0,
                'checked_in_at'    => ($autoCheckin === 1) ? $nowDateTime : null,
                'created_at'       => $nowDateTime,
                'updated_at'       => $nowDateTime,
            ];

            $participantId = $participantModel->insert($participantData);

            // Jika OTS (auto_checkin = 1), insert juga ke tabel participant_checkins
            if ($autoCheckin === 1 && $participantId) {
                $db->table('participant_checkins')->insert([
                    'participant_id'  => (int)$participantId,
                    'event_id'        => (int)$event['id'],
                    'checkin_date'    => $todayDate,
                    'checkin_time'    => $nowDateTime,
                    'gate_officer_id' => null,
                    'note'            => $checkinNote,
                    'created_at'      => $nowDateTime,
                ]);
            }

            $createdParticipants[] = [
                'id'           => $participantId,
                'full_name'    => $ticketFullName,
                'email'        => $email,
                'company_name' => $companyName,
                'job_title'    => $jobTitle,
                'ticket_code'  => $ticketCode,
                'ticket_index' => $i,
                'visit_day'    => $visitDay,
            ];
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->failServerError('Gagal memproses pendaftaran. Silakan coba beberapa saat lagi.');
        }

        return $this->respondCreated([
            'success'          => true,
            'message'          => 'Pendaftaran ' . $ticketQuantity . ' tiket berhasil diproses.',
            'order_id'         => $orderId,
            'ticket_quantity'  => $ticketQuantity,
            'visit_day'        => $visitDay,
            'ticket_code'      => $createdParticipants[0]['ticket_code'], // Primary ticket
            'tickets'          => $createdParticipants,
            'participant'      => [
                'full_name'    => $fullName,
                'email'        => $email,
                'company_name' => $companyName,
                'job_title'    => $jobTitle,
                'event_title'  => $event['title'] ?? 'Energi & Mineral Festival 2026',
            ],
        ]);
    }

    /**
     * Verify Gate Passcode
     */
    public function verifyPasscode()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('Event not found.');
        }

        $rawBody = $this->request->getBody();
        $json = (!empty($rawBody) && is_string($rawBody)) ? json_decode($rawBody, true) : [];
        $inputPasscode = trim((string)($json['passcode'] ?? $this->request->getPost('passcode') ?? ''));

        $correctPasscode = !empty($event['gate_passcode']) ? (string)$event['gate_passcode'] : '123456';

        if ($inputPasscode === $correctPasscode) {
            return $this->respond([
                'success' => true,
                'message' => 'Passcode valid.',
                'event_id' => $event['id'],
                'event_title' => $event['title'],
            ]);
        }

        return $this->response->setStatusCode(401)->setJSON([
            'success' => false,
            'message' => 'Passcode 6-digit salah.',
        ]);
    }

    /**
     * Check-in Scan Endpoint
     */
    public function checkin()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('Event not found.');
        }

        $rawBody = $this->request->getBody();
        $json = (!empty($rawBody) && is_string($rawBody)) ? json_decode($rawBody, true) : [];
        
        $ticketCode = trim((string)($json['ticket_code'] ?? $this->request->getPost('ticket_code') ?? ''));
        $passcode   = trim((string)($json['passcode'] ?? $this->request->getPost('passcode') ?? ''));

        // Validate passcode or admin session
        $correctPasscode = !empty($event['gate_passcode']) ? (string)$event['gate_passcode'] : '123456';
        $isAdminLogged = (bool)session()->get('admin_logged_in');

        if (!$isAdminLogged && $passcode !== $correctPasscode) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Passcode tidak valid.',
            ]);
        }

        if (empty($ticketCode)) {
            return $this->failValidationErrors('Kode tiket tidak boleh kosong.');
        }

        $participantModel = new ParticipantModel();
        $participant = $participantModel->where('event_id', $event['id'])
            ->where('ticket_code', $ticketCode)
            ->first();

        if (!$participant) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'status'  => 'not_found',
                'message' => 'Tiket dengan kode tersebut tidak terdaftar untuk event ini.',
            ]);
        }

        if ((int)$participant['is_checked_in'] === 1) {
            return $this->response->setStatusCode(409)->setJSON([
                'success'        => false,
                'status'         => 'already_checked_in',
                'message'        => 'Tiket sudah pernah digunakan sebelumnya!',
                'checked_in_at'  => $participant['checked_in_at'] ? date('H:i:s, d M Y', strtotime($participant['checked_in_at'])) : '—',
                'participant'    => [
                    'full_name'    => $participant['full_name'],
                    'company_name' => $participant['company_name'],
                    'job_title'    => $participant['job_title'],
                    'ticket_code'  => $participant['ticket_code'],
                ],
            ]);
        }

        $adminId = session()->get('admin_id') ?: null;
        $now = date('Y-m-d H:i:s');

        $participantModel->update($participant['id'], [
            'is_checked_in' => 1,
            'checked_in_at' => $now,
            'checked_in_by' => $adminId,
        ]);

        return $this->respond([
            'success'       => true,
            'status'        => 'success',
            'message'       => 'Check-in berhasil!',
            'checked_in_at' => date('H:i:s, d M Y', strtotime($now)),
            'participant'   => [
                'full_name'    => $participant['full_name'],
                'company_name' => $participant['company_name'],
                'job_title'    => $participant['job_title'],
                'ticket_code'  => $participant['ticket_code'],
                'email'        => $participant['email'],
                'phone'        => $participant['phone'],
            ],
        ]);
    }

    /**
     * Search participant (Instant Search Fallback)
     */
    public function search()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('Event not found.');
        }

        $query = trim((string)$this->request->getGet('q'));
        $participantModel = new ParticipantModel();

        if (empty($query) || $query === 'all') {
            $results = $participantModel->where('event_id', $event['id'])
                ->orderBy('is_checked_in', 'ASC')
                ->orderBy('id', 'DESC')
                ->limit(50)
                ->findAll();

            return $this->respond([
                'success' => true,
                'data'    => $results,
            ]);
        }

        if (mb_strlen($query) < 2) {
            return $this->respond(['data' => []]);
        }

        $results = $participantModel->where('event_id', $event['id'])
            ->groupStart()
                ->like('full_name', $query)
                ->orLike('email', $query)
                ->orLike('phone', $query)
                ->orLike('ticket_code', $query)
                ->orLike('company_name', $query)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->limit(30)
            ->findAll();

        return $this->respond([
            'success' => true,
            'data'    => $results,
        ]);
    }

    /**
     * Get Realtime Presence Stats
     */
    public function stats()
    {
        $event = $this->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('Event not found.');
        }

        $participantModel = new ParticipantModel();
        $totalRegistered = $participantModel->where('event_id', $event['id'])->where('status', 'confirmed')->countAllResults();
        $totalCheckedIn  = $participantModel->where('event_id', $event['id'])->where('status', 'confirmed')->where('is_checked_in', 1)->countAllResults();
        $unattended      = max(0, $totalRegistered - $totalCheckedIn);

        return $this->respond([
            'success'          => true,
            'quota'            => (int)($event['quota'] ?? 0),
            'total_registered' => $totalRegistered,
            'total_checked_in' => $totalCheckedIn,
            'unattended'       => $unattended,
        ]);
    }
}

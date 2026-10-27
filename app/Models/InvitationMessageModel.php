<?php

namespace App\Models;

use CodeIgniter\Model;

class InvitationMessageModel extends Model
{
    protected $table            = 'invitation_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'subject',
        'greeting',
        'body_content',
        'button_text',
        'closing_text',
        'footer_note',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getOrCreateForEvent(int $eventId, array $eventData = []): array
    {
        $existing = $this->where('event_id', $eventId)->first();
        if ($existing) {
            return $existing;
        }

        $defaultBody = "Dengan hormat,\n\nKami mengundang Bapak/Ibu untuk menghadiri perhelatan istimewa {event_title} yang akan diselenggarakan pada:\n\nHari / Tanggal: {event_date}\nWaktu: Pukul 18.30 WIB s/d Selesai\nTempat: {venue}\n\nSilakan klik tombol di bawah ini untuk membuka undangan digital resmi dan melakukan konfirmasi kehadiran (RSVP).";

        $defaultData = [
            'event_id'     => $eventId,
            'subject'      => 'Undangan Resmi ' . ($eventData['title'] ?? 'BeritaSatu Malam Apresiasi 2026'),
            'greeting'     => 'Kepada Yth. {name}',
            'body_content' => $defaultBody,
            'button_text'  => 'Buka Undangan Digital',
            'closing_text' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu berkenan hadir memenuhi undangan ini.',
            'footer_note'  => 'Panitia Pelaksana ' . ($eventData['title'] ?? 'BeritaSatu Malam Apresiasi 2026'),
        ];

        $id = $this->insert($defaultData, true);
        return $this->find($id) ?: $defaultData;
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventHeroImageModel;
use App\Models\EventModel;
use App\Models\EventOverviewModel;
use App\Models\EventSeoModel;

class MetaController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event first.');
        }

        $seoModel       = new EventSeoModel();
        $eventModel     = new EventModel();
        $overviewModel  = new EventOverviewModel();
        $heroImageModel = new EventHeroImageModel();

        $event    = $eventModel->find($eventId);
        $overview = $overviewModel->getByEventId($eventId);
        $seo      = $seoModel->getByEventId($eventId);
        $heroImg  = $heroImageModel->where('event_id', $eventId)->orderBy('sort_order', 'ASC')->first();

        // Fallback calculations according to BackendCMS Brief:
        // COALESCE(event_seo.meta_title, events.title)
        $effectiveTitle = !empty($seo['meta_title']) ? $seo['meta_title'] : ($event['title'] ?? $event['name'] ?? 'Investor Daily Summit');
        
        // COALESCE(event_seo.meta_description, event_overview.intro_text, event_overview.body_text)
        $effectiveDesc = !empty($seo['meta_description']) 
            ? $seo['meta_description'] 
            : (!empty($overview['intro_text']) ? $overview['intro_text'] : ($overview['body_text'] ?? 'Indonesia\'s premier sovereign-economic forum.'));
        
        // COALESCE(event_seo.og_image_url, event_hero_images.image_url)
        $effectiveOg = !empty($seo['og_image_url']) ? $seo['og_image_url'] : ($heroImg['image_url'] ?? 'assets/img/og_default.jpg');

        $this->data['seo']            = $seo;
        $this->data['event']          = $event;
        $this->data['overview']       = $overview;
        $this->data['effectiveTitle'] = $effectiveTitle;
        $this->data['effectiveDesc']  = $effectiveDesc;
        $this->data['effectiveOg']    = $effectiveOg;
        $this->data['pageTitle']      = 'Meta & SEO Configuration';
        $this->data['activeMenu']     = 'meta';

        return $this->render('admin/meta/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $seoModel = new EventSeoModel();
        $userId   = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $seoData = [
            'meta_title'       => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'updated_by'       => $userId,
        ];

        // Handle OG image file upload
        $ogFile = $this->request->getFile('og_image_file');
        
        // Cek apakah user memilih file untuk di-upload
        if ($ogFile && $ogFile->getError() !== UPLOAD_ERR_NO_FILE) {
            // 1. Cek error upload bawaan PHP (misal: upload_max_filesize di php.ini prod)
            if (!$ogFile->isValid()) {
                $errCode = $ogFile->getError();
                $errString = $ogFile->getErrorString();
                log_message('error', "[MetaController] Upload file invalid untuk event {$eventId}. Code: {$errCode}, Message: {$errString}");
                
                $customMsg = "Gagal mengunggah file: {$errString}";
                if ($errCode === UPLOAD_ERR_INI_SIZE) {
                    $iniLimit = ini_get('upload_max_filesize');
                    $customMsg = "Ukuran file melebihi batas 'upload_max_filesize' di php.ini server ({$iniLimit}). Silakan kompres gambar atau naikkan limit php.ini di server.";
                } elseif ($errCode === UPLOAD_ERR_FORM_SIZE) {
                    $customMsg = "Ukuran file melebihi batas form MAX_FILE_SIZE.";
                } elseif ($errCode === UPLOAD_ERR_PARTIAL) {
                    $customMsg = "File hanya terunggah sebagian. Silakan coba lagi.";
                } elseif ($errCode === UPLOAD_ERR_NO_TMP_DIR) {
                    $customMsg = "Folder sementara (temp directory) PHP tidak ditemukan di server.";
                } elseif ($errCode === UPLOAD_ERR_CANT_WRITE) {
                    $customMsg = "Server gagal menulis file ke disk. Periksa permission folder server.";
                }
                return redirect()->back()->withInput()->with('error', $customMsg);
            }

            // 2. Validasi rules CodeIgniter
            if (!$this->validate([
                'og_image_file' => [
                    'rules'  => 'is_image[og_image_file]|mime_in[og_image_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[og_image_file,8192]',
                    'errors' => [
                        'is_image' => 'File yang diunggah harus berupa gambar yang valid.',
                        'mime_in'  => 'Hanya format JPG, JPEG, PNG, dan WebP yang diperbolehkan.',
                        'max_size' => 'Ukuran gambar tidak boleh melebihi 8MB.',
                    ],
                ],
            ])) {
                $valError = $this->validator->getError('og_image_file');
                log_message('error', "[MetaController] Validasi gagal untuk event {$eventId}: {$valError}");
                return redirect()->back()->withInput()->with('error', $valError);
            }

            $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/seo';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                    log_message('error', "[MetaController] Gagal membuat direktori: {$uploadDir}");
                    return redirect()->back()->withInput()->with('error', 'Gagal membuat folder penyimpanan di server: ' . $uploadDir);
                }
            }

            try {
                $newName = $ogFile->getRandomName();
                $ogFile->move($uploadDir, $newName);
                $seoData['og_image_url'] = 'uploads/events/' . $eventId . '/seo/' . $newName;
                log_message('info', "[MetaController] Sukses upload OG Image event {$eventId}: {$seoData['og_image_url']}");
            } catch (\Throwable $e) {
                log_message('error', "[MetaController] Exception saat move file event {$eventId}: " . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal memindahkan file ke server: ' . $e->getMessage());
            }
        }

        $existing = $seoModel->getByEventId($eventId);
        if ($existing) {
            $seoModel->update($existing['id'], $seoData);
            log_message('info', "[MetaController] SEO record updated for event {$eventId} (ID: {$existing['id']})");
        } else {
            $seoData['event_id']   = $eventId;
            $seoData['created_by'] = $userId;
            $seoModel->insert($seoData);
            log_message('info', "[MetaController] New SEO record created for event {$eventId}");
        }

        return redirect()->back()->with('success', 'SEO & Meta tags updated successfully.');
    }
}

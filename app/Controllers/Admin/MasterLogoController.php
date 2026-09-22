<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventLogoModel;
use App\Models\SectionLogoPlacementModel;

class MasterLogoController extends BaseAdminController
{
    protected array $slots = [
        'navbar_slot_1'   => [
            'label'       => 'Navbar Slot 1 (Summit Event Logo)',
            'description' => 'Displayed on the left side of the sticky navbar (color & white versions supported).',
            'section'     => 'Header / Navbar',
            'default_url' => '#home',
        ],
        'navbar_slot_2'   => [
            'label'       => 'Navbar Slot 2 (Media Partner / Investor Daily)',
            'description' => 'Displayed beside Logo 1 separated by a vertical divider.',
            'section'     => 'Header / Navbar',
            'default_url' => 'https://investor.id',
        ],
        'overview'        => [
            'label'       => 'Overview / About Us Section Logo',
            'description' => 'Displayed on the left column above the editorial introductory text.',
            'section'     => 'About Us / Overview',
            'default_url' => '#home',
        ],
        'floating_bottom' => [
            'label'       => 'Floating Bottom Bar Logo',
            'description' => 'Displayed on the left side of the sticky floating registration bar.',
            'section'     => 'Floating Registration Bar',
            'default_url' => '#home',
        ],
        'footer'          => [
            'label'       => 'Footer Brand Logo',
            'description' => 'Displayed on the left column of the footer above social media icons.',
            'section'     => 'Footer',
            'default_url' => 'https://investor.id',
        ],
    ];

    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $logoModel = new EventLogoModel();
        $placementModel = new SectionLogoPlacementModel();

        $logos = $logoModel->getByEvent($eventId);
        $placementsRaw = $placementModel->where('event_id', $eventId)->findAll();

        $placements = [];
        foreach ($placementsRaw as $pr) {
            $placements[$pr['slot_key']] = $pr;
        }

        $this->data['logos']       = $logos;
        $this->data['placements']  = $placements;
        $this->data['slots']       = $this->slots;
        $this->data['pageTitle']   = 'Master Logos & Placements';
        $this->data['activeMenu']  = 'master_logos';

        return $this->render('admin/master_logos/index', $this->data);
    }

    public function saveLogo()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $logoModel = new EventLogoModel();
        $id = (int)$this->request->getPost('id');
        $existing = $id ? $logoModel->find($id) : null;

        $name = trim($this->request->getPost('name') ?: 'Brand Logo');
        $defaultTargetUrl = trim($this->request->getPost('default_target_url') ?: '');

        $logoUrl = $existing['logo_url'] ?? '';
        $logoWhiteUrl = $existing['logo_white_url'] ?? null;

        $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/logos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Color logo
        $logoFile = $this->request->getFile('logo_file');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $newName = $logoFile->getRandomName();
            $logoFile->move($uploadDir, $newName);
            $logoUrl = 'uploads/events/' . $eventId . '/logos/' . $newName;
        }

        // White logo
        $logoWhiteFile = $this->request->getFile('logo_white_file');
        if ($logoWhiteFile && $logoWhiteFile->isValid() && !$logoWhiteFile->hasMoved()) {
            $newName = $logoWhiteFile->getRandomName();
            $logoWhiteFile->move($uploadDir, $newName);
            $logoWhiteUrl = 'uploads/events/' . $eventId . '/logos/' . $newName;
        }

        if (empty($logoUrl)) {
            return redirect()->back()->with('error', 'Logo color image is required.');
        }

        $data = [
            'event_id'           => $eventId,
            'name'               => $name,
            'logo_url'           => $logoUrl,
            'logo_white_url'     => $logoWhiteUrl ?: null,
            'default_target_url' => $defaultTargetUrl ?: null,
        ];

        if ($existing) {
            $logoModel->update($id, $data);
            $msg = 'Logo updated successfully.';
        } else {
            $logoModel->insert($data);
            $msg = 'Logo added to repository.';
        }

        return redirect()->to(base_url('admin/master-logos'))->with('success', $msg);
    }

    public function deleteLogo($id)
    {
        $logoModel = new EventLogoModel();
        $record = $logoModel->find($id);
        if ($record) {
            if (!empty($record['logo_url']) && str_starts_with($record['logo_url'], 'uploads/') && file_exists(FCPATH . $record['logo_url'])) {
                @unlink(FCPATH . $record['logo_url']);
            }
            if (!empty($record['logo_white_url']) && str_starts_with($record['logo_white_url'], 'uploads/') && file_exists(FCPATH . $record['logo_white_url'])) {
                @unlink(FCPATH . $record['logo_white_url']);
            }
            $logoModel->delete($id);
        }

        return redirect()->to(base_url('admin/master-logos'))->with('success', 'Logo removed from repository.');
    }

    public function savePlacements()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $placementModel = new SectionLogoPlacementModel();
        $slotLogos = $this->request->getPost('slot_logo') ?: [];
        $slotVariants = $this->request->getPost('slot_variant') ?: [];
        $slotUrls = $this->request->getPost('slot_url') ?: [];
        $slotActive = $this->request->getPost('slot_active') ?: [];

        foreach ($this->slots as $slotKey => $slotDef) {
            $logoId = !empty($slotLogos[$slotKey]) ? (int)$slotLogos[$slotKey] : null;
            $variant = $slotVariants[$slotKey] ?? 'auto';
            $targetUrl = !empty($slotUrls[$slotKey]) ? trim($slotUrls[$slotKey]) : ($slotDef['default_url'] ?? null);
            $isActive = isset($slotActive[$slotKey]) ? 1 : 0;

            $placementModel->upsertPlacement($eventId, $slotKey, $logoId, $variant, $targetUrl, $isActive);
        }

        return redirect()->to(base_url('admin/master-logos'))->with('success', 'Section logo placements updated.');
    }
}

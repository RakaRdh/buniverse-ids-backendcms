<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventSettingModel;
use App\Models\SponsorModel;
use App\Models\SponsorPartnershipModel;
use App\Models\SponsorTierModel;

class DedicatedSponsorController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $tierModel    = new SponsorTierModel();
        $sponsorModel = new SponsorModel();
        $settingModel = new EventSettingModel();

        $tiers = $tierModel->where('event_id', $eventId)->orderBy('sort_order', 'ASC')->findAll();
        foreach ($tiers as &$tier) {
            $tier['sponsors'] = $sponsorModel->where('tier_id', $tier['id'])->orderBy('sort_order', 'ASC')->findAll();
        }

        $settings = $settingModel->getByEventId($eventId);
        $sponsorDisplayMode = $settings['sponsor_display_mode'] ?? 'dedicated';

        $this->data['tiers']              = $tiers;
        $this->data['sponsorDisplayMode'] = $sponsorDisplayMode;
        $this->data['pageTitle']          = 'Dedicated Sponsors';
        $this->data['activeMenu']         = 'dedicated_sponsors';

        return $this->render('admin/dedicated_sponsors/index', $this->data);
    }

    public function switchMode()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $mode = $this->request->getPost('sponsor_display_mode');
        if (!in_array($mode, ['dedicated', 'package'])) {
            return redirect()->back()->with('error', 'Invalid sponsor mode.');
        }

        $settingModel = new EventSettingModel();
        $settings = $settingModel->getByEventId($eventId);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        if ($settings) {
            $settingModel->update($settings['id'], [
                'sponsor_display_mode' => $mode,
                'updated_by'           => $userId,
            ]);
        } else {
            $settingModel->insert([
                'event_id'             => $eventId,
                'sponsor_display_mode' => $mode,
                'created_by'           => $userId,
                'updated_by'           => $userId,
            ]);
        }

        // Mutually exclusive validation:
        // If 'dedicated', deactivate single package banner.
        // If 'package', activate single package banner.
        $partnershipModel = new SponsorPartnershipModel();
        $existingBanner = $partnershipModel->getByEvent($eventId);
        if ($existingBanner) {
            $partnershipModel->update($existingBanner['id'], [
                'is_active'  => ($mode === 'package') ? 1 : 0,
                'updated_by' => $userId,
            ]);
        }

        $modeLabel = ($mode === 'dedicated') ? 'Dedicated Sponsors (Tier-Based)' : 'Single Package Banner';
        return redirect()->back()->with('success', "Active Sponsor Mode switched to: <strong>{$modeLabel}</strong>.");
    }

    public function saveTier()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $tierModel = new SponsorTierModel();
        $count = $tierModel->where('event_id', $eventId)->countAllResults();

        $groupKey = $this->request->getPost('group_key') ?: 'sponsor';
        if ($groupKey === 'partnerships' || $groupKey === 'partner') {
            $groupLabel = 'PARTNERSHIP :';
        } elseif ($groupKey === 'collaboration' || $groupKey === 'collaborations') {
            $groupLabel = 'COLLABORATION WITH :';
        } else {
            $groupLabel = 'SPONSORED BY :';
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $tierModel->insert([
            'event_id'    => $eventId,
            'name'        => $this->request->getPost('name'),
            'group_key'   => $groupKey,
            'group_label' => $this->request->getPost('group_label') ?: $groupLabel,
            'logo_size'   => $this->request->getPost('logo_size') ?: 'md',
            'sort_order'  => (int)$this->request->getPost('sort_order') ?: ($count + 1),
            'created_by'  => $userId,
            'updated_by'  => $userId,
        ]);

        return redirect()->back()->with('success', 'Sponsor tier added.');
    }

    public function deleteTier($id)
    {
        $tierModel = new SponsorTierModel();
        $tierModel->delete($id);
        return redirect()->back()->with('success', 'Tier deleted.');
    }

    public function saveSponsor()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $sponsorModel = new SponsorModel();
        $tierId = (int)$this->request->getPost('tier_id');
        $count  = $sponsorModel->where('event_id', $eventId)->where('tier_id', $tierId)->countAllResults();
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        $data = [
            'event_id'   => $eventId,
            'tier_id'    => $tierId,
            'name'       => $this->request->getPost('name'),
            'link_url'   => $this->request->getPost('link_url') ?: $this->request->getPost('website_url'),
            'sort_order' => (int)$this->request->getPost('sort_order') ?: ($count + 1),
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        // Handle logo file upload
        $logo = $this->request->getFile('logo_file');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $targetDir = FCPATH . 'uploads/events/' . $eventId . '/sponsors';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $logo->move($targetDir, $newName);
            $data['logo_url'] = 'uploads/events/' . $eventId . '/sponsors/' . $newName;
        } else {
            // Default logo fallback
            $data['logo_url'] = 'assets/img/sponsors.webp';
        }

        $sponsorModel->insert($data);
        return redirect()->back()->with('success', 'Sponsor logo added.');
    }

    public function deleteSponsor($id)
    {
        $sponsorModel = new SponsorModel();
        $sponsorModel->delete($id);
        return redirect()->back()->with('success', 'Sponsor removed.');
    }
}

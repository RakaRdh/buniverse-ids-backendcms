<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\SponsorPartnershipModel;
/*
use App\Models\SponsorModel;
use App\Models\SponsorTierModel;
*/

class SponsorController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $model = new SponsorPartnershipModel();
        $sponsorPartnership = $model->getByEvent($eventId);

        $this->data['sponsorPartnership'] = $sponsorPartnership;
        $this->data['pageTitle']          = 'Sponsors & Partnerships';
        $this->data['activeMenu']         = 'sponsors';

        return $this->render('admin/sponsors/index', $this->data);
    }

    public function save()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $model = new SponsorPartnershipModel();
        $existing = $model->getByEvent($eventId);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $name = $this->request->getPost('name');
        $logoUrl = $existing['logo_url'] ?? '';

        // Handle image file upload
        $logoFile = $this->request->getFile('logo_file');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $newName = $logoFile->getRandomName();
            $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/sponsors';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $logoFile->move($uploadDir, $newName);
            $logoUrl = 'uploads/events/' . $eventId . '/sponsors/' . $newName;
        }

        if (empty($logoUrl)) {
            return redirect()->back()->with('error', 'Please upload a sponsors & partnerships image.');
        }

        $data = [
            'event_id'   => $eventId,
            'name'       => $name,
            'logo_url'   => $logoUrl,
            'updated_by' => $userId,
        ];

        if ($existing) {
            $model->update($existing['id'], $data);
            $message = 'Sponsors & Partnerships image updated.';
        } else {
            $model->insert($data);
            $message = 'Sponsors & Partnerships image saved.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function delete($id = null)
    {
        $model = new SponsorPartnershipModel();
        if ($id) {
            $record = $model->find($id);
            if ($record && !empty($record['logo_url']) && file_exists(FCPATH . $record['logo_url'])) {
                @unlink(FCPATH . $record['logo_url']);
            }
            $model->delete($id);
        }
        return redirect()->back()->with('success', 'Sponsors & Partnerships image removed.');
    }

    /* === LEGACY TIER-BASED SPONSORS (COMMENTED OUT) ===
    public function legacyIndex()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $tierModel    = new SponsorTierModel();
        $sponsorModel = new SponsorModel();

        $tiers = $tierModel->where('event_id', $eventId)->orderBy('sort_order', 'ASC')->findAll();
        foreach ($tiers as &$tier) {
            $tier['sponsors'] = $sponsorModel->where('tier_id', $tier['id'])->orderBy('sort_order', 'ASC')->findAll();
        }

        $this->data['tiers']      = $tiers;
        $this->data['pageTitle']  = 'Sponsors & Partnerships';
        $this->data['activeMenu'] = 'sponsors';

        return $this->render('admin/sponsors/index', $this->data);
    }

    public function saveTier()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $tierModel = new SponsorTierModel();
        $count = $tierModel->where('event_id', $eventId)->countAllResults();

        $groupKey = $this->request->getPost('group_key') ?: 'sponsored_by';
        $groupLabel = ($groupKey === 'partnerships') ? 'PARTNERSHIP :' : 'SPONSORED BY :';

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

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
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

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
            $logo->move(FCPATH . 'uploads/events/' . $eventId . '/sponsors', $newName);
            $data['logo_url'] = 'uploads/events/' . $eventId . '/sponsors/' . $newName;
        }

        $sponsorModel->insert($data);
        return redirect()->back()->with('success', 'Sponsor partner registered.');
    }

    public function deleteSponsor($id)
    {
        $sponsorModel = new SponsorModel();
        $sponsorModel->delete($id);
        return redirect()->back()->with('success', 'Sponsor removed.');
    }
    === END LEGACY TIER-BASED SPONSORS === */
}


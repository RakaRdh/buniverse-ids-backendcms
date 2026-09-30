<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\NewsPortalModel;

class NewsPortalController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $portalModel = new NewsPortalModel();
        $portals = $portalModel->getByEvent($eventId);

        // Auto-provision default portals if missing
        $defaults = [
            'investor-daily' => ['name' => 'Investor Daily', 'url' => 'https://investor.id', 'sort' => 1],
            'beritasatu'     => ['name' => 'BeritaSatu', 'url' => 'https://www.beritasatu.com', 'sort' => 2],
            'jakartaglobe'   => ['name' => 'JakartaGlobe', 'url' => 'https://jakartaglobe.id', 'sort' => 3],
            'datasatu'       => ['name' => 'Datasatu', 'url' => 'https://datasatu.com/tag/investor-daily-summit', 'sort' => 4],
        ];

        $existingKeys = array_column($portals, 'portal_key');
        $needsReload = false;
        foreach ($defaults as $key => $d) {
            if (!in_array($key, $existingKeys)) {
                $portalModel->insert([
                    'event_id'    => $eventId,
                    'portal_key'  => $key,
                    'portal_name' => $d['name'],
                    'portal_url'  => $d['url'],
                    'sort_order'  => $d['sort'],
                    'is_active'   => 1,
                ]);
                $needsReload = true;
            }
        }

        if ($needsReload) {
            $portals = $portalModel->getByEvent($eventId);
        }

        $this->data['portals']    = $portals;
        $this->data['pageTitle']  = 'News Portals Configuration';
        $this->data['activeMenu'] = 'news_portals';

        return $this->render('admin/news_portals/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $portalModel = new NewsPortalModel();
        $portalIds = $this->request->getPost('portal_id') ?? [];
        $portalUrls = $this->request->getPost('portal_url') ?? [];
        $portalNames = $this->request->getPost('portal_name') ?? [];

        foreach ($portalIds as $idx => $id) {
            $url = $portalUrls[$idx] ?? '';
            $name = $portalNames[$idx] ?? '';
            if (!empty($id)) {
                $portalModel->update((int)$id, [
                    'portal_name' => $name,
                    'portal_url'  => $url,
                ]);
            }
        }

        return redirect()->to(base_url('admin/news-portals'))->with('success', 'News portal links updated successfully.');
    }
}

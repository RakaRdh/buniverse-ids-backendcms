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

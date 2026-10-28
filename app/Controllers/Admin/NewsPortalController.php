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

        $defaults = [
            'overview'       => ['name' => 'Overview', 'url' => 'https://datasatu.com/tag/investor-daily-summit', 'sort' => 0],
            'investor-daily' => ['name' => 'Investor Daily', 'url' => 'https://investor.id/tag/investor-daily-summit', 'sort' => 1],
            'beritasatu'     => ['name' => 'BeritaSatu', 'url' => 'https://www.beritasatu.com/tag/investor-daily-summit', 'sort' => 2],
            'jakartaglobe'   => ['name' => 'JakartaGlobe', 'url' => 'https://jakartaglobe.id/search/investor-daily-summit', 'sort' => 3],
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

        $settingModel = new \App\Models\EventSettingModel();
        $setting = $settingModel->getByEventId($eventId);
        $newsApis = [];
        if (!empty($setting['news_apis_json'])) {
            $newsApis = json_decode($setting['news_apis_json'], true) ?: [];
        }

        $this->data['portals']    = $portals;
        $this->data['newsApis']   = $newsApis;
        $this->data['pageTitle']  = 'News Portals & Fetch APIs Configuration';
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
        $portalActive = $this->request->getPost('portal_is_active') ?? [];

        foreach ($portalIds as $idx => $id) {
            $url = $portalUrls[$idx] ?? '';
            $name = $portalNames[$idx] ?? '';
            $isActive = !empty($portalActive[$id]) ? 1 : 0;
            if (!empty($id)) {
                $portalModel->update((int)$id, [
                    'portal_name' => $name,
                    'portal_url'  => $url,
                    'is_active'   => $isActive,
                ]);
            }
        }

        // Simpan konfigurasi Fetch News API (Opsi A - JSON di event_settings)
        $apiUrlsPost = $this->request->getPost('api_url') ?? [];
        $newsApisJson = json_encode($apiUrlsPost);

        $settingModel = new \App\Models\EventSettingModel();
        $existingSetting = $settingModel->getByEventId($eventId);
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? 1;

        if ($existingSetting) {
            $settingModel->where('event_id', $eventId)->update($existingSetting['id'], [
                'news_apis_json' => $newsApisJson,
                'updated_by'     => $userId,
            ]);
        } else {
            $settingModel->insert([
                'event_id'       => $eventId,
                'news_apis_json' => $newsApisJson,
                'created_by'     => $userId,
            ]);
        }

        return redirect()->to(base_url('admin/news-portals'))->with('success', 'News portal links and API endpoints updated successfully.');
    }
}

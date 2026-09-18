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
        if ($ogFile && $ogFile->isValid() && !$ogFile->hasMoved()) {
            $newName = $ogFile->getRandomName();
            $ogFile->move(FCPATH . 'uploads/events/' . $eventId . '/seo', $newName);
            $seoData['og_image_url'] = 'uploads/events/' . $eventId . '/seo/' . $newName;
        }

        $existing = $seoModel->getByEventId($eventId);
        if ($existing) {
            $seoModel->update($existing['id'], $seoData);
        } else {
            $seoData['event_id']   = $eventId;
            $seoData['created_by'] = $userId;
            $seoModel->insert($seoData);
        }

        return redirect()->back()->with('success', 'SEO & Meta tags updated successfully.');
    }
}

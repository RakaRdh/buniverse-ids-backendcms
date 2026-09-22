<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventModel;
use App\Models\EventSettingModel;
use App\Models\EventThemePropertyModel;

class FooterController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an active event first.');
        }

        $eventModel   = new EventModel();
        $settingModel = new EventSettingModel();
        $themeModel   = new EventThemePropertyModel();

        $this->data['event']    = $eventModel->find($eventId);
        $this->data['settings'] = $settingModel->getByEventId($eventId);

        // Fetch existing footer color tokens
        $props = $themeModel->where('event_id', $eventId)
                            ->where('category', 'footer')
                            ->findAll();
        $colors = [];
        foreach ($props as $p) {
            $colors[$p['css_property']] = $p['css_value'];
        }

        // Set defaults matching Frontend CSS
        $colors['--footer-bg']           = $colors['--footer-bg'] ?? '#052431';
        $colors['--footer-text-color']   = $colors['--footer-text-color'] ?? '#ffffff';
        $colors['--footer-social-bg']     = $colors['--footer-social-bg'] ?? '#064057';
        $colors['--footer-social-color']  = $colors['--footer-social-color'] ?? '#ffffff';
        $colors['--footer-border-color']  = $colors['--footer-border-color'] ?? 'rgba(255, 255, 255, 0.12)';

        $placementModel = new \App\Models\SectionLogoPlacementModel();
        $this->data['placements'] = $placementModel->getPlacementsWithLogos($eventId);

        $this->data['footerColors'] = $colors;
        $this->data['palette']      = $themeModel->getPaletteForPicker($eventId);
        $this->data['pageTitle']    = 'Footer Settings & Social Links';
        $this->data['activeMenu']   = 'footer';

        return $this->render('admin/footer/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;
        $settingModel = new EventSettingModel();
        $themeModel   = new EventThemePropertyModel();
        $existingSetting = $settingModel->getByEventId($eventId);

        $settingData = [
            'copyright_text' => $this->request->getPost('copyright_text') ?: 'Investor Daily Summit.',
            'contact_phone'  => $this->request->getPost('contact_phone') ?: '+62 812-1267-8789',
            'contact_label'  => $this->request->getPost('contact_label') ?: 'Phone',
            'contact_url'    => $this->request->getPost('contact_url') ?: null,
            'instagram_url'  => $this->request->getPost('instagram_url'),
            'youtube_url'    => $this->request->getPost('youtube_url'),
            'facebook_url'   => $this->request->getPost('facebook_url'),
            'updated_by'     => $userId,
        ];

        if ($existingSetting) {
            $settingModel->update($existingSetting['id'], $settingData);
        } else {
            $settingData['event_id']   = $eventId;
            $settingData['created_by'] = $userId;
            $settingModel->insert($settingData);
        }

        // Save footer section colors into event_theme_properties
        $colors = $this->request->getPost('colors');
        if (is_array($colors)) {
            $colorLabels = [
                '--footer-bg'           => 'Footer Background',
                '--footer-text-color'   => 'Footer Copyright Text Color',
                '--footer-social-bg'    => 'Footer Social Button Background',
                '--footer-social-color' => 'Footer Social Icon Color',
                '--footer-border-color' => 'Footer Top Border Line',
            ];

            foreach ($colors as $prop => $val) {
                $val = trim($val);
                if ($val !== '') {
                    $lbl = $colorLabels[$prop] ?? ucwords(str_replace(['--', '-'], ['', ' '], $prop));
                    $themeModel->upsertProperty($eventId, $prop, $val, 'footer', $lbl, $userId);
                }
            }
        }

        return redirect()->to(base_url('admin/footer'))->with('success', 'Footer settings and styling updated successfully.');
    }
}

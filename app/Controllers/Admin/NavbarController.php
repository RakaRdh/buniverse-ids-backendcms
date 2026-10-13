<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventModel;
use App\Models\EventSettingModel;
use App\Models\EventThemePropertyModel;

class NavbarController extends BaseAdminController
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

        // Fetch existing navbar color tokens
        $props = $themeModel->where('event_id', $eventId)
                            ->where('category', 'navbar')
                            ->findAll();
        $colors = [];
        foreach ($props as $p) {
            $colors[$p['css_property']] = $p['css_value'];
        }

        // Set defaults if not present
        $colors['--navbar-cta-bg']            = $colors['--navbar-cta-bg'] ?? '#ffffff';
        $colors['--navbar-cta-text']          = $colors['--navbar-cta-text'] ?? '#052431';
        $colors['--navbar-cta-hover-bg']      = $colors['--navbar-cta-hover-bg'] ?? '#ffffff';
        $colors['--navbar-cta-hover-text']    = $colors['--navbar-cta-hover-text'] ?? '#041a24';
        $colors['--navbar-cta-hover-outline'] = $colors['--navbar-cta-hover-outline'] ?? '#ffffff';

        $placementModel = new \App\Models\SectionLogoPlacementModel();
        $this->data['placements'] = $placementModel->getPlacementsWithLogos($eventId);

        $this->data['navbarColors'] = $colors;
        $this->data['palette']      = $themeModel->getPaletteForPicker($eventId);
        $this->data['pageTitle']    = 'Navbar Settings & CTA Button';
        $this->data['activeMenu']   = 'navbar';

        return $this->render('admin/navbar/index', $this->data);
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

        // Process navigation items repeater
        $navLabels = $this->request->getPost('nav_label');
        $navUrls   = $this->request->getPost('nav_url');
        $navItemsJson = null;
        if (is_array($navLabels) && is_array($navUrls)) {
            $navArr = [];
            foreach ($navLabels as $k => $lbl) {
                $lbl = trim($lbl);
                $url = trim($navUrls[$k] ?? '');
                if ($lbl !== '') {
                    $navArr[] = [
                        'label' => $lbl,
                        'url'   => $url ?: '#',
                    ];
                }
            }
            $navItemsJson = json_encode($navArr);
        }

        $settingData = [
            'ticket_url'           => $this->request->getPost('ticket_url'),
            'cta_label'            => $this->request->getPost('cta_label') ?: 'Get Tickets',
            'cta_ticket_active'     => $this->request->getPost('cta_ticket_active') ? 1 : 0,
            'floating_bar_enabled' => $this->request->getPost('floating_bar_enabled') ? 1 : 0,
            'floating_bar_text'    => $this->request->getPost('floating_bar_text') ?: 'Get Your Seat Now!',
            'floating_bar_btn_label' => $this->request->getPost('floating_bar_btn_label') ?: 'Get Tickets',
            'floating_bar_btn_url' => $this->request->getPost('floating_bar_btn_url') ?: null,
            'logo_a_target_url'    => $this->request->getPost('logo_a_target_url') ?: '#home',
            'logo_b_target_url'    => $this->request->getPost('logo_b_target_url') ?: 'https://investor.id',
            'updated_by'           => $userId,
        ];

        if ($navItemsJson !== null) {
            $settingData['nav_items_json'] = $navItemsJson;
        }

        if ($existingSetting) {
            $settingModel->update($existingSetting['id'], $settingData);
        } else {
            $settingData['event_id']   = $eventId;
            $settingData['created_by'] = $userId;
            $settingModel->insert($settingData);
        }

        // Save navbar section colors into event_theme_properties
        $colors = $this->request->getPost('colors');
        if (is_array($colors)) {
            $colorLabels = [
                '--navbar-cta-bg'            => 'Navbar CTA Background',
                '--navbar-cta-text'          => 'Navbar CTA Text Color',
                '--navbar-cta-hover-bg'      => 'Navbar CTA Hover Background',
                '--navbar-cta-hover-text'    => 'Navbar CTA Hover Text Color',
                '--navbar-cta-hover-outline' => 'Navbar CTA Hover Outline',
            ];

            foreach ($colors as $prop => $val) {
                $val = trim($val);
                if ($val !== '') {
                    $lbl = $colorLabels[$prop] ?? ucwords(str_replace(['--', '-'], ['', ' '], $prop));
                    $themeModel->upsertProperty($eventId, $prop, $val, 'navbar', $lbl, $userId);
                }
            }
        }

        return redirect()->to(base_url('admin/navbar'))->with('success', 'Navbar settings and CTA styling updated successfully.');
    }
}

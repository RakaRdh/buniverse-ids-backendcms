<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventModel;

class EventController extends BaseAdminController
{
    public function index()
    {
        $eventModel = new EventModel();
        $this->data['eventsList']   = $eventModel->orderBy('year', 'DESC')->findAll();
        $this->data['pageTitle']    = 'Event Editions';
        $this->data['activeMenu']   = 'events';
        $this->data['isPortalMode'] = true;

        return $this->render('admin/events/index', $this->data);
    }

    public function manage($id)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find((int)$id);
        if (!$event) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Event edition not found.');
        }

        session()->set('active_event_id', (int)$event['id']);
        return redirect()->to(base_url('admin/dashboard'))->with('success', 'Workspace loaded: ' . ($event['title'] ?? 'IDS ' . $event['year']));
    }

    public function save()
    {
        $eventModel = new EventModel();
        $year = (int)$this->request->getPost('year');

        // Check if year already exists
        $existing = $eventModel->where('year', $year)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "An event edition for year {$year} already exists.");
        }

        $title = $this->request->getPost('title') ?: "Investor Daily Summit {$year}";
        $slug = $this->request->getPost('slug') ?: "investor-daily-summit-{$year}";
        $adminId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $id = $eventModel->insert([
            'year'             => $year,
            'title'            => $title,
            'slug'             => $slug,
            'theme'            => $this->request->getPost('theme') ?: "INDONESIA'S ECONOMIC MODEL",
            'status'           => $this->request->getPost('status') ?: 'draft',
            'event_date_start' => $this->request->getPost('event_date_start') ?: null,
            'event_date_end'   => $this->request->getPost('event_date_end') ?: null,
            'news_tag_slug'    => $this->request->getPost('news_tag_slug') ?: 'ids' . $year,
            'template_version' => $this->request->getPost('template_version') ?: 'v1',
            'created_by'       => $adminId,
            'updated_by'       => $adminId,
        ]);

        // Auto-initialize 1:1 records per Brief §1.2, §1.3, §1.4, §1.5
        $db = \Config\Database::connect();
        $db->table('event_hero')->insert([
            'event_id'   => $id,
            'venue'      => 'Raffles Hotel, Jakarta',
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('event_hero_images')->insert([
            'event_id'   => $id,
            'image_url'  => 'assets/img/image1.png',
            'alt_text'   => "Investor Daily Summit {$year} Stage",
            'sort_order' => 1,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('event_overview')->insert([
            'event_id'   => $id,
            'heading'    => $this->request->getPost('theme') ?: "INDONESIA'S ECONOMIC MODEL",
            'intro_text' => "Investor Daily Summit {$year}, Indonesia's largest investment forum is monumental gathering of power and potential.",
            'body_text'  => "Navigating strategic transformation and inclusive economic resilience.",
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('event_settings')->insert([
            'event_id'      => $id,
            'ticket_url'    => '#tickets',
            'logo_a_url'    => 'assets/img/logo_ids26.png',
            'logo_b_url'    => 'assets/img/logo_id.png',
            'created_by'    => $adminId,
            'updated_by'    => $adminId,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $db->table('event_seo')->insert([
            'event_id'         => $id,
            'meta_title'       => "Investor Daily Summit {$year}",
            'meta_description' => "Official Investor Daily Summit {$year} forum and conference.",
            'og_image_url'     => 'assets/img/image1.png',
            'created_by'       => $adminId,
            'updated_by'       => $adminId,
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // Set as active session event
        session()->set('active_event_id', $id);

        return redirect()->to(base_url('admin/events'))->with('success', "Event edition {$year} created and activated.");
    }

    public function update($id)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($id);
        if (!$event) {
            return redirect()->back()->with('error', 'Event not found.');
        }

        $adminId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $eventModel->update($id, [
            'title'            => $this->request->getPost('title') ?: $event['title'],
            'slug'             => $this->request->getPost('slug') ?: $event['slug'],
            'theme'            => $this->request->getPost('theme'),
            'status'           => $this->request->getPost('status') ?: 'draft',
            'event_date_start' => $this->request->getPost('event_date_start') ?: null,
            'event_date_end'   => $this->request->getPost('event_date_end') ?: null,
            'news_tag_slug'    => $this->request->getPost('news_tag_slug') ?: $event['news_tag_slug'],
            'template_version' => $this->request->getPost('template_version') ?: 'v1',
            'updated_by'       => $adminId,
        ]);

        return redirect()->to(base_url('admin/events'))->with('success', "Event edition updated.");
    }
}

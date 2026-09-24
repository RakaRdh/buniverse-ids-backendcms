<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventHeroImageModel;
use App\Models\EventHeroModel;
use App\Models\EventModel;
use App\Models\EventOverviewModel;
use App\Models\EventSettingModel;

class EventSettingsController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an active event first.');
        }

        $eventModel      = new EventModel();
        $heroModel       = new EventHeroModel();
        $heroImageModel  = new EventHeroImageModel();
        $overviewModel   = new EventOverviewModel();
        $settingModel    = new EventSettingModel();

        $this->data['event']       = $eventModel->find($eventId);
        $this->data['hero']        = $heroModel->getByEventId($eventId);
        $this->data['heroImages']  = $heroImageModel->getByEvent($eventId);
        $this->data['overview']    = $overviewModel->getByEventId($eventId);
        $this->data['settings']    = $settingModel->getByEventId($eventId);
        $this->data['activeTab']   = $this->request->getGet('tab') ?? 'general';
        $this->data['pageTitle']   = 'Event Settings & Content';
        $this->data['activeMenu']  = 'event-settings';

        return $this->render('admin/event_settings/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $tab = $this->request->getPost('tab') ?? 'general';
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        if ($tab === 'general') {
            $eventModel = new EventModel();
            $eventModel->update($eventId, [
                'title'            => $this->request->getPost('title'),
                'theme'            => $this->request->getPost('theme'),
                'event_date_start' => $this->request->getPost('event_date_start'),
                'event_date_end'   => $this->request->getPost('event_date_end'),
                'news_tag_slug'    => $this->request->getPost('news_tag_slug'),
                'template_version' => $this->request->getPost('template_version'),
                'status'           => $this->request->getPost('status'),
                'updated_by'       => $userId,
            ]);

            // Update session active event name/title
            $active = session()->get('active_event');
            if ($active && $active['id'] == $eventId) {
                $active['title'] = $this->request->getPost('title');
                $active['name']  = $this->request->getPost('title');
                $active['theme'] = $this->request->getPost('theme');
                session()->set('active_event', $active);
            }
        } elseif ($tab === 'hero') {
            $heroModel = new EventHeroModel();
            $existingHero = $heroModel->getByEventId($eventId);

            $heroData = [
                'tagline'    => $this->request->getPost('tagline'),
                'venue'      => $this->request->getPost('venue'),
                'updated_by' => $userId,
            ];

            if ($existingHero) {
                $heroModel->update($existingHero['id'], $heroData);
            } else {
                $heroData['event_id']   = $eventId;
                $heroData['created_by'] = $userId;
                $heroModel->insert($heroData);
            }
        } elseif ($tab === 'overview') {
            $overviewModel = new EventOverviewModel();
            $existingOverview = $overviewModel->getByEventId($eventId);

            $overviewData = [
                'heading'    => $this->request->getPost('heading'),
                'intro_text' => $this->request->getPost('intro_text'),
                'body_text'  => $this->request->getPost('body_text'),
                'updated_by' => $userId,
            ];

            if ($existingOverview) {
                $overviewModel->update($existingOverview['id'], $overviewData);
            } else {
                $overviewData['event_id']   = $eventId;
                $overviewData['created_by'] = $userId;
                $overviewModel->insert($overviewData);
            }
        } elseif ($tab === 'brand') {
            $settingModel = new EventSettingModel();
            $existingSetting = $settingModel->getByEventId($eventId);

            // Process navigation items from post if provided
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
                'ticket_url'     => $this->request->getPost('ticket_url'),
                'cta_label'      => $this->request->getPost('cta_label') ?: 'Get Tickets',
                'copyright_text' => $this->request->getPost('copyright_text') ?: 'Investor Daily Summit.',
                'facebook_url'   => $this->request->getPost('facebook_url'),
                'instagram_url'  => $this->request->getPost('instagram_url'),
                'twitter_url'    => $this->request->getPost('twitter_url'),
                'youtube_url'    => $this->request->getPost('youtube_url'),
                'linkedin_url'   => $this->request->getPost('linkedin_url'),
                'updated_by'     => $userId,
            ];

            if ($navItemsJson !== null) {
                $settingData['nav_items_json'] = $navItemsJson;
            }

            // Handle logo uploads
            $uploadMap = [
                'logo_a_file'      => 'logo_a_url',
                'logo_b_file'      => 'logo_b_url',
                'logo_footer_file' => 'logo_footer_url',
            ];

            foreach ($uploadMap as $inputName => $dbField) {
                $file = $this->request->getFile($inputName);
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    if (!$this->validate([
                        $inputName => [
                            'rules'  => 'is_image[' . $inputName . ']|mime_in[' . $inputName . ',image/jpg,image/jpeg,image/png,image/webp,image/svg+xml]|max_size[' . $inputName . ',4096]',
                            'errors' => [
                                'is_image' => 'Logo file must be a valid image.',
                                'mime_in'  => 'Only JPG, PNG, WebP, and SVG images are allowed.',
                                'max_size' => 'Logo size cannot exceed 4MB.',
                            ],
                        ],
                    ])) {
                        return redirect()->back()->withInput()->with('error', $this->validator->getError($inputName));
                    }

                    $newName = $file->getRandomName();
                    $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/branding';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $file->move($uploadDir, $newName);
                    $settingData[$dbField] = 'uploads/events/' . $eventId . '/branding/' . $newName;
                }
            }

            if ($existingSetting) {
                $settingModel->where('event_id', $eventId)->update($existingSetting['id'], $settingData);
            } else {
                $settingData['event_id']   = $eventId;
                $settingData['created_by'] = $userId;
                $settingModel->insert($settingData);
            }
        }

        return redirect()->to(base_url('admin/event-settings?tab=' . $tab))->with('success', ucfirst($tab) . ' settings updated successfully.');
    }

    public function uploadHeroImage()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        if (!$this->validate([
            'hero_image_file' => [
                'rules'  => 'uploaded[hero_image_file]|is_image[hero_image_file]|mime_in[hero_image_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[hero_image_file,10240]',
                'errors' => [
                    'uploaded' => 'Please select a hero banner image file to upload.',
                    'is_image' => 'Uploaded file must be a valid image.',
                    'mime_in'  => 'Only JPG, JPEG, PNG, and WebP images are allowed.',
                    'max_size' => 'Image size cannot exceed 10MB.',
                ],
            ],
        ])) {
            return redirect()->to(base_url('admin/event-settings?tab=hero'))->with('error', $this->validator->getError('hero_image_file'));
        }

        $image = $this->request->getFile('hero_image_file');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $uploadDir = FCPATH . 'uploads/events/' . $eventId . '/hero';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $image->move($uploadDir, $newName);

            $heroImageModel = new EventHeroImageModel();
            $count = $heroImageModel->where('event_id', $eventId)->countAllResults();
            $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

            $heroImageModel->insert([
                'event_id'   => $eventId,
                'image_url'  => 'uploads/events/' . $eventId . '/hero/' . $newName,
                'alt_text'   => $this->request->getPost('alt_text') ?? 'Hero Banner Image',
                'sort_order' => $count + 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            return redirect()->to(base_url('admin/event-settings?tab=hero'))->with('success', 'Hero image uploaded.');
        }

        return redirect()->to(base_url('admin/event-settings?tab=hero'))->with('error', 'Failed to upload hero image.');
    }

    public function deleteHeroImage($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        $heroImageModel = new EventHeroImageModel();
        $img = $heroImageModel->where('id', (int)$id)->where('event_id', $eventId)->first();
        if ($img) {
            if (!empty($img['image_url']) && file_exists(FCPATH . $img['image_url'])) {
                @unlink(FCPATH . $img['image_url']);
            }
            $heroImageModel->where('event_id', $eventId)->delete($id);
            return redirect()->to(base_url('admin/event-settings?tab=hero'))->with('success', 'Hero image deleted.');
        }
        return redirect()->to(base_url('admin/event-settings?tab=hero'))->with('error', 'Hero image not found in active event.');
    }
}

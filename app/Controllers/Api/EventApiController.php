<?php

namespace App\Controllers\Api;

use App\Models\EventHeroImageModel;
use App\Models\EventHeroModel;
use App\Models\EventModel;
use App\Models\EventOverviewModel;
use App\Models\EventSeoModel;
use App\Models\EventSettingModel;
use App\Models\EventThemePropertyModel;
use App\Models\GalleryImageModel;
use App\Models\ModeratorModel;
use App\Models\ScheduleDayModel;
use App\Models\ScheduleRoomModel;
use App\Models\ScheduleSessionModel;
use App\Models\SectionDecorationModel;
use App\Models\SpeakerModel;
use App\Models\SponsorModel;
use App\Models\SponsorPartnershipModel;
use App\Models\SponsorTierModel;
use App\Models\TicketClickLogModel;
use CodeIgniter\RESTful\ResourceController;

class EventApiController extends ResourceController
{
    protected $format = 'json';

    public function active()
    {
        $eventModel = new EventModel();
        $event = $eventModel->getActiveEvent();

        if (!$event) {
            return $this->failNotFound('No active event found.');
        }

        return $this->show($event['id']);
    }

    public function show($id = null)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($id);

        if (!$event) {
            return $this->failNotFound("Event ID {$id} not found.");
        }

        $heroModel       = new EventHeroModel();
        $heroImageModel  = new EventHeroImageModel();
        $overviewModel   = new EventOverviewModel();
        $settingModel    = new EventSettingModel();
        $seoModel        = new EventSeoModel();
        $speakerModel    = new SpeakerModel();
        $moderatorModel  = new ModeratorModel();
        $dayModel        = new ScheduleDayModel();
        $sessionModel    = new ScheduleSessionModel();
        $galleryModel    = new GalleryImageModel();
        $tierModel       = new SponsorTierModel();
        $sponsorModel    = new SponsorModel();
        $themeModel      = new EventThemePropertyModel();

        // 1. Site Metadata & Branding
        $hero     = $heroModel->getByEventId($event['id']) ?? [];
        $settings = $settingModel->getByEventId($event['id']) ?? [];
        $seo      = $seoModel->getByEventId($event['id']) ?? [];

        // Build URLs
        $makeUrl = function ($path) {
            if (empty($path)) return '';
            if (str_starts_with($path, 'http')) return $path;
            return base_url($path);
        };

        $navItems = !empty($settings['nav_items_json']) ? json_decode($settings['nav_items_json'], true) : [];
        if (empty($navItems)) {
            $navItems = [
                ['label' => 'Home', 'url' => '#home'],
                ['label' => 'About Us', 'url' => '#about-us'],
                ['label' => 'Schedule', 'url' => '#schedule'],
                ['label' => 'Gallery', 'url' => '#gallery'],
                ['label' => 'News', 'url' => '#news'],
                ['label' => 'Sponsor', 'url' => '#sponsor'],
            ];
        }

        $site = [
            'name'                => $event['title'] ?? $event['name'] ?? '',
            'title'               => $event['title'] ?? '',
            'year'                => (int)$event['year'],
            'theme'               => $event['theme'] ?? '',
            'tagline'             => $event['tagline'] ?? '',
            'headline'            => $hero['headline'] ?? '',
            'subheadline'         => $hero['subheadline'] ?? '',
            'venue'               => $hero['venue'] ?? $hero['venue_name'] ?? '',
            'city'                => $hero['venue_city'] ?? '',
            'location'            => $event['location_display'] ?? '',
            'dates'               => (!empty($event['event_date_start']) && !empty($event['event_date_end'])) ? (date('d', strtotime($event['event_date_start'])) . '-' . date('d F Y', strtotime($event['event_date_end']))) : '',
            'cta_ticket_label'    => $settings['cta_label'] ?? $hero['cta_ticket_label'] ?? 'Get Tickets',
            'cta_ticket_url'      => $settings['ticket_url'] ?? $hero['cta_ticket_url'] ?? '',
            'cta_ticket_active'   => (bool)($hero['cta_ticket_active'] ?? true),
            'cta_secondary_label' => $hero['cta_secondary_label'] ?? 'Explore Speakers',
            'cta_secondary_url'   => $hero['cta_secondary_url'] ?? '#speakers',
            'hero_image'          => $makeUrl($hero['hero_image_url'] ?? ''),
            'logo_a'              => $makeUrl($settings['logo_a_url'] ?? ''),
            'logo_b'              => $makeUrl($settings['logo_b_url'] ?? ''),
            'logo_footer'         => $makeUrl(!empty($settings['logo_footer_url']) ? $settings['logo_footer_url'] : ($settings['logo_b_url'] ?? '')),
            'copyright'           => $settings['copyright_text'] ?? 'Investor Daily Summit.',
            'nav_items'           => $navItems,
            'colors'              => [
                'primary'   => $settings['primary_color'] ?? '#C99400',
                'secondary' => $settings['secondary_color'] ?? '#0A0F1D',
                'accent'    => $settings['accent_color'] ?? '#E0A800',
            ],
            'footer'              => [
                'copyright'   => $settings['copyright_text'] ?? 'Investor Daily Summit.',
                'description' => $settings['footer_description'] ?? '',
            ],
            'socials'             => [
                'facebook'  => $settings['facebook_url'] ?? '',
                'instagram' => $settings['instagram_url'] ?? '',
                'youtube'   => $settings['youtube_url'] ?? '',
                'linkedin'  => $settings['linkedin_url'] ?? '',
                'twitter'   => $settings['twitter_url'] ?? '',
            ],
            'contact'             => [
                'email' => $settings['contact_email'] ?? '',
                'phone' => $settings['contact_phone'] ?? '',
            ],
            'news_tag_slug'       => $event['news_tag_slug'] ?? 'ids2026',
        ];

        // 1b. CSS Theme Properties EAV
        $themeProperties = [];
        $propRows = $themeModel->getByEvent($event['id']);
        foreach ($propRows as $pr) {
            $themeProperties[] = [
                'property'   => $pr['css_property'],
                'value'      => $pr['css_value'],
                'value_type' => $pr['value_type'],
                'category'   => $pr['category'],
                'label'      => $pr['label'],
            ];
        }

        // 2. Hero Carousel Images
        $heroCarousel = [];
        $carouselRows = $heroImageModel->getByEvent($event['id']);
        foreach ($carouselRows as $cr) {
            $heroCarousel[] = [
                'id'        => (int)$cr['id'],
                'image_url' => $makeUrl($cr['image_url']),
                'caption'   => $cr['alt_text'] ?? $cr['caption'] ?? '',
            ];
        }

        // 3. Overview (f.k.a Economic Model)
        $overviewRow = $overviewModel->getByEventId($event['id']) ?? [];
        $overview = [
            'badge'           => $event['theme'] ?? "INDONESIA'S ECONOMIC MODEL",
            'title'           => $overviewRow['heading'] ?? "INDONESIA'S ECONOMIC MODEL",
            'intro'           => $overviewRow['intro_text'] ?? '',
            'content'         => $overviewRow['body_text'] ?? '',
            'video_url'       => $overviewRow['video_url'] ?? '',
            'video_cover_url' => $makeUrl($overviewRow['video_cover_url'] ?? ''),
            'stats'           => [
                [
                    'number' => '8%',
                    'label'  => 'Target Pertumbuhan Ekonomi',
                ],
                [
                    'number' => '32+',
                    'label'  => 'Sesi Plenary & Roundtable',
                ],
                [
                    'number' => '2.500+',
                    'label'  => 'Pemimpin Bisnis & Investor',
                ],
            ],
        ];
        $economicModel = $overview;

        // 4. Speakers
        $speakers = [];
        $speakerRows = $speakerModel->getByEvent($event['id'], true);
        foreach ($speakerRows as $sp) {
            $speakers[] = [
                'id'        => (int)$sp['id'],
                'slug'      => $sp['slug'] ?? url_title($sp['name'], '-', true),
                'name'      => $sp['name'],
                'role'      => $sp['role_text'] ?? '',
                'bio'       => $sp['bio'] ?? '',
                'photo'     => $makeUrl($sp['photo_url'] ?? ''),
            ];
        }

        // 5. Moderators (separate master)
        $moderators = [];
        $moderatorRows = $moderatorModel->getByEvent($event['id'], true);
        foreach ($moderatorRows as $mod) {
            $moderators[] = [
                'id'        => (int)$mod['id'],
                'name'      => $mod['name'],
                'role'      => $mod['role_text'] ?? '',
                'photo'     => $makeUrl($mod['photo_url'] ?? ''),
            ];
        }

        // 6. Schedule (Days -> Sessions with Normal and Multi-Room structures)
        $schedule = [];
        $days = $dayModel->getByEvent($event['id']);
        foreach ($days as $day) {
            $dayKey = 'day_' . $day['day_number'];
            $daySessions = $sessionModel->getSessionsByDay((int)$day['id']);

            $formattedSessions = [];
            foreach ($daySessions as $sess) {
                $sessionItem = [
                    'id'           => (int)$sess['id'],
                    'title'        => $sess['title'],
                    'description'  => $sess['description'] ?? '',
                    'time'         => substr($sess['start_time'], 0, 5) . ' - ' . substr($sess['end_time'], 0, 5) . ' WIB',
                    'start_time'   => substr($sess['start_time'], 0, 5),
                    'end_time'     => substr($sess['end_time'], 0, 5),
                    'session_type' => $sess['session_type'],
                    'is_break'     => ($sess['session_type'] === 'break'),
                    'room'         => $sess['room_name'] ?? '',
                ];

                // Single session moderator & speakers
                if (!empty($sess['moderators'])) {
                    $m = $sess['moderators'][0];
                    $sessionItem['moderator'] = [
                        'label' => 'MOD:',
                        'name'  => $m['name'],
                        'role'  => $m['role_text'] ?? '',
                        'photo' => $makeUrl($m['photo_url'] ?? ''),
                    ];
                }

                $sessionItem['speakers'] = [];
                $sessionItem['speaker_ids'] = [];
                foreach ($sess['speakers'] as $spk) {
                    $spkSlug = $spk['slug'] ?? ('speaker-' . $spk['speaker_id']);
                    $sessionItem['speaker_ids'][] = $spkSlug;
                    $sessionItem['speakers'][] = [
                        'id'    => (int)$spk['speaker_id'],
                        'slug'  => $spkSlug,
                        'name'  => $spk['name'],
                        'role'  => $spk['role_text'] ?? '',
                        'photo' => $makeUrl($spk['photo_url'] ?? ''),
                    ];
                }

                // Multi-room child sessions (Session -> Room -> Items)
                if (!empty($sess['session_rooms'])) {
                    $sessionItem['is_multi_room'] = true;
                    $sessionItem['rooms'] = [];

                    foreach ($sess['session_rooms'] as $sr) {
                        $roomEntry = [
                            'room'     => $sr['room_name'] ?? '',
                            'sessions' => [],
                        ];

                        foreach ($sr['items'] as $it) {
                            $subItem = [
                                'title'       => $it['title'],
                                'subtitle'    => $it['subtitle'] ?? '',
                                'moderator'   => null,
                                'speaker_ids' => [],
                                'speakers'    => [],
                            ];

                            if (!empty($it['moderators'])) {
                                $cm = $it['moderators'][0];
                                $subItem['moderator'] = [
                                    'label' => 'MOD:',
                                    'name'  => $cm['name'],
                                    'role'  => $cm['role_text'] ?? '',
                                    'photo' => $makeUrl($cm['photo_url'] ?? ''),
                                ];
                            }

                            foreach ($it['speakers'] as $cspk) {
                                $subItem['speaker_ids'][] = $cspk['slug'];
                                $subItem['speakers'][] = [
                                    'id'    => (int)$cspk['speaker_id'],
                                    'slug'  => $cspk['slug'],
                                    'name'  => $cspk['name'],
                                    'role'  => $cspk['role_text'] ?? '',
                                    'photo' => $makeUrl($cspk['photo_url'] ?? ''),
                                ];
                            }

                            $roomEntry['sessions'][] = $subItem;
                        }

                        $sessionItem['rooms'][] = $roomEntry;
                    }
                } elseif (!empty($sess['children'])) {
                    // Fallback for legacy flat children
                    $sessionItem['is_multi_room'] = true;
                    $roomGroup = [];

                    foreach ($sess['children'] as $child) {
                        $rName = $child['room_name'] ?? 'Room Track';
                        if (!isset($roomGroup[$rName])) {
                            $roomGroup[$rName] = [
                                'room'     => $rName,
                                'sessions' => [],
                            ];
                        }

                        $subItem = [
                            'title'       => $child['title'],
                            'subtitle'    => $child['subtitle'] ?? '',
                            'moderator'   => null,
                            'speaker_ids' => [],
                            'speakers'    => [],
                        ];

                        if (!empty($child['moderators'])) {
                            $cm = $child['moderators'][0];
                            $subItem['moderator'] = [
                                'label' => 'MOD:',
                                'name'  => $cm['name'],
                                'role'  => $cm['role_text'] ?? '',
                                'photo' => $makeUrl($cm['photo_url'] ?? ''),
                            ];
                        }

                        foreach ($child['speakers'] as $cspk) {
                            $subItem['speaker_ids'][] = $cspk['slug'] ?? ('speaker-' . $cspk['speaker_id']);
                            $subItem['speakers'][] = [
                                'id'    => (int)$cspk['speaker_id'],
                                'slug'  => $cspk['slug'] ?? '',
                                'name'  => $cspk['name'],
                                'role'  => $cspk['role_text'] ?? '',
                                'photo' => $makeUrl($cspk['photo_url'] ?? ''),
                            ];
                        }

                        $roomGroup[$rName]['sessions'][] = $subItem;
                    }

                    $sessionItem['rooms'] = array_values($roomGroup);
                }

                $formattedSessions[] = $sessionItem;
            }

            $rawDayDate = $day['event_date'] ?? $day['date'] ?? null;
            $schedule[$dayKey] = [
                'day_number' => (int)$day['day_number'],
                'label'      => $day['label'],
                'date'       => $rawDayDate ? date('d F Y', strtotime($rawDayDate)) : '',
                'sessions'   => $formattedSessions,
            ];
        }

        // 7. Gallery
        $gallery = [];
        $galleryRows = $galleryModel->getByEvent($event['id']);
        foreach ($galleryRows as $g) {
            $gallery[] = [
                'id'          => (int)$g['id'],
                'image_url'   => $makeUrl($g['image_url']),
                'caption'     => $g['caption'] ?? '',
                'is_featured' => (bool)$g['is_featured'],
            ];
        }

        // 8. Sponsors & Partnerships (Single Image Banner)
        $sponsorPartnershipModel = new SponsorPartnershipModel();
        $spRow = $sponsorPartnershipModel->getByEvent($event['id']);
        $sponsorPartnership = null;
        if ($spRow) {
            $sponsorPartnership = [
                'id'         => (int)$spRow['id'],
                'name'       => $spRow['name'] ?? '',
                'logo_url'   => $makeUrl($spRow['logo_url']),
                'updated_at' => $spRow['updated_at'] ?? '',
            ];
        }

        // 9. Section Decorations (Cakram / Disc)
        $decorationModel = new SectionDecorationModel();
        $decRows = $decorationModel->getActiveByEvent($event['id']);
        $decorations = [];
        foreach ($decRows as $dec) {
            $decorations[] = [
                'id'                 => (int)$dec['id'],
                'section_key'        => $dec['section_key'],
                'layout_mode'        => $dec['layout_mode'],
                'position'           => $dec['position'],
                'auto_count'          => (int)$dec['auto_count'],
                'auto_gap_px'         => (int)$dec['auto_gap_px'],
                'auto_start_side'     => $dec['auto_start_side'] ?? 'left',
                'auto_gap_unit'       => $dec['auto_gap_unit'] ?? 'px',
                'edge_offset_mode'    => $dec['edge_offset_mode'] ?? 'fixed',
                'edge_offset_percent' => (int)($dec['edge_offset_percent'] ?? 50),
                'image_url'           => $makeUrl($dec['image_url']),
                'size_px'            => (int)$dec['size_px'],
                'opacity'            => (float)$dec['opacity'],
                'is_rotated'         => (bool)$dec['is_rotated'],
                'rotation_direction' => $dec['rotation_direction'] ?? 'clockwise',
                'show_on_mobile'     => (bool)$dec['show_on_mobile'],
            ];
        }

        /* === LEGACY TIER-BASED SPONSORS (COMMENTED OUT) ===
        $sponsors = [
            'sponsored_by' => [],
            'partnerships' => [],
        ];
        $tiers = $tierModel->getByEvent($event['id']);
        foreach ($tiers as $tier) {
            $tierSponsors = $sponsorModel->getByTier((int)$tier['id']);
            $sponsorItems = [];
            foreach ($tierSponsors as $ts) {
                $sponsorItems[] = [
                    'id'          => (int)$ts['id'],
                    'name'        => $ts['name'],
                    'logo'        => $makeUrl($ts['logo_url']),
                    'website_url' => $ts['website_url'] ?? '',
                ];
            }
            $targetGroup = ($tier['group_key'] === 'partnerships') ? 'partnerships' : 'sponsored_by';
            $sponsors[$targetGroup][] = [
                'tier_id'     => (int)$tier['id'],
                'tier_name'   => $tier['name'],
                'group_label' => $tier['group_label'],
                'logo_size'   => $tier['logo_size'],
                'items'       => $sponsorItems,
            ];
        }
        === END LEGACY TIER-BASED SPONSORS === */
        $sponsors = [
            'sponsored_by' => [],
            'partnerships' => [],
        ];

        // 10. Custom Placements (Freeform Coordinates)
        $placementModel = new \App\Models\CustomPlacementModel();
        $placementRows = $placementModel->getActiveByEvent($event['id']);
        $customPlacements = [];
        foreach ($placementRows as $cp) {
            $customPlacements[] = [
                'id'                 => (int)$cp['id'],
                'name'               => $cp['name'] ?? '',
                'section_key'        => $cp['section_key'],
                'image_url'          => $makeUrl($cp['image_url']),
                'pos_x_percent'      => (float)$cp['pos_x_percent'],
                'pos_y_percent'      => (float)$cp['pos_y_percent'],
                'size_px'            => (int)$cp['size_px'],
                'opacity'            => (float)$cp['opacity'],
                'is_rotated'         => (bool)$cp['is_rotated'],
                'rotation_direction' => $cp['rotation_direction'] ?? 'clockwise',
                'show_on_mobile'     => (bool)$cp['show_on_mobile'],
                'z_index'            => (int)($cp['z_index'] ?? 1),
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'site'                => $site,
                'seo'                 => [
                    'meta_title'       => $seo['meta_title'] ?? '',
                    'meta_description' => $seo['meta_description'] ?? '',
                    'og_image_url'     => $makeUrl($seo['og_image_url'] ?? ''),
                ],
                'hero_carousel'       => $heroCarousel,
                'overview'            => $overview,
                'economicModel'       => $economicModel,
                'speakers'            => $speakers,
                'moderators'          => $moderators,
                'schedule'            => $schedule,
                'gallery'             => $gallery,
                'sponsors'            => $sponsors,
                'sponsor_partnership' => $sponsorPartnership,
                'section_decorations' => $decorations,
                'custom_placements'   => $customPlacements,
                'theme_properties'    => $themeProperties,
            ],
        ]);
    }

    public function logTicketClick()
    {
        $eventModel = new EventModel();
        $event = $eventModel->getActiveEvent();
        if (!$event) {
            return $this->failNotFound('No active event');
        }

        $clickModel = new TicketClickLogModel();
        $clickModel->insert([
            'event_id'   => $event['id'],
            'clicked_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => substr((string)$this->request->getUserAgent(), 0, 500),
            'referrer'   => substr((string)$this->request->getServer('HTTP_REFERER'), 0, 500),
        ]);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Ticket click telemetry logged',
        ]);
    }
}

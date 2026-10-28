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

    public function showBySlug($slug = null)
    {
        $eventModel = new EventModel();
        $event = $eventModel->where('slug', $slug)->first();

        if (!$event) {
            return $this->failNotFound("Event slug '{$slug}' not found.");
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

        // Resolve Master Logos and Section Placements
        $placementModel = new \App\Models\SectionLogoPlacementModel();
        $placementsRaw = $placementModel->getPlacementsWithLogos($event['id']);
        $logoPlacements = [];
        foreach ($placementsRaw as $pr) {
            $logoPlacements[$pr['slot_key']] = [
                'logo_id'    => $pr['logo_id'],
                'name'       => $pr['logo_name'] ?? '',
                'color_url'  => $makeUrl($pr['logo_url'] ?? ''),
                'white_url'  => $makeUrl($pr['logo_white_url'] ?? ''),
                'variant'    => $pr['variant'] ?? 'auto',
                'target_url' => $pr['target_url'] ?? null,
                'is_active'  => (bool)$pr['is_active'],
            ];
        }

        // Helper to resolve slot logo image URL and target link based on variant
        $resolveSlotLogo = function ($slotKey, $defaultTarget, $fallbackColorUrl, $fallbackWhiteUrl = null) use ($logoPlacements, $settings, $makeUrl) {
            $slot = $logoPlacements[$slotKey] ?? null;
            if ($slot && !empty($slot['is_active']) && (!empty($slot['color_url']) || !empty($slot['white_url']))) {
                $variant = $slot['variant'] ?? 'auto';
                $color = !empty($slot['color_url']) ? $slot['color_url'] : $slot['white_url'];
                $white = !empty($slot['white_url']) ? $slot['white_url'] : $slot['color_url'];

                if ($variant === 'color') {
                    $activeLogo = $color;
                } elseif ($variant === 'white') {
                    $activeLogo = $white;
                } else {
                    // auto variant: return color as main, white as monochrome
                    $activeLogo = $color;
                }

                $target = !empty($slot['target_url']) ? $slot['target_url'] : $defaultTarget;
                return [
                    'active_logo' => $activeLogo,
                    'color_url'   => $color,
                    'white_url'   => $white,
                    'variant'     => $variant,
                    'target_url'  => $target,
                    'is_active'   => true,
                ];
            }

            // Fallback to settings / defaults
            $fColor = $makeUrl($fallbackColorUrl);
            $fWhite = $fallbackWhiteUrl ? $makeUrl($fallbackWhiteUrl) : $fColor;
            return [
                'active_logo' => $fColor,
                'color_url'   => $fColor,
                'white_url'   => $fWhite,
                'variant'     => 'auto',
                'target_url'  => $defaultTarget,
                'is_active'   => true,
            ];
        };

        // Slot 1: Navbar Logo 1 (Summit Event Logo)
        $slot1Resolved = $resolveSlotLogo(
            'navbar_slot_1',
            $settings['logo_a_target_url'] ?? '#home',
            $settings['logo_a_url'] ?? 'assets/img/logo_ids26_color.webp',
            $settings['logo_a_white_url'] ?? 'assets/img/logo_ids26_white.webp'
        );

        // Slot 2: Navbar Logo 2 (Investor Daily Indonesia)
        $slot2Resolved = $resolveSlotLogo(
            'navbar_slot_2',
            $settings['logo_b_target_url'] ?? 'https://investor.id',
            $settings['logo_b_url'] ?? 'assets/img/logo_id_color.webp',
            $settings['logo_b_white_url'] ?? 'assets/img/logo_id_white.webp'
        );

        // Slot 3: Overview (About Us Section)
        $slotOverviewResolved = $resolveSlotLogo(
            'overview',
            '#home',
            $settings['logo_a_url'] ?? 'assets/img/logo_ids26_color.webp',
            $settings['logo_a_white_url'] ?? 'assets/img/logo_ids26_white.webp'
        );

        // Slot 4: Floating Bottom Bar
        $slotFloatingResolved = $resolveSlotLogo(
            'floating_bottom',
            $settings['logo_a_target_url'] ?? '#home',
            $settings['logo_a_white_url'] ?? 'assets/img/logo_ids26_white.webp',
            $settings['logo_a_white_url'] ?? 'assets/img/logo_ids26_white.webp'
        );

        // Slot 5: Footer
        $slotFooterResolved = $resolveSlotLogo(
            'footer',
            $settings['logo_b_target_url'] ?? 'https://investor.id',
            !empty($settings['logo_footer_url']) ? $settings['logo_footer_url'] : ($settings['logo_b_white_url'] ?? 'assets/img/logo_id_white.webp'),
            $settings['logo_b_white_url'] ?? 'assets/img/logo_id_white.webp'
        );

        $logoAColor = $slot1Resolved['color_url'];
        $logoAWhite = $slot1Resolved['white_url'];
        $logoATargetUrl = $slot1Resolved['target_url'];

        $logoBColor = $slot2Resolved['color_url'];
        $logoBWhite = $slot2Resolved['white_url'];
        $logoBTargetUrl = $slot2Resolved['target_url'];

        $logoOverview = $slotOverviewResolved['active_logo'];
        $logoOverviewTargetUrl = $slotOverviewResolved['target_url'];

        $logoFloating = $slotFloatingResolved['active_logo'];

        $logoFooter = $slotFooterResolved['active_logo'];
        $logoFooterWhite = $slotFooterResolved['white_url'];
        $logoFooterTargetUrl = $slotFooterResolved['target_url'];

        $ctaTicketActive = isset($settings['cta_ticket_active']) ? (bool)$settings['cta_ticket_active'] : (bool)($hero['cta_ticket_active'] ?? true);

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
            'dates'               => (!empty($event['event_date_start']) && !empty($event['event_date_end'])) ? (function($start, $end) {
                $t1 = strtotime($start);
                $t2 = strtotime($end);
                $m1 = date('F', $t1);
                $m2 = date('F', $t2);
                $y1 = date('Y', $t1);
                $y2 = date('Y', $t2);
                $d1 = date('jS', $t1);
                $d2 = date('jS', $t2);
                if ($y1 === $y2 && $m1 === $m2) {
                    return "{$m1} {$d1}-{$d2}, {$y1}";
                } elseif ($y1 === $y2) {
                    return "{$m1} {$d1} - {$m2} {$d2}, {$y1}";
                }
                return "{$m1} {$d1}, {$y1} - {$m2} {$d2}, {$y2}";
            })($event['event_date_start'], $event['event_date_end']) : '',
            'cta_ticket_label'    => $settings['cta_label'] ?? $hero['cta_ticket_label'] ?? 'Get Tickets',
            'cta_ticket_url'      => $settings['ticket_url'] ?? $hero['cta_ticket_url'] ?? '',
            'cta_ticket_active'   => $ctaTicketActive,
            'cta_secondary_label' => $hero['cta_secondary_label'] ?? 'Explore Speakers',
            'cta_secondary_url'   => $hero['cta_secondary_url'] ?? '#speakers',
            'hero_image'          => $makeUrl($hero['hero_image_url'] ?? ''),
            'logo_a'              => $logoAColor,
            'logo_b'              => $logoBColor,
            'logo_a_color'        => $logoAColor,
            'logo_a_white'        => $logoAWhite,
            'logo_a_target_url'   => $logoATargetUrl,
            'logo_b_color'        => $logoBColor,
            'logo_b_white'        => $logoBWhite,
            'logo_b_target_url'   => $logoBTargetUrl,
            'logo_overview'       => $logoOverview,
            'logo_overview_target_url' => $logoOverviewTargetUrl,
            'logo_floating'       => $logoFloating,
            'logo_footer'         => $logoFooter,
            'logo_footer_white'   => $logoFooterWhite,
            'logo_footer_target_url' => $logoFooterTargetUrl,
            'logo_placements'     => $logoPlacements,
            'floating_bar'        => [
                'enabled'   => isset($settings['floating_bar_enabled']) ? (bool)$settings['floating_bar_enabled'] : true,
                'text'      => $settings['floating_bar_text'] ?? 'Get Your Seat Now!',
                'btn_label' => $settings['floating_bar_btn_label'] ?? 'Get Tickets',
                'btn_url'   => $settings['floating_bar_btn_url'] ?? null,
            ],
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
                'tiktok'    => $settings['tiktok_url'] ?? '',
                'linkedin'  => $settings['linkedin_url'] ?? '',
                'twitter'   => $settings['twitter_url'] ?? '',
            ],
            'contact'             => [
                'email' => $settings['contact_email'] ?? '',
                'phone' => $settings['contact_phone'] ?? '+62 812-1267-8789',
                'label' => $settings['contact_label'] ?? 'Phone',
                'url'   => $settings['contact_url'] ?? null,
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
            'collage_images'  => [
                'img_1' => $makeUrl($overviewRow['collage_img_1'] ?? ''),
                'img_2' => $makeUrl($overviewRow['collage_img_2'] ?? ''),
                'img_3' => $makeUrl($overviewRow['collage_img_3'] ?? ''),
                'img_4' => $makeUrl($overviewRow['collage_img_4'] ?? ''),
            ],
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

        // 3b. Livestream Settings (Multi-stream support)
        $livestreamModel = new \App\Models\EventLivestreamModel();
        $livestreamRows = $livestreamModel->where('event_id', $event['id'])->orderBy('id', 'ASC')->findAll();
        $livestream = null;
        $livestreams = [];

        foreach ($livestreamRows as $row) {
            $streamItem = [
                'id'          => (int)$row['id'],
                'title'       => $row['title'] ?? 'Investor Daily Summit Live Broadcast',
                'youtube_url' => $row['youtube_url'] ?? '',
                'is_active'   => !empty($row['is_active']),
            ];
            if (!empty($row['is_active']) && !empty(trim($row['youtube_url'] ?? ''))) {
                $livestreams[] = $streamItem;
            }
        }

        if (!empty($livestreams)) {
            $livestream = $livestreams[0];
        } elseif (!empty($livestreamRows)) {
            // If exists but inactive
            $livestream = [
                'id'          => (int)$livestreamRows[0]['id'],
                'title'       => $livestreamRows[0]['title'] ?? 'Investor Daily Summit Live Broadcast',
                'youtube_url' => $livestreamRows[0]['youtube_url'] ?? '',
                'is_active'   => !empty($livestreamRows[0]['is_active']),
            ];
        }

        // 3c. News Portals & Custom Fetch APIs
        $portalModel = new \App\Models\NewsPortalModel();
        $portalRows = $portalModel->getByEvent($event['id']);
        $configuredApis = [];
        if (!empty($settings['news_apis_json'])) {
            $configuredApis = json_decode($settings['news_apis_json'], true) ?: [];
        }
        $newsPortals = [];
        foreach ($portalRows as $pr) {
            $pkey = $pr['portal_key'];
            $newsPortals[$pkey] = [
                'name'      => $pr['portal_name'],
                'url'       => $pr['portal_url'],
                'is_active' => (bool)$pr['is_active'],
                'api_url'   => $configuredApis[$pkey] ?? null,
            ];
        }

        // 3d. Event Highlights (Multi-platform: YouTube, TikTok, Instagram)
        $highlightModel = new \App\Models\EventHighlightModel();
        $highlightRows = $highlightModel->getByEventId($event['id'], true);
        $highlightItems = [];
        foreach ($highlightRows as $hr) {
            $rawUrl = $hr['youtube_url'] ?? '';
            $vid    = $hr['video_id'] ?? '';

            // Platform detection
            $platform = 'youtube';
            if (preg_match('%tiktok\.com%i', $rawUrl)) {
                $platform = 'tiktok';
            } elseif (preg_match('%(?:instagram\.com|instagr\.am)%i', $rawUrl)) {
                $platform = 'instagram';
            }

            // Thumbnail resolution
            $localFile = FCPATH . 'uploads/highlights/' . $hr['id'] . '.jpg';
            if (file_exists($localFile)) {
                $thumbUrl = base_url('uploads/highlights/' . $hr['id'] . '.jpg');
            } elseif ($platform === 'youtube' && $vid) {
                $thumbUrl = "https://i.ytimg.com/vi/{$vid}/hqdefault.jpg";
            } else {
                $thumbUrl = base_url('assets/img/image1.png');
            }

            $watchUrl = $rawUrl;
            if ($platform === 'youtube' && $vid && !str_starts_with($rawUrl, 'http')) {
                $watchUrl = "https://www.youtube.com/watch?v={$vid}";
            }

            $highlightItems[] = [
                'id'            => (int)$hr['id'],
                'title'         => $hr['title'],
                'platform'      => $platform,
                'url'           => $watchUrl,
                'youtube_url'   => $watchUrl,
                'video_id'      => $vid,
                'thumbnail_url' => $thumbUrl,
                'watch_url'     => $watchUrl,
                'embed_url'     => ($platform === 'youtube' && $vid) ? "https://www.youtube.com/embed/{$vid}" : null,
                'sort_order'    => (int)$hr['sort_order'],
            ];
        }
        $highlights = [
            'is_active' => !empty($highlightItems),
            'items'     => $highlightItems,
        ];

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
            $daySessions = $sessionModel->getSessionsByDay((int)$day['id'], true);

            $formattedSessions = [];
            foreach ($daySessions as $sess) {
                $sessionItem = [
                    'id'           => (int)$sess['id'],
                    'title'        => $sess['title'],
                    'subtitle'     => $sess['subtitle'] ?? '',
                    'description'  => $sess['description'] ?? '',
                    'time'         => format_schedule_time(null, $sess['start_time'], $sess['end_time']),
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
                        'label' => 'Moderator:',
                        'name'  => $m['name'],
                        'role'  => $m['role_text'] ?? '',
                        'photo' => $makeUrl($m['photo_url'] ?? ''),
                    ];
                }

                // Pengantar
                $sessionItem['pengantar'] = [];
                $sessionPengantar = $sessionModel->getSessionPengantar((int)$sess['id']);
                foreach ($sessionPengantar as $peng) {
                    $pSlug = $peng['slug'] ?? url_title($peng['name'], '-', true);
                    $sessionItem['pengantar'][] = [
                        'id'    => (int)$peng['pengantar_id'],
                        'slug'  => $pSlug,
                        'name'  => $peng['name'],
                        'role'  => $peng['role_text'] ?? '',
                        'photo' => $makeUrl($peng['photo_url'] ?? ''),
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
                                    'label' => 'Moderator:',
                                    'name'  => $cm['name'],
                                    'role'  => $cm['role_text'] ?? '',
                                    'photo' => $makeUrl($cm['photo_url'] ?? ''),
                                ];
                            }

                            foreach ($it['speakers'] as $cspk) {
                                $cSlug = !empty($cspk['slug']) ? $cspk['slug'] : url_title($cspk['name'] ?? ('speaker-' . $cspk['speaker_id']), '-', true);
                                $subItem['speaker_ids'][] = $cSlug;
                                $subItem['speakers'][] = [
                                    'id'    => (int)$cspk['speaker_id'],
                                    'slug'  => $cSlug,
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
                                'label' => 'Moderator:',
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
                'date'       => $rawDayDate ? date('F j, Y', strtotime($rawDayDate)) : '',
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
        $sponsorDisplayMode = $settings['sponsor_display_mode'] ?? 'dedicated';

        $sponsorPartnership = null;
        if ($spRow) {
            $isPackageActive = ($sponsorDisplayMode === 'package') && (isset($spRow['is_active']) ? (bool)$spRow['is_active'] : true);
            $sponsorPartnership = [
                'id'                   => (int)$spRow['id'],
                'name'                 => $spRow['name'] ?? '',
                'logo_url'             => $makeUrl($spRow['logo_url']),
                'is_active'            => $isPackageActive,
                'sponsor_display_mode' => $sponsorDisplayMode,
                'updated_at'           => $spRow['updated_at'] ?? '',
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
                'is_rotated'          => (bool)$dec['is_rotated'],
                'rotation_direction'  => $dec['rotation_direction'] ?? 'clockwise',
                'perspective_enabled' => (bool)($dec['perspective_enabled'] ?? false),
                'perspective_value'   => (int)($dec['perspective_value'] ?? 800),
                'rotate_x'            => (float)($dec['rotate_x'] ?? 0),
                'rotate_y'            => (float)($dec['rotate_y'] ?? 0),
                'rotate_z'            => (float)($dec['rotate_z'] ?? 0),
                'tint_color'          => $dec['tint_color'] ?? null,
                'show_on_mobile'      => (bool)$dec['show_on_mobile'],
                'device_visibility'   => $dec['device_visibility'] ?? (!empty($dec['show_on_mobile']) ? 'all' : 'desktop_only'),
            ];
        }

        // Tier-Based Dedicated Sponsors (Platinum, Gold, Silver, Bronze, Collaborations, Partnerships)
        $sponsors = [
            'display_mode'   => $sponsorDisplayMode,
            'sponsored_by'   => [],
            'collaborations' => [],
            'partnerships'   => [],
        ];

        if ($sponsorDisplayMode === 'dedicated') {
            $tiers = $tierModel->getByEvent($event['id']);
            foreach ($tiers as $tier) {
                $tierSponsors = $sponsorModel->getByTier((int)$tier['id']);
                $sponsorItems = [];
                foreach ($tierSponsors as $ts) {
                    $sponsorItems[] = [
                        'id'          => (int)$ts['id'],
                        'name'        => $ts['name'],
                        'logo'        => $makeUrl($ts['logo_url']),
                        'website_url' => $ts['link_url'] ?? $ts['website_url'] ?? '',
                    ];
                }
                $gKey = $tier['group_key'] ?? 'sponsor';
                if ($gKey === 'partnerships' || $gKey === 'partner') {
                    $targetGroup = 'partnerships';
                } elseif ($gKey === 'collaboration' || $gKey === 'collaborations') {
                    $targetGroup = 'collaborations';
                } else {
                    $targetGroup = 'sponsored_by';
                }

                $sponsors[$targetGroup][] = [
                    'tier_id'     => (int)$tier['id'],
                    'tier_name'   => $tier['name'],
                    'name'        => $tier['name'],
                    'group_key'   => $gKey,
                    'group_label' => $tier['group_label'],
                    'logo_size'   => $tier['logo_size'],
                    'items'       => $sponsorItems,
                ];
            }
        }

        // 10. Custom Placements (Freeform Coordinates)
        $placementModel = new \App\Models\CustomPlacementModel();
        $placementRows = $placementModel->getActiveByEvent($event['id']);
        $customPlacements = [];
        foreach ($placementRows as $cp) {
            $customPlacements[] = [
                'id'                  => (int)$cp['id'],
                'name'                => $cp['name'] ?? '',
                'section_key'         => $cp['section_key'],
                'image_url'           => $makeUrl($cp['image_url']),
                'pos_x_percent'       => (float)$cp['pos_x_percent'],
                'pos_y_percent'       => (float)$cp['pos_y_percent'],
                'size_px'             => (int)$cp['size_px'],
                'opacity'             => (float)$cp['opacity'],
                'is_rotated'          => (bool)$cp['is_rotated'],
                'rotation_direction'  => $cp['rotation_direction'] ?? 'clockwise',
                'perspective_enabled' => (bool)($cp['perspective_enabled'] ?? false),
                'perspective_value'   => (int)($cp['perspective_value'] ?? 800),
                'rotate_x'            => (float)($cp['rotate_x'] ?? 0),
                'rotate_y'            => (float)($cp['rotate_y'] ?? 0),
                'rotate_z'            => (float)($cp['rotate_z'] ?? 0),
                'tint_color'          => $cp['tint_color'] ?? null,
                'show_on_mobile'      => (bool)$cp['show_on_mobile'],
                'device_visibility'   => $cp['device_visibility'] ?? (!empty($cp['show_on_mobile']) ? 'all' : 'desktop_only'),
                'z_index'             => (int)($cp['z_index'] ?? 1),
            ];
        }

        // 13. Event Locations & Floor Plan
        $locationModel = new \App\Models\EventLocationModel();
        $locRow = $locationModel->getByEventId($event['id']) ?? [];
        $locations = [
            'is_active'        => (!isset($locRow['is_active']) || !empty($locRow['is_active'])),
            'hotel_name'       => $locRow['hotel_name'] ?? 'Raffles Hotel Jakarta',
            'hotel_address'    => $locRow['hotel_address'] ?? 'Ciputra World, Jl. Prof. DR. Satrio, Kuningan, Karet Kuningan, Kecamatan Setiabudi, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12940',
            'gmaps_iframe'     => $locRow['gmaps_iframe'] ?? '',
            'layout_image_url' => $makeUrl($locRow['layout_image_url'] ?? ''),
        ];

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
                'livestream'          => $livestream,
                'livestreams'         => $livestreams,
                'news_portals'        => $newsPortals,
                'highlights'          => $highlights,
                'speakers'            => $speakers,
                'moderators'          => $moderators,
                'schedule'            => $schedule,
                'gallery'             => $gallery,
                'locations'           => $locations,
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
        $slug = $this->request->getGet('event_slug') ?? $this->request->getPost('event_slug');
        if (empty($slug)) {
            $rawBody = $this->request->getBody();
            if (!empty($rawBody)) {
                $json = json_decode($rawBody, true);
                if (!empty($json['event_slug'])) {
                    $slug = $json['event_slug'];
                }
            }
        }

        $event = !empty($slug) ? $eventModel->where('slug', $slug)->first() : null;
        if (!$event) {
            $event = $eventModel->getActiveEvent();
        }

        if (!$event) {
            return $this->failNotFound('No active event');
        }

        $clickModel = new TicketClickLogModel();
        $clickModel->insert([
            'event_id'   => $event['id'],
            'clicked_at' => date('Y-m-d H:i:s'),
            'user_agent' => substr((string)$this->request->getUserAgent(), 0, 500),
            'referrer'   => substr((string)$this->request->getServer('HTTP_REFERER'), 0, 500),
        ]);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Ticket click telemetry logged',
        ]);
    }
}

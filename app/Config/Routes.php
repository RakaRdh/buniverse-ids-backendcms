<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Root redirect
$routes->get('/', function () {
    return redirect()->to(base_url('auth/login'));
});

// Authentication Routes (Guest)
$routes->group('auth', function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::processLogin');
    $routes->get('logout', 'AuthController::logout');
});

// Admin Protected Routes (Session Auth)
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->post('events/switch', 'Admin\EventSwitchController::switch');

    // Event Settings (General)
    $routes->get('event-settings', 'Admin\EventSettingsController::index');
    $routes->post('event-settings/update', 'Admin\EventSettingsController::update');

    // Navbar Settings & CTA
    $routes->get('navbar', 'Admin\NavbarController::index');
    $routes->post('navbar/update', 'Admin\NavbarController::update');

    // Master Logos & Placements (Centralized Brand Asset Repository)
    $routes->get('master-logos', 'Admin\MasterLogoController::index');
    $routes->post('master-logos/save', 'Admin\MasterLogoController::saveLogo');
    $routes->get('master-logos/delete/(:num)', 'Admin\MasterLogoController::deleteLogo/$1');
    $routes->post('master-logos/placements/save', 'Admin\MasterLogoController::savePlacements');

    // Hero Banner & Venue (Dedicated Module)
    $routes->get('hero', 'Admin\HeroController::index');
    $routes->post('hero/venue', 'Admin\HeroController::updateVenue');
    $routes->post('hero/upload-image', 'Admin\HeroController::uploadHeroImage');
    $routes->post('hero/delete-image/(:num)', 'Admin\HeroController::deleteHeroImage/$1');

    // Overview (Dedicated Module)
    $routes->get('overview', 'Admin\OverviewController::index');
    $routes->post('overview/update', 'Admin\OverviewController::update');

    // Footer Settings & Social Links
    $routes->get('footer', 'Admin\FooterController::index');
    $routes->post('footer/update', 'Admin\FooterController::update');

    // Meta & SEO Configuration
    $routes->get('meta', 'Admin\MetaController::index');
    $routes->post('meta/update', 'Admin\MetaController::update');

    // Color Schema & Theme CSS Variables
    $routes->get('color-schema', 'Admin\ColorSchemaController::index');
    $routes->post('color-schema/update', 'Admin\ColorSchemaController::update');

    // Speakers
    $routes->get('speakers', 'Admin\SpeakerController::index');
    $routes->get('speakers/new', 'Admin\SpeakerController::create');
    $routes->post('speakers/save', 'Admin\SpeakerController::save');
    $routes->post('speakers/quick-save', 'Admin\SpeakerController::quickSave');
    $routes->get('speakers/edit/(:num)', 'Admin\SpeakerController::edit/$1');
    $routes->post('speakers/update/(:num)', 'Admin\SpeakerController::update/$1');
    $routes->post('speakers/delete/(:num)', 'Admin\SpeakerController::delete/$1');
    $routes->post('speakers/reorder', 'Admin\SpeakerController::reorder');

    // Moderators
    $routes->get('moderators', 'Admin\ModeratorController::index');
    $routes->get('moderators/new', 'Admin\ModeratorController::create');
    $routes->post('moderators/save', 'Admin\ModeratorController::save');
    $routes->post('moderators/quick-save', 'Admin\ModeratorController::quickSave');
    $routes->get('moderators/edit/(:num)', 'Admin\ModeratorController::edit/$1');
    $routes->post('moderators/update/(:num)', 'Admin\ModeratorController::update/$1');
    $routes->post('moderators/delete/(:num)', 'Admin\ModeratorController::delete/$1');
    $routes->post('moderators/reorder', 'Admin\ModeratorController::reorder');

    // Rooms
    $routes->get('rooms', 'Admin\RoomController::index');
    $routes->post('rooms/save', 'Admin\RoomController::save');
    $routes->post('rooms/update/(:num)', 'Admin\RoomController::update/$1');
    $routes->post('rooms/delete/(:num)', 'Admin\RoomController::delete/$1');

    // Schedules
    $routes->get('schedules', 'Admin\ScheduleController::index');
    $routes->post('schedules/day/save', 'Admin\ScheduleController::saveDay');
    $routes->post('schedules/room/save', 'Admin\ScheduleController::saveRoom');
    $routes->get('schedules/session/new', 'Admin\ScheduleController::createSession');
    $routes->post('schedules/session/save', 'Admin\ScheduleController::saveSession');
    $routes->get('schedules/session/edit/(:num)', 'Admin\ScheduleController::editSession/$1');
    $routes->post('schedules/session/update/(:num)', 'Admin\ScheduleController::updateSession/$1');
    $routes->post('schedules/session/delete/(:num)', 'Admin\ScheduleController::deleteSession/$1');

    // Gallery
    $routes->get('gallery', 'Admin\GalleryController::index');
    $routes->post('gallery/upload', 'Admin\GalleryController::upload');
    $routes->post('gallery/delete/(:num)', 'Admin\GalleryController::delete/$1');
    $routes->post('gallery/toggle-featured/(:num)', 'Admin\GalleryController::toggleFeatured/$1');

    // Event Highlights
    $routes->get('highlights', 'Admin\HighlightController::index');
    $routes->post('highlights/save', 'Admin\HighlightController::save');
    $routes->post('highlights/update/(:num)', 'Admin\HighlightController::update/$1');
    $routes->post('highlights/toggle/(:num)', 'Admin\HighlightController::toggle/$1');
    $routes->post('highlights/delete/(:num)', 'Admin\HighlightController::delete/$1');

    // Sponsors & Partnerships (Single Image Upload)
    $routes->get('sponsors', 'Admin\SponsorController::index');
    $routes->post('sponsors/save', 'Admin\SponsorController::save');
    $routes->post('sponsors/delete/(:num)', 'Admin\SponsorController::delete/$1');
    $routes->get('sponsors/delete/(:num)', 'Admin\SponsorController::delete/$1');
    /* === LEGACY TIER-BASED SPONSORS ROUTES (COMMENTED OUT) ===
    $routes->post('sponsors/tier/save', 'Admin\SponsorController::saveTier');
    $routes->post('sponsors/tier/delete/(:num)', 'Admin\SponsorController::deleteTier/$1');
    === END LEGACY ROUTES === */

    // Live Stream Settings (Support both singular 'livestream' and plural 'livestreams')
    $routes->get('livestreams', 'Admin\LivestreamController::index');
    $routes->post('livestreams/update', 'Admin\LivestreamController::update');
    $routes->get('livestream', 'Admin\LivestreamController::index');
    $routes->post('livestream/update', 'Admin\LivestreamController::update');

    // News Portals Links
    $routes->get('news-portals', 'Admin\NewsPortalController::index');
    $routes->post('news-portals/update', 'Admin\NewsPortalController::update');

    // Section Decorations (Cakram / Disc)
    $routes->get('decorations', 'Admin\DecorationController::index');
    $routes->post('decorations/save', 'Admin\DecorationController::save');
    $routes->post('decorations/delete/(:num)', 'Admin\DecorationController::delete/$1');
    $routes->post('decorations/toggle-active/(:num)', 'Admin\DecorationController::toggleActive/$1');

    // Custom Placement (Freeform Coordinates)
    $routes->get('custom-placements', 'Admin\CustomPlacementController::index');
    $routes->post('custom-placements/save', 'Admin\CustomPlacementController::save');
    $routes->post('custom-placements/delete/(:num)', 'Admin\CustomPlacementController::delete/$1');
    $routes->post('custom-placements/toggle/(:num)', 'Admin\CustomPlacementController::toggle/$1');

    // Events Manager
    $routes->get('events', 'Admin\EventController::index');
    $routes->get('events/manage/(:num)', 'Admin\EventController::manage/$1');
    $routes->post('events/save', 'Admin\EventController::save');
    $routes->post('events/update/(:num)', 'Admin\EventController::update/$1');

    // Admin Users (Superadmin only)
    $routes->group('users', ['filter' => 'auth:superadmin'], function ($routes) {
        $routes->get('', 'Admin\UserController::index');
        $routes->post('save', 'Admin\UserController::save');
        $routes->post('delete/(:num)', 'Admin\UserController::delete/$1');
    });

    // Profile & Password Change (All logged in admins)
    $routes->get('profile', 'Admin\ProfileController::index');
    $routes->post('profile/update-password', 'Admin\ProfileController::updatePassword');
});

// Public REST API Routes for Frontend
$routes->group('api/v1', function ($routes) {
    $routes->get('event/active', 'Api\EventApiController::active');
    $routes->get('event/(:num)', 'Api\EventApiController::show/$1');
    $routes->post('tickets/click', 'Api\EventApiController::logTicketClick');
});

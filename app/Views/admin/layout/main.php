<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Executive Dashboard') ?> — IDS Executive CMS</title>
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-hash" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= config('Security')->headerName ?? 'X-CSRF-TOKEN' ?>">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>?v=<?= file_exists(FCPATH . 'favicon.ico') ? filemtime(FCPATH . 'favicon.ico') : time() ?>">
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>?v=<?= file_exists(FCPATH . 'favicon.ico') ? filemtime(FCPATH . 'favicon.ico') : time() ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=' . time()) ?>">
</head>
<body>
    <div class="app-shell">
        <!-- Fixed Left Sidebar 280px (§6.1) -->
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <?php
                    if (!empty($isPortalMode)) {
                        $brandMark = 'CMS';
                        $brandTitle = 'Global Event CMS';
                    } else {
                        $titleStr = $activeEvent['title'] ?? 'Investor Daily Summit';
                        // Ambil inisial/akronim dari judul event (misal: Investor Daily Summit -> IDS, Energy and Mining Forum -> EMF)
                        $words = preg_split('/[\s\-_]+/', trim(preg_replace('/[0-9]+/', '', $titleStr)));
                        $acronym = '';
                        foreach ($words as $w) {
                            if (!empty($w)) $acronym .= strtoupper($w[0]);
                        }
                        $brandMark = !empty($acronym) ? substr($acronym, 0, 4) : 'EVT';
                        $brandTitle = $titleStr;
                    }
                ?>
                <a href="<?= base_url('admin/events') ?>" class="brand-mark-group" title="<?= esc($brandTitle) ?>">
                    <div class="brand-mark"><?= esc($brandMark) ?></div>
                    <div class="brand-text"><?= esc($brandTitle) ?></div>
                </a>
                <button type="button" class="btn-sidebar-close" id="btnSidebarClose" aria-label="Close Navigation Menu">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <?php if (!empty($isPortalMode)): ?>
                    <div class="nav-section-label">Portal</div>
                    <a href="<?= base_url('admin/events') ?>" class="nav-item <?= ($activeMenu ?? '') === 'events' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>Event Editions</span>
                    </a>

                    <div class="nav-section-label">System</div>
                    <a href="<?= base_url('admin/users') ?>" class="nav-item <?= ($activeMenu ?? '') === 'users' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                        <span>Admin Users</span>
                    </a>
                <?php else: ?>
                    <div class="nav-section-label">Main</div>
                    <a href="<?= base_url('admin/dashboard') ?>" class="nav-item <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>Dashboard</span>
                    </a>

                    <div class="nav-section-label">Event configuration</div>
                    <a href="<?= base_url('admin/event-settings') ?>" class="nav-item <?= ($activeMenu ?? '') === 'event-settings' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        <span>General Settings</span>
                    </a>
                    <a href="<?= base_url('admin/navbar') ?>" class="nav-item <?= ($activeMenu ?? '') === 'navbar' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="6" rx="1.5"></rect><line x1="3" y1="13" x2="21" y2="13"></line><line x1="3" y1="18" x2="14" y2="18"></line></svg>
                        <span>Navbar</span>
                    </a>
                    <a href="<?= base_url('admin/master-logos') ?>" class="nav-item <?= ($activeMenu ?? '') === 'master_logos' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span>Master Logos</span>
                    </a>
                    <a href="<?= base_url('admin/hero') ?>" class="nav-item <?= ($activeMenu ?? '') === 'hero' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                        <span>Hero Banner & Venue</span>
                    </a>
                    <a href="<?= base_url('admin/overview') ?>" class="nav-item <?= ($activeMenu ?? '') === 'overview' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span>Overview</span>
                    </a>
                    <a href="<?= base_url('admin/locations') ?>" class="nav-item <?= ($activeMenu ?? '') === 'locations' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>Locations & Floor Plan</span>
                    </a>
                    <a href="<?= base_url('admin/footer') ?>" class="nav-item <?= ($activeMenu ?? '') === 'footer' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="15" width="18" height="6" rx="1.5"></rect><line x1="3" y1="6" x2="14" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>Footer</span>
                    </a>
                    <a href="<?= base_url('admin/meta') ?>" class="nav-item <?= ($activeMenu ?? '') === 'meta' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        <span>Meta (SEO)</span>
                    </a>
                    <a href="<?= base_url('admin/color-schema') ?>" class="nav-item <?= ($activeMenu ?? '') === 'color_schema' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2z"></path><path d="M12 2v20"></path><path d="M12 12l7-7"></path></svg>
                        <span>Color Schema</span>
                    </a>

                    <div class="nav-section-label">Program & lineup</div>
                    <a href="<?= base_url('admin/schedules') ?>" class="nav-item <?= ($activeMenu ?? '') === 'schedules' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>Schedule Sessions</span>
                    </a>
                    <a href="<?= base_url('admin/rooms') ?>" class="nav-item <?= ($activeMenu ?? '') === 'rooms' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span>Room Lists</span>
                    </a>
                    <a href="<?= base_url('admin/speakers') ?>" class="nav-item <?= ($activeMenu ?? '') === 'speakers' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
                        <span>Speakers</span>
                    </a>
                    <a href="<?= base_url('admin/pengantar') ?>" class="nav-item <?= ($activeMenu ?? '') === 'pengantar' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Pengantar</span>
                    </a>
                    <a href="<?= base_url('admin/moderators') ?>" class="nav-item <?= ($activeMenu ?? '') === 'moderators' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Moderators</span>
                    </a>
                    <a href="<?= base_url('admin/qna') ?>" class="nav-item <?= ($activeMenu ?? '') === 'qna' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        <span>Q&A Questions</span>
                    </a>

                    <div class="nav-section-label">Media & partners</div>
                    <a href="<?= base_url('admin/livestreams') ?>" class="nav-item <?= ($activeMenu ?? '') === 'livestreams' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                        <span>Live Stream</span>
                    </a>
                    <a href="<?= base_url('admin/news-portals') ?>" class="nav-item <?= ($activeMenu ?? '') === 'news_portals' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        <span>News Portals</span>
                    </a>
                    <a href="<?= base_url('admin/gallery') ?>" class="nav-item <?= ($activeMenu ?? '') === 'gallery' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span>Gallery</span>
                    </a>
                    <a href="<?= base_url('admin/highlights') ?>" class="nav-item <?= ($activeMenu ?? '') === 'highlights' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                        <span>Highlights</span>
                    </a>
                    <a href="<?= base_url('admin/sponsors') ?>" class="nav-item <?= ($activeMenu ?? '') === 'sponsors' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Sponsors & Partners</span>
                    </a>
                    <a href="<?= base_url('admin/decorations') ?>" class="nav-item <?= ($activeMenu ?? '') === 'decorations' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                        <span>Section Decorations</span>
                    </a>
                    <a href="<?= base_url('admin/custom-placements') ?>" class="nav-item <?= ($activeMenu ?? '') === 'custom-placements' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v3m0 14v3M2 12h3m14 0h3"></path></svg>
                        <span>Custom Placement</span>
                    </a>

                    <div class="nav-section-label">System</div>
                    <a href="<?= base_url('admin/users') ?>" class="nav-item <?= ($activeMenu ?? '') === 'users' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                        <span>Admin Users</span>
                    </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= base_url('admin/profile') ?>" class="user-block" style="text-decoration:none; cursor:pointer;" title="Edit Profile & Change Password">
                    <div class="user-avatar">
                        <?= strtoupper(substr($currentAdmin['username'] ?? $currentAdmin['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-meta-name"><?= esc($currentAdmin['username'] ?? $currentAdmin['name'] ?? 'Administrator') ?></div>
                        <div class="user-meta-role"><?= esc($currentAdmin['role'] ?? 'editor') ?></div>
                    </div>
                </a>
                <div style="display:flex; align-items:center; gap:4px;">
                    <a href="<?= base_url('admin/profile') ?>" class="btn-logout" title="Profile & Password">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </a>
                    <form action="<?= base_url('auth/logout') ?>" method="POST" style="display:inline; margin:0; padding:0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-logout" title="Sign out" style="background:none; border:none; cursor:pointer; padding:0; display:flex; align-items:center; justify-content:center;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <div class="app-main">
            <!-- Top Utility Bar 48px (§6.2) -->
            <header class="app-topbar">
                <div class="topbar-left">
                    <button type="button" class="btn-sidebar-toggle" id="btnSidebarToggle" aria-label="Toggle Navigation Menu">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="topbar-event-title">
                        <?php if (!empty($isPortalMode)): ?>
                            <span class="topbar-prefix">Portal:</span>
                            <strong>All Event Editions</strong>
                        <?php else: ?>
                            <span class="topbar-prefix">Edition:</span>
                            <strong><?= esc($activeEvent['title'] ?? 'Investor Daily Summit') ?> (<?= esc($activeEvent['year'] ?? date('Y')) ?>)</strong>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="topbar-right">
                    <?php if (empty($isPortalMode)): ?>
                        <a href="<?= base_url('admin/events') ?>" class="btn-topbar-edition" title="Switch Event Edition">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Event Editions</span>
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Backdrop for Mobile Sidebar Drawer -->
            <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

            <!-- Page Content Canvas (max-width 1160px left-aligned) -->
            <main class="app-content">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <span><?= esc(session()->getFlashdata('success')) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error">
                        <span><?= esc(session()->getFlashdata('error')) ?></span>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <!-- Sidebar Desktop Collapse & Mobile Drawer Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('btnSidebarToggle');
        const sidebar = document.querySelector('.app-sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        // Restore desktop collapsed state from localStorage
        if (window.innerWidth > 768) {
            if (localStorage.getItem('cms_sidebar_collapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }
        }

        function openDrawer() {
            if (sidebar) sidebar.classList.add('is-open');
            if (backdrop) backdrop.classList.add('is-open');
            document.body.classList.add('sidebar-drawer-open');
        }

        function closeDrawer() {
            if (sidebar) sidebar.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('is-open');
            document.body.classList.remove('sidebar-drawer-open');
        }

        const closeBtn = document.getElementById('btnSidebarClose');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    if (sidebar && sidebar.classList.contains('is-open')) {
                        closeDrawer();
                    } else {
                        openDrawer();
                    }
                } else {
                    // Desktop collapse toggle
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('cms_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                }
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeDrawer);
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeDrawer);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('is-open')) {
                closeDrawer();
            }
        });

        // Retain sidebar scroll position across navigation (CMS 5)
        const sidebarNav = document.querySelector('.sidebar-nav');
        if (sidebarNav) {
            const savedScroll = sessionStorage.getItem('cms_sidebar_scroll');
            if (savedScroll !== null) {
                sidebarNav.scrollTop = parseInt(savedScroll, 10);
            }

            // Also ensure active nav item is visible
            const activeItem = sidebarNav.querySelector('.nav-item.active');
            if (activeItem) {
                activeItem.scrollIntoView({ block: 'nearest' });
            }

            // Save on click or beforeunload
            sidebarNav.addEventListener('scroll', () => {
                sessionStorage.setItem('cms_sidebar_scroll', sidebarNav.scrollTop);
            }, { passive: true });

            sidebarNav.querySelectorAll('.nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('cms_sidebar_scroll', sidebarNav.scrollTop);
                });
            });
        }

        // Auto-scroll and highlight target row when URL has hash anchor (e.g. #speaker-12, #moderator-5, #pengantar-3, #session-4)
        if (window.location.hash) {
            try {
                const targetEl = document.querySelector(window.location.hash);
                if (targetEl) {
                    setTimeout(() => {
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        targetEl.classList.add('row-anchor-highlight');
                        setTimeout(() => {
                            targetEl.classList.remove('row-anchor-highlight');
                        }, 2500);
                    }, 150);
                }
            } catch (e) {}
        }
    });
    </script>
    <style>
    @keyframes anchorGlow {
        0% { background-color: rgba(212, 175, 55, 0.25); }
        70% { background-color: rgba(212, 175, 55, 0.12); }
        100% { background-color: transparent; }
    }
    .row-anchor-highlight {
        animation: anchorGlow 2.5s ease-out forwards !important;
    }
    </style>
</body>
</html>


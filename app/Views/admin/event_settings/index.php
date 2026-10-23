<div class="page-header">
    <div>
        <h1 class="page-title">Event Settings & Content</h1>
        <p class="page-subtitle">Configure core edition metadata, hero banner, narrative copy, branding, and links for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
</div>

<?php /*
<!-- Sub-navigation Tabs -->
<div class="filter-tabs" style="margin-bottom: 24px;">
    <a href="<?= base_url('admin/event-settings?tab=general') ?>" class="filter-tab <?= ($activeTab === 'general') ? 'active' : '' ?>">
        General Settings
    </a>
    <a href="<?= base_url('admin/navbar') ?>" class="filter-tab">
        Navbar Settings &rarr;
    </a>
    <a href="<?= base_url('admin/footer') ?>" class="filter-tab">
        Footer Settings &rarr;
    </a>
</div>
*/ ?>

<?php if ($activeTab === 'general'): ?>
    <?php /* TAB 1: GENERAL (events table) */ ?>
    <div class="cms-card">
        <div class="card-title-bar">
            <span class="card-title-text">General Event Edition Settings</span>
        </div>
        <div class="cms-card-body">
            <form action="<?= base_url('admin/event-settings/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="tab" value="general">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Event Year</label>
                        <input type="text" class="form-control" value="<?= esc($event['year'] ?? '') ?>" disabled style="font-variant-numeric: tabular-nums;">
                        <div class="help-text">Year is immutable once created. Manage editions in Events Manager.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lifecycle Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" <?= (($event['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= (($event['status'] ?? '') === 'published') ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= (($event['status'] ?? '') === 'archived') ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Event Title *</label>
                    <input type="text" name="title" class="form-control" required value="<?= esc($event['title'] ?? $event['name'] ?? '') ?>" placeholder="INVESTOR DAILY SUMMIT 2026">
                </div>

                <div class="form-group">
                    <label class="form-label">Annual Theme *</label>
                    <input type="text" name="theme" class="form-control" required value="<?= esc($event['theme'] ?? '') ?>" placeholder="INDONESIA'S ECONOMIC MODEL">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Event Start Date</label>
                        <input type="date" name="event_date_start" class="form-control" value="<?= esc($event['event_date_start'] ?? $event['start_date'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Event End Date</label>
                        <input type="date" name="event_date_end" class="form-control" value="<?= esc($event['event_date_end'] ?? $event['end_date'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">News Tag Slug (for B-Universe Portal API)</label>
                        <input type="text" name="news_tag_slug" class="form-control" value="<?= esc($event['news_tag_slug'] ?? 'ids2026') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Template Version</label>
                        <input type="text" name="template_version" class="form-control" value="<?= esc($event['template_version'] ?? 'v2026') ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Visitor Registration Quota</label>
                        <input type="number" name="quota" class="form-control" min="0" value="<?= esc($event['quota'] ?? '') ?>" placeholder="Kosongkan atau 0 jika tidak terbatas">
                        <div class="help-text">Batas maksimal kuota pengunjung terdaftar. Jika pendaftar mencapai batas, form menolak pendaftaran baru secara otomatis.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gate Passcode (6 Digit PIN)</label>
                        <input type="text" name="gate_passcode" class="form-control" maxlength="10" value="<?= esc($event['gate_passcode'] ?? '123456') ?>" placeholder="123456">
                        <div class="help-text">PIN 6 digit untuk membuka dan memvalidasi sesi scanner panitia resepsionis di venue.</div>
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--line);">
                    <button type="submit" class="btn btn-primary">Save General Settings</button>
                </div>
            </form>
        </div>
    </div>

<?php elseif ($activeTab === 'brand'): ?>
    <!-- TAB 4: BRAND & LINKS (event_settings table) -->
    <form action="<?= base_url('admin/event-settings/update') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="tab" value="brand">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Ticket & Brand UI Labels -->
            <div class="cms-card">
                <div class="card-title-bar">
                    <span class="card-title-text">Buttons, Registration & Copyright</span>
                </div>
                <div class="cms-card-body">
                    <div class="form-group">
                        <label class="form-label">Navbar CTA Button Label</label>
                        <input type="text" name="cta_label" class="form-control" value="<?= esc($settings['cta_label'] ?? 'Get Tickets') ?>" placeholder="Get Tickets">
                        <div class="help-text">Text displayed on the primary action button in the header navbar.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Primary Ticket Registration URL</label>
                        <input type="url" name="ticket_url" class="form-control" value="<?= esc($settings['ticket_url'] ?? '') ?>" placeholder="https://megatix.co.id/events/ids2026">
                        <div class="help-text">Target URL when users click the CTA button. Clicks are logged.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Footer Copyright Text</label>
                        <input type="text" name="copyright_text" class="form-control" value="<?= esc($settings['copyright_text'] ?? 'Investor Daily Summit.') ?>" placeholder="Investor Daily Summit.">
                        <div class="help-text">Rendered as: <code>&copy; Copyright <?= date('Y') ?>. [Your Text]</code></div>
                    </div>

                    <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--line);">
                        <label class="form-label" style="margin-bottom: 8px;">Social Media URLs</label>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 12px; font-weight: normal;">Instagram URL</label>
                            <input type="url" name="instagram_url" class="form-control" value="<?= esc($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/investordaily">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 12px; font-weight: normal;">YouTube URL</label>
                            <input type="url" name="youtube_url" class="form-control" value="<?= esc($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/@investordaily">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 12px; font-weight: normal;">Facebook URL</label>
                            <input type="url" name="facebook_url" class="form-control" value="<?= esc($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/investordaily">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 12px; font-weight: normal;">LinkedIn URL</label>
                            <input type="url" name="linkedin_url" class="form-control" value="<?= esc($settings['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/company/investordaily">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Brand Logos & Navigation Items -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="cms-card">
                    <div class="card-title-bar">
                        <span class="card-title-text">Brand Logos (Header & Footer)</span>
                    </div>
                    <div class="cms-card-body">
                        <div class="form-group">
                            <label class="form-label">Summit Event Logo (Logo A - Header & Hero)</label>
                            <?php if (!empty($settings['logo_a_url'])): ?>
                                <div style="background: var(--primary-1-7); padding: 10px 14px; border-radius: var(--radius-control); margin-bottom: 8px; display: inline-block; border: 1px solid var(--line);">
                                    <img src="<?= base_url(esc($settings['logo_a_url'])) ?>" alt="Logo A" style="max-height: 38px; display: block;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo_a_file" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group" style="margin-top: 14px;">
                            <label class="form-label">Media Partner Logo (Logo B - Header Lockup)</label>
                            <?php if (!empty($settings['logo_b_url'])): ?>
                                <div style="background: var(--primary-1-7); padding: 10px 14px; border-radius: var(--radius-control); margin-bottom: 8px; display: inline-block; border: 1px solid var(--line);">
                                    <img src="<?= base_url(esc($settings['logo_b_url'])) ?>" alt="Logo B" style="max-height: 38px; display: block;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo_b_file" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group" style="margin-top: 14px;">
                            <label class="form-label">Footer Brand Logo (Optional, defaults to Logo B)</label>
                            <?php if (!empty($settings['logo_footer_url'])): ?>
                                <div style="background: var(--primary-1-7); padding: 10px 14px; border-radius: var(--radius-control); margin-bottom: 8px; display: inline-block; border: 1px solid var(--line);">
                                    <img src="<?= base_url(esc($settings['logo_footer_url'])) ?>" alt="Logo Footer" style="max-height: 38px; display: block;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo_footer_file" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- Navigation Links Editor -->
                <div class="cms-card">
                    <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="card-title-text">Header Navigation Items</span>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnAddNavItem">+ Add Link</button>
                    </div>
                    <div class="cms-card-body">
                        <?php
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
                        ?>
                        <div id="navItemsContainer" style="display: flex; flex-direction: column; gap: 8px;">
                            <?php foreach ($navItems as $ni): ?>
                                <div class="nav-item-row" style="display: flex; gap: 8px; align-items: center;">
                                    <input type="text" name="nav_label[]" class="form-control" value="<?= esc($ni['label']) ?>" placeholder="Label (e.g. Schedule)" style="flex: 1;">
                                    <input type="text" name="nav_url[]" class="form-control" value="<?= esc($ni['url']) ?>" placeholder="Anchor/URL (e.g. #schedule)" style="flex: 1.2;">
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-nav" style="padding: 6px 10px;" onclick="this.closest('.nav-item-row').remove();">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="help-text" style="margin-top: 8px;">Define links shown in the top navigation bar. Reorder or remove as needed.</div>

                        <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--line);">
                            <button type="submit" class="btn btn-primary">Save Brand & Links</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('btnAddNavItem')?.addEventListener('click', function() {
            const container = document.getElementById('navItemsContainer');
            const row = document.createElement('div');
            row.className = 'nav-item-row';
            row.style.cssText = 'display: flex; gap: 8px; align-items: center;';
            row.innerHTML = `
                <input type="text" name="nav_label[]" class="form-control" placeholder="Label" style="flex: 1;">
                <input type="text" name="nav_url[]" class="form-control" placeholder="URL (e.g. #section)" style="flex: 1.2;">
                <button type="button" class="btn btn-danger btn-sm btn-remove-nav" style="padding: 6px 10px;" onclick="this.closest('.nav-item-row').remove();">&times;</button>
            `;
            container.appendChild(row);
        });
    </script>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Event Editions</h1>
        <p class="page-subtitle">Select an annual summit edition to manage its workspace, program lineup, and publication settings.</p>
    </div>
</div>

<!-- Event Cards Grid -->
<div class="event-cards-grid">
    <?php if (!empty($eventsList)): ?>
        <?php foreach ($eventsList as $evt): ?>
            <div class="event-edition-card <?= (($activeEvent['id'] ?? null) == $evt['id']) ? 'is-active-card' : '' ?>">
                <!-- Event Cover Image Thumbnail -->
                <div class="event-card-cover">
                    <img src="<?= base_url('assets/img/image1.png') ?>" alt="<?= esc($evt['title'] ?? '') ?>" class="event-card-img">
                    <div class="event-card-overlay"></div>
                    <div class="event-card-year-badge"><?= esc($evt['year']) ?></div>
                </div>

                <!-- Event Details Body -->
                <div class="event-card-content">
                    <h2 class="event-card-title"><?= esc($evt['title'] ?? ('Investor Daily Summit ' . $evt['year'])) ?></h2>
                    <div class="event-card-theme"><?= esc($evt['theme'] ?: 'Annual Investment Forum') ?></div>
                    
                    <div class="event-card-dates">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span><?= esc($evt['event_date_start'] ?? '2026-10-07') ?> — <?= esc($evt['event_date_end'] ?? '2026-10-08') ?></span>
                    </div>

                    <div class="event-card-actions">
                        <a href="<?= base_url('admin/events/manage/' . $evt['id']) ?>" class="btn btn-primary event-manage-btn">
                            <span>Manage Event</span>
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Empty Add Event Card -->
    <div class="event-add-card" onclick="document.getElementById('addEventModal').style.display='flex'">
        <div class="event-add-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </div>
        <div class="event-add-title">Create New Event</div>
        <p class="event-add-sub">Add a new annual edition for upcoming summit years</p>
    </div>
</div>

<!-- Modal: Create New Year Edition -->
<div id="addEventModal" style="display:none; position:fixed; inset:0; background:rgba(0,18,25,0.75); backdrop-filter:blur(4px); z-index:200; align-items:center; justify-content:center;">
    <div style="background:var(--bg-surface); width:560px; border-radius:var(--radius-lg); padding:32px; border:1px solid var(--line); max-height:90vh; overflow-y:auto; box-shadow:var(--shadow-card);">
        <h3 style="font-size:20px; margin-bottom:16px; font-weight:700; color:var(--text-strong);">Create Event Edition</h3>
        <form action="<?= base_url('admin/events/save') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Edition Year *</label>
                    <input type="number" name="year" class="form-control" required value="2027" min="2020" max="2099">
                </div>
                <div class="form-group">
                    <label class="form-label">Lifecycle Status</label>
                    <select name="status" class="form-control">
                        <option value="draft" selected>Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Event Title *</label>
                <input type="text" name="title" class="form-control" required value="INVESTOR DAILY SUMMIT 2027">
            </div>

            <div class="form-group">
                <label class="form-label">Annual Theme *</label>
                <input type="text" name="theme" class="form-control" required value="NAVIGATING SUSTAINABLE GROWTH" placeholder="e.g. INDONESIA'S ECONOMIC MODEL">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="event_date_start" class="form-control" value="2027-10-06">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="event_date_end" class="form-control" value="2027-10-07">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">News Tag Slug</label>
                    <input type="text" name="news_tag_slug" class="form-control" value="ids2027">
                </div>
                <div class="form-group">
                    <label class="form-label">Template Version</label>
                    <input type="text" name="template_version" class="form-control" value="v1">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px; border-top:1px solid var(--line); padding-top:20px;">
                <button type="button" onclick="document.getElementById('addEventModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Edition</button>
            </div>
        </form>
    </div>
</div>

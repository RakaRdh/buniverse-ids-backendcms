<div class="page-header">
    <div>
        <h1 class="page-title">Event Overview & Editorial Copy</h1>
        <p class="page-subtitle">Configure the 2-column editorial copy (heading, lead intro, and full body text) for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
</div>

<form action="<?= base_url('admin/overview/update') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="cms-card">
        <div class="card-title-bar">
            <span class="card-title-text">Annual Theme Sync</span>
        </div>
        <div class="cms-card-body">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Annual Theme (Pill Badge)</label>
                <input type="text" name="theme" class="form-control" value="<?= esc($event['theme'] ?? '') ?>" placeholder="e.g. INDONESIA'S ECONOMIC MODEL">
                <div class="help-text">Synced with the events table. Displays as the golden badge above the section title.</div>
            </div>
        </div>
    </div>

    <div class="cms-card" style="margin-top: 24px;">
        <div class="card-title-bar">
            <span class="card-title-text">2-Column Editorial Copy Layout</span>
        </div>
        <div class="cms-card-body">
            <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px;">
                <!-- Column 1: Left Column (Heading & Intro) -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Main Heading (Column 1) *</label>
                        <textarea name="heading" class="form-control" rows="3" required placeholder="e.g. Navigating Indonesia's Strategic Transformation Towards 2045"><?= esc($overview['heading'] ?? '') ?></textarea>
                        <div class="help-text">Large impactful headline in the left editorial column.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Introductory Text (Lead Highlight)</label>
                        <textarea name="intro_text" class="form-control" rows="6" placeholder="The Investor Daily Summit 2026 stands as Indonesia's premier sovereign-economic forum..."><?= esc($overview['intro_text'] ?? '') ?></textarea>
                        <div class="help-text">Prominent lead paragraph supporting the main headline.</div>
                    </div>
                </div>

                <!-- Column 2: Right Column (Body Text) -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Body Text (Editorial Analysis & Narrative)</label>
                        <textarea name="body_text" class="form-control" id="right-textarea" rows="12" placeholder="Grounded in fiscal pragmatism and industrial self-reliance, the summit convenes cabinet ministers, sovereign wealth custodians, and multinational executives..."><?= esc($overview['body_text'] ?? '') ?></textarea>
                        <div class="help-text">Right editorial column containing in-depth narrative paragraphs.</div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 24px; padding-top: 14px; border-top: 1px solid var(--line); display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Save Editorial Overview
                </button>
            </div>
        </div>
    </div>
</form>

<div class="page-header">
    <div>
        <h1 class="page-title">Event Overview & Editorial Copy</h1>
        <p class="page-subtitle">Configure the 2-column editorial copy (heading, lead intro, and full body text) for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
</div>

<form action="<?= base_url('admin/overview/update') ?>" method="POST" enctype="multipart/form-data">
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
        </div>
    </div>

    <div class="cms-card" style="margin-top: 24px;">
        <div class="card-title-bar">
            <span class="card-title-text">Overview Collage Images (4 Columns on Frontend)</span>
        </div>
        <div class="cms-card-body">
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 18px;">
                Upload the 4 decorative images placed below the introductory editorial section on the landing page. If left empty, frontend fallback images will be used.
            </p>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;">
                <?php for ($i = 1; $i <= 4; $i++): 
                    $colKey = 'collage_img_' . $i;
                    $val = $overview[$colKey] ?? '';
                ?>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--line); border-radius: 8px; padding: 14px; display: flex; flex-direction: column;">
                        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 10px;">Image <?= $i ?></div>
                        <div style="height: 120px; background: rgba(0,0,0,0.25); border-radius: 6px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; border: 1px dashed var(--line);">
                            <?php if (!empty($val)): ?>
                                <img src="<?= base_url($val) ?>" alt="Collage <?= $i ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Default / Fallback</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="collage_img_<?= $i ?>" class="form-control" accept="image/*" style="font-size: 0.8rem; margin-top: auto;">
                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 6px;">JPG, PNG, WebP (Max 2MB)</div>
                    </div>
                <?php endfor; ?>
            </div>

            <div style="margin-top: 24px; padding-top: 14px; border-top: 1px solid var(--line); display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Save Overview & Images
                </button>
            </div>
        </div>
    </div>
</form>

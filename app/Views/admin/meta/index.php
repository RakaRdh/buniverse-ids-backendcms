<div class="page-header">
    <div>
        <h1 class="page-title">Meta & SEO Configuration</h1>
        <p class="page-subtitle">Configure search engine metadata, Open Graph sharing cards, and fallbacks for <?= esc($activeEvent['title'] ?? $activeEvent['name'] ?? '') ?></p>
    </div>
</div>

<form action="<?= base_url('admin/meta/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div>
        <div class="cms-card">
            <div class="card-title-bar">
                <span class="card-title-text">SEO Meta Tags</span>
            </div>
            <div class="cms-card-body">
                <div class="form-group">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" id="metaTitleInput" class="form-control" value="<?= esc($seo['meta_title'] ?? '') ?>" placeholder="<?= esc($event['title'] ?? $event['name'] ?? 'Investor Daily Summit 2026') ?>">
                    <div class="help-text">
                        Fallback: <code>COALESCE(event_seo.meta_title, events.title)</code>. Leave empty to use event title.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" id="metaDescInput" class="form-control" rows="4" placeholder="<?= esc(mb_substr($overview['intro_text'] ?? $overview['body_text'] ?? '', 0, 160)) ?>"><?= esc($seo['meta_description'] ?? '') ?></textarea>
                    <div class="help-text">
                        Fallback: <code>COALESCE(event_seo.meta_description, event_overview.intro_text, event_overview.body_text)</code>. Recommended length: 140–160 characters.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Open Graph Social Sharing Image (OG Image)</label>
                    <?php if (!empty($seo['og_image_url'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= base_url(esc($seo['og_image_url'])) ?>" alt="OG Preview" style="max-height: 120px; border-radius: var(--radius-sm); border: 1px solid var(--line);">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="og_image_file" class="form-control" accept="image/*">
                    <div class="help-text">
                        Fallback: <code>COALESCE(event_seo.og_image_url, event_hero_images.image_url)</code>. Recommended dimensions: 1200×630px.
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--line);">
                    <button type="submit" class="btn btn-primary">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Save Meta & SEO
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

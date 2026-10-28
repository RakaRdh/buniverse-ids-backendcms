<div class="top-action-bar">
    <div>
        <h1 class="page-title">Related News Portals</h1>
        <div class="page-subtitle">Configure destination URLs for the "Selengkapnya &rarr;" links on Related News blocks.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="submit" form="newsPortalsForm" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Portal Links
        </button>
    </div>
</div>

<form id="newsPortalsForm" action="<?= base_url('admin/news-portals/update') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="cms-card" style="max-width: 900px;">
        <div class="card-title-bar">
            <span class="card-title-text">Active Media Portals</span>
        </div>
        <div class="cms-card-body" style="padding: 24px;">
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($portals as $portal): ?>
                    <input type="hidden" name="portal_id[]" value="<?= (int)$portal['id'] ?>">
                    <div style="padding: 18px; border-radius: 8px; border: 1px solid var(--line); background: rgba(0,0,0,0.2);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div style="font-weight: 700; font-size: 15px; color: var(--text-strong);">
                                Portal: <?= esc($portal['portal_key']) ?>
                            </div>
                            <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--text-muted);">
                                <input type="checkbox" name="portal_is_active[<?= (int)$portal['id'] ?>]" value="1" <?= !empty($portal['is_active']) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-green, #22c55e);">
                                <span>Aktifkan Section / Portal</span>
                            </label>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600;">Display Name</label>
                                <input type="text" name="portal_name[]" value="<?= esc($portal['portal_name']) ?>" class="form-control" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600;">Target Portal URL ("Selengkapnya &rarr;")</label>
                                <input type="url" name="portal_url[]" value="<?= esc($portal['portal_url']) ?>" class="form-control" placeholder="https://..." required>
                            </div>
                        </div>

                        <!-- Fetch News Article API URL (Dinamis dengan placeholder {limit}) -->
                        <div class="form-group" style="margin-top: 14px; margin-bottom: 0;">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; display: flex; justify-content: space-between;">
                                <span>Fetch News API Endpoint (REST API Internal B-Universe)</span>
                                <span style="font-weight: 400; color: var(--color-green, #22c55e);">Gunakan {limit} untuk jumlah data</span>
                            </label>
                            <input type="text" name="api_url[<?= esc($portal['portal_key']) ?>]" value="<?= esc($newsApis[$portal['portal_key']] ?? '') ?>" class="form-control" placeholder="https://api.../article/.../limit:{limit}" style="font-family: monospace; font-size: 12px;">
                            <div class="help-text" style="font-size: 11px; margin-top: 4px; color: var(--text-muted);">
                                Kosongkan jika ingin menggunakan default endpoint bawaan frontend. Contoh: <code>https://api.investor.id/article/type=latest&num={limit}&tag=energi-mineral-festival-2026&page=1</code>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</form>

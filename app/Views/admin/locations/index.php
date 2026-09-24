<div class="page-header">
    <div>
        <h1 class="page-title">Event Locations & Floor Plan</h1>
        <p class="page-subtitle">Configure hotel venue, full address, Google Maps iframe embed, and floor plan layout image for <?= esc($activeEvent['name'] ?? $activeEvent['title'] ?? '') ?></p>
    </div>
</div>

<div class="cms-card" style="max-width: 960px;">
    <div class="card-title-bar">
        <span class="card-title-text">Venue Location & General Layout Details</span>
    </div>
    <div class="cms-card-body">
        <form action="<?= base_url('admin/locations/update') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="is_active" id="isActiveCheck" value="1" <?= (!isset($location['is_active']) || !empty($location['is_active'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--accent-gold, #004b84);">
                    <span style="font-size: 14px; color: var(--text-strong);">Enable Locations Section on Frontend</span>
                </label>
                <div class="help-text" style="margin-left: 28px; margin-top: 4px;">
                    When checked, the venue location and floor plan layout will appear on the landing page after the Sponsor & Partners section.
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 form-group">
                    <label class="form-label">Hotel / Venue Name <span class="text-danger">*</span></label>
                    <input type="text" name="hotel_name" class="form-control" required value="<?= esc($location['hotel_name'] ?? 'Raffles Hotel Jakarta') ?>" placeholder="e.g. Raffles Hotel Jakarta">
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Hotel Address <span class="text-danger">*</span></label>
                <textarea name="hotel_address" class="form-control" rows="3" required placeholder="Alamat lengkap hotel..."><?= esc($location['hotel_address'] ?? "Ciputra World, Jl. Prof. DR. Satrio, Kuningan, Karet Kuningan, Kecamatan Setiabudi, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12940") ?></textarea>
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Google Maps Embed Code / iframe <span class="text-danger">*</span></label>
                <textarea name="gmaps_iframe" class="form-control" rows="4" placeholder='<iframe src="https://www.google.com/maps/embed?..." ...></iframe>'><?= esc($location['gmaps_iframe'] ?? '') ?></textarea>
                <div class="help-text">Masukkan tag &lt;iframe&gt; embed dari Google Maps (atau langsung URL embed-nya).</div>
            </div>

            <?php if (!empty($location['gmaps_iframe'])): ?>
                <div class="form-group mb-4">
                    <label class="form-label" style="font-size: 13px; color: var(--text-muted);">Preview Google Maps:</label>
                    <div style="border-radius: var(--radius-md); overflow: hidden; max-width: 500px; height: 260px; border: 1px solid var(--border-color);">
                        <?php 
                            $rawMap = $location['gmaps_iframe'];
                            if (str_contains($rawMap, '<iframe')) {
                                echo $rawMap;
                            } else {
                                echo '<iframe src="' . esc($rawMap) . '" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
                            }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group mb-4">
                <label class="form-label">Denah Ruangan Acara (General Layout Floor Plan)</label>
                <?php if (!empty($location['layout_image_url'])): ?>
                    <div class="mb-3" style="background: var(--bg-hover); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color); max-width: 600px;">
                        <img src="<?= base_url($location['layout_image_url']) ?>" alt="Denah Layout" style="width: 100%; height: auto; border-radius: 6px; display: block; object-fit: contain;">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_layout_image" value="1" id="removeImgCheck">
                            <label class="form-check-label text-danger" for="removeImgCheck" style="font-size: 13px;">
                                Hapus denah layout saat ini
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="layout_image" class="form-control" accept="image/png,image/jpeg,image/webp">
                <div class="help-text">Upload file denah acara (Format: PNG, JPG, WebP). Otomatis dioptimalkan agar tajam dan loading cepat.</div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn btn-gold">Save Location Settings</button>
            </div>
        </form>
    </div>
</div>

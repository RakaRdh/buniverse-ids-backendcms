<div class="content-header">
    <div class="header-left">
        <h1 class="page-title"><?= esc($pageTitle) ?></h1>
        <p class="page-desc">Sesuaikan subjek dan narasi teks email blast undangan resmi agar tidak hardcoded.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
    <!-- Form Settings -->
    <div style="background: var(--bg-card); padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
        <form action="<?= base_url('admin/invitation-messages/update') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600; font-size: 13px;">Subject Email *</label>
                <input type="text" name="subject" id="inSubject" class="form-control" required value="<?= esc($message['subject'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600; font-size: 13px;">Salam Pembuka (Greeting) *</label>
                <input type="text" name="greeting" id="inGreeting" class="form-control" required value="<?= esc($message['greeting'] ?? 'Kepada Yth. {name}') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600; font-size: 13px;">Isi Paragraf Pembuka / Undangan *</label>
                <textarea name="body_content" id="inBody" class="form-control" rows="8" required><?= esc($message['body_content'] ?? '') ?></textarea>
                <div style="margin-top: 8px; font-size: 12px; color: var(--text-muted);">
                    <strong>Tersedia Placeholder Dinamis:</strong><br>
                    <code style="color:var(--primary-color);">{name}</code> : Nama lengkap tamu & gelar<br>
                    <code style="color:var(--primary-color);">{invitation_url}</code> : Tautan unik e-undangan tamu<br>
                    <code style="color:var(--primary-color);">{event_title}</code> : Judul acara resmi<br>
                    <code style="color:var(--primary-color);">{event_date}</code> : Tanggal pelaksanaan acara<br>
                    <code style="color:var(--primary-color);">{venue}</code> : Lokasi / venue gedung
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600; font-size: 13px;">Teks Tombol Link Undangan *</label>
                <input type="text" name="button_text" id="inBtn" class="form-control" required value="<?= esc($message['button_text'] ?? 'Buka Undangan Digital') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 600; font-size: 13px;">Kalimat Penutup</label>
                <input type="text" name="closing_text" id="inClosing" class="form-control" value="<?= esc($message['closing_text'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="font-weight: 600; font-size: 13px;">Footer Panitia Pelaksana</label>
                <input type="text" name="footer_note" id="inFooter" class="form-control" value="<?= esc($message['footer_note'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                Simpan Pengaturan Email
            </button>
        </form>
    </div>

    <!-- Live Preview Card -->
    <div style="display:flex; flex-direction:column; gap:12px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:15px; font-weight:700; color:var(--text-strong); margin:0;">Live Email Render Preview</h3>
            <span style="font-size:12px; color:var(--text-muted); background:var(--bg-subtle); padding:3px 8px; border-radius:4px;">Desktop Mail Client</span>
        </div>

        <div style="background:#0d131f; border-radius:12px; border:1px solid rgba(212,175,55,0.3); overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.4); font-family:sans-serif; color:#cbd5e1;">
            <div style="background:linear-gradient(135deg, #09101d, #15243b); padding:24px 20px; text-align:center; border-bottom:2px solid #d4af37;">
                <div style="font-size:11px; letter-spacing:2px; color:#d4af37; font-weight:700;">B-UNIVERSE OFFICIAL INVITATION</div>
                <h2 style="color:#ffffff; font-size:18px; margin:4px 0 2px; font-weight:700;"><?= esc($activeEvent['title'] ?? 'BERITASATU MALAM APRESIASI 2026') ?></h2>
                <div style="font-size:12px; color:#94a3b8;"><?= esc($activeEvent['theme'] ?? 'Menggerakkan Perubahan, Menginspirasi Negeri') ?></div>
            </div>

            <div style="padding:24px 20px; font-size:13px; line-height:1.7;">
                <div id="pvGreeting" style="font-weight:700; color:#fff; font-size:15px; margin-bottom:12px;">Kepada Yth. Bapak/Ibu Tamu Undangan</div>
                <div id="pvBody" style="white-space:pre-line; color:#cbd5e1; margin-bottom:24px;">
                    <?= nl2br(esc($message['body_content'] ?? '')) ?>
                </div>

                <div style="text-align:center; margin:24px 0;">
                    <span id="pvBtn" style="display:inline-block; background:linear-gradient(135deg, #d4af37, #b89127); color:#0b121e; font-weight:700; padding:10px 24px; border-radius:50px; font-size:13px; box-shadow:0 4px 14px rgba(212,175,55,0.4);">
                        <?= esc($message['button_text'] ?? 'Buka Undangan Digital') ?>
                    </span>
                </div>

                <div id="pvClosing" style="font-size:12px; color:#94a3b8; margin-top:16px;">
                    <?= esc($message['closing_text'] ?? '') ?>
                </div>
            </div>

            <div style="background:#090e18; padding:16px 20px; text-align:center; font-size:11px; color:#64748b; border-top:1px solid rgba(255,255,255,0.06);">
                <div id="pvFooter" style="font-weight:600; color:#94a3b8; margin-bottom:2px;"><?= esc($message['footer_note'] ?? 'Panitia Pelaksana BeritaSatu Malam Apresiasi 2026') ?></div>
                <div>Email ini dikirimkan secara otomatis dan resmi kepada Anda.</div>
            </div>
        </div>
    </div>
</div>

<script>
// Interactive live reflection as admin types
document.getElementById('inGreeting')?.addEventListener('input', e => {
    document.getElementById('pvGreeting').textContent = e.target.value.replace('{name}', 'Bapak/Ibu Tamu Undangan');
});
document.getElementById('inBody')?.addEventListener('input', e => {
    let txt = e.target.value
        .replace(/{name}/g, 'Bapak/Ibu Tamu Undangan')
        .replace(/{event_title}/g, '<?= esc($activeEvent['title'] ?? 'BeritaSatu Malam Apresiasi 2026') ?>')
        .replace(/{event_date}/g, '20 November 2026')
        .replace(/{venue}/g, 'Grand Ballroom Hotel Mulia Senayan Jakarta')
        .replace(/{invitation_url}/g, 'https://beritasatu.com/malam-apresiasi-2026/invitation/...');
    document.getElementById('pvBody').textContent = txt;
});
document.getElementById('inBtn')?.addEventListener('input', e => {
    document.getElementById('pvBtn').textContent = e.target.value;
});
document.getElementById('inClosing')?.addEventListener('input', e => {
    document.getElementById('pvClosing').textContent = e.target.value;
});
document.getElementById('inFooter')?.addEventListener('input', e => {
    document.getElementById('pvFooter').textContent = e.target.value;
});
</script>

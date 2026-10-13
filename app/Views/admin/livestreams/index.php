<div class="top-action-bar">
    <div>
        <h1 class="page-title">Live Streaming Settings</h1>
        <div class="page-subtitle">Configure YouTube Live Stream embed and toggle section visibility on the frontend.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="submit" form="livestreamForm" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Live Stream
        </button>
    </div>
</div>

<form id="livestreamForm" action="<?= base_url('admin/livestream/update') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="cms-card" style="max-width: 900px;">
        <div class="card-title-bar">
            <span class="card-title-text">Live Stream Configuration</span>
        </div>
        <div class="cms-card-body" style="padding: 24px;">
            <div class="form-group" style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--line);">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="is_active" value="1" <?= (!empty($stream['is_active'])) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                    <span>Enable Live Streaming Section on Frontend</span>
                </label>
                <div class="form-hint" style="font-size: 12px; color: var(--text-muted); margin-top: 4px; margin-left: 28px;">
                    Jika dinonaktifkan, section Live Streaming akan disembunyikan sepenuhnya dari landing page.
                </div>
            </div>

            <!-- STREAM 1 (Utama / Plenary) -->
            <div style="background: var(--surface-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--line); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span>
                        <strong style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Stream 1 (Utama / Plenary Hall) *</strong>
                    </div>
                    <span style="font-size: 12px; color: var(--text-muted);">Default Stream</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 12px;">Label / Nama Ruangan</label>
                        <input type="text" name="streams[0][title]" value="<?= esc($streams[0]['title'] ?? ($stream['title'] ?? 'Plenary Hall')) ?>" class="form-control" placeholder="Contoh: Plenary Hall" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 12px;">YouTube URL or Video ID *</label>
                        <input type="text" name="streams[0][youtube_url]" value="<?= esc($streams[0]['youtube_url'] ?? ($stream['youtube_url'] ?? '')) ?>" placeholder="https://www.youtube.com/live/... or Video ID" class="form-control yt-url-input" required oninput="previewSelectedStream(0)">
                    </div>
                </div>
            </div>

            <!-- STREAM 2 (Ruang Breakout A - Opsional) -->
            <div style="background: var(--surface-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--line); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #3b82f6;"></span>
                        <strong style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Stream 2 (Ruang Breakout 1 - Opsional)</strong>
                    </div>
                    <span style="font-size: 12px; color: var(--text-muted);">Kosongkan jika hanya 1 link</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 12px;">Label / Nama Ruangan</label>
                        <input type="text" name="streams[1][title]" value="<?= esc($streams[1]['title'] ?? 'Breakout Room 1') ?>" class="form-control" placeholder="Contoh: Room 1">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 12px;">YouTube URL or Video ID</label>
                        <input type="text" name="streams[1][youtube_url]" value="<?= esc($streams[1]['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/live/... (opsional)" class="form-control yt-url-input" oninput="previewSelectedStream(1)">
                    </div>
                </div>
            </div>

            <!-- STREAM 3 (Ruang Breakout B - Opsional) -->
            <div style="background: var(--surface-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--line); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                        <strong style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Stream 3 (Ruang Breakout 2 - Opsional)</strong>
                    </div>
                    <span style="font-size: 12px; color: var(--text-muted);">Kosongkan jika hanya 1 link</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 12px;">Label / Nama Ruangan</label>
                        <input type="text" name="streams[2][title]" value="<?= esc($streams[2]['title'] ?? 'Breakout Room 2') ?>" class="form-control" placeholder="Contoh: Room 2">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 12px;">YouTube URL or Video ID</label>
                        <input type="text" name="streams[2][youtube_url]" value="<?= esc($streams[2]['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/live/... (opsional)" class="form-control yt-url-input" oninput="previewSelectedStream(2)">
                    </div>
                </div>
            </div>

            <!-- Multi-Stream Player Preview -->
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <label class="form-label" style="font-weight: 600; margin: 0;">Player Embed Preview</label>
                    <div style="display: flex; gap: 8px;" id="previewTabButtons">
                        <button type="button" class="btn btn-sm btn-outline-primary active" onclick="switchPreview(0)" id="btnPrev0">Stream 1</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="switchPreview(1)" id="btnPrev1">Stream 2</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="switchPreview(2)" id="btnPrev2">Stream 3</button>
                    </div>
                </div>
                <div style="aspect-ratio: 16/9; max-width: 720px; background: #000; border-radius: 12px; overflow: hidden; border: 1px solid var(--line);">
                    <iframe id="ytPreviewFrame" src="" style="width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function extractVideoId(url) {
    if (!url) return '';
    const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|live\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
    return match ? match[1] : (url.trim().length === 11 ? url.trim() : '');
}

let activePreviewIdx = 0;

function switchPreview(idx) {
    activePreviewIdx = idx;
    [0, 1, 2].forEach(i => {
        const btn = document.getElementById('btnPrev' + i);
        if (btn) {
            if (i === idx) {
                btn.style.background = 'var(--primary, #004b84)';
                btn.style.color = '#fff';
            } else {
                btn.style.background = 'transparent';
                btn.style.color = 'inherit';
            }
        }
    });

    const inputs = document.querySelectorAll('.yt-url-input');
    const targetUrl = inputs[idx] ? inputs[idx].value : '';
    const videoId = extractVideoId(targetUrl);
    const iframe = document.getElementById('ytPreviewFrame');
    if (videoId && videoId.length === 11) {
        iframe.src = 'https://www.youtube.com/embed/' + videoId;
    } else {
        iframe.src = '';
    }
}

function previewSelectedStream(idx) {
    if (idx === activePreviewIdx) {
        switchPreview(idx);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    switchPreview(0);
});
</script>

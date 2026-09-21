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

    <div class="cms-card" style="max-width: 800px;">
        <div class="card-title-bar">
            <span class="card-title-text">Live Stream Configuration</span>
        </div>
        <div class="cms-card-body" style="padding: 24px;">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="is_active" value="1" <?= (!empty($stream['is_active'])) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                    <span>Enable Live Streaming Section on Frontend</span>
                </label>
                <div class="form-hint" style="font-size: 12px; color: var(--text-muted); margin-top: 4px; margin-left: 28px;">
                    If unchecked, the Live Streaming section will be hidden on the landing page.
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="font-weight: 600;">Stream Title</label>
                <input type="text" name="title" value="<?= esc($stream['title'] ?? 'Investor Daily Summit 2026 Live Stream') ?>" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="font-weight: 600;">YouTube URL or Video ID *</label>
                <input type="text" name="youtube_url" id="youtubeUrlInput" value="<?= esc($stream['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=... or Video ID" class="form-control" required oninput="updateYoutubePreview(this.value)">
                <div class="form-hint" style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                    Format examples: <code>https://www.youtube.com/watch?v=rFZHOHl-L8A</code>, <code>https://youtu.be/rFZHOHl-L8A</code>, or <code>rFZHOHl-L8A</code>.
                </div>
            </div>

            <!-- Live Preview -->
            <div style="margin-top: 24px;">
                <label class="form-label" style="font-weight: 600; margin-bottom: 10px; display: block;">Player Embed Preview</label>
                <div style="aspect-ratio: 16/9; max-width: 640px; background: #000; border-radius: 12px; overflow: hidden; border: 1px solid var(--line);">
                    <iframe id="ytPreviewFrame" src="" style="width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function extractVideoId(url) {
    if (!url) return '';
    const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
    return match ? match[1] : url.trim();
}

function updateYoutubePreview(url) {
    const videoId = extractVideoId(url);
    const iframe = document.getElementById('ytPreviewFrame');
    if (videoId && videoId.length === 11) {
        iframe.src = 'https://www.youtube.com/embed/' + videoId;
    } else {
        iframe.src = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateYoutubePreview(document.getElementById('youtubeUrlInput').value);
});
</script>

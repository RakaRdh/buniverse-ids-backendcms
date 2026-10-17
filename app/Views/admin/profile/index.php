<div class="page-header">
    <div>
        <h1 class="page-title">My Profile & Security</h1>
        <p class="page-subtitle">Manage your account credentials and change your password</p>
    </div>
    <?php if (!empty($fromPortal)): ?>
        <div>
            <a href="<?= base_url('admin/events') ?>" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Events</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; max-width: 900px;">
    <!-- Profile Info Card -->
    <div class="cms-card">
        <div class="card-title-bar">
            <span class="card-title-text">Account Information</span>
        </div>
        <div class="cms-card-body">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="width: 56px; height: 56px; border-radius: var(--radius-pill); background: var(--primary-1-2); color: var(--primary-1-8); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; border: 1.5px solid var(--line);">
                    <?= strtoupper(substr($user['username'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text-strong); margin: 0;"><?= esc($user['name'] ?: $user['username']) ?></h3>
                    <span class="status-pill status-live" style="margin-top: 4px; display: inline-block; font-size: 11px; text-transform: uppercase;">
                        <?= esc($user['role'] ?? 'editor') ?>
                    </span>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="color: var(--text-muted); font-size: 12px;">Username</label>
                <input type="text" class="form-control" value="<?= esc($user['username'] ?? '') ?>" readonly style="background: var(--bg-subtle); color: var(--text-body); cursor: not-allowed;">
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="color: var(--text-muted); font-size: 12px;">Email Address</label>
                <input type="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" readonly style="background: var(--bg-subtle); color: var(--text-body); cursor: not-allowed;">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="color: var(--text-muted); font-size: 12px;">Role Level</label>
                <input type="text" class="form-control" value="<?= esc(ucfirst($user['role'] ?? 'Editor')) ?>" readonly style="background: var(--bg-subtle); color: var(--text-body); cursor: not-allowed;">
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="cms-card">
        <div class="card-title-bar">
            <span class="card-title-text">Change Password</span>
        </div>
        <div class="cms-card-body">
            <form action="<?= base_url('admin/profile/update-password') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 600; color: var(--text-strong);">Current Password *</label>
                    <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 600; color: var(--text-strong);">New Password *</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Min. 6 characters">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="font-weight: 600; color: var(--text-strong);">Confirm New Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Re-type new password">
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%;">
                    Update Password Now
                </button>
            </form>
        </div>
    </div>
</div>

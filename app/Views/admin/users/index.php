<div class="page-header">
    <div>
        <h1 class="page-title">Admin User Management</h1>
        <p class="page-subtitle">Manage superadmin and editor access permissions for the Global Event CMS</p>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <?php if (!empty($fromPortal)): ?>
            <a href="<?= base_url('admin/events') ?>" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Events</span>
            </a>
        <?php endif; ?>
        <button type="button" onclick="document.getElementById('addUserModal').style.display='block'" class="btn btn-gold">+ Add Admin User</button>
    </div>
</div>

<div class="cms-card">
    <div class="card-title-bar">
        <span class="card-title-text">All System Administrators (<?= count($users) ?>)</span>
    </div>
    <div class="table-responsive">
        <table class="cms-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?= esc($u['name']) ?></strong></td>
                        <td><code><?= esc($u['username'] ?: '—') ?></code></td>
                        <td><?= esc($u['email']) ?></td>
                        <td>
                            <span class="status-pill status-<?= $u['role'] === 'superadmin' ? 'published' : 'draft' ?>">
                                <?= esc(ucfirst($u['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-size:11.5px; color:<?= $u['is_active'] ? 'var(--color-green)' : 'var(--color-red)' ?>;">
                                <?= $u['is_active'] ? 'Active' : 'Disabled' ?>
                            </span>
                        </td>
                        <td style="font-size:11.5px; color:var(--color-text-muted);">
                            <?= date('d M Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td style="text-align: right;">
                            <?php if ((int)$u['id'] !== (int)$currentAdmin['id']): ?>
                                <form action="<?= base_url('admin/users/delete/' . $u['id']) ?>" method="POST" onsubmit="return confirm('Delete this admin user?');" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                                </form>
                            <?php else: ?>
                                <span style="font-size:11px; color:var(--color-text-muted);">(You)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Add Admin User -->
<div id="addUserModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
    <div style="background:#FFF; width:450px; margin:80px auto; border-radius:8px; padding:20px; box-shadow:0 10px 25px rgba(0,0,0,0.3);">
        <h3 style="font-size:15px; margin-bottom:14px; font-weight:700;">Add Admin User</h3>
        <form action="<?= base_url('admin/users/save') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Jane Doe">
            </div>
            <div class="form-group">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" required placeholder="e.g. janedoe">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" required placeholder="jane@b-universe.id">
            </div>
            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control">
                    <option value="editor">Editor (Content Management Only)</option>
                    <option value="superadmin">Superadmin (Full Access + User Management)</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px;">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

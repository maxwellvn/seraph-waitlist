<?php
$admins = getAllAdmins();
$currentAdmin = getCurrentAdmin();
$action = $_GET['action'] ?? '';
$editAdmin = null;

if (isset($_GET['edit'])) {
    global $db;
    $editAdmin = $db->findOne('admins', ['id' => (int)$_GET['edit']]);
    if ($editAdmin) {
        unset($editAdmin['password']);
    }
}
?>

<?php if ($action === 'new' || $editAdmin): ?>
<!-- Admin Form -->
<div class="max-w-2xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="<?php echo BASE_URL; ?>admin/admins" class="text-admin-muted hover:text-admin-text">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-xl font-semibold text-admin-text">
            <?php echo $editAdmin ? 'Edit Admin User' : 'Add New Admin'; ?>
        </h2>
    </div>

    <form id="adminForm" class="card bg-white rounded-xl">
        <input type="hidden" name="id" value="<?php echo $editAdmin['id'] ?? ''; ?>">

        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-admin-text mb-2">Full Name *</label>
                <input type="text" name="name" required
                       value="<?php echo h($editAdmin['name'] ?? ''); ?>"
                       class="w-full border border-admin-border rounded-lg px-4 py-3"
                       placeholder="Enter full name">
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-text mb-2">Email Address *</label>
                <input type="email" name="email" required
                       value="<?php echo h($editAdmin['email'] ?? ''); ?>"
                       class="w-full border border-admin-border rounded-lg px-4 py-3"
                       placeholder="admin@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-text mb-2">
                    Password <?php echo $editAdmin ? '(leave blank to keep current)' : '*'; ?>
                </label>
                <input type="password" name="password" <?php echo $editAdmin ? '' : 'required'; ?>
                       class="w-full border border-admin-border rounded-lg px-4 py-3"
                       placeholder="<?php echo $editAdmin ? '••••••••' : 'Enter password'; ?>"
                       minlength="6">
                <p class="text-sm text-admin-muted mt-1">Minimum 6 characters</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-admin-text mb-2">Role</label>
                    <select name="role" class="w-full border border-admin-border rounded-lg px-4 py-3">
                        <option value="admin" <?php echo ($editAdmin['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="super_admin" <?php echo ($editAdmin['role'] ?? '') === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                        <option value="manager" <?php echo ($editAdmin['role'] ?? '') === 'manager' ? 'selected' : ''; ?>>Manager</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-admin-text mb-2">Status</label>
                    <select name="status" class="w-full border border-admin-border rounded-lg px-4 py-3">
                        <option value="active" <?php echo ($editAdmin['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($editAdmin['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-admin-bg border-t border-admin-border flex items-center justify-end gap-4">
            <a href="<?php echo BASE_URL; ?>admin/admins" class="btn-secondary px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="btn-primary px-6 py-2 rounded-lg">
                <?php echo $editAdmin ? 'Update Admin' : 'Create Admin'; ?>
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('adminForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    const isEdit = !!data.id;

    // Remove empty password for edit
    if (isEdit && !data.password) {
        delete data.password;
    }

    try {
        const endpoint = isEdit ? 'api/admin/admins/update' : 'api/admin/admins/create';
        const result = await apiRequest(endpoint, 'POST', data);

        if (result.success) {
            showToast(isEdit ? 'Admin updated' : 'Admin created');
            setTimeout(() => {
                window.location.href = baseUrl + 'admin/admins';
            }, 1000);
        } else {
            showToast(result.message || 'Failed to save', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});
</script>

<?php else: ?>
<!-- Admins List -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <p class="text-admin-muted"><?php echo count($admins); ?> admin users</p>
        <a href="<?php echo BASE_URL; ?>admin/admins?action=new" class="btn-primary px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Add Admin
        </a>
    </div>

    <!-- Admins Table -->
    <div class="card bg-white rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-admin-bg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Admin</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-admin-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    <?php if (empty($admins)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-admin-muted">
                                <i class="fas fa-user-shield text-4xl mb-4"></i>
                                <p>No admin users found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($admins as $admin): ?>
                            <tr class="table-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-admin-accent/10 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-shield text-admin-accent"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-admin-text">
                                                <?php echo h($admin['name']); ?>
                                                <?php if ($admin['id'] === $currentAdmin['id']): ?>
                                                    <span class="text-xs text-admin-accent ml-1">(You)</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-admin-muted">
                                    <?php echo h($admin['email']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm px-2 py-1 bg-admin-bg rounded capitalize">
                                        <?php echo str_replace('_', ' ', $admin['role'] ?? 'admin'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="status-badge status-<?php echo $admin['status'] ?? 'active'; ?>">
                                        <?php echo ucfirst($admin['status'] ?? 'active'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-admin-muted">
                                    <?php echo $admin['last_login'] ? formatDate($admin['last_login'], 'M j, Y g:i A') : 'Never'; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo BASE_URL; ?>admin/admins?edit=<?php echo $admin['id']; ?>"
                                       class="text-admin-accent hover:text-amber-600 mr-3" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($admin['id'] !== $currentAdmin['id']): ?>
                                        <button onclick="deleteAdmin(<?php echo $admin['id']; ?>)"
                                                class="text-red-500 hover:text-red-600" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Security Note -->
    <div class="card bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex gap-3">
            <i class="fas fa-shield-alt text-amber-600 mt-0.5"></i>
            <div>
                <p class="font-medium text-amber-800">Security Note</p>
                <p class="text-sm text-amber-700 mt-1">
                    Admin accounts have full access to manage your store. Only create accounts for trusted team members.
                    Regularly review admin access and disable accounts that are no longer needed.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
async function deleteAdmin(id) {
    if (!confirmAction('Are you sure you want to delete this admin? This action cannot be undone.')) return;

    try {
        const result = await apiRequest('api/admin/admins/delete', 'POST', { id });

        if (result.success) {
            showToast('Admin deleted');
            location.reload();
        } else {
            showToast(result.message || 'Failed to delete', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}
</script>
<?php endif; ?>

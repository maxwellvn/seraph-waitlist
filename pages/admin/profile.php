<?php
$admin = getCurrentAdmin();
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Invalid security token';
    } else {
        $updates = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];

        // Handle password change
        if (!empty($_POST['new_password'])) {
            if (strlen($_POST['new_password']) < 6) {
                $errorMessage = 'Password must be at least 6 characters';
            } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
                $errorMessage = 'Passwords do not match';
            } else {
                $updates['password'] = $_POST['new_password'];
            }
        }

        if (!$errorMessage) {
            $result = updateAdmin($admin['id'], $updates);
            if ($result['success']) {
                $successMessage = 'Profile updated successfully';
                // Refresh admin data
                $admin = getCurrentAdmin();
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
            } else {
                $errorMessage = $result['message'];
            }
        }
    }
}
?>

<div class="max-w-2xl space-y-6">
    <?php if ($successMessage): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i><?php echo h($successMessage); ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo h($errorMessage); ?>
        </div>
    <?php endif; ?>

    <!-- Profile Info -->
    <div class="card bg-white rounded-xl">
        <div class="px-6 py-4 border-b border-admin-border">
            <h3 class="font-semibold text-admin-text">Profile Information</h3>
        </div>
        <div class="p-6">
            <form method="POST" class="space-y-6">
                <?php echo csrfField(); ?>

                <div class="flex items-center gap-6 pb-6 border-b border-admin-border">
                    <div class="w-20 h-20 bg-admin-accent/10 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-admin-accent"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-admin-text text-lg"><?php echo h($admin['name']); ?></h4>
                        <p class="text-admin-muted"><?php echo h($admin['email']); ?></p>
                        <p class="text-sm text-admin-muted mt-1">
                            <span class="px-2 py-0.5 bg-admin-bg rounded capitalize">
                                <?php echo str_replace('_', ' ', $admin['role'] ?? 'admin'); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-admin-text mb-2">Full Name</label>
                        <input type="text" name="name" value="<?php echo h($admin['name']); ?>" required
                               class="w-full border border-admin-border rounded-lg px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-admin-text mb-2">Email Address</label>
                        <input type="email" name="email" value="<?php echo h($admin['email']); ?>" required
                               class="w-full border border-admin-border rounded-lg px-4 py-3">
                    </div>
                </div>

                <div class="pt-6 border-t border-admin-border">
                    <h4 class="font-medium text-admin-text mb-4">Change Password</h4>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-admin-text mb-2">New Password</label>
                            <input type="password" name="new_password"
                                   class="w-full border border-admin-border rounded-lg px-4 py-3"
                                   placeholder="Leave blank to keep current"
                                   minlength="6">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-admin-text mb-2">Confirm Password</label>
                            <input type="password" name="confirm_password"
                                   class="w-full border border-admin-border rounded-lg px-4 py-3"
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                    <p class="text-sm text-admin-muted mt-2">Password must be at least 6 characters</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary px-6 py-2 rounded-lg">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Info -->
    <div class="card bg-white rounded-xl">
        <div class="px-6 py-4 border-b border-admin-border">
            <h3 class="font-semibold text-admin-text">Account Information</h3>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-admin-muted">Account Created</p>
                    <p class="font-medium text-admin-text mt-1">
                        <?php echo formatDate($admin['created_at'] ?? '', 'F j, Y \a\t g:i A'); ?>
                    </p>
                </div>

                <div>
                    <p class="text-admin-muted">Last Login</p>
                    <p class="font-medium text-admin-text mt-1">
                        <?php echo $admin['last_login'] ? formatDate($admin['last_login'], 'F j, Y \a\t g:i A') : 'Current session'; ?>
                    </p>
                </div>

                <div>
                    <p class="text-admin-muted">Role</p>
                    <p class="font-medium text-admin-text mt-1 capitalize">
                        <?php echo str_replace('_', ' ', $admin['role'] ?? 'admin'); ?>
                    </p>
                </div>

                <div>
                    <p class="text-admin-muted">Status</p>
                    <p class="mt-1">
                        <span class="status-badge status-<?php echo $admin['status'] ?? 'active'; ?>">
                            <?php echo ucfirst($admin['status'] ?? 'active'); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

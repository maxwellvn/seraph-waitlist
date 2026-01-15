<?php
$customers = getAllUsers();
$viewCustomer = null;
$customerOrders = [];

if (isset($_GET['view'])) {
    $viewCustomer = getUserById($_GET['view']);
    if ($viewCustomer) {
        $customerOrders = getUserOrderHistory($viewCustomer['id']);
    }
}
?>

<?php if ($viewCustomer): ?>
<!-- Customer Detail View -->
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?php echo BASE_URL; ?>admin/customers" class="text-admin-muted hover:text-admin-text">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-semibold text-admin-text"><?php echo h($viewCustomer['name']); ?></h2>
            <p class="text-admin-muted text-sm">Customer since <?php echo formatDate($viewCustomer['created_at'], 'F Y'); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Customer Info -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Profile -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Profile Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-admin-bg rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-admin-muted"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-admin-text"><?php echo h($viewCustomer['name']); ?></p>
                            <p class="text-sm text-admin-muted"><?php echo h($viewCustomer['email']); ?></p>
                        </div>
                    </div>

                    <form id="customerForm" class="space-y-4">
                        <input type="hidden" name="id" value="<?php echo $viewCustomer['id']; ?>">

                        <div>
                            <label class="block text-sm font-medium text-admin-muted mb-1">Name</label>
                            <input type="text" name="name" value="<?php echo h($viewCustomer['name']); ?>"
                                   class="w-full border border-admin-border rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-admin-muted mb-1">Email</label>
                            <input type="email" name="email" value="<?php echo h($viewCustomer['email']); ?>"
                                   class="w-full border border-admin-border rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-admin-muted mb-1">Phone</label>
                            <input type="text" name="phone" value="<?php echo h($viewCustomer['phone'] ?? ''); ?>"
                                   class="w-full border border-admin-border rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-admin-muted mb-1">Status</label>
                            <select name="status" class="w-full border border-admin-border rounded-lg px-4 py-2">
                                <option value="active" <?php echo ($viewCustomer['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($viewCustomer['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full btn-primary px-4 py-2 rounded-lg">
                            Update Customer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Addresses -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Addresses</h3>
                </div>
                <div class="p-6">
                    <?php $addresses = $viewCustomer['addresses'] ?? []; ?>
                    <?php if (empty($addresses)): ?>
                        <p class="text-admin-muted text-center py-4">No addresses saved</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($addresses as $addr): ?>
                                <div class="bg-admin-bg rounded-lg p-4 text-sm">
                                    <?php if ($addr['is_default'] ?? false): ?>
                                        <span class="text-xs bg-admin-accent text-white px-2 py-0.5 rounded mb-2 inline-block">Default</span>
                                    <?php endif; ?>
                                    <p class="font-medium"><?php echo h(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')); ?></p>
                                    <p class="text-admin-muted"><?php echo h($addr['address_line1'] ?? ''); ?></p>
                                    <p class="text-admin-muted"><?php echo h(($addr['city'] ?? '') . ', ' . ($addr['country'] ?? '')); ?></p>
                                    <?php if (!empty($addr['phone'])): ?>
                                        <p class="text-admin-muted"><?php echo h($addr['phone']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Orders History -->
        <div class="lg:col-span-2 order-first lg:order-last">
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Order History</h3>
                </div>
                <div class="overflow-x-auto">
                    <?php if (empty($customerOrders)): ?>
                        <div class="p-12 text-center">
                            <i class="fas fa-shopping-bag text-4xl text-admin-muted mb-4"></i>
                            <p class="text-admin-muted">No orders yet</p>
                        </div>
                    <?php else: ?>
                        <table class="w-full">
                            <thead class="bg-admin-bg">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-admin-muted uppercase">Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-admin-muted uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-admin-muted uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-admin-muted uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border">
                                <?php foreach ($customerOrders as $order): ?>
                                    <tr class="table-row">
                                        <td class="px-6 py-4">
                                            <a href="<?php echo BASE_URL; ?>admin/orders?view=<?php echo $order['id']; ?>"
                                               class="text-admin-accent hover:underline font-medium">
                                                <?php echo h($order['order_number']); ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-admin-muted">
                                            <?php echo formatDate($order['created_at'], 'M j, Y'); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="status-badge status-<?php echo $order['order_status'] ?? 'pending'; ?>">
                                                <?php echo ucfirst($order['order_status'] ?? 'pending'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            <?php echo number_format($order['total'] ?? 0); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="card bg-white rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-admin-text"><?php echo count($customerOrders); ?></p>
                    <p class="text-sm text-admin-muted">Total Orders</p>
                </div>
                <div class="card bg-white rounded-xl p-4 text-center">
                    <?php
                    $totalSpent = array_reduce($customerOrders, function($sum, $order) {
                        return $sum + ($order['payment_status'] === 'completed' ? ($order['total'] ?? 0) : 0);
                    }, 0);
                    ?>
                    <p class="text-2xl font-bold text-admin-text"><?php echo number_format($totalSpent); ?></p>
                    <p class="text-sm text-admin-muted">Total Spent</p>
                </div>
                <div class="card bg-white rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-admin-text"><?php echo count($viewCustomer['addresses'] ?? []); ?></p>
                    <p class="text-sm text-admin-muted">Addresses</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('customerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const result = await apiRequest('api/admin/customers/update', 'POST', data);

        if (result.success) {
            showToast('Customer updated');
        } else {
            showToast(result.message || 'Failed to update', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});
</script>

<?php else: ?>
<!-- Customers List -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <p class="text-admin-muted"><?php echo count($customers); ?> total customers</p>
    </div>

    <!-- Customers Table -->
    <div class="card bg-white rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-admin-bg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-admin-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-admin-muted">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p>No customers yet</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="table-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-admin-bg rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-admin-muted"></i>
                                        </div>
                                        <div>
                                            <a href="<?php echo BASE_URL; ?>admin/customers?view=<?php echo $customer['id']; ?>"
                                               class="font-medium text-admin-text hover:text-admin-accent">
                                                <?php echo h($customer['name']); ?>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-admin-muted">
                                    <?php echo h($customer['email']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-admin-muted">
                                    <?php echo formatDate($customer['created_at'], 'M j, Y'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="status-badge status-<?php echo $customer['status'] ?? 'active'; ?>">
                                        <?php echo ucfirst($customer['status'] ?? 'active'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo BASE_URL; ?>admin/customers?view=<?php echo $customer['id']; ?>"
                                       class="text-admin-accent hover:text-amber-600 mr-3" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="deleteCustomer(<?php echo $customer['id']; ?>)"
                                            class="text-red-500 hover:text-red-600" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function deleteCustomer(id) {
    if (!confirmAction('Are you sure you want to delete this customer? This action cannot be undone.')) return;

    try {
        const result = await apiRequest('api/admin/customers/delete', 'POST', { id });

        if (result.success) {
            showToast('Customer deleted');
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

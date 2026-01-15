<?php
$statusFilter = $_GET['status'] ?? '';
$paymentFilter = $_GET['payment'] ?? '';
$orders = getAllOrders(['status' => $statusFilter, 'payment_status' => $paymentFilter]);

$viewOrder = null;
if (isset($_GET['view'])) {
    $viewOrder = getOrderById($_GET['view']);
}
?>

<?php if ($viewOrder): ?>
<!-- Order Detail View -->
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?php echo BASE_URL; ?>admin/orders" class="text-admin-muted hover:text-admin-text">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-semibold text-admin-text"><?php echo h($viewOrder['order_number']); ?></h2>
            <p class="text-admin-muted text-sm">Placed on <?php echo formatDate($viewOrder['created_at'], 'F j, Y \a\t g:i A'); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Items -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Order Items</h3>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-admin-muted">
                                <th class="pb-3">Product</th>
                                <th class="pb-3">Price</th>
                                <th class="pb-3">Qty</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($viewOrder['items'] ?? [] as $item): ?>
                                <tr class="border-t border-admin-border">
                                    <td class="py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-admin-bg rounded-lg flex items-center justify-center">
                                                <i class="fas fa-box text-admin-muted"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-admin-text"><?php echo h($item['name']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4"><?php echo number_format($item['price']); ?></td>
                                    <td class="py-4"><?php echo $item['quantity']; ?></td>
                                    <td class="py-4 text-right font-medium"><?php echo number_format($item['price'] * $item['quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-t border-admin-border">
                            <tr>
                                <td colspan="3" class="py-3 text-right font-medium">Subtotal</td>
                                <td class="py-3 text-right"><?php echo number_format($viewOrder['subtotal'] ?? 0); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-3 text-right font-medium">Shipping</td>
                                <td class="py-3 text-right"><?php echo number_format($viewOrder['shipping'] ?? 0); ?></td>
                            </tr>
                            <tr class="text-lg">
                                <td colspan="3" class="py-3 text-right font-bold">Total</td>
                                <td class="py-3 text-right font-bold"><?php echo number_format($viewOrder['total'] ?? 0); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Shipping Address</h3>
                </div>
                <div class="p-6">
                    <?php $address = $viewOrder['shipping_address'] ?? []; ?>
                    <?php if (!empty($address)): ?>
                        <p class="text-admin-text">
                            <?php echo h(($address['first_name'] ?? '') . ' ' . ($address['last_name'] ?? '')); ?><br>
                            <?php echo h($address['address_line1'] ?? ''); ?><br>
                            <?php if (!empty($address['address_line2'])): ?>
                                <?php echo h($address['address_line2']); ?><br>
                            <?php endif; ?>
                            <?php echo h(($address['city'] ?? '') . ', ' . ($address['state'] ?? '')); ?><br>
                            <?php echo h($address['country'] ?? ''); ?><br>
                            <?php if (!empty($address['phone'])): ?>
                                Phone: <?php echo h($address['phone']); ?>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <p class="text-admin-muted">No shipping address provided</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Notes -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border flex items-center justify-between">
                    <h3 class="font-semibold text-admin-text">Order Notes</h3>
                </div>
                <div class="p-6">
                    <?php $notes = $viewOrder['notes'] ?? []; ?>
                    <?php if (empty($notes)): ?>
                        <p class="text-admin-muted text-center py-4">No notes yet</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($notes as $note): ?>
                                <div class="bg-admin-bg rounded-lg p-4">
                                    <p class="text-admin-text"><?php echo h($note['note']); ?></p>
                                    <p class="text-sm text-admin-muted mt-2">
                                        By <?php echo h($note['admin']); ?> on <?php echo formatDate($note['created_at'], 'M j, Y g:i A'); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form id="addNoteForm" class="mt-4">
                        <input type="hidden" name="order_id" value="<?php echo $viewOrder['id']; ?>">
                        <textarea name="note" rows="3" placeholder="Add a note..."
                                  class="w-full border border-admin-border rounded-lg px-4 py-3 text-sm"></textarea>
                        <button type="submit" class="mt-2 btn-primary px-4 py-2 rounded-lg text-sm">
                            Add Note
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Update -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Order Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-admin-muted mb-2">Order Status</label>
                        <select id="orderStatus" class="w-full border border-admin-border rounded-lg px-4 py-2"
                                data-order-id="<?php echo $viewOrder['id']; ?>">
                            <option value="pending" <?php echo ($viewOrder['order_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo ($viewOrder['order_status'] ?? '') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo ($viewOrder['order_status'] ?? '') === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo ($viewOrder['order_status'] ?? '') === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo ($viewOrder['order_status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-admin-muted mb-2">Payment Status</label>
                        <select id="paymentStatus" class="w-full border border-admin-border rounded-lg px-4 py-2"
                                data-order-id="<?php echo $viewOrder['id']; ?>">
                            <option value="pending" <?php echo ($viewOrder['payment_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo ($viewOrder['payment_status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="failed" <?php echo ($viewOrder['payment_status'] ?? '') === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            <option value="refunded" <?php echo ($viewOrder['payment_status'] ?? '') === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>

                    <button onclick="updateOrderStatus()" class="w-full btn-primary px-4 py-2 rounded-lg">
                        Update Status
                    </button>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Customer</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-admin-bg rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-admin-muted"></i>
                        </div>
                        <div>
                            <p class="font-medium text-admin-text"><?php echo h($viewOrder['user_name'] ?? 'Guest'); ?></p>
                            <p class="text-sm text-admin-muted"><?php echo h($viewOrder['user_email'] ?? ''); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($viewOrder['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>admin/customers?view=<?php echo $viewOrder['user_id']; ?>"
                           class="text-admin-accent text-sm hover:underline">View Customer Profile</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tracking -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Tracking Info</h3>
                </div>
                <div class="p-6">
                    <form id="trackingForm">
                        <input type="hidden" name="order_id" value="<?php echo $viewOrder['id']; ?>">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-admin-muted mb-2">Tracking Number</label>
                            <input type="text" name="tracking_number"
                                   value="<?php echo h($viewOrder['tracking_number'] ?? ''); ?>"
                                   class="w-full border border-admin-border rounded-lg px-4 py-2"
                                   placeholder="Enter tracking number">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-admin-muted mb-2">Tracking URL</label>
                            <input type="url" name="tracking_url"
                                   value="<?php echo h($viewOrder['tracking_url'] ?? ''); ?>"
                                   class="w-full border border-admin-border rounded-lg px-4 py-2"
                                   placeholder="https://...">
                        </div>
                        <button type="submit" class="w-full btn-secondary px-4 py-2 rounded-lg text-sm">
                            Update Tracking
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card bg-white rounded-xl">
                <div class="px-6 py-4 border-b border-admin-border">
                    <h3 class="font-semibold text-admin-text">Payment Info</h3>
                </div>
                <div class="p-6 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-admin-muted">Reference</span>
                        <span class="font-medium"><?php echo h($viewOrder['payment_ref'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-admin-muted">Method</span>
                        <span class="font-medium"><?php echo h($viewOrder['payment_method'] ?? 'Flutterwave'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-admin-muted">Status</span>
                        <span class="status-badge status-<?php echo $viewOrder['payment_status'] ?? 'pending'; ?>">
                            <?php echo ucfirst($viewOrder['payment_status'] ?? 'pending'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('orderStatus').addEventListener('change', function() {
    // Status changed - will be saved when button clicked
});

document.getElementById('paymentStatus').addEventListener('change', function() {
    // Status changed - will be saved when button clicked
});

async function updateOrderStatus() {
    const orderId = document.getElementById('orderStatus').dataset.orderId;
    const orderStatus = document.getElementById('orderStatus').value;
    const paymentStatus = document.getElementById('paymentStatus').value;

    try {
        const result = await apiRequest('api/admin/orders/update-status', 'POST', {
            id: orderId,
            order_status: orderStatus,
            payment_status: paymentStatus
        });

        if (result.success) {
            showToast('Order status updated');
        } else {
            showToast(result.message || 'Failed to update', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}

document.getElementById('addNoteForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
        const result = await apiRequest('api/admin/orders/add-note', 'POST', {
            id: formData.get('order_id'),
            note: formData.get('note')
        });

        if (result.success) {
            showToast('Note added');
            location.reload();
        } else {
            showToast(result.message || 'Failed to add note', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});

document.getElementById('trackingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
        const result = await apiRequest('api/admin/orders/update-tracking', 'POST', {
            id: formData.get('order_id'),
            tracking_number: formData.get('tracking_number'),
            tracking_url: formData.get('tracking_url')
        });

        if (result.success) {
            showToast('Tracking info updated');
        } else {
            showToast(result.message || 'Failed to update', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});
</script>

<?php else: ?>
<!-- Orders List View -->
<div class="space-y-6">
    <!-- Filters -->
    <div class="card bg-white rounded-xl p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-center">
            <div>
                <select name="status" class="border border-admin-border rounded-lg px-4 py-2 text-sm">
                    <option value="">All Order Status</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $statusFilter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <select name="payment" class="border border-admin-border rounded-lg px-4 py-2 text-sm">
                    <option value="">All Payment Status</option>
                    <option value="pending" <?php echo $paymentFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="completed" <?php echo $paymentFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="failed" <?php echo $paymentFilter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                    <option value="refunded" <?php echo $paymentFilter === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <?php if ($statusFilter || $paymentFilter): ?>
                <a href="<?php echo BASE_URL; ?>admin/orders" class="text-admin-muted hover:text-admin-text text-sm">
                    Clear Filters
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card bg-white rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-admin-bg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-admin-muted uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-admin-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-admin-muted">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p>No orders found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="table-row">
                                <td class="px-6 py-4">
                                    <a href="<?php echo BASE_URL; ?>admin/orders?view=<?php echo $order['id']; ?>"
                                       class="font-medium text-admin-accent hover:underline">
                                        <?php echo h($order['order_number']); ?>
                                    </a>
                                    <p class="text-sm text-admin-muted"><?php echo count($order['items'] ?? []); ?> items</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-admin-text"><?php echo h($order['user_name'] ?? 'Guest'); ?></p>
                                    <p class="text-sm text-admin-muted"><?php echo h($order['user_email'] ?? ''); ?></p>
                                </td>
                                <td class="px-6 py-4 text-sm text-admin-muted">
                                    <?php echo formatDate($order['created_at'], 'M j, Y'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="status-badge status-<?php echo $order['order_status'] ?? 'pending'; ?>">
                                        <?php echo ucfirst($order['order_status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="status-badge status-<?php echo $order['payment_status'] ?? 'pending'; ?>">
                                        <?php echo ucfirst($order['payment_status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    <?php echo number_format($order['total'] ?? 0); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo BASE_URL; ?>admin/orders?view=<?php echo $order['id']; ?>"
                                       class="text-admin-accent hover:text-amber-600">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

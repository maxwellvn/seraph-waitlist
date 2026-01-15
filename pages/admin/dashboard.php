<?php
$stats = getDashboardStats();
?>

<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Revenue -->
        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo number_format($stats['total_revenue']); ?></p>
                    <p class="text-green-600 text-sm mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <?php echo $stats['completed_orders']; ?> completed orders
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-naira-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm font-medium">Total Orders</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo $stats['total_orders']; ?></p>
                    <p class="text-amber-600 text-sm mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        <?php echo $stats['pending_orders']; ?> pending
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm font-medium">Total Customers</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo $stats['total_users']; ?></p>
                    <p class="text-admin-muted text-sm mt-1">Registered users</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Subscribers -->
        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm font-medium">Subscribers</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo $stats['total_subscribers']; ?></p>
                    <p class="text-admin-muted text-sm mt-1">Newsletter signups</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-envelope text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Site Visits -->
        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm font-medium">Site Visits</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo number_format($stats['site_visits']); ?></p>
                    <p class="text-admin-muted text-sm mt-1">Total page views</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-eye text-cyan-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="card bg-white rounded-xl">
            <div class="px-6 py-4 border-b border-admin-border flex items-center justify-between">
                <h3 class="font-semibold text-admin-text">Recent Orders</h3>
                <a href="<?php echo BASE_URL; ?>admin/orders" class="text-admin-accent text-sm hover:underline">View All</a>
            </div>
            <div class="p-6">
                <?php if (empty($stats['recent_orders'])): ?>
                    <p class="text-admin-muted text-center py-8">No orders yet</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($stats['recent_orders'] as $order): ?>
                            <div class="flex items-center justify-between py-3 border-b border-admin-border last:border-0">
                                <div>
                                    <p class="font-medium text-admin-text"><?php echo h($order['order_number']); ?></p>
                                    <p class="text-sm text-admin-muted"><?php echo h($order['user_name'] ?? $order['user_email']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-admin-text"><?php echo number_format($order['total'] ?? 0); ?></p>
                                    <span class="status-badge status-<?php echo $order['order_status'] ?? 'pending'; ?>">
                                        <?php echo ucfirst($order['order_status'] ?? 'pending'); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Subscribers -->
        <div class="card bg-white rounded-xl">
            <div class="px-6 py-4 border-b border-admin-border flex items-center justify-between">
                <h3 class="font-semibold text-admin-text">Recent Subscribers</h3>
                <a href="<?php echo BASE_URL; ?>admin/subscribers" class="text-admin-accent text-sm hover:underline">View All</a>
            </div>
            <div class="p-6">
                <?php if (empty($stats['recent_subscribers'])): ?>
                    <p class="text-admin-muted text-center py-8">No subscribers yet</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($stats['recent_subscribers'] as $sub): ?>
                            <div class="flex items-center justify-between py-3 border-b border-admin-border last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-admin-bg rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-admin-muted"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-admin-text"><?php echo h($sub['name'] ?? 'Subscriber'); ?></p>
                                        <p class="text-sm text-admin-muted"><?php echo h($sub['email']); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-admin-muted">
                                        <?php echo formatDate($sub['subscribed_at'] ?? $sub['created_at'] ?? '', 'M j'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card bg-white rounded-xl p-6">
        <h3 class="font-semibold text-admin-text mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="<?php echo BASE_URL; ?>admin/products?action=new" class="flex flex-col items-center justify-center p-4 bg-admin-bg rounded-xl hover:bg-gray-100 transition">
                <i class="fas fa-plus-circle text-2xl text-admin-accent mb-2"></i>
                <span class="text-sm font-medium text-admin-text">Add Product</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/orders?status=pending" class="flex flex-col items-center justify-center p-4 bg-admin-bg rounded-xl hover:bg-gray-100 transition">
                <i class="fas fa-clock text-2xl text-amber-500 mb-2"></i>
                <span class="text-sm font-medium text-admin-text">Pending Orders</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/subscribers?action=export" class="flex flex-col items-center justify-center p-4 bg-admin-bg rounded-xl hover:bg-gray-100 transition">
                <i class="fas fa-download text-2xl text-green-500 mb-2"></i>
                <span class="text-sm font-medium text-admin-text">Export Subscribers</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/analytics" class="flex flex-col items-center justify-center p-4 bg-admin-bg rounded-xl hover:bg-gray-100 transition">
                <i class="fas fa-chart-bar text-2xl text-blue-500 mb-2"></i>
                <span class="text-sm font-medium text-admin-text">View Analytics</span>
            </a>
        </div>
    </div>
</div>

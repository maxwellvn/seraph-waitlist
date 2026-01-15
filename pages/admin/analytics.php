<?php
$period = $_GET['period'] ?? 'month';
$salesAnalytics = getSalesAnalytics($period);
$subscriberAnalytics = getSubscriberAnalytics();
?>

<div class="space-y-6">
    <!-- Period Filter -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo BASE_URL; ?>admin/analytics?period=week"
               class="px-4 py-2 rounded-lg text-sm <?php echo $period === 'week' ? 'btn-primary' : 'btn-secondary'; ?>">
                Last 7 Days
            </a>
            <a href="<?php echo BASE_URL; ?>admin/analytics?period=month"
               class="px-4 py-2 rounded-lg text-sm <?php echo $period === 'month' ? 'btn-primary' : 'btn-secondary'; ?>">
                Last 30 Days
            </a>
            <a href="<?php echo BASE_URL; ?>admin/analytics?period=year"
               class="px-4 py-2 rounded-lg text-sm <?php echo $period === 'year' ? 'btn-primary' : 'btn-secondary'; ?>">
                Last Year
            </a>
        </div>
    </div>

    <!-- Sales Overview -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm">Total Sales</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo number_format($salesAnalytics['total_sales']); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-naira-sign text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm">Orders</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo $salesAnalytics['total_orders']; ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm">Avg. Order Value</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo number_format($salesAnalytics['average_order']); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-admin-muted text-sm">Subscribers</p>
                    <p class="text-2xl font-bold text-admin-text mt-1"><?php echo $subscriberAnalytics['total']; ?></p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-envelope text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Order Status Distribution -->
        <div class="card bg-white rounded-xl">
            <div class="px-6 py-4 border-b border-admin-border">
                <h3 class="font-semibold text-admin-text">Order Status Distribution</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php
                    $statusColors = [
                        'pending' => 'bg-amber-500',
                        'processing' => 'bg-blue-500',
                        'shipped' => 'bg-indigo-500',
                        'delivered' => 'bg-green-500',
                        'cancelled' => 'bg-red-500'
                    ];
                    $totalOrders = array_sum($salesAnalytics['by_status']);
                    foreach ($salesAnalytics['by_status'] as $status => $count):
                        $percentage = $totalOrders > 0 ? ($count / $totalOrders) * 100 : 0;
                    ?>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-admin-text capitalize"><?php echo $status; ?></span>
                                <span class="text-sm text-admin-muted"><?php echo $count; ?> (<?php echo round($percentage); ?>%)</span>
                            </div>
                            <div class="w-full bg-admin-bg rounded-full h-2">
                                <div class="<?php echo $statusColors[$status] ?? 'bg-gray-500'; ?> h-2 rounded-full"
                                     style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card bg-white rounded-xl">
            <div class="px-6 py-4 border-b border-admin-border">
                <h3 class="font-semibold text-admin-text">Top Products</h3>
            </div>
            <div class="p-6">
                <?php if (empty($salesAnalytics['by_product'])): ?>
                    <p class="text-admin-muted text-center py-8">No product data available</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php
                        arsort($salesAnalytics['by_product']);
                        $topProducts = array_slice($salesAnalytics['by_product'], 0, 5, true);
                        foreach ($topProducts as $product => $data):
                        ?>
                            <div class="flex items-center justify-between py-3 border-b border-admin-border last:border-0">
                                <div>
                                    <p class="font-medium text-admin-text"><?php echo h($product); ?></p>
                                    <p class="text-sm text-admin-muted"><?php echo $data['quantity']; ?> sold</p>
                                </div>
                                <p class="font-semibold text-admin-text"><?php echo number_format($data['revenue']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Sales by Date -->
        <div class="card bg-white rounded-xl">
            <div class="px-6 py-4 border-b border-admin-border">
                <h3 class="font-semibold text-admin-text">Sales Over Time</h3>
            </div>
            <div class="p-6">
                <?php if (empty($salesAnalytics['by_date'])): ?>
                    <p class="text-admin-muted text-center py-8">No sales data available</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-sm text-admin-muted">
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3">Orders</th>
                                    <th class="pb-3 text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border">
                                <?php
                                krsort($salesAnalytics['by_date']);
                                $recentDates = array_slice($salesAnalytics['by_date'], 0, 10, true);
                                foreach ($recentDates as $date => $data):
                                ?>
                                    <tr>
                                        <td class="py-3 text-admin-text"><?php echo formatDate($date, 'M j, Y'); ?></td>
                                        <td class="py-3 text-admin-muted"><?php echo $data['orders']; ?></td>
                                        <td class="py-3 text-right font-medium text-admin-text"><?php echo number_format($data['sales']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Subscriber Analytics -->
        <div class="card bg-white rounded-xl">
            <div class="px-6 py-4 border-b border-admin-border">
                <h3 class="font-semibold text-admin-text">Subscriber Insights</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- By Status -->
                <div>
                    <h4 class="text-sm font-medium text-admin-muted mb-3">By Status</h4>
                    <div class="flex gap-4">
                        <div class="flex-1 bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-green-600"><?php echo $subscriberAnalytics['active']; ?></p>
                            <p class="text-sm text-green-700">Active</p>
                        </div>
                        <div class="flex-1 bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-600"><?php echo $subscriberAnalytics['inactive']; ?></p>
                            <p class="text-sm text-gray-700">Inactive</p>
                        </div>
                    </div>
                </div>

                <!-- By Source -->
                <div>
                    <h4 class="text-sm font-medium text-admin-muted mb-3">By Source</h4>
                    <?php if (!empty($subscriberAnalytics['by_source'])): ?>
                        <div class="space-y-2">
                            <?php foreach ($subscriberAnalytics['by_source'] as $source => $count): ?>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-admin-text capitalize"><?php echo str_replace('_', ' ', $source); ?></span>
                                    <span class="font-medium text-admin-text"><?php echo $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-admin-muted text-sm">No source data available</p>
                    <?php endif; ?>
                </div>

                <!-- Top Cities -->
                <div>
                    <h4 class="text-sm font-medium text-admin-muted mb-3">Top Cities</h4>
                    <?php if (!empty($subscriberAnalytics['by_city'])): ?>
                        <?php
                        arsort($subscriberAnalytics['by_city']);
                        $topCities = array_slice($subscriberAnalytics['by_city'], 0, 5, true);
                        ?>
                        <div class="space-y-2">
                            <?php foreach ($topCities as $city => $count): ?>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-admin-text"><?php echo h($city); ?></span>
                                    <span class="font-medium text-admin-text"><?php echo $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-admin-muted text-sm">No city data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriber Growth -->
    <div class="card bg-white rounded-xl">
        <div class="px-6 py-4 border-b border-admin-border">
            <h3 class="font-semibold text-admin-text">Subscriber Growth</h3>
        </div>
        <div class="p-6">
            <?php if (empty($subscriberAnalytics['by_month'])): ?>
                <p class="text-admin-muted text-center py-8">No subscriber growth data available</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <div class="flex gap-4 min-w-max pb-4">
                        <?php
                        $maxCount = max($subscriberAnalytics['by_month']) ?: 1;
                        foreach ($subscriberAnalytics['by_month'] as $month => $count):
                            $height = ($count / $maxCount) * 150;
                        ?>
                            <div class="flex flex-col items-center">
                                <p class="text-sm font-medium text-admin-text mb-2"><?php echo $count; ?></p>
                                <div class="w-12 bg-admin-accent rounded-t"
                                     style="height: <?php echo max($height, 4); ?>px"></div>
                                <p class="text-xs text-admin-muted mt-2"><?php echo formatDate($month . '-01', 'M'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

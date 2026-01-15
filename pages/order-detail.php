<?php
$isSuccess = isset($_GET['success']);
?>

<section class="py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-4xl mx-auto px-6 lg:px-12">

        <?php if ($isSuccess): ?>
            <!-- Success Message -->
            <div class="mb-8 p-6 bg-green-50 border border-green-200 text-center reveal-up">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="font-display text-2xl text-green-800 mb-2">Order Placed Successfully!</h2>
                <p class="font-body text-green-700">Thank you for your purchase. We've sent a confirmation email.</p>
            </div>
        <?php endif; ?>

        <!-- Order Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <a href="<?php echo BASE_URL; ?>orders" class="font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors mb-2 inline-block">
                    ← Back to Orders
                </a>
                <h1 class="font-display text-3xl sm:text-4xl text-seraph-charcoal"><?php echo $order['order_number']; ?></h1>
                <p class="font-body text-seraph-slate mt-1">
                    Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                </p>
            </div>

            <?php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'delivered' => 'bg-green-100 text-green-800 border-green-200',
                    'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                ];
                $statusColor = $statusColors[$order['order_status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
            ?>
            <span class="self-start px-4 py-2 text-sm font-medium uppercase tracking-wide rounded border <?php echo $statusColor; ?>">
                <?php echo ucfirst($order['order_status']); ?>
            </span>
        </div>

        <!-- Tracking Info Banner (if shipped) -->
        <?php if (!empty($order['tracking_number']) || $order['order_status'] === 'shipped'): ?>
            <div class="mb-8 p-6 bg-purple-50 border border-purple-200">
                <div class="flex items-center gap-3 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <h3 class="font-display text-lg text-purple-800">Your Order Has Shipped!</h3>
                </div>
                <?php if (!empty($order['tracking_number'])): ?>
                    <p class="font-body text-purple-700 mb-2">
                        <span class="font-medium">Tracking Number:</span> <?php echo h($order['tracking_number']); ?>
                    </p>
                    <?php if (!empty($order['tracking_url'])): ?>
                        <a href="<?php echo h($order['tracking_url']); ?>" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 font-body text-sm text-purple-600 hover:text-purple-800 font-medium">
                            Track Your Package
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="font-body text-purple-700">Tracking information will be available soon.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-3 gap-8">

            <!-- Order Items -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-seraph-charcoal/5 p-8">
                    <h2 class="font-display text-xl text-seraph-charcoal mb-6">Items</h2>

                    <div class="space-y-4">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="flex gap-4 py-4 border-b border-seraph-charcoal/5 last:border-0">
                                <div class="w-20 h-20 flex-shrink-0 bg-seraph-cream overflow-hidden">
                                    <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo $item['image']; ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-body font-medium text-seraph-charcoal"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="font-body text-sm text-seraph-slate">Qty: <?php echo $item['quantity']; ?></p>
                                    <p class="font-body text-sm text-seraph-amber mt-1">₦<?php echo number_format($item['price']); ?> each</p>
                                </div>
                                <p class="font-body font-medium text-seraph-charcoal">
                                    ₦<?php echo number_format($item['price'] * $item['quantity']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Delivery Method / Address -->
                <?php if (($order['shipping_method'] ?? 'delivery') === 'pickup'): ?>
                    <!-- Pickup Location -->
                    <div class="bg-white border border-seraph-charcoal/5 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-seraph-amber/10 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-seraph-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h2 class="font-display text-xl text-seraph-charcoal">Pickup Location</h2>
                        </div>
                        <div class="font-body text-seraph-slate">
                            <?php if (!empty($order['pickup_address'])): ?>
                                <p class="text-seraph-charcoal"><?php echo h($order['pickup_address']); ?></p>
                            <?php else: ?>
                                <p class="text-seraph-mist italic">Pickup location will be provided</p>
                            <?php endif; ?>
                        </div>
                        <p class="mt-4 text-sm text-seraph-amber font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Please bring your order confirmation when collecting
                        </p>
                    </div>
                <?php elseif (!empty($order['shipping_address'])): ?>
                    <!-- Shipping Address -->
                    <?php $addr = $order['shipping_address']; ?>
                    <div class="bg-white border border-seraph-charcoal/5 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-seraph-amber/10 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-seraph-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h2 class="font-display text-xl text-seraph-charcoal">Delivery Address</h2>
                        </div>
                        <div class="font-body text-seraph-slate">
                            <p class="font-medium text-seraph-charcoal">
                                <?php echo h(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')); ?>
                            </p>
                            <p><?php echo h($addr['address_line1'] ?? ''); ?></p>
                            <?php if (!empty($addr['address_line2'])): ?>
                                <p><?php echo h($addr['address_line2']); ?></p>
                            <?php endif; ?>
                            <p><?php echo h(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '')); ?></p>
                            <p><?php echo h($addr['country'] ?? ''); ?></p>
                            <?php if (!empty($addr['phone'])): ?>
                                <p class="mt-2"><?php echo h($addr['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-seraph-charcoal/5 p-8 sticky top-28">
                    <h2 class="font-display text-xl text-seraph-charcoal mb-6">Summary</h2>

                    <div class="space-y-4 pb-6 border-b border-seraph-charcoal/10">
                        <div class="flex justify-between font-body">
                            <span class="text-seraph-slate">Subtotal</span>
                            <span class="text-seraph-charcoal">₦<?php echo number_format($order['subtotal']); ?></span>
                        </div>
                        <div class="flex justify-between font-body">
                            <span class="text-seraph-slate">Shipping</span>
                            <span class="text-seraph-charcoal">
                                <?php echo ($order['shipping'] ?? 0) > 0 ? '₦' . number_format($order['shipping']) : 'Free'; ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between py-6 border-b border-seraph-charcoal/10">
                        <span class="font-display text-lg text-seraph-charcoal">Total</span>
                        <span class="font-display text-lg text-seraph-charcoal">₦<?php echo number_format($order['total']); ?></span>
                    </div>

                    <div class="pt-6 space-y-3">
                        <div>
                            <p class="font-body text-xs uppercase tracking-wider text-seraph-mist mb-1">Order Status</p>
                            <p class="font-body text-sm text-seraph-charcoal capitalize"><?php echo $order['order_status']; ?></p>
                        </div>
                        <div>
                            <p class="font-body text-xs uppercase tracking-wider text-seraph-mist mb-1">Payment Status</p>
                            <?php
                                $paymentStatusColors = [
                                    'completed' => 'text-green-600',
                                    'paid' => 'text-green-600',
                                    'pending' => 'text-yellow-600',
                                    'failed' => 'text-red-600',
                                    'refunded' => 'text-purple-600',
                                ];
                                $paymentColor = $paymentStatusColors[$order['payment_status']] ?? 'text-seraph-charcoal';
                            ?>
                            <p class="font-body text-sm capitalize <?php echo $paymentColor; ?>"><?php echo $order['payment_status']; ?></p>
                        </div>
                        <?php if (!empty($order['payment_ref'])): ?>
                        <div>
                            <p class="font-body text-xs uppercase tracking-wider text-seraph-mist mb-1">Transaction ID</p>
                            <p class="font-body text-sm text-seraph-charcoal"><?php echo $order['payment_ref']; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Order Timeline -->
        <div class="mt-12 bg-white border border-seraph-charcoal/5 p-8">
            <h2 class="font-display text-xl text-seraph-charcoal mb-6">Order Timeline</h2>

            <?php
            $timeline = [
                ['status' => 'pending', 'label' => 'Order Placed', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['status' => 'processing', 'label' => 'Processing', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['status' => 'shipped', 'label' => 'Shipped', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                ['status' => 'delivered', 'label' => 'Delivered', 'icon' => 'M5 13l4 4L19 7'],
            ];

            $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
            $currentIndex = array_search($order['order_status'], $statusOrder);
            if ($currentIndex === false) $currentIndex = 0;
            ?>

            <div class="flex items-center justify-between">
                <?php foreach ($timeline as $idx => $step): ?>
                    <?php
                    $isCompleted = $idx <= $currentIndex;
                    $isCurrent = $idx === $currentIndex;
                    $bgColor = $isCompleted ? 'bg-seraph-amber' : 'bg-gray-200';
                    $textColor = $isCompleted ? 'text-white' : 'text-gray-400';
                    $labelColor = $isCompleted ? 'text-seraph-charcoal' : 'text-seraph-slate';
                    ?>
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-10 h-10 rounded-full <?php echo $bgColor; ?> flex items-center justify-center <?php echo $isCurrent ? 'ring-4 ring-seraph-amber/30' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 <?php echo $textColor; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $step['icon']; ?>" />
                            </svg>
                        </div>
                        <p class="font-body text-xs mt-2 text-center <?php echo $labelColor; ?>"><?php echo $step['label']; ?></p>
                    </div>
                    <?php if ($idx < count($timeline) - 1): ?>
                        <div class="flex-1 h-1 <?php echo $idx < $currentIndex ? 'bg-seraph-amber' : 'bg-gray-200'; ?> -mt-6"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Help -->
        <div class="mt-12 p-6 bg-seraph-cream/50 border border-seraph-charcoal/5 text-center">
            <h3 class="font-display text-lg text-seraph-charcoal mb-2">Need Help?</h3>
            <p class="font-body text-seraph-slate mb-4">If you have any questions about your order, contact us.</p>
            <a href="mailto:info@seraph-oral.org" class="font-body text-seraph-amber hover:text-seraph-amber-dark transition-colors">
                info@seraph-oral.org
            </a>
        </div>

    </div>
</section>

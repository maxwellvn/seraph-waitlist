<section class="py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-4xl mx-auto px-6 lg:px-12">
        
        <!-- Header -->
        <div class="mb-12">
            <h1 class="font-display text-4xl sm:text-5xl text-seraph-charcoal">My Orders</h1>
        </div>

        <?php if (empty($orders)): ?>
            <!-- No Orders -->
            <div class="text-center py-20 bg-white border border-seraph-charcoal/5">
                <div class="w-20 h-20 mx-auto mb-6 bg-seraph-cream rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-seraph-mist" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="font-display text-2xl text-seraph-charcoal mb-4">No orders yet</h2>
                <p class="font-body text-seraph-slate mb-8">When you place an order, it will appear here.</p>
                <a href="<?php echo BASE_URL; ?>products" class="btn-primary inline-block px-8 py-4 font-medium tracking-wide">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <!-- Orders List -->
            <div class="space-y-6">
                <?php foreach ($orders as $order): ?>
                    <a href="<?php echo BASE_URL; ?>orders/<?php echo $order['id']; ?>" class="block bg-white border border-seraph-charcoal/5 p-6 hover:border-seraph-amber/30 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                            <div>
                                <h2 class="font-display text-xl text-seraph-charcoal"><?php echo $order['order_number']; ?></h2>
                                <p class="font-body text-sm text-seraph-slate">
                                    <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <!-- Status Badge -->
                                <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'shipped' => 'bg-purple-100 text-purple-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusColor = $statusColors[$order['order_status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-3 py-1 text-xs font-medium uppercase tracking-wide rounded-full <?php echo $statusColor; ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                                
                                <span class="font-display text-lg text-seraph-charcoal">
                                    ₦<?php echo number_format($order['total']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Items Preview -->
                        <div class="flex gap-2">
                            <?php 
                            $maxItems = 4;
                            $items = $order['items'];
                            $remaining = count($items) - $maxItems;
                            ?>
                            <?php foreach (array_slice($items, 0, $maxItems) as $item): ?>
                                <div class="w-12 h-12 bg-seraph-cream overflow-hidden flex-shrink-0">
                                    <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo $item['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="w-full h-full object-cover">
                                </div>
                            <?php endforeach; ?>
                            <?php if ($remaining > 0): ?>
                                <div class="w-12 h-12 bg-seraph-cream flex items-center justify-center flex-shrink-0">
                                    <span class="font-body text-xs text-seraph-slate">+<?php echo $remaining; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

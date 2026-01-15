<?php
$cart = getCart();
$cartTotal = getCartTotal();
$cartCount = getCartCount();
?>

<section class="pt-24 pb-32 sm:py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-6xl mx-auto px-0 sm:px-6 lg:px-12">

        <!-- Header -->
        <div class="mb-6 sm:mb-12 px-4 sm:px-0">
            <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-seraph-charcoal">Your Cart</h1>
            <?php if (!empty($cart)): ?>
            <p class="text-seraph-slate text-sm mt-2"><?php echo $cartCount; ?> item<?php echo $cartCount > 1 ? 's' : ''; ?></p>
            <?php endif; ?>
        </div>

        <?php if (empty($cart)): ?>
            <!-- Empty Cart -->
            <div class="text-center py-16 sm:py-20 bg-white border-y sm:border border-seraph-charcoal/5 mx-0 sm:mx-0">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6 bg-seraph-cream rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-10 sm:h-10 text-seraph-mist" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="font-display text-xl sm:text-2xl text-seraph-charcoal mb-4">Your cart is empty</h2>
                <p class="font-body text-seraph-slate mb-8 text-sm sm:text-base px-4">Looks like you haven't added anything yet.</p>
                <a href="<?php echo BASE_URL; ?>products" class="btn-primary inline-block px-6 sm:px-8 py-3 sm:py-4 font-medium tracking-wide text-sm sm:text-base">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="grid lg:grid-cols-3 gap-0 sm:gap-6 lg:gap-12">

                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-0 sm:space-y-4" id="cartItems">
                    <?php foreach ($cart as $item): ?>
                        <div class="cart-item bg-white border-b sm:border border-seraph-charcoal/5 p-4 sm:p-6" data-product-id="<?php echo $item['product_id']; ?>">
                            <div class="flex gap-3 sm:gap-6">
                                <!-- Image -->
                                <div class="w-16 h-16 sm:w-24 sm:h-24 flex-shrink-0 bg-seraph-cream overflow-hidden rounded">
                                    <?php if (!empty($item['image'])): ?>
                                    <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo htmlspecialchars($item['image']); ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>

                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-2 mb-1 sm:mb-2">
                                        <h3 class="font-display text-base sm:text-lg text-seraph-charcoal leading-tight truncate"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <button onclick="removeItem(<?php echo $item['product_id']; ?>)" class="text-seraph-mist hover:text-red-500 transition-colors flex-shrink-0 p-1 -m-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <p class="font-body text-seraph-amber font-medium text-sm sm:text-base"><?php echo formatPrice($item['prices'] ?? $item['price']); ?></p>

                                    <!-- Mobile: Quantity and Total in row -->
                                    <div class="flex items-center justify-between gap-4 mt-3 sm:mt-4">
                                        <!-- Quantity -->
                                        <div class="flex items-center border border-seraph-charcoal/10 flex-shrink-0">
                                            <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['quantity'] - 1; ?>)"
                                                    class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center text-seraph-charcoal hover:bg-seraph-cream transition-colors active:bg-seraph-cream">
                                                −
                                            </button>
                                            <span class="w-8 sm:w-12 text-center font-body text-sm sm:text-base item-quantity"><?php echo $item['quantity']; ?></span>
                                            <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                                    class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center text-seraph-charcoal hover:bg-seraph-cream transition-colors active:bg-seraph-cream">
                                                +
                                            </button>
                                        </div>

                                        <!-- Item Total -->
                                        <p class="font-body font-semibold text-seraph-charcoal text-sm sm:text-base item-total text-right flex-shrink-0">
                                            <?php
                                            if (isset($item['prices']) && is_array($item['prices'])) {
                                                $itemPrices = [
                                                    'NGN' => ($item['prices']['NGN'] ?? 0) * $item['quantity'],
                                                    'GBP' => ($item['prices']['GBP'] ?? 0) * $item['quantity'],
                                                    'USD' => ($item['prices']['USD'] ?? 0) * $item['quantity']
                                                ];
                                                echo formatPrice($itemPrices);
                                            } else {
                                                $legacyPrice = ($item['price'] ?? 0) * $item['quantity'];
                                                echo '₦' . number_format($legacyPrice);
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Summary - Desktop -->
                <div class="lg:col-span-1 hidden lg:block">
                    <div class="bg-white border border-seraph-charcoal/5 p-8 sticky top-28">
                        <h2 class="font-display text-xl text-seraph-charcoal mb-6">Order Summary</h2>

                        <div class="space-y-4 pb-6 border-b border-seraph-charcoal/10">
                            <div class="flex justify-between font-body">
                                <span class="text-seraph-slate">Subtotal</span>
                                <span class="text-seraph-charcoal" id="subtotal"><?php echo getFormattedCartTotal(); ?></span>
                            </div>
                            <div class="flex justify-between font-body">
                                <span class="text-seraph-slate">Shipping</span>
                                <span class="text-seraph-charcoal">Calculated at checkout</span>
                            </div>
                        </div>

                        <div class="flex justify-between py-6 border-b border-seraph-charcoal/10">
                            <span class="font-display text-lg text-seraph-charcoal">Total</span>
                            <span class="font-display text-lg text-seraph-charcoal" id="cartTotal"><?php echo getFormattedCartTotal(); ?></span>
                        </div>

                        <a href="<?php echo BASE_URL; ?>checkout" class="btn-primary block text-center w-full py-4 font-medium tracking-wide mt-6">
                            Proceed to Checkout
                        </a>

                        <a href="<?php echo BASE_URL; ?>products" class="block text-center mt-4 font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Order Summary - Mobile (Inline) -->
                <div class="lg:hidden mt-4">
                    <div class="bg-white border-y sm:border border-seraph-charcoal/5 p-4 sm:p-6">
                        <h2 class="font-display text-lg text-seraph-charcoal mb-4">Order Summary</h2>

                        <div class="space-y-3 pb-4 border-b border-seraph-charcoal/10">
                            <div class="flex justify-between font-body text-sm">
                                <span class="text-seraph-slate">Subtotal</span>
                                <span class="text-seraph-charcoal"><?php echo getFormattedCartTotal(); ?></span>
                            </div>
                            <div class="flex justify-between font-body text-sm">
                                <span class="text-seraph-slate">Shipping</span>
                                <span class="text-seraph-charcoal">Calculated at checkout</span>
                            </div>
                        </div>

                        <div class="flex justify-between py-4">
                            <span class="font-display text-lg text-seraph-charcoal">Total</span>
                            <span class="font-display text-lg text-seraph-charcoal"><?php echo getFormattedCartTotal(); ?></span>
                        </div>

                        <a href="<?php echo BASE_URL; ?>products" class="block text-center font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                            Continue Shopping
                        </a>
                    </div>
                </div>

            </div>

            <!-- Mobile Fixed Checkout Button -->
            <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-seraph-charcoal/10 p-4 z-50 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-body text-seraph-slate text-sm">Total</span>
                    <span class="font-display text-lg text-seraph-charcoal"><?php echo getFormattedCartTotal(); ?></span>
                </div>
                <a href="<?php echo BASE_URL; ?>checkout" class="btn-primary block text-center w-full py-4 font-medium tracking-wide">
                    Proceed to Checkout
                </a>
            </div>

            <!-- Spacer for fixed bottom bar on mobile -->
            <div class="lg:hidden h-32"></div>
        <?php endif; ?>
    </div>
</section>

<script>
    const baseUrl = '<?php echo BASE_URL; ?>';

    async function updateQuantity(productId, quantity) {
        if (quantity <= 0) {
            removeItem(productId);
            return;
        }

        try {
            const response = await fetch(baseUrl + 'api/cart/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity })
            });

            const data = await response.json();
            if (data.success) {
                location.reload();
            }
        } catch (error) {
            console.error('Error updating cart:', error);
        }
    }

    async function removeItem(productId) {
        try {
            const response = await fetch(baseUrl + 'api/cart/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();
            if (data.success) {
                location.reload();
            }
        } catch (error) {
            console.error('Error removing item:', error);
        }
    }
</script>

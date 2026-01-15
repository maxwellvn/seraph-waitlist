<?php
$cartCount = getCartCount();
?>

<section class="py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-16">
            <div>
                <p class="font-accent text-sm tracking-widest uppercase text-seraph-amber mb-2">
                    Shop
                </p>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl text-seraph-charcoal">
                    Our Collection
                </h1>
            </div>
            
            <!-- Cart Button -->
            <a href="<?php echo BASE_URL; ?>cart" class="inline-flex items-center gap-3 btn-outline px-6 py-3 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Cart
                <?php if ($cartCount > 0): ?>
                    <span class="w-6 h-6 bg-seraph-amber text-white text-xs rounded-full flex items-center justify-center" id="cartBadge">
                        <?php echo $cartCount; ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 xl:gap-12 stagger-children">
            
            <?php if (empty($products)): ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-seraph-slate text-lg">No products available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="group bg-white rounded-lg overflow-hidden border border-seraph-charcoal/5 hover:border-seraph-amber/30 transition-all duration-500">
                        <a href="<?php echo BASE_URL; ?>products/<?php echo $product['id']; ?>" class="block relative aspect-[4/5] bg-seraph-cream overflow-hidden">
                            <?php
                            // Use hover_image if available, otherwise fallback to main image
                            $hoverImage = !empty($product['hover_image']) ? $product['hover_image'] : $product['image'];
                            ?>

                            <!-- Main Image -->
                            <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo $product['image']; ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700 opacity-100 group-hover:opacity-0 z-10">

                            <!-- Hover Image -->
                            <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo $hoverImage; ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 scale-105 group-hover:scale-100 opacity-0 group-hover:opacity-100 z-0">
                            
                            <!-- Flavour Tag -->
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-4 py-1 rounded-full z-20">
                                <span class="font-accent text-xs tracking-wider uppercase text-seraph-charcoal">
                                    <?php echo htmlspecialchars($product['flavour'] ?? 'Natural'); ?>
                                </span>
                            </div>
                        </a>

                        <!-- Content -->
                        <div class="p-8">
                            <a href="<?php echo BASE_URL; ?>products/<?php echo $product['id']; ?>">
                                <h3 class="font-display text-2xl text-seraph-charcoal mb-2 hover:text-seraph-amber transition-colors">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>
                            </a>
                            
                            <p class="font-body text-seraph-amber text-lg font-medium mb-4">
                                <?php echo formatPrice($product['prices'] ?? $product['price']); ?>
                            </p>
                            
                            <p class="font-body text-seraph-slate text-sm leading-relaxed mb-8 line-clamp-3">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>

                            <!-- Add to Cart Button -->
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                    class="add-to-cart-btn w-full btn-primary py-4 font-medium tracking-wide"
                                    data-product-id="<?php echo $product['id']; ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-8 sm:bottom-8 sm:w-auto bg-seraph-charcoal text-white px-6 py-4 rounded-lg shadow-2xl transform translate-y-full opacity-0 transition-all duration-300 z-[9999] flex items-center gap-3">
    <svg id="toastIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    <p class="font-body text-sm" id="toastMessage">Added to cart!</p>
</div>

<script>
    const baseUrl = '<?php echo BASE_URL; ?>';
    
    async function addToCart(productId) {
        const btn = document.querySelector(`button[data-product-id="${productId}"]`);
        const originalText = btn.innerText;
        const originalClasses = btn.className;

        // Visual feedback - change button appearance
        btn.innerText = 'Adding...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        try {
            const response = await fetch(baseUrl + 'api/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            });

            const data = await response.json();

            if (data.success) {
                // Success state on button
                btn.innerHTML = '<svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                btn.classList.remove('opacity-70');
                btn.classList.add('bg-green-600', 'border-green-600');

                showToast('Added to cart!', true);
                updateCartBadge(data.cartCount);

                // Reset button after delay
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.className = originalClasses;
                    btn.disabled = false;
                }, 1500);
            } else {
                showToast(data.message || 'Failed to add to cart', false);
                btn.innerText = originalText;
                btn.className = originalClasses;
                btn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Something went wrong', false);
            btn.innerText = originalText;
            btn.className = originalClasses;
            btn.disabled = false;
        }
    }

    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        toastMessage.innerText = message;

        // Update icon based on success/error
        if (isSuccess) {
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
            toastIcon.classList.remove('text-red-400');
            toastIcon.classList.add('text-green-400');
        } else {
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            toastIcon.classList.remove('text-green-400');
            toastIcon.classList.add('text-red-400');
        }

        // Show toast
        toast.classList.remove('translate-y-full', 'opacity-0');

        // Vibrate on mobile for haptic feedback (if supported)
        if (navigator.vibrate) {
            navigator.vibrate(isSuccess ? 50 : [50, 50, 50]);
        }

        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
        }, 3000);
    }
    
    function updateCartBadge(count) {
        // Update page cart badge
        let badge = document.getElementById('cartBadge');

        if (count > 0) {
            if (!badge) {
                // Create badge if it doesn't exist
                const cartLink = document.querySelector('a[href$="cart"]');
                if (cartLink) {
                    badge = document.createElement('span');
                    badge.id = 'cartBadge';
                    badge.className = 'w-6 h-6 bg-seraph-amber text-white text-xs rounded-full flex items-center justify-center';
                    cartLink.appendChild(badge);
                }
            }
            if (badge) badge.innerText = count;
        } else if (badge) {
            badge.remove();
        }

        // Also update header badges (desktop and mobile)
        const headerBadge = document.getElementById('headerCartBadge');
        const mobileBadge = document.getElementById('mobileCartBadge');

        [headerBadge, mobileBadge].forEach(b => {
            if (b) {
                if (count > 0) {
                    b.innerText = count;
                    b.classList.remove('hidden');
                } else {
                    b.classList.add('hidden');
                }
            }
        });
    }
</script>

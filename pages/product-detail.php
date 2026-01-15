<section class="pt-24 pb-32 sm:py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-12">

        <div class="mb-6 sm:mb-8">
            <a href="<?php echo BASE_URL; ?>products" class="inline-flex items-center text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Shop
            </a>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16">

            <!-- Image Gallery -->
            <div class="space-y-3 sm:space-y-4">
                <?php
                // Build gallery array - main image first, then gallery images
                $allImages = [$product['image']];
                if (!empty($product['gallery']) && is_array($product['gallery'])) {
                    $allImages = array_merge($allImages, $product['gallery']);
                }
                $allImages = array_filter($allImages); // Remove empty values
                $allImages = array_unique($allImages); // Remove duplicates
                $allImages = array_values($allImages); // Re-index
                $imageCount = count($allImages);
                ?>

                <!-- Main Image Container with Swipe -->
                <div class="relative">
                    <!-- Swipeable Gallery -->
                    <div id="galleryContainer" class="aspect-[4/5] bg-seraph-cream overflow-hidden rounded-lg border border-seraph-charcoal/5 relative touch-pan-y">
                        <div id="galleryTrack" class="flex h-full transition-transform duration-300 ease-out" style="width: <?php echo $imageCount * 100; ?>%;">
                            <?php foreach ($allImages as $index => $img): ?>
                                <div class="h-full flex-shrink-0" style="width: <?php echo 100 / $imageCount; ?>%;">
                                    <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo $img; ?>"
                                         alt="<?php echo htmlspecialchars($product['name']); ?> - Image <?php echo $index + 1; ?>"
                                         class="w-full h-full object-cover"
                                         draggable="false">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($imageCount > 1): ?>
                    <!-- Navigation Arrows -->
                    <button id="prevBtn" onclick="changeSlide(-1)"
                            class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all opacity-0 pointer-events-none z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-seraph-charcoal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button id="nextBtn" onclick="changeSlide(1)"
                            class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-seraph-charcoal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Dot Indicators (Desktop only) -->
                    <div class="hidden sm:flex absolute bottom-4 left-1/2 -translate-x-1/2 gap-1.5 z-10 bg-black/20 px-2 py-1.5 rounded-full">
                        <?php for ($i = 0; $i < $imageCount; $i++): ?>
                            <button onclick="goToSlide(<?php echo $i; ?>)"
                                    class="gallery-dot w-2 h-2 rounded-full transition-all <?php echo $i === 0 ? 'bg-white' : 'bg-white/50'; ?>"
                                    data-index="<?php echo $i; ?>"></button>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Thumbnails (Desktop only) -->
                <?php if ($imageCount > 1): ?>
                    <div class="hidden sm:flex gap-3 overflow-x-auto pb-2">
                        <?php foreach ($allImages as $index => $img): ?>
                            <button onclick="goToSlide(<?php echo $index; ?>)"
                                    class="flex-shrink-0 w-20 h-20 lg:w-24 lg:h-24 rounded-lg overflow-hidden border-2 transition-all thumbnail-btn <?php echo $index === 0 ? 'border-seraph-amber' : 'border-transparent hover:border-seraph-charcoal/20'; ?>"
                                    data-index="<?php echo $index; ?>">
                                <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo $img; ?>"
                                     alt="<?php echo htmlspecialchars($product['name']); ?> - Thumbnail <?php echo $index + 1; ?>"
                                     class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Details Column -->
            <div class="lg:sticky lg:top-28 lg:self-start">
                <div class="mb-2">
                    <span class="font-accent text-xs sm:text-sm tracking-widest uppercase text-seraph-amber">
                        <?php echo htmlspecialchars($product['flavour'] ?? 'Natural'); ?>
                    </span>
                </div>

                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-seraph-charcoal mb-4 sm:mb-6">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>

                <p class="font-body text-xl sm:text-2xl text-seraph-charcoal font-medium mb-6 sm:mb-8">
                    <?php echo formatPrice($product['prices'] ?? $product['price']); ?>
                </p>

                <div class="prose prose-seraph text-seraph-slate mb-8 sm:mb-10 text-sm sm:text-base">
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="border-t border-b border-seraph-charcoal/10 py-4 sm:py-6 mb-6 sm:mb-8">
                    <div class="flex items-center gap-2 text-seraph-charcoal text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        In Stock & Ready to Ship
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <div class="sm:w-32">
                        <label class="sr-only">Quantity</label>
                        <div class="flex items-center border border-seraph-charcoal/20 rounded h-12 sm:h-14">
                            <button class="w-12 sm:w-10 h-full flex items-center justify-center text-seraph-charcoal hover:bg-seraph-charcoal/5 active:bg-seraph-charcoal/10" onclick="updateQty(-1)">-</button>
                            <input type="number" id="qty" value="1" min="1" class="w-full h-full text-center bg-transparent border-0 focus:ring-0 p-0 font-medium text-seraph-charcoal" readonly>
                            <button class="w-12 sm:w-10 h-full flex items-center justify-center text-seraph-charcoal hover:bg-seraph-charcoal/5 active:bg-seraph-charcoal/10" onclick="updateQty(1)">+</button>
                        </div>
                    </div>

                    <button onclick="addToCart(<?php echo $product['id']; ?>)"
                            class="flex-1 btn-primary h-12 sm:h-14 flex items-center justify-center font-medium tracking-wide">
                        Add to Cart
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="mt-8 sm:mt-12 space-y-4">
                    <details class="group border-b border-seraph-charcoal/10 pb-4">
                        <summary class="flex justify-between items-center cursor-pointer list-none font-display text-base sm:text-lg text-seraph-charcoal">
                            <span>Ingredients</span>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="text-seraph-slate mt-4 text-sm leading-relaxed">
                            <p>Calcium Carbonate, Aqua, Glycerin, Xylitol, Silica, Sodium Lauroyl Sarcosinate, Natural Flavor, Stevia Rebaudiana Extract, Xanthan Gum, Mentha Piperita (Peppermint) Oil.</p>
                        </div>
                    </details>

                    <details class="group border-b border-seraph-charcoal/10 pb-4">
                        <summary class="flex justify-between items-center cursor-pointer list-none font-display text-base sm:text-lg text-seraph-charcoal">
                            <span>How to Use</span>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="text-seraph-slate mt-4 text-sm leading-relaxed">
                            <p>Apply a pea-sized amount to your toothbrush. Brush thoroughly for two minutes twice a day. Smile confidently.</p>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Toast -->
<div id="toast" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-8 sm:bottom-8 sm:w-auto bg-seraph-charcoal text-white px-6 py-4 rounded-lg shadow-2xl transform translate-y-full opacity-0 transition-all duration-300 z-[9999] flex items-center gap-3">
    <svg id="toastIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    <p class="font-body text-sm" id="toastMessage">Added to cart!</p>
</div>

<script>
    const baseUrl = '<?php echo BASE_URL; ?>';
    const imageCount = <?php echo $imageCount; ?>;
    let currentSlide = 0;

    // Gallery elements
    const galleryContainer = document.getElementById('galleryContainer');
    const galleryTrack = document.getElementById('galleryTrack');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dots = document.querySelectorAll('.gallery-dot');
    const thumbnails = document.querySelectorAll('.thumbnail-btn');
    const slideNumEl = document.getElementById('currentSlideNum');

    // Touch handling
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartY = 0;
    let touchEndY = 0;
    let isDragging = false;
    let startTranslate = 0;
    let currentTranslate = 0;

    if (galleryContainer && imageCount > 1) {
        // Touch events for swipe
        galleryContainer.addEventListener('touchstart', handleTouchStart, { passive: true });
        galleryContainer.addEventListener('touchmove', handleTouchMove, { passive: false });
        galleryContainer.addEventListener('touchend', handleTouchEnd);

        // Mouse events for desktop drag
        galleryContainer.addEventListener('mousedown', handleMouseDown);
        galleryContainer.addEventListener('mousemove', handleMouseMove);
        galleryContainer.addEventListener('mouseup', handleMouseUp);
        galleryContainer.addEventListener('mouseleave', handleMouseUp);
    }

    function handleTouchStart(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        isDragging = true;
        startTranslate = currentSlide * -100;
        galleryTrack.style.transition = 'none';
    }

    function handleTouchMove(e) {
        if (!isDragging) return;

        touchEndX = e.touches[0].clientX;
        touchEndY = e.touches[0].clientY;

        const diffX = touchEndX - touchStartX;
        const diffY = touchEndY - touchStartY;

        // Only prevent default if horizontal swipe is dominant
        if (Math.abs(diffX) > Math.abs(diffY)) {
            e.preventDefault();
            const containerWidth = galleryContainer.offsetWidth;
            const movePercent = (diffX / containerWidth) * 100;
            currentTranslate = startTranslate + movePercent / imageCount;
            galleryTrack.style.transform = `translateX(${currentTranslate}%)`;
        }
    }

    function handleTouchEnd() {
        if (!isDragging) return;
        isDragging = false;
        galleryTrack.style.transition = 'transform 0.3s ease-out';

        const diffX = touchEndX - touchStartX;
        const threshold = 50; // Minimum swipe distance

        if (Math.abs(diffX) > threshold) {
            if (diffX > 0 && currentSlide > 0) {
                // Swipe right - go to previous
                changeSlide(-1);
            } else if (diffX < 0 && currentSlide < imageCount - 1) {
                // Swipe left - go to next
                changeSlide(1);
            } else {
                // Snap back
                updateGalleryPosition();
            }
        } else {
            // Snap back
            updateGalleryPosition();
        }

        touchStartX = 0;
        touchEndX = 0;
    }

    function handleMouseDown(e) {
        e.preventDefault();
        touchStartX = e.clientX;
        isDragging = true;
        startTranslate = currentSlide * -100;
        galleryTrack.style.transition = 'none';
        galleryContainer.style.cursor = 'grabbing';
    }

    function handleMouseMove(e) {
        if (!isDragging) return;
        touchEndX = e.clientX;
        const diffX = touchEndX - touchStartX;
        const containerWidth = galleryContainer.offsetWidth;
        const movePercent = (diffX / containerWidth) * 100;
        currentTranslate = startTranslate + movePercent / imageCount;
        galleryTrack.style.transform = `translateX(${currentTranslate}%)`;
    }

    function handleMouseUp() {
        if (!isDragging) return;
        isDragging = false;
        galleryTrack.style.transition = 'transform 0.3s ease-out';
        galleryContainer.style.cursor = '';

        const diffX = touchEndX - touchStartX;
        const threshold = 50;

        if (Math.abs(diffX) > threshold) {
            if (diffX > 0 && currentSlide > 0) {
                changeSlide(-1);
            } else if (diffX < 0 && currentSlide < imageCount - 1) {
                changeSlide(1);
            } else {
                updateGalleryPosition();
            }
        } else {
            updateGalleryPosition();
        }

        touchStartX = 0;
        touchEndX = 0;
    }

    function changeSlide(direction) {
        const newSlide = currentSlide + direction;
        if (newSlide >= 0 && newSlide < imageCount) {
            goToSlide(newSlide);
        }
    }

    function goToSlide(index) {
        currentSlide = index;
        updateGalleryPosition();
        updateIndicators();
    }

    function updateGalleryPosition() {
        const translateX = -(currentSlide * (100 / imageCount));
        galleryTrack.style.transform = `translateX(${translateX}%)`;
    }

    function updateIndicators() {
        // Update dots
        dots.forEach((dot, i) => {
            if (i === currentSlide) {
                dot.classList.add('bg-white');
                dot.classList.remove('bg-white/50');
            } else {
                dot.classList.remove('bg-white');
                dot.classList.add('bg-white/50');
            }
        });

        // Update thumbnails
        thumbnails.forEach((thumb, i) => {
            if (i === currentSlide) {
                thumb.classList.add('border-seraph-amber');
                thumb.classList.remove('border-transparent');
            } else {
                thumb.classList.remove('border-seraph-amber');
                thumb.classList.add('border-transparent');
            }
        });

        // Update slide number
        if (slideNumEl) {
            slideNumEl.textContent = currentSlide + 1;
        }

        // Update arrow visibility
        if (prevBtn) {
            if (currentSlide === 0) {
                prevBtn.classList.add('opacity-0', 'pointer-events-none');
            } else {
                prevBtn.classList.remove('opacity-0', 'pointer-events-none');
            }
        }
        if (nextBtn) {
            if (currentSlide === imageCount - 1) {
                nextBtn.classList.add('opacity-0', 'pointer-events-none');
            } else {
                nextBtn.classList.remove('opacity-0', 'pointer-events-none');
            }
        }
    }

    // Quantity functions
    function updateQty(change) {
        const input = document.getElementById('qty');
        let val = parseInt(input.value) + change;
        if (val < 1) val = 1;
        input.value = val;
    }

    async function addToCart(productId) {
        const qty = parseInt(document.getElementById('qty').value);
        const btn = document.querySelector('button[onclick^="addToCart"]');
        const originalText = btn.innerText;
        const originalClasses = btn.className;

        btn.innerText = 'Adding...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        try {
            const response = await fetch(baseUrl + 'api/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: qty })
            });

            const data = await response.json();

            if (data.success) {
                btn.innerHTML = '<svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                btn.classList.remove('opacity-70');
                btn.classList.add('bg-green-600', 'border-green-600');

                showToast('Added to cart!', true);

                const headerBadge = document.getElementById('headerCartBadge');
                const mobileBadge = document.getElementById('mobileCartBadge');

                [headerBadge, mobileBadge].forEach(badge => {
                    if (badge) {
                        badge.innerText = data.cartCount;
                        badge.classList.remove('hidden');
                    }
                });

                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.className = originalClasses;
                    btn.disabled = false;
                }, 1500);
            } else {
                showToast(data.message || 'Failed', false);
                btn.innerText = originalText;
                btn.className = originalClasses;
                btn.disabled = false;
            }
        } catch (error) {
            showToast('Error adding to cart', false);
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

        if (isSuccess) {
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
            toastIcon.classList.remove('text-red-400');
            toastIcon.classList.add('text-green-400');
        } else {
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            toastIcon.classList.remove('text-green-400');
            toastIcon.classList.add('text-red-400');
        }

        toast.classList.remove('translate-y-full', 'opacity-0');

        if (navigator.vibrate) {
            navigator.vibrate(isSuccess ? 50 : [50, 50, 50]);
        }

        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
        }, 3000);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        if (imageCount > 1) {
            updateIndicators();
        }
    });
</script>

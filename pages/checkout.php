<?php
$cart = getCart();
$cartTotal = getCartTotal();
$currentCurrency = getCurrentCurrency();
$currencyDetails = getCurrencyDetails();
$user = getCurrentUser();
$addresses = getUserAddresses();
$shippingSettings = getShippingSettings();
$promotions = getPromotionSettings();

// Check pickup availability for current region
$pickupConfig = $shippingSettings['pickup'][$currentCurrency] ?? ['enabled' => false, 'address' => ''];
$pickupAvailable = $pickupConfig['enabled'] ?? false;
$pickupAddress = $pickupConfig['address'] ?? '';

// Default shipping method
$defaultShipping = $shippingSettings['enable_delivery'] ? 'delivery' : ($pickupAvailable ? 'pickup' : 'delivery');
$shippingCost = getShippingCost($defaultShipping);
$orderTotal = getOrderTotal($defaultShipping);

// Check free shipping threshold
$freeShippingThreshold = $promotions['free_shipping_threshold'][$currentCurrency] ?? 0;
$qualifiesForFreeShipping = $promotions['free_shipping_enabled'] && $cartTotal >= $freeShippingThreshold;

// Flutterwave script
$additionalScripts = '
<script src="https://checkout.flutterwave.com/v3.js"></script>
';
?>

<section class="pt-24 pb-40 sm:py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-12">

        <!-- Header -->
        <div class="mb-6 sm:mb-12">
            <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-seraph-charcoal">Checkout</h1>
        </div>

        <div class="grid lg:grid-cols-5 gap-6 lg:gap-8">

            <div class="lg:col-span-3 space-y-4 sm:space-y-6">

                <!-- Delivery Method -->
                <div class="bg-white border border-seraph-charcoal/5 p-4 sm:p-6 lg:p-8">
                    <h2 class="font-display text-lg sm:text-xl text-seraph-charcoal mb-4 sm:mb-6">Delivery Method</h2>

                    <div class="space-y-3">
                        <?php if ($shippingSettings['enable_delivery']): ?>
                        <label class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 border rounded-lg cursor-pointer transition-all active:bg-seraph-cream/50 shipping-option border-seraph-amber bg-seraph-cream/50" data-method="delivery">
                            <input type="radio" name="shipping_method" value="delivery" checked
                                   class="text-seraph-amber focus:ring-seraph-amber w-4 h-4"
                                   onchange="updateShipping('delivery')">
                            <div class="flex-1 min-w-0">
                                <span class="block font-medium text-seraph-charcoal text-sm sm:text-base">
                                    <i class="fas fa-truck mr-2 text-seraph-amber"></i>Delivery
                                </span>
                                <span class="text-xs sm:text-sm text-seraph-slate">Shipped to your address</span>
                            </div>
                            <span class="font-medium text-seraph-charcoal text-sm sm:text-base flex-shrink-0" id="deliveryCost">
                                <?php echo getFormattedShippingCost('delivery'); ?>
                            </span>
                        </label>
                        <?php endif; ?>

                        <?php if ($pickupAvailable): ?>
                        <label class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 border rounded-lg cursor-pointer transition-all active:bg-seraph-cream/50 shipping-option border-seraph-charcoal/10" data-method="pickup">
                            <input type="radio" name="shipping_method" value="pickup"
                                   class="text-seraph-amber focus:ring-seraph-amber w-4 h-4"
                                   onchange="updateShipping('pickup')"
                                   <?php echo !$shippingSettings['enable_delivery'] ? 'checked' : ''; ?>>
                            <div class="flex-1 min-w-0">
                                <span class="block font-medium text-seraph-charcoal text-sm sm:text-base">
                                    <i class="fas fa-store mr-2 text-seraph-amber"></i>Pickup
                                </span>
                                <span class="text-xs sm:text-sm text-seraph-slate line-clamp-2"><?php echo h($pickupAddress); ?></span>
                            </div>
                            <span class="font-medium text-green-600 text-sm sm:text-base flex-shrink-0">Free</span>
                        </label>
                        <?php endif; ?>
                    </div>

                    <?php if ($promotions['free_shipping_enabled'] && !$qualifiesForFreeShipping): ?>
                    <div class="mt-4 p-3 bg-seraph-amber/10 rounded-lg">
                        <p class="text-xs sm:text-sm text-seraph-charcoal">
                            <i class="fas fa-info-circle text-seraph-amber mr-2"></i>
                            Spend <?php echo $currencyDetails['symbol']; ?><?php echo $currentCurrency === 'NGN' ? number_format($freeShippingThreshold) : number_format($freeShippingThreshold, 2); ?> for free delivery!
                            <span class="text-seraph-slate">(<?php echo $currencyDetails['symbol']; ?><?php echo $currentCurrency === 'NGN' ? number_format($freeShippingThreshold - $cartTotal) : number_format($freeShippingThreshold - $cartTotal, 2); ?> away)</span>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Shipping Address (only show if delivery selected) -->
                <div id="addressSection" class="bg-white border border-seraph-charcoal/5 p-4 sm:p-6 lg:p-8 <?php echo !$shippingSettings['enable_delivery'] ? 'hidden' : ''; ?>">
                    <div class="flex justify-between items-center mb-4 sm:mb-6">
                        <h2 class="font-display text-lg sm:text-xl text-seraph-charcoal">Shipping Address</h2>
                        <a href="<?php echo BASE_URL; ?>account/addresses" class="text-xs sm:text-sm text-seraph-amber hover:text-seraph-amber-dark font-medium">
                            Manage
                        </a>
                    </div>

                    <?php if (empty($addresses)): ?>
                        <div class="text-center py-6 bg-seraph-cream/30 border border-dashed border-seraph-charcoal/10 rounded">
                            <p class="text-seraph-slate text-sm mb-4">No address found.</p>
                            <a href="<?php echo BASE_URL; ?>account/addresses" class="btn-outline px-4 py-2 text-sm font-medium">
                                + Add Address
                            </a>
                        </div>
                        <input type="hidden" id="selectedAddressId" value="">
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($addresses as $addr): ?>
                                <label class="flex items-start gap-3 sm:gap-4 p-3 sm:p-4 border rounded-lg cursor-pointer transition-all active:bg-seraph-cream/50 address-card <?php echo !empty($addr['is_default']) ? 'border-seraph-amber bg-seraph-cream/50' : 'border-seraph-charcoal/10'; ?>">
                                    <input type="radio"
                                           name="shipping_address"
                                           value="<?php echo $addr['id']; ?>"
                                           class="mt-0.5 text-seraph-amber focus:ring-seraph-amber w-4 h-4"
                                           onchange="selectAddress(this)"
                                           <?php echo !empty($addr['is_default']) ? 'checked' : ''; ?>>
                                    <span class="text-xs sm:text-sm font-body text-seraph-slate">
                                        <span class="block font-medium text-seraph-charcoal mb-1">
                                            <?php echo h($addr['first_name'] . ' ' . $addr['last_name']); ?>
                                        </span>
                                        <?php echo h($addr['address_line1']); ?><br>
                                        <?php echo h($addr['city']); ?>, <?php echo h($addr['country']); ?><br>
                                        <?php echo h($addr['phone']); ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selectedAddressId" value="<?php echo $addresses[0]['id'] ?? ''; ?>">
                    <?php endif; ?>
                </div>

                <!-- Promo Code -->
                <?php if (!empty($promotions['promo_code'])): ?>
                <div class="bg-white border border-seraph-charcoal/5 p-4 sm:p-6 lg:p-8">
                    <h2 class="font-display text-base sm:text-lg text-seraph-charcoal mb-4">Promo Code</h2>
                    <div class="flex gap-2 sm:gap-3">
                        <input type="text" id="promoCodeInput" placeholder="Enter code"
                               class="flex-1 border border-seraph-charcoal/20 rounded-lg px-3 sm:px-4 py-2.5 sm:py-3 font-body text-sm focus:border-seraph-amber focus:ring-1 focus:ring-seraph-amber outline-none">
                        <button type="button" onclick="applyPromo()" class="btn-outline px-4 sm:px-6 py-2.5 sm:py-3 text-sm font-medium">
                            Apply
                        </button>
                    </div>
                    <p id="promoMessage" class="mt-2 text-sm hidden"></p>
                </div>
                <?php endif; ?>

                <a href="<?php echo BASE_URL; ?>cart" class="inline-flex items-center gap-2 font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Cart
                </a>
            </div>

            <!-- Order Summary & Payment - Desktop -->
            <div class="lg:col-span-2 hidden lg:block">
                <div class="bg-white border border-seraph-charcoal/5 p-6 sm:p-8 sticky top-28">
                    <h2 class="font-display text-xl text-seraph-charcoal mb-6">Order Summary</h2>

                    <!-- Items -->
                    <div class="space-y-3 mb-6 max-h-48 overflow-y-auto">
                        <?php foreach ($cart as $item): ?>
                            <div class="flex gap-3 text-sm">
                                <div class="w-12 h-12 flex-shrink-0 bg-seraph-cream rounded overflow-hidden">
                                    <?php if (!empty($item['image'])): ?>
                                    <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo htmlspecialchars($item['image']); ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-seraph-charcoal truncate"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-seraph-slate">Qty: <?php echo $item['quantity']; ?></p>
                                </div>
                                <p class="font-medium text-seraph-charcoal">
                                    <?php echo formatPrice($item['prices'] ?? $item['price']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Totals -->
                    <div class="border-t border-seraph-charcoal/10 pt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-seraph-slate">Subtotal</span>
                            <span class="text-seraph-charcoal"><?php echo getFormattedCartTotal(); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-seraph-slate">Shipping</span>
                            <span class="text-seraph-charcoal" id="shippingDisplayDesktop"><?php echo getFormattedShippingCost($defaultShipping); ?></span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-seraph-charcoal/10">
                            <span class="font-display text-lg text-seraph-charcoal">Total</span>
                            <span class="font-display text-lg text-seraph-charcoal" id="totalDisplayDesktop">
                                <?php echo getFormattedOrderTotal($defaultShipping); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Paying As -->
                    <div class="mt-6 p-4 bg-seraph-cream/50 rounded-lg">
                        <p class="font-body text-xs text-seraph-slate mb-1">Paying as:</p>
                        <p class="font-body font-medium text-seraph-charcoal text-sm"><?php echo htmlspecialchars($user['name']); ?></p>
                        <p class="font-body text-xs text-seraph-slate"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <div id="paymentMessageDesktop" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="font-body text-sm text-red-700"></p>
                    </div>

                    <button id="payBtnDesktop" onclick="makePayment()" class="btn-primary w-full py-4 font-medium tracking-wide mt-6">
                        Pay <span class="pay-amount"><?php echo $currencyDetails['symbol']; ?><?php echo $currentCurrency === 'NGN' ? number_format($orderTotal) : number_format($orderTotal, 2); ?></span>
                    </button>

                    <p class="mt-4 font-body text-xs text-seraph-mist text-center">
                        Secured by Flutterwave. We never store your card details.
                    </p>
                </div>
            </div>

            <!-- Order Summary - Mobile (Collapsed) -->
            <div class="lg:hidden">
                <div class="bg-white border border-seraph-charcoal/5 p-4">
                    <button type="button" onclick="toggleOrderSummary()" class="w-full flex items-center justify-between">
                        <h2 class="font-display text-lg text-seraph-charcoal">Order Summary</h2>
                        <div class="flex items-center gap-2">
                            <span class="font-display text-lg text-seraph-charcoal" id="totalDisplayMobileHeader">
                                <?php echo getFormattedOrderTotal($defaultShipping); ?>
                            </span>
                            <svg id="summaryChevron" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-seraph-slate transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Collapsible Content -->
                    <div id="orderSummaryContent" class="hidden mt-4 pt-4 border-t border-seraph-charcoal/10">
                        <!-- Items -->
                        <div class="space-y-3 mb-4">
                            <?php foreach ($cart as $item): ?>
                                <div class="flex gap-3 text-sm">
                                    <div class="w-10 h-10 flex-shrink-0 bg-seraph-cream rounded overflow-hidden">
                                        <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo htmlspecialchars($item['image']); ?>"
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="w-full h-full object-cover">
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-seraph-charcoal truncate text-sm"><?php echo htmlspecialchars($item['name']); ?></p>
                                        <p class="text-seraph-slate text-xs">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <p class="font-medium text-seraph-charcoal text-sm">
                                        <?php echo formatPrice($item['prices'] ?? $item['price']); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Totals -->
                        <div class="border-t border-seraph-charcoal/10 pt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-seraph-slate">Subtotal</span>
                                <span class="text-seraph-charcoal"><?php echo getFormattedCartTotal(); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-seraph-slate">Shipping</span>
                                <span class="text-seraph-charcoal" id="shippingDisplayMobile"><?php echo getFormattedShippingCost($defaultShipping); ?></span>
                            </div>
                        </div>

                        <!-- Paying As -->
                        <div class="mt-4 p-3 bg-seraph-cream/50 rounded-lg">
                            <p class="font-body text-xs text-seraph-slate">Paying as: <span class="font-medium text-seraph-charcoal"><?php echo htmlspecialchars($user['name']); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Mobile Fixed Pay Button -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-seraph-charcoal/10 p-4 z-50 shadow-lg">
    <div id="paymentMessageMobile" class="hidden mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="font-body text-sm text-red-700"></p>
    </div>
    <div class="flex items-center justify-between mb-3">
        <div>
            <span class="font-body text-seraph-slate text-xs">Total</span>
            <span class="font-display text-lg text-seraph-charcoal ml-2" id="totalDisplayMobile">
                <?php echo getFormattedOrderTotal($defaultShipping); ?>
            </span>
        </div>
        <span class="text-xs text-seraph-mist">Secured by Flutterwave</span>
    </div>
    <button id="payBtnMobile" onclick="makePayment()" class="btn-primary w-full py-4 font-medium tracking-wide">
        Pay <span class="pay-amount"><?php echo $currencyDetails['symbol']; ?><?php echo $currentCurrency === 'NGN' ? number_format($orderTotal) : number_format($orderTotal, 2); ?></span>
    </button>
</div>

<script>
    const baseUrl = '<?php echo BASE_URL; ?>';
    const currency = '<?php echo $currentCurrency; ?>';
    const currencySymbol = '<?php echo $currencyDetails['symbol']; ?>';

    // Shipping costs
    const shippingCosts = {
        delivery: <?php echo $shippingCost; ?>,
        pickup: 0
    };
    const cartSubtotal = <?php echo $cartTotal; ?>;
    let currentShippingMethod = '<?php echo $defaultShipping; ?>';

    // Toggle order summary on mobile
    function toggleOrderSummary() {
        const content = document.getElementById('orderSummaryContent');
        const chevron = document.getElementById('summaryChevron');
        content.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }

    // Format price
    function formatCurrency(amount) {
        if (currency === 'NGN') {
            return currencySymbol + amount.toLocaleString('en-NG', {maximumFractionDigits: 0});
        }
        return currencySymbol + amount.toFixed(2);
    }

    // Update all displays
    function updateAllDisplays(shipping, total) {
        const shippingText = shipping === 0 ? 'Free' : formatCurrency(shipping);
        const totalText = formatCurrency(total);

        // Desktop
        const shippingDesktop = document.getElementById('shippingDisplayDesktop');
        const totalDesktop = document.getElementById('totalDisplayDesktop');
        if (shippingDesktop) shippingDesktop.textContent = shippingText;
        if (totalDesktop) totalDesktop.textContent = totalText;

        // Mobile
        const shippingMobile = document.getElementById('shippingDisplayMobile');
        const totalMobile = document.getElementById('totalDisplayMobile');
        const totalMobileHeader = document.getElementById('totalDisplayMobileHeader');
        if (shippingMobile) shippingMobile.textContent = shippingText;
        if (totalMobile) totalMobile.textContent = totalText;
        if (totalMobileHeader) totalMobileHeader.textContent = totalText;

        // Pay buttons
        document.querySelectorAll('.pay-amount').forEach(el => {
            el.textContent = totalText;
        });
    }

    // Update shipping method
    function updateShipping(method) {
        currentShippingMethod = method;

        // Update styles
        document.querySelectorAll('.shipping-option').forEach(opt => {
            opt.classList.remove('border-seraph-amber', 'bg-seraph-cream/50');
            opt.classList.add('border-seraph-charcoal/10');
        });
        const selected = document.querySelector(`.shipping-option[data-method="${method}"]`);
        if (selected) {
            selected.classList.remove('border-seraph-charcoal/10');
            selected.classList.add('border-seraph-amber', 'bg-seraph-cream/50');
        }

        // Show/hide address section
        const addressSection = document.getElementById('addressSection');
        if (method === 'pickup') {
            addressSection.classList.add('hidden');
        } else {
            addressSection.classList.remove('hidden');
        }

        // Update totals
        const shipping = shippingCosts[method];
        const total = cartSubtotal + shipping;
        updateAllDisplays(shipping, total);
    }

    // Select address
    function selectAddress(radio) {
        document.getElementById('selectedAddressId').value = radio.value;

        document.querySelectorAll('.address-card').forEach(card => {
            card.classList.remove('border-seraph-amber', 'bg-seraph-cream/50');
            card.classList.add('border-seraph-charcoal/10');
        });
        radio.closest('label').classList.remove('border-seraph-charcoal/10');
        radio.closest('label').classList.add('border-seraph-amber', 'bg-seraph-cream/50');
    }

    // Apply promo code
    async function applyPromo() {
        const code = document.getElementById('promoCodeInput').value.trim();
        const msgEl = document.getElementById('promoMessage');

        if (!code) return;

        try {
            const res = await fetch(baseUrl + 'api/promo/apply', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({code})
            });
            const data = await res.json();

            msgEl.classList.remove('hidden', 'text-green-600', 'text-red-600');
            msgEl.classList.add(data.success ? 'text-green-600' : 'text-red-600');
            msgEl.textContent = data.message;

            if (data.success) {
                setTimeout(() => location.reload(), 1000);
            }
        } catch (e) {
            msgEl.classList.remove('hidden');
            msgEl.classList.add('text-red-600');
            msgEl.textContent = 'Error applying code';
        }
    }

    // Show error message
    function showError(message) {
        // Desktop
        const msgDesktop = document.querySelector('#paymentMessageDesktop p');
        const msgDivDesktop = document.getElementById('paymentMessageDesktop');
        if (msgDesktop && msgDivDesktop) {
            if (message) {
                msgDesktop.innerText = message;
                msgDivDesktop.classList.remove('hidden');
            } else {
                msgDivDesktop.classList.add('hidden');
            }
        }

        // Mobile
        const msgMobile = document.querySelector('#paymentMessageMobile p');
        const msgDivMobile = document.getElementById('paymentMessageMobile');
        if (msgMobile && msgDivMobile) {
            if (message) {
                msgMobile.innerText = message;
                msgDivMobile.classList.remove('hidden');
            } else {
                msgDivMobile.classList.add('hidden');
            }
        }
    }

    // Hide error messages
    function hideErrors() {
        document.getElementById('paymentMessageDesktop')?.classList.add('hidden');
        document.getElementById('paymentMessageMobile')?.classList.add('hidden');
    }

    // Set button states
    function setButtonState(loading) {
        const btnDesktop = document.getElementById('payBtnDesktop');
        const btnMobile = document.getElementById('payBtnMobile');
        const total = cartSubtotal + shippingCosts[currentShippingMethod];
        const totalText = formatCurrency(total);

        if (loading) {
            if (btnDesktop) {
                btnDesktop.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...</span>';
                btnDesktop.disabled = true;
            }
            if (btnMobile) {
                btnMobile.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...</span>';
                btnMobile.disabled = true;
            }
        } else {
            if (btnDesktop) {
                btnDesktop.innerHTML = 'Pay <span class="pay-amount">' + totalText + '</span>';
                btnDesktop.disabled = false;
            }
            if (btnMobile) {
                btnMobile.innerHTML = 'Pay <span class="pay-amount">' + totalText + '</span>';
                btnMobile.disabled = false;
            }
        }
    }

    // Make payment
    function makePayment() {
        const addressId = document.getElementById('selectedAddressId')?.value || '';

        // Validate address for delivery
        if (currentShippingMethod === 'delivery' && !addressId) {
            showError('Please select a shipping address');
            const addressSection = document.getElementById('addressSection');
            if (addressSection) {
                addressSection.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
            return;
        }

        hideErrors();
        setButtonState(true);

        const total = cartSubtotal + shippingCosts[currentShippingMethod];

        const txRef = "SERAPH_" + Date.now();

        // Store payment info in sessionStorage for recovery
        sessionStorage.setItem('pending_payment', JSON.stringify({
            tx_ref: txRef,
            address_id: addressId,
            shipping_method: currentShippingMethod
        }));

        FlutterwaveCheckout({
            public_key: "<?php echo FLUTTERWAVE_PUBLIC_KEY; ?>",
            tx_ref: txRef,
            amount: total,
            currency: currency,
            payment_options: "card, banktransfer, ussd",
            customer: {
                email: "<?php echo htmlspecialchars($user['email']); ?>",
                name: "<?php echo htmlspecialchars($user['name']); ?>",
            },
            meta: {
                address_id: addressId,
                shipping_method: currentShippingMethod
            },
            customizations: {
                title: "Seraph",
                description: "Payment for Seraph products",
                logo: "<?php echo BASE_URL; ?>public/assets/images/favicon.svg",
            },
            callback: async function(response) {
                console.log('Flutterwave callback:', response);

                if (response.status === "successful" || response.status === "completed") {
                    try {
                        setButtonState(true);
                        showError(''); // Clear any errors

                        const orderResponse = await fetch(baseUrl + 'api/orders/create', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                payment_ref: response.transaction_id || response.tx_ref,
                                payment_status: 'paid',
                                address_id: addressId,
                                shipping_method: currentShippingMethod
                            })
                        });

                        const orderData = await orderResponse.json();
                        console.log('Order response:', orderData);

                        if (orderData.success) {
                            sessionStorage.removeItem('pending_payment');
                            window.location.href = baseUrl + 'orders/' + orderData.order.id + '?success=1';
                        } else {
                            showError('Order creation failed: ' + (orderData.message || 'Unknown error'));
                            setButtonState(false);
                        }
                    } catch (error) {
                        console.error('Order creation error:', error);
                        showError('Network error. Please contact support. Ref: ' + txRef);
                        setButtonState(false);
                    }
                } else {
                    console.log('Payment not successful:', response.status);
                    setButtonState(false);
                }
            },
            onclose: function() {
                console.log('Flutterwave modal closed');
                // Check if payment was completed before modal closed
                setTimeout(() => {
                    setButtonState(false);
                }, 500);
            }
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Select default address
        const radios = document.getElementsByName('shipping_address');
        if (radios.length > 0) {
            let checked = false;
            for (let r of radios) {
                if (r.checked) {
                    document.getElementById('selectedAddressId').value = r.value;
                    checked = true;
                }
            }
            if (!checked) {
                radios[0].checked = true;
                document.getElementById('selectedAddressId').value = radios[0].value;
            }
        }
    });
</script>

<?php
$settings = getSettings();
$shipping = $settings['shipping'] ?? [];
$promotions = $settings['promotions'] ?? [];
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Invalid security token';
    } else {
        $newSettings = [
            'site_name' => trim($_POST['site_name'] ?? ''),
            'site_email' => trim($_POST['site_email'] ?? ''),
            'order_prefix' => trim($_POST['order_prefix'] ?? 'ORD-'),
            'low_stock_threshold' => (int)($_POST['low_stock_threshold'] ?? 10),
            'enable_notifications' => isset($_POST['enable_notifications']),
            'maintenance_mode' => isset($_POST['maintenance_mode']),
            'shipping' => [
                'enable_delivery' => isset($_POST['enable_delivery']),
                'pickup' => [
                    'NGN' => [
                        'enabled' => isset($_POST['pickup_ngn_enabled']),
                        'address' => trim($_POST['pickup_ngn_address'] ?? '')
                    ],
                    'GBP' => [
                        'enabled' => isset($_POST['pickup_gbp_enabled']),
                        'address' => trim($_POST['pickup_gbp_address'] ?? '')
                    ],
                    'USD' => [
                        'enabled' => false,
                        'address' => ''
                    ]
                ],
                'costs' => [
                    'NGN' => (float)($_POST['shipping_ngn'] ?? 0),
                    'GBP' => (float)($_POST['shipping_gbp'] ?? 0),
                    'USD' => (float)($_POST['shipping_usd'] ?? 0)
                ]
            ],
            'promotions' => [
                'free_shipping_enabled' => isset($_POST['free_shipping_enabled']),
                'free_shipping_threshold' => [
                    'NGN' => (float)($_POST['free_threshold_ngn'] ?? 0),
                    'GBP' => (float)($_POST['free_threshold_gbp'] ?? 0),
                    'USD' => (float)($_POST['free_threshold_usd'] ?? 0)
                ],
                'promo_code' => strtoupper(trim($_POST['promo_code'] ?? '')),
                'promo_discount_percent' => (float)($_POST['promo_discount_percent'] ?? 0),
                'promo_free_shipping' => isset($_POST['promo_free_shipping'])
            ]
        ];

        $result = updateSettings($newSettings);
        if ($result['success']) {
            $successMessage = 'Settings saved successfully';
            $settings = getSettings();
            $shipping = $settings['shipping'] ?? [];
            $promotions = $settings['promotions'] ?? [];
        } else {
            $errorMessage = $result['message'];
        }
    }
}
?>

<div class="max-w-4xl space-y-6">
    <?php if ($successMessage): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i><?php echo h($successMessage); ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo h($errorMessage); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php echo csrfField(); ?>
        <input type="hidden" name="save_settings" value="1">

        <!-- General Settings -->
        <div class="card bg-white rounded-xl mb-6">
            <div class="px-6 py-4 border-b border-admin-border">
                <h3 class="font-semibold text-admin-text"><i class="fas fa-cog mr-2 text-admin-accent"></i>General Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-admin-text mb-2">Site Name</label>
                        <input type="text" name="site_name" value="<?php echo h($settings['site_name'] ?? ''); ?>"
                               class="w-full border border-admin-border rounded-lg px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-admin-text mb-2">Admin Email</label>
                        <input type="email" name="site_email" value="<?php echo h($settings['site_email'] ?? ''); ?>"
                               class="w-full border border-admin-border rounded-lg px-4 py-3">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-admin-text mb-2">Order Number Prefix</label>
                        <input type="text" name="order_prefix" value="<?php echo h($settings['order_prefix'] ?? 'ORD-'); ?>"
                               class="w-full border border-admin-border rounded-lg px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-admin-text mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" min="0"
                               value="<?php echo $settings['low_stock_threshold'] ?? 10; ?>"
                               class="w-full border border-admin-border rounded-lg px-4 py-3">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="enable_notifications"
                               <?php echo ($settings['enable_notifications'] ?? true) ? 'checked' : ''; ?>
                               class="w-4 h-4 text-admin-accent rounded border-admin-border focus:ring-admin-accent">
                        <span class="text-sm text-admin-text">Email notifications</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="maintenance_mode"
                               <?php echo ($settings['maintenance_mode'] ?? false) ? 'checked' : ''; ?>
                               class="w-4 h-4 text-red-500 rounded border-admin-border focus:ring-red-500">
                        <span class="text-sm text-admin-text">Maintenance mode</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Shipping Settings -->
        <div class="card bg-white rounded-xl mb-6">
            <div class="px-6 py-4 border-b border-admin-border">
                <h3 class="font-semibold text-admin-text"><i class="fas fa-truck mr-2 text-admin-accent"></i>Shipping Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Enable Delivery -->
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="enable_delivery"
                           <?php echo ($shipping['enable_delivery'] ?? true) ? 'checked' : ''; ?>
                           class="w-4 h-4 text-admin-accent rounded border-admin-border focus:ring-admin-accent">
                    <span class="text-sm font-medium text-admin-text">Enable Delivery</span>
                </label>

                <!-- Shipping & Pickup by Region -->
                <div class="grid grid-cols-1 gap-6">
                    <!-- Nigeria -->
                    <div class="bg-admin-bg rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xl">🇳🇬</span>
                            <span class="font-medium text-admin-text">Nigeria (NGN)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-admin-muted mb-2">Delivery Cost</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">₦</span>
                                    <input type="number" name="shipping_ngn" step="1" min="0"
                                           value="<?php echo $shipping['costs']['NGN'] ?? 0; ?>"
                                           class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-2">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-admin-muted mb-2">Pickup</label>
                                <label class="flex items-center gap-2 cursor-pointer mb-2">
                                    <input type="checkbox" name="pickup_ngn_enabled"
                                           <?php echo ($shipping['pickup']['NGN']['enabled'] ?? false) ? 'checked' : ''; ?>
                                           class="w-4 h-4 text-admin-accent rounded border-admin-border">
                                    <span class="text-sm text-admin-text">Enable Pickup</span>
                                </label>
                                <input type="text" name="pickup_ngn_address"
                                       value="<?php echo h($shipping['pickup']['NGN']['address'] ?? ''); ?>"
                                       class="w-full border border-admin-border rounded-lg px-3 py-2 text-sm"
                                       placeholder="Lagos pickup address">
                            </div>
                        </div>
                    </div>

                    <!-- UK & Europe -->
                    <div class="bg-admin-bg rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xl">🇬🇧</span>
                            <span class="font-medium text-admin-text">UK & Europe (GBP)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-admin-muted mb-2">Delivery Cost</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">£</span>
                                    <input type="number" name="shipping_gbp" step="0.01" min="0"
                                           value="<?php echo $shipping['costs']['GBP'] ?? 0; ?>"
                                           class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-2">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-admin-muted mb-2">Pickup</label>
                                <label class="flex items-center gap-2 cursor-pointer mb-2">
                                    <input type="checkbox" name="pickup_gbp_enabled"
                                           <?php echo ($shipping['pickup']['GBP']['enabled'] ?? false) ? 'checked' : ''; ?>
                                           class="w-4 h-4 text-admin-accent rounded border-admin-border">
                                    <span class="text-sm text-admin-text">Enable Pickup</span>
                                </label>
                                <input type="text" name="pickup_gbp_address"
                                       value="<?php echo h($shipping['pickup']['GBP']['address'] ?? ''); ?>"
                                       class="w-full border border-admin-border rounded-lg px-3 py-2 text-sm"
                                       placeholder="UK pickup address">
                            </div>
                        </div>
                    </div>

                    <!-- International -->
                    <div class="bg-admin-bg rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xl">🌍</span>
                            <span class="font-medium text-admin-text">International (USD)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-admin-muted mb-2">Delivery Cost</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">$</span>
                                    <input type="number" name="shipping_usd" step="0.01" min="0"
                                           value="<?php echo $shipping['costs']['USD'] ?? 0; ?>"
                                           class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-2">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-admin-muted mb-2">Pickup</label>
                                <div class="text-sm text-admin-muted italic py-2">
                                    Pickup not available for international orders
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promotions -->
        <div class="card bg-white rounded-xl mb-6">
            <div class="px-6 py-4 border-b border-admin-border">
                <h3 class="font-semibold text-admin-text"><i class="fas fa-tags mr-2 text-admin-accent"></i>Promotions</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Free Shipping Threshold -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="free_shipping_enabled" id="freeShippingToggle"
                                   <?php echo ($promotions['free_shipping_enabled'] ?? false) ? 'checked' : ''; ?>
                                   class="w-4 h-4 text-admin-accent rounded border-admin-border focus:ring-admin-accent"
                                   onchange="toggleFreeShipping()">
                            <span class="text-sm font-medium text-admin-text">Enable Free Shipping Threshold</span>
                        </label>
                    </div>

                    <div id="freeShippingThresholds" class="<?php echo ($promotions['free_shipping_enabled'] ?? false) ? '' : 'opacity-50 pointer-events-none'; ?>">
                        <label class="block text-xs font-medium text-admin-muted mb-2">Free shipping when order exceeds:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">₦</span>
                                <input type="number" name="free_threshold_ngn" step="1" min="0"
                                       value="<?php echo $promotions['free_shipping_threshold']['NGN'] ?? 0; ?>"
                                       class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-2">
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">£</span>
                                <input type="number" name="free_threshold_gbp" step="0.01" min="0"
                                       value="<?php echo $promotions['free_shipping_threshold']['GBP'] ?? 0; ?>"
                                       class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-2">
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">$</span>
                                <input type="number" name="free_threshold_usd" step="0.01" min="0"
                                       value="<?php echo $promotions['free_shipping_threshold']['USD'] ?? 0; ?>"
                                       class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-2">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-admin-border">

                <!-- Promo Code -->
                <div>
                    <label class="block text-sm font-medium text-admin-text mb-3">Promo Code</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-admin-muted mb-2">Code</label>
                            <input type="text" name="promo_code"
                                   value="<?php echo h($promotions['promo_code'] ?? ''); ?>"
                                   class="w-full border border-admin-border rounded-lg px-4 py-2 uppercase"
                                   placeholder="e.g., SAVE10">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-admin-muted mb-2">Discount %</label>
                            <input type="number" name="promo_discount_percent" min="0" max="100" step="1"
                                   value="<?php echo $promotions['promo_discount_percent'] ?? 0; ?>"
                                   class="w-full border border-admin-border rounded-lg px-4 py-2">
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="promo_free_shipping"
                                       <?php echo ($promotions['promo_free_shipping'] ?? false) ? 'checked' : ''; ?>
                                       class="w-4 h-4 text-admin-accent rounded border-admin-border focus:ring-admin-accent">
                                <span class="text-sm text-admin-text">+ Free Shipping</span>
                            </label>
                        </div>
                    </div>
                    <p class="text-xs text-admin-muted mt-2">Leave code empty to disable promo code feature</p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="btn-primary px-8 py-3 rounded-lg">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>

    <!-- System Info -->
    <div class="card bg-white rounded-xl">
        <div class="px-6 py-4 border-b border-admin-border">
            <h3 class="font-semibold text-admin-text"><i class="fas fa-info-circle mr-2 text-admin-accent"></i>System Information</h3>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between py-2 border-b border-admin-border">
                    <span class="text-admin-muted">Application</span>
                    <span class="font-medium text-admin-text"><?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-admin-border">
                    <span class="text-admin-muted">Environment</span>
                    <span class="font-medium text-admin-text"><?php echo APP_ENV; ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-admin-border">
                    <span class="text-admin-muted">PHP Version</span>
                    <span class="font-medium text-admin-text"><?php echo phpversion(); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-admin-border">
                    <span class="text-admin-muted">Payment Gateway</span>
                    <span class="font-medium text-admin-text">Flutterwave</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFreeShipping() {
    const checkbox = document.getElementById('freeShippingToggle');
    const thresholds = document.getElementById('freeShippingThresholds');

    if (checkbox.checked) {
        thresholds.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        thresholds.classList.add('opacity-50', 'pointer-events-none');
    }
}
</script>

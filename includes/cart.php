<?php
/**
 * Shopping Cart Functions
 * Session-based cart with order management
 */

/**
 * Initialize cart in session
 */
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Add item to cart
 */
function addToCart($productId, $quantity = 1) {
    global $db;
    initCart();
    
    // Get product
    $products = $db->find('products', ['id' => (int)$productId]);
    $product = !empty($products) ? reset($products) : null;
    
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found'];
    }
    
    // Check if already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $productId) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => (int)$productId,
            'name' => $product['name'],
            'prices' => $product['prices'] ?? ['NGN' => $product['price'], 'GBP' => 0, 'USD' => 0],
            'price' => $product['price'] ?? 0, // Legacy support
            'image' => $product['image'],
            'quantity' => $quantity
        ];
    }
    
    return ['success' => true, 'message' => 'Added to cart', 'cartCount' => getCartCount()];
}

/**
 * Update cart item quantity
 */
function updateCartItem($productId, $quantity) {
    initCart();
    
    if ($quantity <= 0) {
        return removeFromCart($productId);
    }
    
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $productId) {
            $item['quantity'] = $quantity;
            return ['success' => true, 'message' => 'Cart updated', 'cartCount' => getCartCount()];
        }
    }
    
    return ['success' => false, 'message' => 'Item not found in cart'];
}

/**
 * Remove item from cart
 */
function removeFromCart($productId) {
    initCart();
    
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productId) {
        return $item['product_id'] != $productId;
    });
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    
    return ['success' => true, 'message' => 'Item removed', 'cartCount' => getCartCount()];
}

/**
 * Get cart contents (with refreshed data from database)
 */
function getCart() {
    global $db;
    initCart();

    // Always refresh product data from database to ensure current info
    $updatedCart = [];
    foreach ($_SESSION['cart'] as $item) {
        $product = $db->findOne('products', ['id' => (int)$item['product_id']]);
        if ($product) {
            // Refresh all product data from database
            $item['image'] = $product['image'] ?? '';
            $item['name'] = $product['name'] ?? $item['name'];

            if (isset($product['prices']) && is_array($product['prices'])) {
                $item['prices'] = $product['prices'];
            } else {
                $item['prices'] = [
                    'NGN' => $product['price'] ?? 0,
                    'GBP' => 0,
                    'USD' => 0
                ];
            }
        }
        $updatedCart[] = $item;
    }

    // Update session with refreshed data
    $_SESSION['cart'] = $updatedCart;

    return $_SESSION['cart'];
}

/**
 * Get cart count
 */
function getCartCount() {
    initCart();
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

/**
 * Get cart total in current currency
 */
function getCartTotal() {
    $cart = getCart(); // Get refreshed cart with prices
    $total = 0;
    $currency = function_exists('getCurrentCurrency') ? getCurrentCurrency() : 'NGN';

    foreach ($cart as $item) {
        // Use multi-currency prices if available
        if (isset($item['prices']) && is_array($item['prices'])) {
            $price = $item['prices'][$currency] ?? $item['prices']['NGN'] ?? $item['price'] ?? 0;
        } else {
            // Fallback: assume NGN if no prices array
            $price = ($currency === 'NGN') ? ($item['price'] ?? 0) : 0;
        }
        $total += $price * $item['quantity'];
    }
    return $total;
}

/**
 * Get cart total in a specific currency (used for payment processing)
 */
function getCartTotalInCurrency($currency = 'NGN') {
    $cart = getCart();
    $total = 0;

    foreach ($cart as $item) {
        if (isset($item['prices']) && is_array($item['prices'])) {
            $price = $item['prices'][$currency] ?? $item['prices']['NGN'] ?? 0;
        } else {
            $price = ($currency === 'NGN') ? ($item['price'] ?? 0) : 0;
        }
        $total += $price * $item['quantity'];
    }
    return $total;
}

/**
 * Get formatted cart total
 */
function getFormattedCartTotal() {
    $total = getCartTotal();
    $currency = function_exists('getCurrentCurrency') ? getCurrentCurrency() : 'NGN';
    $details = function_exists('getCurrencyDetails') ? getCurrencyDetails($currency) : ['symbol' => '₦'];

    if ($currency === 'NGN') {
        return $details['symbol'] . number_format($total);
    }
    return $details['symbol'] . number_format($total, 2);
}

/**
 * Get shipping settings
 */
function getShippingSettings() {
    global $db;
    // Settings is a single object, not an array, so use read() directly
    $settings = $db->read('settings');
    return $settings['shipping'] ?? [
        'enable_delivery' => true,
        'enable_pickup' => true,
        'pickup_address' => '',
        'costs' => ['NGN' => 0, 'GBP' => 0, 'USD' => 0]
    ];
}

/**
 * Get promotion settings
 */
function getPromotionSettings() {
    global $db;
    // Settings is a single object, not an array, so use read() directly
    $settings = $db->read('settings');
    return $settings['promotions'] ?? [
        'free_shipping_enabled' => false,
        'free_shipping_threshold' => ['NGN' => 0, 'GBP' => 0, 'USD' => 0],
        'promo_code' => '',
        'promo_discount_percent' => 0,
        'promo_free_shipping' => false
    ];
}

/**
 * Get shipping cost for current currency
 */
function getShippingCost($method = 'delivery') {
    if ($method === 'pickup') {
        return 0;
    }

    $currency = function_exists('getCurrentCurrency') ? getCurrentCurrency() : 'NGN';
    $shipping = getShippingSettings();
    $promotions = getPromotionSettings();
    $cartTotal = getCartTotal();

    // Check for free shipping threshold
    if ($promotions['free_shipping_enabled']) {
        $threshold = $promotions['free_shipping_threshold'][$currency] ?? 0;
        if ($cartTotal >= $threshold) {
            return 0;
        }
    }

    // Check session for promo code free shipping
    if (!empty($_SESSION['promo_free_shipping'])) {
        return 0;
    }

    return $shipping['costs'][$currency] ?? 0;
}

/**
 * Get formatted shipping cost
 */
function getFormattedShippingCost($method = 'delivery') {
    $cost = getShippingCost($method);
    $currency = function_exists('getCurrentCurrency') ? getCurrentCurrency() : 'NGN';
    $details = function_exists('getCurrencyDetails') ? getCurrencyDetails($currency) : ['symbol' => '₦'];

    if ($cost == 0) {
        return 'Free';
    }

    if ($currency === 'NGN') {
        return $details['symbol'] . number_format($cost);
    }
    return $details['symbol'] . number_format($cost, 2);
}

/**
 * Get order total (cart + shipping)
 */
function getOrderTotal($shippingMethod = 'delivery') {
    return getCartTotal() + getShippingCost($shippingMethod);
}

/**
 * Get formatted order total
 */
function getFormattedOrderTotal($shippingMethod = 'delivery') {
    $total = getOrderTotal($shippingMethod);
    $currency = function_exists('getCurrentCurrency') ? getCurrentCurrency() : 'NGN';
    $details = function_exists('getCurrencyDetails') ? getCurrencyDetails($currency) : ['symbol' => '₦'];

    if ($currency === 'NGN') {
        return $details['symbol'] . number_format($total);
    }
    return $details['symbol'] . number_format($total, 2);
}

/**
 * Apply promo code
 */
function applyPromoCode($code) {
    $promotions = getPromotionSettings();

    if (empty($promotions['promo_code']) || strtoupper($code) !== strtoupper($promotions['promo_code'])) {
        return ['success' => false, 'message' => 'Invalid promo code'];
    }

    $_SESSION['promo_code'] = $code;
    $_SESSION['promo_discount'] = $promotions['promo_discount_percent'];
    $_SESSION['promo_free_shipping'] = $promotions['promo_free_shipping'];

    return ['success' => true, 'message' => 'Promo code applied!'];
}

/**
 * Clear promo code
 */
function clearPromoCode() {
    unset($_SESSION['promo_code']);
    unset($_SESSION['promo_discount']);
    unset($_SESSION['promo_free_shipping']);
}

/**
 * Clear cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
    clearPromoCode();
    return ['success' => true, 'message' => 'Cart cleared'];
}

/**
 * Create order from cart
 */
function createOrder($paymentRef, $paymentStatus = 'pending', $addressId = null, $shippingMethod = 'delivery') {
    global $db;

    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to place order'];
    }

    $cart = getCart();
    if (empty($cart)) {
        return ['success' => false, 'message' => 'Cart is empty'];
    }

    $user = getCurrentUser();
    $currency = function_exists('getCurrentCurrency') ? getCurrentCurrency() : 'NGN';

    // Get shipping address (only for delivery)
    $shippingAddress = null;
    $pickupAddress = null;

    if ($shippingMethod === 'pickup') {
        // Get pickup address from settings
        $settings = $db->read('settings');
        $pickupConfig = $settings['shipping']['pickup'][$currency] ?? null;
        if ($pickupConfig && !empty($pickupConfig['address'])) {
            $pickupAddress = $pickupConfig['address'];
        }
    } else {
        // Get delivery address
        if ($addressId) {
            $addresses = getUserAddresses();
            foreach ($addresses as $addr) {
                if ($addr['id'] === $addressId) {
                    $shippingAddress = $addr;
                    break;
                }
            }
        }

        // If no address selected, get default address
        if (!$shippingAddress) {
            $addresses = getUserAddresses();
            foreach ($addresses as $addr) {
                if (!empty($addr['is_default'])) {
                    $shippingAddress = $addr;
                    break;
                }
            }
            // Fallback to first address
            if (!$shippingAddress && !empty($addresses)) {
                $shippingAddress = $addresses[0];
            }
        }
    }

    // Calculate shipping cost
    $shippingCost = ($shippingMethod === 'pickup') ? 0 : getShippingCost('delivery');

    $order = [
        'order_number' => 'ORD-' . strtoupper(substr(uniqid(), -8)),
        'user_id' => $user['id'],
        'user_email' => $user['email'],
        'user_name' => $user['name'],
        'items' => $cart,
        'subtotal' => getCartTotal(),
        'shipping' => $shippingCost,
        'total' => getCartTotal() + $shippingCost,
        'shipping_method' => $shippingMethod,
        'shipping_address' => $shippingAddress,
        'pickup_address' => $pickupAddress,
        'currency' => $currency,
        'payment_ref' => $paymentRef,
        'payment_status' => $paymentStatus,
        'order_status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $savedOrder = $db->insert('orders', $order);

    // Clear cart after order
    clearCart();

    // Send order confirmation email
    sendOrderConfirmation($savedOrder);

    return ['success' => true, 'message' => 'Order placed successfully', 'order' => $savedOrder];
}

/**
 * Get user orders
 */
function getUserOrders() {
    global $db;
    
    if (!isLoggedIn()) {
        return [];
    }
    
    $user = getCurrentUser();
    $orders = $db->find('orders', ['user_id' => $user['id']]);
    
    // Sort by created_at desc
    usort($orders, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    return $orders;
}

/**
 * Get single order
 */
function getOrder($orderId) {
    global $db;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $user = getCurrentUser();
    $order = $db->findOne('orders', ['id' => (int)$orderId]);
    
    // Ensure order belongs to current user
    if ($order && $order['user_id'] == $user['id']) {
        return $order;
    }
    
    return null;
}

/**
 * Send order confirmation email
 */
function sendOrderConfirmation($order) {
    $subject = 'Order Confirmation - ' . $order['order_number'];
    
    $itemsHtml = '';
    foreach ($order['items'] as $item) {
        $itemsHtml .= "<tr>
            <td style='padding: 12px; border-bottom: 1px solid #E5E5E5;'>{$item['name']}</td>
            <td style='padding: 12px; border-bottom: 1px solid #E5E5E5; text-align: center;'>{$item['quantity']}</td>
            <td style='padding: 12px; border-bottom: 1px solid #E5E5E5; text-align: right;'>₦" . number_format($item['price'] * $item['quantity']) . "</td>
        </tr>";
    }
    
    $formattedTotal = number_format($order['total']);
    
    $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; line-height: 1.6; color: #2D2A26; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2D2A26; color: #FAF6F1; padding: 30px; text-align: center; }
        .logo { font-family: 'Playfair Display', Georgia, serif; font-size: 28px; letter-spacing: 0.2em; }
        .content { background: white; padding: 30px; }
        .order-number { color: #C8956C; font-size: 24px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #FAF6F1; padding: 12px; text-align: left; }
        .total { font-size: 20px; font-weight: bold; color: #2D2A26; }
        .footer { text-align: center; padding: 20px; color: #8A8880; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SERAPH</div>
            <p style="margin: 10px 0 0 0; opacity: 0.8;">Order Confirmation</p>
        </div>
        
        <div class="content">
            <p>Hi {$order['user_name']},</p>
            <p>Thank you for your order! We're getting it ready.</p>
            
            <p class="order-number">Order {$order['order_number']}</p>
            <p style="color: #51504A;">Placed on {$order['created_at']}</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
            </table>
            
            <p style="text-align: right;" class="total">Total: ₦{$formattedTotal}</p>
            
            <p style="margin-top: 30px;">We'll send you another email when your order ships.</p>
            
            <p>Best regards,<br>The Seraph Team</p>
        </div>
        
        <div class="footer">
            <p>© 2025 Seraph. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

    $altBody = "Order Confirmation - {$order['order_number']}\n\nThank you for your order!\n\nTotal: ₦" . $formattedTotal . "\n\nWe'll send you another email when your order ships.";

    return sendEmail($order['user_email'], $subject, $body, $altBody);
}

/**
 * Send order status update email
 */
function sendOrderStatusEmail($order, $newStatus, $trackingNumber = null) {
    $statusMessages = [
        'processing' => [
            'title' => 'Your Order is Being Processed',
            'message' => 'Great news! We\'ve started preparing your order.'
        ],
        'shipped' => [
            'title' => 'Your Order Has Been Shipped!',
            'message' => 'Your order is on its way to you.'
        ],
        'delivered' => [
            'title' => 'Your Order Has Been Delivered',
            'message' => 'Your order has been delivered. We hope you love it!'
        ],
        'cancelled' => [
            'title' => 'Your Order Has Been Cancelled',
            'message' => 'Your order has been cancelled. If you have any questions, please contact us.'
        ],
        'ready_for_pickup' => [
            'title' => 'Your Order is Ready for Pickup!',
            'message' => 'Your order is ready and waiting for you at our pickup location.'
        ]
    ];

    $statusInfo = $statusMessages[$newStatus] ?? [
        'title' => 'Order Update',
        'message' => 'Your order status has been updated to: ' . ucfirst($newStatus)
    ];

    $subject = $statusInfo['title'] . ' - ' . $order['order_number'];

    // Tracking info section
    $trackingHtml = '';
    if ($newStatus === 'shipped' && $trackingNumber) {
        $trackingHtml = "
            <div style='background: #F3E8FF; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0; font-weight: bold; color: #7C3AED;'>Tracking Information</p>
                <p style='margin: 0; font-size: 18px; color: #2D2A26;'>{$trackingNumber}</p>
            </div>
        ";
    }

    // Pickup info section
    $pickupHtml = '';
    if ($newStatus === 'ready_for_pickup' && !empty($order['pickup_address'])) {
        $pickupHtml = "
            <div style='background: #FEF3C7; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0; font-weight: bold; color: #D97706;'>Pickup Location</p>
                <p style='margin: 0; color: #2D2A26;'>{$order['pickup_address']}</p>
                <p style='margin: 10px 0 0 0; font-size: 14px; color: #92400E;'>Please bring your order confirmation when collecting.</p>
            </div>
        ";
    }

    $statusColor = match($newStatus) {
        'processing' => '#3B82F6',
        'shipped' => '#8B5CF6',
        'delivered' => '#10B981',
        'cancelled' => '#EF4444',
        'ready_for_pickup' => '#F59E0B',
        default => '#6B7280'
    };

    $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; line-height: 1.6; color: #2D2A26; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2D2A26; color: #FAF6F1; padding: 30px; text-align: center; }
        .logo { font-family: 'Playfair Display', Georgia, serif; font-size: 28px; letter-spacing: 0.2em; }
        .content { background: white; padding: 30px; }
        .status-badge { display: inline-block; background: {$statusColor}; color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .order-number { color: #C8956C; font-size: 20px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; color: #8A8880; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SERAPH</div>
            <p style="margin: 10px 0 0 0; opacity: 0.8;">Order Update</p>
        </div>

        <div class="content">
            <p>Hi {$order['user_name']},</p>

            <span class="status-badge">{$statusInfo['title']}</span>

            <p style="margin-top: 20px;">{$statusInfo['message']}</p>

            <p class="order-number">Order {$order['order_number']}</p>

            {$trackingHtml}
            {$pickupHtml}

            <p style="margin-top: 30px;">If you have any questions, please don't hesitate to contact us.</p>

            <p>Best regards,<br>The Seraph Team</p>
        </div>

        <div class="footer">
            <p>© 2025 Seraph. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

    $altBody = "{$statusInfo['title']} - {$order['order_number']}\n\n{$statusInfo['message']}";
    if ($trackingNumber) {
        $altBody .= "\n\nTracking Number: {$trackingNumber}";
    }

    return sendEmail($order['user_email'], $subject, $body, $altBody);
}

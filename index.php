<?php
/**
 * Main Entry Point
 * All requests are routed through this file
 */

// Load configuration first (contains session settings)
require_once __DIR__ . '/config/config.php';

// Start session (after session ini settings are configured)
session_start();

// Load database helper
require_once __DIR__ . '/config/database.php';

// Load router
require_once __DIR__ . '/router/Router.php';

// Load helper functions
require_once INCLUDES_PATH . 'helpers.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'cart.php';
require_once INCLUDES_PATH . 'user.php';
require_once INCLUDES_PATH . 'currency.php';

// Load components
require_once COMPONENTS_PATH . 'layout.php';
require_once COMPONENTS_PATH . 'admin-layout.php';
require_once COMPONENTS_PATH . 'email.php';
require_once COMPONENTS_PATH . 'countries.php';

// Load admin functions
require_once INCLUDES_PATH . 'admin.php';

// Initialize router
$router = new Router();

// Initialize database
$db = new Database();

// Check remember me cookie for auto-login
checkRememberMe();

// Track site visit
trackVisit();

// Define Routes
// ============================================

// Home Page
$router->get('/', function() {
    renderLayout(PAGES_PATH . 'home.php', [
        'pageTitle' => 'SERAPH - Natural Non-Fluoride Toothpaste'
    ]);
});

// Home Page (Alternative route)
$router->get('/home', function() {
    renderLayout(PAGES_PATH . 'home.php', [
        'pageTitle' => 'SERAPH - Natural Non-Fluoride Toothpaste'
    ]);
});

// Distributors Page
$router->get('/distributors', function() {
    renderLayout(PAGES_PATH . 'distributors.php', [
        'pageTitle' => 'Become a Distributor - SERAPH'
    ]);
});

// Products Page
$router->get('/products', function() {
    global $db;
    $products = $db->find('products');
    
    renderLayout(PAGES_PATH . 'products.php', [
        'pageTitle' => 'Shop Seraph',
        'products' => $products,
        'hideHeaderCart' => true
    ]);
});

// Single Product Page
$router->get('/products/:id', function($id) {
    global $db;
    
    // Cast ID to integer for strict comparison with JSON data
    $product = $db->findOne('products', ['id' => (int)$id]);
    
    if (!$product) {
        header('Location: ' . BASE_URL . 'products');
        exit;
    }
    
    renderLayout(PAGES_PATH . 'product-detail.php', [
        'pageTitle' => $product['name'] . ' - Seraph',
        'product' => $product
    ]);
});

// Cart Page
$router->get('/cart', function() {
    renderLayout(PAGES_PATH . 'cart.php', [
        'pageTitle' => 'Your Cart - SERAPH'
    ]);
});

// Checkout Page
$router->get('/checkout', function() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = BASE_URL . 'checkout';
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
    
    if (getCartCount() == 0) {
        header('Location: ' . BASE_URL . 'products');
        exit;
    }
    
    renderLayout(PAGES_PATH . 'checkout.php', [
        'pageTitle' => 'Checkout - SERAPH'
    ]);
});

// Orders Page
$router->get('/orders', function() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
    
    $orders = getUserOrders();
    
    renderLayout(PAGES_PATH . 'orders.php', [
        'pageTitle' => 'My Orders - SERAPH',
        'orders' => $orders
    ]);
});

// Single Order Page
$router->get('/orders/:id', function($id) {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
    
    $order = getOrder($id);
    
    if (!$order) {
        header('Location: ' . BASE_URL . 'orders');
        exit;
    }
    
    renderLayout(PAGES_PATH . 'order-detail.php', [
        'pageTitle' => 'Order ' . $order['order_number'] . ' - SERAPH',
        'order' => $order
    ]);
});

// Auth Page (Unified login/signup)
$router->get('/login', function() {
    if (isLoggedIn()) {
        header('Location: ' . BASE_URL . 'products');
        exit;
    }
    renderLayout(PAGES_PATH . 'login.php', [
        'pageTitle' => 'Sign In - SERAPH'
    ]);
});

// Register redirects to login (same email-based auth)
$router->get('/register', function() {
    header('Location: ' . BASE_URL . 'login');
    exit;
});

// Logout
$router->get('/logout', function() {
    logoutUser();
    header('Location: ' . BASE_URL);
    exit;
});

// ============================================
// Account Routes
// ============================================

$router->get('/account', function() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
    
    $user = getUserProfile();
    renderLayout(PAGES_PATH . 'account/profile.php', [
        'pageTitle' => 'My Profile - SERAPH',
        'user' => $user
    ]);
});

$router->get('/account/addresses', function() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
    
    renderLayout(PAGES_PATH . 'account/addresses.php', [
        'pageTitle' => 'My Addresses - SERAPH'
    ]);
});

// ============================================
// API Routes
// ============================================

// Profile API
$router->post('/api/profile/update', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }
    
    $result = updateUserProfile($input);
    echo json_encode($result);
});

// Address APIs
$router->post('/api/address/save', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }
    
    $result = saveAddress($input);
    echo json_encode($result);
});

$router->post('/api/address/delete', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }
    
    $result = deleteAddress($input['id']);
    echo json_encode($result);
});

$router->post('/api/address/set-default', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }
    
    $result = setDefaultAddress($input['id']);
    echo json_encode($result);
});

// Auth API - Send verification code
$router->post('/api/auth/send-code', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['email'])) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        return;
    }
    
    $result = initiateAuth($input['email']);
    echo json_encode($result);
});

// Auth API - Verify code and login/signup
$router->post('/api/auth/verify-code', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['email']) || empty($input['code'])) {
        echo json_encode(['success' => false, 'message' => 'Email and code are required']);
        return;
    }

    $result = completeAuth($input['email'], $input['code'], $input['name'] ?? null);

    // Set remember me cookie if requested and login was successful
    if ($result['success'] && !empty($input['remember_me']) && isset($_SESSION['user_id'])) {
        setRememberMeCookie($_SESSION['user_id']);
    }

    echo json_encode($result);
});

// Cart APIs
$router->post('/api/cart/add', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    $result = addToCart($input['product_id'], $input['quantity'] ?? 1);
    echo json_encode($result);
});

$router->post('/api/cart/update', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    $result = updateCartItem($input['product_id'], $input['quantity']);
    echo json_encode($result);
});

$router->post('/api/cart/remove', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    $result = removeFromCart($input['product_id']);
    echo json_encode($result);
});

$router->get('/api/cart', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'cart' => getCart(),
        'count' => getCartCount(),
        'total' => getCartTotal()
    ]);
});

// Currency API
$router->post('/api/currency/set', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    $currency = $input['currency'] ?? '';

    if (setCurrentCurrency($currency)) {
        echo json_encode([
            'success' => true,
            'currency' => getCurrencyDetails($currency)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid currency'
        ]);
    }
});

$router->get('/api/currency', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'current' => getCurrentCurrency(),
        'details' => getCurrencyDetails(),
        'all' => getAllCurrencies()
    ]);
});

// Order API
$router->post('/api/orders/create', function() {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to place order']);
        return;
    }

    $result = createOrder(
        $input['payment_ref'] ?? '',
        $input['payment_status'] ?? 'pending',
        $input['address_id'] ?? null,
        $input['shipping_method'] ?? 'delivery'
    );
    echo json_encode($result);
});

// Subscribe Handler
$router->post('/subscribe', function() {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        return;
    }

    global $db;

    $existing = $db->findOne('subscribers', ['email' => $email]);
    if ($existing) {
        echo json_encode(['success' => true, 'message' => 'You are already subscribed!']);
        return;
    }

    $subscriber = [
        'email' => $email,
        'subscribed_at' => date('Y-m-d H:i:s'),
        'source' => 'newsletter_section',
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    $db->insert('subscribers', $subscriber);
    sendWelcomeEmail('Subscriber', $email);

    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
});

// Distributor Application Handler
$router->post('/api/distributor-application', function() {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $fullName = trim($input['fullName'] ?? '');
    $email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = trim($input['phone'] ?? '');
    $location = trim($input['location'] ?? '');
    $businessType = trim($input['businessType'] ?? '');
    $message = trim($input['message'] ?? '');

    if (empty($fullName) || empty($email) || empty($phone) || empty($location)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        return;
    }

    global $db;

    // Store the application
    $application = [
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'location' => $location,
        'business_type' => $businessType,
        'message' => $message,
        'submitted_at' => date('Y-m-d H:i:s'),
        'status' => 'pending',
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    $db->insert('distributor_applications', $application);

    // Send notification email to admin
    sendDistributorApplicationEmail($application);

    echo json_encode(['success' => true, 'message' => 'Thank you for your application! We will be in touch soon.']);
});

// ============================================
// Admin Routes
// ============================================

// Admin Login
$router->get('/admin/login', function() {
    if (isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }
    renderAdminLoginLayout(PAGES_PATH . 'admin/login.php', [
        'pageTitle' => 'Admin Login'
    ]);
});

$router->post('/admin/login', function() {
    if (isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }

    // Process login before rendering layout
    $error = '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $result = adminLogin($email, $password);
        if ($result['success']) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        } else {
            $error = $result['message'];
        }
    }

    renderAdminLoginLayout(PAGES_PATH . 'admin/login.php', [
        'pageTitle' => 'Admin Login',
        'error' => $error,
        'email' => $email
    ]);
});

// Admin Logout
$router->get('/admin/logout', function() {
    adminLogout();
    header('Location: ' . BASE_URL . 'admin/login');
    exit;
});

// Admin Dashboard
$router->get('/admin', function() {
    renderAdminLayout(PAGES_PATH . 'admin/dashboard.php', [
        'pageTitle' => 'Dashboard'
    ]);
});

// Admin Orders
$router->get('/admin/orders', function() {
    renderAdminLayout(PAGES_PATH . 'admin/orders.php', [
        'pageTitle' => 'Orders'
    ]);
});

// Admin Products
$router->get('/admin/products', function() {
    renderAdminLayout(PAGES_PATH . 'admin/products.php', [
        'pageTitle' => 'Products'
    ]);
});

// Admin Customers
$router->get('/admin/customers', function() {
    renderAdminLayout(PAGES_PATH . 'admin/customers.php', [
        'pageTitle' => 'Customers'
    ]);
});

// Admin Subscribers
$router->get('/admin/subscribers', function() {
    renderAdminLayout(PAGES_PATH . 'admin/subscribers.php', [
        'pageTitle' => 'Subscribers'
    ]);
});

// Admin Distributors
$router->get('/admin/distributors', function() {
    renderAdminLayout(PAGES_PATH . 'admin/distributors.php', [
        'pageTitle' => 'Distributor Applications'
    ]);
});

// Admin Analytics
$router->get('/admin/analytics', function() {
    renderAdminLayout(PAGES_PATH . 'admin/analytics.php', [
        'pageTitle' => 'Analytics'
    ]);
});

// Admin Settings
$router->get('/admin/settings', function() {
    renderAdminLayout(PAGES_PATH . 'admin/settings.php', [
        'pageTitle' => 'Settings'
    ]);
});

$router->post('/admin/settings', function() {
    renderAdminLayout(PAGES_PATH . 'admin/settings.php', [
        'pageTitle' => 'Settings'
    ]);
});

// Admin Users Management
$router->get('/admin/admins', function() {
    renderAdminLayout(PAGES_PATH . 'admin/admins.php', [
        'pageTitle' => 'Admin Users'
    ]);
});

// Admin Profile
$router->get('/admin/profile', function() {
    renderAdminLayout(PAGES_PATH . 'admin/profile.php', [
        'pageTitle' => 'My Profile'
    ]);
});

$router->post('/admin/profile', function() {
    renderAdminLayout(PAGES_PATH . 'admin/profile.php', [
        'pageTitle' => 'My Profile'
    ]);
});

// ============================================
// Admin API Routes
// ============================================

// Image Upload API
$router->post('/api/admin/upload-image', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File too large',
            UPLOAD_ERR_FORM_SIZE => 'File too large',
            UPLOAD_ERR_PARTIAL => 'File partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Server error',
            UPLOAD_ERR_CANT_WRITE => 'Server error',
            UPLOAD_ERR_EXTENSION => 'Upload blocked'
        ];
        $error = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
        echo json_encode(['success' => false, 'message' => $errorMessages[$error] ?? 'Upload failed']);
        return;
    }

    $file = $_FILES['image'];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG and WebP allowed']);
        return;
    }

    // Validate file size (2MB max)
    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum 2MB allowed']);
        return;
    }

    // Generate unique filename
    $extension = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg'
    };
    $filename = 'product_' . uniqid() . '.' . $extension;
    $uploadPath = PUBLIC_PATH . 'assets/images/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo json_encode(['success' => true, 'message' => 'Image uploaded', 'filename' => $filename]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    }
});

// Products API
$router->post('/api/admin/products/create', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = createProduct($input);
    echo json_encode($result);
});

$router->post('/api/admin/products/update', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = updateProduct($input['id'], $input);
    echo json_encode($result);
});

$router->post('/api/admin/products/delete', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = deleteProduct($input['id']);
    echo json_encode($result);
});

// Orders API
$router->post('/api/admin/orders/update-status', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    global $db;

    // Get current order to check for status change
    $order = $db->findOne('orders', ['id' => (int)$input['id']]);
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        return;
    }

    $oldStatus = $order['order_status'] ?? '';
    $newStatus = $input['order_status'] ?? $oldStatus;

    $updates = ['updated_at' => date('Y-m-d H:i:s')];

    if (isset($input['order_status'])) {
        $updates['order_status'] = $input['order_status'];
    }

    if (isset($input['payment_status'])) {
        $updates['payment_status'] = $input['payment_status'];
    }

    $db->update('orders', ['id' => (int)$input['id']], $updates);

    // Send email notification if order status changed
    if (isset($input['order_status']) && $oldStatus !== $newStatus) {
        // Get updated order data
        $updatedOrder = $db->findOne('orders', ['id' => (int)$input['id']]);
        $trackingNumber = $updatedOrder['tracking_number'] ?? null;
        sendOrderStatusEmail($updatedOrder, $newStatus, $trackingNumber);
    }

    echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
});

$router->post('/api/admin/orders/add-note', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = addOrderNote($input['id'], $input['note']);
    echo json_encode($result);
});

$router->post('/api/admin/orders/update-tracking', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    global $db;

    // Get order before update
    $order = $db->findOne('orders', ['id' => (int)$input['id']]);
    $hadTracking = !empty($order['tracking_number']);

    $result = updateOrderTracking($input['id'], $input['tracking_number'] ?? '', $input['tracking_url'] ?? '');

    // Send email if tracking was just added and order is shipped
    $newTracking = $input['tracking_number'] ?? '';
    if ($result['success'] && !$hadTracking && !empty($newTracking)) {
        $updatedOrder = $db->findOne('orders', ['id' => (int)$input['id']]);
        if ($updatedOrder && ($updatedOrder['order_status'] === 'shipped' || $updatedOrder['order_status'] === 'processing')) {
            sendOrderStatusEmail($updatedOrder, 'shipped', $newTracking);
        }
    }

    echo json_encode($result);
});

// Customers API
$router->post('/api/admin/customers/update', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = adminUpdateUser($input['id'], $input);
    echo json_encode($result);
});

$router->post('/api/admin/customers/delete', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = adminDeleteUser($input['id']);
    echo json_encode($result);
});

// Subscribers API
$router->post('/api/admin/subscribers/update', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = updateSubscriber($input['id'], $input);
    echo json_encode($result);
});

$router->post('/api/admin/subscribers/delete', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = deleteSubscriber($input['id']);
    echo json_encode($result);
});

// Distributor Applications API
$router->post('/api/admin/distributors/update', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = updateDistributorApplication($input['id'], $input);
    echo json_encode($result);
});

$router->post('/api/admin/distributors/delete', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = deleteDistributorApplication($input['id']);
    echo json_encode($result);
});

// Admin Users API
$router->post('/api/admin/admins/create', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = createAdmin($input);
    echo json_encode($result);
});

$router->post('/api/admin/admins/update', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = updateAdmin($input['id'], $input);
    echo json_encode($result);
});

$router->post('/api/admin/admins/delete', function() {
    header('Content-Type: application/json');

    if (!isAdminLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        return;
    }

    $result = deleteAdmin($input['id']);
    echo json_encode($result);
});

// 404 Handler
$router->notFound(function() {
    renderLayout(PAGES_PATH . '404.php', [
        'pageTitle' => '404 Not Found - ' . APP_NAME
    ]);
});

// Run the router
$router->run();

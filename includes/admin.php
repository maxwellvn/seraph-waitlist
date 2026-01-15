<?php
/**
 * Admin Authentication & Management Functions
 */

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Get current admin data
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }

    global $db;
    return $db->findOne('admins', ['id' => $_SESSION['admin_id']]);
}

/**
 * Admin login
 */
function adminLogin($email, $password) {
    global $db;

    $admin = $db->findOne('admins', ['email' => strtolower(trim($email))]);

    if (!$admin) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    if (!password_verify($password, $admin['password'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    if ($admin['status'] !== 'active') {
        return ['success' => false, 'message' => 'Account is disabled'];
    }

    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_role'] = $admin['role'];

    // Update last login
    $db->update('admins', ['id' => $admin['id']], [
        'last_login' => date('Y-m-d H:i:s')
    ]);

    return ['success' => true, 'message' => 'Login successful'];
}

/**
 * Admin logout
 */
function adminLogout() {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_role']);
}

/**
 * Create admin account
 */
function createAdmin($data) {
    global $db;

    // Validate required fields
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        return ['success' => false, 'message' => 'All fields are required'];
    }

    $email = strtolower(trim($data['email']));

    // Check if email exists
    $existing = $db->findOne('admins', ['email' => $email]);
    if ($existing) {
        return ['success' => false, 'message' => 'Email already exists'];
    }

    $admin = [
        'name' => trim($data['name']),
        'email' => $email,
        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        'role' => $data['role'] ?? 'admin',
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s'),
        'last_login' => null
    ];

    $result = $db->insert('admins', $admin);

    return ['success' => true, 'message' => 'Admin created successfully', 'admin' => $result];
}

/**
 * Update admin
 */
function updateAdmin($id, $data) {
    global $db;

    $updates = [];

    if (!empty($data['name'])) {
        $updates['name'] = trim($data['name']);
    }

    if (!empty($data['email'])) {
        $email = strtolower(trim($data['email']));
        $existing = $db->findOne('admins', ['email' => $email]);
        if ($existing && $existing['id'] !== (int)$id) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        $updates['email'] = $email;
    }

    if (!empty($data['password'])) {
        $updates['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    if (isset($data['role'])) {
        $updates['role'] = $data['role'];
    }

    if (isset($data['status'])) {
        $updates['status'] = $data['status'];
    }

    if (empty($updates)) {
        return ['success' => false, 'message' => 'No data to update'];
    }

    $db->update('admins', ['id' => (int)$id], $updates);

    return ['success' => true, 'message' => 'Admin updated successfully'];
}

/**
 * Delete admin
 */
function deleteAdmin($id) {
    global $db;

    // Prevent deleting self
    if ((int)$id === $_SESSION['admin_id']) {
        return ['success' => false, 'message' => 'Cannot delete your own account'];
    }

    $count = $db->delete('admins', ['id' => (int)$id]);

    if ($count > 0) {
        return ['success' => true, 'message' => 'Admin deleted successfully'];
    }

    return ['success' => false, 'message' => 'Admin not found'];
}

/**
 * Get all admins
 */
function getAllAdmins() {
    global $db;
    $admins = $db->find('admins');
    // Remove passwords from response
    return array_map(function($admin) {
        unset($admin['password']);
        return $admin;
    }, $admins);
}

// ==========================================
// DASHBOARD STATS FUNCTIONS
// ==========================================

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    global $db;

    $users = $db->find('users');
    $orders = $db->find('orders');
    $products = $db->find('products');
    $subscribers = $db->find('subscribers');

    // Calculate revenue
    $totalRevenue = 0;
    $completedOrders = 0;
    $pendingOrders = 0;

    foreach ($orders as $order) {
        if ($order['payment_status'] === 'completed') {
            $totalRevenue += $order['total'] ?? 0;
            $completedOrders++;
        }
        if ($order['order_status'] === 'pending') {
            $pendingOrders++;
        }
    }

    // Recent orders (last 5)
    $recentOrders = array_slice(array_reverse($orders), 0, 5);

    // Recent subscribers (last 5)
    $recentSubscribers = array_slice(array_reverse($subscribers), 0, 5);

    // Get site visits
    $siteVisits = getSiteVisits();

    return [
        'total_users' => count($users),
        'total_orders' => count($orders),
        'total_products' => count($products),
        'total_subscribers' => count($subscribers),
        'total_revenue' => $totalRevenue,
        'completed_orders' => $completedOrders,
        'pending_orders' => $pendingOrders,
        'recent_orders' => $recentOrders,
        'recent_subscribers' => $recentSubscribers,
        'site_visits' => $siteVisits
    ];
}

/**
 * Track a site visit
 */
function trackVisit() {
    // Don't track admin pages or API requests
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($uri, '/admin') !== false || strpos($uri, '/api/') !== false) {
        return;
    }

    // Don't track bots
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/bot|crawl|spider|slurp|googlebot|bingbot/i', $userAgent)) {
        return;
    }

    $visitsFile = DATA_PATH . 'visits.json';

    $visits = ['total' => 0];
    if (file_exists($visitsFile)) {
        $content = file_get_contents($visitsFile);
        $visits = json_decode($content, true) ?: ['total' => 0];
    }

    $visits['total']++;
    $visits['last_updated'] = date('Y-m-d H:i:s');

    file_put_contents($visitsFile, json_encode($visits, JSON_PRETTY_PRINT));
}

/**
 * Get total site visits
 */
function getSiteVisits() {
    $visitsFile = DATA_PATH . 'visits.json';

    if (!file_exists($visitsFile)) {
        return 0;
    }

    $content = file_get_contents($visitsFile);
    $visits = json_decode($content, true);

    return $visits['total'] ?? 0;
}

// ==========================================
// PRODUCT MANAGEMENT FUNCTIONS
// ==========================================

/**
 * Get all products
 */
function getAllProducts() {
    global $db;
    return $db->find('products');
}

/**
 * Get single product
 */
function getProduct($id) {
    global $db;
    return $db->findOne('products', ['id' => (int)$id]);
}

/**
 * Create product
 */
function createProduct($data) {
    global $db;

    if (empty($data['name']) || empty($data['price'])) {
        return ['success' => false, 'message' => 'Name and price are required'];
    }

    // Handle gallery - ensure it's an array
    $gallery = [];
    if (!empty($data['gallery'])) {
        if (is_array($data['gallery'])) {
            $gallery = $data['gallery'];
        } elseif (is_string($data['gallery'])) {
            $decoded = json_decode($data['gallery'], true);
            $gallery = is_array($decoded) ? $decoded : [];
        }
    }

    $product = [
        'name' => trim($data['name']),
        'price' => (float)$data['price'],
        'description' => trim($data['description'] ?? ''),
        'image' => $data['image'] ?? '',
        'hover_image' => $data['hover_image'] ?? '',
        'gallery' => $gallery,
        'flavour' => trim($data['flavour'] ?? ''),
        'stock' => (int)($data['stock'] ?? 100),
        'status' => $data['status'] ?? 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $result = $db->insert('products', $product);

    return ['success' => true, 'message' => 'Product created successfully', 'product' => $result];
}

/**
 * Update product
 */
function updateProduct($id, $data) {
    global $db;

    $updates = [];

    if (isset($data['name'])) $updates['name'] = trim($data['name']);
    if (isset($data['price'])) $updates['price'] = (float)$data['price'];
    if (isset($data['description'])) $updates['description'] = trim($data['description']);
    if (isset($data['image'])) $updates['image'] = $data['image'];
    if (isset($data['hover_image'])) $updates['hover_image'] = $data['hover_image'];

    // Handle gallery - ensure it's an array
    if (isset($data['gallery'])) {
        if (is_array($data['gallery'])) {
            $updates['gallery'] = $data['gallery'];
        } elseif (is_string($data['gallery'])) {
            $decoded = json_decode($data['gallery'], true);
            $updates['gallery'] = is_array($decoded) ? $decoded : [];
        }
    }

    if (isset($data['flavour'])) $updates['flavour'] = trim($data['flavour']);
    if (isset($data['stock'])) $updates['stock'] = (int)$data['stock'];
    if (isset($data['status'])) $updates['status'] = $data['status'];

    if (empty($updates)) {
        return ['success' => false, 'message' => 'No data to update'];
    }

    $updates['updated_at'] = date('Y-m-d H:i:s');

    $db->update('products', ['id' => (int)$id], $updates);

    return ['success' => true, 'message' => 'Product updated successfully'];
}

/**
 * Delete product
 */
function deleteProduct($id) {
    global $db;

    $count = $db->delete('products', ['id' => (int)$id]);

    if ($count > 0) {
        return ['success' => true, 'message' => 'Product deleted successfully'];
    }

    return ['success' => false, 'message' => 'Product not found'];
}

// ==========================================
// ORDER MANAGEMENT FUNCTIONS
// ==========================================

/**
 * Get all orders with optional filters
 */
function getAllOrders($filters = []) {
    global $db;
    $orders = $db->find('orders');

    // Apply filters
    if (!empty($filters['status'])) {
        $orders = array_filter($orders, function($order) use ($filters) {
            return $order['order_status'] === $filters['status'];
        });
    }

    if (!empty($filters['payment_status'])) {
        $orders = array_filter($orders, function($order) use ($filters) {
            return $order['payment_status'] === $filters['payment_status'];
        });
    }

    // Sort by date descending
    usort($orders, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    return array_values($orders);
}

/**
 * Get single order
 */
function getOrderById($id) {
    global $db;
    return $db->findOne('orders', ['id' => (int)$id]);
}

/**
 * Update order status
 */
function updateOrderStatus($id, $status, $type = 'order') {
    global $db;

    $validOrderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    $validPaymentStatuses = ['pending', 'completed', 'failed', 'refunded'];

    $updates = [];

    if ($type === 'order') {
        if (!in_array($status, $validOrderStatuses)) {
            return ['success' => false, 'message' => 'Invalid order status'];
        }
        $updates['order_status'] = $status;
    } else {
        if (!in_array($status, $validPaymentStatuses)) {
            return ['success' => false, 'message' => 'Invalid payment status'];
        }
        $updates['payment_status'] = $status;
    }

    $updates['updated_at'] = date('Y-m-d H:i:s');

    $db->update('orders', ['id' => (int)$id], $updates);

    return ['success' => true, 'message' => 'Order updated successfully'];
}

/**
 * Add tracking info to order
 */
function updateOrderTracking($id, $trackingNumber, $trackingUrl = '') {
    global $db;

    $updates = [
        'tracking_number' => $trackingNumber,
        'tracking_url' => $trackingUrl,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $db->update('orders', ['id' => (int)$id], $updates);

    return ['success' => true, 'message' => 'Tracking info updated successfully'];
}

/**
 * Add note to order
 */
function addOrderNote($id, $note) {
    global $db;

    $order = getOrderById($id);
    if (!$order) {
        return ['success' => false, 'message' => 'Order not found'];
    }

    $notes = $order['notes'] ?? [];
    $notes[] = [
        'note' => $note,
        'admin' => $_SESSION['admin_name'] ?? 'System',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db->update('orders', ['id' => (int)$id], ['notes' => $notes, 'updated_at' => date('Y-m-d H:i:s')]);

    return ['success' => true, 'message' => 'Note added successfully'];
}

// ==========================================
// USER/CUSTOMER MANAGEMENT FUNCTIONS
// ==========================================

/**
 * Get all users/customers
 */
function getAllUsers() {
    global $db;
    $users = $db->find('users');

    // Remove sensitive data
    return array_map(function($user) {
        unset($user['password']);
        return $user;
    }, $users);
}

/**
 * Get single user
 */
function getUserById($id) {
    global $db;
    $user = $db->findOne('users', ['id' => (int)$id]);
    if ($user) {
        unset($user['password']);
    }
    return $user;
}

/**
 * Update user
 */
function adminUpdateUser($id, $data) {
    global $db;

    $updates = [];

    if (isset($data['name'])) $updates['name'] = trim($data['name']);
    if (isset($data['email'])) $updates['email'] = strtolower(trim($data['email']));
    if (isset($data['phone'])) $updates['phone'] = trim($data['phone']);
    if (isset($data['status'])) $updates['status'] = $data['status'];

    if (empty($updates)) {
        return ['success' => false, 'message' => 'No data to update'];
    }

    $updates['updated_at'] = date('Y-m-d H:i:s');

    $db->update('users', ['id' => (int)$id], $updates);

    return ['success' => true, 'message' => 'User updated successfully'];
}

/**
 * Delete user
 */
function adminDeleteUser($id) {
    global $db;

    $count = $db->delete('users', ['id' => (int)$id]);

    if ($count > 0) {
        return ['success' => true, 'message' => 'User deleted successfully'];
    }

    return ['success' => false, 'message' => 'User not found'];
}

/**
 * Get user order history
 */
function getUserOrderHistory($userId) {
    global $db;
    $orders = $db->find('orders', ['user_id' => (int)$userId]);
    return array_reverse($orders);
}

// ==========================================
// SUBSCRIBER MANAGEMENT FUNCTIONS
// ==========================================

/**
 * Get all subscribers
 */
function getAllSubscribers($filters = []) {
    global $db;
    $subscribers = $db->find('subscribers');

    // Apply filters
    if (!empty($filters['status'])) {
        $subscribers = array_filter($subscribers, function($sub) use ($filters) {
            return ($sub['status'] ?? 'active') === $filters['status'];
        });
    }

    if (!empty($filters['source'])) {
        $subscribers = array_filter($subscribers, function($sub) use ($filters) {
            return ($sub['source'] ?? '') === $filters['source'];
        });
    }

    // Sort by date descending
    usort($subscribers, function($a, $b) {
        return strtotime($b['subscribed_at'] ?? $b['created_at'] ?? '0') -
               strtotime($a['subscribed_at'] ?? $a['created_at'] ?? '0');
    });

    return array_values($subscribers);
}

/**
 * Get single subscriber
 */
function getSubscriberById($id) {
    global $db;
    return $db->findOne('subscribers', ['id' => (int)$id]);
}

/**
 * Update subscriber
 */
function updateSubscriber($id, $data) {
    global $db;

    $updates = [];

    if (isset($data['name'])) $updates['name'] = trim($data['name']);
    if (isset($data['email'])) $updates['email'] = strtolower(trim($data['email']));
    if (isset($data['phone'])) $updates['phone'] = trim($data['phone']);
    if (isset($data['city'])) $updates['city'] = trim($data['city']);
    if (isset($data['status'])) $updates['status'] = $data['status'];

    if (empty($updates)) {
        return ['success' => false, 'message' => 'No data to update'];
    }

    $db->update('subscribers', ['id' => (int)$id], $updates);

    return ['success' => true, 'message' => 'Subscriber updated successfully'];
}

/**
 * Delete subscriber
 */
function deleteSubscriber($id) {
    global $db;

    $count = $db->delete('subscribers', ['id' => (int)$id]);

    if ($count > 0) {
        return ['success' => true, 'message' => 'Subscriber deleted successfully'];
    }

    return ['success' => false, 'message' => 'Subscriber not found'];
}

/**
 * Export subscribers as CSV
 */
function exportSubscribers($filters = []) {
    $subscribers = getAllSubscribers($filters);

    $csv = "ID,Name,Email,Phone,City,Source,Status,Subscribed Date\n";

    foreach ($subscribers as $sub) {
        $csv .= sprintf(
            "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
            $sub['id'] ?? '',
            $sub['name'] ?? '',
            $sub['email'] ?? '',
            $sub['phone'] ?? '',
            $sub['city'] ?? '',
            $sub['source'] ?? '',
            $sub['status'] ?? 'active',
            $sub['subscribed_at'] ?? $sub['created_at'] ?? ''
        );
    }

    return $csv;
}

// ==========================================
// SETTINGS MANAGEMENT FUNCTIONS
// ==========================================

/**
 * Get all settings
 */
function getSettings() {
    global $db;
    $settings = $db->read('settings');

    if (empty($settings)) {
        // Default settings
        return [
            'site_name' => APP_NAME,
            'site_email' => ADMIN_EMAIL,
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'shipping_fee' => 0,
            'tax_rate' => 0,
            'order_prefix' => 'ORD-',
            'low_stock_threshold' => 10,
            'enable_notifications' => true,
            'maintenance_mode' => false
        ];
    }

    return $settings;
}

/**
 * Update settings
 */
function updateSettings($data) {
    global $db;

    $settings = getSettings();

    foreach ($data as $key => $value) {
        $settings[$key] = $value;
    }

    $settings['updated_at'] = date('Y-m-d H:i:s');

    $db->write('settings', $settings);

    return ['success' => true, 'message' => 'Settings updated successfully'];
}

// ==========================================
// ANALYTICS FUNCTIONS
// ==========================================

/**
 * Get sales analytics
 */
function getSalesAnalytics($period = 'month') {
    global $db;
    $orders = $db->find('orders');

    $now = time();
    $analytics = [
        'total_sales' => 0,
        'total_orders' => 0,
        'average_order' => 0,
        'by_date' => [],
        'by_product' => [],
        'by_status' => [
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0
        ]
    ];

    // Define period
    switch ($period) {
        case 'week':
            $start = strtotime('-7 days');
            break;
        case 'month':
            $start = strtotime('-30 days');
            break;
        case 'year':
            $start = strtotime('-365 days');
            break;
        default:
            $start = 0;
    }

    foreach ($orders as $order) {
        $orderDate = strtotime($order['created_at']);

        if ($orderDate >= $start) {
            // Count by status
            $status = $order['order_status'] ?? 'pending';
            $analytics['by_status'][$status] = ($analytics['by_status'][$status] ?? 0) + 1;

            if ($order['payment_status'] === 'completed') {
                $analytics['total_sales'] += $order['total'] ?? 0;
                $analytics['total_orders']++;

                // Group by date
                $dateKey = date('Y-m-d', $orderDate);
                if (!isset($analytics['by_date'][$dateKey])) {
                    $analytics['by_date'][$dateKey] = ['sales' => 0, 'orders' => 0];
                }
                $analytics['by_date'][$dateKey]['sales'] += $order['total'] ?? 0;
                $analytics['by_date'][$dateKey]['orders']++;

                // Group by product
                foreach ($order['items'] ?? [] as $item) {
                    $productName = $item['name'] ?? 'Unknown';
                    if (!isset($analytics['by_product'][$productName])) {
                        $analytics['by_product'][$productName] = ['quantity' => 0, 'revenue' => 0];
                    }
                    $analytics['by_product'][$productName]['quantity'] += $item['quantity'] ?? 0;
                    $analytics['by_product'][$productName]['revenue'] += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
                }
            }
        }
    }

    if ($analytics['total_orders'] > 0) {
        $analytics['average_order'] = $analytics['total_sales'] / $analytics['total_orders'];
    }

    return $analytics;
}

/**
 * Get subscriber analytics
 */
function getSubscriberAnalytics() {
    global $db;
    $subscribers = $db->find('subscribers');

    $analytics = [
        'total' => count($subscribers),
        'active' => 0,
        'inactive' => 0,
        'by_source' => [],
        'by_city' => [],
        'by_month' => []
    ];

    foreach ($subscribers as $sub) {
        // By status
        $status = $sub['status'] ?? 'active';
        if ($status === 'active') {
            $analytics['active']++;
        } else {
            $analytics['inactive']++;
        }

        // By source
        $source = $sub['source'] ?? 'unknown';
        $analytics['by_source'][$source] = ($analytics['by_source'][$source] ?? 0) + 1;

        // By city
        $city = $sub['city'] ?? 'Unknown';
        $analytics['by_city'][$city] = ($analytics['by_city'][$city] ?? 0) + 1;

        // By month
        $date = $sub['subscribed_at'] ?? $sub['created_at'] ?? null;
        if ($date) {
            $monthKey = date('Y-m', strtotime($date));
            $analytics['by_month'][$monthKey] = ($analytics['by_month'][$monthKey] ?? 0) + 1;
        }
    }

    // Sort by_month
    ksort($analytics['by_month']);

    return $analytics;
}

// ==========================================
// DISTRIBUTOR APPLICATION FUNCTIONS
// ==========================================

/**
 * Get all distributor applications
 */
function getDistributorApplications($filters = []) {
    global $db;
    $applications = $db->find('distributor_applications');

    // Apply filters
    if (!empty($filters['status'])) {
        $applications = array_filter($applications, function($app) use ($filters) {
            return ($app['status'] ?? 'pending') === $filters['status'];
        });
    }

    // Sort by date descending
    usort($applications, function($a, $b) {
        return strtotime($b['submitted_at'] ?? $b['created_at'] ?? '0') -
               strtotime($a['submitted_at'] ?? $a['created_at'] ?? '0');
    });

    return array_values($applications);
}

/**
 * Get single distributor application
 */
function getDistributorApplicationById($id) {
    global $db;
    return $db->findOne('distributor_applications', ['id' => (int)$id]);
}

/**
 * Update distributor application
 */
function updateDistributorApplication($id, $data) {
    global $db;

    $updates = [];

    if (isset($data['full_name'])) $updates['full_name'] = trim($data['full_name']);
    if (isset($data['email'])) $updates['email'] = strtolower(trim($data['email']));
    if (isset($data['phone'])) $updates['phone'] = trim($data['phone']);
    if (isset($data['location'])) $updates['location'] = trim($data['location']);
    if (isset($data['business_type'])) $updates['business_type'] = trim($data['business_type']);
    if (isset($data['message'])) $updates['message'] = trim($data['message']);
    if (isset($data['status'])) $updates['status'] = $data['status'];
    if (isset($data['admin_notes'])) $updates['admin_notes'] = trim($data['admin_notes']);

    if (empty($updates)) {
        return ['success' => false, 'message' => 'No data to update'];
    }

    $updates['updated_at'] = date('Y-m-d H:i:s');

    $db->update('distributor_applications', ['id' => (int)$id], $updates);

    return ['success' => true, 'message' => 'Application updated successfully'];
}

/**
 * Delete distributor application
 */
function deleteDistributorApplication($id) {
    global $db;

    $count = $db->delete('distributor_applications', ['id' => (int)$id]);

    if ($count > 0) {
        return ['success' => true, 'message' => 'Application deleted successfully'];
    }

    return ['success' => false, 'message' => 'Application not found'];
}

/**
 * Export distributor applications as CSV
 */
function exportDistributorApplications($filters = []) {
    $applications = getDistributorApplications($filters);

    $csv = "ID,Full Name,Email,Phone,Location,Business Type,Status,Submitted Date,Message\n";

    foreach ($applications as $app) {
        $csv .= sprintf(
            "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
            $app['id'] ?? '',
            str_replace('"', '""', $app['full_name'] ?? ''),
            $app['email'] ?? '',
            $app['phone'] ?? '',
            str_replace('"', '""', $app['location'] ?? ''),
            $app['business_type'] ?? '',
            $app['status'] ?? 'pending',
            $app['submitted_at'] ?? '',
            str_replace('"', '""', str_replace(["\r", "\n"], ' ', $app['message'] ?? ''))
        );
    }

    return $csv;
}

<?php
/**
 * Helper Functions
 * Useful utility functions for the application
 */

/**
 * Generate CSRF Token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF Input Field
 */
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Generate unique Address ID
 */
function generateAddressId() {
    return 'addr_' . bin2hex(random_bytes(8));
}

/**
 * Sanitize Output
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize output for HTML (legacy alias)
 */
function e($string) {
    return h($string);
}

/**
 * Redirect to a URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Get current URL path
 */
function currentUrl() {
    return $_SERVER['REQUEST_URI'];
}

/**
 * Check if current page matches path
 */
function isActive($path) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = parse_url(BASE_URL, PHP_URL_PATH);
    $relativePath = str_replace($basePath, '', $currentPath);
    
    return $relativePath === $path || $relativePath === '/' . trim($path, '/');
}

/**
 * Format date
 */
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Generate random string
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Get or set session flash messages
 */
function flash($key = null, $value = null) {
    if ($key === null) {
        return $_SESSION;
    }
    
    if ($value === null) {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }
    
    $_SESSION[$key] = $value;
}

/**
 * Debug helper - print and die
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Debug helper - print
 */
function dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * Check if request is POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get POST data
 */
function post($key = null, $default = null) {
    if ($key === null) {
        return $_POST;
    }
    return $_POST[$key] ?? $default;
}

/**
 * Get GET data
 */
function get($key = null, $default = null) {
    if ($key === null) {
        return $_GET;
    }
    return $_GET[$key] ?? $default;
}

/**
 * Generate asset URL
 */
function asset($path) {
    return BASE_URL . 'public/assets/' . ltrim($path, '/');
}

/**
 * Generate URL
 */
function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

/**
 * Truncate string
 */
function str_limit($string, $limit = 100, $end = '...') {
    if (mb_strlen($string) <= $limit) {
        return $string;
    }
    return mb_substr($string, 0, $limit) . $end;
}

/**
 * Convert array to JSON
 */
function toJson($data) {
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Parse JSON to array
 */
function fromJson($json) {
    return json_decode($json, true);
}


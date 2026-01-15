<?php

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) continue;

        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                $value = $matches[1];
            }

            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Helper function to get env value with default
function env($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Application Configuration
define('APP_NAME', env('APP_NAME', 'SERAPH'));
define('APP_VERSION', env('APP_VERSION', '1.0.0'));
define('APP_ENV', env('APP_ENV', 'development'));

// Base URL Configuration
define('BASE_URL', env('BASE_URL', '/'));
define('BASE_PATH', __DIR__ . '/../');

// Directory Paths
define('DATA_PATH', BASE_PATH . 'data/');
define('INCLUDES_PATH', BASE_PATH . 'includes/');
define('COMPONENTS_PATH', BASE_PATH . 'components/');
define('PAGES_PATH', BASE_PATH . 'pages/');
define('PUBLIC_PATH', BASE_PATH . 'public/');

// Email Configuration (SMTP)
define('SMTP_HOST', env('SMTP_HOST', 'smtp.hostinger.com'));
define('SMTP_PORT', (int) env('SMTP_PORT', 587));
define('SMTP_ENCRYPTION', env('SMTP_ENCRYPTION', 'tls'));
define('SMTP_USERNAME', env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD', env('SMTP_PASSWORD', ''));
define('SMTP_FROM_EMAIL', env('SMTP_FROM_EMAIL', ''));
define('SMTP_FROM_NAME', env('SMTP_FROM_NAME', 'Seraph'));
define('ADMIN_EMAIL', env('ADMIN_EMAIL', ''));

// Payment Configuration (Flutterwave)
define('FLUTTERWAVE_PUBLIC_KEY', env('FLUTTERWAVE_PUBLIC_KEY', ''));
define('FLUTTERWAVE_SECRET_KEY', env('FLUTTERWAVE_SECRET_KEY', ''));
define('FLUTTERWAVE_ENCRYPTION_KEY', env('FLUTTERWAVE_ENCRYPTION_KEY', ''));

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

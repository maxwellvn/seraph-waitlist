<?php
/**
 * Currency Helper Functions
 * Handles multi-currency pricing with geolocation detection
 */

// Supported currencies
define('CURRENCIES', [
    'NGN' => [
        'code' => 'NGN',
        'symbol' => '₦',
        'name' => 'Nigerian Naira',
        'region' => 'Nigeria',
        'flag' => '🇳🇬'
    ],
    'GBP' => [
        'code' => 'GBP',
        'symbol' => '£',
        'name' => 'British Pound',
        'region' => 'UK & Europe',
        'flag' => '🇬🇧'
    ],
    'USD' => [
        'code' => 'USD',
        'symbol' => '$',
        'name' => 'US Dollar',
        'region' => 'International',
        'flag' => '🇺🇸'
    ]
]);

// European country codes (for GBP region)
define('EUROPE_COUNTRIES', [
    'GB', 'UK', 'IE', 'FR', 'DE', 'IT', 'ES', 'PT', 'NL', 'BE',
    'AT', 'CH', 'SE', 'NO', 'DK', 'FI', 'PL', 'CZ', 'HU', 'RO',
    'BG', 'GR', 'HR', 'SK', 'SI', 'LT', 'LV', 'EE', 'LU', 'MT', 'CY'
]);

/**
 * Detect user's country from IP address
 * Uses free ip-api.com service
 */
function detectCountryFromIP() {
    // Check if we have cached result in session
    if (isset($_SESSION['detected_country'])) {
        return $_SESSION['detected_country'];
    }

    $country = null;

    // Get user's IP
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? null;

    // Skip for localhost
    if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
        // Handle multiple IPs (from proxies)
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        try {
            // Use ip-api.com (free, no API key required, 45 requests/minute)
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false,
                stream_context_create(['http' => ['timeout' => 2]])
            );

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['countryCode'])) {
                    $country = $data['countryCode'];
                }
            }
        } catch (Exception $e) {
            // Silently fail, will default to USD
        }
    }

    // Cache in session
    $_SESSION['detected_country'] = $country;

    return $country;
}

/**
 * Get currency code based on country
 */
function getCurrencyForCountry($countryCode) {
    if ($countryCode === 'NG') {
        return 'NGN';
    }

    if (in_array($countryCode, EUROPE_COUNTRIES)) {
        return 'GBP';
    }

    return 'USD'; // Default for everywhere else
}

/**
 * Get current user's currency
 * Priority: 1. Session preference, 2. Cookie preference, 3. Geolocation, 4. Default (USD)
 */
function getCurrentCurrency() {
    // Check session first (user manually selected)
    if (isset($_SESSION['currency']) && isset(CURRENCIES[$_SESSION['currency']])) {
        return $_SESSION['currency'];
    }

    // Check cookie
    if (isset($_COOKIE['seraph_currency']) && isset(CURRENCIES[$_COOKIE['seraph_currency']])) {
        $_SESSION['currency'] = $_COOKIE['seraph_currency'];
        return $_COOKIE['seraph_currency'];
    }

    // Try geolocation
    $country = detectCountryFromIP();
    if ($country) {
        $currency = getCurrencyForCountry($country);
        $_SESSION['currency'] = $currency;
        return $currency;
    }

    // Default to USD
    $_SESSION['currency'] = 'USD';
    return 'USD';
}

/**
 * Set user's preferred currency
 */
function setCurrentCurrency($currencyCode) {
    if (isset(CURRENCIES[$currencyCode])) {
        $_SESSION['currency'] = $currencyCode;
        // Set cookie for 30 days
        setcookie('seraph_currency', $currencyCode, time() + (30 * 24 * 60 * 60), '/');
        return true;
    }
    return false;
}

/**
 * Get currency details
 */
function getCurrencyDetails($currencyCode = null) {
    $code = $currencyCode ?? getCurrentCurrency();
    return CURRENCIES[$code] ?? CURRENCIES['USD'];
}

/**
 * Get all available currencies
 */
function getAllCurrencies() {
    return CURRENCIES;
}

/**
 * Format price with currency symbol
 */
function formatPrice($prices, $currencyCode = null) {
    $code = $currencyCode ?? getCurrentCurrency();
    $currency = getCurrencyDetails($code);

    // Handle both old single-price format and new multi-currency format
    if (is_array($prices)) {
        $amount = $prices[$code] ?? $prices['NGN'] ?? 0;
    } else {
        // Legacy: single price in NGN
        $amount = $prices;
        // If not NGN, we can't convert without rates, so show NGN
        if ($code !== 'NGN') {
            $code = 'NGN';
            $currency = getCurrencyDetails('NGN');
        }
    }

    // Format based on currency
    if ($code === 'NGN') {
        return $currency['symbol'] . number_format($amount);
    } else {
        return $currency['symbol'] . number_format($amount, 2);
    }
}

/**
 * Get price amount for current currency
 */
function getPriceAmount($prices, $currencyCode = null) {
    $code = $currencyCode ?? getCurrentCurrency();

    if (is_array($prices)) {
        return $prices[$code] ?? $prices['NGN'] ?? 0;
    }

    // Legacy single price
    return $prices;
}

/**
 * Get default prices array (for new products)
 */
function getDefaultPrices() {
    return [
        'NGN' => 0,
        'GBP' => 0,
        'USD' => 0
    ];
}

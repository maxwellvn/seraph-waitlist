<?php
/**
 * Shopify-style Passwordless Authentication
 * Uses email verification codes (OTP) instead of passwords
 */

/**
 * Generate a 6-digit verification code
 */
function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Send verification code to email
 */
function sendVerificationCode($email, $code) {
    $subject = 'Your Seraph Login Code';
    
    $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; line-height: 1.6; color: #2D2A26; background-color: #FAF6F1; }
        .container { max-width: 500px; margin: 40px auto; padding: 40px; background: white; }
        .logo { font-family: 'Playfair Display', Georgia, serif; font-size: 28px; color: #2D2A26; letter-spacing: 0.2em; text-align: center; margin-bottom: 30px; }
        .code { font-size: 36px; font-weight: bold; letter-spacing: 0.3em; text-align: center; color: #C8956C; padding: 20px; background: #FAF6F1; margin: 30px 0; }
        .text { color: #51504A; text-align: center; }
        .footer { text-align: center; padding-top: 30px; color: #8A8880; font-size: 12px; border-top: 1px solid #E5E5E5; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">SERAPH</div>
        <p class="text">Enter this code to sign in to your Seraph account:</p>
        <div class="code">{$code}</div>
        <p class="text">This code expires in 10 minutes.</p>
        <p class="text" style="margin-top: 20px; font-size: 14px;">If you didn't request this code, you can safely ignore this email.</p>
        <div class="footer">
            <p>© 2025 Seraph. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

    $altBody = "Your Seraph login code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you didn't request this code, you can safely ignore this email.";
    
    return sendEmail($email, $subject, $body, $altBody);
}

/**
 * Store verification code in session
 */
function storeVerificationCode($email, $code) {
    $_SESSION['verification_email'] = $email;
    $_SESSION['verification_code'] = $code;
    $_SESSION['verification_expires'] = time() + (10 * 60); // 10 minutes
}

/**
 * Verify the code entered by user
 */
function verifyCode($email, $code) {
    if (!isset($_SESSION['verification_code']) || 
        !isset($_SESSION['verification_email']) || 
        !isset($_SESSION['verification_expires'])) {
        return ['success' => false, 'message' => 'No verification in progress'];
    }
    
    if (time() > $_SESSION['verification_expires']) {
        unset($_SESSION['verification_code'], $_SESSION['verification_email'], $_SESSION['verification_expires']);
        return ['success' => false, 'message' => 'Code has expired. Please request a new one.'];
    }
    
    if ($_SESSION['verification_email'] !== $email) {
        return ['success' => false, 'message' => 'Email mismatch'];
    }
    
    if ($_SESSION['verification_code'] !== $code) {
        return ['success' => false, 'message' => 'Invalid code'];
    }
    
    // Code is valid - clear verification session
    unset($_SESSION['verification_code'], $_SESSION['verification_email'], $_SESSION['verification_expires']);
    
    return ['success' => true];
}

/**
 * Start login/signup process - send code
 */
function initiateAuth($email) {
    global $db;
    
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please enter a valid email address'];
    }
    
    $code = generateVerificationCode();
    storeVerificationCode($email, $code);
    
    $result = sendVerificationCode($email, $code);
    
    if ($result['success']) {
        return ['success' => true, 'message' => 'Verification code sent to your email'];
    } else {
        return ['success' => false, 'message' => 'Failed to send verification code. Please try again.'];
    }
}

/**
 * Complete authentication - verify code and login/create account
 */
function completeAuth($email, $code, $name = null) {
    global $db;
    
    $verifyResult = verifyCode($email, $code);
    if (!$verifyResult['success']) {
        return $verifyResult;
    }
    
    // Check if user exists
    $user = $db->findOne('users', ['email' => $email]);
    
    if ($user) {
        // Existing user - log them in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        return ['success' => true, 'message' => 'Welcome back!', 'isNew' => false];
    } else {
        // New user - need name to create account
        if (empty($name)) {
            return ['success' => false, 'needsName' => true, 'message' => 'Please provide your name to complete signup'];
        }
        
        // Create new user
        $newUser = [
            'name' => $name,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $savedUser = $db->insert('users', $newUser);
        
        $_SESSION['user_id'] = $savedUser['id'];
        $_SESSION['user_name'] = $savedUser['name'];
        $_SESSION['user_email'] = $savedUser['email'];
        
        return ['success' => true, 'message' => 'Account created successfully!', 'isNew' => true];
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}

/**
 * Logout user
 */
function logoutUser() {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);

    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        clearRememberToken($_COOKIE['remember_token']);
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }

    return ['success' => true, 'message' => 'Logged out successfully'];
}

/**
 * Generate remember me token
 */
function generateRememberToken($userId) {
    global $db;

    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days

    $tokenData = [
        'user_id' => $userId,
        'token' => hash('sha256', $token),
        'expires_at' => $expires,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db->insert('remember_tokens', $tokenData);

    return $token;
}

/**
 * Set remember me cookie
 */
function setRememberMeCookie($userId) {
    $token = generateRememberToken($userId);
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

/**
 * Clear remember token
 */
function clearRememberToken($token) {
    global $db;
    $hashedToken = hash('sha256', $token);

    $tokens = $db->read('remember_tokens');
    if (is_array($tokens)) {
        $tokens = array_filter($tokens, function($t) use ($hashedToken) {
            return $t['token'] !== $hashedToken;
        });
        $db->write('remember_tokens', array_values($tokens));
    }
}

/**
 * Check and auto-login from remember me cookie
 */
function checkRememberMe() {
    if (isLoggedIn()) {
        return true;
    }

    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }

    global $db;

    $token = $_COOKIE['remember_token'];
    $hashedToken = hash('sha256', $token);

    $tokens = $db->read('remember_tokens');
    if (!is_array($tokens)) {
        return false;
    }

    $validToken = null;
    foreach ($tokens as $t) {
        if ($t['token'] === $hashedToken && strtotime($t['expires_at']) > time()) {
            $validToken = $t;
            break;
        }
    }

    if (!$validToken) {
        // Invalid or expired token - clear cookie
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        return false;
    }

    // Get user and log them in
    $user = $db->findOne('users', ['id' => (int)$validToken['user_id']]);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        return true;
    }

    return false;
}

/**
 * Require login - redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}

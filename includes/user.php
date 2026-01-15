<?php
/**
 * User Profile & Address Management
 */

/**
 * Get user profile
 */
function getUserProfile() {
    global $db;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $userId = $_SESSION['user_id'];
    return $db->findOne('users', ['id' => $userId]);
}

/**
 * Update user profile
 */
function updateUserProfile($data) {
    global $db;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to update profile'];
    }
    
    $userId = $_SESSION['user_id'];
    $allowedFields = ['name', 'phone'];
    $updates = [];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updates[$field] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        return ['success' => false, 'message' => 'No changes made'];
    }
    
    $updated = $db->update('users', ['id' => $userId], $updates);
    
    if ($updated) {
        // Update session
        if (isset($updates['name'])) {
            $_SESSION['user_name'] = $updates['name'];
        }
        return ['success' => true, 'message' => 'Profile updated successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to update profile'];
}

/**
 * Get user addresses
 */
function getUserAddresses() {
    $user = getUserProfile();
    return $user['addresses'] ?? [];
}

/**
 * Get single address
 */
function getAddress($addressId) {
    $addresses = getUserAddresses();
    foreach ($addresses as $addr) {
        if ($addr['id'] === $addressId) {
            return $addr;
        }
    }
    return null;
}

/**
 * Save address (add or update)
 */
function saveAddress($addressData) {
    global $db;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to manage addresses'];
    }
    
    $user = getUserProfile();
    $addresses = $user['addresses'] ?? [];
    $isUpdate = isset($addressData['id']) && !empty($addressData['id']);
    
    // Validate required fields
    $required = ['first_name', 'last_name', 'address_line1', 'city', 'country', 'phone'];
    foreach ($required as $field) {
        if (empty($addressData[$field])) {
            return ['success' => false, 'message' => 'Please fill all required fields'];
        }
    }
    
    if ($isUpdate) {
        // Update existing address
        $found = false;
        foreach ($addresses as &$addr) {
            if ($addr['id'] === $addressData['id']) {
                $addr = array_merge($addr, $addressData);
                $found = true;
                break;
            }
        }
        if (!$found) {
            return ['success' => false, 'message' => 'Address not found'];
        }
    } else {
        // Add new address
        $addressData['id'] = generateAddressId();
        
        // If first address, make it default
        if (empty($addresses)) {
            $addressData['is_default'] = true;
        }
        
        // If new address is set as default, unset others
        if (!empty($addressData['is_default'])) {
            foreach ($addresses as &$addr) {
                $addr['is_default'] = false;
            }
        }
        
        $addresses[] = $addressData;
    }
    
    $db->update('users', ['id' => $user['id']], ['addresses' => $addresses]);
    
    return ['success' => true, 'message' => 'Address saved successfully', 'address' => $addressData];
}

/**
 * Delete address
 */
function deleteAddress($addressId) {
    global $db;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to manage addresses'];
    }
    
    $user = getUserProfile();
    $addresses = $user['addresses'] ?? [];
    
    // Filter out deleted address
    $newAddresses = array_filter($addresses, function($addr) use ($addressId) {
        return $addr['id'] !== $addressId;
    });
    
    if (count($addresses) === count($newAddresses)) {
        return ['success' => false, 'message' => 'Address not found'];
    }
    
    // Re-index array
    $newAddresses = array_values($newAddresses);
    
    $db->update('users', ['id' => $user['id']], ['addresses' => $newAddresses]);
    
    return ['success' => true, 'message' => 'Address deleted successfully'];
}

/**
 * Set default address
 */
function setDefaultAddress($addressId) {
    global $db;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login'];
    }
    
    $user = getUserProfile();
    $addresses = $user['addresses'] ?? [];
    $found = false;
    
    foreach ($addresses as &$addr) {
        if ($addr['id'] === $addressId) {
            $addr['is_default'] = true;
            $found = true;
        } else {
            $addr['is_default'] = false;
        }
    }
    
    if (!$found) {
        return ['success' => false, 'message' => 'Address not found'];
    }
    
    $db->update('users', ['id' => $user['id']], ['addresses' => $addresses]);
    
    return ['success' => true, 'message' => 'Default address updated'];
}

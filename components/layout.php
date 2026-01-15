<?php
/**
 * Main Layout Component
 * Wraps header and footer around page content
 */

function renderLayout($pagePath, $data = []) {
    // Extract data for use in included files
    extract($data);
    
    // Include header
    require_once COMPONENTS_PATH . 'header.php';
    
    // Include the page content
    if (file_exists($pagePath)) {
        require_once $pagePath;
    } else {
        echo '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">';
        echo '<div class="text-center">';
        echo '<h1 class="text-4xl font-bold text-gray-900 mb-4">Page Not Found</h1>';
        echo '<p class="text-gray-600">The requested page could not be found.</p>';
        echo '</div>';
        echo '</div>';
    }
    
    // Include footer
    require_once COMPONENTS_PATH . 'footer.php';
}


<?php
/**
 * Admin Layout Component
 * Modern sidebar layout for admin panel
 */

function renderAdminLayout($pagePath, $data = []) {
    // Check admin authentication
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . 'admin/login');
        exit;
    }

    $admin = getCurrentAdmin();
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Extract data for use in included files
    extract($data);
    $pageTitle = $pageTitle ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($pageTitle); ?> - <?php echo APP_NAME; ?> Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>public/assets/images/favicon.svg">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'admin': {
                            'primary': '#1e293b',
                            'secondary': '#334155',
                            'accent': '#c8a55b',
                            'bg': '#f8fafc',
                            'card': '#ffffff',
                            'border': '#e2e8f0',
                            'text': '#1e293b',
                            'muted': '#64748b'
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(200, 165, 91, 0.1); }
        .sidebar-link.active { background: rgba(200, 165, 91, 0.15); border-right: 3px solid #c8a55b; }
        .card { box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1); }
        .btn-primary { background: #c8a55b; color: white; }
        .btn-primary:hover { background: #b8954b; }
        .btn-secondary { background: #e2e8f0; color: #1e293b; }
        .btn-secondary:hover { background: #cbd5e1; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .table-row:hover { background: #f8fafc; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .status-active, .status-completed, .status-delivered { background: #dcfce7; color: #166534; }
        .status-pending, .status-processing { background: #fef9c3; color: #854d0e; }
        .status-inactive, .status-cancelled, .status-failed { background: #fee2e2; color: #991b1b; }
        .status-shipped { background: #dbeafe; color: #1e40af; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #c8a55b; box-shadow: 0 0 0 3px rgba(200, 165, 91, 0.1); }
        .dropdown-content { display: none; }
        .dropdown-content.show { display: block; }

        /* Mobile sidebar */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
        }

        /* Mobile optimizations */
        @media (max-width: 640px) {
            .mobile-full { width: 100% !important; }
            .mobile-stack { flex-direction: column !important; }
            .mobile-text-sm { font-size: 0.875rem !important; }
            .mobile-p-4 { padding: 1rem !important; }
            .mobile-hide { display: none !important; }
            .mobile-gap-2 { gap: 0.5rem !important; }
        }

        /* Better touch targets */
        @media (max-width: 768px) {
            .btn-primary, .btn-secondary, .btn-danger {
                padding: 0.75rem 1rem;
                min-height: 44px;
            }
            input, select, textarea {
                min-height: 44px;
                font-size: 16px; /* Prevents zoom on iOS */
            }
            .table-row td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-admin-bg">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed lg:static inset-y-0 left-0 z-50 w-64 bg-admin-primary text-white transition-transform duration-300 lg:translate-x-0">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                <div class="w-10 h-10 bg-admin-accent rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">S</span>
                </div>
                <div>
                    <h1 class="font-semibold text-lg"><?php echo APP_NAME; ?></h1>
                    <p class="text-xs text-gray-400">Admin Panel</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <a href="<?php echo BASE_URL; ?>admin"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin') !== false && strpos($currentPath, '/admin/') === false ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="<?php echo BASE_URL; ?>admin/orders"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/orders') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart w-5 text-center"></i>
                        <span>Orders</span>
                    </a>

                    <a href="<?php echo BASE_URL; ?>admin/products"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/products') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-box w-5 text-center"></i>
                        <span>Products</span>
                    </a>

                    <a href="<?php echo BASE_URL; ?>admin/customers"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/customers') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span>Customers</span>
                    </a>

                    <a href="<?php echo BASE_URL; ?>admin/subscribers"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/subscribers') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-envelope w-5 text-center"></i>
                        <span>Subscribers</span>
                    </a>

                    <a href="<?php echo BASE_URL; ?>admin/distributors"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/distributors') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-handshake w-5 text-center"></i>
                        <span>Distributors</span>
                    </a>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Settings</p>
                    <div class="space-y-1">
                        <a href="<?php echo BASE_URL; ?>admin/analytics"
                           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/analytics') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line w-5 text-center"></i>
                            <span>Analytics</span>
                        </a>

                        <a href="<?php echo BASE_URL; ?>admin/settings"
                           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/settings') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-cog w-5 text-center"></i>
                            <span>Settings</span>
                        </a>

                        <a href="<?php echo BASE_URL; ?>admin/admins"
                           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white <?php echo strpos($currentPath, '/admin/admins') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-user-shield w-5 text-center"></i>
                            <span>Admin Users</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Admin Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-admin-secondary rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?php echo h($admin['name'] ?? 'Admin'); ?></p>
                        <p class="text-xs text-gray-400 truncate"><?php echo h($admin['role'] ?? 'Administrator'); ?></p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>admin/logout" class="text-gray-400 hover:text-white" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="bg-white border-b border-admin-border px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-admin-text p-2 hover:bg-gray-100 rounded-lg flex-shrink-0">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg sm:text-xl font-semibold text-admin-text truncate"><?php echo h($pageTitle); ?></h2>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Quick Actions -->
                    <a href="<?php echo BASE_URL; ?>" target="_blank" class="text-admin-muted hover:text-admin-text" title="View Site">
                        <i class="fas fa-external-link-alt"></i>
                    </a>

                    <!-- Notifications -->
                    <div class="relative dropdown">
                        <button class="text-admin-muted hover:text-admin-text relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>

                    <!-- Admin Dropdown -->
                    <div class="relative">
                        <button onclick="toggleProfileDropdown(event)" class="flex items-center gap-2 text-admin-muted hover:text-admin-text">
                            <span class="text-sm hidden sm:inline"><?php echo h($admin['name'] ?? 'Admin'); ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform" id="dropdownArrow"></i>
                        </button>
                        <div id="profileDropdown" class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-admin-border py-2">
                            <a href="<?php echo BASE_URL; ?>admin/profile" class="block px-4 py-2 text-sm text-admin-text hover:bg-gray-50">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/settings" class="block px-4 py-2 text-sm text-admin-text hover:bg-gray-50">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                            <hr class="my-2">
                            <a href="<?php echo BASE_URL; ?>admin/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6">
                <?php
                if (file_exists($pagePath)) {
                    require_once $pagePath;
                } else {
                    echo '<div class="bg-white rounded-lg p-8 text-center">';
                    echo '<h3 class="text-xl font-semibold text-gray-900 mb-2">Page Not Found</h3>';
                    echo '<p class="text-gray-600">The requested admin page could not be found.</p>';
                    echo '</div>';
                }
                ?>
            </main>

            <!-- Footer -->
            <footer class="border-t border-admin-border px-6 py-4 bg-white">
                <p class="text-sm text-admin-muted text-center">
                    <?php echo APP_NAME; ?> Admin Panel &copy; <?php echo date('Y'); ?>
                </p>
            </footer>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

    <!-- Global Scripts -->
    <script>
        const baseUrl = '<?php echo BASE_URL; ?>';
        const csrfToken = '<?php echo generateCsrfToken(); ?>';

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        }

        function toggleProfileDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('profileDropdown');
            const arrow = document.getElementById('dropdownArrow');
            dropdown.classList.toggle('show');
            arrow.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : '';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const arrow = document.getElementById('dropdownArrow');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                arrow.style.transform = '';
            }
        });

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-y-full opacity-0 ${
                type === 'success' ? 'bg-green-600 text-white' :
                type === 'error' ? 'bg-red-600 text-white' :
                'bg-gray-800 text-white'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-full', 'opacity-0');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Confirm dialog
        function confirmAction(message) {
            return confirm(message);
        }

        // Format currency
        function formatCurrency(amount) {
            return '₦' + new Intl.NumberFormat().format(amount);
        }

        // API helper
        async function apiRequest(endpoint, method = 'GET', data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                }
            };

            if (data) {
                data.csrf_token = csrfToken;
                options.body = JSON.stringify(data);
            }

            const response = await fetch(baseUrl + endpoint, options);
            return await response.json();
        }
    </script>
</body>
</html>
<?php
}

/**
 * Render admin login page (no sidebar)
 */
function renderAdminLoginLayout($pagePath, $data = []) {
    extract($data);
    $pageTitle = $pageTitle ?? 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($pageTitle); ?> - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>public/assets/images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'admin': {
                            'primary': '#1e293b',
                            'accent': '#c8a55b',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center p-4">
    <?php
    if (file_exists($pagePath)) {
        require_once $pagePath;
    }
    ?>
</body>
</html>
<?php
}

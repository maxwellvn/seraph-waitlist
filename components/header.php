<?php
$cartCount = function_exists('getCartCount') ? getCartCount() : 0;
$currentCurrency = function_exists('getCurrentCurrency') ? getCurrentCurrency() : 'USD';
$currencyDetails = function_exists('getCurrencyDetails') ? getCurrencyDetails() : ['symbol' => '$', 'region' => 'International', 'flag' => '🇺🇸'];
$allCurrencies = function_exists('getAllCurrencies') ? getAllCurrencies() : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $pageTitle ?? APP_NAME; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>public/assets/images/favicon.svg">

    <!-- SEO Meta Tags -->
    <meta name="description" content="SERAPH - Premium fluoride-free toothpaste made from 100% natural ingredients. Experience purity in every smile with Turmeric, Strawberry, and Peach flavours.">
    <meta name="keywords" content="natural toothpaste, fluoride-free, organic oral care, SERAPH">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'display': ['Playfair Display', 'Georgia', 'serif'],
                        'body': ['DM Sans', 'system-ui', 'sans-serif'],
                        'accent': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'seraph': {
                            'amber': '#C8956C',
                            'amber-dark': '#9A6B42',
                            'amber-light': '#E4C4A8',
                            'cream': '#FAF6F1',
                            'ivory': '#FFFDF9',
                            'charcoal': '#2D2A26',
                            'slate': '#51504A',
                            'mist': '#8A8880',
                        }
                    },
                    letterSpacing: {
                        'ultra-wide': '0.25em',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- GSAP + ScrollTrigger + Lenis -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/custom.css">

    <style>
        /* Base Typography */
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background-color: #FFFDF9;
            color: #2D2A26;
            overflow-x: hidden;
        }

        /* Smooth scroll overrides for Lenis */
        html.lenis, html.lenis body {
            height: auto;
        }

        .lenis.lenis-smooth {
            scroll-behavior: auto !important;
        }

        .lenis.lenis-smooth [data-lenis-prevent] {
            overscroll-behavior: contain;
        }

        .lenis.lenis-stopped {
            overflow: hidden;
        }

        /* Custom selection color */
        ::selection {
            background-color: #C8956C;
            color: #FFFDF9;
        }

        /* Refined scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #FAF6F1;
        }

        ::-webkit-scrollbar-thumb {
            background: #C8956C;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9A6B42;
        }

        /* Header animation prep */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background-color: #C8956C;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Button styles */
        .btn-primary {
            background-color: #C8956C;
            color: #FFFDF9;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #9A6B42;
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid #2D2A26;
            color: #2D2A26;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background-color: #2D2A26;
            color: #FFFDF9;
        }

        /* Reveal animation classes */
        .reveal-up {
            opacity: 0;
            transform: translateY(30px);
        }

        .reveal-left {
            opacity: 0;
            transform: translateX(-30px);
        }

        .reveal-right {
            opacity: 0;
            transform: translateX(30px);
        }

        .reveal-scale {
            opacity: 0;
            transform: scale(0.95);
        }

        /* Stagger children animation prep */
        .stagger-children > * {
            opacity: 0;
            transform: translateY(20px);
        }

        /* Image reveal effect */
        .img-reveal {
            overflow: hidden;
        }

        .img-reveal img {
            transform: scale(1.1);
            transition: transform 0.6s ease;
        }

        .img-reveal:hover img {
            transform: scale(1);
        }

        /* Split text animation prep */
        .char {
            display: inline-block;
            transform: translateY(100%);
            opacity: 0;
        }

        /* Preloader styling */
        .preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #2D2A26;
        }

        .preloader-content {
            text-align: center;
        }

        .preloader-logo {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 400;
            color: #FAF6F1;
            letter-spacing: 0.3em;
            margin-bottom: 2rem;
        }

        .preloader-bar {
            width: 200px;
            height: 2px;
            background-color: rgba(250, 246, 241, 0.2);
            border-radius: 1px;
            overflow: hidden;
            margin: 0 auto;
        }

        .preloader-bar-fill {
            height: 100%;
            width: 0;
            background-color: #C8956C;
        }

        /* Cookie banner refined */
        .cookie-banner {
            backdrop-filter: blur(10px);
            background-color: rgba(45, 42, 38, 0.95);
        }
    </style>

    <?php if (isset($additionalHead)) echo $additionalHead; ?>
</head>
<body class="antialiased">

    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-content">
            <div class="preloader-logo">SERAPH</div>
            <div class="preloader-bar">
                <div class="preloader-bar-fill" id="preloaderBar"></div>
            </div>
        </div>
    </div>

    <!-- Cookie Consent Banner -->
    <div id="cookieConsent" class="hidden cookie-banner fixed bottom-0 left-0 right-0 text-white py-4 px-6 z-50">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm font-body text-seraph-cream/90">
                We use cookies to personalise your experience. By continuing, you agree to our use of cookies.
            </p>
            <div class="flex gap-3">
                <button onclick="acceptCookies()" class="px-5 py-2 text-sm font-medium bg-seraph-amber hover:bg-seraph-amber-dark text-white transition-colors">
                    Accept
                </button>
                <button onclick="declineCookies()" class="px-5 py-2 text-sm font-medium text-seraph-cream/70 hover:text-seraph-cream transition-colors">
                    Decline
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <header class="fixed top-0 left-0 right-0 z-40 transition-all duration-500" id="mainHeader">
        <nav class="bg-seraph-ivory/95 backdrop-blur-sm border-b border-seraph-charcoal/5">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <a href="<?php echo BASE_URL; ?>" class="font-display text-2xl tracking-ultra-wide text-seraph-charcoal hover:text-seraph-amber transition-colors">
                        SERAPH
                    </a>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center gap-10">
                        <a href="<?php echo BASE_URL; ?>" class="nav-link font-body text-sm tracking-wide text-seraph-slate hover:text-seraph-charcoal transition-colors">Home</a>
                        <a href="<?php echo BASE_URL; ?>products" class="nav-link font-body text-sm tracking-wide text-seraph-slate hover:text-seraph-charcoal transition-colors">Shop</a>
                        <a href="<?php echo BASE_URL; ?>#about" class="nav-link font-body text-sm tracking-wide text-seraph-slate hover:text-seraph-charcoal transition-colors">About</a>
                        <a href="<?php echo BASE_URL; ?>#faq" class="nav-link font-body text-sm tracking-wide text-seraph-slate hover:text-seraph-charcoal transition-colors">FAQ</a>
                        <a href="<?php echo BASE_URL; ?>distributors" class="nav-link font-body text-sm tracking-wide text-seraph-slate hover:text-seraph-charcoal transition-colors">Distributors</a>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="hidden md:flex items-center gap-6">
                        <!-- Currency Selector -->
                        <div class="relative" id="currencyDropdown">
                            <button type="button" onclick="toggleCurrencyDropdown()" class="flex items-center gap-2 font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                                <span><?php echo $currencyDetails['flag']; ?></span>
                                <span><?php echo $currentCurrency; ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="currencyDropdownMenu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white border border-seraph-charcoal/10 rounded-lg shadow-lg z-50 py-2">
                                <?php foreach ($allCurrencies as $code => $curr): ?>
                                <button type="button" onclick="setCurrency('<?php echo $code; ?>')"
                                        class="w-full px-4 py-2 text-left font-body text-sm hover:bg-seraph-cream transition-colors flex items-center gap-3 <?php echo $code === $currentCurrency ? 'bg-seraph-cream/50 text-seraph-amber' : 'text-seraph-slate'; ?>">
                                    <span><?php echo $curr['flag']; ?></span>
                                    <span class="flex-1"><?php echo $curr['region']; ?></span>
                                    <span class="text-seraph-mist"><?php echo $curr['symbol']; ?></span>
                                </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="w-px h-6 bg-seraph-charcoal/10"></div>

                        <!-- Cart Link -->
                        <?php if (empty($hideHeaderCart)): ?>
                        <a href="<?php echo BASE_URL; ?>cart" class="relative group text-seraph-slate hover:text-seraph-amber transition-colors">
                            <span class="sr-only">Cart</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span id="headerCartBadge" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-seraph-amber text-white text-[10px] font-bold rounded-full flex items-center justify-center <?php echo $cartCount > 0 ? '' : 'hidden'; ?>">
                                <?php echo $cartCount; ?>
                            </span>
                        </a>

                        <div class="w-px h-6 bg-seraph-charcoal/10"></div>
                        <?php endif; ?>

                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo BASE_URL; ?>account" class="font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                                My Account
                            </a>
                            <a href="<?php echo BASE_URL; ?>orders" class="font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                                Orders
                            </a>
                            <a href="<?php echo BASE_URL; ?>logout" class="btn-outline px-5 py-2.5 text-sm font-medium tracking-wide">
                                Logout
                            </a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>login" class="btn-primary px-5 py-2.5 text-sm font-medium tracking-wide">
                                Sign In
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile Actions -->
                    <div class="md:hidden flex items-center gap-2">
                        <?php if (empty($hideHeaderCart)): ?>
                        <a href="<?php echo BASE_URL; ?>cart" class="relative w-10 h-10 flex items-center justify-center text-seraph-slate hover:text-seraph-amber transition-colors">
                            <span class="sr-only">Cart</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span id="mobileCartBadge" class="absolute top-0.5 right-0.5 w-5 h-5 bg-seraph-amber text-white text-[10px] font-bold rounded-full flex items-center justify-center <?php echo $cartCount > 0 ? '' : 'hidden'; ?>">
                                <?php echo $cartCount; ?>
                            </span>
                        </a>
                        <?php endif; ?>

                        <!-- Mobile Menu Toggle -->
                        <button id="mobileMenuBtn" class="w-10 h-10 flex flex-col justify-center items-center gap-1.5">
                            <span class="hamburger-line w-6 h-0.5 bg-seraph-charcoal transition-all"></span>
                            <span class="hamburger-line w-6 h-0.5 bg-seraph-charcoal transition-all"></span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden hidden bg-seraph-ivory border-b border-seraph-charcoal/5">
            <div class="px-6 py-8 flex flex-col gap-6">
                <a href="<?php echo BASE_URL; ?>" class="font-body text-lg text-seraph-charcoal">Home</a>
                <a href="<?php echo BASE_URL; ?>products" class="font-body text-lg text-seraph-charcoal">Shop</a>
                <a href="<?php echo BASE_URL; ?>#about" class="font-body text-lg text-seraph-charcoal">About</a>
                <a href="<?php echo BASE_URL; ?>#faq" class="font-body text-lg text-seraph-charcoal">FAQ</a>
                <a href="<?php echo BASE_URL; ?>distributors" class="font-body text-lg text-seraph-charcoal">Distributors</a>

                <!-- Mobile Currency Selector -->
                <div class="border-t border-seraph-charcoal/10 pt-6 mt-2">
                    <p class="font-body text-sm text-seraph-slate mb-3">Select Region</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($allCurrencies as $code => $curr): ?>
                        <button type="button" onclick="setCurrency('<?php echo $code; ?>')"
                                class="flex items-center gap-2 px-4 py-2 rounded-full border text-sm font-body transition-colors <?php echo $code === $currentCurrency ? 'border-seraph-amber bg-seraph-amber/10 text-seraph-amber' : 'border-seraph-charcoal/20 text-seraph-slate hover:border-seraph-amber'; ?>">
                            <span><?php echo $curr['flag']; ?></span>
                            <span><?php echo $curr['symbol']; ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="border-t border-seraph-charcoal/10 pt-6 mt-2">
                    <?php if (isLoggedIn()): ?>
                        <p class="font-body text-sm text-seraph-slate mb-4">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        <a href="<?php echo BASE_URL; ?>account" class="block font-body text-lg text-seraph-charcoal mb-3">My Account</a>
                        <a href="<?php echo BASE_URL; ?>orders" class="block font-body text-lg text-seraph-charcoal mb-4">Orders</a>
                        <a href="<?php echo BASE_URL; ?>logout" class="btn-outline inline-block text-center px-6 py-3 text-sm font-medium">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login" class="btn-primary inline-block text-center px-6 py-3 text-sm font-medium w-full">
                            Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Spacer for fixed header -->
    <div class="h-20"></div>

    <!-- Main Content Container -->
    <main>


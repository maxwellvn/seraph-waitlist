    </main>

    <!-- Footer -->
    <footer class="bg-seraph-charcoal text-seraph-cream mt-auto">
        <!-- Main Footer Content -->
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">
                
                <!-- Brand Column -->
                <div class="lg:col-span-5">
                    <a href="<?php echo BASE_URL; ?>" class="font-display text-3xl tracking-ultra-wide text-seraph-cream inline-block mb-6">
                        SERAPH
                    </a>
                    <p class="font-body text-seraph-cream/70 text-base leading-relaxed mb-6 max-w-sm">
                        Premium fluoride-free toothpaste crafted with 100% natural ingredients. 
                        Experience the purity of nature in every brush.
                    </p>
                    <p class="font-display italic text-seraph-amber text-lg">
                        "Purity in every smile"
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="lg:col-span-3">
                    <h4 class="font-body text-sm font-semibold tracking-wider uppercase text-seraph-cream mb-6">
                        Navigation
                    </h4>
                    <nav class="flex flex-col gap-4">
                        <a href="<?php echo BASE_URL; ?>" class="font-body text-seraph-cream/70 hover:text-seraph-amber transition-colors">Home</a>
                        <a href="<?php echo BASE_URL; ?>products" class="font-body text-seraph-cream/70 hover:text-seraph-amber transition-colors">Shop</a>
                        <a href="<?php echo BASE_URL; ?>#about" class="font-body text-seraph-cream/70 hover:text-seraph-amber transition-colors">About SERAPH</a>
                        <a href="<?php echo BASE_URL; ?>#faq" class="font-body text-seraph-cream/70 hover:text-seraph-amber transition-colors">FAQ</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo BASE_URL; ?>logout" class="font-body text-seraph-cream/70 hover:text-seraph-amber transition-colors">Logout</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>login" class="font-body text-seraph-cream/70 hover:text-seraph-amber transition-colors">Login / Register</a>
                        <?php endif; ?>
                    </nav>
                </div>

                <!-- Our Promise -->
                <div class="lg:col-span-4">
                    <h4 class="font-body text-sm font-semibold tracking-wider uppercase text-seraph-cream mb-6">
                        Our Promise
                    </h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 bg-seraph-amber rounded-full mt-2 flex-shrink-0"></span>
                            <span class="font-body text-seraph-cream/70">100% Natural Ingredients</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 bg-seraph-amber rounded-full mt-2 flex-shrink-0"></span>
                            <span class="font-body text-seraph-cream/70">Fluoride-Free Formula</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 bg-seraph-amber rounded-full mt-2 flex-shrink-0"></span>
                            <span class="font-body text-seraph-cream/70">Three Unique Flavours</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 bg-seraph-amber rounded-full mt-2 flex-shrink-0"></span>
                            <span class="font-body text-seraph-cream/70">Safe for the Whole Family</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-seraph-cream/10">
            <div class="max-w-7xl mx-auto px-6 lg:px-12 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="font-body text-sm text-seraph-cream/50">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                    </p>
                    <div class="flex items-center gap-6">
                        <a href="mailto:info@seraph-oral.org" class="font-body text-sm text-seraph-cream/50 hover:text-seraph-amber transition-colors">
                            info@seraph-oral.org
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Currency Selector Script -->
    <script>
        const currencyBaseUrl = '<?php echo BASE_URL; ?>';

        function toggleCurrencyDropdown() {
            const menu = document.getElementById('currencyDropdownMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('currencyDropdown');
            const menu = document.getElementById('currencyDropdownMenu');
            if (dropdown && menu && !dropdown.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        async function setCurrency(code) {
            try {
                const response = await fetch(currencyBaseUrl + 'api/currency/set', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ currency: code })
                });

                const data = await response.json();

                if (data.success) {
                    // Reload page to update all prices
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error setting currency:', error);
            }
        }
    </script>

    <!-- Mobile Menu Script -->
    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenuBtn.classList.toggle('active');
            });

            // Close mobile menu when clicking links
            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                    mobileMenuBtn.classList.remove('active');
                });
            });
        }
    </script>

    <!-- Lenis Smooth Scroll Init -->
    <script>
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            orientation: 'vertical',
            gestureOrientation: 'vertical',
            smoothWheel: true,
            wheelMultiplier: 1,
            touchMultiplier: 2,
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }

        requestAnimationFrame(raf);

        // Connect Lenis to GSAP ScrollTrigger
        lenis.on('scroll', ScrollTrigger.update);

        gsap.ticker.add((time) => {
            lenis.raf(time * 1000);
        });

        gsap.ticker.lagSmoothing(0);
    </script>

    <!-- GSAP Animations -->
    <script>
        // Wait for DOM
        document.addEventListener('DOMContentLoaded', function() {
            
            // Register ScrollTrigger
            gsap.registerPlugin(ScrollTrigger);

            // Header scroll effect
            const header = document.getElementById('mainHeader');
            let lastScrollY = 0;

            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                
                if (currentScrollY > 100) {
                    header.classList.add('shadow-lg');
                } else {
                    header.classList.remove('shadow-lg');
                }
                
                lastScrollY = currentScrollY;
            });

            // Reveal animations for elements with reveal classes
            gsap.utils.toArray('.reveal-up').forEach((elem) => {
                gsap.to(elem, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: elem,
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                });
            });

            gsap.utils.toArray('.reveal-left').forEach((elem) => {
                gsap.to(elem, {
                    opacity: 1,
                    x: 0,
                    duration: 0.8,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: elem,
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                });
            });

            gsap.utils.toArray('.reveal-right').forEach((elem) => {
                gsap.to(elem, {
                    opacity: 1,
                    x: 0,
                    duration: 0.8,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: elem,
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                });
            });

            gsap.utils.toArray('.reveal-scale').forEach((elem) => {
                gsap.to(elem, {
                    opacity: 1,
                    scale: 1,
                    duration: 0.8,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: elem,
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                });
            });

            // Stagger children animations
            gsap.utils.toArray('.stagger-children').forEach((container) => {
                const children = container.children;
                gsap.to(children, {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    stagger: 0.15,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: container,
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                });
            });

            // Image parallax effect
            gsap.utils.toArray('.parallax-img').forEach((img) => {
                gsap.to(img, {
                    yPercent: -15,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: img.parentElement,
                        start: 'top bottom',
                        end: 'bottom top',
                        scrub: true
                    }
                });
            });

        });
    </script>

    <!-- Preloader Script -->
    <script>
        let progress = 0;
        const preloader = document.getElementById('preloader');
        const preloaderBar = document.getElementById('preloaderBar');
        const images = document.querySelectorAll('img');
        let loadedImages = 0;
        const totalImages = images.length || 1;

        // Progress animation
        function updateProgress(value) {
            progress = Math.min(value, 100);
            if (preloaderBar) {
                preloaderBar.style.width = progress + '%';
                preloaderBar.style.transition = 'width 0.3s ease';
            }
        }

        // Track image loading
        images.forEach((img) => {
            if (img.complete) {
                loadedImages++;
                updateProgress((loadedImages / totalImages) * 100);
            } else {
                img.addEventListener('load', () => {
                    loadedImages++;
                    updateProgress((loadedImages / totalImages) * 100);
                });
                img.addEventListener('error', () => {
                    loadedImages++;
                    updateProgress((loadedImages / totalImages) * 100);
                });
            }
        });

        // Hide preloader
        function hidePreloader() {
            if (preloader) {
                updateProgress(100);
                setTimeout(() => {
                    gsap.to(preloader, {
                        opacity: 0,
                        duration: 0.6,
                        ease: 'power2.out',
                        onComplete: () => {
                            preloader.style.display = 'none';
                            document.body.style.overflow = '';
                            
                            // Trigger hero animations after preloader
                            gsap.to('.hero-title', {
                                opacity: 1,
                                y: 0,
                                duration: 1,
                                ease: 'power3.out',
                                delay: 0.2
                            });
                            gsap.to('.hero-subtitle', {
                                opacity: 1,
                                y: 0,
                                duration: 1,
                                ease: 'power3.out',
                                delay: 0.4
                            });
                            gsap.to('.hero-cta', {
                                opacity: 1,
                                y: 0,
                                duration: 1,
                                ease: 'power3.out',
                                delay: 0.6
                            });
                            gsap.to('.hero-image', {
                                opacity: 1,
                                scale: 1,
                                duration: 1.2,
                                ease: 'power3.out',
                                delay: 0.3
                            });
                        }
                    });
                }, 400);
            }
        }

        // Check when everything is loaded
        window.addEventListener('load', () => {
            hidePreloader();
        });

        // Fallback - hide after max 5 seconds
        setTimeout(hidePreloader, 5000);
    </script>

    <!-- Cookie Consent Script -->
    <script>
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
        }

        function getCookie(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        function checkCookieConsent() {
            const consent = getCookie('seraph_cookie_consent');
            if (!consent) {
                setTimeout(() => {
                    document.getElementById('cookieConsent').classList.remove('hidden');
                }, 2000);
            }
        }

        function acceptCookies() {
            setCookie('seraph_cookie_consent', 'accepted', 365);
            gsap.to('#cookieConsent', {
                y: 100,
                opacity: 0,
                duration: 0.4,
                ease: 'power2.in',
                onComplete: () => {
                    document.getElementById('cookieConsent').classList.add('hidden');
                }
            });
        }

        function declineCookies() {
            setCookie('seraph_cookie_consent', 'declined', 365);
            gsap.to('#cookieConsent', {
                y: 100,
                opacity: 0,
                duration: 0.4,
                ease: 'power2.in',
                onComplete: () => {
                    document.getElementById('cookieConsent').classList.add('hidden');
                }
            });
        }

        window.addEventListener('load', checkCookieConsent);
    </script>

    <?php if (isset($additionalScripts)) echo $additionalScripts; ?>
</body>
</html>


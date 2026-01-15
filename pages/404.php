<!-- 404 Page -->
<div class="min-h-[60vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold font-playfair text-gray-900">404</h1>
        </div>
        
        <h2 class="text-4xl font-bold font-poppins text-gray-900 mb-4">
            Page Not Found
        </h2>
        
        <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto font-inter">
            Sorry, we couldn't find the page you're looking for. It might have been moved or deleted.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a 
                href="<?php echo BASE_URL; ?>" 
                class="inline-block bg-gray-900 text-white px-8 py-3 font-poppins font-medium hover:bg-gray-800 transition"
            >
                Go Home
            </a>
            <a 
                href="<?php echo BASE_URL; ?>contact" 
                class="inline-block border-2 border-gray-900 text-gray-900 px-8 py-3 font-poppins font-medium hover:bg-gray-50 transition"
            >
                Contact Support
            </a>
        </div>

        <!-- Decorative Element -->
        <div class="mt-12">
            <svg class="mx-auto h-64 w-64 text-gray-100" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
        </div>
    </div>
</div>


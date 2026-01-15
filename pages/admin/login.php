<?php
// $error and $email are passed from router
$error = $error ?? '';
$email = $email ?? '';
?>

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-admin-accent rounded-xl mb-4">
            <span class="text-white font-bold text-2xl">S</span>
        </div>
        <h1 class="text-2xl font-bold text-white"><?php echo APP_NAME; ?> Admin</h1>
        <p class="text-gray-400 mt-2">Sign in to your admin account</p>
    </div>

    <!-- Login Form -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo h($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" id="email" name="email" required
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-admin-accent focus:border-admin-accent"
                           placeholder="admin@example.com"
                           value="<?php echo h($email); ?>">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" id="password" name="password" required
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-admin-accent focus:border-admin-accent"
                           placeholder="Enter your password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" class="w-4 h-4 text-admin-accent border-gray-300 rounded focus:ring-admin-accent">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-admin-accent hover:bg-amber-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                Sign In
            </button>
        </form>
    </div>

    <!-- Back to Site -->
    <p class="text-center mt-6">
        <a href="<?php echo BASE_URL; ?>" class="text-gray-400 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Back to Site
        </a>
    </p>
</div>

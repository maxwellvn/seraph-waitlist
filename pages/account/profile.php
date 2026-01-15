<section class="py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-6xl mx-auto px-6 lg:px-12">
        
        <div class="grid lg:grid-cols-4 gap-12">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-2">
                    <a href="<?php echo BASE_URL; ?>account" class="block px-4 py-2 font-body text-seraph-charcoal bg-seraph-amber/10 border-l-2 border-seraph-amber">
                        Profile
                    </a>
                    <a href="<?php echo BASE_URL; ?>account/addresses" class="block px-4 py-2 font-body text-seraph-slate hover:text-seraph-charcoal hover:bg-seraph-cream transition-colors">
                        Addresses
                    </a>
                    <a href="<?php echo BASE_URL; ?>orders" class="block px-4 py-2 font-body text-seraph-slate hover:text-seraph-charcoal hover:bg-seraph-cream transition-colors">
                        Orders
                    </a>
                    <a href="<?php echo BASE_URL; ?>logout" class="block px-4 py-2 font-body text-red-600 hover:bg-red-50 transition-colors mt-8">
                        Logout
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="mb-8">
                    <h1 class="font-display text-3xl text-seraph-charcoal">Profile Settings</h1>
                </div>
                
                <!-- Personal Info -->
                <div class="bg-white border border-seraph-charcoal/5 p-8 mb-8">
                    <h2 class="font-display text-xl text-seraph-charcoal mb-6">Personal Information</h2>
                    
                    <form id="profileForm" class="space-y-6">
                        <?php echo csrfField(); ?>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block font-body text-sm font-medium text-seraph-charcoal mb-2">Full Name</label>
                                <input type="text" id="name" name="name" value="<?php echo h($user['name']); ?>" required
                                       class="w-full px-4 py-3 bg-seraph-cream/30 border border-seraph-charcoal/10 focus:border-seraph-amber focus:outline-none transition-colors">
                            </div>
                            
                            <?php
                            $countries = require_once BASE_PATH . 'config/countries.php';
                            
                            $fullPhone = $user['phone'] ?? '';
                            $defaultCode = '+234';
                            $localPhone = '';
                            
                            // Sort countries by dial code length desc to match +1-xxx before +1
                            usort($countries, function($a, $b) {
                                return strlen($b['dial_code']) - strlen($a['dial_code']);
                            });
                            
                            foreach ($countries as $country) {
                                $code = $country['dial_code'];
                                if (strpos($fullPhone, $code) === 0) {
                                    $defaultCode = $code;
                                    $localPhone = substr($fullPhone, strlen($code));
                                    break;
                                }
                            }
                            if (empty($localPhone) && !empty($fullPhone)) $localPhone = $fullPhone; 
                            
                            // Re-sort alphabetically for display
                            usort($countries, function($a, $b) {
                                return strcmp($a['name'], $b['name']);
                            });
                            ?>
                            
                            <div>
                                <label for="email" class="block font-body text-sm font-medium text-seraph-charcoal mb-2">Email Address</label>
                                <input type="email" id="email" value="<?php echo h($user['email']); ?>" disabled
                                       class="w-full px-4 py-3 bg-seraph-cream/10 border border-seraph-charcoal/5 text-seraph-slate cursor-not-allowed">
                                <p class="text-xs text-seraph-mist mt-1">Email cannot be changed</p>
                            </div>
                            
                            <div>
                                <label for="phone" class="block font-body text-sm font-medium text-seraph-charcoal mb-2">Phone Number</label>
                                <div class="flex gap-2">
                                    <select name="country_code" class="w-24 px-3 py-3 bg-seraph-cream/30 border border-seraph-charcoal/10 focus:border-seraph-amber focus:outline-none transition-colors">
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?php echo $country['dial_code']; ?>" <?php echo $defaultCode === $country['dial_code'] ? 'selected' : ''; ?>>
                                                <?php echo $country['dial_code']; ?> (<?php echo $country['code']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="tel" id="local_phone" name="local_phone" value="<?php echo h($localPhone); ?>" placeholder="8012345678"
                                           class="flex-1 px-4 py-3 bg-seraph-cream/30 border border-seraph-charcoal/10 focus:border-seraph-amber focus:outline-none transition-colors">
                                </div>
                            </div>
                        </div>
                        
                        <div id="message" class="hidden p-4 rounded text-sm font-body"></div>
                        
                        <button type="submit" id="saveBtn" class="btn-primary px-8 py-3 font-medium tracking-wide">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</section>

<script>
    const baseUrl = '<?php echo BASE_URL; ?>';
    
    document.getElementById('profileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('saveBtn');
        const msg = document.getElementById('message');
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        // Combine phone
        if (data.country_code && data.local_phone) {
            data.phone = data.country_code + data.local_phone;
        }
        delete data.country_code;
        delete data.local_phone;
        
        btn.innerText = 'Saving...';
        btn.disabled = true;
        msg.classList.add('hidden');
        
        try {
            const response = await fetch(baseUrl + 'api/profile/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            msg.innerText = result.message;
            msg.className = result.success 
                ? 'p-4 rounded text-sm font-body bg-green-50 text-green-700 block' 
                : 'p-4 rounded text-sm font-body bg-red-50 text-red-700 block';
                
        } catch (error) {
            msg.innerText = 'Network error. Please try again.';
            msg.className = 'p-4 rounded text-sm font-body bg-red-50 text-red-700 block';
        } finally {
            btn.innerText = 'Save Changes';
            btn.disabled = false;
        }
    });
</script>

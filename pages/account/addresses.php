<?php
$addresses = getUserAddresses();
$countries = require_once BASE_PATH . 'config/countries.php';

// Sort alphabetically
usort($countries, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});
?>

<section class="py-32 bg-seraph-ivory min-h-screen">
    <div class="max-w-6xl mx-auto px-6 lg:px-12">
        
        <div class="grid lg:grid-cols-4 gap-12">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-2">
                    <a href="<?php echo BASE_URL; ?>account" class="block px-4 py-2 font-body text-seraph-slate hover:text-seraph-charcoal hover:bg-seraph-cream transition-colors">
                        Profile
                    </a>
                    <a href="<?php echo BASE_URL; ?>account/addresses" class="block px-4 py-2 font-body text-seraph-charcoal bg-seraph-amber/10 border-l-2 border-seraph-amber">
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
                <div class="flex justify-between items-center mb-8">
                    <h1 class="font-display text-3xl text-seraph-charcoal">Addresses</h1>
                    <button onclick="openModal()" class="btn-outline px-6 py-2 text-sm font-medium">
                        + Add New
                    </button>
                </div>
                
                <?php if (empty($addresses)): ?>
                    <div class="text-center py-12 bg-white border border-seraph-charcoal/5">
                        <p class="text-seraph-slate mb-4">No addresses saved yet.</p>
                        <button onclick="openModal()" class="text-seraph-amber font-medium">Add your first address</button>
                    </div>
                <?php else: ?>
                    <div class="grid md:grid-cols-2 gap-6">
                        <?php foreach ($addresses as $addr): ?>
                            <div class="bg-white border <?php echo !empty($addr['is_default']) ? 'border-seraph-amber' : 'border-seraph-charcoal/5'; ?> p-6 relative group">
                                <?php if (!empty($addr['is_default'])): ?>
                                    <span class="absolute top-4 right-4 text-xs font-medium text-seraph-amber bg-seraph-amber/10 px-2 py-1 rounded">Default</span>
                                <?php endif; ?>
                                
                                <h3 class="font-display text-lg text-seraph-charcoal mb-2">
                                    <?php echo h($addr['first_name'] . ' ' . $addr['last_name']); ?>
                                </h3>
                                <p class="font-body text-seraph-slate text-sm mb-1"><?php echo h($addr['address_line1']); ?></p>
                                <p class="font-body text-seraph-slate text-sm mb-1">
                                    <?php echo h($addr['city']); ?>, <?php echo h($addr['country']); ?>
                                </p>
                                <p class="font-body text-seraph-slate text-sm mb-4"><?php echo h($addr['phone']); ?></p>
                                
                                <div class="flex gap-4 border-t border-seraph-charcoal/5 pt-4">
                                    <button onclick='editAddress(<?php echo json_encode($addr); ?>)' class="text-seraph-slate hover:text-seraph-charcoal text-sm font-medium">Edit</button>
                                    <button onclick="deleteAddress('<?php echo $addr['id']; ?>')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                    <?php if (empty($addr['is_default'])): ?>
                                        <button onclick="setDefault('<?php echo $addr['id']; ?>')" class="text-seraph-amber hover:text-seraph-amber-dark text-sm font-medium ml-auto">Set Default</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</section>

<!-- Address Modal -->
<div id="addressModal" class="fixed inset-0 bg-seraph-charcoal/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white max-w-lg w-full p-8 rounded shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-display text-xl text-seraph-charcoal" id="modalTitle">Add New Address</h2>
            <button onclick="closeModal()" class="text-seraph-slate hover:text-seraph-charcoal">✕</button>
        </div>
        
        <form id="addressForm" class="space-y-4">
            <?php echo csrfField(); ?>
            <input type="hidden" name="id" id="addrId">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-seraph-charcoal mb-1">First Name</label>
                    <input type="text" name="first_name" id="firstName" required class="w-full px-3 py-2 border border-seraph-charcoal/10 focus:border-seraph-amber outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-seraph-charcoal mb-1">Last Name</label>
                    <input type="text" name="last_name" id="lastName" required class="w-full px-3 py-2 border border-seraph-charcoal/10 focus:border-seraph-amber outline-none">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-seraph-charcoal mb-1">Address</label>
                <input type="text" name="address_line1" id="addressLine1" required class="w-full px-3 py-2 border border-seraph-charcoal/10 focus:border-seraph-amber outline-none">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-seraph-charcoal mb-1">City</label>
                    <input type="text" name="city" id="city" required class="w-full px-3 py-2 border border-seraph-charcoal/10 focus:border-seraph-amber outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-seraph-charcoal mb-1">Country</label>
                    <select name="country" id="country" required class="w-full px-3 py-2 border border-seraph-charcoal/10 focus:border-seraph-amber outline-none bg-white">
                        <option value="">Select Country</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo htmlspecialchars($country['name']); ?>"><?php echo htmlspecialchars($country['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-seraph-charcoal mb-1">Phone</label>
                <div class="flex gap-2">
                    <select name="country_code" id="country_code" class="w-24 px-3 py-2 bg-white border border-seraph-charcoal/10 focus:border-seraph-amber outline-none">
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo $country['dial_code']; ?>" <?php echo $country['code'] === 'NG' ? 'selected' : ''; ?>>
                                <?php echo $country['dial_code']; ?> (<?php echo $country['code']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="tel" name="local_phone" id="local_phone" required class="flex-1 px-3 py-2 border border-seraph-charcoal/10 focus:border-seraph-amber outline-none">
                </div>
            </div>
            
            <button type="submit" class="w-full btn-primary py-3 mt-4">Save Address</button>
        </form>
    </div>
</div>

<script>
    const baseUrl = '<?php echo BASE_URL; ?>';
    const modal = document.getElementById('addressModal');
    
    function openModal() {
        document.getElementById('addressForm').reset();
        document.getElementById('modalTitle').innerText = 'Add New Address';
        document.getElementById('addrId').value = '';
        modal.classList.remove('hidden');
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    function editAddress(addr) {
        document.getElementById('modalTitle').innerText = 'Edit Address';
        document.getElementById('addrId').value = addr.id;
        document.getElementById('firstName').value = addr.first_name;
        document.getElementById('lastName').value = addr.last_name;
        document.getElementById('addressLine1').value = addr.address_line1;
        document.getElementById('city').value = addr.city;
        document.getElementById('country').value = addr.country;
        
        // Handle phone split
        let phone = addr.phone || '';
        let code = '+234';
        let local = phone;
        
        <?php
        // Create array of codes for JS, sorted by length desc
        $jsCodes = array_map(function($c) { return $c['dial_code']; }, $countries);
        $jsCodes = array_unique($jsCodes);
        usort($jsCodes, function($a, $b) { return strlen($b) - strlen($a); });
        ?>
        const codes = <?php echo json_encode(array_values($jsCodes)); ?>;
        
        for (let c of codes) {
            if (phone.startsWith(c)) {
                code = c;
                local = phone.substring(c.length);
                break;
            }
        }
        
        document.getElementById('country_code').value = code;
        document.getElementById('local_phone').value = local;
        
        modal.classList.remove('hidden');
    }
    
    document.getElementById('addressForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        
        // Combine phone
        if (data.country_code && data.local_phone) {
            data.phone = data.country_code + data.local_phone;
        }
        delete data.country_code;
        delete data.local_phone;
        
        try {
            const res = await fetch(baseUrl + 'api/address/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) location.reload();
            else alert(result.message);
        } catch (err) {
            alert('Error saving address');
        }
    });
    
    async function deleteAddress(id) {
        if (!confirm('Area you sure?')) return;
        try {
            const res = await fetch(baseUrl + 'api/address/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, csrf_token: '<?php echo generateCsrfToken(); ?>' })
            });
            if ((await res.json()).success) location.reload();
        } catch (err) {}
    }
    
    async function setDefault(id) {
        try {
            const res = await fetch(baseUrl + 'api/address/set-default', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, csrf_token: '<?php echo generateCsrfToken(); ?>' })
            });
            if ((await res.json()).success) location.reload();
        } catch (err) {}
    }
</script>

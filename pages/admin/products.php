<?php
$products = getAllProducts();
$action = $_GET['action'] ?? '';
$editProduct = null;

if (isset($_GET['edit'])) {
    $editProduct = getProduct($_GET['edit']);
}
?>

<?php if ($action === 'new' || $editProduct): ?>
<!-- Product Form -->
<div class="max-w-3xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="<?php echo BASE_URL; ?>admin/products" class="text-admin-muted hover:text-admin-text">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-xl font-semibold text-admin-text">
            <?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?>
        </h2>
    </div>

    <form id="productForm" class="card bg-white rounded-xl">
        <input type="hidden" name="id" value="<?php echo $editProduct['id'] ?? ''; ?>">

        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label class="block text-sm font-medium text-admin-text mb-2">Product Name *</label>
                    <input type="text" name="name" required
                           value="<?php echo h($editProduct['name'] ?? ''); ?>"
                           class="w-full border border-admin-border rounded-lg px-4 py-3"
                           placeholder="e.g., Seraph Turmeric">
                </div>

                <div>
                    <label class="block text-sm font-medium text-admin-text mb-2">Flavour</label>
                    <input type="text" name="flavour"
                           value="<?php echo h($editProduct['flavour'] ?? ''); ?>"
                           class="w-full border border-admin-border rounded-lg px-4 py-3"
                           placeholder="e.g., Turmeric">
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="bg-admin-bg rounded-lg p-4">
                <label class="block text-sm font-semibold text-admin-text mb-3">
                    <i class="fas fa-money-bill-wave mr-2"></i>Pricing (All Currencies) *
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-admin-muted mb-1">Nigeria (NGN)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">₦</span>
                            <input type="number" name="price_ngn" required min="0" step="1"
                                   value="<?php echo $editProduct['prices']['NGN'] ?? $editProduct['price'] ?? ''; ?>"
                                   class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-3"
                                   placeholder="2500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-admin-muted mb-1">UK & Europe (GBP)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">£</span>
                            <input type="number" name="price_gbp" required min="0" step="0.01"
                                   value="<?php echo $editProduct['prices']['GBP'] ?? ''; ?>"
                                   class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-3"
                                   placeholder="3.50">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-admin-muted mb-1">International (USD)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-admin-muted">$</span>
                            <input type="number" name="price_usd" required min="0" step="0.01"
                                   value="<?php echo $editProduct['prices']['USD'] ?? ''; ?>"
                                   class="w-full border border-admin-border rounded-lg pl-8 pr-4 py-3"
                                   placeholder="4.40">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label class="block text-sm font-medium text-admin-text mb-2">Stock Quantity</label>
                    <input type="number" name="stock" min="0"
                           value="<?php echo $editProduct['stock'] ?? 100; ?>"
                           class="w-full border border-admin-border rounded-lg px-4 py-3"
                           placeholder="100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-admin-text mb-2">Status</label>
                    <select name="status" class="w-full border border-admin-border rounded-lg px-4 py-3">
                        <option value="active" <?php echo ($editProduct['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($editProduct['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="out_of_stock" <?php echo ($editProduct['status'] ?? '') === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-text mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-admin-border rounded-lg px-4 py-3"
                          placeholder="Product description..."><?php echo h($editProduct['description'] ?? ''); ?></textarea>
            </div>

            <!-- Main Image -->
            <div>
                <label class="block text-sm font-medium text-admin-text mb-2">Main Image (Product Listing)</label>
                <input type="hidden" name="image" id="mainImageFilename" value="<?php echo h($editProduct['image'] ?? ''); ?>">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-shrink-0">
                        <div id="mainImagePreview" class="w-28 h-28 bg-admin-bg rounded-lg border-2 border-dashed border-admin-border flex items-center justify-center overflow-hidden">
                            <?php if (!empty($editProduct['image'])): ?>
                                <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo h($editProduct['image']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="text-center text-admin-muted"><i class="fas fa-image text-xl"></i><p class="text-xs mt-1">Main</p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="border-2 border-dashed border-admin-border rounded-lg p-3 text-center hover:border-admin-accent transition-colors cursor-pointer" onclick="document.getElementById('mainImageUpload').click()">
                            <input type="file" id="mainImageUpload" accept="image/jpeg,image/png,image/webp" class="hidden" data-target="main">
                            <i class="fas fa-cloud-upload-alt text-xl text-admin-muted"></i>
                            <p class="text-xs text-admin-muted mt-1">Click to upload main image</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hover Image -->
            <div>
                <label class="block text-sm font-medium text-admin-text mb-2">Hover Image (Product Listing)</label>
                <input type="hidden" name="hover_image" id="hoverImageFilename" value="<?php echo h($editProduct['hover_image'] ?? ''); ?>">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-shrink-0">
                        <div id="hoverImagePreview" class="w-28 h-28 bg-admin-bg rounded-lg border-2 border-dashed border-admin-border flex items-center justify-center overflow-hidden">
                            <?php if (!empty($editProduct['hover_image'])): ?>
                                <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo h($editProduct['hover_image']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="text-center text-admin-muted"><i class="fas fa-image text-xl"></i><p class="text-xs mt-1">Hover</p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="border-2 border-dashed border-admin-border rounded-lg p-3 text-center hover:border-admin-accent transition-colors cursor-pointer" onclick="document.getElementById('hoverImageUpload').click()">
                            <input type="file" id="hoverImageUpload" accept="image/jpeg,image/png,image/webp" class="hidden" data-target="hover">
                            <i class="fas fa-cloud-upload-alt text-xl text-admin-muted"></i>
                            <p class="text-xs text-admin-muted mt-1">Click to upload hover image</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Images -->
            <div>
                <label class="block text-sm font-medium text-admin-text mb-2">Gallery Images (Product Detail Carousel)</label>
                <input type="hidden" name="gallery" id="galleryFilenames" value="<?php echo h(json_encode($editProduct['gallery'] ?? [])); ?>">

                <div id="galleryPreview" class="flex flex-wrap gap-3 mb-3">
                    <?php if (!empty($editProduct['gallery'])): ?>
                        <?php foreach ($editProduct['gallery'] as $idx => $img): ?>
                            <div class="relative group" data-filename="<?php echo h($img); ?>">
                                <div class="w-20 h-20 bg-admin-bg rounded-lg border overflow-hidden">
                                    <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo h($img); ?>" class="w-full h-full object-cover">
                                </div>
                                <button type="button" onclick="removeGalleryImage('<?php echo h($img); ?>')" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="border-2 border-dashed border-admin-border rounded-lg p-4 text-center hover:border-admin-accent transition-colors cursor-pointer" onclick="document.getElementById('galleryUpload').click()">
                    <input type="file" id="galleryUpload" accept="image/jpeg,image/png,image/webp" class="hidden" data-target="gallery" multiple>
                    <i class="fas fa-images text-2xl text-admin-muted"></i>
                    <p class="text-sm text-admin-text font-medium mt-1">Click to add gallery images</p>
                    <p class="text-xs text-admin-muted">You can select multiple images</p>
                </div>
            </div>

            <!-- Upload Progress -->
            <div id="uploadProgress" class="hidden">
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-admin-bg rounded-full h-2">
                        <div id="progressBar" class="bg-admin-accent h-2 rounded-full transition-all" style="width: 0%"></div>
                    </div>
                    <span id="progressText" class="text-xs text-admin-muted">0%</span>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 py-4 bg-admin-bg border-t border-admin-border flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4">
            <a href="<?php echo BASE_URL; ?>admin/products" class="btn-secondary px-6 py-2 rounded-lg text-center">
                Cancel
            </a>
            <button type="submit" class="btn-primary px-6 py-2 rounded-lg">
                <?php echo $editProduct ? 'Update Product' : 'Create Product'; ?>
            </button>
        </div>
    </form>
</div>

<script>
// Gallery images array
let galleryImages = <?php echo json_encode($editProduct['gallery'] ?? []); ?>;

// Upload single image (main or hover)
async function uploadSingleImage(file, target) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showToast('Please upload a JPG, PNG or WebP image', 'error');
        return null;
    }
    if (file.size > 2 * 1024 * 1024) {
        showToast('Image must be less than 2MB', 'error');
        return null;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('csrf_token', csrfToken);

    const progressDiv = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');

    progressDiv.classList.remove('hidden');

    return new Promise((resolve) => {
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressText.textContent = percent + '%';
            }
        });

        xhr.onload = function() {
            progressDiv.classList.add('hidden');
            progressBar.style.width = '0%';

            if (xhr.status === 200) {
                const result = JSON.parse(xhr.responseText);
                if (result.success) {
                    resolve(result.filename);
                } else {
                    showToast(result.message || 'Upload failed', 'error');
                    resolve(null);
                }
            } else {
                showToast('Upload failed', 'error');
                resolve(null);
            }
        };

        xhr.onerror = function() {
            progressDiv.classList.add('hidden');
            showToast('Network error', 'error');
            resolve(null);
        };

        xhr.open('POST', baseUrl + 'api/admin/upload-image');
        xhr.send(formData);
    });
}

// Update preview image
function updatePreview(previewId, src) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = `<img src="${src}" class="w-full h-full object-cover">`;
}

// Main image upload
document.getElementById('mainImageUpload').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => updatePreview('mainImagePreview', e.target.result);
    reader.readAsDataURL(file);

    const filename = await uploadSingleImage(file, 'main');
    if (filename) {
        document.getElementById('mainImageFilename').value = filename;
        showToast('Main image uploaded');
    }
});

// Hover image upload
document.getElementById('hoverImageUpload').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => updatePreview('hoverImagePreview', e.target.result);
    reader.readAsDataURL(file);

    const filename = await uploadSingleImage(file, 'hover');
    if (filename) {
        document.getElementById('hoverImageFilename').value = filename;
        showToast('Hover image uploaded');
    }
});

// Gallery images upload
document.getElementById('galleryUpload').addEventListener('change', async function(e) {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    for (const file of files) {
        // Show temp preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const tempDiv = document.createElement('div');
            tempDiv.className = 'relative group opacity-50';
            tempDiv.innerHTML = `
                <div class="w-20 h-20 bg-admin-bg rounded-lg border overflow-hidden">
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-spinner fa-spin text-admin-accent"></i>
                </div>
            `;
            document.getElementById('galleryPreview').appendChild(tempDiv);
        };
        reader.readAsDataURL(file);

        const filename = await uploadSingleImage(file, 'gallery');
        if (filename) {
            galleryImages.push(filename);
            updateGalleryHiddenField();
        }
    }

    // Rebuild gallery preview
    rebuildGalleryPreview();
    showToast(`${files.length} image(s) uploaded to gallery`);
});

// Remove gallery image
function removeGalleryImage(filename) {
    galleryImages = galleryImages.filter(img => img !== filename);
    updateGalleryHiddenField();
    rebuildGalleryPreview();
}

// Update hidden field
function updateGalleryHiddenField() {
    document.getElementById('galleryFilenames').value = JSON.stringify(galleryImages);
}

// Rebuild gallery preview
function rebuildGalleryPreview() {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';

    galleryImages.forEach(img => {
        const div = document.createElement('div');
        div.className = 'relative group';
        div.dataset.filename = img;
        div.innerHTML = `
            <div class="w-20 h-20 bg-admin-bg rounded-lg border overflow-hidden">
                <img src="${baseUrl}public/assets/images/${img}" class="w-full h-full object-cover">
            </div>
            <button type="button" onclick="removeGalleryImage('${img}')" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-times"></i>
            </button>
        `;
        preview.appendChild(div);
    });
}

// Form submission
document.getElementById('productForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    // Combine price fields into prices object
    data.prices = {
        NGN: parseFloat(data.price_ngn) || 0,
        GBP: parseFloat(data.price_gbp) || 0,
        USD: parseFloat(data.price_usd) || 0
    };
    // Keep legacy price field for backwards compatibility
    data.price = data.prices.NGN;
    // Remove individual price fields
    delete data.price_ngn;
    delete data.price_gbp;
    delete data.price_usd;

    // Parse gallery as array
    try {
        data.gallery = JSON.parse(data.gallery || '[]');
    } catch {
        data.gallery = [];
    }

    const isEdit = !!data.id;

    try {
        const endpoint = isEdit ? 'api/admin/products/update' : 'api/admin/products/create';
        const result = await apiRequest(endpoint, 'POST', data);

        if (result.success) {
            showToast(isEdit ? 'Product updated' : 'Product created');
            setTimeout(() => {
                window.location.href = baseUrl + 'admin/products';
            }, 1000);
        } else {
            showToast(result.message || 'Failed to save product', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});
</script>

<?php else: ?>
<!-- Products List -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-admin-muted"><?php echo count($products); ?> total products</p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin/products?action=new" class="btn-primary px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Add Product
        </a>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <?php if (empty($products)): ?>
            <div class="col-span-full card bg-white rounded-xl p-12 text-center">
                <i class="fas fa-box-open text-4xl text-admin-muted mb-4"></i>
                <p class="text-admin-muted">No products yet</p>
                <a href="<?php echo BASE_URL; ?>admin/products?action=new"
                   class="inline-block mt-4 text-admin-accent hover:underline">
                    Add your first product
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="card bg-white rounded-xl overflow-hidden">
                    <div class="aspect-video bg-admin-bg">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo BASE_URL; ?>public/assets/images/<?php echo h($product['image']); ?>"
                                 alt="<?php echo h($product['name']); ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-admin-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-semibold text-admin-text"><?php echo h($product['name']); ?></h3>
                                <?php if (isset($product['prices'])): ?>
                                <p class="text-admin-accent font-medium text-sm">
                                    ₦<?php echo number_format($product['prices']['NGN']); ?> /
                                    £<?php echo number_format($product['prices']['GBP'], 2); ?> /
                                    $<?php echo number_format($product['prices']['USD'], 2); ?>
                                </p>
                                <?php else: ?>
                                <p class="text-admin-accent font-medium">₦<?php echo number_format($product['price'] ?? 0); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="status-badge status-<?php echo $product['status'] ?? 'active'; ?>">
                                <?php echo ucfirst($product['status'] ?? 'active'); ?>
                            </span>
                        </div>

                        <p class="text-sm text-admin-muted mb-4 line-clamp-2">
                            <?php echo h($product['description'] ?? 'No description'); ?>
                        </p>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-admin-muted">
                                <i class="fas fa-cubes mr-1"></i>
                                Stock: <?php echo $product['stock'] ?? 'N/A'; ?>
                            </span>

                            <div class="flex items-center gap-2">
                                <a href="<?php echo BASE_URL; ?>admin/products?edit=<?php echo $product['id']; ?>"
                                   class="text-admin-accent hover:text-amber-600" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteProduct(<?php echo $product['id']; ?>)"
                                        class="text-red-500 hover:text-red-600" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
async function deleteProduct(id) {
    if (!confirmAction('Are you sure you want to delete this product?')) return;

    try {
        const result = await apiRequest('api/admin/products/delete', 'POST', { id });

        if (result.success) {
            showToast('Product deleted');
            location.reload();
        } else {
            showToast(result.message || 'Failed to delete', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}
</script>
<?php endif; ?>

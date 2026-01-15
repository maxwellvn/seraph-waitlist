<?php
$statusFilter = $_GET['status'] ?? '';
$sourceFilter = $_GET['source'] ?? '';
$subscribers = getAllSubscribers(['status' => $statusFilter, 'source' => $sourceFilter]);

// Handle export
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers_' . date('Y-m-d') . '.csv"');
    echo exportSubscribers(['status' => $statusFilter, 'source' => $sourceFilter]);
    exit;
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-admin-muted"><?php echo count($subscribers); ?> total subscribers</p>

        <a href="<?php echo BASE_URL; ?>admin/subscribers?action=export<?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $sourceFilter ? '&source=' . $sourceFilter : ''; ?>"
           class="btn-primary px-4 py-2 rounded-lg">
            <i class="fas fa-download mr-2"></i>Export CSV
        </a>
    </div>

    <!-- Filters -->
    <div class="card bg-white rounded-xl p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-center">
            <div>
                <select name="status" class="border border-admin-border rounded-lg px-4 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div>
                <select name="source" class="border border-admin-border rounded-lg px-4 py-2 text-sm">
                    <option value="">All Sources</option>
                    <option value="waitlist_form" <?php echo $sourceFilter === 'waitlist_form' ? 'selected' : ''; ?>>Waitlist Form</option>
                    <option value="newsletter_section" <?php echo $sourceFilter === 'newsletter_section' ? 'selected' : ''; ?>>Newsletter</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <?php if ($statusFilter || $sourceFilter): ?>
                <a href="<?php echo BASE_URL; ?>admin/subscribers" class="text-admin-muted hover:text-admin-text text-sm">
                    Clear Filters
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Subscribers Table -->
    <div class="card bg-white rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-admin-bg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-admin-border">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Subscriber</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Source</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-admin-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    <?php if (empty($subscribers)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-admin-muted">
                                <i class="fas fa-envelope text-4xl mb-4"></i>
                                <p>No subscribers found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subscribers as $sub): ?>
                            <tr class="table-row" data-id="<?php echo $sub['id']; ?>">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="row-select rounded border-admin-border" value="<?php echo $sub['id']; ?>">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-admin-bg rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-admin-muted"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-admin-text"><?php echo h($sub['name'] ?? 'Subscriber'); ?></p>
                                            <p class="text-sm text-admin-muted"><?php echo h($sub['email']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-admin-muted"><?php echo h($sub['phone'] ?? '-'); ?></p>
                                    <p class="text-sm text-admin-muted"><?php echo h($sub['city'] ?? '-'); ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm px-2 py-1 bg-admin-bg rounded">
                                        <?php echo h($sub['source'] ?? 'Unknown'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-admin-muted">
                                    <?php echo formatDate($sub['subscribed_at'] ?? $sub['created_at'] ?? '', 'M j, Y'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="status-badge status-<?php echo $sub['status'] ?? 'active'; ?>">
                                        <?php echo ucfirst($sub['status'] ?? 'active'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="editSubscriber(<?php echo $sub['id']; ?>)"
                                            class="text-admin-accent hover:text-amber-600 mr-3" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteSubscriber(<?php echo $sub['id']; ?>)"
                                            class="text-red-500 hover:text-red-600" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="hidden card bg-white rounded-xl p-4">
        <div class="flex items-center justify-between">
            <p class="text-admin-muted"><span id="selectedCount">0</span> selected</p>
            <div class="flex gap-2">
                <button onclick="bulkUpdateStatus('active')" class="btn-secondary px-4 py-2 rounded-lg text-sm">
                    Mark Active
                </button>
                <button onclick="bulkUpdateStatus('inactive')" class="btn-secondary px-4 py-2 rounded-lg text-sm">
                    Mark Inactive
                </button>
                <button onclick="bulkDelete()" class="btn-danger px-4 py-2 rounded-lg text-sm">
                    Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl w-full max-w-md mx-auto my-8">
        <div class="px-6 py-4 border-b border-admin-border flex items-center justify-between">
            <h3 class="font-semibold text-admin-text">Edit Subscriber</h3>
            <button onclick="closeModal()" class="text-admin-muted hover:text-admin-text">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" class="p-6 space-y-4">
            <input type="hidden" name="id" id="editId">

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Name</label>
                <input type="text" name="name" id="editName"
                       class="w-full border border-admin-border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Email</label>
                <input type="email" name="email" id="editEmail"
                       class="w-full border border-admin-border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Phone</label>
                <input type="text" name="phone" id="editPhone"
                       class="w-full border border-admin-border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">City</label>
                <input type="text" name="city" id="editCity"
                       class="w-full border border-admin-border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Status</label>
                <select name="status" id="editStatus" class="w-full border border-admin-border rounded-lg px-4 py-2">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 btn-secondary px-4 py-2 rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="flex-1 btn-primary px-4 py-2 rounded-lg">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Store subscribers data
const subscribers = <?php echo json_encode($subscribers); ?>;

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-select');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkActions();
});

// Row checkboxes
document.querySelectorAll('.row-select').forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const selected = document.querySelectorAll('.row-select:checked').length;
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (selected > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = selected;
    } else {
        bulkActions.classList.add('hidden');
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);
}

function editSubscriber(id) {
    const sub = subscribers.find(s => s.id === id);
    if (!sub) return;

    document.getElementById('editId').value = sub.id;
    document.getElementById('editName').value = sub.name || '';
    document.getElementById('editEmail').value = sub.email || '';
    document.getElementById('editPhone').value = sub.phone || '';
    document.getElementById('editCity').value = sub.city || '';
    document.getElementById('editStatus').value = sub.status || 'active';

    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const result = await apiRequest('api/admin/subscribers/update', 'POST', data);

        if (result.success) {
            showToast('Subscriber updated');
            closeModal();
            location.reload();
        } else {
            showToast(result.message || 'Failed to update', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});

async function deleteSubscriber(id) {
    if (!confirmAction('Are you sure you want to delete this subscriber?')) return;

    try {
        const result = await apiRequest('api/admin/subscribers/delete', 'POST', { id });

        if (result.success) {
            showToast('Subscriber deleted');
            location.reload();
        } else {
            showToast(result.message || 'Failed to delete', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}

async function bulkUpdateStatus(status) {
    const ids = getSelectedIds();
    if (ids.length === 0) return;

    try {
        for (const id of ids) {
            await apiRequest('api/admin/subscribers/update', 'POST', { id, status });
        }
        showToast(`${ids.length} subscribers updated`);
        location.reload();
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}

async function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    if (!confirmAction(`Are you sure you want to delete ${ids.length} subscribers?`)) return;

    try {
        for (const id of ids) {
            await apiRequest('api/admin/subscribers/delete', 'POST', { id });
        }
        showToast(`${ids.length} subscribers deleted`);
        location.reload();
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}
</script>

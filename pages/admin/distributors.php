<?php
$statusFilter = $_GET['status'] ?? '';
$applications = getDistributorApplications(['status' => $statusFilter]);

// Handle export
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="distributor_applications_' . date('Y-m-d') . '.csv"');
    echo exportDistributorApplications(['status' => $statusFilter]);
    exit;
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-admin-muted"><?php echo count($applications); ?> total applications</p>

        <a href="<?php echo BASE_URL; ?>admin/distributors?action=export<?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?>"
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
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="contacted" <?php echo $statusFilter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                    <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <?php if ($statusFilter): ?>
                <a href="<?php echo BASE_URL; ?>admin/distributors" class="text-admin-muted hover:text-admin-text text-sm">
                    Clear Filters
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Applications Table -->
    <div class="card bg-white rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-admin-bg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-admin-border">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Business Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-admin-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-admin-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    <?php if (empty($applications)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-admin-muted">
                                <i class="fas fa-handshake text-4xl mb-4"></i>
                                <p>No distributor applications found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                            <tr class="table-row" data-id="<?php echo $app['id']; ?>">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="row-select rounded border-admin-border" value="<?php echo $app['id']; ?>">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-admin-accent/20 rounded-full flex items-center justify-center">
                                            <i class="fas fa-store text-admin-accent"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-admin-text"><?php echo h($app['full_name']); ?></p>
                                            <p class="text-sm text-admin-muted"><?php echo h($app['email']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-admin-text"><?php echo h($app['phone']); ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-admin-text"><?php echo h($app['location']); ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm px-2 py-1 bg-admin-bg rounded">
                                        <?php echo h(ucfirst($app['business_type'] ?? 'Not specified')); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-admin-muted">
                                    <?php echo formatDate($app['submitted_at'] ?? '', 'M j, Y'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $statusClass = match($app['status'] ?? 'pending') {
                                        'approved' => 'status-active',
                                        'contacted' => 'status-shipped',
                                        'rejected' => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($app['status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="viewApplication(<?php echo $app['id']; ?>)"
                                            class="text-blue-500 hover:text-blue-600 mr-3" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editApplication(<?php echo $app['id']; ?>)"
                                            class="text-admin-accent hover:text-amber-600 mr-3" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteApplication(<?php echo $app['id']; ?>)"
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
                <button onclick="bulkUpdateStatus('contacted')" class="btn-secondary px-4 py-2 rounded-lg text-sm">
                    Mark Contacted
                </button>
                <button onclick="bulkUpdateStatus('approved')" class="btn-primary px-4 py-2 rounded-lg text-sm">
                    Approve
                </button>
                <button onclick="bulkUpdateStatus('rejected')" class="btn-secondary px-4 py-2 rounded-lg text-sm">
                    Reject
                </button>
                <button onclick="bulkDelete()" class="btn-danger px-4 py-2 rounded-lg text-sm">
                    Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl w-full max-w-lg mx-auto my-8">
        <div class="px-6 py-4 border-b border-admin-border flex items-center justify-between">
            <h3 class="font-semibold text-admin-text">Application Details</h3>
            <button onclick="closeViewModal()" class="text-admin-muted hover:text-admin-text">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-admin-muted uppercase mb-1">Full Name</label>
                    <p id="viewName" class="text-admin-text font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-admin-muted uppercase mb-1">Email</label>
                    <p id="viewEmail" class="text-admin-text"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-admin-muted uppercase mb-1">Phone</label>
                    <p id="viewPhone" class="text-admin-text"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-admin-muted uppercase mb-1">Location</label>
                    <p id="viewLocation" class="text-admin-text"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-admin-muted uppercase mb-1">Business Type</label>
                    <p id="viewBusinessType" class="text-admin-text"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-admin-muted uppercase mb-1">Submitted</label>
                    <p id="viewDate" class="text-admin-text"></p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-admin-muted uppercase mb-1">Message</label>
                <div id="viewMessage" class="text-admin-text bg-admin-bg p-4 rounded-lg text-sm"></div>
            </div>

            <div class="flex gap-4 pt-4">
                <button onclick="closeViewModal()" class="flex-1 btn-secondary px-4 py-2 rounded-lg">
                    Close
                </button>
                <button onclick="closeViewModal(); editApplication(currentViewId);" class="flex-1 btn-primary px-4 py-2 rounded-lg">
                    Edit Application
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl w-full max-w-md mx-auto my-8">
        <div class="px-6 py-4 border-b border-admin-border flex items-center justify-between">
            <h3 class="font-semibold text-admin-text">Edit Application</h3>
            <button onclick="closeModal()" class="text-admin-muted hover:text-admin-text">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" class="p-6 space-y-4">
            <input type="hidden" name="id" id="editId">

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Full Name</label>
                <input type="text" name="full_name" id="editName"
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
                <label class="block text-sm font-medium text-admin-muted mb-1">Location</label>
                <input type="text" name="location" id="editLocation"
                       class="w-full border border-admin-border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Business Type</label>
                <select name="business_type" id="editBusinessType" class="w-full border border-admin-border rounded-lg px-4 py-2">
                    <option value="">Not specified</option>
                    <option value="retail">Retail Store</option>
                    <option value="wholesale">Wholesale / Distribution</option>
                    <option value="pharmacy">Pharmacy / Health Store</option>
                    <option value="online">Online / E-commerce</option>
                    <option value="salon">Salon / Spa</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Status</label>
                <select name="status" id="editStatus" class="w-full border border-admin-border rounded-lg px-4 py-2">
                    <option value="pending">Pending</option>
                    <option value="contacted">Contacted</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-admin-muted mb-1">Admin Notes</label>
                <textarea name="admin_notes" id="editNotes" rows="3"
                          class="w-full border border-admin-border rounded-lg px-4 py-2"
                          placeholder="Internal notes about this application..."></textarea>
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
// Store applications data
const applications = <?php echo json_encode($applications); ?>;
let currentViewId = null;

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

function viewApplication(id) {
    const app = applications.find(a => a.id === id);
    if (!app) return;

    currentViewId = id;

    document.getElementById('viewName').textContent = app.full_name || '';
    document.getElementById('viewEmail').textContent = app.email || '';
    document.getElementById('viewPhone').textContent = app.phone || '';
    document.getElementById('viewLocation').textContent = app.location || '';
    document.getElementById('viewBusinessType').textContent = app.business_type ? app.business_type.charAt(0).toUpperCase() + app.business_type.slice(1) : 'Not specified';
    document.getElementById('viewDate').textContent = app.submitted_at || '';
    document.getElementById('viewMessage').textContent = app.message || 'No message provided';

    document.getElementById('viewModal').classList.remove('hidden');
    document.getElementById('viewModal').classList.add('flex');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.getElementById('viewModal').classList.remove('flex');
    currentViewId = null;
}

function editApplication(id) {
    const app = applications.find(a => a.id === id);
    if (!app) return;

    document.getElementById('editId').value = app.id;
    document.getElementById('editName').value = app.full_name || '';
    document.getElementById('editEmail').value = app.email || '';
    document.getElementById('editPhone').value = app.phone || '';
    document.getElementById('editLocation').value = app.location || '';
    document.getElementById('editBusinessType').value = app.business_type || '';
    document.getElementById('editStatus').value = app.status || 'pending';
    document.getElementById('editNotes').value = app.admin_notes || '';

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
        const result = await apiRequest('api/admin/distributors/update', 'POST', data);

        if (result.success) {
            showToast('Application updated');
            closeModal();
            location.reload();
        } else {
            showToast(result.message || 'Failed to update', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});

async function deleteApplication(id) {
    if (!confirmAction('Are you sure you want to delete this application?')) return;

    try {
        const result = await apiRequest('api/admin/distributors/delete', 'POST', { id });

        if (result.success) {
            showToast('Application deleted');
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
            await apiRequest('api/admin/distributors/update', 'POST', { id, status });
        }
        showToast(`${ids.length} applications updated`);
        location.reload();
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}

async function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    if (!confirmAction(`Are you sure you want to delete ${ids.length} applications?`)) return;

    try {
        for (const id of ids) {
            await apiRequest('api/admin/distributors/delete', 'POST', { id });
        }
        showToast(`${ids.length} applications deleted`);
        location.reload();
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}
</script>

<?php
include '../../includes/auth/admin_auth.php';
require_once "../../includes/auth/fetch_data.php";
?>


<section>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Users Overview</h1>
    </div>

    <!-- User Stats Cards -->
    <div class="row mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($users) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($users, fn($u) => $u['status'] === 'Active')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspended Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Suspended Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($users, fn($u) => $u['status'] === 'Suspended')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New This Month -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                New This Month
                            </div>
                            <?php
                            $thisMonth = array_filter($users, fn($u) => isset($u['member_since']) && date('Y-m', strtotime($u['member_since'])) === date('Y-m'));
                            ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($thisMonth) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usersTable">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAllUsers"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Spent</th>
                            <th>Member Since</th>
                            <th>Last Login</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr data-id="<?= $user['id'] ?>">
                                <td data-label="Select"><input type="checkbox" name="selectedUsers[]" value="<?= $user['id'] ?>"></td>
                                <td data-label="ID"><strong>#<?= sprintf("%04d", $user['id']) ?></strong></td>
                                <td data-label="Name" class="editable" data-field="name"><?= htmlspecialchars(($user['firstname'] ?? '') . " " . ($user['lastname'] ?? '')) ?></td>
                                <td data-label="Email" class="editable" data-field="email"><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                                <td class="editable" data-field="phone"><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                                <td class="editable" data-field="status"><?= htmlspecialchars($user['status'] ?? 'Inactive') ?></td>
                                <td class="editable" data-field="total_bookings"><?= htmlspecialchars($user['total_bookings'] ?? 0) ?></td>
                                <td class="editable" data-field="total_spent"><?= number_format($user['total_spent'] ?? 0, 2) ?></td>
                                <td><?= isset($user['member_since']) ? date('M j, Y', strtotime($user['member_since'])) : '-' ?></td>
                                <td><?= isset($user['last_login']) ? date('M j, Y g:i A', strtotime($user['last_login'])) : '-' ?></td>
                                <td class="text-center d-flex justify-content-center gap-1">
                                    <button class="btn btn-warning btn-icon btn-xs btn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-success btn-icon btn-xs btn-save d-none" title="Save">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-danger btn-icon btn-xs btn-delete" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary" disabled id="bulkActionBtn">
                    <i class="fas fa-tasks"></i> Bulk Actions (<span class="selected-count">0</span>)
                </button>
                <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" disabled id="bulkDropdown">
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-pause"></i> Suspend Users</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-play"></i> Activate Users</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash"></i> Delete Users</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Pagination -->
            <nav aria-label="Users pagination">
                <ul class="pagination justify-content-end">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    </div>
    </div>

</section>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h4 id="modalUserName">John Doe</h4>
                        <span class="badge bg-success" id="modalUserStatus">Active</span>
                    </div>
                    <div class="col-md-8">
                        <h6>Contact Information</h6>
                        <p><strong>Email:</strong> <span id="modalUserEmail">john@example.com</span></p>
                        <p><strong>Phone:</strong> <span id="modalUserPhone">+1 555-0101</span></p>

                        <h6 class="mt-4">Account Statistics</h6>
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-primary" id="modalUserBookings">15</h4>
                                    <small class="text-muted">Total Bookings</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-success" id="modalUserSpent">$375.00</h4>
                                    <small class="text-muted">Total Spent</small>
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-4">Account Details</h6>
                        <p><strong>Member Since:</strong> <span id="modalUserJoined">June 15, 2023</span></p>
                        <p><strong>Last Login:</strong> <span id="modalUserLastLogin">January 9, 2024</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Send Message</button>
                <button type="button" class="btn btn-warning">Edit User</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Enable inline edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelectorAll('.editable').forEach(cell => {
                const value = cell.textContent.trim();
                cell.innerHTML = `<input type="text" class="form-control form-control-sm" value="${value}">`;
            });
            row.querySelector('.btn-edit').classList.add('d-none');
            row.querySelector('.btn-save').classList.remove('d-none');
        });
    });

    // Save edited row
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const userId = row.dataset.id;

            let updatedData = {
                id: userId
            };

            row.querySelectorAll('.editable').forEach(cell => {
                const field = cell.dataset.field;
                const input = cell.querySelector('input');
                if (input) {
                    const value = input.value.trim();
                    if (field === "name") {
                        // Split name into firstname + lastname
                        const parts = value.split(" ");
                        updatedData.firstname = parts[0] || "";
                        updatedData.lastname = parts.slice(1).join(" ") || "";
                    } else {
                        updatedData[field] = value;
                    }
                    cell.textContent = value; // revert back to text
                }
            });

            // Send AJAX request
            fetch('../../includes/auth/update_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updatedData)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('User updated successfully');
                    } else {
                        alert('Failed to update user: ' + (data.message || 'Unknown error'));
                    }
                });

            row.querySelector('.btn-edit').classList.remove('d-none');
            row.querySelector('.btn-save').classList.add('d-none');
        });
    });


    // Delete user
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const userId = row.dataset.id;

            if (confirm("Are you sure you want to delete this user?")) {
                fetch('../../includes/auth/delete_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: userId,
                            type: "user"
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                            alert('User deleted successfully');
                        } else {
                            alert('Failed to delete user: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(err => {
                        console.error('Delete error:', err);
                        alert('Error deleting user');
                    });
            }
        });
    });
</script>

<script>
    // View user function
    function viewUser(userId) {
        // Mock data - in real app, this would fetch from server
        const userData = {
            1: {
                name: 'John Doe',
                email: 'john@example.com',
                phone: '+1 555-0101',
                status: 'Active',
                totalBookings: 15,
                totalSpent: 375.00,
                joined: 'June 15, 2023',
                lastLogin: 'January 9, 2024'
            }
        };

        const user = userData[userId] || userData[1]; // Fallback to first user

        document.getElementById('modalUserName').textContent = user.name;
        document.getElementById('modalUserEmail').textContent = user.email;
        document.getElementById('modalUserPhone').textContent = user.phone;
        document.getElementById('modalUserStatus').textContent = user.status;
        document.getElementById('modalUserBookings').textContent = user.totalBookings;
        document.getElementById('modalUserSpent').textContent = '$' + user.totalSpent.toFixed(2);
        document.getElementById('modalUserJoined').textContent = user.joined;
        document.getElementById('modalUserLastLogin').textContent = user.lastLogin;
    }

    // Checkbox change handler (updates count and enables/disables bulk button)
const bulkBtn = document.getElementById('bulkActionBtn');
const bulkDropdown = document.getElementById('bulkDropdown');
const selectedCountSpan = document.querySelector('.selected-count');

function updateBulkUI() {
    const selected = document.querySelectorAll('input[name="selectedUsers[]"]:checked').length;
    selectedCountSpan.textContent = selected;

    if (selected > 0) {
        bulkBtn.removeAttribute('disabled');
        bulkDropdown.removeAttribute('disabled');
    } else {
        bulkBtn.setAttribute('disabled', 'true');
        bulkDropdown.setAttribute('disabled', 'true');
    }
}

// Watch all checkboxes
document.addEventListener('change', function (e) {
    if (e.target && e.target.name === "selectedUsers[]") {
        updateBulkUI();
    }
});

// Bulk action dropdown handler
document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
    item.addEventListener('click', function (e) {
        e.preventDefault();

        const action = this.textContent.trim();
        const selectedIds = Array.from(document.querySelectorAll('input[name="selectedUsers[]"]:checked'))
            .map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert("No users selected.");
            return;
        }

        if (action.includes("Delete")) {
            if (!confirm("Are you sure you want to delete the selected users?")) return;
        }

        fetch('../../includes/auth/bulk_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, ids: selectedIds })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Error: " + (data.message || "Unknown"));
            }
        })
        .catch(err => {
            console.error('Bulk error:', err);
            alert("Failed to perform bulk action.");
        });
    });
});

// Initialize bulk UI on page load
updateBulkUI();
    document.getElementById('selectAllUsers').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('input[name="selectedUsers[]"]').forEach(cb => {
            cb.checked = checked;
        });
        updateBulkUI();
    });


    // Filter users function
    function applyUserFilters() {
        const searchTerm = document.getElementById('userSearch').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const table = document.getElementById('usersTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            const name = row.cells[2].textContent.toLowerCase();
            const email = row.cells[3].textContent.toLowerCase();
            const status = row.cells[4].textContent.toLowerCase();

            const matchesSearch = searchTerm === '' || name.includes(searchTerm) || email.includes(searchTerm);
            const matchesStatus = statusFilter === '' || status.includes(statusFilter);

            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        }
    }
</script>
<style>
    .avatar-sm {
        width: 2rem;
        height: 2rem;
        font-size: 0.75rem;
    }

    .avatar-lg {
        width: 5rem;
        height: 5rem;
        font-size: 1.5rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }
</style>
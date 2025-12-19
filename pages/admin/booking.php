<?php
require_once "../../includes/auth/dashboard_backend.php";
require_once "../../includes/auth/all_bookings_backend.php";
?>

<section>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Booking Management</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
        </div>
    </div>

    <!-- 🔍 Search & Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="#filtersCollapse" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtersCollapse" class="text-decoration-none">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Search & Filters</h6>
            </a>
        </div>
        <div class="collapse" id="filtersCollapse">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Search bookings..." id="searchInput">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="dateFilter">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="locationFilter">
                            <option value="">All Locations</option>
                            <option value="downtown">Downtown Business</option>
                            <option value="mall">City Center Mall</option>
                            <option value="airport">Airport Terminal</option>
                            <option value="university">University Campus</option>
                            <option value="medical">Medical Center</option>
                            <option value="sports">Sports Arena</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📋 All Bookings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Bookings</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Booking Receipt</th>
                            <th>Customer</th>
                            <th>Slot</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allBookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['receipt']); ?></td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking['user']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></small>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($booking['slot']); ?></span></td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars(date('M d, Y', strtotime($booking['start_time']))); ?></strong><br>
                                        <small><?php echo $booking['end_time'] ? htmlspecialchars(date('M d, Y', strtotime($booking['end_time']))) : 'Ongoing'; ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    if ($booking['end_time']) {
                                        $start = new DateTime($booking['start_time']);
                                        $end = new DateTime($booking['end_time']);
                                        $duration = $start->diff($end);
                                        echo $duration->h . 'h ' . $duration->i . 'm';
                                    } else {
                                        echo 'Ongoing';
                                    }
                                    ?>
                                </td>
                                <td>₱<?php echo number_format($booking['amount'], 2); ?></td>
                                <td>
                                    <span class="badge
                                            <?php echo $booking['status'] === 'pending' ? 'bg-info' : ($booking['status'] === 'active' ? 'bg-warning' : ($booking['status'] === 'completed' ? 'bg-success' : ($booking['status'] === 'cancelled' ? 'bg-danger' : 'bg-secondary'))); ?>">
                                        <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteBooking('<?php echo $booking['id']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editBookingModal" onclick="editBooking('<?php echo $booking['id']; ?>', '<?php echo $booking['status']; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- 📌 Pagination -->
            <nav aria-label="Bookings pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link">Previous</a></li>
                    <li class="page-item active"><a class="page-link">1</a></li>
                    <li class="page-item"><a class="page-link">2</a></li>
                    <li class="page-item"><a class="page-link">3</a></li>
                    <li class="page-item"><a class="page-link">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Edit Booking Status Modal -->
    <div class="modal fade" id="editBookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Booking Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStatusForm">
                        <input type="hidden" id="editBookingId" name="booking_id">
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updateBookingStatus()">Update Status</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function editBooking(bookingId, currentStatus) {
        document.getElementById('editBookingId').value = bookingId;
        document.getElementById('editStatus').value = currentStatus;
    }

    function updateBookingStatus() {
        const bookingId = document.getElementById('editBookingId').value;
        const status = document.getElementById('editStatus').value;

        fetch('../../includes/auth/update_booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: bookingId, status: status }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Booking status updated successfully.');
                location.reload();
            } else {
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    }

    function deleteBooking(bookingId) {
        if (confirm('Are you sure you want to delete this booking?')) {
            fetch('../../includes/auth/delete_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: bookingId, type: 'booking' }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking deleted successfully.');
                    location.reload();
                } else {
                    alert('Error deleting booking: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the booking.');
            });
        }
    }
</script>

<script src="../../assets/js/admin.js"></script>

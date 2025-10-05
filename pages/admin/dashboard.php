<?php
require_once "../../includes/auth/dashboard_backend.php";
require_once "../../includes/auth/all_bookings_backend.php";
?>

<section>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard Overview</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Parking Spaces
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalSpaces); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-parking fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($todayRevenue); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Occupancy Rate
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $occupancyRate; ?>%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: <?php echo $occupancyRate; ?>%" aria-valuenow="<?php echo $occupancyRate; ?>" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                New Bookings Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $newBookingsToday; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalBookings; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $activeBookings; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Today's Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($todayRevenue, 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completedBookings; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
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
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Slot</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allBookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
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
                                        <small><?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))); ?> - <?php echo $booking['end_time'] ? htmlspecialchars(date('H:i', strtotime($booking['end_time']))) : 'Ongoing'; ?></small>
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
                                    <?php if ($booking['receipt']): ?>
                                        <a href="../../<?php echo htmlspecialchars($booking['receipt']); ?>" target="_blank">
                                            <img src="../../<?php echo htmlspecialchars($booking['receipt']); ?>" alt="Receipt" style="width: 50px; height: auto;">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No receipt</span>
                                    <?php endif; ?>
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


    <!-- View Booking Modal -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p><strong>Name:</strong> <span id="modalCustomerName"></span></p>
                            <p><strong>Email:</strong> <span id="modalCustomerEmail"></span></p>
                            <p><strong>Phone:</strong> +1 (555) 123-4567</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Booking Details</h6>
                            <p><strong>Booking ID:</strong> <span id="modalBookingId"></span></p>
                            <p><strong>Location:</strong> <span id="modalLocation"></span></p>
                            <p><strong>Space:</strong> <span id="modalSpace"></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Schedule</h6>
                            <p><strong>Date:</strong> <span id="modalDate"></span></p>
                            <p><strong>Start Time:</strong> <span id="modalStartTime"></span></p>
                            <p><strong>End Time:</strong> <span id="modalEndTime"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment</h6>
                            <p><strong>Amount:</strong> $<span id="modalAmount"></span></p>
                            <p><strong>Status:</strong> <span id="modalPaymentStatus"></span></p>
                            <p><strong>Method:</strong> Credit Card (****1234)</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Send Notification</button>
                    <button type="button" class="btn btn-warning">Modify Booking</button>
                </div>
            </div>
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
    function viewBooking(bookingId) {
        // Mock data population - in real app, this would fetch from server
        const bookingData = {
            'BK001': {
                customerName: 'John Doe',
                customerEmail: 'john@example.com',
                location: 'Downtown Business District',
                space: 'A-15',
                date: '2024-01-09',
                startTime: '09:00',
                endTime: '17:00',
                amount: '25.00',
                paymentStatus: 'Paid'
            }
        };

        const booking = bookingData[bookingId];
        if (booking) {
            document.getElementById('modalCustomerName').textContent = booking.customerName;
            document.getElementById('modalCustomerEmail').textContent = booking.customerEmail;
            document.getElementById('modalBookingId').textContent = bookingId;
            document.getElementById('modalLocation').textContent = booking.location;
            document.getElementById('modalSpace').textContent = booking.space;
            document.getElementById('modalDate').textContent = booking.date;
            document.getElementById('modalStartTime').textContent = booking.startTime;
            document.getElementById('modalEndTime').textContent = booking.endTime;
            document.getElementById('modalAmount').textContent = booking.amount;
            document.getElementById('modalPaymentStatus').textContent = booking.paymentStatus;
        }
    }

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
                alert('Booking status updated successfully.');
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

    function cancelBooking(bookingId) {
        if (confirm('Are you sure you want to cancel this booking?')) {
            alert('Booking ' + bookingId + ' has been cancelled.');
            // In real app, send AJAX request to cancel booking
        }
    }
</script>
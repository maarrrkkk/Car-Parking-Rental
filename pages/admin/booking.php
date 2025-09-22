<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Mock data for bookings
$bookings = [
    ['id' => 'BK001', 'user' => 'John Doe', 'email' => 'john@example.com', 'location' => 'Downtown Business', 'space' => 'A-15', 'date' => '2024-01-09', 'start_time' => '09:00', 'end_time' => '17:00', 'amount' => 25.00, 'status' => 'Active', 'payment_status' => 'Paid'],
    ['id' => 'BK002', 'user' => 'Jane Smith', 'email' => 'jane@example.com', 'location' => 'City Center Mall', 'space' => 'B-32', 'date' => '2024-01-09', 'start_time' => '12:00', 'end_time' => '15:00', 'amount' => 15.00, 'status' => 'Completed', 'payment_status' => 'Paid'],
    ['id' => 'BK003', 'user' => 'Mike Johnson', 'email' => 'mike@example.com', 'location' => 'Airport Terminal', 'space' => 'C-101', 'date' => '2024-01-09', 'start_time' => '06:00', 'end_time' => '18:00', 'amount' => 45.00, 'status' => 'Active', 'payment_status' => 'Paid'],
    ['id' => 'BK004', 'user' => 'Sarah Wilson', 'email' => 'sarah@example.com', 'location' => 'University Campus', 'space' => 'D-25', 'date' => '2024-01-08', 'start_time' => '08:00', 'end_time' => '14:00', 'amount' => 12.00, 'status' => 'Completed', 'payment_status' => 'Paid'],
    ['id' => 'BK005', 'user' => 'David Brown', 'email' => 'david@example.com', 'location' => 'Medical Center', 'space' => 'E-67', 'date' => '2024-01-08', 'start_time' => '10:00', 'end_time' => '16:00', 'amount' => 30.00, 'status' => 'Active', 'payment_status' => 'Pending'],
    ['id' => 'BK006', 'user' => 'Lisa Garcia', 'email' => 'lisa@example.com', 'location' => 'Sports Arena', 'space' => 'F-234', 'date' => '2024-01-07', 'start_time' => '18:00', 'end_time' => '23:00', 'amount' => 75.00, 'status' => 'Cancelled', 'payment_status' => 'Refunded'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - ParkEase Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="admin-styles.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Bookings Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                                <i class="fas fa-plus"></i> New Booking
                            </button>
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
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($bookings); ?></div>
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
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count(array_filter($bookings, function($b) { return $b['status'] === 'Active'; })); ?></div>
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
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format(array_sum(array_column(array_filter($bookings, function($b) { return $b['date'] === '2024-01-09'; }), 'amount')), 2); ?></div>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Payments
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count(array_filter($bookings, function($b) { return $b['payment_status'] === 'Pending'; })); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="Search bookings..." id="searchInput">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="paymentFilter">
                                    <option value="">All Payments</option>
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                    <option value="refunded">Refunded</option>
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

                <!-- Bookings Table -->
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">All Bookings</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="bookingsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Location</th>
                                        <th>Space</th>
                                        <th>Date & Time</th>
                                        <th>Duration</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($booking['id']); ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($booking['user']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($booking['space']); ?></span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($booking['date']); ?></strong>
                                                <br><small><?php echo htmlspecialchars($booking['start_time']); ?> - <?php echo htmlspecialchars($booking['end_time']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $start = new DateTime($booking['start_time']);
                                            $end = new DateTime($booking['end_time']);
                                            $duration = $start->diff($end);
                                            echo $duration->h . 'h ' . $duration->i . 'm';
                                            ?>
                                        </td>
                                        <td>
                                            <strong>$<?php echo number_format($booking['amount'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch($booking['status']) {
                                                case 'Active': $statusClass = 'bg-success'; break;
                                                case 'Completed': $statusClass = 'bg-primary'; break;
                                                case 'Cancelled': $statusClass = 'bg-danger'; break;
                                                default: $statusClass = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $paymentClass = '';
                                            switch($booking['payment_status']) {
                                                case 'Paid': $paymentClass = 'bg-success'; break;
                                                case 'Pending': $paymentClass = 'bg-warning'; break;
                                                case 'Refunded': $paymentClass = 'bg-info'; break;
                                                default: $paymentClass = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $paymentClass; ?>">
                                                <?php echo htmlspecialchars($booking['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewBookingModal" onclick="viewBooking('<?php echo $booking['id']; ?>')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editBookingModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($booking['status'] === 'Active'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelBooking('<?php echo $booking['id']; ?>')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Bookings pagination">
                            <ul class="pagination justify-content-center">
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
            </main>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
        
        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                alert('Booking ' + bookingId + ' has been cancelled.');
                // In real app, send AJAX request to cancel booking
            }
        }
    </script>
</body>
</html>
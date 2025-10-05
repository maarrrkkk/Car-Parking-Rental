<?php
include '../../includes/auth/admin_auth.php';
require_once '../../config/database.php';   // <-- Add this line
require_once '../../includes/auth/report_data.php';

$timePeriod = isset($_GET['period']) ? $_GET['period'] : 'month';

// Pass the PDO connection into getReportData
$reportData = getReportData($pdo, $timePeriod);

// Extract data into variables for easier access in the view
$keyMetrics = $reportData['keyMetrics'];
$monthlyPerformance = $reportData['monthlyPerformance'];
$topSlots = $reportData['topSlots'];
$chartData = $reportData['charts'];

?>


<section>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reports</h1>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Time Period</label>
                    <select class="form-select" id="timePeriodSelector">
                        <option value="today" <?php echo ($timePeriod == 'today') ? 'selected' : ''; ?>>Today</option>
                        <option value="week" <?php echo ($timePeriod == 'week') ? 'selected' : ''; ?>>This Week</option>
                        <option value="month" <?php echo ($timePeriod == 'month') ? 'selected' : ''; ?>>This Month</option>
                        <option value="year" <?php echo ($timePeriod == 'year') ? 'selected' : ''; ?>>This Year</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Revenue (<?php echo ucfirst($timePeriod); ?>) </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($keyMetrics['totalRevenue'], 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Total Bookings (<?php echo ucfirst($timePeriod); ?>) </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($keyMetrics['totalBookings']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                                Current Occupancy </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $keyMetrics['averageOccupancy']; ?>%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $keyMetrics['averageOccupancy']; ?>%" aria-valuenow="<?php echo $keyMetrics['averageOccupancy']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                                Avg Revenue/Booking </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($keyMetrics['avgRevenuePerBooking'], 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue & Bookings Trend (YTD)</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueBookingsChart" width="100" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Booking Status Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="bookingStatusChart" width="100" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Performance (YTD)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Month</th>
                                    <th>Revenue</th>
                                    <th>Bookings</th>
                                    <th>Avg/Booking</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($monthlyPerformance)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No data available for this year yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($monthlyPerformance as $month): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($month['month']); ?></strong></td>
                                            <td>$<?php echo number_format($month['revenue']); ?></td>
                                            <td><?php echo number_format($month['bookings']); ?></td>
                                            <td>$<?php echo number_format($month['revenue'] / ($month['bookings'] ?: 1), 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performing Slots (All Time)</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($topSlots)): ?>
                        <p class="text-center">No booking data available to rank slots.</p>
                    <?php else: ?>
                        <?php foreach ($topSlots as $index => $slot): ?>
                            <div class="mb-3 <?php echo $index < count($topSlots) - 1 ? 'border-bottom pb-3' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($slot['name']); ?></h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-peso-sign"></i> <?php echo number_format($slot['revenue']); ?> |
                                            <i class="fas fa-calendar-check"></i> <?php echo number_format($slot['bookings']); ?> bookings
                                        </div>
                                    </div>
                                    <div class="text-end ms-2">
                                        <div class="badge bg-primary">#<?php echo $index + 1; ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

</div>
</div>

<script id="report-data" type="application/json">
    <?php echo json_encode($chartData); ?>
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jsonData = document.getElementById('report-data').textContent;
        const chartData = JSON.parse(jsonData);
        initializeReportCharts(chartData);

        // Add event listener to the time period selector
        document.getElementById('timePeriodSelector').addEventListener('change', function() {
            const selectedPeriod = this.value;
            // Reload the page with the new period selection in the URL
            window.location.href = `index.php?current_page=reports&period=${selectedPeriod}`;
        });
    });

    function initializeReportCharts(data) {
        // Revenue & Bookings Chart
        const revenueBookingsCtx = document.getElementById('revenueBookingsChart');
        if (revenueBookingsCtx && data.revenueBookings && data.revenueBookings.labels.length > 0) {
            new Chart(revenueBookingsCtx, {
                type: 'line',
                data: {
                    labels: data.revenueBookings.labels,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: data.revenueBookings.revenue,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        yAxisID: 'y'
                    }, {
                        label: 'Bookings',
                        data: data.revenueBookings.bookings,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { position: 'left', ticks: { callback: value => '$' + value.toLocaleString() } },
                        y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: value => value.toLocaleString() } }
                    }
                }
            });
        }

        // Booking Status Chart
        const bookingStatusCtx = document.getElementById('bookingStatusChart');
        if (bookingStatusCtx && data.bookingStatus && data.bookingStatus.labels.length > 0) {
            new Chart(bookingStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: data.bookingStatus.labels,
                    datasets: [{
                        data: data.bookingStatus.data,
                        backgroundColor: ['#198754', '#0d6efd', '#dc3545', '#6c757d'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    }
</script>
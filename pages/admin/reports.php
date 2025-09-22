<?php
// session_start();
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit();
// }

?>

<section>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reports & Analytics</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-calendar-alt"></i> Date Range
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-primary">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Report Period Selector -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select class="form-select" id="reportType">
                        <option value="revenue">Revenue Report</option>
                        <option value="occupancy">Occupancy Report</option>
                        <option value="customer">Customer Report</option>
                        <option value="location">Location Performance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Time Period</label>
                    <select class="form-select" id="timePeriod">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" id="fromDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" id="toDate">
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Monthly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$68,000</div>
                            <div class="mt-2 d-flex align-items-center">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                <span class="text-success small">11.5% from last month</span>
                            </div>
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
                                Total Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,720</div>
                            <div class="mt-2 d-flex align-items-center">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                <span class="text-success small">8.9% from last month</span>
                            </div>
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
                                Average Occupancy
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">76.4%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: 76.4%" aria-valuenow="76.4" aria-valuemin="0"
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
                                Avg Revenue/Booking
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$39.53</div>
                            <div class="mt-2 d-flex align-items-center">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                <span class="text-success small">2.3% from last month</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue & Bookings Trend</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active">Revenue</button>
                        <button type="button" class="btn btn-outline-secondary">Bookings</button>
                        <button type="button" class="btn btn-outline-secondary">Both</button>
                    </div>
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

    <!-- Monthly Revenue Table -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Performance</h6>
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
                                    <th>Growth</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthlyRevenue as $month): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($month['month']); ?></strong></td>
                                        <td>$<?php echo number_format($month['revenue']); ?></td>
                                        <td><?php echo number_format($month['bookings']); ?></td>
                                        <td>$<?php echo number_format($month['revenue'] / $month['bookings'], 2); ?></td>
                                        <td>
                                            <?php
                                            $growthClass = $month['growth'] >= 0 ? 'text-success' : 'text-danger';
                                            $growthIcon = $month['growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                                            ?>
                                            <span class="<?php echo $growthClass; ?>">
                                                <i class="fas <?php echo $growthIcon; ?>"></i>
                                                <?php echo abs($month['growth']); ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Slots -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performing Slots</h6>
                </div>
                <div class="card-body">
                    <?php foreach ($topSlots as $index => $slot): ?>
                        <div class="mb-3 <?php echo $index < count($topSlots) - 1 ? 'border-bottom pb-3' : ''; ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($slot['name']); ?></h6>
                                    <div class="small text-muted">
                                        <i class="fas fa-dollar-sign"></i> $<?php echo number_format($slot['revenue']); ?>
                                        | <i class="fas fa-calendar-check"></i> <?php echo number_format($slot['bookings']); ?> bookings
                                    </div>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: <?php echo $slot['occupancy']; ?>%"
                                            aria-valuenow="<?php echo $slot['occupancy']; ?>"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="text-end ms-2">
                                    <div class="badge bg-primary">#<?php echo $index + 1; ?></div>
                                    <div class="small text-muted"><?php echo $slot['occupancy']; ?>% occupied</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Hourly Usage Pattern -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Usage Pattern</h6>
                </div>
                <div class="card-body">
                    <canvas id="hourlyUsageChart" width="100" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100 mb-2" onclick="generateReport('pdf')">
                                <i class="fas fa-file-pdf"></i> Generate PDF Report
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success w-100 mb-2" onclick="generateReport('excel')">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-info w-100 mb-2" onclick="generateReport('csv')">
                                <i class="fas fa-file-csv"></i> Export to CSV
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-warning w-100 mb-2" onclick="scheduleReport()">
                                <i class="fas fa-clock"></i> Schedule Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
</div>

<script>
    // Initialize charts when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeReportCharts();
    });

    function initializeReportCharts() {
        // Revenue & Bookings Chart
        const revenueBookingsCtx = document.getElementById('revenueBookingsChart');
        if (revenueBookingsCtx) {
            new Chart(revenueBookingsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue ($)',
                        data: [45000, 52000, 48000, 55000, 61000, 68000],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    }, {
                        label: 'Bookings',
                        data: [1250, 1380, 1290, 1450, 1580, 1720],
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Booking Status Chart
        const bookingStatusCtx = document.getElementById('bookingStatusChart');
        if (bookingStatusCtx) {
            new Chart(bookingStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Active', 'Cancelled', 'No Show'],
                    datasets: [{
                        data: [1245, 387, 88, 25],
                        backgroundColor: ['#28a745', '#007bff', '#dc3545', '#6c757d'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Hourly Usage Chart
        const hourlyUsageCtx = document.getElementById('hourlyUsageChart');
        if (hourlyUsageCtx) {
            new Chart(hourlyUsageCtx, {
                type: 'bar',
                data: {
                    labels: ['6AM', '7AM', '8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM', '9PM', '10PM'],
                    datasets: [{
                        label: 'Occupancy %',
                        data: [25, 45, 78, 92, 87, 75, 82, 79, 73, 68, 71, 83, 88, 76, 62, 45, 28],
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        borderColor: '#007bff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Report generation functions
    function generateReport(format) {
        alert(`Generating ${format.toUpperCase()} report... This would download a ${format} file in a real application.`);
    }

    function scheduleReport() {
        alert('Schedule report dialog would open here, allowing users to set up recurring reports.');
    }

    // Time period change handler
    document.getElementById('timePeriod').addEventListener('change', function() {
        const customInputs = document.querySelectorAll('#fromDate, #toDate');
        const isCustom = this.value === 'custom';

        customInputs.forEach(input => {
            input.style.display = isCustom ? 'block' : 'none';
            input.required = isCustom;
        });
    });
</script>
</body>

</html>
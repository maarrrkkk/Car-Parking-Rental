<?php
// This function fetches all data needed for the reports page based on a time period.
function getReportData($pdo, $period = 'month') {

    if (!$pdo) {
        return [
            'keyMetrics' => [
                'totalRevenue' => 0,
                'totalBookings' => 0,
                'averageOccupancy' => 0,
                'avgRevenuePerBooking' => 0,
            ],
            'monthlyPerformance' => [],
            'topSlots' => [],
            'charts' => [
                'revenueBookings' => ['labels' => [], 'revenue' => [], 'bookings' => []],
                'bookingStatus' => ['labels' => [], 'data' => []],
            ]
        ];
    }
    
    // Determine date range based on the selected period
    $dateConditions = "";
    switch ($period) {
        case 'today':
            $dateConditions = "DATE(b.paid_at) = CURDATE()";
            $bookingDateConditions = "DATE(b.created_at) = CURDATE()";
            break;
        case 'week':
            $dateConditions = "YEARWEEK(b.paid_at, 1) = YEARWEEK(CURDATE(), 1)";
            $bookingDateConditions = "YEARWEEK(b.created_at, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'year':
            $dateConditions = "YEAR(b.paid_at) = YEAR(CURDATE())";
            $bookingDateConditions = "YEAR(b.created_at) = YEAR(CURDATE())";
            break;
        case 'month':
        default:
            $dateConditions = "YEAR(b.paid_at) = YEAR(CURDATE()) AND MONTH(b.paid_at) = MONTH(CURDATE())";
            $bookingDateConditions = "YEAR(b.created_at) = YEAR(CURDATE()) AND MONTH(b.created_at) = MONTH(CURDATE())";
            break;
    }

    $report = [
        'keyMetrics' => [
            'totalRevenue' => 0,
            'totalBookings' => 0,
            'averageOccupancy' => 0,
            'avgRevenuePerBooking' => 0,
        ],
        'monthlyPerformance' => [],
        'topSlots' => [],
        'charts' => [
            'revenueBookings' => ['labels' => [], 'revenue' => [], 'bookings' => []],
            'bookingStatus' => ['labels' => [], 'data' => []],
        ]
    ];

    try {
        // === Key Metrics ===
        $stmt = $pdo->prepare("SELECT SUM(b.amount) as totalRevenue, COUNT(b.id) as totalBookings FROM bookings b WHERE b.status = 'completed' AND $dateConditions");
        $stmt->execute();
        $metrics = $stmt->fetch(PDO::FETCH_ASSOC);
        $report['keyMetrics']['totalRevenue'] = $metrics['totalRevenue'] ?? 0;
        $report['keyMetrics']['totalBookings'] = $metrics['totalBookings'] ?? 0;
        if ($report['keyMetrics']['totalBookings'] > 0) {
            $report['keyMetrics']['avgRevenuePerBooking'] = $report['keyMetrics']['totalRevenue'] / $report['keyMetrics']['totalBookings'];
        }
        $totalSlots = $pdo->query("SELECT COUNT(*) FROM slots")->fetchColumn();
        $activeBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'active'")->fetchColumn();
        if ($totalSlots > 0) {
            $report['keyMetrics']['averageOccupancy'] = round(($activeBookings / $totalSlots) * 100, 1);
        }

        // === Monthly Performance Table & Revenue/Bookings Chart Data ===
        $stmt = $pdo->query("
            SELECT
                DATE_FORMAT(b.paid_at, '%b %Y') as month,
                SUM(b.amount) as revenue,
                COUNT(b.id) as bookings
            FROM bookings b
            WHERE b.status = 'completed' AND YEAR(b.paid_at) = YEAR(CURDATE())
            GROUP BY YEAR(b.paid_at), MONTH(b.paid_at)
            ORDER BY YEAR(b.paid_at), MONTH(b.paid_at)
            LIMIT 6
        ");
        $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $report['monthlyPerformance'] = $monthlyData;
        foreach (array_reverse($monthlyData) as $row) { // Reverse for chart order
            $report['charts']['revenueBookings']['labels'][] = $row['month'];
            $report['charts']['revenueBookings']['revenue'][] = $row['revenue'];
            $report['charts']['revenueBookings']['bookings'][] = $row['bookings'];
        }

        // === Top Performing Slots ===
        $stmt = $pdo->query("
            SELECT
                s.name,
                SUM(b.amount) as revenue,
                COUNT(b.id) as bookings
            FROM slots s
            JOIN bookings b ON s.id = b.slot_id
            WHERE b.status = 'completed'
            GROUP BY s.id
            ORDER BY revenue DESC
            LIMIT 3
        ");
        $report['topSlots'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // === Booking Status Distribution Chart ===
        $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
        $statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($statusData as $row) {
            $report['charts']['bookingStatus']['labels'][] = ucfirst($row['status']);
            $report['charts']['bookingStatus']['data'][] = $row['count'];
        }

    } catch (PDOException $e) {
        // Log error or handle it gracefully
        // For now, we return the empty $report array
    }

    return $report;
}
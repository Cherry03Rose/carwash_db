<?php
require 'connect.php';
session_start();

// Handle date range filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$customFrom = $_GET['from'] ?? '';
$customTo = $_GET['to'] ?? '';

// --- For earnings ---
$earningsConditions = ["p.status = 'paid'", "b.status = 'completed'", "p.paid_at IS NOT NULL"];

if ($filter === 'today') {
    $earningsConditions[] = "DATE(p.paid_at) = CURDATE()";
} elseif ($filter === 'week') {
    $earningsConditions[] = "YEARWEEK(p.paid_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $earningsConditions[] = "MONTH(p.paid_at) = MONTH(CURDATE()) AND YEAR(p.paid_at) = YEAR(CURDATE())";
} elseif ($filter === 'custom' && $customFrom && $customTo) {
    $earningsConditions[] = "DATE(p.paid_at) BETWEEN '$customFrom' AND '$customTo'";
}
$earningsWhereSQL = implode(" AND ", $earningsConditions);

// --- For bookings ---
$bookingWhereSQL = "1"; // Default: no filter

if ($filter === 'today') {
    $bookingWhereSQL = "DATE(b.updated_at) = CURDATE()";
} elseif ($filter === 'week') {
    $bookingWhereSQL = "YEARWEEK(b.updated_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $bookingWhereSQL = "MONTH(b.updated_at) = MONTH(CURDATE()) AND YEAR(b.updated_at) = YEAR(CURDATE())";
} elseif ($filter === 'custom' && $customFrom && $customTo) {
    $bookingWhereSQL = "DATE(b.updated_at) BETWEEN '$customFrom' AND '$customTo'";
}

// Booking statistics
$stmt = $pdo->query("
    SELECT 
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        COUNT(*) as total
    FROM bookings b
    WHERE $bookingWhereSQL
");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

$bookingStats = [
    'Pending' => $stats['pending'],
    'Approved' => $stats['approved'],
    'Completed' => $stats['completed'],
    'Cancelled' => $stats['cancelled']
];
$totalBookings = $stats['total'];

// Earnings total (based on p.paid_at)
$earningsStmt = $pdo->query("
    SELECT SUM(p.amount) AS total_earnings
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    WHERE $earningsWhereSQL
");
$earningsResult = $earningsStmt->fetch(PDO::FETCH_ASSOC);
$totalEarnings = $earningsResult['total_earnings'] ?? 0;

// Earnings Per Day (based on p.paid_at)
$earningsPerDayStmt = $pdo->query("
    SELECT DATE(p.paid_at) AS day, SUM(p.amount) AS earnings
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    WHERE $earningsWhereSQL
    GROUP BY day
    ORDER BY day ASC
");
$earningsPerDayResult = $earningsPerDayStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare chart data
$earningLabels = [];
$earningValues = [];

foreach ($earningsPerDayResult as $row) {
    $earningLabels[] = $row['day'];
    $earningValues[] = $row['earnings'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AutoWash Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <style>
      body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #f0f4f8, #e6ecf2);

        }
        
        .nav-bar {
      width: 250px;
      background: #202C45;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      padding: 2rem 1.5rem;
    }

        
        .nav-bar .nav-link {
            color: #ffffff;
            padding: 12px 20px;
            font-weight: 500;
            letter-spacing: 1px;
            display: block;
            border-left: 4px solid transparent;
        }
        
        .nav-bar .nav-link:hover,
        .nav-bar .nav-link.active {
            color: #E81C2E;
            background-color: rgba(255,255,255,0.05);
            border-left: 4px solid #E81C2E;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .nav-bar .btn-custom {
            background: #ffffff;
            color: #202C45;
            border: none;
            padding: 10px 30px;
            border-radius: 60px;
            font-weight: 500;
            transition: ease-out 0.5s;
            box-shadow: inset 0 0 0 0 #E81C2E;
            margin-bottom: 30px;
        }
        
        .nav-bar .btn-custom:hover {
            background: #E81C2E;
            color: #ffffff;
            box-shadow: inset 200px 0 0 0 #E81C2E;
        }

        .dashboard-container {
    padding: 30px 40px; /* Was 40px 60px */
}

    .card-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Always show 3 cards per row on wide screens */
    gap: 30px;
    margin-bottom: 40px;
}

             .summary-card {
    background: #fff;
    padding: 20px; /* Reduced from 30px */
    border-radius: 16px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.3s ease;
}
        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card h2 {
            font-size: 1.5rem;
            color: #202C45;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .summary-card p {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin: 0;
        }


        .graph-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: space-between;
        }

        .chart-box {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            flex: 1 1 48%;
            min-width: 300px;
            height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .chart-box canvas {
            width: 100% !important;
            height: 100% !important;
        }

        @media (max-width: 768px) {
            .graph-wrapper {
                flex-direction: column;
            }
            .chart-box {
                flex: 1 1 100%;
            }
        }

        .filter-buttons {
            margin-bottom: 20px;
        }

      @media (max-width: 992px) {
    .card-container {
        grid-template-columns: repeat(2, 1fr);
    }
    .chart-box {
        flex: 1 1 100%;
    }
}

@media (max-width: 576px) {
    .card-container {
        grid-template-columns: 1fr;
    }
    .dashboard-container {
        padding: 20px;
    }
    .filter-buttons {
        flex-direction: column;
        align-items: flex-start;
    }
    .filter-buttons .form-inline {
        width: 100%;
        margin-top: 10px;
    }
}
    </style>
</head>
<body>
    <div class="d-flex">
    <!-- Sidebar -->
    <nav class="nav-bar d-flex flex-column px-4 pt-5" style="width: 250px; min-height: 100vh;">
        <h4 class="text-white font-weight-bold mb-4">MENU</h4>
        <div class="admin-profile d-flex align-items-center mb-4">
            <div class="profile-circle bg-white text-dark d-flex align-items-center justify-content-center mr-3"
                style="width: 45px; height: 45px; border-radius: 50%; font-weight: bold;">
                <?php
                if (!empty($_SESSION['user_photo'])) {
                    echo '<img src="'.htmlspecialchars($_SESSION['user_photo']).'" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
                } else {
                    $nameParts = explode(' ', $_SESSION['user_name']);
                    $initials = strtoupper(substr($nameParts[0],0,1).(isset($nameParts[1])?substr($nameParts[1],0,1):''));
                    echo $initials;
                }
                ?>
            </div>
            <div class="text-white">
                <div style="font-weight:600;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></div>
                <div style="font-size:13px;">Administrator</div>
            </div>
        </div>
        <a href="admin_booking_summary.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_booking_summary.php'?'active':''; ?>">Dashboard</a>
        <a href="admin_bookings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_bookings.php'?'active':''; ?>">Bookings</a>
        <a href="admin_service.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_service.php'?'active':''; ?>">Services</a>
        <a href="admin_payment.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_payment.php'?'active':''; ?>">Payments</a>
        <div class="mt-auto pt-4">
            <a href="logout.php" class="btn btn-custom w-100">Logout</a>
        </div>
    </nav>

<div class="dashboard-container">
    <h2 class="text-center mb-4">Dashboard Summary</h2>

    <!-- Filter -->
    <div class="d-flex justify-content-between align-items-center flex-wrap filter-buttons">
        <div class="btn-group mb-2">
            <a href="?filter=today" class="btn btn-outline-primary <?= $filter === 'today' ? 'active' : '' ?>">Today</a>
            <a href="?filter=week" class="btn btn-outline-primary <?= $filter === 'week' ? 'active' : '' ?>">7 Days</a>
            <a href="?filter=month" class="btn btn-outline-primary <?= $filter === 'month' ? 'active' : '' ?>">30 Days</a>
        </div>
        <form method="get" class="form-inline mb-2">
            <input type="hidden" name="filter" value="custom">
            <label class="mr-2 font-weight-bold">From:</label>
            <input type="date" name="from" value="<?= htmlspecialchars($customFrom) ?>" class="form-control mr-2">
            <label class="mr-2 font-weight-bold">To:</label>
            <input type="date" name="to" value="<?= htmlspecialchars($customTo) ?>" class="form-control mr-2">
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="card-container">
        <div class="summary-card">
            <h2>Total Earnings</h2>
            <p>₱<?= number_format($totalEarnings, 2) ?></p>
        </div>
        <div class="summary-card">
            <h2>Total Bookings</h2>
            <p><?= $totalBookings ?></p>
        </div>
        <div class="summary-card">
            <h2>Completed</h2>
            <p><?= $bookingStats['Completed'] ?></p>
        </div>
        <div class="summary-card">
            <h2>Pending</h2>
            <p><?= $bookingStats['Pending'] ?></p>
        </div>
        <div class="summary-card">
            <h2>Cancelled</h2>
            <p><?= $bookingStats['Cancelled'] ?></p>
        </div>
        <div class="summary-card">
            <h2>Approved</h2>
            <p><?= $bookingStats['Approved'] ?></p>
        </div>
    </div>

    <!-- Graphs -->
    <div class="graph-wrapper">
        <div class="chart-box">
            <canvas id="bookingChart"></canvas>
        </div>
        <div class="chart-box">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>
</div>

<!-- Charts Script -->
<script>
const bookingCtx = document.getElementById('bookingChart').getContext('2d');
new Chart(bookingCtx, {
    type: 'bar',
    data: {
        labels: ['Pending', 'Approved', 'Completed', 'Cancelled'],
        datasets: [{
            label: 'Bookings',
            data: [
                <?= $bookingStats['Pending'] ?>,
                <?= $bookingStats['Approved'] ?>,
                <?= $bookingStats['Completed'] ?>,
                <?= $bookingStats['Cancelled'] ?>
            ],
            backgroundColor: ['#4B77BE', '#5C97BF', '#3A539B', '#ABB7B7'],
            borderRadius: 12,
            barPercentage: 0.6
        }]
    },
    options: {
        responsive: true,
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        },
        plugins: {
            title: {
                display: true,
                text: 'Booking Status Overview',
                font: {
                    size: 18,
                    weight: '600'
                },
                padding: { top: 10, bottom: 20 },
                color: '#202C45'
            },
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

const earningsCtx = document.getElementById('earningsChart').getContext('2d');
new Chart(earningsCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($earningLabels) ?>,
        datasets: [{
            label: 'Daily Earnings',
            data: <?= json_encode($earningValues) ?>,
            borderColor: '#054773',
            backgroundColor: 'rgba(5, 71, 115, 0.2)',
            fill: true,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#054773'
        }]
    },
    options: {
        responsive: true,
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        },
        plugins: {
            title: {
                display: true,
                text: 'Earnings Over Time',
                font: {
                    size: 18,
                    weight: '600'
                },
                padding: { top: 10, bottom: 20 },
                color: '#202C45'
            },
            legend: { display: false }
        },
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'day',
                    tooltipFormat: 'MMM dd'
                }
            },
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
</body>
</html>

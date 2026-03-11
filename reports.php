<?php
require_once '../php/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get booking statistics
$yearly_stats = $conn->query("
    SELECT DATE_TRUNC(date, MONTH) as month, COUNT(*) as count
    FROM reservations
    WHERE YEAR(date) = YEAR(CURDATE())
    GROUP BY DATE_TRUNC(date, MONTH)
    ORDER BY month DESC
");

// Get status statistics
$status_stats = $conn->query("
    SELECT status, COUNT(*) as count
    FROM reservations
    GROUP BY status
");

// Get monthly revenue (based on number of confirmed bookings)
$monthly_stats = $conn->query("
    SELECT DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as total_bookings, 
           SUM(guests) as total_guests
    FROM reservations
    WHERE status IN ('confirmed', 'pending')
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="sidebar-brand">🍽️ Admin Panel</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="manage_bookings.php">📋 Manage Bookings</a></li>
                <li><a href="manage_tables.php">🪑 Manage Tables</a></li>
                <li><a href="manage_slots.php">⏰ Manage Time Slots</a></li>
                <li><a href="view_users.php">👥 View Users</a></li>
                <li><a href="reports.php" class="active">📊 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <h2>📊 Reports & Analytics</h2>

            <div class="report-section">
                <h3>Booking Status Distribution</h3>
                <?php if ($status_stats->num_rows > 0): ?>
                    <table class="admin-table" style="max-width: 500px;">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $statuses = [];
                            $status_stats->data_seek(0);
                            while ($row = $status_stats->fetch_assoc()): 
                                $statuses[$row['status']] = $row['count'];
                            ?>
                                <tr>
                                    <td><span class="status-badge <?php echo $row['status']; ?>"><?php echo strtoupper($row['status']); ?></span></td>
                                    <td><?php echo $row['count']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="report-section">
                <h3>Monthly Booking Statistics</h3>
                <?php if ($monthly_stats->num_rows > 0): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Bookings</th>
                                <th>Total Guests</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $monthly_stats->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
                                    <td><?php echo $row['total_bookings']; ?></td>
                                    <td><?php echo $row['total_guests']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="report-section">
                <h3>Quick Stats</h3>
                <div class="stats-grid">
                    <?php
                    $today_count = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE DATE(date) = CURDATE()")->fetch_assoc()['count'];
                    $week_count = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE WEEK(date) = WEEK(CURDATE())")->fetch_assoc()['count'];
                    $month_count = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE MONTH(date) = MONTH(CURDATE())")->fetch_assoc()['count'];
                    $avg_guests = $conn->query("SELECT AVG(guests) as avg FROM reservations")->fetch_assoc()['avg'];
                    ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $today_count; ?></div>
                        <div class="stat-label">Bookings Today</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $week_count; ?></div>
                        <div class="stat-label">This Week</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $month_count; ?></div>
                        <div class="stat-label">This Month</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($avg_guests, 1); ?></div>
                        <div class="stat-label">Avg. Guests/Booking</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .report-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</body>
</html>

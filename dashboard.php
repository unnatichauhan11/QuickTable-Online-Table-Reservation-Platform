<?php
require_once '../php/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM reservations")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'")->fetch_assoc()['count'];
$confirmed_bookings = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'confirmed'")->fetch_assoc()['count'];
$today_bookings = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE DATE(date) = CURDATE()")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Restaurant Booking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="sidebar-brand">🍽️ Admin Panel</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="manage_bookings.php">📋 Manage Bookings</a></li>
                <li><a href="manage_tables.php">🪑 Manage Tables</a></li>
                <li><a href="manage_slots.php">⏰ Manage Time Slots</a></li>
                <li><a href="view_users.php">👥 View Users</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <div class="admin-header">
                <h1>📊 Admin Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
            </div>

            <div class="stats-grid admin-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_bookings; ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #ff9800;"><?php echo $pending_bookings; ?></div>
                    <div class="stat-label">Pending Approvals</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #4caf50;"><?php echo $confirmed_bookings; ?></div>
                    <div class="stat-label">Confirmed Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $today_bookings; ?></div>
                    <div class="stat-label">Today's Bookings</div>
                </div>
            </div>

            <div class="action-buttons admin-actions">
                <a href="manage_bookings.php" class="btn btn-primary">📋 Review Pending Bookings</a>
                <a href="manage_tables.php" class="btn btn-secondary">🪑 Manage Tables</a>
                <a href="view_users.php" class="btn btn-secondary">👥 View Users</a>
                <a href="reports.php" class="btn btn-secondary">📊 Generate Reports</a>
            </div>

            <div class="recent-section">
                <h2>📋 Recent Bookings</h2>
                <?php
                $recent_query = $conn->query("
                    SELECT r.id, r.date, r.time, r.guests, r.status, u.name, u.email
                    FROM reservations r
                    JOIN users u ON r.user_id = u.id
                    ORDER BY r.created_at DESC
                    LIMIT 10
                ");
                ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent_query->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($row['date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($row['time'])); ?></td>
                                <td><?php echo $row['guests']; ?></td>
                                <td><span class="status-badge <?php echo $row['status']; ?>"><?php echo strtoupper($row['status']); ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .admin-sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 20px;
            font-size: 1.3em;
            font-weight: bold;
            border-bottom: 2px solid #34495e;
            margin-bottom: 20px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: background-color 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: #34495e;
            border-left-color: #3498db;
        }

        .admin-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }

        .admin-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .admin-header h1 {
            margin: 0;
            margin-bottom: 5px;
        }

        .admin-header p {
            margin: 0;
            color: #666;
        }

        .admin-stats {
            margin-bottom: 30px;
        }

        .admin-actions {
            margin-bottom: 30px;
        }

        .recent-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .admin-table thead {
            background-color: #f9f9f9;
        }

        .admin-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }

        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .admin-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .admin-content {
                margin-left: 0;
            }
        }
    </style>
</body>
</html>

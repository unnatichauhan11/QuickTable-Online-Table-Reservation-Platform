<?php
require_once '../php/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get all time slots
$slots_query = $conn->query("SELECT * FROM time_slots ORDER BY slot_time");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Time Slots - Admin Panel</title>
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
                <li><a href="manage_slots.php" class="active">⏰ Manage Time Slots</a></li>
                <li><a href="view_users.php">👥 View Users</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <h2>⏰ Manage Time Slots</h2>

            <h3>Available Time Slots</h3>
            <p>These are the booking time slots available for customers.</p>

            <?php if ($slots_query->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Time Slot</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($slot = $slots_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('g:i A', strtotime($slot['slot_time'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $slot['status']; ?>">
                                        <?php echo strtoupper($slot['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No time slots found.</p>
            <?php endif; ?>

            <div style="margin-top: 30px; padding: 20px; background: white; border-radius: 8px;">
                <p><strong>Note:</strong> Time slots are configured in the database. To modify database time slots, run the SQL queries directly.</p>
                <p><strong>Default Slots:</strong> 11:00 AM, 11:30 AM, 12:00 PM, 12:30 PM, 1:00 PM, 5:00 PM, 5:30 PM, 6:00 PM, 6:30 PM, 7:00 PM, 7:30 PM, 8:00 PM</p>
            </div>
        </div>
    </div>
</body>
</html>

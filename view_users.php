<?php
require_once '../php/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get all users
$users_query = $conn->query("
    SELECT u.id, u.name, u.email, u.contact, u.created_at, COUNT(r.id) as total_bookings
    FROM users u
    LEFT JOIN reservations r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users - Admin Panel</title>
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
                <li><a href="view_users.php" class="active">👥 View Users</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <h2>👥 View Users</h2>

            <?php if ($users_query->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Total Bookings</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users_query->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['contact']); ?></td>
                                <td><?php echo $user['total_bookings']; ?></td>
                                <td><?php echo date('F d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

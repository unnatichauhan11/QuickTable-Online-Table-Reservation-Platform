<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get user's booking statistics
$user_query = $conn->prepare("SELECT email, contact FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_info = $user_result->fetch_assoc();
$user_query->close();

// Get bookings stats
$stats_query = $conn->prepare("
    SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM reservations WHERE user_id = ?
");
$stats_query->bind_param("i", $user_id);
$stats_query->execute();
$stats_result = $stats_query->get_result();
$stats = $stats_result->fetch_assoc();
$stats_query->close();

// Get upcoming bookings
$upcoming_query = $conn->prepare("
    SELECT id, date, time, guests, status 
    FROM reservations 
    WHERE user_id = ? AND date >= CURDATE() AND status != 'cancelled'
    ORDER BY date ASC, time ASC
    LIMIT 5
");
$upcoming_query->bind_param("i", $user_id);
$upcoming_query->execute();
$upcoming_result = $upcoming_query->get_result();
$upcoming_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Restaurant Booking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">🍽️ Restaurant Booking</div>
            <ul class="nav-menu">
                <li><a href="customer_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="book_table.php">Book Table</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
            <p>Manage your restaurant reservations here</p>
        </div>

        <div class="user-info-section">
            <div class="info-item">
                <strong>📧 Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?>
            </div>
            <div class="info-item">
                <strong>📱 Contact:</strong> <?php echo htmlspecialchars($user_info['contact']); ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_bookings'] ?? 0; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #4caf50;"><?php echo $stats['confirmed'] ?? 0; ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #ff9800;"><?php echo $stats['pending'] ?? 0; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #f44336;"><?php echo $stats['rejected'] ?? 0; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="book_table.php" class="btn btn-primary btn-lg">📅 Book a New Table</a>
            <a href="my_bookings.php" class="btn btn-secondary btn-lg">📋 View All Bookings</a>
        </div>

        <div class="upcoming-section">
            <h2>Upcoming Bookings 📅</h2>
            <?php if ($upcoming_result->num_rows > 0): ?>
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $upcoming_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('F d, Y', strtotime($booking['date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($booking['time'])); ?></td>
                                <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo htmlspecialchars($booking['status']); ?>">
                                        <?php echo strtoupper(htmlspecialchars($booking['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No upcoming bookings. <a href="book_table.php">Book a table now!</a></p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 Restaurant Booking System. All rights reserved.</p>
    </footer>
</body>
</html>

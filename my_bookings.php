<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = (int)$_POST['booking_id'];

    // Verify the booking belongs to this user and can be cancelled
    $check = $conn->prepare("SELECT status, date, time FROM reservations WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $booking_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 1) {
        $booking = $result->fetch_assoc();
        $booking_datetime = $booking['date'] . ' ' . $booking['time'];

        if (strtotime($booking_datetime) > time()) {
            // Can cancel if booking is in future
            $update = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
            $update->bind_param("i", $booking_id);

            if ($update->execute()) {
                $success = "✅ Booking cancelled successfully!";
            } else {
                $error = "Failed to cancel booking!";
            }
            $update->close();
        } else {
            $error = "Cannot cancel past bookings!";
        }
    } else {
        $error = "Booking not found!";
    }
    $check->close();
}

// Get user's bookings
$query = $conn->prepare("
    SELECT id, guests, date, time, status, special_requests, created_at 
    FROM reservations 
    WHERE user_id = ? 
    ORDER BY date DESC, time DESC
");
$query->bind_param("i", $user_id);
$query->execute();
$bookings_result = $query->get_result();
$query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Restaurant Booking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">🍽️ Restaurant Booking</div>
            <ul class="nav-menu">
                <li><a href="customer_dashboard.php">Dashboard</a></li>
                <li><a href="book_table.php">Book Table</a></li>
                <li><a href="my_bookings.php" class="active">My Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>📋 My Bookings</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="bookings-container">
            <?php if ($bookings_result->num_rows > 0): ?>
                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                    <div class="booking-card status-<?php echo htmlspecialchars($booking['status']); ?>">
                        <div class="booking-header">
                            <h3>Booking #<?php echo htmlspecialchars($booking['id']); ?></h3>
                            <span class="status-badge <?php echo htmlspecialchars($booking['status']); ?>">
                                <?php echo strtoupper(htmlspecialchars($booking['status'])); ?>
                            </span>
                        </div>

                        <div class="booking-details">
                            <div class="detail-item">
                                <strong>📅 Date:</strong>
                                <?php echo date('F d, Y', strtotime($booking['date'])); ?>
                            </div>
                            <div class="detail-item">
                                <strong>⏰ Time:</strong>
                                <?php echo date('g:i A', strtotime($booking['time'])); ?>
                            </div>
                            <div class="detail-item">
                                <strong>👥 Guests:</strong>
                                <?php echo htmlspecialchars($booking['guests']); ?>
                            </div>

                            <?php if (!empty($booking['special_requests'])): ?>
                                <div class="detail-item">
                                    <strong>📝 Special Requests:</strong>
                                    <?php echo htmlspecialchars($booking['special_requests']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="detail-item">
                                <strong>📌 Booked on:</strong>
                                <?php echo date('F d, Y g:i A', strtotime($booking['created_at'])); ?>
                            </div>
                        </div>

                        <?php
                        $booking_datetime = $booking['date'] . ' ' . $booking['time'];
                        $can_cancel = strtotime($booking_datetime) > time() && $booking['status'] != 'cancelled';
                        ?>

                        <?php if ($can_cancel): ?>
                            <form method="POST" class="cancel-form" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                <button type="submit" name="cancel_booking" class="btn btn-danger btn-sm">Cancel Booking</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>You haven't made any bookings yet.</p>
                    <a href="book_table.php" class="btn btn-primary">Book a Table Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .booking-card {
            background: white;
            border-left: 5px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .booking-card.status-pending {
            border-left-color: #ff9800;
        }

        .booking-card.status-confirmed {
            border-left-color: #4caf50;
        }

        .booking-card.status-rejected {
            border-left-color: #f44336;
        }

        .booking-card.status-cancelled {
            border-left-color: #999;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            color: white;
        }

        .status-badge.pending {
            background-color: #ff9800;
        }

        .status-badge.confirmed {
            background-color: #4caf50;
        }

        .status-badge.rejected {
            background-color: #f44336;
        }

        .status-badge.cancelled {
            background-color: #999;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            font-size: 0.95em;
        }
    </style>
</body>
</html>

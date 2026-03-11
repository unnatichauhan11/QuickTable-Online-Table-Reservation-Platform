<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

// Get minimum date (today)
$min_date = date('Y-m-d', strtotime('+1 day'));
$max_date = date('Y-m-d', strtotime('+90 days'));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guests = (int)($_POST['guests'] ?? 0);
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $special_requests = trim($_POST['special_requests'] ?? '');

    // Validation
    if ($guests < 1 || $guests > 12) {
        $error = "Number of guests must be between 1 and 12!";
    } elseif (empty($date) || empty($time)) {
        $error = "Please select both date and time!";
    } elseif (strtotime($date) < strtotime('tomorrow')) {
        $error = "Please select a future date!";
    } else {
        // Check availability
        $check_avail = $conn->prepare("
            SELECT COUNT(*) as count FROM reservations 
            WHERE date = ? AND time = ? AND status IN ('confirmed', 'pending')
        ");
        $check_avail->bind_param("ss", $date, $time);
        $check_avail->execute();
        $avail_result = $check_avail->get_result();
        $avail_row = $avail_result->fetch_assoc();
        $check_avail->close();

        if ($avail_row['count'] > 0) {
            $error = "This time slot is no longer available. Please select another.";
        } else {
            // Insert reservation
            $insert = $conn->prepare("
                INSERT INTO reservations (user_id, guests, date, time, special_requests, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $insert->bind_param("iisss", $user_id, $guests, $date, $time, $special_requests);

            if ($insert->execute()) {
                $success = "✅ Booking request submitted successfully! Admin will confirm your reservation.";
                $_POST = array(); // Clear form
            } else {
                $error = "Booking failed! Please try again.";
            }
            $insert->close();
        }
    }
}

// Get user info
$user_query = $conn->prepare("SELECT name, email, contact FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_info = $user_result->fetch_assoc();
$user_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - Restaurant Booking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">🍽️ Restaurant Booking</div>
            <ul class="nav-menu">
                <li><a href="customer_dashboard.php">Dashboard</a></li>
                <li><a href="book_table.php" class="active">Book Table</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="booking-wrapper">
            <h2>📅 Book Your Table</h2>
            <p class="subtitle">Reserve a table at our restaurant</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" id="bookingForm" class="booking-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" value="<?php echo htmlspecialchars($user_info['name']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact">Contact</label>
                        <input type="tel" id="contact" value="<?php echo htmlspecialchars($user_info['contact']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="guests">Number of Guests *</label>
                        <select id="guests" name="guests" required>
                            <option value="">Select number of guests</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date *</label>
                        <input type="date" id="date" name="date" min="<?php echo $min_date; ?>" max="<?php echo $max_date; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="time">Time *</label>
                        <select id="time" name="time" required>
                            <option value="">Select time</option>
                            <option value="11:00:00">11:00 AM</option>
                            <option value="11:30:00">11:30 AM</option>
                            <option value="12:00:00">12:00 PM</option>
                            <option value="12:30:00">12:30 PM</option>
                            <option value="13:00:00">1:00 PM</option>
                            <option value="17:00:00">5:00 PM</option>
                            <option value="17:30:00">5:30 PM</option>
                            <option value="18:00:00">6:00 PM</option>
                            <option value="18:30:00">6:30 PM</option>
                            <option value="19:00:00">7:00 PM</option>
                            <option value="19:30:00">7:30 PM</option>
                            <option value="20:00:00">8:00 PM</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="special_requests">Special Requests (Optional)</label>
                    <textarea id="special_requests" name="special_requests" rows="4" placeholder="Any special requests or dietary requirements?"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Book Table</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/booking.js"></script>
</body>
</html>

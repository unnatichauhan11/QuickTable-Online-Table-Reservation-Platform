<?php
require_once '../php/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_booking'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = $_POST['status'];

    if (in_array($new_status, ['confirmed', 'rejected', 'pending', 'cancelled'])) {
        $update = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $update->bind_param("si", $new_status, $booking_id);

        if ($update->execute()) {
            $success = "✅ Booking status updated successfully!";
        } else {
            $error = "Failed to update booking!";
        }
        $update->close();
    }
}

// Handle filter
$filter_date = $_GET['filter_date'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';

$query = "
    SELECT r.id, r.date, r.time, r.guests, r.status, r.special_requests, 
           r.created_at, u.name, u.email, u.contact
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    WHERE 1=1
";

$params = array();
$types = '';

if ($filter_date) {
    $query .= " AND DATE(r.date) = ?";
    $params[] = $filter_date;
    $types .= 's';
}

if ($filter_status && $filter_status !== 'all') {
    $query .= " AND r.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

$query .= " ORDER BY r.date DESC, r.time DESC";

$stmt = $conn->prepare($query);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$bookings_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="sidebar-brand">🍽️ Admin Panel</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="manage_bookings.php" class="active">📋 Manage Bookings</a></li>
                <li><a href="manage_tables.php">🪑 Manage Tables</a></li>
                <li><a href="manage_slots.php">⏰ Manage Time Slots</a></li>
                <li><a href="view_users.php">👥 View Users</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <h2>📋 Manage Bookings</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="filter-section">
                <form method="GET" id="filterForm" class="filter-form">
                    <div class="form-group">
                        <label for="filter_date">Filter by Date:</label>
                        <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                    </div>

                    <div class="form-group">
                        <label for="filter_status">Filter by Status:</label>
                        <select id="filter_status" name="filter_status">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $filter_status === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="rejected" <?php echo $filter_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    <a href="manage_bookings.php" class="btn btn-secondary">Clear Filters</a>
                </form>
            </div>

            <?php if ($bookings_result->num_rows > 0): ?>
                <table class="admin-table bookings-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Special Requests</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                            <tr class="booking-row-<?php echo $booking['status']; ?>">
                                <td>#<?php echo $booking['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($booking['email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['contact']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($booking['date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($booking['time'])); ?></td>
                                <td><?php echo $booking['guests']; ?></td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <select name="status" class="status-select" onchange="this.form.submit();">
                                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="rejected" <?php echo $booking['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_booking" style="display:none;">Update</button>
                                    </form>
                                </td>
                                <td><?php echo !empty($booking['special_requests']) ? htmlspecialchars(substr($booking['special_requests'], 0, 30)) . '...' : '-'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="showDetails(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
                                        View
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No bookings found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Booking Details</h2>
            <div id="modalBody"></div>
        </div>
    </div>

    <style>
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-form .form-group {
            margin: 0;
            flex: 1;
            min-width: 150px;
        }

        .bookings-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .inline-form {
            margin: 0;
        }

        .status-select {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 0.9em;
        }

        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }
    </style>

    <script>
        function showDetails(booking) {
            let html = `
                <p><strong>Customer Name:</strong> ${booking.name}</p>
                <p><strong>Email:</strong> ${booking.email}</p>
                <p><strong>Contact:</strong> ${booking.contact}</p>
                <p><strong>Date:</strong> ${new Date(booking.date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</p>
                <p><strong>Time:</strong> ${new Date('2000-01-01 ' + booking.time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</p>
                <p><strong>Guests:</strong> ${booking.guests}</p>
                <p><strong>Status:</strong> ${booking.status.toUpperCase()}</p>
                <p><strong>Special Requests:</strong> ${booking.special_requests || 'None'}</p>
                <p><strong>Booked On:</strong> ${new Date(booking.created_at).toLocaleString()}</p>
            `;
            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('detailsModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }
    </script>
</body>
</html>

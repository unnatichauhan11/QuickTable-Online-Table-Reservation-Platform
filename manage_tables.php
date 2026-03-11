<?php
require_once '../php/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Handle add table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_table'])) {
    $table_number = (int)$_POST['table_number'];
    $capacity = (int)$_POST['capacity'];

    if ($table_number > 0 && $capacity > 0) {
        $insert = $conn->prepare("INSERT INTO restaurant_tables (table_number, capacity) VALUES (?, ?)");
        $insert->bind_param("ii", $table_number, $capacity);

        if ($insert->execute()) {
            $success = "✅ Table added successfully!";
        } else {
            $error = "Failed to add table!";
        }
        $insert->close();
    } else {
        $error = "Invalid table number or capacity!";
    }
}

// Handle delete table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_table'])) {
    $table_id = (int)$_POST['table_id'];
    $delete = $conn->prepare("DELETE FROM restaurant_tables WHERE id = ?");
    $delete->bind_param("i", $table_id);

    if ($delete->execute()) {
        $success = "✅ Table deleted successfully!";
    } else {
        $error = "Failed to delete table!";
    }
    $delete->close();
}

// Get all tables
$tables_query = $conn->query("SELECT * FROM restaurant_tables ORDER BY table_number");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="sidebar-brand">🍽️ Admin Panel</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="manage_bookings.php">📋 Manage Bookings</a></li>
                <li><a href="manage_tables.php" class="active">🪑 Manage Tables</a></li>
                <li><a href="manage_slots.php">⏰ Manage Time Slots</a></li>
                <li><a href="view_users.php">👥 View Users</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <h2>🪑 Manage Tables</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="add-table-section">
                <h3>Add New Table</h3>
                <form method="POST" class="form-inline">
                    <div class="form-group">
                        <label for="table_number">Table Number:</label>
                        <input type="number" id="table_number" name="table_number" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Capacity (Guests):</label>
                        <input type="number" id="capacity" name="capacity" min="1" max="20" required>
                    </div>

                    <button type="submit" name="add_table" class="btn btn-primary">Add Table</button>
                </form>
            </div>

            <h3>Restaurant Tables</h3>
            <?php if ($tables_query->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Table Number</th>
                            <th>Capacity</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($table = $tables_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $table['table_number']; ?></td>
                                <td><?php echo $table['capacity']; ?> Guests</td>
                                <td><?php echo date('F d, Y', strtotime($table['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this table?');">
                                        <input type="hidden" name="table_id" value="<?php echo $table['id']; ?>">
                                        <button type="submit" name="delete_table" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No tables found.</p>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .add-table-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-inline {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-inline .form-group {
            margin: 0;
        }

        .form-inline input,
        .form-inline select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</body>
</html>

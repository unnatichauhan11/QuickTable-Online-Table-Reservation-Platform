<?php
require_once 'config.php';

header('Content-Type: application/json');

if (isset($_POST['date']) && isset($_POST['guests'])) {
    $date = $_POST['date'];
    $guests = (int)$_POST['guests'];
    $response = array();

    // Get all tables that can accommodate the guests
    $tables_query = $conn->prepare("SELECT id FROM restaurant_tables WHERE capacity >= ?");
    $tables_query->bind_param("i", $guests);
    $tables_query->execute();
    $tables_result = $tables_query->get_result();
    $suitable_tables = array();

    while ($table = $tables_result->fetch_assoc()) {
        $suitable_tables[] = $table['id'];
    }
    $tables_query->close();

    if (count($suitable_tables) == 0) {
        echo json_encode(['available' => false, 'message' => 'No suitable tables for this guest count']);
        exit;
    }

    // Get booked times for suitable tables on this date
    $placeholders = implode(',', array_fill(0, count($suitable_tables), '?'));
    $booked_query = $conn->prepare("
        SELECT DISTINCT time FROM reservations 
        WHERE date = ? 
        AND status IN ('confirmed', 'pending')
    ");
    $booked_query->bind_param("s", $date);
    $booked_query->execute();
    $booked_result = $booked_query->get_result();
    $booked_times = array();

    while ($row = $booked_result->fetch_assoc()) {
        $booked_times[] = $row['time'];
    }
    $booked_query->close();

    // Get all available time slots
    $slots_query = $conn->query("SELECT slot_time FROM time_slots WHERE status = 'available' ORDER BY slot_time");
    $available_slots = array();

    while ($slot = $slots_query->fetch_assoc()) {
        $time = $slot['slot_time'];
        if (!in_array($time, $booked_times)) {
            // Check if the date is in the future or today
            $now = date('Y-m-d H:i:s');
            $slot_datetime = $date . ' ' . $time;

            if (strtotime($slot_datetime) >= strtotime($now)) {
                $available_slots[] = $time;
            }
        }
    }

    if (count($available_slots) > 0) {
        echo json_encode(['available' => true, 'slots' => $available_slots]);
    } else {
        echo json_encode(['available' => false, 'message' => 'No available time slots for this date']);
    }
} else {
    echo json_encode(['error' => 'Missing parameters']);
}
?>

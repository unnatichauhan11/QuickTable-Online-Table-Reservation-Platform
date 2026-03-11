-- Create Database
CREATE DATABASE IF NOT EXISTS restaurant_booking;
USE restaurant_booking;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contact VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reservations Table
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    guests INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'rejected', 'cancelled') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tables Table (for restaurant table management)
CREATE TABLE restaurant_tables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_number INT NOT NULL UNIQUE,
    capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Time Slots Table
CREATE TABLE time_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slot_time TIME NOT NULL UNIQUE,
    status ENUM('available', 'unavailable') DEFAULT 'available'
);

-- Insert default time slots
INSERT INTO time_slots (slot_time, status) VALUES
('11:00:00', 'available'),
('11:30:00', 'available'),
('12:00:00', 'available'),
('12:30:00', 'available'),
('13:00:00', 'available'),
('17:00:00', 'available'),
('17:30:00', 'available'),
('18:00:00', 'available'),
('18:30:00', 'available'),
('19:00:00', 'available'),
('19:30:00', 'available'),
('20:00:00', 'available');

-- Insert default tables
INSERT INTO restaurant_tables (table_number, capacity) VALUES
(1, 2),
(2, 2),
(3, 4),
(4, 4),
(5, 6),
(6, 8),
(7, 6),
(8, 4);

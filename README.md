# Restaurant Table Reservation System

A complete web-based restaurant table booking system built with HTML, CSS, JavaScript, PHP, and MySQL.

## 🎯 Project Features

### User Side (Customer Panel)
- **Registration & Login**: Secure user authentication with password hashing
- **Book a Table**: Easy table booking with availability checking
- **View Bookings**: Track all reservations with status updates
- **Cancel Booking**: Cancel pending reservations before the reservation time
- **Booking Confirmation**: Instant confirmation messages

### Admin Panel
- **Dashboard**: Overview of all statistics and recent bookings
- **Manage Bookings**: Review, approve, or reject customer bookings
- **Filter Bookings**: Filter by date and status
- **Manage Tables**: Add/delete restaurant tables and define capacity
- **View Users**: See all registered users and their booking history
- **Reports**: Booking statistics and analytics

### Database
- **Users Table**: User registration and login data
- **Reservations Table**: All booking information
- **Restaurant Tables**: Table capacity and availability
- **Time Slots**: Available booking time slots

## 📋 Project Structure

```
restaurant booking/
├── php/
│   ├── config.php                 # Database configuration
│   ├── database.sql               # Database schema
│   ├── register.php               # User registration
│   ├── login.php                  # User login
│   ├── customer_dashboard.php      # Customer dashboard
│   ├── book_table.php             # Booking form
│   ├── my_bookings.php            # View/cancel bookings
│   ├── check_availability.php     # AJAX availability checker
│   └── logout.php                 # Logout
├── admin/
│   ├── login.php                  # Admin login
│   ├── dashboard.php              # Admin dashboard
│   ├── manage_bookings.php        # Manage reservations
│   ├── manage_tables.php          # Manage restaurant tables
│   ├── manage_slots.php           # Manage time slots
│   ├── view_users.php             # View all users
│   ├── reports.php                # Analytics & reports
│   └── logout.php                 # Admin logout
├── assets/
│   ├── css/
│   │   └── style.css              # All styling
│   └── js/
│       ├── validation.js          # Form validation
│       └── booking.js             # Booking functionality
└── README.md                       # This file
```

## 🚀 Setup Instructions

### 1. Database Setup
- Import the `database.sql` file into your MySQL database
- Create a database named `restaurant_booking`

### 2. Configuration
- Edit `php/config.php` with your database credentials:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', 'your_password');
  define('DB_NAME', 'restaurant_booking');
  ```

### 3. Run the Application
- Place the entire folder in your web server directory (htdocs for XAMPP)
- Access via: `http://localhost/restaurant%20booking/php/register.php`

## 👤 Default Credentials

### Customer
- **Register**: Create a new account on registration page

### Admin
- **Username**: `admin`
- **Password**: `admin123`
- **Access**: `http://localhost/restaurant%20booking/admin/login.php`

## 💻 Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (with PHP support)

## 📱 Responsive Design

The system is fully responsive and works seamlessly on:
- Desktop browsers
- Tablets
- Mobile devices

## 🔐 Security Features

- Password hashing using PHP `password_hash()`
- SQL injection prevention with prepared statements
- Session management
- Form validation on both client and server side
- CSRF protection ready (can be enhanced)

## ✨ Key Features Implemented

✅ User registration with validation
✅ Secure login with password hashing
✅ Real-time booking availability checking
✅ Booking status management
✅ Admin approval/rejection system
✅ Booking cancellation feature
✅ Admin dashboard with statistics
✅ Responsive UI design
✅ Form validation (client & server side)
✅ Table and time slot management

## 🎓 Advanced Features (For Higher Marks)

- AJAX real-time availability checking
- Status filtering for bookings
- Date filtering for bookings
- Admin analytics and reports
- Responsive admin panel
- Guest count-based table selection
- Special requests field for bookings

## 📝 Database Schema

### Users Table
```
id | name | email | contact | password | created_at
```

### Reservations Table
```
id | user_id | guests | date | time | status | special_requests | created_at
```

### Restaurant Tables Table
```
id | table_number | capacity | created_at
```

### Time Slots Table
```
id | slot_time | status
```

## 🐛 Troubleshooting

1. **Database Connection Error**: Check database credentials in `config.php`
2. **Page Not Found**: Ensure PHP files are in correct directory structure
3. **Styles Not Loading**: Check CSS file path in HTML
4. **Admin Login Failed**: Verify username/password (admin/admin123)
5. **Booking Not Showing**: Clear browser cache and check database

## 📧 Future Enhancements

- Email notifications (PHPMailer integration)
- SMS confirmations
- Payment gateway integration
- Calendar view for bookings
- Auto-cancellation for unconfirmed bookings
- Email reminder before booking time
- User profile management
- Password reset functionality
- Two-factor authentication

## 📄 License

This project is free to use for educational purposes.

## 👨‍💼 Author

Restaurant Booking Management System - Educational Project

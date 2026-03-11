# 🍽️ Restaurant Table Reservation System - Installation Guide

## Prerequisites
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache with PHP support (XAMPP/WAMP/LAMP)
- **Browser**: Modern browser (Chrome, Firefox, Safari, Edge)

## Step-by-Step Installation

### 1. Download & Setup

```bash
# The project is already in:
# C:\Users\Unnati Chauhan\OneDrive\Desktop\restaurant booking
```

### 2. Database Setup

#### Option A: Using phpMyAdmin (GUI)
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click on "SQL" tab
3. Copy all content from `php/database.sql`
4. Paste in the SQL editor
5. Click "Go" to execute

#### Option B: Using MySQL Command Line
```bash
# Open MySQL Command Prompt
mysql -u root -p

# Execute SQL file
mysql -u root -p < "C:\Users\Unnati Chauhan\OneDrive\Desktop\restaurant booking\php\database.sql"
```

### 3. Configuration

Edit `php/config.php` and update database credentials:

```php
define('DB_HOST', 'localhost');      // Your MySQL host
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'restaurant_booking');
```

### 4. File Placement

Place the entire `restaurant booking` folder in your web server directory:

- **XAMPP**: `C:\xampp\htdocs\restaurant booking`
- **WAMP**: `C:\wamp64\www\restaurant booking`
- **LAMP**: `/var/www/html/restaurant booking`

### 5. Run the Application

Open your browser and navigate to:

```
http://localhost/restaurant%20booking/index.html
```

Or directly to:

- **Customer Registration**: `http://localhost/restaurant%20booking/php/register.php`
- **Customer Login**: `http://localhost/restaurant%20booking/php/login.php`
- **Admin Login**: `http://localhost/restaurant%20booking/admin/login.php`

## 🔑 Default Credentials

### Admin Login
- **Username**: `admin`
- **Password**: `admin123`

### Customer
- **Register**: Create new account on registration page
- **Sample User**: You can create one after registration

## 📁 Project Structure

```
restaurant booking/
├── index.html                 # Main landing page
├── php/
│   ├── config.php            # Database config - UPDATE THIS!
│   ├── database.sql          # Database schema
│   ├── register.php          # User registration
│   ├── login.php             # User login
│   ├── customer_dashboard.php
│   ├── book_table.php
│   ├── my_bookings.php
│   ├── check_availability.php
│   └── logout.php
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── manage_bookings.php
│   ├── manage_tables.php
│   ├── manage_slots.php
│   ├── view_users.php
│   ├── reports.php
│   └── logout.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── validation.js
│       └── booking.js
└── README.md
```

## ✅ Verification Checklist

- [ ] MySQL is running
- [ ] Database `restaurant_booking` is created
- [ ] Tables are created successfully
- [ ] `php/config.php` is updated with correct credentials
- [ ] Files are in web server directory
- [ ] PHP is enabled in your server
- [ ] Can access `http://localhost/restaurant%20booking/`

## 🐛 Troubleshooting

### Error: "Connection failed: Connection refused"
- MySQL service is not running
- Check database credentials in `config.php`
- Verify MySQL is accessible on `localhost:3306`

### Error: "Page not found"
- Check folder placement in web server directory
- Verify folder name spelling and spaces
- Restart Apache/web server

### Error: "Table not found"
- Run the SQL script in `php/database.sql`
- Check if database `restaurant_booking` exists
- Verify all tables are created

### Styles not loading
- Clear browser cache (Ctrl+Shift+Delete)
- Check CSS file path
- Verify `assets/css/style.css` exists

### Forms not working
- Verify PHP is enabled
- Check PHP error logs
- Ensure database connection is successful

## 🔒 Security Notes

⚠️ **For Production Use Only**:
1. Change admin password in database
2. Use strong passwords
3. Implement SSL/HTTPS
4. Add CSRF tokens
5. Regular database backups
6. Update PHP to latest version
7. Disable direct database access

## 📞 Support

For issues:
1. Check error messages in browser console (F12)
2. Review PHP error logs
3. Verify database connection
4. Check file permissions
5. Ensure all files are uploaded correctly

## 🚀 Performance Tips

- Use prepared statements (already implemented)
- Regular database optimization
- Enable PHP caching
- Minify CSS and JavaScript
- Optimize images
- Use CDN for static files

## 📚 Database Statistics

After setup, you'll have:
- **Users Table**: For customer registration
- **Reservations Table**: For booking records
- **Restaurant Tables**: 8 default tables (capacities: 2, 2, 4, 4, 6, 8, 6, 4)
- **Time Slots**: 12 default time slots from 11 AM to 8 PM

## ✨ Next Steps

1. **Create a test booking**: Register → Login → Book Table
2. **Test admin panel**: Login with admin/admin123
3. **Explore features**: Try filtering, cancelling, etc.
4. **Customize**: Modify tables, time slots, styling as needed

---

Happy Booking! 🍽️

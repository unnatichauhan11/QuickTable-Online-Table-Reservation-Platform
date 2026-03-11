# 🎓 Restaurant Booking System - Viva Questions & Answers

## Overview Questions

### Q1: What is the Restaurant Table Reservation System?
**A:** It's a web-based application that allows customers to book tables at a restaurant online instead of calling or visiting physically. It handles user registration, table booking, reservation management, and provides an admin panel for restaurant staff.

### Q2: What are the main components?
**A:** 
1. **User Panel**: Registration, login, table booking, view bookings
2. **Admin Panel**: Manage bookings, tables, time slots, and view reports
3. **Database**: Store user and booking information
4. **Frontend**: HTML, CSS, JavaScript for UI
5. **Backend**: PHP for business logic

---

## User Side Questions

### Q3: How does user registration work?
**A:** 
- User fills in: name, email, contact number, password
- Password is hashed using PHP's `password_hash()` function
- Email validation ensures valid email format
- Contact number validated for 10-15 digits
- Duplicate email check prevents multiple registrations
- User data stored in `users` table

### Q4: What happens after user login?
**A:**
- Email and password verified against database
- Password checked using `password_verify()`
- Session created with user_id and user_name
- User redirected to customer dashboard
- Session maintained throughout browsing

### Q5: How is table availability checked?
**A:**
- System queries `reservations` table for bookings on selected date
- Filters by date, time, and status (pending/confirmed)
- Fetches suitable tables from `restaurant_tables` based on guest count
- Compares booked times with available time slots
- Returns available slots as JSON via AJAX
- Updates dropdown in real-time

### Q6: What validation is done on booking form?
**A:**
- Guest count: Between 1-12
- Date: Must be today or future date (minimum 1 day ahead)
- Time: Must be selected from available slots
- Date format: YYYY-MM-DD
- All validations done on both client (JavaScript) and server (PHP)

### Q7: How can users cancel bookings?
**A:**
- Users can cancel only future bookings (before reservation time)
- Status changes from 'pending'/'confirmed' to 'cancelled'
- Cancelled bookings still visible in history
- Cannot cancel past bookings
- Confirmation dialog prevents accidental cancellation

---

## Admin Panel Questions

### Q8: What is the admin dashboard?
**A:**
- Shows summary statistics of the system
- Displays: total users, total bookings, pending approvals, confirmed bookings
- Shows today's bookings count
- Lists recent 10 bookings in table format
- Provides quick access links to all admin functions

### Q9: How does booking management work?
**A:**
- Admin views all customer reservations
- Can filter by: date, status (pending/confirmed/rejected/cancelled)
- Can change booking status using dropdown
- View customer details: name, email, contact
- See special requests added by customers
- Updated status instantly saves to database

### Q10: How are tables managed?
**A:**
- Admin can view all restaurant tables
- Can add new table with: table number and capacity
- Can delete existing tables
- Each table has unique number and seating capacity
- Default: 8 tables with capacities 2, 2, 4, 4, 6, 8, 6, 4

### Q11: What information is in Reports?
**A:**
- Booking status distribution (pending/confirmed/rejected/cancelled)
- Monthly booking statistics
- Quick stats: bookings today, this week, this month
- Average number of guests per booking
- Historical data visualization

---

## Database Questions

### Q12: Explain the database structure
**A:**

**Users Table:**
```
id (INT, PK) | name | email (UNIQUE) | contact | password | created_at
```

**Reservations Table:**
```
id (INT, PK) | user_id (FK) | guests | date | time | status | special_requests | created_at
```

**Restaurant Tables Table:**
```
id (INT, PK) | table_number (UNIQUE) | capacity | created_at
```

**Time Slots Table:**
```
id (INT, PK) | slot_time (UNIQUE) | status (available/unavailable)
```

### Q13: What are the relationships between tables?
**A:**
- **Users ↔ Reservations**: One-to-Many (one user can have many bookings)
- Foreign key: `reservations.user_id` references `users.id`
- ON DELETE CASCADE ensures bookings deleted when user deleted
- No direct relationship between reservations and restaurant_tables in schema (booking logic checks compatibility)

### Q14: What are booking statuses?
**A:**
1. **Pending**: Initial status when customer books, waiting for admin approval
2. **Confirmed**: Admin approved the booking
3. **Rejected**: Admin rejected the booking
4. **Cancelled**: Customer or admin cancelled the booking

---

## Technical Implementation Questions

### Q15: How is password security implemented?
**A:**
- Passwords NOT stored in plain text
- PHP `password_hash()` function uses bcrypt algorithm
- `password_verify()` used during login to check password
- Each password hashed differently even if same password
- Impossible to reverse-engineer original password

### Q16: How is SQL injection prevented?
**A:**
- Use of prepared statements with placeholders (?)
- `bind_param()` binds variables to placeholders
- User input never directly concatenated in SQL queries
- Type checking: 's' for string, 'i' for integer
- Example:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
```

### Q17: How does the AJAX availability check work?
**A:**
- JavaScript sends POST request to `check_availability.php` with date and guest count
- Server queries database for booked times
- Returns available time slots as JSON
- JavaScript updates time dropdown without page reload
- Improves user experience with real-time feedback

### Q18: How are sessions managed?
**A:**
- `session_start()` initializes session in config.php
- User data stored in `$_SESSION` array
- Session ID stored in browser cookie
- Server validates session on each page
- If not logged in, redirected to login page
- Session destroyed on logout

---

## Security & Performance Questions

### Q19: What security features are implemented?
**A:**
1. Password hashing with bcrypt
2. SQL injection prevention with prepared statements
3. Session-based authentication
4. Input validation on client and server
5. Type checking for database queries
6. Unique email enforcement in database
7. FOREIGN KEY constraints for data integrity

### Q20: How is data integrity maintained?
**A:**
- PRIMARY KEYs ensure unique records
- UNIQUE constraints on email and table_number
- FOREIGN KEYs enforce referential integrity
- AUTO_INCREMENT for sequence IDs
- TIMESTAMP for auto-tracking record creation
- Data validation before database insertion

### Q21: What happens if someone makes duplicate bookings?
**A:**
- System checks availability before confirming
- If another user books same slot, system returns "not available"
- Prevents duplicate bookings through database constraints
- Real-time checking via AJAX prevents race conditions
- User gets immediate feedback

### Q22: Can admin delete user accounts?
**A:**
- Current system doesn't provide delete user function
- Can be added in future versions
- Due to ON DELETE CASCADE, deleting user would delete all bookings
- Better to disable accounts instead of delete

---

## Feature-Specific Questions

### Q23: What happens when a customer doesn't get admin approval?
**A:**
- Booking stays in 'pending' state
- Customer can see it in "My Bookings" with "Pending" status
- Customer can cancel if needed
- Admin can reject if table not available
- No automatic timeout (can be added: auto-reject after X days)

### Q24: How are time slots defined?
**A:**
- 12 default time slots predefined in database
- Slots: 11:00 AM to 8:00 PM in 30-minute intervals
- Lunch: 11:00 AM - 1:00 PM
- Dinner: 5:00 PM - 8:00 PM
- Admin can modify in future enhancement
- Slots only show if tables available

### Q25: What is special_requests field?
**A:**
- Optional field for customer notes
- Examples: "Vegetarian", "Window seat", "Birthday celebration"
- Admin can see these when reviewing bookings
- Helps personalize customer experience
- Stored as TEXT in database

### Q26: How many bookings can a user make?
**A:**
- Unlimited bookings
- Can book multiple days
- Can have overlapping bookings (different tables)
- Can have both pending and confirmed bookings
- No booking limit imposed

---

## Improvement & Enhancement Questions

### Q27: What advanced features can be added?
**A:**
1. **Email Notifications**: Send confirmation emails using PHPMailer
2. **SMS Alerts**: Book SMS API for confirmation texts
3. **Payment Integration**: Add payment gateway for deposits
4. **Calendar View**: Visual calendar for bookings
5. **Auto-cancellation**: Auto-reject unconfirmed bookings after X days
6. **Rating System**: Allow customers to rate restaurants
7. **Reviews**: Customer testimonials and feedback
8. **Email Reminders**: Send reminder 24 hours before booking
9. **Waitlist**: Queue system if no tables available
10. **Table Diagrams**: Visual representation of table layout

### Q28: How to scale this system?
**A:**
1. **Database**: Use database replication/clustering
2. **Caching**: Implement Redis for session and data caching
3. **Load Balancing**: Distribute traffic across servers
4. **API**: Create REST API for mobile apps
5. **Cloud**: Deploy on AWS/Azure for scalability
6. **Queue**: Use message queues for email sending
7. **Monitoring**: Add error tracking and monitoring tools

### Q29: What are potential issues in current system?
**A:**
1. No concurrent booking protection
2. Admin credentials hardcoded
3. No email confirmation system
4. No payment integration
5. Limited search/filtering options
6. No user profile management
7. No password reset feature
8. No role-based access control

### Q30: How would you improve security?
**A:**
1. Use HTTPS/SSL encryption
2. Implement CSRF tokens
3. Add rate limiting for login attempts
4. Use OAuth for social login
5. Implement 2FA (Two-Factor Authentication)
6. Regular security audits
7. SQL injection testing
8. XSS protection
9. Input sanitization
10. Security headers in HTTP responses

---

## Practical Scenario Questions

### Q31: User can't login. What could be wrong?
**A:**
1. Wrong email/password
2. Account not registered
3. Email not created account with
4. Database connection issue
5. PHP error in login script
6. Session not starting
7. Database corruption

### Q32: Booking shows as available but fails to save. Why?
**A:**
1. Another user booked same slot (race condition)
2. Database connection lost
3. Invalid data passed
4. Duplicate booking attempt
5. Database error (space, permissions)
6. Form validation failed on server
7. Session expired

### Q33: Admin can't approve bookings. Why?
**A:**
1. Not logged in as admin
2. Admin session expired
3. Database write permission issue
4. Wrong booking ID passed
5. Booking status invalid
6. PHP error in update script
7. Database locked

---

## Viva Tips & Study Points

1. **Know the flow**: Registration → Login → Dashboard → Booking → Admin Review
2. **Database schema**: Be ready to draw relationships and explain
3. **Security**: Always emphasize password hashing and prepared statements
4. **Validation**: Mention both client-side and server-side validation
5. **User experience**: Discuss AJAX for real-time availability
6. **Scalability**: Be prepared to discuss how system can grow
7. **Error handling**: Mention validation error messages
8. **Code structure**: Explain modular design and separation of concerns
9. **Best practices**: Discuss prepared statements, sessions, error handling
10. **Real-world usage**: Connect features to actual restaurant needs

---

**Good Luck with Your Viva! 🍽️**

Remember to speak confidently, explain with examples, and ask clarifying questions if needed!

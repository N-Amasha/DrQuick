# Admin Authentication System

A secure PHP and MySQL-based authentication system featuring session management, password hashing, and login protection. This project is designed to demonstrate foundational full-stack web development skills.

## Features
- **User Registration:** Secure account creation with email and hashed passwords using `password_hash()`.
- **Login Authentication:** Credential validation via `password_verify()` integrated with PHP sessions.
- **Session Security:**
  - Automatic session regeneration to mitigate fixation attacks.
  - Access-controlled dashboard via `auth_check.php` to block unauthorized users.
- **Login Attempt Limiting:** Basic session-based protection that restricts access after 3 failed attempts.
- **Logout Management:** Complete session destruction and secure redirection to the login portal.
- **Modern UI:** Clean, responsive interface styled with a deep jungle green theme and Google Fonts.
- **SQL Protection:** Implementation of prepared statements to prevent SQL injection vulnerabilities.

## Technologies Used
- **Frontend:** HTML5, CSS3, JavaScript  
- **Backend:** PHP 8+  
- **Database:** MySQL  
- **Environment:** XAMPP / phpMyAdmin  
- **Security:** Password Hashing (BCRYPT), Session Management, Prepared Statements

## Folder Structure

Project1_Admin_Login/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── auth/
│   └── logout.php
├── config/
│   └── db.php
├── includes/
│   ├── auth_check.php
│   ├── header.php
│   └── footer.php
├── dashboard.php
├── index.php
└── register.php


## Setup Instructions

### Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/admin-login-system.git

Database Configuration
    1.Open phpMyAdmin.
    2.Create a new database named internship_db.
    3.Execute the following SQL to create the user table:

```SQL
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

Connection Settings
    Update config/db.php with your local database credentials.

Navigate to config/db.php.

Update the database credentials (host, username, password) to match your local environment.

Deployment
    1.Move the project folder into your local server directory (e.g., htdocs for XAMPP).
    2.Start Apache and MySQL via the XAMPP Control Panel.
    3.Open your browser and go to: http://localhost/Project1_Admin_Login/

Project Highlights
    Follows industry-standard secure authentication protocols.
    Showcases clean code organization and modular PHP architecture.
    Optimized for internship technical demonstrations.

Future Enhancements
    Password Recovery: Integration of "Forgot Password" via email SMTP.
    Persistent Security: Storing login attempts in the database for cross-session blocking.
    RBAC: Implementation of Role-Based Access Control for different permission levels.
    UI Expansion: Adding a comprehensive sidebar and data visualization on the dashboard.

Author
Nethmi Amasha - Aspiring Web Developer
Email: ne.amasha@hotmail.com

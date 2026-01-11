<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | Patient Dashboard</title>
    <link rel="stylesheet" href="../patient/assets/fontawesome-free-7.1.0-web/css/all.min.css">
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    session_start();
    require '../config/db.php';

    // Access control: Ensure user is logged in and is a patient
    if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient'){
        header("Location: ../auth/login.php");
        exit;
    }
    ?>

    <header class="dashboard-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/images/Drlogo.png" alt="DrQuick">
                <span class="brand-text">DrQuick <span class="badge">Patient</span></span>
            </div>
            <div class="nav-actions">
                <a href="../auth/logout.php" class="logout-link">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="welcome-section">
            <h1>Hello, Patient</h1>
            <p class="tagline">Find the best care and manage your health appointments.</p>
        </div>

        <div class="card-grid">
            <div class="action-card">
                <div class="card-icon-wrapper">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                </div>
                <h3>Book Appointment</h3>
                <p>Search for available doctors and book your next visit.</p>
                <a href="search_doctors.php" class="btn-primary">Find a Doctor</a>
            </div>

            <div class="action-card">
                <div class="card-icon-wrapper">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3>My Bookings</h3>
                <p>View your upcoming appointments and medical history.</p>
                <a href="my_appointments.php" class="btn-primary">View Appointments</a>
            </div>

            <div class="action-card">
                <div class="card-icon-wrapper">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <h3>My Profile</h3>
                <p>Manage your personal information and contact details.</p>
                <a href="profile.php" class="btn-primary">Edit Profile</a>
            </div>
        </div>
    </main>

</body>
</html>
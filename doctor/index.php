<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | Patient Dashboard</title>
    <link rel="stylesheet" href="../doctor/assets/fontawesome-free-7.1.0-web/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../doctor/assets/css/style.css">
    </head>
<body>
    <?php
    session_start();
    require '../config/db.php';

    // Access control: Ensure user is logged in and is a doctor
    if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor'){
        header("Location: ../auth/login.php");
        exit;
    }
    ?>

    <header class="dashboard-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/images/Drlogo.png" alt="DrQuick">
                <span class="brand-text">DrQuick <span class="badge">Doctor</span></span>
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
            <h1>Hello, Doctor</h1>
            <p class="tagline">Smart healthcare management at your fingertips.</p>
        </div>

        <div class="card-grid">
            <div class="action-card">
                <div class="card-icon-wrapper">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                </div>
                <h3>Manage Schedule</h3>
                <p>Set your available days and time slots for patients.</p>
                <a href="manage_schedule.php" class="btn-primary">Find a Doctor</a>
            </div>

            <div class="action-card">
                <div class="card-icon-wrapper">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3>Appointments</h3>
                <p>View and manage your upcoming patient consultations.</p>
                <a href="appointments.php" class="btn-primary">View Appointments</a>
            </div>

            <div class="action-card">
                <div class="card-icon-wrapper">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <h3>My Profile</h3>
                <p>Update your professional details and clinic info here.</p>
                <a href="profile.php" class="btn-primary">Edit Profile</a>
            </div>
        </div>
    </main>

</body>
</html>
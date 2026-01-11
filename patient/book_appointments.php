<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;

// Fetch Doctor Details for the header
$stmt = mysqli_prepare($conn, "SELECT fullname FROM users WHERE id = ? AND role = 'doctor'");
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$doctor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$doctor) {
    header("Location: find_doctor.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | Available Schedules</title>
    <link rel="stylesheet" href="../patient/assets/fontawesome-free-7.1.0-web/css/all.min.css">
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-body">

    <header class="dashboard-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/images/Drlogo.png" alt="DrQuick" style="width:40px;">
                <span class="brand-text">DrQuick <span class="badge">Booking</span></span>
            </div>
            <a href="find_doctor.php" class="logout-link"><i class="fa-solid fa-arrow-left"></i> Back to Search</a>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="welcome-section">
            <h1>Schedules for Dr. <?php echo htmlspecialchars($doctor['fullname']); ?></h1>
            <p class="tagline">Select a preferred time slot below to confirm your appointment.</p>
        </div>

        <div class="schedule-grid">
            <?php
            // Fetch schedules for this specific doctor
            $sched_stmt = mysqli_prepare($conn, "SELECT id, day_of_week, start_time, end_time FROM doctor_schedule WHERE doctor_id = ?");
            mysqli_stmt_bind_param($sched_stmt, "i", $doctor_id);
            mysqli_stmt_execute($sched_stmt);
            $slots = mysqli_stmt_get_result($sched_stmt);

            if (mysqli_num_rows($slots) > 0) {
                while ($slot = mysqli_fetch_assoc($slots)) {
            ?>
                <div class="action-card schedule-card">
                    <div class="schedule-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="schedule-details">
                        <h3 class="day-label"><?php echo $slot['day_of_week']; ?></h3>
                        <p class="time-range">
                            <?php echo date("h:i A", strtotime($slot['start_time'])); ?> 
                            <span>to</span> 
                            <?php echo date("h:i A", strtotime($slot['end_time'])); ?>
                        </p>
                    </div>
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="schedule_id" value="<?php echo $slot['id']; ?>">
                        <button type="submit" class="btn-primary full-width">
                            <i class="fa-solid fa-check"></i> Book This Slot
                        </button>
                    </form>
                </div>
            <?php
                }
            } else {
                echo "<div class='no-results'><i class='fa-solid fa-calendar-xmark'></i><p>No available slots for this doctor.</p></div>";
            }
            ?>
        </div>
    </main>
</body>
</html>
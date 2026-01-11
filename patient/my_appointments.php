<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT a.*, u.fullname AS doctor_name
    FROM appointments a
    JOIN users u ON a.doctor_id = u.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | My Appointments</title>
    <link rel="stylesheet" href="../patient/assets/fontawesome-free-7.1.0-web/css/all.min.css">
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-body">

    <header class="dashboard-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/images/Drlogo.png" alt="DrQuick" style="width:40px;">
                <span class="brand-text">DrQuick <span class="badge">Patient</span></span>
            </div>
            <a href="index.php" class="logout-link"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
        </div>
    </header>

    <main class="dashboard-container">
    <div class="welcome-section">
        <h1>My Bookings</h1>
        <p class="tagline">Track the status of your medical appointments and history.</p>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert-box alert-<?php echo $_SESSION['msg_type']; ?>">
            <i class="fa-solid <?php echo ($_SESSION['msg_type'] == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <div class="action-card table-card">
        <h3 style="text-align: center; margin-bottom: 25px;">Appointment History</h3>
        
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Doctor Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="mini-avatar"><i class="fa-solid fa-user-md"></i></div>
                                    <strong>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></strong>
                                </div>
                            </td>
                            <td><?php echo date("M d, Y", strtotime($row['appointment_date'])); ?></td>
                            <td><span class="time-badge"><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></span></td>
                            <td>
                                <span class="status-pill <?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; opacity: 0.5;">
                                <i class="fa-solid fa-calendar-xmark" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                You haven't booked any appointments yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</main>

</body>
</html>
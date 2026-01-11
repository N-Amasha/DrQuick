<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor'){
    header("Location: ../auth/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Handle Add Schedule
if(isset($_POST['add_schedule'])){
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if($start_time >= $end_time){
        $error = "Start time must be before end time.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO doctor_schedule(doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $doctor_id, $day, $start_time, $end_time);
        if(mysqli_stmt_execute($stmt)){
            $success = "New slot added to your schedule!";
        } else {
            $error = "Database error. Please try again.";
        }
    }
}

// Fetch doctor's schedule
$stmt = mysqli_prepare($conn, "SELECT * FROM doctor_schedule WHERE doctor_id = ? ORDER BY FIELD (day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), start_time ASC");
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | Manage Schedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../doctor/assets/css/style.css">
</head>
<body class="dashboard-body">

    <header class="dashboard-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/images/Drlogo.png" alt="DrQuick" style="width:40px;">
                <span class="brand-text">DrQuick <span class="badge">Schedule</span></span>
            </div>
            <a href="index.php" class="logout-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="welcome-section">
            <h1>Manage Your Availability</h1>
            <p class="tagline">Define your working hours so patients can book appointments.</p>
        </div>

        <div class="action-card schedule-form-card">
            <h3>Add New Slot</h3>
            <form action="" method="POST" class="horizontal-form">
                <div class="form-group">
                    <label>Day of Week</label>
                    <select name="day" required class= "days">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" required>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="end_time" required>
                </div>
                <button type="submit" name="add_schedule" class="btn-primary">Add to Schedule</button>
            </form>
        </div>

        <div class="action-card table-card">
            <h3>Current Weekly Slots</h3>
            <div class="table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Working Hours</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><strong><?php echo $row['day_of_week']; ?></strong></td>
                                <td><?php echo date("h:i A", strtotime($row['start_time'])); ?> - <?php echo date("h:i A", strtotime($row['end_time'])); ?></td>
                                <td>
                                    <a href="delete_schedule.php?id=<?php echo $row['id']; ?>" class="delete-link" onclick="return confirm('Delete slot?')">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="empty-msg">No slots defined yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];

// Approve / Cancel logic
if (isset($_GET['action'], $_GET['id'])) {
    $status = ($_GET['action'] === 'approve') ? 'approved' : 'cancelled';
    $id = (int) $_GET['id'];

    $stmt = mysqli_prepare($conn, "UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
    mysqli_stmt_bind_param($stmt, "sii", $status, $id, $doctor_id);
    mysqli_stmt_execute($stmt);

    header("Location: appointments.php");
    exit;
}

// Fetch appointments with patient names
$stmt = mysqli_prepare($conn, "
    SELECT a.id, a.appointment_date, a.appointment_time, a.status, u.fullname AS patient_name
    FROM appointments a
    INNER JOIN users u ON a.patient_id = u.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | Patient Appointments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../doctor/assets/css/style.css">
</head>
<body class="dashboard-body">

    <header class="dashboard-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/images/Drlogo.png" alt="DrQuick" style="width:40px;">
                <span class="brand-text">DrQuick <span class="badge">Appointments</span></span>
            </div>
            <a href="index.php" class="logout-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="welcome-section">
            <h1>Patient Appointments</h1>
            <p class="tagline">Review and manage your upcoming consultations.</p>
        </div>

        <div class="action-card table-card">
            <h3>Recent Requests</h3>
            <div class="table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['patient_name']); ?></strong></td>
                                <td><?php echo date("D, M d, Y", strtotime($row['appointment_date'])); ?></td>
                                <td><span class="time-badge"><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></span></td>
                                <td>
                                    <span class="status-pill <?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn-approve" title="Approve">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                        <a href="?action=cancel&id=<?php echo $row['id']; ?>" class="btn-cancel" title="Cancel" onclick="return confirm('Cancel this appointment?')">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="action-done">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-msg">No appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
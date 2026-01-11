<?php
session_start();
require '../config/db.php';

// Only doctors can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Fetch current doctor info
$stmt = mysqli_prepare($conn, "SELECT fullname, email, contact FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $update = mysqli_prepare($conn, "UPDATE users SET fullname=?, email=?, contact=?, password=? WHERE id=?");
            mysqli_stmt_bind_param($update, "ssssi", $fullname, $email, $contact, $hashedPassword, $doctor_id);
        } else {
            $update = mysqli_prepare($conn, "UPDATE users SET fullname=?, email=?, contact=? WHERE id=?");
            mysqli_stmt_bind_param($update, "sssi", $fullname, $email, $contact, $doctor_id);
        }

        if (mysqli_stmt_execute($update)) {
            $success = "Profile updated successfully.";
            $user['fullname'] = $fullname;
            $user['email'] = $email;
            $user['contact'] = $contact;
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../doctor/assets/css/style.css">
</head>
<body class="dashboard-body">

    <header class="dashboard-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/images/Drlogo.png" alt="DrQuick" style="width:40px;">
                <span class="brand-text">DrQuick <span class="badge">Profile</span></span>
            </div>
            <a href="index.php" class="logout-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="welcome-section">
            <h1>Account Settings</h1>
            <p class="tagline">Keep your professional information and contact details up to date.</p>
        </div>

        <div class="profile-card-centered">
            <div class="action-card">
                <h3><i class="fa-solid fa-user-gear"></i> Update Your Information</h3>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form action="" method="POST" class="styled-form">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>">
                    </div>

                    <hr class="form-divider">
                    <p class="form-hint">Security: Only fill the fields below if you want to change your password.</p>

                    <div class="time-row">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary full-width" style="margin-top: 20px;">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
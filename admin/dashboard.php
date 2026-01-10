<?php
require_once "includes/auth_check.php";

if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-body">

    <nav class="navbar">
        <div class="nav-content">
            <h1 class="logo-small">User Authentication System</h1>
            <div class="user-info">
                <span>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['email']);?></strong></span>
                <a href="auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <main class="dashboard-container">
        <div class="welcome-card">
            <h2>Welcome Back!</h2>
            <p>You have successfully accessed.</p>
            <div class="stats-grid">
                <div class="stats-item">
                    <small>STATUS</small>
                    <p>Active</p>
                </div>
                <div class="stats-item">
                    <small>ROLE</small>
                    <p>Administrator</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>


<?php
session_start();
require '../config/db.php';

/* Allow only patients */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | Find a Doctor</title>
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
            <h1>Find the Best Care</h1>
            <p class="tagline">Search for specialist doctors and book your consultation instantly.</p>
        </div>

        <div class="search-wrapper">
            <form method="GET" class="search-bar-container glass-search">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" 
                       placeholder="Search by name or specialty..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-primary">Find Doctor</button>
            </form>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert-box alert-<?php echo $_SESSION['msg_type']; ?>" style="margin-top: 20px;">
                <i class="fa-solid <?php echo ($_SESSION['msg_type'] == 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                <span><?php echo $_SESSION['msg']; ?></span>
            </div>
            <?php unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
        <?php endif; ?>

        <div class="doctor-grid">
            <?php
            $query = "SELECT id, fullname, contact FROM users WHERE role = 'doctor'";
            if ($search !== "") {
                $query .= " AND fullname LIKE ?";
            }

            $stmt = mysqli_prepare($conn, $query);
            
            if ($search !== "") {
                $like = "%" . $search . "%";
                mysqli_stmt_bind_param($stmt, "s", $like);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <div class="action-card doctor-card">
                    <div class="doc-avatar">
                        <i class="fa-solid fa-user-md"></i>
                    </div>
                    <div class="doc-details">
                        <h3>Dr. <?php echo htmlspecialchars($row['fullname']); ?></h3>
                        <p class="specialty-text">Specialist Professional</p>
                        
                        <div class="doc-meta">
                            <span><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($row['contact']); ?></span>
                        </div>
                    </div>
                    
                    <a href="book_appointments.php?doctor_id=<?php echo $row['id']; ?>" class="btn-primary full-width btn-book">
                        <i class="fa-solid fa-calendar-check"></i> Book Now
                    </a>
                </div>
            <?php
                }
            } else {
                echo "<div class='no-results-full'><i class='fa-solid fa-user-slash'></i><p>We couldn't find any doctors matching '$search'.</p></div>";
            }
            ?>
        </div>
    </main>

</body>
</html>
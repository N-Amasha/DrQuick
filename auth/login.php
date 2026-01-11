<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrQuick | Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php
    session_start();
    require '../config/db.php';

    if(!isset($_SESSION['login_attempts'])){ $_SESSION['login_attempts'] = 0; }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if($_SESSION['login_attempts'] >= 5){
            $_SESSION["error"] = "Too many failed attempts.";
            header("Location: login.php"); exit;
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = mysqli_prepare($conn, "SELECT id, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($user = mysqli_fetch_assoc($result)){
            if(password_verify($password, $user['password'])){
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fullname'] = $user['fullname'];
                
                if($user['role'] === 'doctor'){
                    header("Location: ../doctor/index.php");
                    exit;
                }elseif($user['role'] === 'patient'){
                    header("Location: ../patient/index.php");
                    exit;
                }else{
                    $_SESSION["error"] = "Unauthorized role.";
                    header("Location: ../auth/login.php");
                    exit;
                }

            }
        }
        $_SESSION['login_attempts']++;
        $_SESSION["error"] = "Invalid email or password";
        header("Location: login.php"); exit;
    }
    ?>

    <div class="auth-container">
        <div class="logo-area">
            <img src="../assets/images/Drlogo.png" alt="DrQuick" class="auth-logo">
            <h1 class="auth-title">User Login</h1>
        </div>
        
        <div class="auth-card">
            <?php if (isset($_SESSION["error"])): ?>
                <p class="error-msg"><?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-field">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>

                <div class="input-field">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="primary-btn">Login <span class="arrow">→</span></button>
            </form>
        </div>
        <a href="register.php" class="auth-link">New here? Create an account</a>
    </div>

</body>
</html>
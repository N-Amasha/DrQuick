<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php
    session_start();
    require 'config/db.php';

    if(!isset($_SESSION['login_attempts'])){
        $_SESSION['login_attempts'] = 0;
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        if($_SESSION['login_attempts'] >= 3){
            $_SESSION["error"] = "Too many failed attempts. Try again later.";
            header("Location: index.php");
            exit;
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = mysqli_prepare($conn,"SELECT id,password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt,"s",$email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($user = mysqli_fetch_assoc($result)){
            if(password_verify($password,$user['password'])){

                session_regenerate_id(true);// Prevent session fixation
                $_SESSION['email'] = $email;// Store login identity
                $_SESSION['login_attempts'] = 0;

                header("Location: dashboard.php");
                exit;
            }
        }

        //Secure error handling
        $_SESSION['login_attempts']++;
        $_SESSION["error"] = "Invalid email or password";
        header("Location:index.php");
        exit;

    }
    ?>

    <div class="login-container">
        <h1 class="logo">Login</h1>

        <div class="login-card">

            <?php
                if (isset($_SESSION["error"])){
                    echo "<p class='error'>" .htmlspecialchars($_SESSION["error"]) . "</p>";
                    unset($_SESSION["error"]);
                }
            ?>

            <form method="POST" action="" onsubmit="return validateForm()">

                <div class="input-group">
                    <label for="email">Email</label><br>
                    <input type="email" name="email" id="email" placeholder="Enter email" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label><br>
                    <input type="password" name="password" id="password" placeholder="Enter password" required>
                </div>

                <button type="submit" class="login-btn">Login<span class="arrow">→</span></button>

            </form>
        </div>
        <a href="register.php" class="forgot-password">Create an account</a>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>

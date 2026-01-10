<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php
    session_start();
    require 'config/db.php';//Include database connection

    $error = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if($password !== $confirm){
            $error = "Passwords do not match";
        }

        //Hash Password
        $hashedPassword = password_hash($password,PASSWORD_DEFAULT);

        //check if email exists
        $check = mysqli_prepare($conn,"SELECT id from users WHERE email = ?");
        mysqli_stmt_bind_param($check,"s",$email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if(mysqli_stmt_num_rows($check) > 0){
            $error = "Email already registered";
        }

        //Insert user
        $stmt = mysqli_prepare($conn,"INSERT INTO users (email,password) VALUES (?,?)");
        mysqli_stmt_bind_param($stmt,"ss",$email,$hashedPassword);

        if(mysqli_stmt_execute($stmt)){
            header("Location: index.php");
            exit;
        }else{
            echo "Registration failed";
        }
    }

    ?>

    <div class="login-container">
        <h1 class="logo">Register</h1>
        <div class="login-card">
            <?php if($error): ?>
                <p style="color: #ff4d4d; font-size: 0.8rem; margin-bottom: 15px; font-weight: 600;">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <form action="" method="POST">

                <div class="input-group">
                    <label for="email">Create Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter email" required>
                </div>

                <div class="input-group">
                    <label for="password">Choose Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                </div>
                
                <button type="submit" class="login-btn">Register<span class="arrow">→</span></button>
            </form>
        </div>
        <a href="index.php" class="forgot-password">Already Have An Account? Log In</a>
    </div>
</body>
</html>
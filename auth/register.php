<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php
    session_start();
    require '../config/db.php';//Include database connection

    $error = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $role = $_POST['role'];
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
        $stmt = mysqli_prepare($conn,"INSERT INTO users (fullname,email,password,role) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt,"ssss",$fullname, $email,$hashedPassword, $role);

        if(mysqli_stmt_execute($stmt)){
            header("Location: ../auth/login.php?success=registered");
            exit;
        }else{
            echo "System error. Please try again later.";
        }
    }

    ?>

    <div class="login-container">

        <div class="logo-container">
            <img src="../assets/images/Drlogo.png" alt="DrQuick" class="logo-img" style="width: 80px;">
            <h1 class="logo-text">Create Account</h1>
        </div>

        <div class="login-card">
            <?php if($error): ?>
                <p style="color: #ff4d4d; font-size: 0.8rem; margin-bottom: 15px; font-weight: 600;">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <form action="" method="POST">

                <div class="input-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" id="fullname" placeholder="Dr. John Doe / Jane Smith" required>
                </div>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="text" name="email" id="email" placeholder="name@example.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Register As</label>
                    <select name="role" id="role" style="width: 100%; padding: 14px; background-color: #520015; border: none; border-radius: 8px; color: white; font-size:0.9em;">
                        <option value="patient">Patient(Booking Appointments)</option>
                        <option value="doctor">Doctor(Managing Schedule)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="password">Create Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="login-btn">Register<span class="arrow">→</span></button>
            </form>
        </div>
        <a href="../auth/login.php" class="forgot-password">Already Have An Account? Log In</a>
    </div>
</body>
</html>
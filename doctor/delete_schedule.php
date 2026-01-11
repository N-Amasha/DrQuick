<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];

//Validate schedule ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: manage_schedule.php");
    exit;
}

$schedule_id = $_GET['id'];

//Delete only if this schedule belongs to this doctor
$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM doctor_schedule WHERE id = ? AND doctor_id = ?"
);

mysqli_stmt_bind_param($stmt,"ii",$schedule_id,$doctor_id);
mysqli_stmt_execute($stmt);

//Redirect back
header("Location: manage_schedule.php");
exit;

?>
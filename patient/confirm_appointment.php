<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];

// Helper function to redirect with message
function goBack($msg, $type = "error") {
    $_SESSION['msg'] = $msg;
    $_SESSION['msg_type'] = $type;
    header("Location: find_doctor.php");
    exit;
}

if (!isset($_POST['schedule_id']) || !is_numeric($_POST['schedule_id'])) {
    goBack("Invalid schedule selection.");
}

$schedule_id = (int) $_POST['schedule_id'];

/* Fetch schedule details */
$stmt = mysqli_prepare($conn, "SELECT doctor_id, day_of_week, start_time FROM doctor_schedule WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $schedule_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$schedule = mysqli_fetch_assoc($result);

if (!$schedule) {
    goBack("The selected schedule is no longer available.");
}

$dayOfWeek = $schedule['day_of_week'];
$appointment_date = date('Y-m-d', strtotime("next $dayOfWeek"));
$appointment_time = $schedule['start_time'];
$doctor_id = $schedule['doctor_id'];

/* Check for duplicate booking */
$check = mysqli_prepare($conn, "SELECT id FROM appointments WHERE patient_id = ? AND doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
mysqli_stmt_bind_param($check, "iiss", $patient_id, $doctor_id, $appointment_date, $appointment_time);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    goBack("You already have a pending or approved appointment for this slot.");
}

/* Insert appointment */
$insert = mysqli_prepare($conn, "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')");
mysqli_stmt_bind_param($insert, "iiss", $patient_id, $doctor_id, $appointment_date, $appointment_time);

if (mysqli_stmt_execute($insert)) {
    $_SESSION['msg'] = "Appointment booked successfully! Awaiting doctor approval.";
    $_SESSION['msg_type'] = "success";
    header("Location: my_appointments.php");
    exit;
} else {
    goBack("Database error. Please try again later.");
}
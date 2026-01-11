<?php
session_start();
require '../config/db.php';

// 1. Access Control: Only patients can book
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];

// Helper function to redirect back to search with a message
function goBack($msg, $type = "error") {
    $_SESSION['msg'] = $msg;
    $_SESSION['msg_type'] = $type;
    header("Location: find_doctor.php");
    exit;
}

// 2. Validate the incoming Schedule ID
if (!isset($_POST['schedule_id']) || !is_numeric($_POST['schedule_id'])) {
    goBack("Invalid schedule selection. Please try again.");
}

$schedule_id = (int) $_POST['schedule_id'];

/* 3. Fetch Slot Details
   We need the doctor_id and the day (e.g., Monday) to calculate the actual date. */
$stmt = mysqli_prepare($conn, "SELECT doctor_id, day_of_week, start_time FROM doctor_schedule WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $schedule_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$schedule = mysqli_fetch_assoc($result);

if (!$schedule) {
    goBack("The selected schedule slot is no longer available.");
}

$doctor_id = $schedule['doctor_id'];
$dayOfWeek = $schedule['day_of_week']; // e.g., 'Tuesday'
$appointment_time = $schedule['start_time'];

/* 4. Date Calculation
   If today is Monday and the slot is for Tuesday, it books for 'Tomorrow'.
   If today is Wednesday and the slot is for Tuesday, it books for 'Next Tuesday'. */
$appointment_date = date('Y-m-d', strtotime("next $dayOfWeek"));

/* 5. Duplicate Booking Prevention
   Check if the patient already has an active appointment for this exact slot. */
$check_query = "SELECT id FROM appointments 
                WHERE patient_id = ? AND doctor_id = ? 
                AND appointment_date = ? AND appointment_time = ? 
                AND status != 'cancelled'";

$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "iiss", $patient_id, $doctor_id, $appointment_date, $appointment_time);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    goBack("You have already booked this specific time slot.");
}

/* 6. Insert the Appointment
   The default status is 'pending' until the doctor approves it in their dashboard. */
$insert_query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) 
                 VALUES (?, ?, ?, ?, 'pending')";

$insert_stmt = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($insert_stmt, "iiss", $patient_id, $doctor_id, $appointment_date, $appointment_time);

if (mysqli_stmt_execute($insert_stmt)) {
    // Set success message for the My Appointments page
    $_SESSION['msg'] = "Appointment requested! Please wait for the doctor to approve it.";
    $_SESSION['msg_type'] = "success";
    header("Location: my_appointments.php");
    exit;
} else {
    goBack("System error: Could not complete the booking. Please try again later.");
}
?>
<?php
session_start();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

//Check if the user is logged in
if(!isset($_SESSION['email'])){
    header("Location: ./index.php");
    exit;
}
?>
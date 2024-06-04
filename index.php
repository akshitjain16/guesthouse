<?php
session_start();

if (isset($_SESSION['user_id'])) {
    // User is logged in, redirect to dashboard
    header("Location: src/php/dashboard.php");
    exit();
} else {
    // User is not logged in, redirect to login page
    header("Location: src/php/login.php");
    exit();
}
?>




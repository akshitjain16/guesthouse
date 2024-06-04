<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

echo "<h1>Admin Panel</h1>";
echo "<a href='manage_menu.php'>Manage Menu</a><br>";
echo "<a href='manage_orders.php'>Manage Orders</a><br>";
echo "<a href='manage_employees.php'>Manage Employees</a><br>";
echo "<a href='dashboard.php'>Back to Dashboard</a>";
?>

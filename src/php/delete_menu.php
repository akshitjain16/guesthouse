<?php
session_start();
include '../../config/config.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu_id = $_POST['menu_id'];
    $sql = "DELETE FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $menu_id);

    if ($stmt->execute()) {
        echo "Menu item deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

header("Location: manage_menu.php");
?>

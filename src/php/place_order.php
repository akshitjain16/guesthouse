<?php
session_start();
include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['role'] == 'employee') {
    $user_id = $_SESSION['user_id'];
    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['quantity'];

    $sql = "SELECT price FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu = $result->fetch_assoc();
    $total = $menu['price'] * $quantity;

    $sql = "INSERT INTO orders (user_id, menu_id, quantity, total) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $user_id, $menu_id, $quantity, $total);

    if ($stmt->execute()) {
        echo "Order placed successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<a href="employee.php">Back to Menu</a>

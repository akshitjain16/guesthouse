<?php
session_start();
include '../../config/config.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

$sql = "SELECT orders.id, users.name AS username, menu.name AS menu_name, orders.quantity, orders.status 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        JOIN menu ON orders.menu_id = menu.id";
$result = $conn->query($sql);

echo "<h1>Manage Orders</h1>";
while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h2>Order #" . $row['id'] . "</h2>";
    echo "<p>Employee: " . $row['username'] . "</p>";
    echo "<p>Menu Item: " . $row['menu_name'] . "</p>";
    echo "<p>Quantity: " . $row['quantity'] . "</p>";
    echo "<form method='post' action='update_order.php'>";
    echo "<input type='hidden' name='order_id' value='" . $row['id'] . "'>";
    echo "<select name='status'>";
    echo "<option value='pending'" . ($row['status'] == 'pending' ? ' selected' : '') . ">Pending</option>";
    echo "<option value='preparing'" . ($row['status'] == 'preparing' ? ' selected' : '') . ">Preparing</option>";
    echo "<option value='ready'" . ($row['status'] == 'ready' ? ' selected' : '') . ">Ready</option>";
    echo "<option value='completed'" . ($row['status'] == 'completed' ? ' selected' : '') . ">Completed</option>";
    echo "<option value='cancelled'" . ($row['status'] == 'cancelled' ? ' selected' : '') . ">Cancelled</option>";
    echo "</select>";
    echo "<button type='submit'>Update</button>";
    echo "</form>";
    echo "</div>";
}
?>
<a href="admin.php">Back to Admin Panel</a>

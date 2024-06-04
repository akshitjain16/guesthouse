<?php
session_start();
include '../../config/config.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $availability = isset($_POST['availability']) ? 1 : 0;

    if (isset($_POST['menu_id'])) {
        $menu_id = $_POST['menu_id'];
        $sql = "UPDATE menu SET name = ?, description = ?, price = ?, availability = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdii", $name, $description, $price, $availability, $menu_id);
    } else {
        $sql = "INSERT INTO menu (name, description, price, availability) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $name, $description, $price, $availability);
    }

    if ($stmt->execute()) {
        echo "Menu item saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

if (isset($_GET['edit'])) {
    $menu_id = $_GET['edit'];
    $sql = "SELECT * FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $menu = $stmt->get_result()->fetch_assoc();
}
?>

<h1>Manage Menu</h1>
<form method="post">
    <input type="hidden" name="menu_id" value="<?php echo isset($menu['id']) ? $menu['id'] : ''; ?>">
    <label for="name">Name:</label>
    <input type="text" name="name" value="<?php echo isset($menu['name']) ? $menu['name'] : ''; ?>" required>
    <label for="description">Description:</label>
    <textarea name="description" required><?php echo isset($menu['description']) ? $menu['description'] : ''; ?></textarea>
    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" value="<?php echo isset($menu['price']) ? $menu['price'] : ''; ?>" required>
    <label for="availability">Available:</label>
    <input type="checkbox" name="availability" <?php echo isset($menu['availability']) && $menu['availability'] ? 'checked' : ''; ?>>
    <button type="submit">Save</button>
</form>

<h2>Menu Items</h2>
<?php
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h3>" . $row['name'] . "</h3>";
    echo "<p>" . $row['description'] . "</p>";
    echo "<p>$" . $row['price'] . "</p>";
    echo "<a href='manage_menu.php?edit=" . $row['id'] . "'>Edit</a>";
    echo "<form method='post' action='delete_menu.php' style='display:inline;'>";
    echo "<input type='hidden' name='menu_id' value='" . $row['id'] . "'>";
    echo "<button type='submit'>Delete</button>";
    echo "</form>";
    echo "</div>";
}
?>
<a href="admin.php">Back to Admin Panel</a>

<?php
session_start();
include '../../config/config.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $role = 'employee';

    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $sql = "UPDATE users SET username = ?, password = ?, name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $name, $user_id);
    } else {
        $sql = "INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $password, $name, $role);
    }

    if ($stmt->execute()) {
        echo "Employee account saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $sql = "SELECT * FROM users WHERE id = ? AND role = 'employee'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}
?>

<h1>Manage Employees</h1>
<form method="post">
    <input type="hidden" name="user_id" value="<?php echo isset($user['id']) ? $user['id'] : ''; ?>">
    <label for="username">Username:</label>
    <input type="text" name="username" value="<?php echo isset($user['username']) ? $user['username'] : ''; ?>" required>
    <label for="password">Password:</label>
    <input type="password" name="password" value="" required>
    <label for="name">Full Name:</label>
    <input type="text" name="name" value="<?php echo isset($user['name']) ? $user['name'] : ''; ?>" required>
    <button type="submit">Save</button>
</form>

<h2>Employee Accounts</h2>
<?php
$sql = "SELECT * FROM users WHERE role = 'employee'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h3>" . $row['username'] . " (" . $row['name'] . ")</h3>";
    echo "<a href='manage_employees.php?edit=" . $row['id'] . "'>Edit</a>";
    echo "<form method='post' action='delete_user.php' style='display:inline;'>";
    echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
    echo "<button type='submit'>Delete</button>";
    echo "</form>";
    echo "</div>";
}
?>
<a href="admin.php">Back to Admin Panel</a>
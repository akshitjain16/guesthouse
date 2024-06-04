<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $sql = "UPDATE users SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $_SESSION['username'] = $name;
        echo "Profile updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->
fetch_assoc();

?>

<h1>Profile Management</h1>
<form method="post">
    <label for="name">Full Name:</label>
    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
    <button type="submit">Update Profile</button>
</form>
<a href="dashboard.php">Back to Dashboard</a>
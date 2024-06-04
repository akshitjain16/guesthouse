<?php
include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username already exists
        $error_message = "Username already taken. Please choose another.";
    } else {
        // Username is available, proceed with registration
        $sql = "INSERT INTO users (username, password, name, role, email, phone_number) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $password, $name, $role, $email, $phone_number);

        if ($stmt->execute()) {
            echo "Registration successfull!";

            $stmt->close();

        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="../style/register.css">
</head>

<body>
    <div class="container">
        <form method="POST">
            <h2 style="text-align: center;">Registration Page</h2>
            <div class="input-group">
                <select name="role">
                    <option value="employee">Employee</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="input-group">
                <input type="text" name="name" required placeholder="Employee Name">
            </div>
            <div class="input-group">
                <input type="number" name="emp_id" required placeholder="Employee Id">
            </div>
            <div class="input-group">
                <input type="text" name="username" required placeholder="Username">
            </div>
            <div class="input-group">
                <input type="text" name="depname" required placeholder="Department Name">
            </div>
            <div class="input-group">
                <input type="email" name="email" required placeholder="Email">
            </div>
            <div class="input-group">
                <input type="number" name="phone_number" required placeholder="Phone No.">
            </div>
            <div class="input-group">
                <input type="password" name="password" required placeholder="Password">
            </div>

            <button type="submit">Register</button>
        </form>
    </div>
</body>

</html>
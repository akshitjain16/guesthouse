<?php
include '../../config/config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = $_POST['emp_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $role = "employee";
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $department_name = $_POST['depname'];

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
        $sql = "INSERT INTO users (emp_id, username, password, name, role, email, phone_number ,department_name) VALUES (?, ?, ?, ?, ?, ?,?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $emp_id, $username, $password, $name, $role, $email, $phone_number, $department_name);

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

<body class='reg-body'>
<div class="reg-nav"><?php include './navbar.php'; ?></div>
    <div class="reg-container">
        <div class="reg-form">
            <h2>Registration Page</h2>
            <form method="post">
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
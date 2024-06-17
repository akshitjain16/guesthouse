<?php
session_start();
include '../../config/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Function to check for duplicate usernames
function isDuplicateUsername($emp_id, $conn)
{
    $sql = "SELECT * FROM users WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = $_POST['emp_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $department_name = $_POST['depname'];

    if (isDuplicateUsername($emp_id, $conn)) {
        $error_message = "User already exists. Please login.";
    } else {
        $sql = "INSERT INTO users (emp_id, password, name, role, email, phone_number ,department_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $emp_id, $password, $name, $role, $email, $phone_number, $department_name);

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
    <title>Registration Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="navbar-login"><?php include 'navbar.php'; ?></div>
    <div class="container">
        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <h3>New User Registration</h3>
        <form method="POST">
            <div class="form-group">
                <label for="role">Select Role</label>
                    <input type="radio" name="role" value="employee" required> Employee
                    <input type="radio" name="role" value="admin" required> Admin
                    <input type="radio" name="role" value="other" required> Other
            </div>

            <div class="form-group">
                <label for="emp_id">Employee Code</label>
                <input type="number" class="form-control" id="emp_id" name="emp_id" required>
            </div>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" >
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="number" class="form-control" id="phone_number" name="phone_number" >
            </div>
            <div class="form-group">
                <label for="email">Email-ID</label>
                <input type="email" class="form-control" id="email" name="email" >
            </div>
            <div class="form-group">
                <label for="depname">Department Name</label>
                <input type="text" class="form-control" id="depname" name="depname" >
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <a href="admin.php" class="btn btn-secondary mt-3 ">Back to Admin Dashboard</a>
    </div>
</body>

</html>
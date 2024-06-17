<?php
session_start();
include '../../config/config.php';
if (isset($_SESSION['user_id']) || ($_SESSION['role'] == 'employee')) {
    header("Location: dashboard.php");
    exit();
}
elseif(isset($_SESSION['user_id']) || ($_SESSION['role'] == 'admin')){
    header("Location: admin.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = $_POST['emp_id'];
    $password = $_POST['password'];

    $sql = "SELECT emp_id, password, role, name , Department_name, phone_number FROM users WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['emp_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['department'] = $user['Department_name'];
            $_SESSION['contact'] = $user['phone_number'];
            if($_SESSION['role'] === 'employee'){
            header("Location: dashboard.php");
            } elseif($_SESSION['role'] === 'admin'){
                header("Location: admin.php");
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../style/login.css">
</head>

<body class="log-body">
    <div class="log-container">
    <div class="navbar-login"><?php include 'navbar.php'; ?></div>
        <div class="login-form">
            <h2>Login</h2>
            <form method="post">
                <div class="input-group">
                    <label for="emp_id">Employee Code</label>
                    <input type="text" id="emp_id" name="emp_id" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" >Login</button>
                <p style="margin-top: 30px;">
                    <a href="forgot_password.php" style="text-decoration: none ">Forgot Password?</a>
                </p>
            </form>
        </div>
    </div>
</body>

</html>
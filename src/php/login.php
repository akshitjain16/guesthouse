<?php
include '../../config/config.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'employee') {
        header("Location: dashboard.php");
        exit();
    } elseif ($_SESSION["role"] == "admin") {
        header("Location: adminPanel.php");
        exit();
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $emp_id = $_POST['emp_id'];
    $password = $_POST['password'];

    $sql = "SELECT emp_id, password, role, name , Department_name, phone_number, guesthouse_id, status FROM users WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['status'] == 'false') {
            $_SESSION['message'] = "Your account has been deactivated. Please contact the administrator.";
            $_SESSION['message_type'] = "danger";
            header("Location: login.php");
            exit();
        } else {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['emp_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['department'] = $user['Department_name'];
                $_SESSION['contact'] = $user['phone_number'];
                $_SESSION['message'] = "Login successfull!";
                $_SESSION['message_type'] = "success";
                if ($_SESSION['role'] === 'employee') {
                    header("Location: dashboard.php");
                } elseif ($user['role'] === 'admin') {
                    $_SESSION['guesthouse'] = $user['guesthouse_id'];
                    header("Location: adminPanel.php");
                }
            } else {
                $_SESSION['message'] = "Invalid User ID or Password.";
                $_SESSION['message_type'] = "danger";
            }
        }
    } else {
        $_SESSION['message'] = "No User Found.";
        $_SESSION['message_type'] = "danger";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color:#D7E1EB">
    <div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h1 class="text-center">Login</h1>
                        <form method="post" class="mt-4">
                            <div class="form-group">
                                <label for="emp_id">Employee Code</label>
                                <input type="text" class="form-control" id="emp_id" name="emp_id" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                            <p class="text-center mt-3">
                                <a href="forgot_password.php" style="text-decoration: none;">Forgot Password?</a>
                            </p>
                        </form>

                        <?php
                        if (isset($_SESSION['message'])) {
                            echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert fade show" role="alert">';
                            echo $_SESSION['message'];
                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                            echo '<span aria-hidden="true">&times;</span>';
                            echo '</button>';
                            echo '</div>';
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
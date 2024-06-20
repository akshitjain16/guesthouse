<?php
include '../../config/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$guesthouse_id = $_SESSION['guesthouse'];
$background_class = '';
if ($guesthouse_id == 1) {
    $background_class = 'satkar-background';
} elseif ($guesthouse_id == 2) {
    $background_class = 'swagat-background';
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $emp_id = $_POST['emp_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $department_name = $_POST['depname'];

    if (isDuplicateUsername($emp_id, $conn)) {
        $_SESSION['message'] = "User already exists. Please login.";
        $_SESSION['message_type'] = "danger";
    } else {
        $sql = "INSERT INTO users (emp_id, password, name, role, email, phone_number ,department_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $emp_id, $password, $name, $role, $email, $phone_number, $department_name);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Registration successfull!";
            $_SESSION['message_type'] = "success";
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
    <link rel="stylesheet" href="./background.css">
</head>

<body class="<?php echo $background_class; ?>">
<div><?php include 'navbar.php'; ?></div>
    <div class="container mt-5">
        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center">New User Registration</h3>
                        <form method="POST">
                            <div class="form-group">
                                <label for="role">Select Role<span class="required">*</span></label><br>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="role" value="employee" required>
                                    <label class="form-check-label" for="employee">Employee</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="role" value="admin" required>
                                    <label class="form-check-label" for="admin">Admin</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="role" value="other" required>
                                    <label class="form-check-label" for="other">Other</label>
                                </div>
                            </div>
                            <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="emp_id">Employee Code<span class="required">*</span></label>
                                <input type="number" class="form-control" id="emp_id" name="emp_id" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone_number">Phone Number</label>
                                <input type="number" class="form-control" id="phone_number" name="phone_number">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">Email-ID</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="depname">Department Name</label>
                                <input type="text" class="form-control" id="depname" name="depname">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="password">Password<span class="required">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                        </form>
                        <?php
                        if (isset($_SESSION['message'])) {
                            echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert fade show mt-2" role="alert">';
                            echo $_SESSION['message'];
                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                            echo '<span aria-hidden="true">&times;</span>';
                            echo '</button>';
                            echo '</div>';
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                        }
                        ?>
                        <a href="adminPanel.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
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

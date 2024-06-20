<?php
include '../../config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT emp_id, name, email, phone_number, total_payment FROM users WHERE emp_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-5">Your Profile</h1>
        <table class="table table-striped mt-3">
            <tr>
                <th>Employee Code</th>
                <td><?php echo htmlspecialchars($user['emp_id']); ?></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
            </tr>
            <tr>
                <th>Email-ID</th>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
            <!-- </tr>
            <tr>
                <th>Total Payment</th>
                <td>
                    <?php echo htmlspecialchars($user['total_payment']); ?>
                </td>
            </tr> -->
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>

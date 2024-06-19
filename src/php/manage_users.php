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

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM users WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "User account deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    header("Location: manage_users.php");
    exit();
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $sql = "UPDATE users SET name = ?, email = ?, phone_number = ? WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $phone_number, $edit_id);
    if ($stmt->execute()) {
        echo "User details updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    header("Location: manage_users.php");
    exit();
}

// Fetch all users and their total payments from the database
$search_emp_id = $_GET['search_emp_id'] ?? '';
$search_query = $search_emp_id ? "AND u.emp_id = ?" : "";
$sql = "SELECT u.*, IFNULL(SUM(mb.price), 0) AS total_payment
        FROM users u
        LEFT JOIN meal_bookings mb ON u.emp_id = mb.user_id AND mb.guesthouse_id = ?
        WHERE u.role = 'employee' $search_query
        GROUP BY u.emp_id";
$stmt = $conn->prepare($sql);

if ($search_emp_id) {
    $stmt->bind_param("ii", $guesthouse_id, $search_emp_id);
} else {
    $stmt->bind_param("i", $guesthouse_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./background.css">
</head>

<body class="<?php echo $background_class; ?>">
<div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-5">Manage Users</h1>
        <form method="GET" action="manage_users.php" class="form-inline mb-3">
            <input type="text" name="search_emp_id" class="form-control mr-2" placeholder="Enter Employee Code" value="<?php echo htmlspecialchars($search_emp_id); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Employee Code</th>
                    <th>Full Name</th>
                    <th>Email-ID</th>
                    <th>Phone Number</th>
                    <th>Total Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['emp_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($row['total_payment'], 2)); ?></td>
                            <td>
                            <a href="view_bookings.php?emp_id=<?php echo $row['emp_id']; ?>" class="btn btn-sm btn-secondary">View</a>
                                <a href="manage_users.php?edit_id=<?php echo $row['emp_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="manage_users.php?delete_id=<?php echo $row['emp_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="adminPanel.php" class="btn btn-secondary mt-3">Back to Admin Panel</a>
    </div>

    <?php
    // If edit_id is set, display the edit form
    if (isset($_GET['edit_id'])) :
        $edit_id = $_GET['edit_id'];
        $sql = "SELECT * FROM users WHERE emp_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    ?>
        <div class="container">
            <h2 class="mt-5">Edit Employee</h2>
            <form method="POST" action="manage_users.php">
                <div class="form-row">
                    <input type="hidden" name="edit_id" value="<?php echo $user['emp_id']; ?>">
                    <div class="form-group col-md-3">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">Email-ID</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone_number">Phone Number</label>
                        <input type="number" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    <?php endif; ?>
</body>

</html>

<?php
include '../../config/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get the admin's guesthouse_id from the session
$guesthouse_id = $_SESSION['guesthouse'];
$background_class = '';
if ($guesthouse_id == 1) {
    $background_class = 'satkar-background';
} elseif ($guesthouse_id == 2) {
    $background_class = 'swagat-background';
}

// Fetch employees for the dropdown
$employees_query = $conn->query("SELECT emp_id, name FROM users WHERE role='employee' AND guesthouse_id = '$guesthouse_id'");
$employees = $employees_query->fetch_all(MYSQLI_ASSOC);

// Fetch meal bookings based on filters
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$meal_type_filter = isset($_GET['meal_type']) ? $_GET['meal_type'] : '';
$emp_id_filter = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';

// Prepare the base SQL query
$sql = "SELECT mb.user_id, u.name, mb.meal_date, mb.meal_type 
        FROM meal_bookings mb 
        JOIN users u ON mb.user_id = u.emp_id 
        WHERE mb.meal_date = ? AND mb.guesthouse_id = ?";

// Append meal type filter if selected
if ($meal_type_filter) {
    $sql .= " AND mb.meal_type = ?";
}

// Append employee filter if selected
if ($emp_id_filter) {
    $sql .= " AND mb.user_id = ?";
}

$stmt = $conn->prepare($sql);

if ($meal_type_filter && $emp_id_filter) {
    $stmt->bind_param('sssi', $date_filter, $guesthouse_id, $meal_type_filter, $emp_id_filter);
} elseif ($meal_type_filter) {
    $stmt->bind_param('sss', $date_filter, $guesthouse_id, $meal_type_filter);
} elseif ($emp_id_filter) {
    $stmt->bind_param('ssi', $date_filter, $guesthouse_id, $emp_id_filter);
} else {
    $stmt->bind_param('ss', $date_filter, $guesthouse_id);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./background.css">
</head>
<body class="<?php echo $background_class; ?>">
<div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-5">Manage Meals</h1>
        <form class="form-inline mb-4" method="GET" action="">
            <input type="hidden" name="emp_id" value="<?php echo htmlspecialchars($emp_id_filter); ?>">
            <div class="form-group mr-4">
                <label for="date" class="mr-2">Date:</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $date_filter; ?>">
            </div>
            <div class="form-group mr-2">
                <label for="meal_type" class="mr-2">Meal Type:</label>
                <select class="form-control" id="meal_type" name="meal_type">
                    <option value="">All</option>
                    <option value="breakfast" <?php echo $meal_type_filter == 'breakfast' ? 'selected' : ''; ?>>Breakfast</option>
                    <option value="lunch" <?php echo $meal_type_filter == 'lunch' ? 'selected' : ''; ?>>Lunch</option>
                    <option value="dinner" <?php echo $meal_type_filter == 'dinner' ? 'selected' : ''; ?>>Dinner</option>
                </select>
            </div>
            <div class="form-group col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <h2 class="mt-5">Total Bookings for <?php echo htmlspecialchars($date_filter); ?>: <?php echo count($bookings); ?></h2>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Employee Code</th>
                    <th>Employee Name</th>
                    <th>Meal Date</th>
                    <th>Meal Type</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bookings) > 0): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['meal_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['meal_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No bookings found for this date.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>

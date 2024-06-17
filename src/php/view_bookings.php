<?php
session_start();
include '../../config/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch employees for the dropdown
$employees_query = $conn->query("SELECT emp_id, name FROM users WHERE role='employee'");
$employees = $employees_query->fetch_all(MYSQLI_ASSOC);

// Fetch meal bookings based on filters
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$meal_type_filter = isset($_GET['meal_type']) ? $_GET['meal_type'] : '';

$sql = "SELECT mb.user_id, u.name, mb.meal_date, mb.meal_type FROM meal_bookings mb 
        JOIN users u ON mb.user_id = u.emp_id WHERE mb.meal_date = '$date_filter'";

if ($meal_type_filter) {
    $sql .= " AND mb.meal_type = '$meal_type_filter'";
}

$bookings_query = $conn->query($sql);
$bookings = $bookings_query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view bookings</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Manage Meals</h1>
        <form class="form-inline mb-4">
            <div class="form-group mr-2">
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
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <h2>Total Bookings for <?php echo $date_filter; ?>: <?php echo count($bookings); ?></h2>

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

        <!-- <h2 class="mt-5">Add Meal Booking</h2>
        <form action="add_booking.php" method="POST">
            <div class="form-group">
                <label for="employee_id">Select Employee</label>
                <select class="form-control" id="employee_id" name="employee_id" required>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo htmlspecialchars($employee['emp_id']); ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="meal_date">Select Date</label>
                <input type="date" class="form-control" id="meal_date" name="meal_date" required>
            </div>
            <div class="form-group">
                <label for="meal_type">Select Meal Type</label>
                <select class="form-control" id="meal_type" name="meal_type" required>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Booking</button>
        </form> -->
    </div>
</body>
</html>

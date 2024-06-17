<?php
session_start();
include '../../config/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get current date
$current_date = date('Y-m-d');

// Add this function to check if a user exists
function user_exists($conn, $employee_id)
{
    $sql = "SELECT * FROM users WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to check if an employee has booked a meal
function check_meal_booking($conn, $employee_id, $meal_date, $meal_type)
{
    $sql = "SELECT * FROM meal_bookings WHERE user_id = ? AND meal_date = ? AND meal_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $employee_id, $meal_date, $meal_type);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to count total bookings for a given date
function count_total_bookings($conn, $meal_date, $meal_type)
{
    $sql = "SELECT COUNT(*) AS total FROM meal_bookings WHERE meal_date = ? AND meal_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $meal_date, $meal_type);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['check_booking'])) {
        $employee_id = $_POST['employee_id'];
        $meal_date = $_POST['meal_date'];
        $meal_type = $_POST['meal_type'];
        if (user_exists($conn, $employee_id)) {
            $booking_exists = check_meal_booking($conn, $employee_id, $meal_date, $meal_type);
            $user_exists = true;
        } else {
            $user_exists = false;
        }
    } elseif (isset($_POST['update_booking'])) {
        $employee_id = $_POST['employee_id'];
        $meal_date = $_POST['meal_date'];
        $meal_type = $_POST['meal_type'];
        if (user_exists($conn, $employee_id)) {
            $sql = "INSERT INTO meal_bookings (user_id, meal_date, meal_type) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $employee_id, $meal_date, $meal_type);
            $stmt->execute();
            $booking_exists = true;  // Assuming booking was successful
            $user_exists = true;
            // echo json_encode(['message' => 'Check-in status updated successfully']);
        } else {
            $user_exists = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Check</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/booking.css">
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <h2>Total Bookings</h2>
            <form action="booking_check.php" method="POST">
                <div class="form-group">
                    <label for="filter_date">Select Date</label>
                    <input type="date" class="form-control" id="filter_date" name="filter_date" value="<?php echo $current_date; ?>" required>
                </div>
                <div class="form-group">
                    <label for="meal_type">Meal Type</label>
                    <select class="form-control" id="meal_type" name="meal_type" required>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>
                <button type="submit" name="filter_bookings" class="btn btn-primary">Total Bookings</button>
                <a href="view_bookings.php" class=" btn btn-primary mt-3">view bookings</a>
            </form>

            <?php if (isset($_POST['filter_bookings'])) : ?>
                <?php
                $filter_date = $_POST['filter_date'];
                $filter_meal_type = $_POST['meal_type'];
                $total_bookings = count_total_bookings($conn, $filter_date, $filter_meal_type);
                ?>
                <div class="mt-3">
                    <h3>Total Bookings for <?php echo htmlspecialchars($filter_date); ?> (<?php echo htmlspecialchars($filter_meal_type); ?>): <?php echo $total_bookings; ?></h3>
                </div>
            <?php endif; ?>
        </div>

        <div class="right-panel">
            <h2>Check Booking Details</h2>
            <form action="booking_check.php" method="POST">
                <div class="form-group">
                    <label for="employee_id">Employee ID</label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                </div>
                <div class="form-group">
                    <label for="meal_type">Meal Type</label>
                    <select class="form-control" id="meal_type" name="meal_type" required>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="meal_date">Meal Date</label>
                    <input type="date" class="form-control" id="meal_date" name="meal_date" value="<?php echo $current_date; ?>" required>
                </div>
                <button type="submit" name="check_booking" class="btn btn-primary">Check Booking</button>
            </form>

            <?php if (isset($user_exists) && !$user_exists) : ?>
                <div class="mt-3 alert alert-danger">
                    <strong>Error:</strong> Employee does not exist.
                </div>
            <?php endif; ?>

            <?php if (isset($user_exists) && $user_exists && isset($booking_exists)) : ?>
                <div class="mt-3">
                    <h3>Booking Status: <?php echo $booking_exists ? 'Yes' : 'No'; ?></h3>
                </div>
            <?php endif; ?>

            <?php if (isset($user_exists) && $user_exists && isset($booking_exists) && !$booking_exists) : ?>
                <form action="booking_check.php" method="POST" class="mt-3">
                    <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee_id); ?>">
                    <input type="hidden" name="meal_date" value="<?php echo htmlspecialchars($meal_date); ?>">
                    <div class="form-group">
                        <label for="meal_type">Meal Type</label>
                        <select class="form-control" id="meal_type" name="meal_type" required>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                    <button type="submit" name="update_booking" class="btn btn-success">Update Booking</button>
                </form>
            <?php endif; ?>
        </div>
        
    
    </div>
    <!-- <a href="admin.php" class="back-btn">Back to Admin Panel</a> -->
     
</body>

</html>
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

// Get current date
$current_date = date('Y-m-d');

// Function to check if a user exists
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
function check_meal_booking($conn, $employee_id, $meal_date, $meal_type, $guesthouse_id)
{
    $sql = "SELECT * FROM meal_bookings WHERE user_id = ? AND meal_date = ? AND meal_type = ? AND guesthouse_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $employee_id, $meal_date, $meal_type, $guesthouse_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to count total bookings for a given date
function count_total_bookings($conn, $meal_date, $meal_type, $guesthouse_id)
{
    $sql = "SELECT COUNT(*) AS total FROM meal_bookings WHERE meal_date = ? AND meal_type = ? AND guesthouse_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $meal_date, $meal_type, $guesthouse_id);
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
            $booking_exists = check_meal_booking($conn, $employee_id, $meal_date, $meal_type, $guesthouse_id);
            $user_exists = true;
        } else {
            $user_exists = false;
        }
    } elseif (isset($_POST['update_booking'])) {
        $employee_id = $_POST['employee_id'];
        $meal_date = $_POST['meal_date'];
        $meal_type = $_POST['meal_type'];
        if (user_exists($conn, $employee_id)) {
            // Ensure that the admin can only update bookings for their guesthouse
            $day_of_week = date('l', strtotime($meal_date));  // Full name of the day

            // Fetch price from menus table for the specific guesthouse
            $price_sql = "SELECT price FROM menus WHERE meal_type = ? AND weekday = ? AND guesthouse_id = ?";
            $price_stmt = $conn->prepare($price_sql);
            $price_stmt->bind_param("ssi", $meal_type, $day_of_week, $guesthouse_id);
            $price_stmt->execute();
            $price_result = $price_stmt->get_result();
            $price_row = $price_result->fetch_assoc();
            $price = $price_row['price'];

            // Insert into meal_bookings table for the specific guesthouse
            $sql = "INSERT INTO meal_bookings (user_id, meal_date, meal_type, price, guesthouse_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdi", $employee_id, $meal_date, $meal_type, $price, $guesthouse_id);
            $stmt->execute();

            // Update total_payment for the user in the specific guesthouse
            $update_sql = "UPDATE users SET total_payment = total_payment + ? WHERE emp_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("di", $price, $employee_id);
            $update_stmt->execute();

            $booking_exists = true;  // Assuming booking was successful
            $user_exists = true;
            $_SESSION['success'] = "Check-in status updated successfully!";
        } else {
            $user_exists = false;
        }
    }
}

// Fetch error and success messages from session
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';

unset($_SESSION['error']);
unset($_SESSION['success']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./background.css">
</head>

<body class="<?php echo $background_class; ?>">
    <div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <?php if ($guesthouse_id == 1) { ?>
            <h1 class="mt-3">Welcome to Satkar Admin Panel!</h1>
        <?php } elseif ($guesthouse_id == 2) { ?>
            <h1 class="mt-3">Welcome to Swagat Admin Panel!</h1>
        <?php } ?>
        <div class="left-panel">
            <h2 class="mt-5">Total Bookings</h2>
            <form method="POST" class="mt-3">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="filter_date">Select Date</label>
                        <input type="date" class="form-control" id="filter_date" name="filter_date" value="<?php echo $current_date; ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="meal_type">Meal Type</label>
                        <select class="form-control" id="meal_type" name="meal_type" required>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 align-self-end">
                        <button type="submit" name="filter_bookings" class="btn btn-primary">Total Bookings</button>
                        <a href="view_bookings.php" class=" btn btn-primary">View Bookings</a>
                    </div>
                </div>
            </form>

            <?php if (isset($_POST['filter_bookings'])) : ?>
                <?php
                $filter_date = $_POST['filter_date'];
                $filter_meal_type = $_POST['meal_type'];
                $total_bookings = count_total_bookings($conn, $filter_date, $filter_meal_type, $guesthouse_id);
                ?>
                <div class="mt-3">
                    <h3>Total Bookings for <?php echo htmlspecialchars($filter_date); ?> (<?php echo htmlspecialchars($filter_meal_type); ?>): <?php echo $total_bookings; ?></h3>
                </div>
            <?php endif; ?>
        </div>

        <div class="right-panel">
            <h2 class="mt-5">Check Booking Details</h2>
            <form method="POST" class="mt-3">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="employee_id">Employee ID</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="meal_type">Meal Type</label>
                        <select class="form-control" id="meal_type" name="meal_type" required>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                    <div class="form-group ">
                        <label for="meal_date">Meal Date</label>
                        <input type="date" class="form-control" id="meal_date" name="meal_date" value="<?php echo $current_date; ?>" required>
                    </div>
                    <div class="form-group col-md-3 align-self-end">
                        <button type="submit" name="check_booking" class="btn btn-primary">Check Booking</button>
                    </div>
                </div>
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
                <form method="POST" class="mt-3">
                    <div class="form-row">
                        <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee_id); ?>">
                        <input type="hidden" name="meal_date" value="<?php echo htmlspecialchars($meal_date); ?>">
                        <input type="hidden" name="meal_type" value="<?php echo htmlspecialchars($meal_type); ?>">
                        <div class="form-group col-md-3">
                            <label for="meal_type">Meal Type</label>
                            <input type="text" class="form-control" id="meal_type" name="meal_type_display" value="<?php echo htmlspecialchars($meal_type); ?>" disabled>
                        </div>
                        <div class="form-group col-md-3 align-self-end">
                            <button type="submit" name="update_booking" class="btn btn-success">Update Booking</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success mt-3">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
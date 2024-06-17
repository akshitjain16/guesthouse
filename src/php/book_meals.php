<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function is_booking_exists($conn, $user_id, $meal_date, $meal_type) {
    if ($meal_type == 'all') {
        $sql = "SELECT * FROM meal_bookings WHERE user_id = ? AND meal_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $meal_date);
    } else {
        $sql = "SELECT * FROM meal_bookings WHERE user_id = ? AND meal_date = ? AND meal_type = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $meal_date, $meal_type);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in session
$today = date('Y-m-d');

// Fetch last booked day
$sql_last_booked = "SELECT MAX(meal_date) as last_booked_date FROM meal_bookings WHERE user_id = ?";
$stmt_last_booked = $conn->prepare($sql_last_booked);
$stmt_last_booked->bind_param("i", $user_id);
$stmt_last_booked->execute();
$result_last_booked = $stmt_last_booked->get_result();
$last_booked = $result_last_booked->fetch_assoc()['last_booked_date'];

// Fetch all prebooked meals
$sql_prebooked = "SELECT meal_date, meal_type FROM meal_bookings WHERE user_id = ? ORDER BY meal_date";
$stmt_prebooked = $conn->prepare($sql_prebooked);
$stmt_prebooked->bind_param("i", $user_id);
$stmt_prebooked->execute();
$result_prebooked = $stmt_prebooked->get_result();
$prebooked_meals = [];
while ($row = $result_prebooked->fetch_assoc()) {
    $prebooked_meals[] = $row;
}

// Combine meal types for the same date
$combined_meals = [];
foreach ($prebooked_meals as $meal) {
    $date = $meal['meal_date'];
    if (!isset($combined_meals[$date])) {
        $combined_meals[$date] = [];
    }
    $combined_meals[$date][] = $meal['meal_type'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_meals'])) {
    $meal_date_from = $_POST['meal_date_from'];
    $meal_date_to = $_POST['meal_date_to'];
    $meal_type = $_POST['meal_type'];

    $error = '';
    $success = '';

    $start_date = new DateTime($meal_date_from);
    $end_date = new DateTime($meal_date_to);
    $end_date->modify('+1 day'); // Include the end date in the range

    while ($start_date < $end_date) {
        $meal_date = $start_date->format('Y-m-d');
        if ($meal_type == 'all') {
            $meal_types = ['breakfast', 'lunch', 'dinner'];
        } else {
            $meal_types = [$meal_type];
        }

        foreach ($meal_types as $type) {
            if (is_booking_exists($conn, $user_id, $meal_date, $type)) {
                $_SESSION['error'] = "You have already booked till $last_booked.";
                header("Location: book_meals.php");
                exit();
            }
        }

        $start_date->modify('+1 day');
    }

    if (empty($error)) {
        $start_date = new DateTime($meal_date_from);
        while ($start_date < $end_date) {
            $meal_date = $start_date->format('Y-m-d');
            foreach ($meal_types as $type) {
                $sql = "INSERT INTO meal_bookings (user_id, meal_date, meal_type) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $user_id, $meal_date, $type);
                $stmt->execute();
            }
            $start_date->modify('+1 day');

        }
        $_SESSION['success'] = "Meal(s) booked successfully.";
        header("Location: book_meals.php");
        exit();
    }
}


// Fetch error and success messages from session
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';

// Unset session error and success messages
unset($_SESSION['error']);
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Meals</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function togglePrebookedMeals() {
            var prebookedMealsDiv = document.getElementById('prebookedMealsDiv');
            if (prebookedMealsDiv.style.display === 'none') {
                prebookedMealsDiv.style.display = 'block';
            } else {
                prebookedMealsDiv.style.display = 'none';
            }
        }
    </script>
</head>

<body>
<div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-5">Book Meals</h1>
        <form action="book_meals.php" method="POST">
            <div class="form-group">
                <label for="meal_date_from">From Date</label>
                <input type="date" class="form-control" id="meal_date_from" name="meal_date_from" min="<?php echo $today; ?>" required>
            </div>
            <div class="form-group">
                <label for="meal_date_to">To Date</label>
                <input type="date" class="form-control" id="meal_date_to" name="meal_date_to" min="<?php echo $today; ?>" required>
            </div>
            <div class="form-group">
                <label for="meal_type">Meal Type</label>
                <select class="form-control" id="meal_type" name="meal_type" required>
                    <option value="all">All</option>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                </select>
            </div>
            <button type="submit" name="book_meals" class="btn btn-primary">Book Meals</button>
        </form>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger mt-3">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success mt-3">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <button class="btn btn-info mt-3" onclick="togglePrebookedMeals()">Show bookings</button>

        <div id="prebookedMealsDiv" style="display: none;">
        <h3 class="mt-3" style="text-align:center">Booked Meals</h3>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Meal Types</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($combined_meals) > 0): ?>
                        <?php foreach ($combined_meals as $date => $meals): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($date); ?></td>
                                <td><?php echo htmlspecialchars(implode(', ', $meals)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No booked meals found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-secondary m-5">Back to Dashboard</a>
</body>
</html>

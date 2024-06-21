<?php
include '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in session

function is_booking_exists($conn, $user_id, $meal_date, $meal_type, $guesthouse_id)
{
    if ($meal_type == 'all') {
        $sql = "SELECT * FROM meal_bookings WHERE user_id = ? AND meal_date = ? AND guesthouse_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $user_id, $meal_date, $guesthouse_id);
    } else {
        $sql = "SELECT * FROM meal_bookings WHERE user_id = ? AND meal_date = ? AND meal_type = ? AND guesthouse_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $user_id, $meal_date, $meal_type, $guesthouse_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

$today = date('Y-m-d');

// Fetch last booked day
$sql_last_booked = "SELECT MAX(meal_date) as last_booked_date FROM meal_bookings WHERE user_id = ? AND guesthouse_id = ?";
$stmt_last_booked = $conn->prepare($sql_last_booked);
$stmt_last_booked->bind_param("ii", $user_id, $guesthouse_id);
$stmt_last_booked->execute();
$result_last_booked = $stmt_last_booked->get_result();
$last_booked = $result_last_booked->fetch_assoc()['last_booked_date'];

// Fetch all prebooked meals from today onwards
$sql_prebooked = "SELECT meal_date, meal_type, guesthouse_id FROM meal_bookings WHERE user_id = ? AND meal_date >= ? ORDER BY meal_date";
$stmt_prebooked = $conn->prepare($sql_prebooked);
$stmt_prebooked->bind_param("is", $user_id, $today);
$stmt_prebooked->execute();
$result_prebooked = $stmt_prebooked->get_result();
$prebooked_meals = [];
while ($row = $result_prebooked->fetch_assoc()) {
    $prebooked_meals[] = $row;
}

// Combine meal types for the same date and guesthouse
$combined_meals = [];
foreach ($prebooked_meals as $meal) {
    $date = $meal['meal_date'];
    $guesthouse = $meal['guesthouse_id'];
    if (!isset($combined_meals[$guesthouse])) {
        $combined_meals[$guesthouse] = [];
    }
    if (!isset($combined_meals[$guesthouse][$date])) {
        $combined_meals[$guesthouse][$date] = [];
    }
    $combined_meals[$guesthouse][$date][] = $meal['meal_type'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_meals'])) {
    $meal_date_from = $_POST['meal_date_from'];
    $meal_date_to = $_POST['meal_date_to'];
    $meal_type = $_POST['meal_type'];
    $guesthouse_id = $_POST['guesthouse_id'];

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
            if (is_booking_exists($conn, $user_id, $meal_date, $type, $guesthouse_id)) {
                $_SESSION['error'] = "You have already booked $type for $meal_date.";
                header("Location: dashboard.php");
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
                $day_of_week = date('l', strtotime($meal_date));  // Full name of the day

                // Fetch price from menus table
                $price_sql = "SELECT price FROM menus WHERE meal_type = ? AND weekday = ? AND guesthouse_id = ?";
                $price_stmt = $conn->prepare($price_sql);
                $price_stmt->bind_param("ssi", $type, $day_of_week, $guesthouse_id);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $price_row = $price_result->fetch_assoc();
                $price = $price_row['price'];

                $sql = "INSERT INTO meal_bookings (user_id, meal_date, meal_type, price, guesthouse_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issdi", $user_id, $meal_date, $type, $price, $guesthouse_id);
                $stmt->execute();

                $update_sql = "UPDATE users SET total_payment = total_payment + ? WHERE emp_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("di", $price, $user_id);
                $update_stmt->execute();
            }

            $start_date->modify('+1 day');
        }
        $_SESSION['success'] = "Meals booked successfully and an confirmation message has been sent successfully.";
        header("Location: " . $_SERVER['REQUEST_URI']);
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
    <title>Bookings</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .highlight-today {
            background-color: yellow;
        }
    </style>
    <script>
        function togglePrebookedMeals() {
            var prebookedMealsDiv = document.getElementById('prebookedMealsDiv');
            if (prebookedMealsDiv.style.display === 'none') {
                prebookedMealsDiv.style.display = 'block';
            } else {
                prebookedMealsDiv.style.display = 'none';
            }
        }

        function filterBookings() {
            var selectedGuesthouse = document.getElementById('filter_guesthouse').value;
            var rows = document.querySelectorAll('#prebookedMealsTable tbody tr');
            rows.forEach(function(row) {
                var guesthouse = row.getAttribute('data-guesthouse');
                if (selectedGuesthouse === '' || guesthouse === selectedGuesthouse) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>

<body style="background-color:#DCDEDF">
    <div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-5">Book Meals</h1>
        <form method="POST">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="meal_date_from">From Date</label>
                    <input type="date" class="form-control" id="meal_date_from" name="meal_date_from" min="<?php echo $today; ?>" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="meal_date_to">To Date</label>
                    <input type="date" class="form-control" id="meal_date_to" name="meal_date_to" min="<?php echo $today; ?>" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="meal_type">Meal Type</label>
                    <select class="form-control" id="meal_type" name="meal_type" required>
                        <option value="all">All</option>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="guesthouse_id">Guest House</label>
                    <select class="form-control" id="guesthouse_id" name="guesthouse_id" required>
                        <option value="1">Satkar</option>
                        <option value="2">Swagat</option>
                    </select>
                </div>
                <div class="form-group col-md-4 align-self-end">
                    <button type="submit" name="book_meals" class="btn btn-primary">Book Meals</button>
                </div>
            </div>
        </form>

        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger mt-3">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success mt-3">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <button class="btn btn-info" onclick="togglePrebookedMeals()">Show bookings</button>

        <div id="prebookedMealsDiv" style="display: none;">
            <h3 class="mt-3" style="text-align:center">Booked Meals</h3>
            <div class="form-group col-md-3">
                <label for="filter_guesthouse">Filter by Guest House</label>
                <select class="form-control" id="filter_guesthouse" onchange="filterBookings()">
                    <option value="">All</option>
                    <option value="1">Satkar</option>
                    <option value="2">Swagat</option>
                </select>
            </div>
            <div class="table-responsive">
            <table class="table table-striped mt-3" id="prebookedMealsTable" style="background-color:white">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Meal Types</th>
                        <th>Guest House</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($combined_meals) > 0) : ?>
                        <?php foreach ($combined_meals as $guesthouse => $meals_by_date) : ?>
                            <?php foreach ($meals_by_date as $date => $meals) : ?>
                                <tr data-guesthouse="<?php echo $guesthouse; ?>" class="<?php echo $date == $today ? 'highlight-today' : ''; ?>">
                                    <td><?php echo date('d-F-Y', strtotime($date)); ?></td>
                                    <td><?php echo htmlspecialchars(implode(', ', $meals)); ?></td>
                                    <td><?php echo $guesthouse == 1 ? 'Satkar' : 'Swagat'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3">No booked meals found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <script>
        // Remove success message after 3 seconds
        setTimeout(function() {
            let alertSuccess = document.querySelector('.alert');
            if (alertSuccess) {
                alertSuccess.style.display = 'none';
            }
        }, 3000);
    </script>
</body>

</html>

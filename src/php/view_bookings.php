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

// Default date range for current month
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

// Initialize filters
$date_from_filter = isset($_GET['date_from']) ? $_GET['date_from'] : $current_month_start;
$date_to_filter = isset($_GET['date_to']) ? $_GET['date_to'] : $current_month_end;
$meal_type_filter = isset($_GET['meal_type']) ? $_GET['meal_type'] : '';
$emp_id_filter = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';

// Prepare the base SQL query
$sql = "SELECT mb.user_id, u.name, mb.meal_date, mb.meal_type 
        FROM meal_bookings mb 
        JOIN users u ON mb.user_id = u.emp_id 
        WHERE mb.meal_date BETWEEN ? AND ? AND mb.guesthouse_id = ?";

// Append meal type filter if selected
if ($meal_type_filter) {
    $sql .= " AND mb.meal_type = ?";
}

// Append employee filter if selected
if ($emp_id_filter) {
    $sql .= " AND mb.user_id = ?";
}

$stmt = $conn->prepare($sql);

$params = array($date_from_filter, $date_to_filter, $guesthouse_id);

if ($meal_type_filter) {
    $params[] = $meal_type_filter;
}

if ($emp_id_filter) {
    $params[] = $emp_id_filter;
}

// Bind parameters
$stmt->bind_param(str_repeat('s', count($params)), ...$params);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total counts and total payment for current month
$total_breakfast = 0;
$total_lunch = 0;
$total_dinner = 0;
$total_payment = 0;

foreach ($bookings as $booking) {
    switch ($booking['meal_type']) {
        case 'breakfast':
            $total_breakfast++;
            break;
        case 'lunch':
            $total_lunch++;
            break;
        case 'dinner':
            $total_dinner++;
            break;
    }
    // Assuming there's a price column in meal_bookings table
    // $total_payment += $booking['price'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meals</title>
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
                <label for="date_from" class="mr-2">From Date:</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from_filter; ?>">
            </div>
            <div class="form-group mr-4">
                <label for="date_to" class="mr-2">To Date:</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to_filter; ?>">
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
            <div class="form-group align-self-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="view_bookings.php?emp_id=<?php echo $emp_id_filter; ?>" class="btn btn-secondary ml-2">Remove</a>
                <a class="btn btn-success ml-2" onclick="exportTableToExcel('mealTable', 'Bookings')">Export</a>
            </div>
        </form>


        <h2 class="mt-5">Total Bookings from <?php echo date('d-F-Y', strtotime($date_from_filter)); ?> to <?php echo date('d-F-Y', strtotime($date_to_filter)); ?>:</h2>
        <div class="form-row">
            <div class="mr-3 ml-2">
                <p>Breakfast: <?php echo $total_breakfast; ?></p>
            </div>
            <div class="mr-3">
                <p>Lunch: <?php echo $total_lunch; ?></p>
            </div>
            <div class="mr-3">
                <p>Dinner: <?php echo $total_dinner; ?></p>
            </div>
            <!-- Uncomment this if you have total payment calculation -->
            <!-- <p>Total Payment: <?php echo $total_payment; ?> Rs.</p> -->
        </div>

        <div class="table-responsive">
            <table class="table table-striped mt-3" id="mealTable" style="background-color: white;">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Employee Name</th>
                        <th>Meal Date</th>
                        <th>Meal Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0) : ?>
                        <?php foreach ($bookings as $booking) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                <td><?php echo date('d-F-Y', strtotime($booking['meal_date'])); ?></td>
                                <td><?php echo htmlspecialchars($booking['meal_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No bookings found for this date range.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    </div>

    <script>
        function exportTableToExcel(tableID, filename = '') {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            // Specify file name
            filename = filename ? filename + '.xls' : 'excel_data.xls';

            // Create download link element
            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if (navigator.msSaveOrOpenBlob) {
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }
        }
    </script>
</body>

</html>
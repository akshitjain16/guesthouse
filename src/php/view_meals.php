<?php
include '../../config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$meal_type_filter = isset($_GET['meal_type']) ? $_GET['meal_type'] : '';
$guesthouse_filter = isset($_GET['guesthouse_id']) ? $_GET['guesthouse_id'] : '';

// Get the current month
$current_month_start = date("Y-m-01");
$current_month_end = date("Y-m-t");

// Fetch the user's total meal history and total price for the current month
$sql_total = "SELECT price FROM meal_bookings WHERE user_id = ? AND meal_date BETWEEN ? AND ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("iss", $user_id, $current_month_start, $current_month_end);
$stmt_total->execute();
$result_total = $stmt_total->get_result();

$total_price = 0;
while ($row_total = $result_total->fetch_assoc()) {
    $total_price += $row_total['price'];
}

// Build the SQL query based on the filters for the filtered total price and meals display
$sql_filtered = "SELECT meal_date, meal_type, price, guesthouse_id FROM meal_bookings WHERE user_id = ?";

$params = [$user_id];
$types = "i";

if ($from_date && $to_date) {
    $sql_filtered .= " AND meal_date BETWEEN ? AND ?";
    $params[] = $from_date;
    $params[] = $to_date;
    $types .= "ss";
} elseif ($from_date) {
    $sql_filtered .= " AND meal_date >= ?";
    $params[] = $from_date;
    $types .= "s";
} elseif ($to_date) {
    $sql_filtered .= " AND meal_date <= ?";
    $params[] = $to_date;
    $types .= "s";
} else {
    // Default to showing current month's bookings
    $sql_filtered .= " AND meal_date BETWEEN ? AND ?";
    $params[] = $current_month_start;
    $params[] = $current_month_end;
    $types .= "ss";
}

if ($meal_type_filter && $meal_type_filter !== 'all') {
    $sql_filtered .= " AND meal_type = ?";
    $params[] = $meal_type_filter;
    $types .= "s";
}

if ($guesthouse_filter && $guesthouse_filter !== 'all') {
    $sql_filtered .= " AND guesthouse_id = ?";
    $params[] = $guesthouse_filter;
    $types .= "i";
}

$sql_filtered .= " ORDER BY meal_date";
$stmt_filtered = $conn->prepare($sql_filtered);
$stmt_filtered->bind_param($types, ...$params);
$stmt_filtered->execute();
$result_filtered = $stmt_filtered->get_result();

$meals = [];
$filtered_price = 0;

while ($row_filtered = $result_filtered->fetch_assoc()) {
    $meals[] = $row_filtered;
    $filtered_price += $row_filtered['price'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meals History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleTable() {
            var table = document.getElementById('mealTable');
            table.style.display = (table.style.display === 'none') ? 'table' : 'none';
        }

        function resetFilters() {
            window.location.href = 'view_meals.php';
        }
    </script>
</head>

<body style="background-color:#DCDEDF">
    <div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-5 ">Your Meal History</h1>
        <h3 class="mt-4 total-payment">Total Charges for Current Month: <?php echo number_format($total_price, 2); ?> Rs.</h3>
        <?php if ($from_date || $to_date || $meal_type_filter || $guesthouse_filter) : ?>
            <h3>Filtered Charges: $<?php echo number_format($filtered_price, 2); ?></h3>
        <?php endif; ?>
        <form method="GET" class="mt-3">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="from_date">From Date:</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="to_date">To Date:</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="meal_type">Meal Type:</label>
                    <select class="form-control" id="meal_type" name="meal_type">
                        <option value="all" <?php echo $meal_type_filter == 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="breakfast" <?php echo $meal_type_filter == 'breakfast' ? 'selected' : ''; ?>>Breakfast</option>
                        <option value="lunch" <?php echo $meal_type_filter == 'lunch' ? 'selected' : ''; ?>>Lunch</option>
                        <option value="dinner" <?php echo $meal_type_filter == 'dinner' ? 'selected' : ''; ?>>Dinner</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="guesthouse_id">Guest House:</label>
                    <select class="form-control" id="guesthouse_id" name="guesthouse_id">
                        <option value="all" <?php echo $guesthouse_filter == 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="1" <?php echo $guesthouse_filter == '1' ? 'selected' : ''; ?>>Satkar</option>
                        <option value="2" <?php echo $guesthouse_filter == '2' ? 'selected' : ''; ?>>Swagat</option>
                    </select>
                </div>
                <div class="form-group col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">Remove Filters</button>
                </div>
            </div>
        </form>
        <button class="btn btn-primary mt-3 " onclick="toggleTable()">Show History</button>
        <button class="btn btn-success mt-3" onclick="exportTableToExcel('mealTable', 'meal_history')">Export as Excel File</button>
        <div class="table-responsive">
            <table class="table table-striped mt-3" id="mealTable" style="display:none;" style="background-color: white;">
                <thead>
                    <tr>
                        <th>Meal Date</th>
                        <th>Meal Type</th>
                        <th>Guesthouse</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($meals)) : ?>
                        <?php foreach ($meals as $meal) : ?>
                            <tr>
                                <td class="date-format"><?php echo date('d-F-Y', strtotime($meal['meal_date'])); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($meal['meal_type'])); ?></td>
                                <td><?php echo $meal['guesthouse_id'] == 1 ? 'Satkar' : 'Swagat'; ?></td>
                                <td><?php echo htmlspecialchars($meal['price']); ?> Rs.</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No meals found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="adminPanel.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
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
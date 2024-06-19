<?php
include '../../config/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

// Fetch all guesthouses
$guesthouses_query = $conn->query("SELECT guesthouse_id, name FROM guesthouses");
$guesthouses = $guesthouses_query->fetch_all(MYSQLI_ASSOC);

// Fetch all meal types
$meal_types_query = $conn->query("SELECT DISTINCT meal_type FROM menus");
$meal_types = $meal_types_query->fetch_all(MYSQLI_ASSOC);

// Set default filters
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : date('Y-m-d');
$filter_meal_type = isset($_GET['filter_meal_type']) ? $_GET['filter_meal_type'] : '';
$filter_guesthouse_id = isset($_GET['filter_guesthouse_id']) ? $_GET['filter_guesthouse_id'] :2;

// Prepare the base SQL query
$sql = "SELECT m.*, g.name AS guesthouse_name FROM menus m 
        JOIN guesthouses g ON m.guesthouse_id = g.guesthouse_id 
        WHERE m.weekday = DAYNAME(?)";

// Append meal type filter if selected
if ($filter_meal_type) {
    $sql .= " AND m.meal_type = ?";
}

// Append guesthouse filter if selected
if ($filter_guesthouse_id) {
    $sql .= " AND m.guesthouse_id = ?";
}

$stmt = $conn->prepare($sql);

if ($filter_meal_type && $filter_guesthouse_id) {
    $stmt->bind_param('ssi', $filter_date, $filter_meal_type, $filter_guesthouse_id);
} elseif ($filter_meal_type) {
    $stmt->bind_param('ss', $filter_date, $filter_meal_type);
} elseif ($filter_guesthouse_id) {
    $stmt->bind_param('si', $filter_date, $filter_guesthouse_id);
} else {
    $stmt->bind_param('s', $filter_date);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
$menus = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color:#DCDEDF">
    <div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-3">Menu Management</h1>
        <form method="GET" action="" class="form-group mb-3">
            <div class="form-row mt-5">
            <div class="form-group col-md-2">
                <label for="filter_date" class="mr-2">Select Date:</label>
                <input type="date" class="form-control" id="filter_date" name="filter_date" value="<?php echo $filter_date; ?>">
            </div>
            <div class="form-group col-md-2">
                <label for="filter_meal_type" class="mr-2">Meal Type:</label>
                <select class="form-control" id="filter_meal_type" name="filter_meal_type">
                    <option value="">All</option>
                    <?php foreach ($meal_types as $meal_type) : ?>
                        <option value="<?php echo $meal_type['meal_type']; ?>" <?php echo $filter_meal_type == $meal_type['meal_type'] ? 'selected' : ''; ?>>
                            <?php echo ucfirst($meal_type['meal_type']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label for="filter_guesthouse_id" class="mr-2">Guesthouse:</label>
                <select class="form-control" id="filter_guesthouse_id" name="filter_guesthouse_id">
                    <option value="">All</option>
                    <?php foreach ($guesthouses as $guesthouse) : ?>
                        <option value="<?php echo $guesthouse['guesthouse_id']; ?>" <?php echo $filter_guesthouse_id == $guesthouse['guesthouse_id'] ? 'selected' : ''; ?>>
                            <?php echo $guesthouse['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            </div>
        </form>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Meal Type</th>
                    <th>Guesthouse</th>
                    <th>Menu</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($menus) > 0) : ?>
                    <?php foreach ($menus as $menu) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu['weekday']); ?></td>
                            <td><?php echo htmlspecialchars($menu['meal_type']); ?></td>
                            <td><?php echo htmlspecialchars($menu['guesthouse_name']); ?></td>
                            <td><?php echo htmlspecialchars($menu['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($menu['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">No menus found for the selected filters.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>

</html>
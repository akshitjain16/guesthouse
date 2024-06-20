<?php
include '../../config/config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Retrieve guesthouse_id from session
$guesthouse_id = $_SESSION['guesthouse'];
$background_class = '';
if ($guesthouse_id == 1) {
    $background_class = 'satkar-background';
} elseif ($guesthouse_id == 2) {
    $background_class = 'swagat-background';
}

// Function to fetch menu items
function fetch_menu_items($conn, $guesthouse_id) {
    $sql = "SELECT * FROM menus WHERE guesthouse_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $guesthouse_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Function to add or update a menu item
function add_or_update_menu_item($conn, $guesthouse_id, $meal_type, $weekday, $item_name, $price) {
    // Check if the item already exists
    $sql_check = "SELECT * FROM menus WHERE guesthouse_id = ? AND meal_type = ? AND weekday = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iss", $guesthouse_id, $meal_type, $weekday);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Item exists, update it
        $sql_update = "UPDATE menus SET item_name = ?, price = ? WHERE guesthouse_id = ? AND meal_type = ? AND weekday = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sdiss", $item_name, $price, $guesthouse_id, $meal_type, $weekday);
        return $stmt_update->execute();
    } else {
        // Item does not exist, insert it
        $sql_insert = "INSERT INTO menus (guesthouse_id, meal_type, weekday, item_name, price) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isssd", $guesthouse_id, $meal_type, $weekday, $item_name, $price);
        return $stmt_insert->execute();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_menu'])) {
        $meal_type = $_POST['meal_type'];
        $weekday = $_POST['weekday'];
        $item_name = $_POST['item_name'];
        $price = $_POST['price'];
        add_or_update_menu_item($conn, $guesthouse_id, $meal_type, $weekday, $item_name, $price);
        $_SESSION['success'] = "Menu has been updated successfully!";
    }
}

// Fetch existing menu items
$menu_items = fetch_menu_items($conn, $guesthouse_id);

// Fetch success message from session
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
     <link rel="stylesheet" href="./background.css">
</head>

<body class="<?php echo $background_class; ?>">
    <div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <h1 class="mt-5">Manage Menu</h1>

        <form method="POST" class="mt-5">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="meal_type">Meal Type</label>
                    <select class="form-control" id="meal_type" name="meal_type" required>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="weekday">Weekday</label>
                    <select class="form-control" id="weekday" name="weekday" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="item_name">Item Name</label>
                    <input type="text" class="form-control" id="item_name" name="item_name" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="price">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
            </div>
            <button type="submit" name="add_menu" class="btn btn-primary">Update Menu</button>
        </form>

        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success mt-3">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <h2 class="mt-5">Existing Menu Items</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Meal Type</th>
                    <th>Weekday</th>
                    <th>Item Name</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($menu_items) > 0): ?>
                    <?php foreach ($menu_items as $menu_item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu_item['meal_type']); ?></td>
                            <td><?php echo htmlspecialchars($menu_item['weekday']); ?></td>
                            <td><?php echo htmlspecialchars($menu_item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($menu_item['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No menu items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="adminPanel.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Remove success message after 3 seconds
        setTimeout(function() {
            let alertSuccess = document.querySelector('.alert-success');
            if (alertSuccess) {
                alertSuccess.style.display = 'none';
            }
        }, 3000);
    </script>
</body>

</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'employee')) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/dashboard.css">
</head>

<body class="dash-body">
    <div><?php include 'navbar.php'; ?></div>
        <div class="container">
                <h2>Menus</h2>
                <div id="meals-eaten">
                    <p>Breakfast:
                        <button onclick="location.href='menu.php?meal=breakfast'">View Breakfast Menu</button>
                    </p>
                    <p>Lunch:
                        <button onclick="location.href='menu.php?meal=lunch'">View Lunch Menu</button>
                    </p>
                    <p>Dinner:
                        <button onclick="location.href='menu.php?meal=dinner'">View Dinner Menu</button>
                    </p>
                </div>
            </div>
        </div>
        </form>
</body>

</html>
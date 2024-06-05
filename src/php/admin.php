<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

echo "<h1>Admin Panel</h1>";
echo "<a class='btn' href='manage_menu.php'>Manage Menu</a><br>";
echo "<a class='btn' href='manage_orders.php'>Manage Orders</a><br>";
echo "<a class='btn' href='manage_employees.php'>Manage Employees</a><br>";
echo "<a class='back-btn' href='dashboard.php'>Back to Dashboard</a>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .body{
            margin: 0;
            padding: 0;
            display: flex;
        }
        .btn{
            border: 2px black solid;
            border-radius: 20px ;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 20px;
        }

        .back-btn{
            position: absolute;
            border: 2px black solid;
            border-radius: 20px ;
            text-decoration: none;
            padding: 10px;
            right: 50;
            bottom: 30;
            background-color: palevioletred ;
            color: white;
        }
        .back-btn:hover{
            background-color: white;
            color: black;
            opacity: 50%;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="view_user.php" class="btn btn-primary mt-3">View All Users</a>
        <!-- Add more admin options here -->
        <a href="logout.php" class="btn btn-secondary mt-3">Logout</a>
    </div>
</body>
</html>
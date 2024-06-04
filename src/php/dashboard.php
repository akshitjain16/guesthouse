<?php
session_start();
if (!isset($_SESSION['username'])) {
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
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>You are logged in as an <?php echo htmlspecialchars($_SESSION['role']); ?>.</p>
        <?php if ($_SESSION['role'] == 'admin') { ?>
            <a href="admin.php" class="btn btn-primary">Admin Panel</a>
        <?php } else { ?>
            <a href="employee.php" class="btn btn-primary">Employee Panel</a>
        <?php } ?>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
</body>
</html>
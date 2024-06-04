<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Menu</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Menu</h1>
        <div class="row">
            <?php
            session_start();
            include('config.php');

            if ($_SESSION['role'] != 'employee') {
                header("Location: dashboard.php");
                exit();
            }

            $sql = "SELECT * FROM menu WHERE availability = TRUE";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4'>";
                echo "<div class='card mb-4'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . $row['name'] . "</h5>";
                echo "<p class='card-text'>" . $row['description'] . "</p>";
                echo "<p class='card-text'>$" . $row['price'] . "</p>";
                echo "<form method='post' action='place_order.php'>";
                echo "<input type='hidden' name='menu_id' value='" . $row['id'] . "'>";
                echo "<div class='form-group'>";
                echo "<input type='number' name='quantity' class='form-control' min='1' value='1'>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary'>Order</button>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>

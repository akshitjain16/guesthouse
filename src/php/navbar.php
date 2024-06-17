<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../style/navbar.css">
</head>

<body class='n-body'>
    <nav class="navbar-body">
        <div class="navbar-items"><img class="logo" src="../../public/assets/logo.png" alt="Shree logo"></div>
        <ul class="navbar-navv">
            <li class="nav-items">
                <a class="nav-links" aria-current="true" href="./home.php">Home</a>
            </li>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <?php if ($_SESSION['role'] === 'admin') : ?>
                    <li class="nav-items">
                        <a class="nav-links" href="./register.php">Register</a>
                    </li>
                    <li class="nav-items">
                        <a class="nav-links" href="./manage_users.php">Manage Users</a>
                    </li>
                    <li class="nav-items">
                        <a class="nav-links" href="./manage_meals.php">Manage Menu</a>
                    </li>
                    <li class="nav-items">
                        <a class="nav-links" href="booking_check.php">Mess Manage</a>
                    </li>
                <?php elseif ($_SESSION['role'] === 'employee') : ?>
                    <li class="nav-items">
                        <a class="nav-links" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-items">
                        <a class="nav-links" href="">Menu</a>
                    </li>
                    <li class="nav-items">
                        <a class="nav-links" href="./book_meals.php">Book Meals</a>
                    </li>
                    <li class="nav-items">
                        <a class="nav-links" href="./view_meals.php">Meal History</a>
                    </li>
                <?php endif; ?>
                <li class="nav-items dropdown">
                    <a class="nav-links dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="../../public/assets/placeholder.jpg" alt="Profile Icon" style="width:30px; border-radius:15px">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="./profile.php">View Profile</a>
                        <a class="dropdown-item" href="./logout.php">Logout</a>
                    </div>
                </li>
            <?php else : ?>
                <li class="nav-items">
                    <a class="nav-links" href="./login.php">Login</a>
                </li>
                <li class="nav-items">
                    <a class="nav-links" href="./contact.php">Contact</a>
                </li>
            <?php endif; ?>
        </ul>
        </div>
    </nav>
</body>

</html>
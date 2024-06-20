<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand img {
            height: 20px;
        }

        .nav-item img {
            width: 30px;
            border-radius: 15px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        .nav-item {
            padding-left: 15px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#"><img class="logo" src="../../public/assets/logo.png" alt="Shree logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <?php if ($_SESSION['role'] === 'admin') : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="adminPanel.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./manage_users.php">Manage Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./manage_menu.php">Manage Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./register.php">Register</a>
                        </li>
                    <?php elseif ($_SESSION['role'] === 'employee') : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./view_meals.php">Meal History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./menu.php">Menu</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="../../public/assets/placeholder.jpg" alt="Profile Icon">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="./profile.php">View Profile</a>
                            <a class="dropdown-item" href="./logout.php">Logout</a>
                        </div>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="true" href="./home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./contact.php">Contact</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
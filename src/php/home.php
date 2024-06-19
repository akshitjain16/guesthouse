<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Shree Guest House</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        .background {
            background-image: url('../../public/assets/satkar.JPG');
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .welcome-text {
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            color: white;
            padding: 20px;
            border-radius: 10px;
            font-size: 3em;
            text-align: center;
        }
    </style>
</head>
<body>
<div><?php include 'navbar.php'; ?></div>
    <div class="background">
        <div class="welcome-text">
            Welcome to Shree Guest House
        </div>
    </div>
</body>
</html>

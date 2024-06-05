<?php
include '../../config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../../phpmailer/vendor/autoload.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = hash("sha256", "bin2hex(random_bytes(50))");
        $sql = "UPDATE users 
                SET reset_token = ?, 
                reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Send the reset link to the user's email
        $reset_link = "http://localhost/guesthouse/src/php/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = '192.168.100.100';
            $mail->SMTPAuth   = false;
            // $mail->Username   = 'noreply';
            // $mail->Password   = 'mugneeram';
            // $mail->SMTPSecure = 'tls' ;
            $mail->Port       = 25;

            //Recipients
            $mail->setFrom('noreply@shreecement.com', 'Mailer');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Testing Password Reset Request';
            $mail->Body    = 'Please click on the following link to reset your password: <a href="' . $reset_link . '">' . $reset_link . '</a>';
            $mail->AltBody = 'Please click on the following link to reset your password: ' . $reset_link;

            $mail->send();
            echo 'Password reset link has been sent to your email.';
        } catch (Exception $e) {
            echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Forgot Password</h1>
        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

</html>
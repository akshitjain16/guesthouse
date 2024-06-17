<?php
session_start();
include '../../config/config.php';
// require '../../vendor/autoload.php';  // Assuming you use Composer to install PHPMailer and Twilio

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// use Twilio\Rest\Client;

// Check if the user is logged in as an employee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_query = $conn->query("SELECT * FROM users WHERE emp_id = '$user_id'");
$user = $user_query->fetch_assoc();
$email = $user['email'];
$phone = $user['phone_number'];

// // Send email confirmation
// $mail = new PHPMailer(true);
// try {
//     // Server settings
//     $mail->SMTPDebug;
//             $mail->isSMTP();
//             $mail->Host       = '192.168.100.100';
//             $mail->SMTPAuth   = false;
//             // $mail->Username   = 'noreply';
//             // $mail->Password   = 'mugneeram';
//             // $mail->SMTPSecure = 'tls' ;
//             $mail->Port       = 25;

//     // Recipients
//     $mail->setFrom('noreply@shreecement.com', 'Guest House Canteen');
//     $mail->addAddress($email, $user['name']); // Add a recipient

//     // Content
//     $mail->isHTML(true); // Set email format to HTML
//     $mail->Subject = 'Meal Booking Confirmation';
//     $mail->Body    = "Dear " . $user['name'] . ",<br>Your meal bookings have been confirmed for the following dates and meal types:<br>";

//     foreach ($period as $date) {
//         $meal_date = $date->format('Y-m-d');
//         $mail->Body .= "$meal_date: " . implode(", ", $meal_types) . "<br>";
//     }

//     $mail->send();
// } catch (Exception $e) {
//     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
// }

// // Send SMS confirmation using Twilio
// $sid = 'your_twilio_sid';
// $token = 'your_twilio_token';
// $twilio = new Client($sid, $token);

// $message_body = "Your meal bookings have been confirmed for the following dates and meal types:\n";
// foreach ($period as $date) {
//     $meal_date = $date->format('Y-m-d');
//     $message_body .= "$meal_date: " . implode(", ", $meal_types) . "\n";
// }

// $message = $twilio->messages
//                   ->create($phone, // to
//                            ["from" => "+1234567890", // from a valid Twilio number
//                             "body" => $message_body]);

header("Location: booking_confirmation.php");
exit();
?>

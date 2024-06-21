<?php
include '../../config/config.php';

$error_message = '';
$success_message = '';

// Guesthouse email mapping
$guesthouse_emails = [
    'Swagat' => 'guesthouse1@example.com',
    'Satakr' => 'guesthouse2@example.com',
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $guesthouse = $_POST['guesthouse'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message) || empty($guesthouse)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email format.';
    } else {
        $to_email = $guesthouse_emails[$guesthouse];

        // Send email (Example)
        $headers = "From: $email";
        if (mail($to_email, $subject, $message, $headers)) {
            $success_message = 'Your message has been sent successfully!';
        } else {
            $error_message = 'Failed to send your message. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .contact-form {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        .contact-form h2 {
            margin-bottom: 20px;
        }
    </style>
</head>

<body style="background-color:#D7E1EB">
    <div><?php include 'navbar.php'; ?></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-body">
                        <h3 class="card-title text-center">Contact Us</h3>
                        <?php if ($error_message) : ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <?php if ($success_message) : ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Guesthouse</label><br>
                                <?php foreach ($guesthouse_emails as $guesthouse => $email) : ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="guesthouse" id="<?php echo $guesthouse; ?>" value="<?php echo $guesthouse; ?>" required>
                                        <label class="form-check-label" for="<?php echo $guesthouse; ?>"><?php echo $guesthouse; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

</body>
<?php include 'footer.php'; ?>

</html>
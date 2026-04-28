<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $customer_id = $_SESSION['id']; // Assuming you have the customer_id in session
    $file = "support_messages.json";
$messages = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

$new_message = [
    "customer_id" => $_SESSION['id'],
    "name" => $name,
    "email" => $email,
    "message" => $message,
    "reply" => "",
    "followup" => ""
];

$messages[] = $new_message;
file_put_contents($file, json_encode($messages, JSON_PRETTY_PRINT));

    $success = "Your message has been sent successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Support</title>
  <style>
    /* Your CSS styles */
  </style>
</head>
<body>

<div class="main">
  <h2>📞 Contact Support</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="email" name="email" placeholder="Your Email" required>
    <textarea name="message" rows="5" placeholder="Your Message..." required></textarea>
    <button type="submit">Send Message</button>
  </form>
</div>

<div style="text-align: center; margin-top: 50px; margin-bottom: 30px;">
  <a href="customerdashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
</div>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file = "support_messages.json";
    $messages = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    $new_message = [
        "customer_id" => $_SESSION['id'],
        "name" => htmlspecialchars($_POST['name']),
        "email" => htmlspecialchars($_POST['email']),
        "message" => htmlspecialchars($_POST['message']),
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
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
    }

    .main {
      max-width: 600px;
      margin: 40px auto;
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input, textarea {
      margin: 12px 0;
      padding: 12px;
      font-size: 16px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      padding: 12px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #2980b9;
    }

    .success {
      color: green;
      text-align: center;
      margin-bottom: 10px;
    }

    .back-btn {
      display: inline-block;
      background-color: #3498db;
      color: white;
      padding: 12px 20px;
      border-radius: 30px;
      text-decoration: none;
      font-size: 16px;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .back-btn:hover {
      background-color: #2980b9;
      transform: scale(1.05);
    }

    .back-btn:active {
      transform: scale(1);
    }
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

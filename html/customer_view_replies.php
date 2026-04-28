<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$reply_file = "customer_replies_$customer_id.txt";
$replies = file_exists($reply_file) ? file_get_contents($reply_file) : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $followup_message = htmlspecialchars($_POST['followup_message']);
    $followup_data = "\nCustomer Follow-up: $followup_message\n---\n";
    file_put_contents($reply_file, $followup_data, FILE_APPEND);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Support Messages</title>
    <style>
        /* Similar styling as admin view for consistency */
    </style>
</head>
<body>
    <div class="main-container">
        <h2>Your Support Messages</h2>

        <div class="message-card">
            <h4>Admin Replies</h4>
            <pre><?= nl2br(htmlspecialchars($replies)) ?></pre>

            <form method="POST" action="">
                <textarea name="followup_message" rows="4" cols="50" placeholder="Enter your follow-up..."></textarea>
                <button type="submit">Send Follow-Up</button>
            </form>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .main-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 24px;
        }
        .message-card {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }
        .message-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .message-card h4 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }
        .message-card p {
            font-size: 16px;
            color: #333;
            margin: 10px 0;
        }
        .message-card .reply-form, .message-card .mark-read-form {
            margin-top: 15px;
        }
        .reply-btn, .mark-read-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .reply-btn:hover, .mark-read-btn:hover {
            background-color: #2980b9;
        }
        .back-btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            text-align: center;
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="main-container">
    <h2>Support Messages</h2>

    <?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$messages = file_exists("support_messages.txt") ? file_get_contents("support_messages.txt") : "";
$followups = file_exists("customer_followups.txt") ? file_get_contents("customer_followups.txt") : "";
$replies = file_exists("customer_replies.txt") ? file_get_contents("customer_replies.txt") : "";

$followup_array = explode("\n---\n", $followups);
$reply_array = explode("\n---\n", $replies);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message_index = $_POST['message_index'];
    $admin_reply = $_POST['admin_reply'];
    
    // Update the replies file
    $reply_array[$message_index] = $admin_reply;
    file_put_contents("customer_replies.txt", implode("\n---\n", $reply_array));
}
?>

<div class="main-container">
    <h2>Support Messages</h2>

    <?php
    $message_array = explode("\n---\n", $messages);
    foreach ($message_array as $index => $msg) {
        if (!empty($msg)) {
            echo "<div class='message-card'>";
            echo "<pre>" . nl2br(htmlspecialchars($msg)) . "</pre>";

            // Show customer follow-up if exists
            if (isset($followup_array[$index]) && !empty(trim($followup_array[$index]))) {
                echo "<div class='reply'><strong>Customer Follow-up:</strong><pre>" . nl2br(htmlspecialchars($followup_array[$index])) . "</pre></div>";
            }

            // Show admin reply if exists
            if (isset($reply_array[$index]) && !empty(trim($reply_array[$index]))) {
                echo "<div class='reply'><strong>Admin Reply:</strong><pre>" . nl2br(htmlspecialchars($reply_array[$index])) . "</pre></div>";
            } else {
                // Show form to reply if no reply exists yet
                echo "<form action='' method='POST' class='reply-form'>
                        <textarea name='admin_reply' rows='4' cols='50' placeholder='Enter your reply...'></textarea>
                        <input type='hidden' name='message_index' value='$index'>
                        <button type='submit' class='reply-btn'>Reply</button>
                      </form>";
            }
            echo "</div>";
        }
    }
    ?>

    <a href="admindashboard.php" class="back-btn">Back to Dashboard</a>
</div>

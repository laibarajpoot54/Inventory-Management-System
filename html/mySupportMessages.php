<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}


$customer_id = $_SESSION['id'];
$file = "support_messages.json";
$messages = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

?>
<!-- 
// Load support messages and replies
// $messages = file_get_contents("support_messages_.txt");
// $replies = file_get_contents("customer_replies.txt");

// // If customer is submitting a reply
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_index']) && isset($_POST['customer_reply'])) {
//     $message_index = $_POST['message_index'];
//     $customer_reply = $_POST['customer_reply'];
    
//     // Append the reply to customer_replies.txt (make sure to append at the right position)
//     $reply_array = explode("\n---\n", $replies);
//     $reply_array[$message_index] .= "\n" . $customer_reply;
    
//     // Update the replies file
//     file_put_contents("customer_replies.txt", implode("\n---\n", $reply_array));
    
//     // Optionally, redirect to the same page to show updated message
//     header("Location: mySupportMessages.php");
//     exit();
// }
// ?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   
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
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #2c3e50;
        }
        .message {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .message h4 {
            margin: 0;
            font-size: 18px;
        }
        .message p {
            font-size: 16px;
            color: #333;
        }
        .reply {
            background-color: #f5f5f5;
            padding: 10px;
            margin-top: 10px;
        }
        .status {
            font-size: 14px;
            color: #2ecc71;
        }
        .reply-form {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .reply-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .reply-form button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        .reply-form button:hover {
            background-color: #2980b9;
        }
        .back-btn {
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
    <h2>My Support Messages</h2>
    <?php


// Ensure the user is logged in and is a customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Get the customer ID from the session
$id = $_SESSION['id']; // Assuming you have the customer_id in session
$file_name = "support_messages_{$id}.txt"; // Customer-specific message file

// Check if the file exists
if (file_exists($file_name)) {
    $messages = file_get_contents($file_name); // Fetch only the messages for the logged-in customer
} else {
    $messages = ""; // No messages found for the customer
}

// If customer is submitting a reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_index']) && isset($_POST['customer_reply'])) {
    $message_index = $_POST['message_index'];
    $customer_reply = $_POST['customer_reply'];

    // Load the current replies
    $replies = file_get_contents("customer_replies.txt");

    // Append the new reply for this message
    $reply_array = explode("\n---\n", $replies);
    $reply_array[$message_index] .= "\n" . $customer_reply;

    // Update the replies file
    file_put_contents("customer_replies.txt", implode("\n---\n", $reply_array));

    // Optionally, redirect to the same page to show the updated message
    header("Location: mySupportMessages.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <style>
        /* Your existing styles remain the same */
    </style>
</head>
<body>

<div class="main-container">
   

    <?php
    // Check if there are any messages for the customer
    if (!empty($messages)) {
        $message_array = explode("\n---\n", $messages);
        foreach ($message_array as $index => $msg) {
            if (!empty($msg)) {
                echo "<div class='message'>";
                echo "<pre>" . nl2br(htmlspecialchars($msg)) . "</pre>";

                // Display admin reply if available
                if (strpos($msg, "Reply:") !== false) {
                    // Admin reply available
                } else {
                    echo "<div class='status'>Waiting for Admin's Reply...</div>";
                }

                // Display customer's follow-up reply if available
                $followup_array = file_exists("customer_followups.txt") ? explode("\n---\n", file_get_contents("customer_followups.txt")) : [];
                if (isset($followup_array[$index]) && !empty(trim($followup_array[$index]))) {
                    echo "<div class='reply'><strong>My Follow-up:</strong><pre>" . nl2br(htmlspecialchars($followup_array[$index])) . "</pre></div>";
                }

                // Follow-up Form
                echo "<form method='post' action='mySupportMessages.php' style='margin-top:10px;'>
                        <input type='hidden' name='message_index' value='$index'>
                        <textarea name='customer_reply' rows='3' cols='50' placeholder='Write your reply here...' required></textarea><br><br>
                        <button type='submit' class='back-btn'>Send Reply</button>
                      </form>";

                echo "</div>";
            }
        }
    } else {
        echo "<p>No support messages found for you.</p>";
    }
    ?>

    <a href="customerDashboard.php" class="back-btn">Back to Dashboard</a>
</div>

</body>
</html>
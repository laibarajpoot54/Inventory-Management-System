<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Get submitted data
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['followup_message'])) {
    $index = intval($_POST['message_index']);
    $followup = trim($_POST['followup_message']);

    // Load existing followups for the current customer
    $customer_id = $_SESSION['customer_id'];
    $followups_file = "customer_followups_$customer_id.txt"; // Customer-specific followup file
    $followups = file_exists($followups_file) ? explode("\n---\n", file_get_contents($followups_file)) : [];

    // Update the correct index for the customer's follow-up message
    $followups[$index] = $followup;

    // Save back to the customer-specific file
    file_put_contents($followups_file, implode("\n---\n", $followups));
}

// Redirect back to support messages page
header("Location: mySupportMessages.php");
exit();
?>

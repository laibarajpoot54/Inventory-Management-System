<?php
include("../Config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ Sanitize input
    $sup_name = trim($_POST["sup_name"]);
    $contact_person = trim($_POST["contact_person"]);
    $sup_address = trim($_POST["sup_address"]);
    $sup_email = trim($_POST["sup_email"]);
    $sup_phoneNo = trim($_POST["sup_phoneNo"]);

    // ✅ Validation
    if (empty($sup_name) || empty($contact_person) || empty($sup_address) || empty($sup_email) || empty($sup_phoneNo)) {
        die("❌ All fields are required.");
    }

    if (!filter_var($sup_email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format.");
    }

    // ✅ Prepared Statement
    $stmt = $conn->prepare("INSERT INTO suppliers 
        (sup_name, contact_person, sup_address, sup_email, sup_phoneNo) 
        VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $sup_name, $contact_person, $sup_address, $sup_email, $sup_phoneNo);

    if ($stmt->execute()) {
        echo "✅ Supplier added successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
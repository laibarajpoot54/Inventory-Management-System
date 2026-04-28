<?php
// insertUser.php

include("../Config/db.php");

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = "customer";  // 🔥 Default role set karo

    // ✅ Basic Validation
    if (empty($username) || empty($email) || empty($password)) {
        die("❌ All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format.");
    }

    if (strlen($password) < 6) {
        die("❌ Password must be at least 6 characters.");
    }

    // ✅ Check if email already exists
    $check = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        die("❌ Email already exists.");
    }

    $check->close();

    // ✅ Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Prepared Statement
    $stmt = $conn->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

    // Execute
    if ($stmt->execute()) {
        echo "✅ User inserted successfully!";
        // Optionally redirect
        // header("Location: login.php");
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
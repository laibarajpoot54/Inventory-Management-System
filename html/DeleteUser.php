<?php
session_start();
include("../config/db.php");

// ======================
// ADMIN CHECK
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ======================
// CSRF TOKEN CHECK
// ======================
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed!");
}

// ======================
// VALIDATE ID
// ======================
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("Invalid request!");
}

$id = intval($_POST['id']);

// ======================
// START TRANSACTION (SAFE DELETE)
// ======================
$conn->begin_transaction();

try {

    // ======================
    // DELETE SALES FIRST (SAFE)
    // ======================
    $stmt1 = $conn->prepare("DELETE FROM sales WHERE user_id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->close();

    // ======================
    // DELETE USER
    // ======================
    $stmt2 = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    // COMMIT
    $conn->commit();

    header("Location: ManageUsers.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error deleting user!");
}
?>
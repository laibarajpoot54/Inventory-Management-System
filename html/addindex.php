<?php
session_start();

// ======================
// AUTH CHECK
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

include("../config/db.php");

// ======================
// CSRF CHECK
// ======================
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("❌ CSRF validation failed!");
}

// ======================
// ALLOWED TABLES
// ======================
$allowed_tables = ['products', 'sales', 'suppliers', 'user'];

// ======================
// INPUT VALIDATION
// ======================
$table = $_POST['table'] ?? '';
$column = $_POST['column'] ?? '';
$indexType = $_POST['index_type'] ?? '';

// ✅ Table check
if (!in_array($table, $allowed_tables)) {
    die("❌ Invalid table!");
}

// ======================
// COLUMN VALIDATION (IMPORTANT)
// ======================
$columns = [];
$colResult = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");

while ($col = mysqli_fetch_assoc($colResult)) {
    $columns[] = $col['Field'];
}

if (!in_array($column, $columns)) {
    die("❌ Invalid column!");
}

// ======================
// INDEX TYPE VALIDATION
// ======================
$allowed_types = ['INDEX', 'UNIQUE', 'FULLTEXT'];

if (!in_array($indexType, $allowed_types)) {
    die("❌ Invalid index type!");
}

// ======================
// SAFE INDEX NAME
// ======================
$indexName = strtolower($table . '_' . $column . '_idx');

// ======================
// CREATE INDEX (SAFE)
// ======================
$sql = "ALTER TABLE `$table` ADD $indexType `$indexName` (`$column`)";

if (mysqli_query($conn, $sql)) {

    // SAFE REDIRECT
    $safeTable = htmlspecialchars($table);

    echo "<script>
        alert('✅ Index added successfully!');
        window.location.href='manage_indexing.php?table=$safeTable';
    </script>";

} else {
    die("❌ Error adding index!");
}
?>
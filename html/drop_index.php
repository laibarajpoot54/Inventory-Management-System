<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// CSRF CHECK
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF failed");
}

$allowedTables = ['products', 'sales', 'suppliers', 'user'];

$table = $_POST['table'];
$index = $_POST['index_name'];

if (!in_array($table, $allowedTables)) {
    die("Invalid table");
}

if ($index !== 'PRIMARY') {

    $sql = "DROP INDEX `$index` ON `$table`";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_indexing.php?table=$table&msg=Index+Dropped");
    } else {
        echo mysqli_error($conn);
    }

} else {
    header("Location: manage_indexing.php?table=$table&msg=Cannot+drop+PRIMARY");
}
?>
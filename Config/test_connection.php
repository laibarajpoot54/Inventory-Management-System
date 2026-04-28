<?php
$conn = mysqli_connect("localhost", "root", "", "inventory");

if (!$conn) {
    die("Connection FAILED: " . mysqli_connect_error());
} else {
    echo "Connection SUCCESS! Database connected.";
    mysqli_close($conn);
}
?>
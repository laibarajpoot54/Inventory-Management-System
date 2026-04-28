<?php
session_start();
require("../config/db.php");

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* =========================
   SESSION SECURITY
========================= */
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

$timeout = 1800; // 30 min
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

/* =========================
   SAFE QUERIES
========================= */
$productResult = $conn->query("SELECT COUNT(*) AS total FROM products");
$productCount = $productResult->fetch_assoc()['total'];

$salesResult = $conn->query("SELECT COUNT(*) AS total FROM sales");
$salesCount = $salesResult->fetch_assoc()['total'];

$userResult = $conn->query("SELECT COUNT(*) AS total FROM user");
$userCount = $userResult->fetch_assoc()['total'];

/* =========================
   JSON REQUEST COUNT
========================= */
$pendingCount = 0;
if (file_exists("pending_requests.json")) {
    $requests = json_decode(file_get_contents("pending_requests.json"), true);
    if (is_array($requests)) {
        foreach ($requests as $req) {
            if (isset($req['status']) && $req['status'] === 'pending') {
                $pendingCount++;
            }
        }
    }
}

/* =========================
   ADMIN NAME
========================= */
$adminName = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Dashboard</title>

<?php include("CSS.php"); ?>
<script src="../assets/vendor/js/helpers.js"></script>
<script src="../assets/js/config.js"></script>

<style>
body {
    background: #f5f7ff;
    font-family: 'Segoe UI', sans-serif;
}

.content-wrapper {
    background: #f5f7ff;
}

.welcome-box {
    background: linear-gradient(135deg, #4361ee, #3a0ca3, #7209b7);
    background-size: 300% 300%;
    animation: gradientMove 8s ease infinite;
    padding: 35px 40px;
    border-radius: 24px;
    color: white;
    margin-bottom: 35px;
    box-shadow: 0 12px 30px rgba(67, 97, 238, 0.25);
    position: relative;
    overflow: hidden;
}

/* Decorative circles */
.welcome-box::before,
.welcome-box::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
}

.welcome-box::before {
    width: 180px;
    height: 180px;
    top: -60px;
    right: -60px;
}

.welcome-box::after {
    width: 120px;
    height: 120px;
    bottom: -40px;
    left: -40px;
}

/* Heading */
.welcome-box h1 {
    margin: 0;
    font-size: 34px;
    font-weight: 800;
    letter-spacing: 0.5px;
    color: #ffffff;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.25);
    position: relative;
    z-index: 2;
}

.welcome-box p {
    margin-top: 10px;
    font-size: 16px;
    color: rgba(255,255,255,0.95);
    text-shadow: 1px 1px 6px rgba(0,0,0,0.2);
    font-weight: 400;
    position: relative;
    z-index: 2;
}

/* Animated Gradient */
@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.dashboard-tile {
    border-radius: 20px;
    padding: 30px;
    color: white;
    text-align: center;
    transition: 0.3s ease;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.dashboard-tile:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.12);
}

.dashboard-tile::before {
    content: '';
    position: absolute;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    top: -20px;
    right: -20px;
}

.dashboard-icon {
    font-size: 45px;
    margin-bottom: 10px;
}

.dashboard-tile h5 {
    font-weight: 600;
}

.dashboard-tile span {
    font-size: 28px;
    font-weight: bold;
}

.products { background: linear-gradient(135deg,#00c853,#64dd17);}
.sales { background: linear-gradient(135deg,#2196f3,#42a5f5);}
.users { background: linear-gradient(135deg,#ff9800,#ffb74d);}
.pending { background: linear-gradient(135deg,#9c27b0,#e040fb);}
</style>
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
<div class="layout-container">

<?php include("AdminMenu.php"); ?>

<div class="layout-page">
<div class="content-wrapper">
<div class="container-xxl flex-grow-1 container-p-y">

<div class="welcome-box">
    <h1>Welcome, <?= $adminName ?> 👋</h1>
    <p>Here is a quick overview of your Inventory Management System.</p>
</div>

<div class="row g-4">

<div class="col-lg-3 col-md-6">
<div class="dashboard-tile products">
<i class="bx bx-box dashboard-icon"></i>
<h5>Total Products</h5>
<span><?= $productCount ?></span>
</div>
</div>

<div class="col-lg-3 col-md-6">
<div class="dashboard-tile sales">
<i class="bx bx-bar-chart-alt dashboard-icon"></i>
<h5>Total Sales</h5>
<span><?= $salesCount ?></span>
</div>
</div>

<div class="col-lg-3 col-md-6">
<div class="dashboard-tile users">
<i class="bx bx-group dashboard-icon"></i>
<h5>Total Users</h5>
<span><?= $userCount ?></span>
</div>
</div>

<div class="col-lg-3 col-md-6">
<div class="dashboard-tile pending">
<i class="bx bx-bell dashboard-icon"></i>
<h5>Pending Requests</h5>
<span><?= $pendingCount ?></span>
</div>
</div>

</div>
</div>

<?php include("Footer.php"); ?>
<div class="content-backdrop fade"></div>

</div>
</div>
</div>
</div>

<?php include("JS.php"); ?>
</body>
</html>
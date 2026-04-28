<?php
session_start();
include("../config/db.php");

/* AUTH CHECK */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}

/* SESSION SECURITY */
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

/* DATA */
$stmt = $conn->prepare("SELECT product_id, product_name, description, price, quantity_in_stock, product_img FROM products");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Available Products</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg,#74ebd5,#acb6e5);
    font-family: 'Segoe UI', sans-serif;
    min-height:100vh;
}

/* MAIN BOX */
.container-box{
    max-width:1000px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
}

/* TITLE */
.title{
    text-align:center;
    font-size:32px;
    font-weight:700;
    background: linear-gradient(to right,#3498db,#3a0ca3);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    margin-bottom:25px;
}

/* TABLE STYLE */
table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 12px;
}

thead th{
    background:#3498db;
    color:white;
    padding:12px;
    text-align:center;
    font-size:14px;
}

tbody tr{
    background:white;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:0.3s;
}

tbody tr:hover{
    transform:scale(1.01);
}

/* CELLS */
td{
    padding:12px;
    text-align:center;
    vertical-align:middle;
    font-size:14px;
}

.product-img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:10px;
    border:2px solid #ddd;
}

/* NAME */
.name{
    font-weight:600;
    color:#2c3e50;
}

/* PRICE */
.price{
    font-weight:bold;
    color:#3a0ca3;
}

/* BADGES */
.badge-stock{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:500;
}

.in-stock{ background:#d1fae5; color:#065f46; }
.low-stock{ background:#fef3c7; color:#92400e; }
.out-stock{ background:#fee2e2; color:#991b1b; }

/* BUTTON */
.btn-back{
    display:block;
    width:220px;
    margin:30px auto 0;
    padding:10px;
    text-align:center;
    border-radius:30px;
    text-decoration:none;
    color:white;
    background:linear-gradient(to right,#3498db,#3a0ca3);
    transition:0.3s;
}

.btn-back:hover{
    transform:translateY(-3px);
}
</style>
</head>

<body>

<div class="container-box">

<h2 class="title">📦 Available Products</h2>

<?php if ($result->num_rows === 0): ?>
    <p class="text-center text-muted">No products found.</p>
<?php else: ?>

<table>
<thead>
<tr>
    <th>Image</th>
    <th>Product Name</th>
    <th>Description</th>
    <th>Price</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()):

    $qty = (int)$row['quantity_in_stock'];

    if ($qty == 0) {
        $status = "out-stock";
        $text = "Out of Stock";
    } elseif ($qty <= 10) {
        $status = "low-stock";
        $text = "Low Stock";
    } else {
        $status = "in-stock";
        $text = "In Stock";
    }
?>

<tr>

<td>
<img class="product-img"
     src="<?= htmlspecialchars($row['product_img']) ?>"
     onerror="this.src='images/default.jpg'">
</td>

<td class="name">
    <?= htmlspecialchars($row['product_name']) ?>
</td>

<td>
    <?= htmlspecialchars($row['description']) ?>
</td>

<td class="price">
    Rs <?= number_format($row['price'],2) ?>
</td>

<td>
    <span class="badge-stock <?= $status ?>">
        <?= $text ?> (<?= $qty ?>)
    </span>
</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

<?php endif; ?>

<a href="UserDashboard.php" class="btn-back">⬅ Back to Dashboard</a>

</div>

</body>
</html>
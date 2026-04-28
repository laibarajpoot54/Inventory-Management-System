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
// CSRF TOKEN GENERATION
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ======================
// FETCH PRODUCTS (SAFE)
// ======================
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include("CSS.php"); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ⚠️ SECURITY HEADERS (XSS Protection) -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">

    <style>
        :root {
            --primary: #6c5ce7;
            --primary-light: #a29bfe;
            --secondary: #00b894;
            --accent: #fd79a8;
            --dark: #2d3436;
            --light: #f0f4ff;
            --table-bg: #f7f9fc;
            --border-color: #dcdde1;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #d63031;
        }

        body {
            background-color: #f4f6fc;
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
        }

        .product-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 40px;
            color: var(--primary);
            font-size: 2rem;
            font-weight: 700;
            position: relative;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .product-table {
            background-color: var(--table-bg);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--primary-light);
        }

        .product-table thead {
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            color: white;
        }

        .product-table th,
        .product-table td {
            padding: 12px 15px;
            font-size: 0.9rem;
            text-align: center;
            vertical-align: middle;
        }

        .product-table th {
            font-weight: 600;
        }

        .product-row {
            background-color: #ecf0f7;
            transition: all 0.3s ease;
        }

        .product-row:hover {
            background-color: #e6ebf5;
            transform: scale(1.01);
        }

        .product-name {
            color: var(--primary);
            font-weight: 600;
        }

        .product-description {
            color: #555;
            font-size: 0.9rem;
        }

        .product-price {
            font-weight: 600;
            color: var(--dark);
            font-size: 1rem;
        }

        .stock-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .in-stock { background-color: rgba(0, 184, 148, 0.15); color: var(--success); }
        .low-stock { background-color: rgba(253, 203, 110, 0.2); color: #e17055; }
        .out-of-stock { background-color: rgba(214, 48, 49, 0.1); color: var(--danger); }

        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 25px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.95rem;
            margin-top: 30px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.4);
        }

        .indexing-bar {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="product-container">

<!-- 🔐 CSRF Protected Form -->
<form method="GET" action="manage_indexing.php" class="indexing-bar">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="input-group mb-4" style="max-width: 600px; margin: 0 auto;">

        <span class="input-group-text" style="border-radius: 25px 0 0 25px; background-color: #6c63ff; color: white; font-weight: bold;">
            Select Table
        </span>

        <select class="form-select" name="table">
            <option value="products">Products</option>
            <option value="sales">Sales</option>
            <option value="suppliers">Suppliers</option>
            <option value="user">User</option>
        </select>

        <button type="submit" class="btn btn-primary" style="border-radius: 0 25px 25px 0;">
            🔍 Manage Indexing
        </button>

    </div>
</form>

<h2 class="page-title">Available Products</h2>

<table class="table product-table">
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
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<?php
    $qty = (int)$row['quantity_in_stock'];

    if ($qty == 0) {
        $statusClass = "out-of-stock";
        $statusIcon = "❌";
        $statusText = "Out of Stock";
    } elseif ($qty <= 10) {
        $statusClass = "low-stock";
        $statusIcon = "⚠️";
        $statusText = "Low Stock";
    } else {
        $statusClass = "in-stock";
        $statusIcon = "✅";
        $statusText = "In Stock";
    }
?>
<tr class="product-row">

<td>
<img src="<?= htmlspecialchars($row['product_img']) ?>"
     onerror="this.onerror=null;this.src='images/default.jpg';"
     style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
</td>

<td><?= htmlspecialchars($row['product_name']) ?></td>
<td><?= htmlspecialchars($row['description']) ?></td>
<td>$<?= htmlspecialchars(number_format((float)$row['price'],2)) ?></td>

<td>
<span class="stock-indicator <?= $statusClass ?>">
<?= $statusIcon ?> <?= htmlspecialchars($statusText) ?> (<?= $qty ?>)
</span>
</td>

</tr>
<?php endwhile; ?>
</tbody>
</table>

<a href="AdminDashBoard.php" class="back-btn">
⬅️ Back to Dashboard
</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
session_start();
include("../config/db.php");

// ======================
// AUTH CHECK (ADMIN ONLY)
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ======================
// SESSION SECURITY
// ======================
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// timeout (30 min)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

// ======================
// CSRF TOKEN
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ======================
// SECURE QUERY (Prepared)
// ======================
$stmt = $conn->prepare("
    SELECT s.sales_id, s.product_id, p.product_img, s.user_id,
           s.quantity_sold, s.total_price, s.date_of_sale,
           p.product_name, u.username
    FROM sales s
    INNER JOIN products p ON s.product_id = p.product_id
    INNER JOIN user u ON s.user_id = u.id
    ORDER BY s.date_of_sale DESC
");

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sales History</title>

    <?php include("CSS.php"); ?>

    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

    <!-- ================= YOUR CSS (UNCHANGED) ================= -->
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .container-box {
            max-width: 900px;
            width: 100%;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .sales-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table th {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 12px;
            font-size: 14px;
        }

        .table td {
            text-align: center;
            padding: 12px;
            font-size: 14px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: #e2edff;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .no-data {
            font-style: italic;
            color: #6c757d;
            text-align: center;
            padding: 20px 0;
        }

        .btn-back {
            display: block;
            width: 200px;
            margin: 30px auto 0;
            padding: 10px 0;
            text-align: center;
            background-color: #0d6efd;
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-back:hover {
            background-color: #0b5ed7;
        }

        @media (max-width: 768px) {
            .sales-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>

<div class="container-box">

    <h1 class="sales-title">📊 Sales History</h1>

    <div class="card table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Sales ID</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Price</th>
                    <th>Sale Date</th>
                    <th>Sold By</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$row['sales_id'] ?></td>

                        <td>
                            <img src="<?= htmlspecialchars($row['product_img'] ?? 'images/default.jpg') ?>"
                                 onerror="this.onerror=null;this.src='images/default.jpg';"
                                 class="product-img">
                        </td>

                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= (int)$row['quantity_sold'] ?></td>
                        <td>Rs. <?= htmlspecialchars($row['total_price']) ?></td>
                        <td><?= htmlspecialchars($row['date_of_sale']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="no-data">No sales records found.</td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>
    </div>

    <a href="AdminDashboard.php" class="btn-back">🏠 Back to Dashboard</a>

</div>

<?php include("JS.php"); ?>
</body>
</html>
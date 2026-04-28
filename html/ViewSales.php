<?php
session_start();

// Only logged-in users with 'user' role can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

$userId = $_SESSION['user_id'];

// Fetch sales made by this user
$sql = "SELECT s.sales_id,p.product_img, p.product_name, s.quantity_sold, s.total_price, s.date_of_sale 
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        WHERE s.user_id = ?
        ORDER BY s.date_of_sale DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Sales History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --secondary: #4cc9f0;
            --accent: #f72585;
            --success: #38b000;
            --warning: #ffaa00;
            --danger: #ef233c;
            --light: #f8f9fa;
            --dark: #212529;
        }

        body {
            background-color: #f5f7ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sales-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .page-header h2 {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 2.5rem;
            display: inline-block;
            position: relative;
            padding-bottom: 15px;
        }

        .page-header h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .sales-table {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.1);
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-header th {
            padding: 18px;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            border: none;
        }

        .sales-row {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f1f1;
        }

        .sales-row:last-child {
            border-bottom: none;
        }

        .sales-row:hover {
            background-color: #f8f9ff;
            transform: translateX(5px);
        }

        .sales-row td {
            padding: 15px;
            vertical-align: middle;
            text-align: center;
        }

        .sale-id {
            font-weight: 600;
            color: var(--primary);
        }

        .product-name {
            font-weight: 500;
            color: var(--dark);
        }

        .quantity {
            font-weight: 600;
            color: var(--success);
        }

        .price {
            font-weight: 700;
            color: var(--primary-dark);
        }

        .date {
            color: #666;
            font-weight: 500;
        }

        .no-sales {
            text-align: center;
            padding: 60px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .no-sales i {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-sales h4 {
            color: var(--dark);
            font-weight: 500;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 40px;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
            color: white;
        }

        .back-btn i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .sales-container {
                margin: 30px auto;
            }

            .page-header h2 {
                font-size: 2rem;
            }

            .table-header th {
                padding: 12px;
                font-size: 0.8rem;
            }

            .sales-row td {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="sales-container">
        <div class="page-header">
            <h2><i class="fas fa-history me-2"></i>Your Sales History</h2>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table sales-table">
                    <thead class="table-header">
                        <tr>
                            <th style="background: linear-gradient(135deg, #4cc9f0, #4361ee); color: white; font-size: 1rem; font-weight: 700; letter-spacing: 1px; box-shadow: inset 0 -3px 0 rgba(255,255,255,0.1);">
                                <i class="fas fa-receipt me-1"></i> Sale ID
                            </th>
                            <th style="background: linear-gradient(135deg, #4cc9f0, #4361ee); color: white;">
                            <i class="fas fa-image me-1"></i> Product Image
                            </th>

                            <th style="background: linear-gradient(135deg, #4361ee, #3a0ca3); color: white; font-size: 1rem; font-weight: 700; letter-spacing: 1px;">
                                <i class="fas fa-box me-1"></i> Product Name
                            </th>
                            <th style="background: linear-gradient(135deg, #3a0ca3, #f72585); color: white; font-size: 1rem; font-weight: 700; letter-spacing: 1px;">
                                <i class="fas fa-sort-numeric-up me-1"></i> Quantity Sold
                            </th>
                            <th style="background: linear-gradient(135deg, #f72585, #ff006e); color: white; font-size: 1rem; font-weight: 700; letter-spacing: 1px;">
                                <i class="fas fa-rupee-sign me-1"></i> Total Price
                            </th>
                            <th style="background: linear-gradient(135deg, #ff006e, #7209b7); color: white; font-size: 1rem; font-weight: 700; letter-spacing: 1px;">
                                <i class="fas fa-calendar-alt me-1"></i> Date of Sale
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="sales-row">
                                <td class="sale-id"><?= htmlspecialchars($row['sales_id']) ?></td>
                                <td>
    <?php 
        $image = !empty($row['product_img']) ? $row['product_img'] : 'images/default.jpg';
    ?>

    <img 
        src="<?= htmlspecialchars($image) ?>" 
        onerror="this.onerror=null;this.src='images/default.jpg';"
        alt="Product Image" 
        style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;"
    >
</td>
                                </td>
                                <td class="product-name"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="quantity"><?= htmlspecialchars($row['quantity_sold']) ?></td>
                                <td class="price">Rs. <?= htmlspecialchars(number_format($row['total_price'], 2)) ?></td>
                                <td class="date"><?= htmlspecialchars($row['date_of_sale']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-sales">
                <i class="fas fa-box-open"></i>
                <h4>No sales records found</h4>
            </div>
        <?php endif; ?>

        <div class="text-center">
        <a href="UserDashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>

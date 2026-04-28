<?php
session_start();
include("../config/db.php");

// ======================
// AUTH CHECK
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ======================
// CSRF TOKEN GENERATION
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ======================
// DELETE LOGIC (SECURE)
// ======================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {

    // CSRF CHECK
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    $product_id = intval($_POST['delete_id']);

    // Delete related sales first
    $sales_stmt = $conn->prepare("DELETE FROM sales WHERE product_id = ?");
    $sales_stmt->bind_param("i", $product_id);
    $sales_stmt->execute();
    $sales_stmt->close();

    // Delete product
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $success = "✅ Product deleted successfully!";
    } else {
        $error = "❌ Error deleting product.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Product</title>
    <?php include("CSS.php"); ?>
</head>
<body>

<div class="container mt-5">
    <h2>Delete Product</h2>

    <!-- Messages -->
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Table -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM products");
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_id']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['quantity_in_stock']) ?></td>
                    <td>
                        <!-- ✅ SECURE DELETE FORM -->
                        <form method="POST" style="display:inline;">
                            
                            <!-- CSRF TOKEN -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                            <!-- SAFE ID -->
                            <input type="hidden" name="delete_id" value="<?= (int)$row['product_id'] ?>">

                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="AdminDashboard.php" class="btn btn-primary mb-3">
        ⬅️ Back to Dashboard
    </a>
</div>

<?php include("JS.php"); ?>
</body>
</html>
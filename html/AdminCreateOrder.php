<?php
session_start();
include("../config/db.php");

// ======================
// AUTH CHECK
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ======================
// CSRF TOKEN
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$successMsg = "";
$errorMsg = "";

// ======================
// HANDLE FORM
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ CSRF validation failed");
    }

    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = (int)($_POST['quantity'] ?? 0);
    $note       = trim($_POST['note'] ?? '');

    if ($product_id <= 0 || $quantity <= 0) {
        $errorMsg = "Please select a valid product and quantity.";
    } else {

        $stmt = $conn->prepare("
            UPDATE products 
            SET quantity_in_stock = quantity_in_stock + ? 
            WHERE product_id = ?
        ");

        $stmt->bind_param("ii", $quantity, $product_id);

        if ($stmt->execute()) {
            $successMsg = "Order created successfully ✔ Inventory updated!";
        } else {
            $errorMsg = "Failed to create order.";
        }

        $stmt->close();
    }
}

// ======================
// FETCH PRODUCTS
// ======================
$products = [];
$result = $conn->query("SELECT product_id, product_name FROM products");

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Order</title>

<?php include("CSS.php"); ?>

<style>
body{
    margin:0;
    font-family:Arial;
    background: linear-gradient(135deg,#74ebd5,#9face6);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.card{
    width:420px;
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
    animation:fadeIn 0.6s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

h2{
    text-align:center;
    margin-bottom:20px;
    color:#3a0ca3;
}

label{
    font-weight:bold;
    font-size:14px;
}

input,select,textarea{
    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:8px;
}

.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:25px;
    background:linear-gradient(135deg,#4361ee,#3a0ca3);
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.03);
    box-shadow:0 5px 15px rgba(67,97,238,0.4);
}

.back{
    display:block;
    text-align:center;
    margin-top:15px;
    text-decoration:none;
    color:#3a0ca3;
    font-weight:bold;
}

.alert{
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
    font-size:14px;
}

.success{background:#d1fae5;color:#065f46;}
.error{background:#fee2e2;color:#991b1b;}
</style>
</head>

<body>

<div class="card">

    <h2>🛒 Create Order</h2>

    <!-- Messages -->
    <?php if ($successMsg): ?>
        <div class="alert success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="alert error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- CSRF -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Select Product</label>
        <select name="product_id" required>
            <option value="">-- Choose Product --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= (int)$p['product_id'] ?>">
                    <?= htmlspecialchars($p['product_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Quantity</label>
        <input type="number" name="quantity" min="1" required>

        <label>Note (Optional)</label>
        <textarea name="note" rows="3"></textarea>

        <button type="submit" class="btn">➕ Create Order</button>
    </form>

    <a class="back" href="AdminDashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>
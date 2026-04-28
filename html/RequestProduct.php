<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    die("User ID is not available. Please log in again.");
}

include("../config/db.php");

/* =========================
   CSRF TOKEN
========================= */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* =========================
   GET PRODUCTS
========================= */
$productQuery = "SELECT product_id, product_name FROM products";
$productResult = mysqli_query($conn, $productQuery);

$successMsg = "";

/* =========================
   FORM SUBMIT SECURITY
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }

    // INPUT VALIDATION
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity   = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $note       = trim($_POST['note'] ?? '');

    if (!$product_id || $product_id <= 0) {
        die("Invalid product selected.");
    }

    if (!$quantity || $quantity <= 0) {
        die("Invalid quantity.");
    }

    // SAFE DB QUERY
    $stmt = $conn->prepare("SELECT product_name, sup_id FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        die("Product not found.");
    }

    $product_name = $product['product_name'];
    $sup_id = $product['sup_id'];

    // SAFE DATA STRUCTURE
    $requestData = [
        'user_id'      => (int)$_SESSION['user_id'],
        'product_id'   => $product_id,
        'product_name' => $product_name,
        'quantity'     => $quantity,
        'sup_id'       => $sup_id,
        'note'         => htmlspecialchars($note, ENT_QUOTES, 'UTF-8'),
        'requested_by' => 'user',
        'status'       => 'pending',
        'date'         => date("Y-m-d H:i:s")
    ];

    // SAFE JSON WRITE
    $filePath = 'pending_requests.json';

    $existingRequests = [];
    if (file_exists($filePath)) {
        $existingRequests = json_decode(file_get_contents($filePath), true);
        if (!is_array($existingRequests)) {
            $existingRequests = [];
        }
    }

    $existingRequests[] = $requestData;

    file_put_contents(
        $filePath,
        json_encode($existingRequests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );

    $successMsg = "Request for <b>" . htmlspecialchars($product_name) .
                  "</b> (Qty: <b>$quantity</b>) submitted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Request a Product</title>
<?php include("CSS.php"); ?>

<!-- 🔥 SAME UI (NO CHANGE) -->
<style>
  body {
    background: linear-gradient(135deg, #74ebd5, #acb6e5);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
  }

  .sell-form-container {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    width: 500px;
    animation: fadeIn 1s ease-in-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .sell-form-container h2 {
    margin-bottom: 30px;
    text-align: center;
    font-weight: bold;
    color: #3a0ca3;
  }

  .btn-combined {
    width: 100%;
    padding: 12px 30px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    border: none;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-top: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
  }

  .btn-combined:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
  }

  .btn-container {
    display: flex;
    gap: 10px;
    flex-direction: column;
  }
</style>
</head>

<body>

<div class="sell-form-container">
    <h2>Request a Product</h2>

    <?php if ($successMsg): ?>
        <div class="alert alert-success text-center">
            <?= $successMsg ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <!-- CSRF -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="mb-3">
            <label class="form-label">Select Product:</label>
            <select name="product_id" class="form-select" required>
                <option value="">-- Choose Product --</option>
                <?php while ($row = mysqli_fetch_assoc($productResult)): ?>
                    <option value="<?= (int)$row['product_id'] ?>">
                        <?= htmlspecialchars($row['product_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity Needed:</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Optional Note:</label>
            <textarea name="note" class="form-control"></textarea>
        </div>

        <div class="btn-container">
            <button type="submit" class="btn-combined">
                <i class="fas fa-check-circle"></i> Send Request
            </button>

            <a href="UserDashboard.php" class="btn-combined">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

    </form>
</div>

<?php include("JS.php"); ?>
</body>
</html>
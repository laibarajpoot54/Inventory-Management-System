<?php
session_start();
include("../config/db.php");

// Only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Path to the JSON file
$jsonFile = "pending_requests.json";

// Read existing requests
$requests = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $index = $_POST['index'];

    if (isset($requests[$index])) {
        $p = $requests[$index];

        if ($action === 'approve') {
            
            $product_name = $p['product_name'];
            $description = $p['description'];
            $price = $p['price'];
            $quantity = $p['quantity'];
            $product_img = $p['product_img'];
            $sup_id = $p['sup_id'];

            // Optional: Check if supplier exists
            $checkSupplier = $conn->prepare("SELECT sup_id FROM suppliers WHERE sup_id = ?");
            $checkSupplier->bind_param("i", $sup_id);
            $checkSupplier->execute();
            $result = $checkSupplier->get_result();

            if ($result->num_rows > 0) {
                // Supplier exists, check if product already exists
                $checkProduct = $conn->prepare("SELECT product_id, quantity_in_stock FROM products WHERE product_name = ?");
                $checkProduct->bind_param("s", $product_name);
                $checkProduct->execute();
                $existingProduct = $checkProduct->get_result();

                if ($existingProduct->num_rows > 0) {
                    // Product exists, update quantity
                    $row = $existingProduct->fetch_assoc();
                    $new_quantity = $row['quantity_in_stock'] + $quantity;

                    // Update product quantity
                    $updateQty = $conn->prepare("UPDATE products SET quantity_in_stock = ? WHERE product_id = ?");
                    $updateQty->bind_param("ii", $new_quantity, $row['product_id']);
                    $updateQty->execute();
                    $updateQty->close();
                } else {
                    // Product doesn't exist, insert new product
                    if (!empty($description) && !empty($price) && !empty($product_img)) {
                      // Admin-style full insert (all fields filled)
                      $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, quantity_in_stock, product_img, sup_id) VALUES (?, ?, ?, ?, ?, ?)");
                      $stmt->bind_param("ssdisi", $product_name, $description, $price, $quantity, $product_img, $sup_id);
                  } else {
                      // User-style insert (only required fields)
                      $stmt = $conn->prepare("INSERT INTO products (product_name, quantity_in_stock, sup_id) VALUES (?, ?, ?)");
                      $stmt->bind_param("sii", $product_name, $quantity, $sup_id);
                  }
                 
                    $stmt->execute();
                    $stmt->close();
                }

                // Mark the request as approved
                $requests[$index]['status'] = 'approved';
            } else {
                // Reject request if supplier does not exist
                $requests[$index]['status'] = 'rejected';
                $requests[$index]['note'] .= ' (Supplier ID invalid or not found)';
            }
        } elseif ($action === 'reject') {
            // Reject request
            $requests[$index]['status'] = 'rejected';
        }

        // Save the updated requests to JSON
        file_put_contents($jsonFile, json_encode($requests, JSON_PRETTY_PRINT));
    }

    // Redirect back to the requests page after processing
    header("Location: approveRequests.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approve Requests - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4">Product Order Requests </h2>

  <?php if (empty($requests)): ?>
    <div class="alert alert-info">No product requests found.</div>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 g-4">
      <?php foreach ($requests as $index => $req): ?>
        <div class="col">
          <div class="card shadow">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($req['product_name'] ?? 'Unnamed Product') ?></h5>
              <ul class="list-unstyled mb-3">
                <li><strong>Quantity:</strong> <?= $req['quantity'] ?? '0' ?></li>
                <li><strong>Supplier ID:</strong> <?= $req['sup_id'] ?? 'N/A' ?></li>
                <li><strong>Status:</strong> 
                  <span class="badge bg-<?= $req['status'] === 'approved' ? 'success' : ($req['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                    <?= ucfirst($req['status'] ?? 'pending') ?>
                  </span>
                </li>
                <li><strong>Note:</strong> <?= htmlspecialchars($req['note'] ?? 'No note provided') ?></li>
              </ul>

              <?php if (!isset($req['status']) || $req['status'] === 'pending'): ?>
                <form method="post" action="ApprovePendingRequests.php" class="d-flex gap-2">
                  <input type="hidden" name="index" value="<?= $index ?>">
                  <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                  <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                </form>
              <?php else: ?>
                <div class="text-muted fst-italic">This request has been <?= $req['status'] ?>.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="mt-4">
    <a href="AdminDashBoard.php" class="btn btn-secondary">Back to Dashboard</a>
  </div>
</div>
</body>
</html>

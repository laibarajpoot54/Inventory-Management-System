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
$requests = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
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
  <h2 class="mb-4">Pending Product Requests</h2>

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

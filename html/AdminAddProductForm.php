<?php
session_start();

// ======================
// ADMIN AUTH CHECK
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product</title>
    <?php include("CSS.php"); ?>

    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
            overflow-x: hidden;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 500px;
            animation: fadeIn 1s ease-in-out;
            z-index: 1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-container h2 {
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
            color: #3a0ca3;
        }

        .form-label {
            font-weight: 500;
            color: #555;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }

        .btn-add, .btn-back {
            display: block;
            width: 80%;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 30px;
            border: none;
            margin: 15px auto 0 auto;
            transition: 0.3s;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            text-decoration: none;
            text-align: center;
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 117, 140, 0.4);
        }

        textarea.form-control {
            resize: none;
            min-height: 100px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="form-title">🛒 Add New Product</h2>
    <form action="AdminInsertProduct.php" method="POST" enctype="multipart/form-data">
        <!-- CSRF TOKEN -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" name="product_name" id="product_name" class="form-control" placeholder="e.g. Wireless Mouse" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" placeholder="Brief product details..." required></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (Rs)</label>
            <input type="number" name="price" id="price" class="form-control" step="0.01" placeholder="e.g. 999.99" required>
        </div>

        <div class="mb-3">
            <label for="quantity_in_stock" class="form-label">Quantity in Stock</label>
            <input type="number" name="quantity_in_stock" id="quantity_in_stock" class="form-control" placeholder="e.g. 100" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" name="category" id="category" class="form-control" placeholder="e.g. Electronics" required>
        </div>

        <div class="mb-3">
            <label for="sup_id" class="form-label">Supplier ID</label>
            <input type="number" name="sup_id" id="sup_id" class="form-control" placeholder="e.g. 5" required>
        </div>

        <div class="mb-4">
            <label for="product_img" class="form-label">Product Image</label>
            <input type="file" name="product_img" id="product_img" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn-add">➕ Add Product</button>
        <br>
      
        <a href="AdminDashboard.php" class="btn-back">🏠 Back to Dashboard</a>

       

    </form>
</div>

<?php include("JS.php"); ?>
</body>
</html>

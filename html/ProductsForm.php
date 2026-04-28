<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Product</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .form-container {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      width: 100%;
      max-width: 400px;
      box-sizing: border-box;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    label {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 5px;
      color: #555;
    }

    input[type="text"], input[type="number"], input[type="decimal"], textarea {
      width: 100%;
      padding: 8px;
      margin: 6px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 14px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }

    .form-footer {
      text-align: center;
      margin-top: 15px;
    }

    .form-footer a {
      color: #007BFF;
      text-decoration: none;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Add New Product</h2>
    <form action="InsertProduct.php" method="POST" enctype="multipart/form-data">

      <label for="product_name">Product Name:</label>
      <input type="text" id="product_name" name="product_name" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="3" required></textarea>

      <label for="price">Price:</label>
      <input type="number" step="0.01" id="price" name="price" required>

      <label for="quantity_in_stock">Quantity in Stock:</label>
      <input type="number" id="quantity_in_stock" name="quantity_in_stock" required>

      <label for="category">Category:</label>
      <input type="text" id="category" name="category" required>

      <label for="sup_id">Supplier ID:</label>
      <input type="number" id="sup_id" name="sup_id" required>
      
      <!-- New Image Upload Field  -->
      <label for="product_img">Product Image:</label>
      <input type="file" id="product_img" name="product_img" accept="image/*" required>

      <input type="submit" value="Add Product">
    </form>

    <div class="form-footer">
      <p>Go back to <a href="user_dashboard.php">Dashboard</a></p>
    </div>
  </div>

</body>
</html>

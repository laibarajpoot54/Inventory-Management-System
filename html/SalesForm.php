<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Record New Sale</title>
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
      display: block;
    }

    input[type="number"], input[type="datetime-local"] {
      width: 100%;
      padding: 8px;
      margin: 6px 0 15px 0;
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
    <h2>Record New Sale</h2>
    <form action="insertSales.php" method="post">
      <label for="product_id">Product ID:</label>
      <input type="number" id="product_id" name="product_id" required>

      <label for="user_id">User ID:</label>
      <input type="number" id="user_id" name="user_id" required>

      <label for="quantity_sold">Quantity Sold:</label>
      <input type="number" id="quantity_sold" name="quantity_sold" required>

      <label for="total_price">Total Price:</label>
      <input type="number" step="0.01" id="total_price" name="total_price" required>

      <label for="date_of_sale">Date of Sale:</label>
      <input type="datetime-local" id="date_of_sale" name="date_of_sale" required>

      <input type="submit" value="Record Sale">
    </form>

    <div class="form-footer">
      <p>Go back to <a href="user_dashboard.php">Dashboard</a></p>
    </div>
  </div>

</body>
</html>

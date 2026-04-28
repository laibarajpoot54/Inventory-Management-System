<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Supplier</title>
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
      max-width: 450px;
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

    input[type="text"], input[type="email"], textarea {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 14px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #218838;
    }

    .form-footer {
      text-align: center;
      margin-top: 15px;
    }

    .form-footer a {
      color: #28a745;
      text-decoration: none;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Add New Supplier</h2>
    <form action="insertSuppliers.php" method="post">
      <label for="sup_name">Supplier Name:</label>
      <input type="text" id="sup_name" name="sup_name" required>

      <label for="contact_person">Contact Person:</label>
      <input type="text" id="contact_person" name="contact_person" required>

      <label for="sup_address">Address:</label>
      <textarea id="sup_address" name="sup_address" rows="3" required></textarea>

      <label for="sup_email">Email:</label>
      <input type="email" id="sup_email" name="sup_email" required>

      <label for="sup_phoneNo">Phone Number:</label>
      <input type="text" id="sup_phoneNo" name="sup_phoneNo" required>

      <input type="submit" value="Add Supplier">
    </form>

    <div class="form-footer">
      <p>Go back to <a href="userDashboard.php">Dashboard</a></p>
    </div>
  </div>

</body>
</html>

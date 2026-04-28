<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include("../Config/db.php"); // Database connection

// Fetch all users
$sql = "SELECT * FROM user";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Users</title>
  <?php include("CSS.php"); ?>
  <style>
body {
    background: linear-gradient(135deg, #eef2ff, #dbeafe);
    font-family: 'Segoe UI', sans-serif;
}

.container {
    max-width: 1150px;
    margin: 50px auto;
    padding: 30px;
    background: rgba(255,255,255,0.95);
    border-radius: 22px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    backdrop-filter: blur(10px);
}

h2 {
    text-align: center;
    font-size: 30px;
    font-weight: 800;
    color: #3a0ca3;
    margin-bottom: 25px;
    position: relative;
}

h2::after {
    content: "";
    width: 120px;
    height: 4px;
    background: linear-gradient(135deg,#4361ee,#3a0ca3);
    display: block;
    margin: 10px auto 0;
    border-radius: 10px;
}

/* TABLE */
.table {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
    border-radius: 15px;
}

.table thead {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
}

.table th {
    padding: 16px;
    font-size: 15px;
    letter-spacing: 0.5px;
}

.table td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

.table tr:hover {
    background: #f8f9ff;
    transform: scale(1.01);
    transition: 0.2s;
}

/* BUTTONS */
.btn {
    padding: 7px 14px;
    border-radius: 30px;
    font-size: 13px;
    text-decoration: none;
    color: white;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}

.btn-warning {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(245,158,11,0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(220,38,38,0.3);
}

/* BACK BUTTON */
.back-btn {
    display: block;
    width: 240px;
    margin: 35px auto 0;
    text-align: center;
    background: linear-gradient(135deg,#4361ee,#3a0ca3);
    color: white;
    padding: 12px;
    border-radius: 30px;
    font-size: 15px;
    text-decoration: none;
    transition: 0.3s;
}

.back-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(67,97,238,0.3);
}
</style>
   
</head>

<body>

  <div class="container">
    <h2>Manage Users</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td>
              <a href="EditUser.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
              <!-- DELETE FORM (SECURE) -->
  <form method="POST" action="DeleteUser.php" style="display:inline;" 
        onsubmit="return confirm('Are you sure you want to delete this user?');">

    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

    <!-- CSRF TOKEN -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <button type="submit" class="btn btn-danger">
      Delete
    </button>

  </form>
</td>
            
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <a href="admindashboard.php" class="back-btn">Back to Dashboard</a>
  </div>

</body>
</html>

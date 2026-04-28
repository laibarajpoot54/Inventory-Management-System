<?php
session_start();
include("../config/db.php");

// AUTH CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$successMsg = "";
$errorMsg = "";

// FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed!");
  }

  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $role = 'user';

  if (empty($username) || empty($email) || empty($password)) {
    $errorMsg = "All fields are required!";
  }
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMsg = "Invalid email format!";
  }
  elseif (strlen($password) < 6) {
    $errorMsg = "Password must be at least 6 characters!";
  }
  else {

    $check = "SELECT id FROM user WHERE email = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $errorMsg = "Email already exists!";
    } else {

      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $sql = "INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

      if ($stmt->execute()) {
        $successMsg = "User added successfully!";
      } else {
        $errorMsg = "Something went wrong!";
      }

      $stmt->close();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add User</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg,#eef2ff,#dbeafe);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:Segoe UI;
}

.card-box{
    width:100%;
    max-width:520px;
    background:white;
    border-radius:22px;
    padding:35px;
    box-shadow:0 15px 40px rgba(0,0,0,0.1);
    animation:fadeIn .5s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

.title{
    text-align:center;
    font-size:26px;
    font-weight:800;
    color:#3a0ca3;
    margin-bottom:5px;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-bottom:25px;
}

.form-control{
    border-radius:14px;
    padding:12px;
}

.form-control:focus{
    box-shadow:0 0 0 .2rem rgba(67,97,238,.2);
    border-color:#4361ee;
}

.btn-primary{
    width:100%;
    border-radius:14px;
    padding:12px;
    background:linear-gradient(135deg,#4361ee,#3a0ca3);
    border:none;
    font-weight:600;
}

.btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(67,97,238,.3);
}

.alert{
    border-radius:12px;
}
</style>
</head>

<body>

<div class="card-box">

    <div class="title">
        <i class="fa fa-user-plus"></i> Add New User
    </div>
    <div class="subtitle">Create a secure account for system access</div>

    <?php if (!empty($successMsg)) : ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-plus-circle me-1"></i> Add User
        </button>

    </form>

    <a href="AdminDashboard.php" class="btn btn-link mt-3 w-100">← Back to Dashboard</a>

</div>

</body>
</html>
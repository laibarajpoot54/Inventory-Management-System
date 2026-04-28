<style>

body {
    background-color: #f8f9fa;
}

.container {
    max-width: 600px;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    background-color: #007bff; 
    color: #1E90FF;
}

.form-label {
    font-weight: bold;
}


</style>
<?php
session_start();
include("../config/db.php");

// ======================
// AUTH CHECK
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

// ======================
// GET USER ID SAFELY
// ======================
if (!isset($_GET['id'])) {
    die("No user selected.");
}

$id = intval($_GET['id']);

// ======================
// FETCH USER (SAFE SQL)
// ======================
$stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// ======================
// UPDATE USER
// ======================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // CSRF CHECK
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    // INPUT CLEANING
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // OPTIONAL PASSWORD
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $password = $user['password'];
    }

    // UPDATE QUERY (SAFE)
    $stmt = $conn->prepare("
        UPDATE user 
        SET username=?, email=?, password=?, role=? 
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssi",
        $username,
        $email,
        $password,
        $role,
        $id
    );

    $stmt->execute();

    header("Location: ManageUsers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        max-width: 600px;
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .card-header {
        background-color: #007bff;
        color: white;
    }

    .form-label {
        font-weight: bold;
    }
  </style>
</head>

<body>

<div class="container mt-5">

    <div class="card shadow-lg">

        <div class="card-header text-center">
            <h2>Edit User</h2>
        </div>

        <div class="card-body">

            <form method="POST">

                <!-- CSRF TOKEN -->
                <input type="hidden" name="csrf_token"
                       value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- USERNAME -->
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                           value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <!-- EMAIL -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <!-- PASSWORD -->
                <div class="mb-3">
                    <label class="form-label">Password (Leave blank to keep unchanged)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <!-- ROLE -->
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
    <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
    <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
    <option value="customer" <?= $user['role']=='customer'?'selected':'' ?>>Customer</option>
</select>
                </div>

                <!-- BUTTONS -->
                <div class="d-flex justify-content-between">
                    <a href="ManageUsers.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
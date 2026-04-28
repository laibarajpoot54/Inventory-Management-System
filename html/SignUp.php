<?php
session_start();
include("../config/db.php");

$error = "";
$success = "";

// ======================
// CSRF TOKEN GENERATION
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ======================
    // CSRF CHECK
    // ======================
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    // ======================
    // INPUT CLEANING
    // ======================
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // ======================
    // VALIDATION
    // ======================
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    }
    elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Password must contain at least one uppercase letter!";
    }
    elseif (!preg_match('/[a-z]/', $password)) {
        $error = "Password must contain at least one lowercase letter!";
    }
    elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one number!";
    }
    elseif (!preg_match('/[\W_]/', $password)) {
        $error = "Password must contain at least one special character!";
    }
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    }
    else {

        // ======================
        // DUPLICATE EMAIL CHECK
        // ======================
        $check = "SELECT id FROM user WHERE email = ?";
        $stmt = $conn->prepare($check);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists!";
        } else {

            // ======================
            // PASSWORD HASHING
            // ======================
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // ======================
            // INSERT USER (SECURE)
            // ======================
            $query = "INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt3 = $conn->prepare($query);

            if (!$stmt3) {
                die("Database error!");
            }

            $stmt3->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt3->execute()) {
                $success = "Account created successfully! Please login.";
            } else {
                $error = "Something went wrong!";
            }

            $stmt3->close();
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg,#dbeafe,#eef2ff);
    font-family:'Segoe UI',sans-serif;
}

.signup-card{
    width:100%;
    max-width:480px;
    background:rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
    border-radius:25px;
    padding:35px;
    box-shadow:0 15px 35px rgba(0,0,0,0.08);
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

.signup-title{
    text-align:center;
    font-weight:800;
    font-size:28px;
    color:#3a0ca3;
    margin-bottom:5px;
}

.signup-subtitle{
    text-align:center;
    color:#666;
    margin-bottom:25px;
}

.form-control, .form-select{
    border-radius:30px;
    padding:12px 18px;
    border:1px solid #ddd;
}

.form-control:focus, .form-select:focus{
    border-color:#4361ee;
    box-shadow:0 0 0 0.2rem rgba(67,97,238,.15);
}

.input-group-text{
    border-radius:30px 0 0 30px;
    background:#f8f9ff;
    border:1px solid #ddd;
}

.btn-signup{
    border:none;
    border-radius:30px;
    padding:12px;
    font-weight:600;
    background:linear-gradient(135deg,#4361ee,#3a0ca3);
    color:white;
    transition:0.3s;
}

.btn-signup:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(67,97,238,.25);
}

.login-link{
    text-align:center;
    margin-top:18px;
}

.login-link a{
    color:#4361ee;
    text-decoration:none;
    font-weight:600;
}
</style>
</head>

<script>
function togglePassword(fieldId, iconSpan) {
    const field = document.getElementById(fieldId);
    const icon = iconSpan.querySelector("i");

    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>
<body>

<div class="signup-card">

    <div class="signup-title">Create Account ✨</div>
    <div class="signup-subtitle">Join Inventory Management System</div>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)) : ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-user"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
        </div>

       <div class="mb-3 input-group">
    <span class="input-group-text"><i class="fa fa-lock"></i></span>
    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
    <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
        <i class="fa fa-eye"></i>
    </span>
</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fa fa-lock"></i></span>
    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
    <span class="input-group-text" onclick="togglePassword('confirm_password', this)" style="cursor:pointer;">
        <i class="fa fa-eye"></i>
    </span>
</div>
        <div class="mb-3">
            <select name="role" class="form-select" required>
                <option value="" disabled selected>Select Role</option>
                <option value="user">User</option>
                <option value="customer">Customer</option>
            </select>
        </div>

        <button type="submit" class="btn btn-signup w-100">
            <i class="fa fa-user-plus me-2"></i> Sign Up
        </button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>
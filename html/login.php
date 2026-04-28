<?php
session_start();
include("../config/db.php");

$error = "";

// ======================
// CSRF TOKEN GENERATION
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['login'])) {

    // ======================
    // CSRF CHECK
    // ======================
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ======================
    // VALIDATION
    // ======================
    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {

        // ======================
        // SQL SAFE QUERY
        // ======================
        $sql = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Database error!");
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                session_regenerate_id(true);

                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['user_id'] = $row['id'];

                if ($row['role'] === 'admin') {
                    header("Location: AdminDashBoard.php");
                } elseif ($row['role'] === 'user') {
                    header("Location: UserDashBoard.php");
                } elseif ($row['role'] === 'customer') {
                    header("Location: CustomerDashBoard.php");
                } else {
                    $error = "Invalid role!";
                }
                exit();

            } else {
                $error = "Invalid username or password!";
            }

        } else {
            $error = "No user found!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="height: 100vh; margin:0; display:flex; justify-content:center; align-items:center;
background: linear-gradient(135deg, #4f46e5, #60a5fa); font-family: 'Segoe UI', sans-serif;">

<div style="
    width: 380px;
    background: white;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    animation: fadeIn 0.6s ease;
">

    <div style="text-align:center; margin-bottom:25px;">
        <div style="
            width:60px;
            height:60px;
            background: linear-gradient(135deg,#4f46e5,#60a5fa);
            border-radius:50%;
            margin:auto;
            display:flex;
            justify-content:center;
            align-items:center;
            color:white;
            font-size:22px;
            font-weight:bold;
        ">
            🔐
        </div>

        <h3 style="margin-top:15px; color:#1f2937;">Welcome To IMS</h3>
        <p style="color:#6b7280; font-size:14px;">Login to continue</p>
    </div>

    <?php if (!empty($error)) : ?>
        <div style="background:#fee2e2; color:#b91c1c; padding:10px; border-radius:10px; margin-bottom:15px; text-align:center;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div style="margin-bottom:15px;">
            <input type="text" name="username" placeholder="Username"
                style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:10px; outline:none;">
        </div>

        <div style="position:relative; margin-bottom:20px;">

    <input type="password" name="password" id="password"
        placeholder="Password"
        style="width:100%; padding:12px 40px 12px 12px; border:1px solid #e5e7eb; border-radius:10px; outline:none;">

    <span onclick="togglePassword()" 
        style="
            position:absolute;
            right:12px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#6b7280;
            font-size:18px;
        ">
        👁
    </span>

</div>

        <button type="submit" name="login"
            style="
                width:100%;
                padding:12px;
                border:none;
                border-radius:10px;
                background: linear-gradient(135deg,#4f46e5,#2563eb);
                color:white;
                font-weight:600;
                cursor:pointer;
                transition:0.3s;
            "
            onmouseover="this.style.transform='scale(1.02)'"
            onmouseout="this.style.transform='scale(1)'"
        >
            Login
        </button>

    </form>

</div>

<style>
@keyframes fadeIn {
    from {opacity:0; transform: translateY(20px);}
    to {opacity:1; transform: translateY(0);}
}
</style>

</body>
</html>

<script>
function togglePassword() {
    var pass = document.getElementById("password");

    if (pass.type === "password") {
        pass.type = "text";
    } else {
        pass.type = "password";
    }
}
</script>
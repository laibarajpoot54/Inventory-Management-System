<?php
session_start();

// ======================
// AUTH CHECK
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

include("../config/db.php");

// ======================
// CSRF TOKEN
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ======================
// ALLOWED TABLES (IMPORTANT)
// ======================
$allowed_tables = ['products', 'sales', 'suppliers', 'user'];

$table = $_GET['table'] ?? '';

if (!in_array($table, $allowed_tables)) {
    die("❌ Invalid table selected!");
}

// ======================
// FETCH INDEXES
// ======================
$indexQuery = "SHOW INDEXES FROM `$table`";
$indexResult = mysqli_query($conn, $indexQuery);

// ======================
// FETCH FOREIGN KEYS (SECURE)
// ======================
$foreignKeys = [];

$fkQuery = "
    SELECT DISTINCT CONSTRAINT_NAME, COLUMN_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = ?
      AND REFERENCED_TABLE_NAME IS NOT NULL
";

$stmt = $conn->prepare($fkQuery);
$stmt->bind_param("s", $table);
$stmt->execute();
$fkResult = $stmt->get_result();

while ($fkRow = $fkResult->fetch_assoc()) {
    $foreignKeys[$fkRow['COLUMN_NAME']] = $fkRow['CONSTRAINT_NAME'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Indexing</title>

  <!-- 🔐 SECURITY HEADERS -->
  <meta http-equiv="X-Content-Type-Options" content="nosniff">
  <meta http-equiv="X-Frame-Options" content="DENY">
  <meta http-equiv="X-XSS-Protection" content="1; mode=block">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f8;
      padding: 40px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
      background: #fff;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    th, td {
      padding: 10px 12px;
      border: 1px solid #eee;
      text-align: center;
      font-size: 15px;
    }

    th {
      background-color: #2c3e50;
      color: white;
      text-transform: uppercase;
      font-size: 14px;
    }

    h2 {
      color: #2c3e50;
    }

    form {
      margin-top: 30px;
      background: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    label, select, button {
      margin-right: 10px;
      font-size: 15px;
    }

    button {
      background: #27ae60;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    .drop-btn {
      background: #e74c3c;
      padding: 5px 10px;
      font-size: 12px;
      border-radius: 4px;
    }

    .drop-btn:hover {
      background: #c0392b;
    }

    .locked {
      font-size: 18px;
      color: #999;
    }

    a {
      text-decoration: none;
      display: inline-block;
      margin-top: 20px;
      background: #3498db;
      color: white;
      padding: 10px 16px;
      border-radius: 8px;
    }
  </style>
</head>

<body>

<h2>📌 Indexes for Table: <?= htmlspecialchars($table) ?></h2>

<?php if (isset($_GET['msg'])): ?>
  <div style="background: #d4edda; color: #155724; padding: 10px 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
<?php endif; ?>

<?php if ($indexResult && mysqli_num_rows($indexResult) > 0): ?>
  <table>
    <tr>
      <th>Key Name</th>
      <th>Column Name</th>
      <th>Index Type</th>
      <th>Non-Unique</th>
      <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($indexResult)): ?>
      <tr>
        <td><?= htmlspecialchars($row['Key_name']) ?></td>
        <td><?= htmlspecialchars($row['Column_name']) ?></td>
        <td><?= htmlspecialchars($row['Index_type']) ?></td>
        <td><?= $row['Non_unique'] ? 'Yes' : 'No' ?></td>

        <td>
          <?php
          $isPrimary = $row['Key_name'] === 'PRIMARY';
          $isForeign = isset($foreignKeys[$row['Column_name']]);

          if (!$isPrimary && !$isForeign): ?>
            
            <form method="POST" action="drop_index.php"
                  onsubmit="return confirm('Are you sure you want to drop this index?');"
                  style="display:inline;">

              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
              <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
              <input type="hidden" name="index_name" value="<?= htmlspecialchars($row['Key_name']) ?>">

              <button type="submit" class="drop-btn">Drop</button>
            </form>

          <?php else: ?>
            <span class="locked">🔒</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

<?php else: ?>
  <p>No indexes found for this table.</p>
<?php endif; ?>

<!-- Add Index -->
<form method="POST" action="addindex.php">
  <h3 style="margin-bottom: 15px;">➕ Add New Index</h3>

  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">

  <label>Column:</label>
  <select name="column" required>
    <?php
    $cols = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
    while ($col = mysqli_fetch_assoc($cols)) {
      echo '<option value="' . htmlspecialchars($col['Field']) . '">' . htmlspecialchars($col['Field']) . '</option>';
    }
    ?>
  </select>

  <label>Index Type:</label>
  <select name="index_type">
    <option value="INDEX">INDEX</option>
    <option value="UNIQUE">UNIQUE</option>
    <option value="FULLTEXT">FULLTEXT</option>
  </select>

  <button type="submit">Create Index</button>
</form>

<a href="AdminViewProducts.php">⬅️ Back to Dashboard</a>

</body>
</html>
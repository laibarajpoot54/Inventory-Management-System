
====user dashboard===
<!-- This is the USER Dashboard -->
<?php
  session_start();
  if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
  }
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title>User Dashboard</title>
    <meta name="description" content="" />
    <?php include("CSS.php"); ?>
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

    <!-- Custom Styling for Cards -->
    <style>
      .dashboard-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 20px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        border: none;
      }

      .dashboard-card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
      }

      .dashboard-card i {
        transition: color 0.3s ease;
      }

      .dashboard-card:hover i {
        color: #0d6efd;
      }
    </style>
  </head>

  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">

        <!-- Sidebar -->
        <?php include ("UserMenu.php"); ?>

        <!-- Page Content -->
        <div class="layout-page">

          <!-- Navbar -->
          <?php include("SearchBar.php"); ?>

          <!-- Content Wrapper -->
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">

              <!-- Welcome Message -->
              <div class="row">
                <div class="col-12 mb-4">
                  <h1 class="fw-bold">Welcome to User Dashboard</h1>
                </div>
              </div>

              <!-- Functional Cards Section -->
              <div class="row">
                <!-- Sell Product -->
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card dashboard-card text-center">
                    <div class="card-body">
                      <i class="bx bx-cart-alt" style="font-size: 40px;"></i>
                      <h5 class="card-title mt-2">Sell Product</h5>
                      <p class="card-text">Sell available inventory.</p>
                      <a href="sellProductForm.php" class="btn btn-primary">Sell Now</a>
                    </div>
                  </div>
                </div>

                <!-- Request Product -->
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card dashboard-card text-center">
                    <div class="card-body">
                      <i class="bx bx-message-square-add" style="font-size: 40px;"></i>
                      <h5 class="card-title mt-2">Request Product</h5>
                      <p class="card-text">Request more stock if inventory is low.</p>
                      <a href="requestProduct.php" class="btn btn-success">Request</a>
                    </div>
                  </div>
                </div>

                <!-- View Sales -->
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card dashboard-card text-center">
                    <div class="card-body">
                      <i class="bx bx-list-ul" style="font-size: 40px;"></i>
                      <h5 class="card-title mt-2">Sales History</h5>
                      <p class="card-text">View your previous sales records.</p>
                      <a href="viewSales.php" class="btn btn-warning">View</a>
                    </div>
                  </div>
                </div>

                <!-- Available Products -->
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card dashboard-card text-center">
                    <div class="card-body">
                      <i class="bx bx-box" style="font-size: 40px;"></i>
                      <h5 class="card-title mt-2">Available Products</h5>
                      <p class="card-text">Check products in inventory.</p>
                      <a href="availableProducts.php" class="btn btn-info">Check</a>
                    </div>
                  </div>
                </div>

                <!-- Logout -->
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card dashboard-card text-center">
                    <div class="card-body">
                      <i class="bx bx-power-off" style="font-size: 40px;"></i>
                      <h5 class="card-title mt-2">Logout</h5>
                      <p class="card-text">End your session securely.</p>
                      <a href="logout.php" class="btn btn-danger">Logout</a>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Footer -->
            <?php include("Footer.php"); ?>
            <div class="content-backdrop fade"></div>
          </div>
        </div>
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Buy Now Button -->
    <div class="buy-now">
      <a
        href="https://themeselection.com/products/sneat-bootstrap-html-admin-template/"
        target="_blank"
        class="btn btn-danger btn-buy-now"
        >Upgrade to Pro</a
      >
    </div>

    <!-- Scripts -->
    <?php include("JS.php"); ?>
  </body>
</html>

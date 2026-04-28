<!-- AdminMenu.php -->
<style>
  #layout-menu {
    padding-top: 20px;
    background: #ffffff;
    box-shadow: 4px 0 12px rgba(0,0,0,0.1);
    color: #333;
    width: 260px;
    min-height: 100vh;
    position: fixed;
  }

  .app-brand {
    padding: 25px 20px;
    text-align: center;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
  }

  .app-brand-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-size: 1.4rem;
    font-weight: bold;
    color: #343a40;
    text-decoration: none;
  }

  .app-brand-logo i {
    font-size: 28px;
    color: #007bff; /* Primary Blue */
  }

  .menu-inner {
    padding: 20px 0;
  }

  .menu-item {
    margin-bottom: 10px;
  }

  .menu-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 22px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 500;
    color: #333;
    background-color: transparent;
    transition: background 0.3s, color 0.3s;
    cursor: pointer;
  }

  .menu-link:hover,
  .menu-link:focus {
    background-color: #f0f0f0;
    color: #007bff; /* Primary blue on hover */
    text-decoration: none;
  }

  .menu-sub {
    padding-left: 20px;
    margin-top: 6px;
  }

  .menu-sub .menu-link {
    font-size: 14px;
    padding: 10px 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    margin-bottom: 6px;
    color: #555;
  }

  .menu-sub .menu-link:hover {
    background-color: #e2e6ea;
    color: #007bff;
  }

  .menu-icon {
    font-size: 20px;
    color: #6c757d; /* Soft grey */
  }
</style>

<aside id="layout-menu" class="layout-menu menu-vertical">
  <div class="app-brand">
    <a href="AdminDashboard.php" class="app-brand-link">
      <span class="app-brand-logo"><i class='bx bxs-cube'></i></span>
      <span class="app-brand-text menu-text">Inventory System</span>
    </a>
  </div>

  <ul class="menu-inner">
    <!-- Dashboard -->
    <li class="menu-item">
      <a href="AdminDashboard.php" class="menu-link">
        <i class="menu-icon bx bx-home-circle"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <!-- User Management -->
    <li class="menu-item">
      <a class="menu-link menu-toggle">
        <i class="menu-icon bx bx-user"></i>
        <div>User Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="AddUser.php" class="menu-link">Add User</a></li>
        <li class="menu-item"><a href="ManageUsers.php" class="menu-link">Manage Users</a></li>
      </ul>
    </li>

    <!-- Products -->
    <li class="menu-item">
      <a class="menu-link menu-toggle">
        <i class="menu-icon bx bx-box"></i>
        <div>Products</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="AdminAddProductForm.php" class="menu-link">Add Product</a></li>
        <li class="menu-item"><a href="AdminManageProducts.php" class="menu-link">Manage Products</a></li>
        <li class="menu-item"><a href="AdminViewProducts.php" class="menu-link">View Products</a></li> 
      </ul>
    </li>

    <!-- Orders -->
    <li class="menu-item">
      <a class="menu-link menu-toggle">
        <i class="menu-icon bx bx-cart"></i>
        <div>Orders</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="AdminCreateOrder.php" class="menu-link">Create Order</a></li>
        <li class="menu-item"><a href="ApprovePendingRequests.php" class="menu-link">View Orders</a></li>
      </ul>
    </li>

      <!-- Support Messages
    <li class="menu-item">
      <a href="viewSupportMsgs.php" class="menu-link">
        <i class="menu-icon bx bx-support"></i>
        <div>Support Messages</div>
      </a>
    </li> -->


    <!-- Sales -->
    <li class="menu-item">
      <a class="menu-link menu-toggle">
        <i class="menu-icon bx bx-bar-chart-alt-2"></i>
        <div>Sales</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="AllSales.php" class="menu-link">View Sales</a></li>
      </ul>
    </li>


    <!-- Logout -->
    <li class="menu-item">
      <a href="logout.php" class="menu-link">
        <i class="menu-icon bx bx-power-off"></i>
        <div>Logout</div>
      </a>
    </li>
  </ul>
</aside>


<style>
  .layout-menu {
    background-color: #ffffff;
    color: #333;
    height: 100vh;
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
  }

  .app-brand {
    background-color: #3498db;
    padding: 20px;
    text-align: center;
    font-weight: bold;
    color: white;
    font-size: 22px;
    letter-spacing: 1px;
  }

  ul.menu-inner {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .menu-item {
    position: relative;
  }

  .menu-link {
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease, transform 0.2s ease;
    font-weight: 500;
    cursor: pointer;
  }

  .menu-link:hover,
  .menu-item.active > .menu-link {
    background-color: #ecf0f1;
    color: #3498db;
    transform: scale(1.05);
  }

  .menu-icon {
    margin-right: 12px;
    font-size: 18px;
  }

  .menu-toggle::after {
    content: '\25BC';
    margin-left: auto;
    font-size: 12px;
    transition: transform 0.3s;
  }

  .menu-item.open .menu-toggle::after {
    transform: rotate(180deg);
  }

  .submenu {
    display: none;
    background-color: #f5f5f5;
    transition: all 0.3s ease;
  }

  .menu-item.open > .submenu {
    display: block;
  }

  .submenu .menu-link {
    padding-left: 40px;
    font-size: 14px;
    color: #555;
  }

  .submenu .menu-link:hover {
    color: #3498db;
  }
</style>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll(".menu-toggle");

    toggles.forEach(toggle => {
      toggle.addEventListener("click", function (e) {
        e.preventDefault();
        this.parentElement.classList.toggle("open");
      });
    });
  });
</script>



<aside class="layout-menu">
  <div class="app-brand">
    Inventory System
  </div>

  <ul class="menu-inner">
    <!-- Dashboard Menu -->
    <li class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'customerDashboard.php' ? 'active' : '' ?>">
      <a href="customerDashboard.php" class="menu-link">
        <i class="menu-icon bx bx-home"></i> <span>Dashboard</span>
      </a>
    </li>

    <!-- Products Menu with Submenu -->
    <li class="menu-item">
      <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-cart"></i> <span>Products</span>
      </a>
      <ul class="submenu">
        <li><a href="shopProducts.php" class="menu-link">Shop Products</a></li>
      </ul>
    </li>

    <!-- Orders Menu with Submenu -->
    <li class="menu-item">
      <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-box"></i> <span>Orders</span>
      </a>
      <ul class="submenu">
        <li><a href="myOrders.php" class="menu-link">My Orders</a></li>
      </ul>
    </li>

    <!-- Support Menu with Submenu
    <li class="menu-item">
      <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-headphone"></i> <span>Support</span>
      </a>
      <ul class="submenu">
        <li><a href="contactUs.php" class="menu-link">Contact Us</a></li>
        <li><a href="mySupportMessages.php" class="menu-link">SupportMsgs</a></li>


      </ul>
    </li> -->

    <!-- Logout Menu -->
    <li class="menu-item">
      <a href="logout.php" class="menu-link">
        <i class="menu-icon bx bx-power-off text-danger"></i> <span class="text-danger">Logout</span>
      </a>
    </li>
  </ul>
</aside>

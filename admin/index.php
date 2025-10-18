<?php
session_start();
include __DIR__ . "/partials/connectdb.php";

// ✅ ดึงข้อมูลสรุปจากฐานข้อมูล
$total_products   = $conn->query("SELECT COUNT(*) FROM product")->fetchColumn();
$total_categories = $conn->query("SELECT COUNT(*) FROM category")->fetchColumn();
$total_customers  = $conn->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$total_orders     = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_income     = $conn->query("SELECT SUM(total_price) FROM orders")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - MyCommiss Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap');
body {
  background: #0d1117;
  font-family: "Prompt", sans-serif;
  color: #fff;
  overflow-x: hidden;
}

/* ✅ Sidebar */
#sidebar {
  background: #0d1117;
  position: fixed;
  top: 0; left: 0;
  width: 250px;
  height: 100vh;
  box-shadow: 0 0 25px rgba(0,0,0,0.6);
  transition: left 0.3s ease;
  z-index: 1000;
}
#sidebar.collapsed { left: -250px; }

#sidebar .brand {
  font-weight: 700;
  font-size: 1.25rem;
  text-align: center;
  padding: 22px 0;
  border-bottom: 1px solid #1f1f1f;
  color: #fff;
}
#sidebar .brand i { color: #00d25b; }

#sidebar ul { list-style: none; padding: 0; margin: 25px 0; }
#sidebar ul li a {
  display: flex; align-items: center;
  padding: 12px 20px;
  color: #b0b9c4; text-decoration: none;
  border-radius: 10px; transition: 0.3s;
  font-weight: 500; margin: 6px 10px;
}
#sidebar ul li a:hover {
  background: linear-gradient(145deg, rgba(0,210,91,0.25), rgba(0,210,91,0.15));
  color: #00d25b;
  transform: translateX(5px);
}
#sidebar ul li a.active {
  background: linear-gradient(145deg, #00d25b, #00b14a);
  color: #fff;
  box-shadow: 0 0 15px rgba(0,210,91,0.5);
}
.logout-btn {
  display: block; width: 85%; margin: 25px auto;
  padding: 12px 0; background: linear-gradient(145deg, #e74c3c, #c0392b);
  border-radius: 10px; color: #fff; font-weight: 600;
  text-align: center; transition: 0.3s;
}
.logout-btn:hover {
  background: linear-gradient(145deg, #ff5240, #e74c3c);
  box-shadow: 0 0 18px rgba(231,76,60,0.6);
  transform: translateY(-2px);
}

/* ✅ Navbar (มือถือ) */
.navbar {
  background: #161b22;
  display: none;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
.navbar .toggle-btn {
  border: none;
  background: none;
  color: #fff;
  font-size: 1.5rem;
}

/* ✅ Main Panel */
.main-panel {
  margin-left: 250px;
  padding: 30px;
  transition: all 0.3s ease;
  background: #0d1117;
  min-height: 100vh;
}
.section-title {
  color: #fff;
  font-weight: 700;
  border-left: 5px solid #00d25b;
  padding-left: 10px;
  margin-bottom: 1.5rem;
}

/* ✅ Card Style */
.card-custom {
  background: linear-gradient(145deg, #161b22, #0e1116);
  border: 1px solid #2c313a;
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  transition: transform .3s, box-shadow .3s;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.card-custom:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.5);
}
.card-custom h4 { color: #c9d1d9; font-weight: 600; }
.card-custom h2 { color: #00d25b; font-size: 2rem; font-weight: bold; }

/* ✅ รายได้รวม */
.not-clickable {
  cursor: default !important;
  pointer-events: none !important;
  background: linear-gradient(145deg, #161b22, #101318);
  border: 1px solid #2c313a;
  box-shadow: inset 0 0 12px rgba(255,50,50,0.2);
}

/* ✅ Responsive */
@media (max-width: 991px) {
  #sidebar { left: -250px; }
  #sidebar.show { left: 0; }
  .navbar { display: flex; position: sticky; top: 0; z-index: 999; }
  .main-panel { margin-left: 0; padding-top: 80px; }
}
  </style>
</head>

<body>
  <!-- ✅ Navbar มือถือ -->
  <nav class="navbar d-lg-none">
    <button class="toggle-btn" id="menuToggle"><i class="bi bi-list"></i></button>
    <h5 class="mb-0 fw-bold text-white">Dashboard</h5>
  </nav>

  <!-- ✅ Sidebar -->
  <aside id="sidebar">
    <div class="brand"><i class="bi bi-laptop"></i> MyCommiss</div>
    <ul>
      <li><a href="index.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
      <li><a href="product/products.php"><i class="bi bi-box-seam me-2"></i> จัดการสินค้า</a></li>
      <li><a href="categories/categories.php"><i class="bi bi-tags me-2"></i> ประเภทสินค้า</a></li>
      <li><a href="customer/customers.php"><i class="bi bi-people me-2"></i> ลูกค้า</a></li>
      <li><a href="order/orders.php"><i class="bi bi-bag-check me-2"></i> คำสั่งซื้อ</a></li>
    </ul>
    <a href="logout.php" class="logout-btn"><i class="bi bi-box-arrow-right me-2"></i> ออกจากระบบ</a>
  </aside>

  <!-- ✅ Main Content -->
  <div class="main-panel">
    <div class="content-wrapper">
      <h3 class="section-title"><i class="bi bi-speedometer2"></i> แผงควบคุมระบบหลังบ้าน</h3>

      <!-- 🔹 แถวสรุปข้อมูล -->
      <div class="row g-3">
        <div class="col-md-3 col-sm-6">
          <a href="product/products.php" class="text-decoration-none">
            <div class="card card-custom text-center p-3">
              <div class="card-body">
                <i class="bi bi-box-seam display-5 text-primary"></i>
                <h4 class="mt-3">สินค้า</h4>
                <h2><?= $total_products ?></h2>
              </div>
            </div>
          </a>
        </div>

        <div class="col-md-3 col-sm-6">
          <a href="categories/categories.php" class="text-decoration-none">
            <div class="card card-custom text-center p-3">
              <div class="card-body">
                <i class="bi bi-tags display-5 text-info"></i>
                <h4 class="mt-3">ประเภทสินค้า</h4>
                <h2><?= $total_categories ?></h2>
              </div>
            </div>
          </a>
        </div>

        <div class="col-md-3 col-sm-6">
          <a href="customer/customers.php" class="text-decoration-none">
            <div class="card card-custom text-center p-3">
              <div class="card-body">
                <i class="bi bi-people display-5 text-success"></i>
                <h4 class="mt-3">ลูกค้า</h4>
                <h2><?= $total_customers ?></h2>
              </div>
            </div>
          </a>
        </div>

        <div class="col-md-3 col-sm-6">
          <a href="order/orders.php" class="text-decoration-none">
            <div class="card card-custom text-center p-3">
              <div class="card-body">
                <i class="bi bi-bag-check display-5 text-warning"></i>
                <h4 class="mt-3">คำสั่งซื้อ</h4>
                <h2><?= $total_orders ?></h2>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- 🔹 การ์ดรายได้รวม -->
      <div class="row mt-4 justify-content-center">
        <div class="col-md-10">
          <div class="card card-custom text-center p-4 not-clickable">
            <div class="card-body">
              <i class="bi bi-cash-stack display-5 text-danger"></i>
              <h4 class="mt-3 text-white">รายได้รวมทั้งหมด</h4>
              <h2 class="text-success"><?= number_format($total_income, 2) ?> ฿</h2>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ✅ Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('menuToggle');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));
  }
  document.addEventListener('click', (e) => {
    if (window.innerWidth < 992 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
      sidebar.classList.remove('show');
    }
  });
  </script>
</body>
</html>

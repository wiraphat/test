<?php
session_start();
include("connectdb.php");

// 🔍 รับค่าค้นหา
$search = $_GET['search'] ?? '';
$cat = $_GET['cat'] ?? '';

// 🔹 ดึงหมวดหมู่
$cats = $conn->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// 🔹 ดึงสินค้า
$sql = "SELECT p.*, c.cat_name 
        FROM product p 
        LEFT JOIN category c ON p.cat_id = c.cat_id 
        WHERE 1";

if (!empty($search)) $sql .= " AND p.p_name LIKE :search";
if (!empty($cat)) $sql .= " AND p.cat_id = :cat";

$stmt = $conn->prepare($sql);
if (!empty($search)) $stmt->bindValue(':search', "%$search%");
if (!empty($cat)) $stmt->bindValue(':cat', $cat);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | หน้าร้าน</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #F6F7F8;
      color: #2B2D42;
    }

    /* 🔹 Navbar */
    .navbar {
      background-color: #15161D;
    }
    .navbar-brand {
      color: #FFF !important;
      font-weight: 700;
      font-size: 1.5rem;
    }
    .navbar-brand span {
      color: #D10024;
    }
    .nav-link {
      color: #FFF !important;
      margin: 0 10px;
      transition: 0.2s;
    }
    .nav-link:hover {
      color: #D10024 !important;
    }

    /* 🔹 การ์ดสินค้า */
    .product-card {
      background: #fff;
      border-radius: 10px;
      border: 1px solid #E4E7ED;
      box-shadow: 0 5px 15px rgba(0,0,0,0.06);
      transition: all 0.3s ease;
      overflow: hidden;
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }

    .product-card img {
      height: 200px;
      object-fit: cover;
      border-bottom: 1px solid #E4E7ED;
    }

    .product-card .price {
      color: #D10024;
      font-weight: 600;
      font-size: 1.1rem;
    }

    .btn-red {
      background-color: #D10024;
      color: #FFF;
      border-radius: 30px;
      transition: 0.2s;
    }
    .btn-red:hover {
      background-color: #a5001a;
    }

    footer {
      background: #15161D;
      color: #FFF;
      padding: 20px 0;
      text-align: center;
      margin-top: 40px;
    }

    .toast-container {
      z-index: 3000;
    }
  </style>
</head>
<body>

<!-- 🔺 Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">My<span>Commiss</span></a>
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <i class="fa fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="index.php" class="nav-link">หน้าแรก</a></li>
        <li class="nav-item"><a href="store.php" class="nav-link active text-danger">หน้าร้าน</a></li>
        <?php if(isset($_SESSION['customer_id'])): ?>
          <li class="nav-item"><a href="profile.php" class="nav-link"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['customer_name']) ?></a></li>
          <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a></li>
        <?php else: ?>
          <li class="nav-item"><a href="login.php" class="nav-link"><i class="fa fa-sign-in-alt"></i> เข้าสู่ระบบ</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- 🔔 Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <?php if (isset($_SESSION['toast_success'])): ?>
    <div class="toast align-items-center text-bg-success border-0 show">
      <div class="d-flex">
        <div class="toast-body"><?= $_SESSION['toast_success'] ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
    <?php unset($_SESSION['toast_success']); ?>
  <?php endif; ?>
</div>

<!-- 🔍 Search Section -->
<div class="container mt-5">
  <div class="card border-0 shadow-sm p-4 mb-4">
    <form class="row g-2 align-items-center" method="get">
      <div class="col-md-3">
        <select name="cat" class="form-select">
          <option value="">หมวดหมู่ทั้งหมด</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= $c['cat_id'] ?>" <?= $cat == $c['cat_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['cat_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-7">
        <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อสินค้า..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-red"><i class="fa fa-search"></i> ค้นหา</button>
      </div>
    </form>
  </div>

  <!-- 🛒 แสดงสินค้า -->
  <div class="row g-4">
    <?php if (count($products) > 0): ?>
      <?php foreach ($products as $p): ?>
        <?php
          $imagePath = "../admin/uploads/" . $p['p_image'];
          if (!file_exists($imagePath) || empty($p['p_image'])) {
            $imagePath = "img/default.png";
          }
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="product-card h-100">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($p['p_name']) ?>" class="w-100">
            <div class="p-3">
              <h6 class="text-truncate" title="<?= htmlspecialchars($p['p_name']) ?>">
                <?= htmlspecialchars($p['p_name']) ?>
              </h6>
              <p class="price mb-2"><?= number_format($p['p_price'], 2) ?> บาท</p>
              <a href="product_detail.php?id=<?= $p['p_id'] ?>" class="btn btn-outline-dark btn-sm w-100 mb-2">
                <i class="fa fa-eye"></i> ดูรายละเอียด
              </a>

              <?php if (isset($_SESSION['customer_id'])): ?>
                <form method="post" action="cart_add.php">
                  <input type="hidden" name="id" value="<?= $p['p_id'] ?>">
                  <button type="submit" class="btn btn-red btn-sm w-100">
                    <i class="fa fa-cart-plus"></i> หยิบใส่ตะกร้า
                  </button>
                </form>
              <?php else: ?>
                <a href="login.php" class="btn btn-outline-secondary btn-sm w-100">
                  <i class="fa fa-lock"></i> เข้าสู่ระบบเพื่อสั่งซื้อ
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-muted">ไม่พบสินค้าที่ค้นหา</p>
    <?php endif; ?>
  </div>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | หน้าร้าน
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.forEach(toastEl => {
      const toast = new bootstrap.Toast(toastEl, { delay: 5000, autohide: true });
      toast.show();
    });
  });
</script>
</body>
</html>

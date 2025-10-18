<?php
session_start();
include "connectdb.php";

// ดึงสินค้าใหม่ล่าสุด
$stmt = $conn->query("
  SELECT p.*, c.cat_name 
  FROM product p
  LEFT JOIN category c ON p.cat_id = c.cat_id
  ORDER BY p.p_id DESC
  LIMIT 8
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | หน้าหลัก</title>

  <!-- ✅ ใช้ฟอนต์และสไตล์จาก Electro Theme -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/slick.css">
  <link rel="stylesheet" href="css/slick-theme.css">
  <link rel="stylesheet" href="css/nouislider.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
  body {
    font-family: 'Montserrat', sans-serif;
    background-color: #F8F9FA;
  }
  .product-img img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 10px;
    transition: 0.3s;
  }
  .product-img img:hover {
    transform: scale(1.05);
  }
  .product {
    background: #fff;
    border: 1px solid #E4E7ED;
    border-radius: 10px;
    padding: 15px;
    transition: 0.2s;
  }
  .product:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }
  .product .product-price {
    color: #D10024;
    font-weight: 700;
    margin-top: 5px;
  }
  .product-name a {
    color: #2B2D42;
    font-weight: 600;
    text-decoration: none;
  }
  .product-name a:hover {
    color: #D10024;
  }
  </style>
</head>
<body>

  <!-- 🔹 Header -->
  <header>
    <div id="top-header">
      <div class="container">
        <ul class="header-links pull-left">
          <li><a href="#"><i class="fa fa-phone"></i> +66 81-234-5678</a></li>
          <li><a href="#"><i class="fa fa-envelope-o"></i> support@mycommiss.com</a></li>
        </ul>
        <ul class="header-links pull-right">
          <?php if (isset($_SESSION['customer_id'])): ?>
            <li><a href="profile.php"><i class="fa fa-user-o"></i> บัญชีของฉัน</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> ออกจากระบบ</a></li>
          <?php else: ?>
            <li><a href="login.php"><i class="fa fa-sign-in"></i> เข้าสู่ระบบ</a></li>
            <li><a href="register.php"><i class="fa fa-user-plus"></i> สมัครสมาชิก</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div id="header">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-3 text-center text-md-start">
            <h3 style="line-height:1.3;">
              <b><u><span style="color:#D10024;">MyCommiss</span></u></b><br>
              <b><u><span style="color:#ffffff;">MyCommiss</span></u></b><br>
              <b><u><span style="color:#2B2D42;">MyCommiss</span></u></b>
            </h3>
          </div>
          <div class="col-md-6 text-center">
            <form method="get" action="store.php" class="d-flex search-box">
              <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า...">
              <button class="btn btn-danger px-4"><i class="fa fa-search"></i></button>
            </form>
          </div>
          <div class="col-md-3 text-end">
            <a href="cart.php" class="text-white"><i class="fa fa-shopping-cart"></i> ตะกร้า</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- 🔹 NAV -->
  <nav id="navigation">
    <div class="container">
      <div id="responsive-nav">
        <ul class="main-nav nav navbar-nav">
          <li class="active"><a href="index.php">หน้าหลัก</a></li>
          <li><a href="#">โปรโมชั่น</a></li>
          <li><a href="#">หมวดหมู่</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- 🔹 SECTION: ดึงสินค้าจากฐานข้อมูล -->
  <div class="section">
    <div class="container">
      <div class="row text-center mb-4">
        <h3><i class="fa fa-star text-danger"></i> สินค้ามาใหม่</h3>
      </div>

      <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php if ($products): ?>
          <?php foreach ($products as $p): ?>
            <div class="col">
              <div class="product">
                <div class="product-img">
                  <?php
                    $uploadPath = "admin/uploads/";
                    $imgFile = $p['p_image'];
                    if (!empty($imgFile) && file_exists($uploadPath . $imgFile)) {
                      $imgPath = $uploadPath . $imgFile;
                    } else {
                      $imgPath = "img/default.png";
                    }
                  ?>
                  <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>">
                </div>
                <div class="product-body text-center mt-2">
                  <p class="product-category"><?= htmlspecialchars($p['cat_name']) ?></p>
                  <h4 class="product-name">
                    <a href="product_detail.php?id=<?= $p['p_id'] ?>">
                      <?= htmlspecialchars($p['p_name']) ?>
                    </a>
                  </h4>
                  <p class="product-price"><?= number_format($p['p_price'], 2) ?> บาท</p>
                  <?php if (isset($_SESSION['customer_id'])): ?>
                    <form method="post" action="cart_add.php">
                      <input type="hidden" name="id" value="<?= $p['p_id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fa fa-shopping-cart"></i> หยิบใส่ตะกร้า
                      </button>
                    </form>
                  <?php else: ?>
                    <a href="login.php" class="btn btn-outline-secondary btn-sm w-100">
                      <i class="fa fa-sign-in"></i> เข้าสู่ระบบเพื่อสั่งซื้อ
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted">ไม่มีสินค้าในระบบ</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- 🔹 FOOTER -->
  <footer id="footer" class="bg-dark text-white text-center py-3 mt-5">
    © <?= date('Y') ?> MyCommiss | Electro Theme Enhanced
  </footer>

  <!-- 🔹 JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/slick.min.js"></script>
  <script src="js/nouislider.min.js"></script>
  <script src="js/jquery.zoom.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>

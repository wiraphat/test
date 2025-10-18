<?php
session_start();
include "connectdb.php";

// ดึงหมวดหมู่
$cats = $conn->query("SELECT * FROM category ORDER BY cat_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// 🔥 สินค้าขายดี Top 10
$sql_top = "
  SELECT p.*, c.cat_name 
  FROM product p
  LEFT JOIN category c ON p.cat_id = c.cat_id
  ORDER BY p.sold_qty DESC
  LIMIT 10";
$topProducts = $conn->query($sql_top)->fetchAll(PDO::FETCH_ASSOC);

// 🆕 สินค้าใหม่ล่าสุด 10
$sql_new = "
  SELECT p.*, c.cat_name 
  FROM product p
  LEFT JOIN category c ON p.cat_id = c.cat_id
  ORDER BY p.created_at DESC
  LIMIT 10";
$newProducts = $conn->query($sql_new)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | หน้าหลัก</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
  body { font-family: 'Montserrat', sans-serif; background: #F8F9FA; }

  /* Banner */
  .category-banner {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
  }
  .category-banner img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: 0.4s ease;
  }
  .category-banner:hover img {
    transform: scale(1.05);
  }
  .category-banner .overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(209, 0, 36, 0.85);
    clip-path: polygon(0 0, 70% 0, 100% 100%, 0 100%);
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding-left: 30px;
  }

  /* Product */
  .product { 
    background: #fff; 
    border: 1px solid #E4E7ED; 
    border-radius: 10px; 
    padding: 15px; 
    transition: 0.2s; 
  }
  .product:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
  .product-img img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 10px;
  }
  .product-body { text-align: center; }
  .product-price { color: #D10024; font-weight: 700; }
  .product-name a { color: #2B2D42; font-weight: 600; text-decoration: none; }
  .product-name a:hover { color: #D10024; }
  </style>
</head>
<body>

<!-- 🔹 HEADER -->
<header class="py-3 bg-dark text-white text-center">
  <h2><span style="color:#D10024;">My</span>Commiss Store</h2>
</header>

<!-- 🔹 NAV -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="#">หน้าหลัก</a>
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link text-white" href="#">โปรโมชั่น</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="#">หมวดหมู่</a></li>
    </ul>
  </div>
</nav>

<!-- 🔹 Banner Section -->
<div class="container my-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="category-banner">
        <img src="img/laptop.jpg" alt="Laptop">
        <div class="overlay">
          <h4>Laptop Collection</h4>
          <a href="store.php?cat_id=1" class="text-white text-decoration-none">SHOP NOW <i class="fa fa-arrow-circle-o-right"></i></a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="category-banner">
        <img src="img/headphone.jpg" alt="Accessories">
        <div class="overlay">
          <h4>Accessories Collection</h4>
          <a href="store.php?cat_id=2" class="text-white text-decoration-none">SHOP NOW <i class="fa fa-arrow-circle-o-right"></i></a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="category-banner">
        <img src="img/camera.jpg" alt="Cameras">
        <div class="overlay">
          <h4>Cameras Collection</h4>
          <a href="store.php?cat_id=3" class="text-white text-decoration-none">SHOP NOW <i class="fa fa-arrow-circle-o-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 🔥 Top Selling -->
<div class="container mb-5">
  <h3 class="mb-4 text-center text-danger"><i class="fa fa-fire"></i> สินค้าขายดี Top 10</h3>
  <div class="row row-cols-1 row-cols-md-5 g-4">
    <?php foreach ($topProducts as $p): ?>
      <div class="col">
        <div class="product">
          <?php
            $uploadPath = "admin/uploads/";
            $imgFile = $p['p_image'];
            $imgPath = (!empty($imgFile) && file_exists($uploadPath.$imgFile)) ? 
              $uploadPath.$imgFile : "img/default.png";
          ?>
          <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>" class="product-img">
          <div class="product-body mt-2">
            <p class="text-muted small"><?= htmlspecialchars($p['cat_name']) ?></p>
            <h5 class="product-name"><a href="product_detail.php?id=<?= $p['p_id'] ?>"><?= htmlspecialchars($p['p_name']) ?></a></h5>
            <p class="product-price"><?= number_format($p['p_price'],2) ?> บาท</p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- 🆕 New Arrivals -->
<div class="container mb-5">
  <h3 class="mb-4 text-center text-danger"><i class="fa fa-star"></i> สินค้ามาใหม่ 10 รายการ</h3>
  <div class="row row-cols-1 row-cols-md-5 g-4">
    <?php foreach ($newProducts as $p): ?>
      <div class="col">
        <div class="product">
          <?php
            $uploadPath = "admin/uploads/";
            $imgFile = $p['p_image'];
            $imgPath = (!empty($imgFile) && file_exists($uploadPath.$imgFile)) ? 
              $uploadPath.$imgFile : "img/default.png";
          ?>
          <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>" class="product-img">
          <div class="product-body mt-2">
            <p class="text-muted small"><?= htmlspecialchars($p['cat_name']) ?></p>
            <h5 class="product-name"><a href="product_detail.php?id=<?= $p['p_id'] ?>"><?= htmlspecialchars($p['p_name']) ?></a></h5>
            <p class="product-price"><?= number_format($p['p_price'],2) ?> บาท</p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- 🔹 FOOTER -->
<footer class="bg-dark text-center text-white py-3">
  © <?= date('Y') ?> MyCommiss | Powered by Electro Theme
</footer>

</body>
</html>

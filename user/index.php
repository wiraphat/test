<?php
session_start();
include "connectdb.php";

// ✅ ดึงหมวดหมู่สินค้า
$cats = $conn->query("SELECT * FROM category ORDER BY cat_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ✅ ตรวจสอบว่ามีคอลัมน์ sold_qty / created_at ไหม ถ้าไม่มีจะข้าม
function columnExists($conn, $table, $column) {
  $stmt = $conn->prepare("SHOW COLUMNS FROM `$table` LIKE :col");
  $stmt->execute(['col' => $column]);
  return $stmt->fetch() ? true : false;
}

$hasSold = columnExists($conn, 'product', 'sold_qty');
$hasCreated = columnExists($conn, 'product', 'created_at');

// ✅ ดึงสินค้าขายดี 10 รายการ
if ($hasSold) {
  $topProducts = $conn->query("
    SELECT p.*, c.cat_name 
    FROM product p
    LEFT JOIN category c ON p.cat_id = c.cat_id
    ORDER BY p.sold_qty DESC
    LIMIT 10
  ")->fetchAll(PDO::FETCH_ASSOC);
} else {
  $topProducts = $conn->query("
    SELECT p.*, c.cat_name 
    FROM product p
    LEFT JOIN category c ON p.cat_id = c.cat_id
    ORDER BY p.p_id DESC
    LIMIT 10
  ")->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ ดึงสินค้าใหม่ล่าสุด 10 รายการ
if ($hasCreated) {
  $newProducts = $conn->query("
    SELECT p.*, c.cat_name 
    FROM product p
    LEFT JOIN category c ON p.cat_id = c.cat_id
    ORDER BY p.created_at DESC
    LIMIT 10
  ")->fetchAll(PDO::FETCH_ASSOC);
} else {
  $newProducts = $conn->query("
    SELECT p.*, c.cat_name 
    FROM product p
    LEFT JOIN category c ON p.cat_id = c.cat_id
    ORDER BY p.p_id DESC
    LIMIT 10
  ")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | หน้าร้าน</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <style>
    body { font-family: 'Montserrat', sans-serif; background: #fff; color: #2B2D42; }
    .section { padding: 40px 0; }
    .product img { width: 100%; height: 220px; object-fit: cover; border-radius: 10px; transition: .3s; }
    .product img:hover { transform: scale(1.05); }
    .product { background: #fff; border-radius: 10px; padding: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: .3s; }
    .product:hover { transform: translateY(-5px); }
    .product h6 a { text-decoration: none; color: #2B2D42; }
    .product h6 a:hover { color: #D10024; }
    h3.section-title { font-weight: 700; text-align: center; margin-bottom: 30px; }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="py-3 bg-dark text-white">
    <div class="container d-flex justify-content-between align-items-center">
      <h3><span style="color:#D10024;">My</span>Commiss</h3>
      <form method="get" class="d-flex" style="max-width:500px;width:100%;">
        <select name="cat_id" class="form-select me-2">
          <option value="">ประเภทสินค้า</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="text" name="search" class="form-control me-2" placeholder="ค้นหาสินค้า...">
        <button class="btn btn-danger">ค้นหา</button>
      </form>
      <div>
        <a href="wishlist.php" class="text-white me-3"><i class="fa fa-heart-o"></i></a>
        <a href="cart.php" class="text-white"><i class="fa fa-shopping-cart"></i></a>
      </div>
    </div>
  </header>

  <!-- 🔥 สินค้าขายดี Top 10 -->
  <div class="section">
    <div class="container">
      <h3 class="section-title text-danger"><i class="fa fa-fire"></i> สินค้าขายดี Top 10</h3>
      <div class="row g-4">
        <?php if ($topProducts): ?>
          <?php foreach ($topProducts as $p): 
            $uploadPath = "admin/uploads/";
            $imgPath = (!empty($p['p_image']) && file_exists($uploadPath.$p['p_image']))
              ? $uploadPath.$p['p_image'] : "img/default.png";
          ?>
            <div class="col-md-3 col-6">
              <div class="product text-center">
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>">
                <p class="text-muted small mt-2"><?= htmlspecialchars($p['cat_name'] ?? '-') ?></p>
                <h6><a href="product_detail.php?id=<?= $p['p_id'] ?>"><?= htmlspecialchars($p['p_name']) ?></a></h6>
                <p class="text-danger fw-bold mb-0"><?= number_format($p['p_price'], 2) ?> บาท</p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted">ยังไม่มีข้อมูลสินค้า</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- 🆕 สินค้าใหม่ล่าสุด -->
  <div class="section bg-light">
    <div class="container">
      <h3 class="section-title text-primary"><i class="fa fa-star"></i> สินค้าใหม่ล่าสุด</h3>
      <div class="row g-4">
        <?php if ($newProducts): ?>
          <?php foreach ($newProducts as $p): 
            $uploadPath = "admin/uploads/";
            $imgPath = (!empty($p['p_image']) && file_exists($uploadPath.$p['p_image']))
              ? $uploadPath.$p['p_image'] : "img/default.png";
          ?>
            <div class="col-md-3 col-6">
              <div class="product text-center">
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>">
                <p class="text-muted small mt-2"><?= htmlspecialchars($p['cat_name'] ?? '-') ?></p>
                <h6><a href="product_detail.php?id=<?= $p['p_id'] ?>"><?= htmlspecialchars($p['p_name']) ?></a></h6>
                <p class="text-danger fw-bold mb-0"><?= number_format($p['p_price'], 2) ?> บาท</p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted">ยังไม่มีสินค้าใหม่</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="bg-dark text-center text-white py-3">
    © <?= date('Y') ?> MyCommiss | หน้าเเรก 💻
  </footer>

</body>
</html>

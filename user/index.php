<?php
session_start();
include("connectdb.php");

// รับค่าค้นหา / หมวดหมู่
$search = $_GET['search'] ?? '';
$cat = $_GET['cat'] ?? '';

// ดึงหมวดหมู่
$cats = $conn->query("SELECT * FROM category ORDER BY cat_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ดึงสินค้า
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
  <title>MyCommiss | สินค้าทั้งหมด</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    body { font-family: 'Montserrat', sans-serif; background-color: #f8f9fa; }
    .section-title h3 { font-weight: 700; }
    .product-card {
      transition: all 0.3s ease;
      border-radius: 15px;
      background: #fff;
      overflow: hidden;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    .product-img img {
      width: 100%;
      height: 230px;
      object-fit: cover;
      border-bottom: 1px solid #eee;
    }
    .product-body {
      padding: 15px;
      text-align: center;
    }
    .product-body h6 {
      font-weight: 600;
      min-height: 45px;
    }
    .product-body p {
      color: #d10024;
      font-weight: bold;
      font-size: 16px;
    }
    .btn-cart {
      background: #d10024;
      color: #fff;
      border-radius: 30px;
      padding: 6px 15px;
      transition: 0.2s;
    }
    .btn-cart:hover {
      background: #a7001c;
      color: #fff;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<!-- ✅ Header ธีมเดิม -->
<?php include("navbar_user.php"); ?>

<!-- 🔍 ส่วนค้นหา -->
<div class="container mt-4">
  <form class="row mb-4" method="get">
    <div class="col-md-3">
      <select name="cat" class="form-select">
        <option value="">ทุกหมวดหมู่</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= $c['cat_id'] ?>" <?= $cat == $c['cat_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['cat_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-7">
      <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..."
             value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-dark">🔍 ค้นหา</button>
    </div>
  </form>
</div>

<!-- 🛍 แสดงสินค้า -->
<div class="section py-4">
  <div class="container">
    <div class="row g-4">
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
          <?php
            $imgPath = "http://212.80.215.29/admin/uploads/" . $p['p_image'];
          ?>
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="product-card h-100">
              <div class="product-img">
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>">
              </div>
              <div class="product-body">
                <small class="text-muted d-block mb-1"><?= htmlspecialchars($p['cat_name']) ?></small>
                <h6><?= htmlspecialchars($p['p_name']) ?></h6>
                <p><?= number_format($p['p_price'], 2) ?> บาท</p>

                <a href="product_detail.php?id=<?= $p['p_id'] ?>" class="btn btn-outline-dark btn-sm w-100 mb-2">
                  🔍 ดูรายละเอียด
                </a>

                <?php if (isset($_SESSION['customer_id'])): ?>
                  <form method="post" action="cart_add.php">
                    <input type="hidden" name="id" value="<?= $p['p_id'] ?>">
                    <button type="submit" class="btn btn-cart btn-sm w-100">🛒 เพิ่มในตะกร้า</button>
                  </form>
                <?php else: ?>
                  <a href="login.php" class="btn btn-outline-secondary btn-sm w-100">🔑 เข้าสู่ระบบเพื่อสั่งซื้อ</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center text-muted py-5">
          😕 ไม่พบสินค้าที่ค้นหา
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ✅ Footer -->
<footer id="footer" class="text-center py-3 bg-dark text-white">
  © <?= date('Y') ?> MyCommiss | หน้าร้านสินค้า
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

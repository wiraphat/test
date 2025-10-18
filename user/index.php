<?php
session_start();
include "connectdb.php";

// รับค่าค้นหา / หมวดหมู่
$search = $_GET['search'] ?? '';
$cat_id = $_GET['cat_id'] ?? '';

// ดึงหมวดหมู่
$cats = $conn->query("SELECT * FROM category ORDER BY cat_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ดึงสินค้า
$sql = "SELECT p.*, c.cat_name 
        FROM product p 
        LEFT JOIN category c ON p.cat_id = c.cat_id 
        WHERE 1";

if (!empty($search)) $sql .= " AND p.p_name LIKE :search";
if (!empty($cat_id)) $sql .= " AND p.cat_id = :cat_id";

$stmt = $conn->prepare($sql);
if (!empty($search)) $stmt->bindValue(':search', "%$search%");
if (!empty($cat_id)) $stmt->bindValue(':cat_id', $cat_id);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | หน้าร้าน</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/slick.css">
  <link rel="stylesheet" href="css/slick-theme.css">
  <link rel="stylesheet" href="css/nouislider.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
  /* ✅ ปรับโครงสร้าง header ให้ตรงกลางสวย */
  #header .row {
    align-items: center;
  }

  /* ✅ โลโก้ */
  .header-logo h3 {
    font-weight: 800;
    line-height: 1.3;
    margin: 0;
  }

  /* ✅ ช่องค้นหา */
  .header-search {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .header-search .input-select {
    border-radius: 30px 0 0 30px !important;
    border: 1px solid #E4E7ED;
    border-right: none;
    padding: 10px 15px;
    height: 42px;
    background: #fff;
  }

  .header-search .input {
    border: 1px solid #E4E7ED;
    border-left: none;
    border-right: none;
    padding: 10px 15px;
    height: 42px;
    flex: 1;
    border-radius: 0;
  }

  .header-search .search-btn {
    border-radius: 0 30px 30px 0 !important;
    background-color: #D10024;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
  }

  .header-search .search-btn:hover {
    background-color: #a7001c;
  }

  /* ✅ Wishlist / Cart */
  .header-ctn {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 30px;
  }

  .header-ctn a {
    color: #fff;
    text-decoration: none;
    text-align: center;
  }

  .header-ctn a:hover span {
    color: #D10024;
  }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header>
    <!-- TOP HEADER -->
    <div id="top-header">
      <div class="container">
        <ul class="header-links pull-left">
          <li><a href="#"><i class="fa fa-phone"></i> +66 81-234-5678</a></li>
          <li><a href="#"><i class="fa fa-envelope-o"></i> support@mycommiss.com</a></li>
          <li><a href="#"><i class="fa fa-map-marker"></i> Bangkok, Thailand</a></li>
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
    <!-- /TOP HEADER -->

    <!-- MAIN HEADER -->
    <div id="header">
      <div class="container">
        <div class="row align-items-center">
          <!--  โลโก้ -->
          <div class="col-md-3">
            <div class="header-logo text-center text-md-start">
              <h3>
                <b><u><span style="color:#D10024;">MyCommiss</span></u></b><br>
                <b><u><span style="color:#ffffff;">MyCommiss</span></u></b><br>
                <b><u><span style="color:#2B2D42;">MyCommiss</span></u></b>
              </h3>
            </div>
          </div>

          <!-- 🟦 ช่องค้นหา -->
<div class="col-md-6 d-flex justify-content-center align-items-center">
  <div class="header-search w-100" style="max-width:700px;">
    <form method="get" class="d-flex w-100">
      <select name="cat_id" class="input-select">
        <option value="">ประเภทสินค้า</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= $c['cat_id'] ?>" <?= $cat_id == $c['cat_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['cat_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <input type="text" name="search" class="input" value="<?= htmlspecialchars($search) ?>" placeholder="ค้นหาสินค้า...">

      <button type="submit" class="search-btn">ค้นหา</button>
    </form>
  </div>
</div>




          <!-- 🟨 Wishlist / Cart -->
          <div class="col-md-3">
            <div class="header-ctn">
              <div>
                <a href="wishlist.php">
                  <i class="fa fa-heart-o fa-lg"></i><br>
                  <span>สินค้าที่ชอบ</span>
                </a>
              </div>
              <div>
                <a href="cart.php">
                  <i class="fa fa-shopping-cart fa-lg"></i><br>
                  <span>ตะกร้าของฉัน</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <!-- /HEADER -->

  <!-- NAVIGATION -->
  <nav id="navigation">
    <div class="container">
      <div id="responsive-nav">
        <ul class="main-nav nav navbar-nav">
          <li class="active"><a href="index.php">หน้าหลัก</a></li>
          <li><a href="#">โปรโมชั่น</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- /NAVIGATION -->

  <!-- SECTION: PRODUCT LIST -->
  <div class="section">
    <div class="container">
      <div class="row">
        <?php if ($products): ?>
          <?php foreach ($products as $p): ?>
            <div class="col-md-3 col-xs-6">
              <div class="product">
                <div class="product-img">
                  <?php
                    $imgPath = "../admin/uploads/" . $p['p_image'];
                    if (!file_exists($imgPath) || empty($p['p_image'])) $imgPath = "img/default.png";
                  ?>
                  <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($p['p_name']) ?>">
                  <div class="product-label"><span class="new">NEW</span></div>
                </div>
                <div class="product-body">
                  <p class="product-category"><?= htmlspecialchars($p['cat_name'] ?? '-') ?></p>
                  <h3 class="product-name">
                    <a href="product_detail.php?id=<?= $p['p_id'] ?>">
                      <?= htmlspecialchars($p['p_name']) ?>
                    </a>
                  </h3>
                  <h4 class="product-price"><?= number_format($p['p_price'], 2) ?> บาท</h4>
                </div>
                <div class="add-to-cart">
                  <?php if (isset($_SESSION['customer_id'])): ?>
                    <form method="post" action="cart_add.php">
                      <input type="hidden" name="id" value="<?= $p['p_id'] ?>">
                      <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> หยิบใส่ตะกร้า</button>
                    </form>
                  <?php else: ?>
                    <a href="login.php" class="add-to-cart-btn"><i class="fa fa-sign-in"></i> เข้าสู่ระบบก่อน</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted">ไม่พบสินค้า</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer id="footer">
    <div class="section">
      <div class="container text-center">
        <span class="copyright">
          © <?= date('Y') ?> MyCommiss | หน้าแรก <a href="#">Electro Theme</a>
        </span>
      </div>
    </div>
  </footer>

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/slick.min.js"></script>
  <script src="js/nouislider.min.js"></script>
  <script src="js/jquery.zoom.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>

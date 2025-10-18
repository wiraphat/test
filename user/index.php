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
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/slick.css">
  <link rel="stylesheet" href="css/slick-theme.css">
  <link rel="stylesheet" href="css/nouislider.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
  body { font-family: 'Montserrat', sans-serif; }

  /* ✅ โลโก้ */
  .header-logo h3 {
    font-weight: 800;
    line-height: 1.3;
    margin: 0;
  }

  /* ✅ ช่องค้นหา */
  .search-box {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 30px;
    overflow: hidden;
    width: 100%;
    height: 48px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border: 1px solid #e4e7ed;
  }
  .input-select {
    border: none;
    background: #fff;
    color: #2B2D42;
    font-weight: 500;
    padding: 0 20px;
    flex: 0 0 180px;
    font-size: 15px;
    height: 100%;
    border-right: 1px solid #E4E7ED;
    appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg fill='%232B2D42' height='12' viewBox='0 0 24 24' width='12' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 14px;
  }
  .input {
    border: none;
    outline: none;
    background: #fff;
    padding: 0 18px;
    flex: 1;
    font-size: 15px;
    color: #2B2D42;
  }
  .input::placeholder { color: #888; }
  .search-btn {
    border: none;
    background: #D10024;
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    padding: 0 28px;
    height: 100%;
    border-radius: 0 30px 30px 0;
    transition: all 0.2s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .search-btn:hover {
    background-color: #a7001c;
    transform: scale(1.02);
  }

  /* ✅ รูปสินค้า */
  .product-img img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 10px;
    transition: 0.3s ease;
  }
  .product-img img:hover {
    transform: scale(1.05);
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

    <div id="header">
      <div class="container">
        <div class="row align-items-center">
          <!-- โลโก้ -->
          <div class="col-md-3">
            <div class="header-logo text-center text-md-start">
              <h3>
                <b><u><span style="color:#D10024;">MyCommiss</span></u></b><br>
                <b><u><span style="color:#ffffff;">MyCommiss</span></u></b><br>
                <b><u><span style="color:#2B2D42;">MyCommiss</span></u></b>
              </h3>
            </div>
          </div>

          <!-- ช่องค้นหา -->
          <div class="col-md-6 d-flex justify-content-center align-items-center">
            <div class="header-search w-100" style="max-width:750px;">
              <form method="get" class="search-box d-flex">
                <select name="cat_id" class="input-select">
                  <option value="">ประเภทสินค้า</option>
                  <?php foreach ($cats as $c): ?>
                    <option value="<?= $c['cat_id'] ?>" <?= $cat_id == $c['cat_id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($c['cat_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <input type="text" name="search" class="input" 
                       value="<?= htmlspecialchars($search) ?>" placeholder="ค้นหาสินค้า...">
                <button type="submit" class="search-btn"><i class="fa fa-search"></i> ค้นหา</button>
              </form>
            </div>
          </div>

          <!-- Wishlist / Cart -->
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

  <!-- เมนูหลัก -->
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

  

  <footer id="footer">
    <div class="section">
      <div class="container text-center">
        <span class="copyright">
          © <?= date('Y') ?> MyCommiss | หน้าแรก <a href="#">Electro Theme</a>
        </span>
      </div>
    </div>
  </footer>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/slick.min.js"></script>
  <script src="js/nouislider.min.js"></script>
  <script src="js/jquery.zoom.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>

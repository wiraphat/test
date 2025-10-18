<?php
session_start();
include "connectdb.php";
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
        <div class="row">
          <div class="col-md-3">
            <div class="header-logo">
              <h3>
                <b><u><span style="color:#D10024;">MyCommiss</span></u></b><br>
                <b><u><span style="color:#fff;">MyCommiss</span></u></b><br>
                <b><u><span style="color:#2B2D42;">MyCommiss</span></u></b>
              </h3>
            </div>
          </div>

          <div class="col-md-6">
            <div class="header-search">
              <form method="get" action="store.php">
                <select class="input-select" name="cat_id">
                  <option value="">ประเภทสินค้า</option>
                  <?php
                  $cats = $conn->query("SELECT * FROM category ORDER BY cat_name ASC")->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($cats as $c):
                  ?>
                    <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <input class="input" name="search" placeholder="ค้นหาสินค้า...">
                <button class="search-btn"><i class="fa fa-search"></i> ค้นหา</button>
              </form>
            </div>
          </div>

          <div class="col-md-3 clearfix">
            <div class="header-ctn">
              <div>
                <a href="wishlist.php">
                  <i class="fa fa-heart-o"></i>
                  <span>สินค้าที่ชอบ</span>
                </a>
              </div>
              <div>
                <a href="cart.php">
                  <i class="fa fa-shopping-cart"></i>
                  <span>ตะกร้าของฉัน</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- NAVIGATION -->
  <nav id="navigation">
    <div class="container">
      <div id="responsive-nav">
        <ul class="main-nav nav navbar-nav">
          <li class="active"><a href="index.php">หน้าหลัก</a></li>
          <li><a href="#">โปรโมชั่น</a></li>
          <?php foreach ($cats as $cat): ?>
            <li><a href="store.php?cat_id=<?= $cat['cat_id'] ?>"><?= htmlspecialchars($cat['cat_name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- SHOP CATEGORIES -->
  <div class="section">
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-xs-6">
          <div class="shop">
            <div class="shop-img">
              <img src="img/shop01.png" alt="">
            </div>
            <div class="shop-body">
              <h3>Laptop<br>Collection</h3>
              <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-xs-6">
          <div class="shop">
            <div class="shop-img">
              <img src="img/shop03.png" alt="">
            </div>
            <div class="shop-body">
              <h3>Accessories<br>Collection</h3>
              <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-xs-6">
          <div class="shop">
            <div class="shop-img">
              <img src="img/shop02.png" alt="">
            </div>
            <div class="shop-body">
              <h3>Cameras<br>Collection</h3>
              <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- NEW PRODUCTS -->
  <div class="section">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-title">
            <h3 class="title">New Products</h3>
            <div class="section-nav">
              <ul class="section-tab-nav tab-nav">
                <li class="active"><a data-toggle="tab" href="#tab1">สินค้าทั้งหมด</a></li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="row">
            <div class="products-tabs">
              <div id="tab1" class="tab-pane active">
                <div class="products-slick" data-nav="#slick-nav-1">
                  <?php
                  $sql_new = "SELECT p.*, c.cat_name FROM product p
                              LEFT JOIN category c ON p.cat_id = c.cat_id
                              ORDER BY p.p_id DESC LIMIT 10";
                  $newProducts = $conn->query($sql_new)->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($newProducts as $p):
                  ?>
                  <div class="product">
                    <div class="product-img">
                      <img src="uploads/<?= htmlspecialchars($p['p_image']) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>">
                      <div class="product-label">
                        <span class="new">NEW</span>
                      </div>
                    </div>
                    <div class="product-body">
                      <p class="product-category"><?= htmlspecialchars($p['cat_name']) ?></p>
                      <h3 class="product-name"><a href="product_detail.php?id=<?= $p['p_id'] ?>"><?= htmlspecialchars($p['p_name']) ?></a></h3>
                      <h4 class="product-price">฿<?= number_format($p['price'], 2) ?></h4>
                      <div class="product-rating">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                          <i class="fa fa-star<?= $i < ($p['rating'] ?? 0) ? '' : '-o' ?>"></i>
                        <?php endfor; ?>
                      </div>
                      <div class="product-btns">
                        <button class="add-to-wishlist"><i class="fa fa-heart-o"></i></button>
                        <button class="add-to-compare"><i class="fa fa-exchange"></i></button>
                        <button class="quick-view"><i class="fa fa-eye"></i></button>
                      </div>
                    </div>
                    <div class="add-to-cart">
                      <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> Add to cart</button>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
                <div id="slick-nav-1" class="products-slick-nav"></div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- TOP SELLING -->
  <div class="section">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-title">
            <h3 class="title">Top Selling</h3>
          </div>
        </div>
        <div class="col-md-12">
          <div class="products-slick" data-nav="#slick-nav-2">
            <?php
            $sql_top = "SELECT p.*, c.cat_name FROM product p
                        LEFT JOIN category c ON p.cat_id = c.cat_id
                        ORDER BY p.sold DESC LIMIT 10";
            $topProducts = $conn->query($sql_top)->fetchAll(PDO::FETCH_ASSOC);
            foreach ($topProducts as $p):
            ?>
            <div class="product">
              <div class="product-img">
                <img src="uploads/<?= htmlspecialchars($p['p_image']) ?>" alt="<?= htmlspecialchars($p['p_name']) ?>">
                <div class="product-label">
                  <span class="sale">Top</span>
                </div>
              </div>
              <div class="product-body">
                <p class="product-category"><?= htmlspecialchars($p['cat_name']) ?></p>
                <h3 class="product-name"><a href="product_detail.php?id=<?= $p['p_id'] ?>"><?= htmlspecialchars($p['p_name']) ?></a></h3>
                <h4 class="product-price">฿<?= number_format($p['price'], 2) ?></h4>
                <div class="product-rating">
                  <?php for ($i = 0; $i < 5; $i++): ?>
                    <i class="fa fa-star<?= $i < ($p['rating'] ?? 0) ? '' : '-o' ?>"></i>
                  <?php endfor; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <div id="slick-nav-2" class="products-slick-nav"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer id="footer">
    <div class="section">
      <div class="container text-center">
        <p>© 2025 MyCommiss. All Rights Reserved.</p>
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

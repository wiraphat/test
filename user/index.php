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
<?php
// เปิดแสดง error
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 🔥 ดึงสินค้าขายดี Top 10 (จำลองจากสินค้าที่มี stock น้อยสุด)
try {
  $topProducts = $conn->query("
    SELECT p.*, c.cat_name 
    FROM product p
    LEFT JOIN category c ON p.cat_id = c.cat_id
    ORDER BY p.p_stock ASC
    LIMIT 10
  ")->fetchAll(PDO::FETCH_ASSOC);

  // 🆕 ดึงสินค้าใหม่ล่าสุด 10 รายการ
  $newProducts = $conn->query("
    SELECT p.*, c.cat_name 
    FROM product p
    LEFT JOIN category c ON p.cat_id = c.cat_id
    ORDER BY p.created_at DESC
    LIMIT 10
  ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "<div style='color:red;text-align:center;margin-top:20px;'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  exit;
}
?>

<!-- 🔥 สินค้าขายดี Top 10 -->
<div class="container my-5">
  <h3 class="text-center text-danger fw-bold mb-4">
    <i class="fa fa-fire"></i> สินค้าขายดี Top 10
  </h3>

  <?php if (empty($topProducts)): ?>
    <p class="text-center text-muted">ยังไม่มีข้อมูลสินค้าขายดี</p>
  <?php else: ?>
  <div class="row row-cols-1 row-cols-md-5 g-4">
    <?php foreach ($topProducts as $p): ?>
      <?php
        $imgFile = trim($p['p_image']);
        // ✅ ใช้ URL เต็มตรงจากเว็บ Cloud
        $imgUrl = "http://212.80.215.29/test/admin/uploads/" . $imgFile;
        // ✅ ถ้าไม่มีชื่อไฟล์หรือไฟล์ไม่เจอ ให้ใช้ default.png
        if (empty($imgFile)) {
          $imgUrl = "img/default.png";
        }
      ?>
      <div class="col">
        <div class="product text-center p-3 border rounded shadow-sm bg-white">
          <div class="product-img mb-2">
            <img src="<?= htmlspecialchars($imgUrl) ?>" 
                 alt="<?= htmlspecialchars($p['p_name']) ?>" 
                 onerror="this.src='img/default.png';"
                 style="width:100%;height:220px;object-fit:cover;border-radius:10px;">
          </div>
          <p class="text-muted small mb-1"><?= htmlspecialchars($p['cat_name'] ?? '-') ?></p>
          <h6 class="fw-semibold">
            <a href="product_detail.php?id=<?= $p['p_id'] ?>" class="text-dark text-decoration-none">
              <?= htmlspecialchars($p['p_name']) ?>
            </a>
          </h6>
          <p class="text-danger fw-bold mb-0"><?= number_format($p['p_price'], 2) ?> บาท</p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<!-- shop -->
					<div class="col-md-4 col-xs-6">
						<div class="shop">
							<div class="shop-img">
								<img src="./img/shop01.png" alt="">
							</div>
							<div class="shop-body">
								<h3>Laptop<br>Collection</h3>
								<a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- /shop -->

					<!-- shop -->
					<div class="col-md-4 col-xs-6">
						<div class="shop">
							<div class="shop-img">
								<img src="./img/shop03.png" alt="">
							</div>
							<div class="shop-body">
								<h3>Accessories<br>Collection</h3>
								<a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- /shop -->

					<!-- shop -->
					<div class="col-md-4 col-xs-6">
						<div class="shop">
							<div class="shop-img">
								<img src="./img/shop02.png" alt="">
							</div>
							<div class="shop-body">
								<h3>Cameras<br>Collection</h3>
								<a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- /shop -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->

		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">

					<!-- section title -->
					<div class="col-md-12">
						<div class="section-title">
							<h3 class="title">New Products</h3>
							<div class="section-nav">
								<ul class="section-tab-nav tab-nav">
									<li class="active"><a data-toggle="tab" href="#tab1">Laptops</a></li>
									<li><a data-toggle="tab" href="#tab1">Smartphones</a></li>
									<li><a data-toggle="tab" href="#tab1">Cameras</a></li>
									<li><a data-toggle="tab" href="#tab1">Accessories</a></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /section title -->

					<!-- Products tab & slick -->
					<div class="col-md-12">
						<div class="row">
							<div class="products-tabs">
								<!-- tab -->
								<div id="tab1" class="tab-pane active">
									<div class="products-slick" data-nav="#slick-nav-1">
										<!-- product -->
										<div class="product">
											<div class="product-img">
												<img src="./img/product01.png" alt="">
												<div class="product-label">
													<span class="sale">-30%</span>
													<span class="new">NEW</span>
												</div>
											</div>
											<div class="product-body">
												<p class="product-category">Category</p>
												<h3 class="product-name"><a href="#">product name goes here</a></h3>
												<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
												<div class="product-rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</div>
												<div class="product-btns">
													<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
													<button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
													<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
												</div>
											</div>
											<div class="add-to-cart">
												<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
											</div>
										</div>
										<!-- /product -->

										<!-- product -->
										<div class="product">
											<div class="product-img">
												<img src="./img/product02.png" alt="">
												<div class="product-label">
													<span class="new">NEW</span>
												</div>
											</div>
											<div class="product-body">
												<p class="product-category">Category</p>
												<h3 class="product-name"><a href="#">product name goes here</a></h3>
												<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
												<div class="product-rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star-o"></i>
												</div>
												<div class="product-btns">
													<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
													<button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
													<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
												</div>
											</div>
											<div class="add-to-cart">
												<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
											</div>
										</div>
										<!-- /product -->

										<!-- product -->
										<div class="product">
											<div class="product-img">
												<img src="./img/product03.png" alt="">
												<div class="product-label">
													<span class="sale">-30%</span>
												</div>
											</div>
											<div class="product-body">
												<p class="product-category">Category</p>
												<h3 class="product-name"><a href="#">product name goes here</a></h3>
												<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
												<div class="product-rating">
												</div>
												<div class="product-btns">
													<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
													<button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
													<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
												</div>
											</div>
											<div class="add-to-cart">
												<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
											</div>
										</div>
										<!-- /product -->

										<!-- product -->
										<div class="product">
											<div class="product-img">
												<img src="./img/product04.png" alt="">
											</div>
											<div class="product-body">
												<p class="product-category">Category</p>
												<h3 class="product-name"><a href="#">product name goes here</a></h3>
												<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
												<div class="product-rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</div>
												<div class="product-btns">
													<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
													<button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
													<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
												</div>
											</div>
											<div class="add-to-cart">
												<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
											</div>
										</div>
										<!-- /product -->

										<!-- product -->
										<div class="product">
											<div class="product-img">
												<img src="./img/product05.png" alt="">
											</div>
											<div class="product-body">
												<p class="product-category">Category</p>
												<h3 class="product-name"><a href="#">product name goes here</a></h3>
												<h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
												<div class="product-rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</div>
												<div class="product-btns">
													<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
													<button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
													<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
												</div>
											</div>
											<div class="add-to-cart">
												<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
											</div>
										</div>
										<!-- /product -->
									</div>
									<div id="slick-nav-1" class="products-slick-nav"></div>
								</div>
								<!-- /tab -->
							</div>
						</div>
					</div>
					<!-- Products tab & slick -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->



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

<?php
session_start();
include("connectdb.php");

// 🔹 รับค่าค้นหา
$search = $_GET['search'] ?? '';
$cat = $_GET['cat'] ?? '';

// 🔹 ดึงข้อมูลหมวดหมู่
$cats = $conn->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// 🔹 ดึงข้อมูลสินค้า
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .toast {
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
    }
    .toast.show {
      opacity: 1;
    }
  </style>
</head>
<body class="bg-light">
  <!-- 🔔 Toast แสดงเมื่อเพิ่มสินค้าสำเร็จ -->
<?php if (isset($_SESSION['toast_success'])): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <?= $_SESSION['toast_success'] ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['toast_success']); ?>
<?php endif; ?>

<!-- 🔔 Toast แสดงเมื่อบันทึกโปรไฟล์สำเร็จ -->
<?php if (isset($_SESSION['toast_success'])): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <?= $_SESSION['toast_success'] ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['toast_success']); ?>
<?php endif; ?>

<!-- ✅ Navbar ส่วนกลาง -->
<?php include("navbar_user.php"); ?>

<div class="container mt-4">
  <!-- 🔍 ฟอร์มค้นหา -->
  <form class="row mb-4" method="get">
    <div class="col-md-4">
      <select name="cat" class="form-select">
        <option value="">-- เลือกหมวดหมู่ --</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= $c['cat_id'] ?>" <?= $cat == $c['cat_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['cat_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..." 
             value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-primary">ค้นหา</button>
    </div>
  </form>

  <!-- 🛍 แสดงสินค้า -->
  <div class="row row-cols-1 row-cols-md-4 g-4">
    <?php if (count($products) > 0): ?>
      <?php foreach ($products as $p): ?>
        <?php
          $imagePath = "../admin/uploads/" . $p['p_image'];
          if (!file_exists($imagePath) || empty($p['p_image'])) {
            $imagePath = "img/default.png"; // ใช้ภาพสำรอง
          }
        ?>
        <div class="col">
          <div class="card h-100 shadow-sm border-0">
            <img src="<?= $imagePath ?>" class="card-img-top" style="height:200px;object-fit:cover;">
            <div class="card-body">
              <h6 class="card-title text-truncate" title="<?= htmlspecialchars($p['p_name']) ?>">
                <?= htmlspecialchars($p['p_name']) ?>
              </h6>
              <p class="text-muted mb-2"><?= number_format($p['p_price'], 2) ?> บาท</p>
              <a href="product_detail.php?id=<?= $p['p_id'] ?>" 
                 class="btn btn-sm btn-outline-primary w-100">
                ดูรายละเอียด
              </a>

              <?php if (isset($_SESSION['customer_id'])): ?>
                <!-- ✅ ถ้าล็อกอินแล้ว แสดงปุ่มซื้อ -->
                <form method="post" action="cart_add.php" class="mt-2">
                  <input type="hidden" name="id" value="<?= $p['p_id'] ?>">
                  <button type="submit" class="btn btn-success btn-sm w-100">🛒 หยิบใส่ตะกร้า</button>
                </form>
              <?php else: ?>
                <!-- 🚫 ถ้ายังไม่ล็อกอิน -->
                <a href="login.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                  🔑 เข้าสู่ระบบเพื่อสั่งซื้อ
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

<footer class="text-center py-3 mt-5 bg-dark text-white">
  © <?= date('Y') ?> MyCommiss | หน้าร้าน
</footer>

<!-- ✅ Bootstrap Toast 5 วินาที -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const toastElList = [].slice.call(document.querySelectorAll('.toast'));
  const toastList = toastElList.map(function (toastEl) {
    return new bootstrap.Toast(toastEl, { delay: 5000, autohide: true });
  });
  toastList.forEach(toast => toast.show());
</script>

</body>
</html>

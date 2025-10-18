<?php
session_start();
include("connectdb.php");

// ✅ ต้องเข้าสู่ระบบก่อน
if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

// ✅ ฟังก์ชันลบสินค้า
if (isset($_GET['remove'])) {
  $id = intval($_GET['remove']);
  unset($_SESSION['cart'][$id]);
  header("Location: cart.php");
  exit;
}

// ✅ ฟังก์ชันอัปเดตจำนวนสินค้า
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
  foreach ($_POST['qty'] as $id => $qty) {
    if ($qty <= 0) unset($_SESSION['cart'][$id]);
    else $_SESSION['cart'][$id]['qty'] = intval($qty);
  }
  $_SESSION['toast_success'] = "✅ อัปเดตจำนวนสินค้าเรียบร้อยแล้ว!";
  header("Location: cart.php");
  exit;
}

// ✅ ดึงข้อมูลตะกร้า
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | ตะกร้าสินค้า</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: #f6f6f6;
    }
    .navbar {
      background-color: #2B2D42 !important;
    }
    .navbar a.nav-link {
      color: #fff !important;
      font-weight: 500;
    }
    .navbar a.nav-link:hover {
      color: #D10024 !important;
    }
    .cart-title {
      color: #D10024;
      font-weight: 700;
      text-align: center;
      margin-top: 40px;
    }
    .btn-primary {
      background: #D10024;
      border: none;
    }
    .btn-primary:hover {
      background: #a7001c;
    }
    footer {
      background: #2B2D42;
      color: #fff;
      padding: 15px 0;
      text-align: center;
      margin-top: 50px;
    }
  </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="index.php">
      <i class="fa fa-shopping-bag"></i> MyCommiss
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">หน้าหลัก</a></li>
        <li class="nav-item"><a class="nav-link" href="wishlist.php">สินค้าที่ชอบ</a></li>
        <li class="nav-item"><a class="nav-link active" href="cart.php">ตะกร้า</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- 🔔 Toast แจ้งเตือน -->
<?php if (isset($_SESSION['toast_success']) || isset($_SESSION['toast_error'])): ?>
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
    <?php if (isset($_SESSION['toast_error'])): ?>
      <div class="toast align-items-center text-bg-danger border-0 show">
        <div class="d-flex">
          <div class="toast-body"><?= $_SESSION['toast_error'] ?></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
      <?php unset($_SESSION['toast_error']); ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<!-- ✅ เนื้อหาหลัก -->
<div class="container my-5">
  <h3 class="cart-title"><i class="fa fa-shopping-cart"></i> ตะกร้าสินค้าของคุณ</h3>

  <?php if (empty($cart)): ?>
    <div class="alert alert-info text-center mt-4 shadow-sm">
      🧺 ยังไม่มีสินค้าในตะกร้า  
      <br><br>
      <a href="index.php" class="btn btn-primary">⬅️ กลับไปเลือกซื้อสินค้า</a>
    </div>
  <?php else: ?>
    <form method="post" class="mt-4">
      <div class="table-responsive shadow-sm">
        <table class="table align-middle text-center table-bordered bg-white">
          <thead class="table-dark">
            <tr>
              <th>ภาพสินค้า</th>
              <th>ชื่อสินค้า</th>
              <th>ราคา</th>
              <th>จำนวน</th>
              <th>รวม</th>
              <th>ลบ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $item): 
              $sum = $item['price'] * $item['qty'];
              $total += $sum;
              $imgPath = "../admin/uploads/" . $item['image'];
              if (!file_exists($imgPath) || empty($item['image'])) $imgPath = "img/default.png";
            ?>
              <tr>
                <td><img src="<?= $imgPath ?>" width="70" height="70" class="rounded shadow-sm"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 2) ?> บาท</td>
                <td style="width:100px;">
                  <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1" class="form-control text-center">
                </td>
                <td class="text-danger fw-bold"><?= number_format($sum, 2) ?> บาท</td>
                <td><a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="table-light">
              <th colspan="4" class="text-end">💰 รวมทั้งหมด:</th>
              <th colspan="2" class="text-danger fw-bold"><?= number_format($total, 2) ?> บาท</th>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="d-flex justify-content-between mt-3">
        <a href="index.php" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> กลับหน้าร้าน</a>
        <div class="d-flex gap-2">
          <button type="submit" name="update" class="btn btn-warning"><i class="fa fa-refresh"></i> อัปเดตจำนวน</button>
          <a href="checkout.php" class="btn btn-success"><i class="fa fa-credit-card"></i> ชำระเงิน</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | Powered by Electro Template
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.forEach(toastEl => {
      const toast = new bootstrap.Toast(toastEl, { delay: 4000, autohide: true });
      toast.show();
    });
  });
</script>
</body>
</html>

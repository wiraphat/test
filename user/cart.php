<?php
session_start();
include("connectdb.php");

// ✅ ต้องเข้าสู่ระบบก่อน
if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

// ✅ ฟังก์ชันลบสินค้าออกจากตะกร้า
if (isset($_GET['remove'])) {
  $id = intval($_GET['remove']);
  unset($_SESSION['cart'][$id]);
  header("Location: cart.php");
  exit;
}

// ✅ ฟังก์ชันอัปเดตจำนวนสินค้า
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
  foreach ($_POST['qty'] as $id => $qty) {
    if ($qty <= 0) {
      unset($_SESSION['cart'][$id]);
    } else {
      $_SESSION['cart'][$id]['qty'] = intval($qty);
    }
  }
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<!-- 🔔 Toast แสดงเมื่อมีข้อความแจ้งเตือน -->
<?php if (isset($_SESSION['toast_success']) || isset($_SESSION['toast_error'])): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3">
    <?php if (isset($_SESSION['toast_success'])): ?>
      <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body"><?= $_SESSION['toast_success'] ?></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
      <?php unset($_SESSION['toast_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['toast_error'])): ?>
      <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body"><?= $_SESSION['toast_error'] ?></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
      <?php unset($_SESSION['toast_error']); ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<!-- ✅ Navbar ส่วนกลาง -->
<?php include("navbar_user.php"); ?>

<div class="container mt-4">
  <h3 class="fw-bold mb-4 text-center">🛒 ตะกร้าสินค้าของคุณ</h3>

  <?php if (empty($cart)): ?>
    <div class="alert alert-info text-center shadow-sm">
      🧺 ยังไม่มีสินค้าในตะกร้า  
      <br><br>
      <a href="index.php" class="btn btn-primary">⬅️ กลับไปเลือกซื้อสินค้า</a>
    </div>
  <?php else: ?>
    <form method="post">
      <div class="table-responsive shadow-sm rounded">
        <table class="table align-middle table-bordered text-center bg-white">
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
              if (!file_exists($imgPath) || empty($item['image'])) {
                $imgPath = "img/default.png";
              }
            ?>
              <tr>
                <td><img src="<?= $imgPath ?>" width="80" height="80" class="rounded shadow-sm"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 2) ?> บาท</td>
                <td style="width:100px;">
                  <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" 
                         min="1" class="form-control text-center">
                </td>
                <td><?= number_format($sum, 2) ?> บาท</td>
                <td>
                  <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('ลบสินค้านี้ออกจากตะกร้า?');">ลบ</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="table-light">
              <th colspan="4" class="text-end">💰 ราคารวมทั้งหมด:</th>
              <th colspan="2" class="text-danger"><?= number_format($total, 2) ?> บาท</th>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="d-flex justify-content-between mt-3">
        <a href="index.php" class="btn btn-secondary">⬅️ กลับหน้าร้าน</a>
        <div class="d-flex gap-2">
          <button type="submit" name="update" class="btn btn-warning">🔁 อัปเดตจำนวน</button>
          <a href="checkout.php" class="btn btn-success">✅ ดำเนินการชำระเงิน</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<footer class="text-center py-3 mt-5 bg-dark text-white">
  © <?= date('Y') ?> MyCommiss | ตะกร้าสินค้า
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // ให้ Toast แสดง 5 วิ แล้วปิดเอง
  document.addEventListener("DOMContentLoaded", () => {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.forEach(toastEl => {
      const toast = new bootstrap.Toast(toastEl, { delay: 5000, autohide: true });
      toast.show();
    });
  });
</script>

</body>
</html>

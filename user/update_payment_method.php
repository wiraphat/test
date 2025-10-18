<?php
session_start();
include("connectdb.php");

// ✅ ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

// ✅ ตรวจสอบข้อมูลที่ส่งมา
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['order_id'], $_POST['payment_method'])) {
  $toast_error = "❌ ข้อมูลไม่ถูกต้อง";
} else {
  $order_id = intval($_POST['order_id']);
  $payment_method = trim($_POST['payment_method']);
  $customer_id = $_SESSION['customer_id'];

  // ✅ ตรวจสอบสิทธิ์คำสั่งซื้อ
  $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
  $stmt->execute([$order_id, $customer_id]);
  $order = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$order) {
    $toast_error = "❌ ไม่พบคำสั่งซื้อของคุณ";
  } else {
    // ✅ อัปเดตวิธีชำระเงิน
    $update = $conn->prepare("UPDATE orders SET payment_method = ? WHERE order_id = ?");
    $update->execute([$payment_method, $order_id]);
    $toast_success = "✅ เปลี่ยนวิธีชำระเงินเรียบร้อยแล้ว!";
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>อัปเดตการชำระเงิน | MyCommiss</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', sans-serif; background-color: #f6f6f6; }
    .navbar { background-color: #2B2D42 !important; }
    .navbar a.nav-link { color: #fff !important; font-weight: 500; }
    .navbar a.nav-link:hover { color: #D10024 !important; }
    .card {
      max-width: 500px;
      margin: 100px auto;
      border-radius: 15px;
      border: none;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    .card-header {
      background-color: #D10024;
      color: #fff;
      font-weight: bold;
      border-radius: 15px 15px 0 0;
      text-align: center;
    }
    footer {
      background: #2B2D42;
      color: #fff;
      padding: 15px 0;
      text-align: center;
      margin-top: 60px;
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
        <li class="nav-item"><a class="nav-link" href="order_history.php">คำสั่งซื้อ</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">อัปเดตการชำระเงิน</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ Toast แจ้งเตือน -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:3000;">
  <?php if (!empty($toast_success)): ?>
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body"><?= $toast_success ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php elseif (!empty($toast_error)): ?>
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body"><?= $toast_error ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- ✅ การ์ดแสดงสถานะ -->
<div class="card text-center">
  <div class="card-header">
    🧾 อัปเดตวิธีชำระเงิน
  </div>
  <div class="card-body p-4">
    <?php if (!empty($toast_success)): ?>
      <i class="fa fa-check-circle text-success" style="font-size:60px;"></i>
      <h4 class="mt-3 text-success">สำเร็จ!</h4>
      <p class="text-muted">คุณได้เปลี่ยนวิธีชำระเงินเรียบร้อยแล้ว</p>
      <a href="order_detail.php?id=<?= $order_id ?>" class="btn btn-primary mt-3">
        <i class="fa fa-arrow-right"></i> กลับไปหน้ารายละเอียดคำสั่งซื้อ
      </a>
    <?php else: ?>
      <i class="fa fa-exclamation-triangle text-danger" style="font-size:60px;"></i>
      <h4 class="mt-3 text-danger">ไม่สำเร็จ</h4>
      <p class="text-muted"><?= $toast_error ?? 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล' ?></p>
      <a href="order_history.php" class="btn btn-secondary mt-3">
        <i class="fa fa-chevron-left"></i> กลับหน้าคำสั่งซื้อ
      </a>
    <?php endif; ?>
  </div>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | อัปเดตการชำระเงิน
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

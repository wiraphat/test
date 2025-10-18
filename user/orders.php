<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("connectdb.php");

if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

$customer_id = $_SESSION['customer_id'];

// ✅ ดึงเฉพาะออเดอร์ของลูกค้าคนนี้ (เรียงจากใหม่ -> เก่า)
$sql = "SELECT * FROM orders WHERE customer_id = :cid ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':cid', $customer_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | ประวัติคำสั่งซื้อ</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f6f6f6;
      font-family: 'Montserrat', sans-serif;
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
    h3.title {
      color: #D10024;
      font-weight: 700;
      text-align: center;
      margin-top: 40px;
    }
    .table th {
      background-color: #2B2D42 !important;
      color: #fff;
    }
    .btn-primary {
      background: #D10024;
      border: none;
    }
    .btn-primary:hover {
      background: #a7001c;
    }
    .btn-warning {
      background: #fbb034;
      border: none;
    }
    footer {
      background: #2B2D42;
      color: #fff;
      padding: 15px 0;
      text-align: center;
      margin-top: 50px;
    }
    .badge {
      font-size: 0.9rem;
      padding: 6px 10px;
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
        <li class="nav-item"><a class="nav-link" href="cart.php">ตะกร้า</a></li>
        <li class="nav-item"><a class="nav-link active" href="order_history.php">คำสั่งซื้อ</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- 🔔 Toast แจ้งเตือน -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:3000;">
  <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $color): ?>
    <?php if (isset($_SESSION["toast_{$key}"])): ?>
      <div class="toast align-items-center text-bg-<?= $color ?> border-0 show" role="alert">
        <div class="d-flex">
          <div class="toast-body"><?= $_SESSION["toast_{$key}"] ?></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
      <?php unset($_SESSION["toast_{$key}"]); ?>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<div class="container my-5">
  <h3 class="title"><i class="fa fa-list"></i> ประวัติคำสั่งซื้อของคุณ</h3>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info text-center shadow-sm mt-4">
      😕 ยังไม่มีคำสั่งซื้อในระบบ<br>
      <a href="index.php" class="btn btn-primary mt-3"><i class="fa fa-chevron-left"></i> กลับไปเลือกซื้อสินค้า</a>
    </div>
  <?php else: ?>
    <div class="table-responsive shadow-sm mt-4">
      <table class="table align-middle text-center table-bordered bg-white">
        <thead>
          <tr>
            <th>#</th>
            <th>วันที่สั่งซื้อ</th>
            <th>วิธีชำระเงิน</th>
            <th>ยอดรวม</th>
            <th>สถานะชำระเงิน</th>
            <th>สถานะคำสั่งซื้อ</th>
            <th>การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $index = 1;
          foreach ($orders as $o):
            $status = $o['payment_status'] ?? 'รอดำเนินการ';
            $order_status = $o['order_status'] ?? 'รอดำเนินการ';
            $admin_verified = $o['admin_verified'] ?? 'รอตรวจสอบ';

            // สี badge payment
            $badgeClass = match($status) {
              'ชำระเงินแล้ว' => 'success',
              'ยกเลิก' => 'danger',
              default => 'warning'
            };
            // สี badge order
            $orderBadge = match($order_status) {
              'จัดส่งแล้ว' => 'success',
              'กำลังจัดเตรียม' => 'info',
              'ยกเลิก' => 'danger',
              default => 'secondary'
            };
            // payment method
            $methodText = match($o['payment_method']) {
              'QR' => 'ชำระด้วย QR Code',
              'COD' => 'เก็บเงินปลายทาง',
              default => htmlspecialchars($o['payment_method'])
            };
            $rowClass = ($order_status === 'ยกเลิก') ? 'table-danger' : '';
          ?>
            <tr class="<?= $rowClass ?>">
              <td><?= $index++ ?></td>
              <td><?= date('d/m/Y H:i', strtotime($o['order_date'])) ?></td>
              <td><?= $methodText ?></td>
              <td class="text-danger fw-bold"><?= number_format($o['total_price'], 2) ?> ฿</td>
              <td><span class="badge bg-<?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
              <td><span class="badge bg-<?= $orderBadge ?>"><?= htmlspecialchars($order_status) ?></span></td>
              <td>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                  <?php if ($o['payment_method'] === 'QR' && $status === 'รอดำเนินการ' && !in_array($admin_verified, ['กำลังตรวจสอบ', 'อนุมัติ'])): ?>
                    <a href="payment_confirm.php?id=<?= $o['order_id'] ?>" class="btn btn-sm btn-warning">
                      <i class="fa fa-money"></i> แจ้งชำระเงิน
                    </a>
                  <?php endif; ?>
                  <a href="order_detail.php?id=<?= $o['order_id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-search"></i> รายละเอียด
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | ประวัติคำสั่งซื้อ
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

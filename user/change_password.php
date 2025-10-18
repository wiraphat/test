<?php
session_start();
include("connectdb.php");

// 🔒 ต้องเข้าสู่ระบบก่อน
if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

$customer_id = $_SESSION['customer_id'];

// ✅ เมื่อมีการส่งฟอร์มเปลี่ยนรหัสผ่าน
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old_password = $_POST['old_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  // ✅ ดึงข้อมูลผู้ใช้จากฐานข้อมูล
  $stmt = $conn->prepare("SELECT password FROM customers WHERE customer_id = ?");
  $stmt->execute([$customer_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    $_SESSION['toast_error'] = "❌ ไม่พบข้อมูลผู้ใช้";
    header("Location: change_password.php");
    exit;
  }

  // ✅ ตรวจสอบรหัสผ่านเก่า
  if (!password_verify($old_password, $user['password'])) {
    $_SESSION['toast_error'] = "❌ รหัสผ่านเดิมไม่ถูกต้อง";
    header("Location: change_password.php");
    exit;
  }

  // ✅ ตรวจสอบว่ารหัสใหม่ตรงกันไหม
  if ($new_password !== $confirm_password) {
    $_SESSION['toast_error'] = "❌ รหัสผ่านใหม่ไม่ตรงกัน";
    header("Location: change_password.php");
    exit;
  }

  // ✅ บันทึกรหัสใหม่ (เข้ารหัสก่อน)
  $hashed = password_hash($new_password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE customers SET password = ? WHERE customer_id = ?");
  $stmt->execute([$hashed, $customer_id]);

  // ✅ Toast แจ้งเตือนสำเร็จ
  $_SESSION['toast_success'] = "✅ เปลี่ยนรหัสผ่านเรียบร้อยแล้ว";

  header("Location: profile.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เปลี่ยนรหัสผ่าน | MyCommiss</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f6f6f6;
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
    .card {
      max-width: 500px;
      margin: 70px auto;
      border-radius: 15px;
      border: none;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    .card-header {
      background: #D10024;
      color: #fff;
      font-weight: bold;
      border-radius: 15px 15px 0 0;
      text-align: center;
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
      margin-top: 60px;
    }
  </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="index.php"><i class="fa fa-shopping-bag"></i> MyCommiss</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">หน้าหลัก</a></li>
        <li class="nav-item"><a class="nav-link" href="profile.php">โปรไฟล์</a></li>
        <li class="nav-item"><a class="nav-link active" href="change_password.php">เปลี่ยนรหัสผ่าน</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- 🔔 Toast แจ้งเตือน -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <?php if (isset($_SESSION['toast_error'])): ?>
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body"><?= $_SESSION['toast_error'] ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
    <?php unset($_SESSION['toast_error']); ?>
  <?php endif; ?>
</div>

<!-- 🔐 ฟอร์มเปลี่ยนรหัส -->
<div class="card">
  <div class="card-header">🔑 เปลี่ยนรหัสผ่าน</div>
  <div class="card-body p-4">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">รหัสผ่านเดิม</label>
        <input type="password" name="old_password" class="form-control" placeholder="••••••" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">รหัสผ่านใหม่</label>
        <input type="password" name="new_password" class="form-control" placeholder="••••••" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่</label>
        <input type="password" name="confirm_password" class="form-control" placeholder="••••••" required>
      </div>

      <div class="d-flex justify-content-between mt-4">
        <a href="profile.php" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> กลับ</a>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </form>
  </div>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | เปลี่ยนรหัสผ่าน
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // ✅ Toast แสดงอัตโนมัติ
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

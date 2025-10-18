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

  // ✅ ตั้ง Toast แจ้งเตือนสำเร็จ
  $_SESSION['toast_success'] = "✅ เปลี่ยนรหัสผ่านเรียบร้อยแล้ว";

  // ✅ กลับไปหน้าโปรไฟล์
  header("Location: profile.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เปลี่ยนรหัสผ่าน | MyCommiss</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card {
      max-width: 500px;
      margin: 60px auto;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .card-header {
      background: #198754;
      color: #fff;
      font-weight: bold;
      border-radius: 15px 15px 0 0;
    }
    .btn:hover { transform: scale(1.05); transition: 0.2s; }
  </style>
</head>
<body>

<?php include("navbar_user.php"); ?>

<!-- 🔔 Toast แจ้งเตือน -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <?php if (isset($_SESSION['toast_error'])): ?>
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body">
          <?= $_SESSION['toast_error'] ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
    <?php unset($_SESSION['toast_error']); ?>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header text-center">🔑 เปลี่ยนรหัสผ่าน</div>
  <div class="card-body p-4">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">รหัสผ่านเดิม</label>
        <input type="password" name="old_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">รหัสผ่านใหม่</label>
        <input type="password" name="new_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="profile.php" class="btn btn-secondary">⬅️ กลับ</a>
        <button type="submit" class="btn btn-success">💾 เปลี่ยนรหัสผ่าน</button>
      </div>
    </form>
  </div>
</div>

<footer class="text-center py-3 mt-5 bg-dark text-white">
  © <?= date('Y') ?> MyCommiss | เปลี่ยนรหัสผ่าน
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // ✅ ให้ Toast แสดง 5 วิ แล้วปิดอัตโนมัติ
  const toastElList = [].slice.call(document.querySelectorAll('.toast'));
  const toastList = toastElList.map(toastEl => new bootstrap.Toast(toastEl, { delay: 5000, autohide: true }));
  toastList.forEach(toast => toast.show());
</script>

</body>
</html>

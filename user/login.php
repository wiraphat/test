<?php
session_start();
include("connectdb.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['customer_id'] = $user['customer_id'];
    $_SESSION['customer_name'] = $user['name'];

    // ✅ ใช้ Toast แทน alert
    $_SESSION['toast_success'] = "✅ เข้าสู่ระบบสำเร็จ ยินดีต้อนรับคุณ " . htmlspecialchars($user['name']);
    header("Location: index.php");
    exit;
  } else {
    $_SESSION['toast_error'] = "❌ อีเมลหรือรหัสผ่านไม่ถูกต้อง";
    header("Location: login.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เข้าสู่ระบบ | MyCommiss</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- ✅ Toast แจ้งเตือน -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:3000;">
  <?php if (isset($_SESSION['toast_success'])): ?>
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body"><?= $_SESSION['toast_success'] ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
    <?php unset($_SESSION['toast_success']); ?>
  <?php endif; ?>

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

<div class="container mt-5" style="max-width:500px;">
  <div class="card shadow border-0">
    <div class="card-header bg-dark text-white text-center fw-bold">เข้าสู่ระบบ</div>
    <div class="card-body">
      <form method="post">
        <div class="mb-3">
          <label class="form-label">อีเมล</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">รหัสผ่าน</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="d-grid">
          <button class="btn btn-primary">🔓 เข้าสู่ระบบ</button>
        </div>
      </form>
    </div>
  </div>
  <div class="text-center mt-3">
    <a href="register.php" class="text-decoration-none">ยังไม่มีบัญชี? สมัครสมาชิก</a><br>
    <a href="index.php" class="btn btn-secondary btn-sm mt-2">⬅️ กลับหน้าหลัก</a>
  </div>
</div>

<footer class="text-center py-3 mt-5 bg-dark text-white">
  © <?= date('Y') ?> MyCommiss | เข้าสู่ระบบ
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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

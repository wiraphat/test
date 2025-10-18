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
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/slick.css">
  <link rel="stylesheet" href="css/slick-theme.css">
  <link rel="stylesheet" href="css/nouislider.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      background: #fff;
      font-family: 'Montserrat', sans-serif;
    }
    .login-wrapper {
      max-width: 420px;
      margin: 80px auto;
      background: #FBFBFC;
      border: 1px solid #E4E7ED;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
      padding: 40px 35px;
    }
    .login-wrapper h3 {
      text-align: center;
      color: #2B2D42;
      font-weight: 700;
      margin-bottom: 30px;
    }
    .form-control {
      border-radius: 25px;
      height: 45px;
      border: 1px solid #E4E7ED;
      box-shadow: none;
      font-size: 15px;
    }
    .form-control:focus {
      border-color: #D10024;
      box-shadow: 0 0 0 0.2rem rgba(209,0,36,0.15);
    }
    .primary-btn {
      background: #D10024;
      color: #fff;
      border-radius: 30px;
      font-weight: 600;
      transition: all 0.3s;
    }
    .primary-btn:hover {
      background: #a5001a;
      color: #fff;
    }
    .text-link {
      color: #D10024;
      font-weight: 500;
      text-decoration: none;
    }
    .text-link:hover {
      text-decoration: underline;
    }
    footer {
      text-align: center;
      color: #999;
      margin-top: 50px;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<!-- 🔔 Toast แจ้งเตือน -->
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

<!-- 🔐 กล่องล็อกอิน -->
<div class="login-wrapper">
  <h3>เข้าสู่ระบบ MyCommiss</h3>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">อีเมล</label>
      <input type="email" name="email" class="form-control" required placeholder="example@email.com">
    </div>
    <div class="mb-3">
      <label class="form-label">รหัสผ่าน</label>
      <input type="password" name="password" class="form-control" required placeholder="••••••••">
    </div>
    <div class="d-grid mt-4">
      <button type="submit" class="btn primary-btn btn-lg">🔓 เข้าสู่ระบบ</button>
    </div>
  </form>
  <div class="text-center mt-4">
    <p>ยังไม่มีบัญชี? <a href="register.php" class="text-link">สมัครสมาชิก</a></p>
    <a href="index.php" class="text-secondary small"><i class="fa fa-arrow-left"></i> กลับหน้าหลัก</a>
  </div>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | เข้าสู่ระบบ
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.toast').forEach(toastEl => {
    const toast = new bootstrap.Toast(toastEl, { delay: 4000, autohide: true });
    toast.show();
  });
});
</script>

</body>
</html>

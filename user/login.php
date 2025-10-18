<?php
session_start();
include("connectdb.php");

// 🔹 ตรวจสอบการเข้าสู่ระบบ
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
    background: #F6F7F8;
    font-family: 'Montserrat', sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  /* กล่อง login */
  .login-wrapper {
    background: #fff;
    border: 1px solid #E4E7ED;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    padding: 50px 60px;
    max-width: 520px;
    min-height: 450px;
    margin: 60px auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: all 0.3s ease;
  }
  .login-wrapper:hover {
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    transform: translateY(-4px);
  }

  /* หัวข้อ */
  .login-title {
    text-align: center;
    color: #2B2D42;
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 25px;
  }
  .login-title i {
    color: #D10024;
    margin-right: 6px;
  }

  /* ช่อง input */
  .input {
    border-radius: 40px;
    border: 1px solid #E4E7ED;
    padding: 10px 18px;
    transition: 0.2s;
  }
  .input:focus {
    border-color: #D10024;
    box-shadow: 0 0 0 3px rgba(209,0,36,0.15);
    outline: none;
  }

  /* ปุ่มหลัก */
  .primary-btn {
    background-color: #D10024;
    color: #fff;
    border-radius: 40px;
    width: 100%;
    font-weight: 600;
    text-transform: uppercase;
    transition: 0.3s;
    padding: 12px;
    margin-top: 10px;
  }
  .primary-btn:hover {
    background-color: #a5001a;
  }

  .text-link {
    color: #D10024;
    text-decoration: none;
    font-weight: 500;
  }
  .text-link:hover {
    text-decoration: underline;
  }

  footer {
    background: #15161D;
    padding: 20px;
    color: #fff;
    text-align: center;
    margin-top: auto;
    font-size: 0.9rem;
  }

  .toast-container {
    z-index: 3000;
  }
  </style>
</head>
<body>

<!-- ✅ Header -->
<header>
  <div id="header" style="background:#15161D; padding:20px 0;">
    <div class="container text-center">
      <h3 style="color:#FFF; font-weight:700;">เข้าสู่ระบบ <span style="color:#D10024;">MyCommiss</span></h3>
    </div>
  </div>
</header>

<!-- ✅ Toast แจ้งเตือน -->
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

<!-- ✅ ฟอร์มเข้าสู่ระบบ -->
<div class="login-wrapper">
  <div class="login-title">
    <i class="fa fa-lock"></i> เข้าสู่ระบบ
  </div>

  <form method="post">
    <div class="mb-3">
      <input type="email" name="email" class="input w-100" placeholder="Email" required>
    </div><br>
    <div class="mb-3">
      <input type="password" name="password" class="input w-100" placeholder="Password" required>
    </div>

    <button class="primary-btn">เข้าสู่ระบบ</button>

    <div class="text-center mt-3">
      <p>ยังไม่มีบัญชี? <a href="register.php" class="text-link">สมัครสมาชิก</a></p>
      <a href="index.php" class="text-muted"><i class="fa fa-arrow-left"></i> กลับหน้าหลัก</a>
    </div>
  </form>
</div>

<!-- ✅ Footer -->
<footer>
  © <?= date('Y') ?> MyCommiss | เข้าสู่ระบบ
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
  // ✅ Toast auto-hide
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.toast').forEach(toastEl => {
      const toast = new bootstrap.Toast(toastEl, { delay: 4000, autohide: true });
      toast.show();
    });
  });
</script>
</body>
</html>

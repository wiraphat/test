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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #ffffff;
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* 🟥 Header */
    .navbar {
      background-color: #D10024;
      color: #fff;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: #fff !important;
      letter-spacing: 0.5px;
    }

    /* 🧾 Card Login */
    .login-card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      overflow: hidden;
    }
    .login-header {
      background-color: #D10024;
      color: #fff;
      font-weight: 600;
      text-align: center;
      padding: 1rem;
      font-size: 1.2rem;
    }
    .form-label {
      font-weight: 500;
      color: #333;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #ccc;
      transition: all 0.3s;
    }
    .form-control:focus {
      border-color: #D10024;
      box-shadow: 0 0 0 0.2rem rgba(209,0,36,0.25);
    }

    .btn-primary {
      background-color: #D10024;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    .btn-primary:hover {
      background-color: #a5001b;
    }

    .btn-secondary {
      background-color: #6c757d;
      border: none;
      border-radius: 10px;
    }

    a {
      color: #D10024;
      text-decoration: none;
      font-weight: 500;
    }
    a:hover {
      text-decoration: underline;
    }

    footer {
      background-color: #f8f9fa;
      border-top: 2px solid #D10024;
      color: #555;
      padding: 15px 0;
      text-align: center;
      font-size: 0.9rem;
    }

    /* Toast */
    .toast-container {
      z-index: 3000;
    }
  </style>
</head>
<body>

<!-- 🔻 Navbar -->
<nav class="navbar">
  <div class="container">
    <a class="navbar-brand" href="index.php">💻 MyCommiss</a>
  </div>
</nav>

<!-- ✅ Toast แจ้งเตือน -->
<div class="toast-container position-fixed top-0 end-0 p-3">
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

<!-- 🔐 กล่องเข้าสู่ระบบ -->
<div class="container my-5" style="max-width: 450px;">
  <div class="card login-card">
    <div class="login-header">เข้าสู่ระบบ</div>
    <div class="card-body p-4">
      <form method="post">
        <div class="mb-3">
          <label class="form-label">อีเมล</label>
          <input type="email" name="email" class="form-control" required placeholder="กรอกอีเมลของคุณ">
        </div>
        <div class="mb-3">
          <label class="form-label">รหัสผ่าน</label>
          <input type="password" name="password" class="form-control" required placeholder="กรอกรหัสผ่าน">
        </div>
        <div class="d-grid">
          <button class="btn btn-primary">🔓 เข้าสู่ระบบ</button>
        </div>
      </form>
    </div>
  </div>

  <div class="text-center mt-4">
    <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
    <a href="index.php" class="btn btn-secondary btn-sm mt-2">⬅️ กลับหน้าหลัก</a>
  </div>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | เข้าสู่ระบบ
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

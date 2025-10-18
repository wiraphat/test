<?php
include("connectdb.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $confirm = trim($_POST['confirm']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);

  // ✅ ตรวจสอบเบอร์โทรศัพท์ (เฉพาะตัวเลข 10 หลัก)
  if (!preg_match('/^[0-9]{10}$/', $phone)) {
    $_SESSION['toast_error'] = "⚠️ กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (เฉพาะตัวเลข 10 หลัก)";
    header("Location: register.php");
    exit;
  }

  // ✅ ตรวจสอบรหัสผ่านตรงกัน
  if ($password !== $confirm) {
    $_SESSION['toast_error'] = "❌ รหัสผ่านไม่ตรงกัน";
    header("Location: register.php");
    exit;
  }

  // ✅ ตรวจสอบอีเมลซ้ำ
  $check = $conn->prepare("SELECT * FROM customers WHERE email = ?");
  $check->execute([$email]);
  if ($check->rowCount() > 0) {
    $_SESSION['toast_error'] = "⚠️ อีเมลนี้ถูกใช้ไปแล้ว";
    header("Location: register.php");
    exit;
  }

  // ✅ เข้ารหัสรหัสผ่าน
  $hashed = password_hash($password, PASSWORD_DEFAULT);

  // ✅ บันทึกข้อมูลลงฐานข้อมูล
  $stmt = $conn->prepare("
    INSERT INTO customers (name, email, password, phone, address)
    VALUES (:name, :email, :password, :phone, :address)
  ");
  $stmt->execute([
    ':name' => $name,
    ':email' => $email,
    ':password' => $hashed,
    ':phone' => $phone,
    ':address' => $address
  ]);

  // ✅ Toast สำเร็จ
  $_SESSION['toast_success'] = "✅ สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สมัครสมาชิก | MyCommiss</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #F6F7F8;
      font-family: 'Montserrat', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .register-wrapper {
      background: #FFF;
      border-radius: 20px;
      border: 1px solid #E4E7ED;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
      padding: 50px 60px;
      width: 520px;
      transition: all 0.3s ease;
    }

    .register-wrapper:hover {
      transform: translateY(-3px);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .register-title {
      color: #2B2D42;
      font-weight: 700;
      font-size: 1.5rem;
      text-align: center;
      margin-bottom: 25px;
    }

    .register-title i {
      color: #D10024;
      margin-right: 8px;
    }

    .form-control {
      border-radius: 10px;
      padding: 10px;
    }

    .btn-danger {
      background-color: #D10024;
      border: none;
      font-weight: 600;
      padding: 10px;
      border-radius: 8px;
    }

    .btn-danger:hover {
      background-color: #b0001f;
    }

    .text-secondary {
      color: #6c757d !important;
    }

    footer {
      background-color: #1E1F29;
      color: #fff;
      text-align: center;
      padding: 10px 0;
      font-size: 14px;
      width: 100%;
      margin-top: 40px;
    }

    /* Toast */
    .toast-container {
      z-index: 3000;
    }
  </style>
</head>
<body>

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

<!-- ✅ กล่องสมัครสมาชิก -->
<div class="register-wrapper">
  <form method="post">
    <h3 class="register-title"><i class="fa fa-user-plus"></i> สมัครสมาชิก</h3>

    <div class="mb-3">
      <label class="form-label">ชื่อ-นามสกุล</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">อีเมล</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">รหัสผ่าน</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">ยืนยันรหัสผ่าน</label>
        <input type="password" name="confirm" class="form-control" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">เบอร์โทรศัพท์</label>
      <input type="text" name="phone" class="form-control" maxlength="10"
             pattern="^[0-9]{10}$" title="กรุณากรอกเฉพาะตัวเลข 10 หลัก" required
             oninput="this.value=this.value.replace(/[^0-9]/g,'');">
    </div>

    <div class="mb-3">
      <label class="form-label">ที่อยู่</label>
      <textarea name="address" class="form-control" rows="3"></textarea>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-danger">สมัครสมาชิก</button>
    </div>

    <div class="text-center mt-3">
      <a href="login.php" class="text-decoration-none">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a><br>
      <a href="index.php" class="text-secondary small"><i class="fa fa-arrow-left"></i> กลับหน้าหลัก</a>
    </div>
  </form>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | สมัครสมาชิก
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

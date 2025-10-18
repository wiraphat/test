<?php
session_start();
include("connectdb.php");

// 🔒 ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

$customer_id = $_SESSION['customer_id'];

// ✅ ดึงข้อมูลผู้ใช้
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  die("<p class='text-center text-danger mt-5'>❌ ไม่พบข้อมูลผู้ใช้</p>");
}

// ✅ เมื่อกดบันทึกข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);

  if (!preg_match('/^[0-9]{10}$/', $phone)) {
    $_SESSION['toast_error'] = "❌ กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (10 หลัก)";
    header("Location: profile.php");
    exit;
  } else {
    $stmt = $conn->prepare("UPDATE customers 
                            SET name = ?, email = ?, phone = ?, address = ? 
                            WHERE customer_id = ?");
    $stmt->execute([$name, $email, $phone, $address, $customer_id]);

    $_SESSION['customer_name'] = $name;
    $_SESSION['toast_success'] = "✅ บันทึกข้อมูลเรียบร้อยแล้ว";
    header("Location: profile.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>โปรไฟล์ของฉัน | MyCommiss</title>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #0F111A;
      font-family: 'Prompt', sans-serif;
      color: #FFF;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* 🔹 กล่องโปรไฟล์ */
    .profile-card {
      max-width: 720px;
      margin: 60px auto;
      background: #1E1F29;
      border-radius: 20px;
      border: 1px solid #D10024;
      box-shadow: 0 0 15px rgba(209, 0, 36, 0.4);
      transition: 0.3s;
    }

    .profile-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 30px rgba(209, 0, 36, 0.7);
    }

    .card-header {
      background: #D10024;
      color: #FFF;
      font-weight: 600;
      font-size: 1.3rem;
      border-radius: 20px 20px 0 0;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }

    /* 🔹 ฟอร์ม */
    .form-label {
      color: #E4E7ED;
      font-weight: 500;
    }

    .form-control, textarea {
      background-color: #2B2D42;
      color: #FFF;
      border: 1px solid #444;
      border-radius: 10px;
    }

    .form-control:focus {
      border-color: #D10024;
      box-shadow: 0 0 0 2px rgba(209,0,36,0.3);
    }

    /* 🔹 ปุ่ม */
    .btn-primary {
      background-color: #D10024;
      border: none;
      border-radius: 10px;
      font-weight: 600;
    }
    .btn-primary:hover { background-color: #a7001c; }

    .btn-success {
      background-color: #00B894;
      border: none;
      border-radius: 10px;
      font-weight: 600;
    }
    .btn-success:hover { background-color: #009774; }

    .btn-secondary {
      background-color: #2B2D42;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      color: #FFF;
    }
    .btn-secondary:hover { background-color: #1E1F29; }

    /* 🔹 Toast */
    .toast-container { z-index: 3000; }

    /* 🔹 Footer */
    footer {
      background-color: #1E1F29;
      color: #FFF;
      text-align: center;
      padding: 15px 0;
      font-size: 14px;
      margin-top: auto;
      border-top: 2px solid #D10024;
    }
  </style>
</head>
<body>

<?php include("navbar_user.php"); ?>

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

  <?php if (isset($_SESSION['toast_success'])): ?>
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body"><?= $_SESSION['toast_success'] ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
    <?php unset($_SESSION['toast_success']); ?>
  <?php endif; ?>
</div>

<!-- ✅ กล่องโปรไฟล์ -->
<div class="container">
  <div class="profile-card">
    <div class="card-header text-center py-3">
      <i class="fa fa-user-circle me-2"></i> โปรไฟล์ของฉัน
    </div>
    <div class="card-body p-4">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">ชื่อ - นามสกุล</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">อีเมล</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">เบอร์โทรศัพท์</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" 
                 class="form-control" maxlength="10" pattern="[0-9]{10}" 
                 oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10);" required>
        </div>

        <div class="mb-3">
          <label class="form-label">ที่อยู่จัดส่ง</label>
          <textarea name="address" rows="3" class="form-control"><?= htmlspecialchars($user['address']) ?></textarea>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-3 mt-4 flex-wrap">
          <a href="index.php" class="btn btn-secondary px-4"><i class="fa fa-home me-1"></i> กลับหน้าหลัก</a>
          <a href="change_password.php" class="btn btn-success px-4"><i class="fa fa-key me-1"></i> เปลี่ยนรหัสผ่าน</a>
          <button type="submit" class="btn btn-primary px-4"><i class="fa fa-save me-1"></i> บันทึกข้อมูล</button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | โปรไฟล์ผู้ใช้
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

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

  // ✅ ตรวจสอบเบอร์โทรศัพท์
  if (!preg_match('/^[0-9]{10}$/', $phone)) {
    $_SESSION['toast_error'] = "❌ กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (10 หลัก)";
    header("Location: profile.php");
    exit;
  } else {
    // ✅ อัปเดตข้อมูล
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #F6F7F8;
      font-family: 'Montserrat', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .profile-card {
      max-width: 700px;
      margin: 60px auto;
      background: #FFF;
      border-radius: 20px;
      border: 1px solid #E4E7ED;
      box-shadow: 0 15px 30px rgba(0,0,0,0.08);
      transition: 0.3s;
    }

    .profile-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .card-header {
      background: #1E1F29;
      color: #FFF;
      font-weight: 600;
      font-size: 1.3rem;
      border-radius: 20px 20px 0 0;
    }

    .btn-primary {
      background-color: #2575FC;
      border: none;
      font-weight: 600;
      border-radius: 10px;
    }

    .btn-success {
      background-color: #00B894;
      border: none;
      border-radius: 10px;
      font-weight: 600;
    }

    .btn-secondary {
      background-color: #6C757D;
      border: none;
      border-radius: 10px;
      font-weight: 600;
    }

    .btn-primary:hover { background-color: #1a5fd9; }
    .btn-success:hover { background-color: #009774; }
    .btn-secondary:hover { background-color: #555; }

    footer {
      background-color: #1E1F29;
      color: #FFF;
      text-align: center;
      padding: 12px 0;
      font-size: 14px;
      margin-top: auto;
    }

    .toast-container {
      z-index: 3000;
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
          <label class="form-label fw-semibold">ชื่อ - นามสกุล</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control rounded-3" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">อีเมล</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control rounded-3" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">เบอร์โทรศัพท์</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"
                 class="form-control rounded-3" maxlength="10" pattern="[0-9]{10}"
                 oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10);" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">ที่อยู่จัดส่ง</label>
          <textarea name="address" rows="3" class="form-control rounded-3"><?= htmlspecialchars($user['address']) ?></textarea>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-3 mt-4 flex-wrap">
          <a href="index.php" class="btn btn-secondary px-4">⬅️ กลับหน้าหลัก</a>
          <a href="change_password.php" class="btn btn-success px-4">🔑 เปลี่ยนรหัสผ่าน</a>
          <button type="submit" class="btn btn-primary px-4">💾 บันทึกข้อมูล</button>
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
      const toast = new bootstrap.Toast(toastEl, { delay: 5000, autohide: true });
      toast.show();
    });
  });
</script>

</body>
</html>

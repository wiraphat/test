<?php
session_start();
include("connectdb.php");

// ✅ ตรวจสอบการส่งข้อมูล
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id'])) {
  header("Location: index.php");
  exit;
}

$productId = intval($_POST['id']);
$qty = intval($_POST['qty'] ?? 1);

// ✅ ถ้ายังไม่ล็อกอิน → ให้ไปหน้า login
if (!isset($_SESSION['customer_id'])) {
  $_SESSION['toast_error'] = "⚠️ กรุณาเข้าสู่ระบบก่อนสั่งซื้อสินค้า";
  header("Location: login.php");
  exit;
}

// ✅ ดึงข้อมูลสินค้า
$stmt = $conn->prepare("SELECT * FROM product WHERE p_id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  $_SESSION['toast_error'] = "❌ ไม่พบสินค้านี้";
  header("Location: index.php");
  exit;
}

// ✅ ตรวจสอบว่าสินค้าหมดหรือไม่
if ($product['p_stock'] <= 0) {
  $_SESSION['toast_error'] = "❌ สินค้านี้หมดสต็อกแล้ว";
  header("Location: index.php");
  exit;
}

// ✅ เพิ่มสินค้าลงตะกร้าใน session
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$productId])) {
  $_SESSION['cart'][$productId]['qty'] += $qty;
} else {
  $_SESSION['cart'][$productId] = [
    'id' => $product['p_id'],
    'name' => $product['p_name'],
    'price' => $product['p_price'],
    'image' => $product['p_image'],
    'qty' => $qty
  ];
}

// ✅ ตั้งข้อความ Toast
$_SESSION['toast_success'] = htmlspecialchars($product['p_name']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มสินค้าในตะกร้า | MyCommiss</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <style>
    body {
      background: #f6f6f6;
      font-family: 'Montserrat', sans-serif;
      text-align: center;
      padding-top: 120px;
    }
    .toast-box {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #D10024;
      color: #fff;
      font-weight: 600;
      padding: 20px 25px;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      gap: 10px;
      animation: fadeInUp 0.6s ease;
    }
    @keyframes fadeInUp {
      0% { transform: translateY(20px); opacity: 0; }
      100% { transform: translateY(0); opacity: 1; }
    }
    .btn {
      margin-top: 40px;
      background: #D10024;
      color: #fff;
      padding: 12px 25px;
      border-radius: 6px;
      border: none;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.2s;
    }
    .btn:hover {
      background: #a7001c;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

  <?php if (isset($_SESSION['toast_success'])): ?>
  <div class="toast-box" id="toast">
    <i class="fa fa-check-circle fa-lg"></i>
    <span>✅ เพิ่มสินค้า <b><?= $_SESSION['toast_success'] ?></b> ในตะกร้าเรียบร้อยแล้ว!</span>
  </div>
  <script>
    setTimeout(() => {
      const t = document.getElementById("toast");
      if (t) {
        t.style.transition = "0.5s";
        t.style.opacity = "0";
        setTimeout(() => t.remove(), 800);
      }
    }, 3000);
  </script>
  <?php unset($_SESSION['toast_success']); endif; ?>

  <h2>✅ เพิ่มสินค้าลงในตะกร้าเรียบร้อยแล้ว</h2>
  <p style="font-size:18px;color:#555;">เลือกสิ่งที่คุณต้องการทำต่อไป</p>

  <a href="cart.php"><button class="btn"><i class="fa fa-shopping-cart"></i> ไปยังตะกร้าสินค้า</button></a>
  <a href="index.php"><button class="btn"><i class="fa fa-home"></i> กลับหน้าหลัก</button></a>

</body>
</html>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("connectdb.php");

// ✅ ต้องเข้าสู่ระบบก่อน
if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

$customer_id = $_SESSION['customer_id'];

// ✅ ตรวจสอบว่ามี id คำสั่งซื้อหรือไม่
if (!isset($_GET['id'])) {
  die("<p class='text-center mt-5 text-danger'>❌ ไม่พบรหัสคำสั่งซื้อ</p>");
}

$order_id = intval($_GET['id']);

// ✅ ดึงข้อมูลคำสั่งซื้อของลูกค้าคนนั้น
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$order_id, $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  die("<p class='text-center mt-5 text-danger'>❌ ไม่พบคำสั่งซื้อนี้ หรือคุณไม่มีสิทธิ์ดู</p>");
}

/* ✅ ฟังก์ชันสร้าง QR พร้อมเพย์ (EMVCo มาตรฐาน) */
function generatePromptPayPayload($promptPayID, $amount = 0.00) {
  $id = preg_replace('/[^0-9]/', '', $promptPayID);
  if (strlen($id) == 10) $id = '0066' . substr($id, 1);
  $data = [
    '00' => '01', '01' => '11',
    '29' => formatField('00', 'A000000677010111') . formatField('01', $id),
    '53' => '764',
    '54' => sprintf('%0.2f', $amount),
    '58' => 'TH',
  ];
  $payload = '';
  foreach ($data as $id => $val) $payload .= $id . sprintf('%02d', strlen($val)) . $val;
  $payload .= '6304';
  return $payload . strtoupper(crc16($payload));
}
function formatField($id, $value) { return $id . sprintf('%02d', strlen($value)) . $value; }
function crc16($data) {
  $crc = 0xFFFF;
  for ($i = 0; $i < strlen($data); $i++) {
    $crc ^= ord($data[$i]) << 8;
    for ($j = 0; $j < 8; $j++) {
      if ($crc & 0x8000) $crc = ($crc << 1) ^ 0x1021;
      else $crc <<= 1;
      $crc &= 0xFFFF;
    }
  }
  return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
}

/* ✅ ยืนยันการชำระเงิน */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $uploadDir = dirname(__DIR__) . "/admin/uploads/slips/";
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
  if (!is_writable($uploadDir)) die("<p class='text-danger text-center mt-5'>❌ ไม่มีสิทธิ์เขียนไฟล์ใน: $uploadDir</p>");

  $fileName = "";
  if (!empty($_FILES['slip']['name'])) {
    $ext = pathinfo($_FILES['slip']['name'], PATHINFO_EXTENSION);
    $fileName = "slip_" . time() . "_" . rand(1000,9999) . "." . $ext;
    $targetFile = $uploadDir . $fileName;
    if (!move_uploaded_file($_FILES['slip']['tmp_name'], $targetFile)) {
      die("<p class='text-danger text-center mt-5'>❌ ไม่สามารถอัปโหลดไฟล์ได้</p>");
    }
  }

  $stmt = $conn->prepare("UPDATE orders 
                          SET payment_status = 'รอดำเนินการ',
                              admin_verified = 'กำลังตรวจสอบ',
                              slip_image = :slip,
                              payment_date = NOW()
                          WHERE order_id = :oid AND customer_id = :cid");
  $stmt->execute([':slip' => $fileName, ':oid' => $order_id, ':cid' => $customer_id]);

  echo "<script>alert('✅ แจ้งชำระเงินเรียบร้อยแล้ว! ระบบจะตรวจสอบโดยแอดมิน'); window.location='order_detail.php?id=$order_id';</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แจ้งชำระเงิน | MyCommiss</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
  <style>
    body { background-color: #f6f6f6; font-family: 'Montserrat', sans-serif; }
    .navbar { background-color: #2B2D42 !important; }
    .navbar a.nav-link { color: #fff !important; font-weight: 500; }
    .navbar a.nav-link:hover { color: #D10024 !important; }
    .card-header { background-color: #D10024; color: #fff; font-weight: bold; }
    .btn-primary { background: #D10024; border: none; }
    .btn-primary:hover { background: #a7001c; }
    .btn-success { background-color: #28a745; border: none; }
    footer { background: #2B2D42; color: #fff; padding: 15px 0; margin-top: 50px; }
  </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="index.php">
      <i class="fa fa-shopping-bag"></i> MyCommiss
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">หน้าหลัก</a></li>
        <li class="nav-item"><a class="nav-link" href="order_history.php">คำสั่งซื้อ</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">แจ้งชำระเงิน</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
  <div class="card shadow border-0 mx-auto" style="max-width:600px;">
    <div class="card-header text-center">
      💰 แจ้งชำระเงินคำสั่งซื้อ #<?= $order_id ?>
    </div>
    <div class="card-body text-center">
      <p><strong>วิธีชำระ:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>

      <?php if ($order['payment_method'] === 'QR'): ?>
        <?php
          $shopPromptPay = "0903262100";
          $payload = generatePromptPayPayload($shopPromptPay, $order['total_price']);
        ?>
        <div class="my-4">
          <h5 class="fw-bold text-danger">📱 สแกน QR พร้อมเพย์ เพื่อชำระเงิน</h5>
          <div id="qrcode" class="border p-3 rounded bg-white d-inline-block"></div>
          <p class="mt-3 text-muted">
            💵 ยอดชำระ <?= number_format($order['total_price'], 2) ?> บาท<br>
            ☎️ พร้อมเพย์: <?= htmlspecialchars($shopPromptPay) ?>
          </p>
        </div>
        <script>
          new QRCode(document.getElementById("qrcode"), { text: "<?= $payload ?>", width: 200, height: 200 });
        </script>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" class="mt-4 text-start">
        <label for="slip" class="form-label fw-semibold">📎 แนบสลิปการชำระเงิน (ถ้ามี)</label>
        <input type="file" name="slip" id="slip" class="form-control" accept="image/*">
        <small class="text-muted">* หากไม่มีสลิป สามารถกดยืนยันได้ ระบบจะรอตรวจสอบโดยแอดมิน</small>

        <div class="d-grid gap-2 mt-4">
          <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> ยืนยันการชำระเงิน</button>
          <a href="order_history.php" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> กลับหน้าคำสั่งซื้อ</a>
          <a href="order_detail.php?id=<?= $order_id ?>" class="btn btn-outline-primary"><i class="fa fa-search"></i> ดูรายละเอียด</a>
        </div>
      </form>
    </div>
  </div>
</div>

<footer>
  © <?= date('Y') ?> MyCommiss | แจ้งชำระเงิน
</footer>

</body>
</html>

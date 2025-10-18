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
</head>
<body>

<!-- 🔹 Header ของธีม -->
<header>
  <div id="header">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <h2 style="color:white; margin:20px 0;">เข้าสู่ระบบ MyCommiss</h2>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- 🔹 Section ฟอร์มล็อกอิน -->
<div class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="order-summary" style="padding:30px; background:#fff;">
          <h4 class="section-title text-center">🔒 เข้าสู่ระบบ</h4>
          <form method="post" action="login_process.php">
            <div class="form-group mb-3">
              <input class="input" type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group mb-3">
              <input class="input" type="password" name="password" placeholder="Password" required>
            </div>
            <div class="text-center mt-4">
              <button class="primary-btn w-100">เข้าสู่ระบบ</button>
            </div>
          </form>

          <div class="text-center mt-4">
            <p>ยังไม่มีบัญชี? <a href="register.php" style="color:#D10024;">สมัครสมาชิก</a></p>
            <a href="index.php" class="text-muted"><i class="fa fa-arrow-left"></i> กลับหน้าหลัก</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 🔹 Footer -->
<footer id="footer">
  <div class="section">
    <div class="container text-center">
      <p style="color:#fff;">© <?= date('Y') ?> MyCommiss | เข้าสู่ระบบ</p>
    </div>
  </div>
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

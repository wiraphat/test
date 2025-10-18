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
    min-height: 100vh;              /* ✅ ทำให้สูงเท่าจอ */
    display: flex;
    flex-direction: column;         /* ✅ จัดแนวตั้ง */
  }

  /* กล่อง login */
  .login-wrapper {
  background: #fff;
  border: 1px solid #E4E7ED;
  border-radius: 20px; /* มุมโค้งนุ่มขึ้น */
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); /* เงานุ่มธรรมชาติ */
  padding: 50px 60px; /* เพิ่มความโปร่ง */
  max-width: 520px; /* ✅ กว้างขึ้นเล็กน้อย */
  min-height: 450px; /* ✅ เพิ่มความสูงให้สมดุลกับฟอร์ม */
  margin: 60px auto;
  display: flex;
  flex-direction: column;
  justify-content: center; /* จัดให้อยู่ตรงกลางในกล่อง */
  transition: all 0.3s ease;
}

.login-wrapper:hover {
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
  transform: translateY(-4px);
}
<h3 class="login-title"><i class="fa fa-lock"></i> เข้าสู่ระบบ</h3>
.login-title {
  color: #2B2D42;
  font-weight: 700;
  font-size: 1.5rem;
  text-align: center;
  margin-bottom: 25px;
}

.login-title i {
  color: #D10024;
  margin-right: 6px;
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

  /* ✅ Footer ติดขอบล่าง */
  footer {
    background: #15161D;
    padding: 20px;
    color: #fff;
    text-align: center;
    margin-top: auto;               /* ✅ บังคับชิดขอบล่าง */
    font-size: 0.9rem;
  }
</style>

</head>
<body>

<!-- 🔹 Header -->
<header>
  <div id="header" style="background:#15161D; padding:20px 0;">
    <div class="container text-center">
      <h3 style="color:#FFF; font-weight:700;">เข้าสู่ระบบ <span style="color:#D10024;">MyCommiss</span></h3>
    </div>
  </div>
</header>

<!-- 🔹 Login Form -->
<div class="login-wrapper">
  <div class="login-title">
    <i class="fa fa-lock"></i> เข้าสู่ระบบ
  </div>

  <form method="post" action="index.php">
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

<!-- 🔹 Footer -->
<footer>
  © <?= date('Y') ?> MyCommiss | เข้าสู่ระบบ
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

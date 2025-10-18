<?php
// ✅ ตรวจสอบ Session (ป้องกันการเรียกซ้ำ)
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      🖥 MyCommiss
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            🏠 หน้าร้าน
          </a>
        </li>

        <li class="nav-item">
          <a href="cart.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">
            🛒 ตะกร้า
          </a>
        </li>

        <?php if (isset($_SESSION['customer_id'])): ?>
          <li class="nav-item">
            <a href="orders.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">
              📦 คำสั่งซื้อของฉัน
            </a>
          </li>

          <!-- 🔹 ชื่อผู้ใช้ (กดเพื่อไปหน้าโปรไฟล์) -->
          <li class="nav-item">
            <a href="profile.php" 
               class="nav-link fw-semibold user-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
              👤 <?= htmlspecialchars($_SESSION['customer_name']) ?>
            </a>
          </li>
          
          <!-- 🔹 ปุ่มออกจากระบบ พร้อมยืนยัน -->
          <li class="nav-item">
            <a href="#" class="nav-link text-danger fw-semibold" onclick="confirmLogout(event)">
              🚪 ออกจากระบบ
            </a>
          </li>

        <?php else: ?>
          <!-- 🔹 ยังไม่ล็อกอิน -->
          <li class="nav-item">
            <a href="login.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : '' ?>">
              🔑 เข้าสู่ระบบ
            </a>
          </li>
          <li class="nav-item">
            <a href="register.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : '' ?>">
              📝 สมัครสมาชิก
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ Confirm Logout -->
<script>
function confirmLogout(e) {
  e.preventDefault();
  if (confirm("คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?")) {
    window.location = "logout.php";
  }
}
</script>

<style>
/* 💡 ปรับสีชื่อผู้ใช้ให้เด่น */
.user-link {
  color: #0dcaf0 !important;
  transition: 0.2s ease;
  text-decoration: none !important;
}
.user-link:hover {
  color: #31d2f2 !important;
  text-decoration: none !important;
}
/* 💡 ถ้าอยู่หน้า profile ให้เป็นฟ้าเข้มกว่าปกติ */
.user-link.active {
  color: #58d6f7 !important;
  text-shadow: 0 0 6px rgba(13, 202, 240, 0.6);
  text-decoration: none !important;
}
</style>

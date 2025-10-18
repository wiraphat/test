<?php
session_start();
include "connectdb.php";

// รับค่าค้นหา / หมวดหมู่
$search = $_GET['search'] ?? '';
$cat_id = $_GET['cat_id'] ?? '';

// ดึงหมวดหมู่
$cats = $conn->query("SELECT * FROM category ORDER BY cat_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ดึงสินค้า
$sql = "SELECT p.*, c.cat_name 
        FROM product p 
        LEFT JOIN category c ON p.cat_id = c.cat_id 
        WHERE 1";

if (!empty($search)) $sql .= " AND p.p_name LIKE :search";
if (!empty($cat_id)) $sql .= " AND p.cat_id = :cat_id";

$stmt = $conn->prepare($sql);
if (!empty($search)) $stmt->bindValue(':search', "%$search%");
if (!empty($cat_id)) $stmt->bindValue(':cat_id', $cat_id);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>MyCommiss | หน้าร้าน</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/slick.css">
  <link rel="stylesheet" href="css/slick-theme.css">
  <link rel="stylesheet" href="css/nouislider.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
    /* ✅ ช่องค้นหา */
    .header-search {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .header-search .input-select {
      border-radius: 30px 0 0 30px !important;
      border: 1px solid #E4E7ED;
      border-right: none;
      padding: 10px 15px;
      height: 42px;
      background: #fff;
    }
    .header-search .input {
      border: 1px solid #E4E7ED;
      border-left: none;
      border-right: none;
      padding: 10px 15px;
      height: 42px;
      flex: 1;
      border-radius: 0;
    }
    .header-search .search-btn {
      border-radius: 0 30px 30px 0 !important;
      background-color: #D10024;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-weight: 600;
    }
    .header-search .search-btn:hover {
      background-color: #a7001c;
    }

    /* ✅ header alignment */
    #header .row {
      align-items: center;
    }
    .header-ctn {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 30px;
    }
    .header-ctn a {
      color: #fff;
      text-decoration: none;
      text-align: center;
    }
    .header-ctn a:hover span {
      color: #D10024;
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header>
    <!-- TOP HEADER -->
    <div id="top-header">
      <div class="container">
        <ul class="header-links pull-left">
          <li><a href="#"><i class="fa fa-phone"></i> +66 81-234-5678</a></li>
          <li><a href="#"><i class="fa fa-envelope-o"></i> support@mycommiss.com</a></li>
          <li><a href="#"><i class="fa fa-map-marker"></i> Bangkok, Thailand</a></li>
        </ul>
        <ul class="header-links pull-right">
          <?php if (isset($_SESSION['customer_id'])): ?>
            <li><a href="profile.php"><i class="fa fa-user-o"></i> บัญชีของฉัน</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> ออกจากระบบ</a></li>
          <?php else: ?>
            <li><a href="login.php"><i class="fa fa-sign-in"></i> เข้าสู่ระบบ</a></li>
            <li><a href="register.php"><i class="fa fa-user-plus"></i> สมัครสมาชิก</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <!-- /TOP HEADER -->

    <!-- MAIN HEADER -->
    <div id="header">
      <div class="container">
        <div class="row align-items-center">
          <!-- 🟥 โลโก้ -->
          <div class="col-md-3">
            <div class="hea

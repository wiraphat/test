<?php
include __DIR__ . "/../partials/connectdb.php"; // ✅ ปรับ path ให้ถูกต้อง

$id = $_GET['id'] ?? null;
if (!$id) {
  die("<script>alert('ไม่พบสินค้า!');history.back();</script>");
}

$stmt = $conn->prepare("DELETE FROM product WHERE p_id = ?");
$stmt->execute([$id]);

header("Location: products.php");
exit;
?>

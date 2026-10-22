<?php
// =====================================================
// comment_add.php : เพิ่มความเห็นใต้ประกาศ (ฟีเจอร์เสริม)
// =====================================================
require 'config.php';
require_login();

// 1. รับค่าจากฟอร์ม
$listing_id = (int)($_POST['listing_id'] ?? 0);
$body       = trim($_POST['body'] ?? '');

// 2. ถ้าไม่ว่างเปล่า ให้บันทึกลงตาราง comments
if ($body !== '') {
    $stmt = $pdo->prepare("INSERT INTO comments (listing_id, user_id, body) VALUES (?, ?, ?)");
    $stmt->execute([$listing_id, $_SESSION['user']['id'], $body]);
}

// 3. กลับไปหน้าประกาศเดิม
header('Location: listing_view.php?id=' . $listing_id);
exit;

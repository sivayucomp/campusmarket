<?php
// =====================================================
// comment_delete.php : ลบความเห็น
// เจ้าของความเห็นลบของตัวเองได้ / แอดมินลบได้ทุกความเห็น
// =====================================================
require 'config.php';
require_login();

// 1. รับ id ความเห็น และ id ประกาศ (ไว้ใช้กลับหน้าเดิม)
$id         = (int)($_POST['id'] ?? 0);
$listing_id = (int)($_POST['listing_id'] ?? 0);

// 2. แอดมินลบได้ทุกอัน / สมาชิกลบได้เฉพาะของตัวเอง
if (is_admin()) {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user']['id']]);
}

// 3. กลับไปหน้าประกาศเดิม
header('Location: listing_view.php?id=' . $listing_id);
exit;

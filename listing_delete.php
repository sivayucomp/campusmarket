<?php
// =====================================================
// listing_delete.php : ลบประกาศ (Delete)
// เจ้าของลบของตัวเองได้ / แอดมินลบได้ทุกประกาศ
// =====================================================
require 'config.php';
require_login();

// 1. รับ id ประกาศ (ส่งมาแบบ POST จากปุ่มลบ)
$id = (int)($_POST['id'] ?? 0);

// 2. ดึงประกาศมาตรวจว่าใครเป็นเจ้าของ
$stmt = $pdo->prepare("SELECT user_id FROM listings WHERE id = ?");
$stmt->execute([$id]);
$l = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. ตรวจสิทธิ์: เจ้าของ หรือ แอดมิน
// [แก้ได้] ถ้าอาจารย์ให้ปิดสิทธิ์แอดมิน ให้ลบ "|| is_admin()" ออก
if ($l && ($l['user_id'] == $_SESSION['user']['id'] || is_admin())) {

    // 3.1 ลบประกาศ (ความเห็นที่ผูกอยู่จะถูกลบตามอัตโนมัติ เพราะตั้ง ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM listings WHERE id = ?");
    $stmt->execute([$id]);

    flash('ลบประกาศแล้ว');
} else {
    // 3.2 ไม่มีสิทธิ์
    flash('คุณไม่มีสิทธิ์ลบประกาศนี้', 'danger');
}

// 4. กลับหน้าแรก
header('Location: index.php');
exit;

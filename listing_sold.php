<?php
// =====================================================
// listing_sold.php : สลับสถานะสินค้า "ขายอยู่" <-> "ขายแล้ว"
// เฉพาะเจ้าของประกาศ
// =====================================================
require 'config.php';
require_login();

// 1. รับ id ประกาศ
$id = (int)($_POST['id'] ?? 0);

// 2. สลับสถานะ: ถ้าเป็น selling -> sold, ถ้าเป็น sold -> selling
// 2.1 เงื่อนไข user_id ทำให้แก้ได้เฉพาะประกาศของตัวเอง
$stmt = $pdo->prepare(
    "UPDATE listings
     SET status = IF(status = 'selling', 'sold', 'selling')
     WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user']['id']]);

// 3. กลับไปหน้ารายละเอียด
flash('อัปเดตสถานะสินค้าแล้ว');
header('Location: listing_view.php?id=' . $id);
exit;

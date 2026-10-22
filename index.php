<?php
// =====================================================
// index.php : หน้าแรก แสดงรายการประกาศ (Read) + ค้นหา + กรองหมวดหมู่
// ทุกคนเข้าได้ (ผู้เยี่ยมชม / สมาชิก / แอดมิน)
// =====================================================
require 'config.php';

// 1. รับค่าค้นหาจาก URL เช่น index.php?q=เครื่องคิดเลข&cat=หนังสือเรียน
$q    = trim($_GET['q'] ?? '');
$cat  = $_GET['cat'] ?? '';
$mine = isset($_GET['mine']) && is_login();   // ?mine=1 = เฉพาะประกาศของฉัน

// 2. สร้างคำสั่ง SQL ดึงประกาศ + ชื่อผู้ขาย
$sql    = "SELECT l.*, u.name AS seller
           FROM listings l JOIN users u ON l.user_id = u.id
           WHERE 1=1";
$params = [];

// 2.1 ค้นหาจากชื่อสินค้า
if ($q !== '') {
    $sql .= " AND l.title LIKE ?";
    $params[] = "%$q%";
}
// 2.2 กรองตามหมวดหมู่
if ($cat !== '') {
    $sql .= " AND l.category = ?";
    $params[] = $cat;
}
// 2.3 เฉพาะประกาศของฉัน
if ($mine) {
    $sql .= " AND l.user_id = ?";
    $params[] = $_SESSION['user']['id'];
}
// 2.4 เรียงให้ "ขายอยู่" มาก่อน "ขายแล้ว" แล้วเรียงใหม่สุดก่อน
// [แก้ได้] เปลี่ยน DESC เป็น ASC ถ้าอยากให้เก่าสุดขึ้นก่อน
$sql .= " ORDER BY l.status ASC, l.created_at DESC";

// 3. ส่งคำสั่งไปถามฐานข้อมูล
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. ส่วนแสดงหน้าเว็บ
$page_title = 'บอร์ดประกาศ';
include 'header.php';
?>

<!-- 4.1 หัวข้อ + ช่องค้นหา -->
<div class="board-head">
  <h1><?= $mine ? 'ประกาศของฉัน' : 'บอร์ดซื้อขายของมือสอง' ?></h1>
  <p>ของมือสองในรั้วมหาวิทยาลัย หาง่าย รู้ว่าขายแล้วหรือยัง</p>

  <form class="row g-2 justify-content-center" method="get">
    <div class="col-12 col-md-5">
      <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="ค้นหาชื่อสินค้า เช่น เครื่องคิดเลข">
    </div>
    <div class="col-8 col-md-3">
      <select class="form-select" name="cat">
        <option value="">ทุกหมวดหมู่</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= e($c) ?>" <?= $cat === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-4 col-md-2">
      <button class="btn btn-pin w-100">ค้นหา</button>
    </div>
  </form>
</div>

<!-- 4.2 การ์ดประกาศ (หน้าตาเหมือนโน้ตติดบอร์ด) -->
<?php if (count($listings) === 0): ?>
  <div class="paper-card text-center">ยังไม่พบประกาศที่ค้นหา 🔍</div>
<?php endif; ?>

<div class="row g-4">
  <?php foreach ($listings as $l): ?>
    <div class="col-12 col-sm-6 col-lg-4">
      <!-- 4.3 สีโน้ตเปลี่ยนตามเลข id (note-0 ถึง note-3) -->
      <a href="listing_view.php?id=<?= $l['id'] ?>" class="note note-<?= $l['id'] % 4 ?> <?= $l['status'] === 'sold' ? 'is-sold' : '' ?>">
        <span class="pin"></span>
        <?php if ($l['status'] === 'sold'): ?>
          <span class="stamp">ขายแล้ว</span>
        <?php endif; ?>
        <span class="note-cat"><?= e($l['category']) ?></span>
        <h3><?= e($l['title']) ?></h3>
        <div class="note-price"><?= number_format($l['price']) ?> ฿</div>
        <div class="note-meta">โดย <?= e($l['seller']) ?> · <?= date('d/m/Y', strtotime($l['created_at'])) ?></div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>

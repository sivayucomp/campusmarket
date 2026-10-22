<?php
// =====================================================
// listing_view.php : หน้ารายละเอียดสินค้า (Read) + ความเห็น
// =====================================================
require 'config.php';

// 1. รับ id ประกาศจาก URL เช่น listing_view.php?id=3
$id = (int)($_GET['id'] ?? 0);

// 2. ดึงข้อมูลประกาศ + ข้อมูลผู้ขาย
$stmt = $pdo->prepare(
    "SELECT l.*, u.name AS seller, u.contact
     FROM listings l JOIN users u ON l.user_id = u.id
     WHERE l.id = ?");
$stmt->execute([$id]);
$l = $stmt->fetch(PDO::FETCH_ASSOC);

// 2.1 ถ้าไม่เจอประกาศ กลับหน้าแรก
if (!$l) {
    flash('ไม่พบประกาศนี้ (อาจถูกลบไปแล้ว)', 'warning');
    header('Location: index.php');
    exit;
}

// 3. ดึงความเห็นของประกาศนี้
$stmt = $pdo->prepare(
    "SELECT c.*, u.name FROM comments c
     JOIN users u ON c.user_id = u.id
     WHERE c.listing_id = ? ORDER BY c.created_at ASC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. เช็คสิทธิ์: เป็นเจ้าของประกาศไหม?
$is_owner = is_login() && $_SESSION['user']['id'] == $l['user_id'];

// 5. ส่วนแสดงหน้าเว็บ
$page_title = $l['title'];
include 'header.php';
?>

<a href="index.php" class="back-link">← กลับบอร์ด</a>

<div class="row g-4 mt-1">
  <!-- 5.1 ฝั่งซ้าย: รายละเอียดสินค้า -->
  <div class="col-lg-7">
    <div class="note note-<?= $l['id'] % 4 ?> note-big <?= $l['status'] === 'sold' ? 'is-sold' : '' ?>">
      <span class="pin"></span>
      <?php if ($l['status'] === 'sold'): ?><span class="stamp">ขายแล้ว</span><?php endif; ?>
      <span class="note-cat"><?= e($l['category']) ?></span>
      <h2><?= e($l['title']) ?></h2>
      <div class="note-price"><?= number_format($l['price']) ?> ฿</div>
      <p class="note-desc"><?= nl2br(e($l['description'])) ?></p>
      <div class="note-meta">ลงเมื่อ <?= date('d/m/Y H:i', strtotime($l['created_at'])) ?> · โดย <?= e($l['seller']) ?></div>
    </div>

    <!-- 5.2 ช่องทางติดต่อ: เห็นเฉพาะสมาชิกที่ล็อกอิน -->
    <div class="paper-card mt-4">
      <h5>📞 ช่องทางติดต่อผู้ขาย</h5>
      <?php if (is_login()): ?>
        <p class="contact-box mb-0"><?= e($l['contact']) ?></p>
      <?php else: ?>
        <p class="mb-0">กรุณา <a href="login.php">เข้าสู่ระบบ</a> เพื่อดูช่องทางติดต่อ</p>
      <?php endif; ?>
    </div>

    <!-- 5.3 ปุ่มจัดการ: เจ้าของ = แก้ไข/ขายแล้ว/ลบ, แอดมิน = ลบได้ทุกประกาศ -->
    <?php if ($is_owner || is_admin()): ?>
      <div class="d-flex flex-wrap gap-2 mt-3">

        <?php if ($is_owner): ?>
          <a class="btn btn-outline-dark" href="listing_edit.php?id=<?= $l['id'] ?>">✏️ แก้ไข</a>

          <!-- ปุ่มสลับสถานะ ขายอยู่ <-> ขายแล้ว -->
          <form method="post" action="listing_sold.php">
            <input type="hidden" name="id" value="<?= $l['id'] ?>">
            <button class="btn btn-success">
              <?= $l['status'] === 'selling' ? '✅ ทำเครื่องหมายว่าขายแล้ว' : '↩️ กลับมาขายอีกครั้ง' ?>
            </button>
          </form>
        <?php endif; ?>

        <form method="post" action="listing_delete.php" onsubmit="return confirm('ยืนยันลบประกาศนี้?')">
          <input type="hidden" name="id" value="<?= $l['id'] ?>">
          <button class="btn btn-danger">
            🗑️ ลบประกาศ<?= (!$is_owner && is_admin()) ? ' (แอดมิน)' : '' ?>
          </button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <!-- 5.4 ฝั่งขวา: ความเห็น (ฟีเจอร์เสริม) -->
  <div class="col-lg-5">
    <div class="paper-card">
      <h5>💬 ความเห็น (<?= count($comments) ?>)</h5>

      <?php foreach ($comments as $c): ?>
        <div class="comment">
          <div class="d-flex justify-content-between">
            <strong><?= e($c['name']) ?></strong>
            <!-- แอดมินลบความเห็นได้ / เจ้าของความเห็นลบของตัวเองได้ -->
            <?php if (is_admin() || (is_login() && $_SESSION['user']['id'] == $c['user_id'])): ?>
              <form method="post" action="comment_delete.php" onsubmit="return confirm('ลบความเห็นนี้?')">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <input type="hidden" name="listing_id" value="<?= $l['id'] ?>">
                <button class="btn-x" title="ลบ">✕</button>
              </form>
            <?php endif; ?>
          </div>
          <div><?= e($c['body']) ?></div>
          <small class="text-muted"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></small>
        </div>
      <?php endforeach; ?>

      <?php if (count($comments) === 0): ?>
        <p class="text-muted">ยังไม่มีความเห็น</p>
      <?php endif; ?>

      <!-- ฟอร์มเพิ่มความเห็น (ต้องล็อกอิน) -->
      <?php if (is_login()): ?>
        <form method="post" action="comment_add.php" class="mt-3">
          <input type="hidden" name="listing_id" value="<?= $l['id'] ?>">
          <textarea class="form-control mb-2" name="body" rows="2" maxlength="500" placeholder="เขียนความเห็น..." required></textarea>
          <button class="btn btn-pin btn-sm">ส่งความเห็น</button>
        </form>
      <?php else: ?>
        <p class="mt-3 mb-0"><a href="login.php">เข้าสู่ระบบ</a> เพื่อแสดงความเห็น</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

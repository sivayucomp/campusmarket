<?php
// =====================================================
// listing_edit.php : แก้ไขประกาศของตัวเอง (Update)
// =====================================================
require 'config.php';
require_login();

// 1. ดึงประกาศที่จะแก้
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ?");
$stmt->execute([$id]);
$l = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. ตรวจสิทธิ์: ต้องเป็นเจ้าของประกาศเท่านั้น (แอดมินลบได้ แต่ไม่แก้ของคนอื่น)
if (!$l || $l['user_id'] != $_SESSION['user']['id']) {
    flash('คุณไม่มีสิทธิ์แก้ไขประกาศนี้', 'danger');
    header('Location: index.php');
    exit;
}

$error = '';

// 3. เมื่อกดบันทึก
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3.1 รับค่าใหม่
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price       = $_POST['price'];
    $category    = $_POST['category'];

    // 3.2 ตรวจความถูกต้อง
    if ($title === '' || $description === '') {
        $error = 'กรอกชื่อและรายละเอียดสินค้าด้วยนะ';
    } elseif (!is_numeric($price) || $price < 0) {
        $error = 'ราคาต้องเป็นตัวเลข';
    } else {
        // 3.3 อัปเดตข้อมูล (เงื่อนไข user_id ซ้ำอีกชั้นเพื่อความปลอดภัย)
        $stmt = $pdo->prepare(
            "UPDATE listings
             SET title = ?, description = ?, price = ?, category = ?
             WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $description, $price, $category, $id, $_SESSION['user']['id']]);

        flash('แก้ไขประกาศเรียบร้อย');
        header('Location: listing_view.php?id=' . $id);
        exit;
    }
    // 3.4 ถ้ามี error ให้โชว์ค่าที่เพิ่งกรอกกลับในฟอร์ม
    $l['title'] = $title; $l['description'] = $description;
    $l['price'] = $price; $l['category'] = $category;
}

// 4. ส่วนแสดงหน้าเว็บ
$page_title = 'แก้ไขประกาศ';
include 'header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="paper-card">
      <h2 class="mb-3">✏️ แก้ไขประกาศ</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <input type="hidden" name="id" value="<?= $l['id'] ?>">

        <label class="form-label">ชื่อสินค้า</label>
        <input class="form-control mb-3" name="title" value="<?= e($l['title']) ?>" required>

        <label class="form-label">รายละเอียด</label>
        <textarea class="form-control mb-3" name="description" rows="4" required><?= e($l['description']) ?></textarea>

        <div class="row">
          <div class="col-6">
            <label class="form-label">ราคา (บาท)</label>
            <input type="number" min="0" class="form-control mb-3" name="price" value="<?= e($l['price']) ?>" required>
          </div>
          <div class="col-6">
            <label class="form-label">หมวดหมู่</label>
            <select class="form-select mb-3" name="category">
              <?php foreach ($categories as $c): ?>
                <option <?= $l['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-pin flex-grow-1">บันทึกการแก้ไข</button>
          <a class="btn btn-outline-dark" href="listing_view.php?id=<?= $l['id'] ?>">ยกเลิก</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<?php
// =====================================================
// listing_add.php : ลงประกาศสินค้า (Create)
// เฉพาะสมาชิกที่ล็อกอินแล้ว
// =====================================================
require 'config.php';

// 1. ต้องล็อกอินก่อน
require_login();

$error = '';

// 2. เมื่อกดปุ่มลงประกาศ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2.1 รับค่าจากฟอร์ม
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price       = $_POST['price'];
    $category    = $_POST['category'];

    // 2.2 ตรวจความถูกต้อง
    if ($title === '' || $description === '') {
        $error = 'กรอกชื่อและรายละเอียดสินค้าด้วยนะ';
    } elseif (!is_numeric($price) || $price < 0) {
        $error = 'ราคาต้องเป็นตัวเลข';
    } else {
        // 2.3 บันทึกลงตาราง listings
        // user_id เอามาจาก session = เจ้าของประกาศคือคนที่ล็อกอินอยู่
        $stmt = $pdo->prepare(
            "INSERT INTO listings (user_id, title, description, price, category)
             VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user']['id'], $title, $description, $price, $category]);

        // 2.4 ไปหน้ารายละเอียดของประกาศที่เพิ่งสร้าง
        flash('ติดประกาศขึ้นบอร์ดแล้ว');
        header('Location: listing_view.php?id=' . $pdo->lastInsertId());
        exit;
    }
}

// 3. ส่วนแสดงหน้าเว็บ
$page_title = 'ติดประกาศ';
include 'header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="paper-card">
      <h2 class="mb-3">📌 ติดประกาศขายของ</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <label class="form-label">ชื่อสินค้า</label>
        <input class="form-control mb-3" name="title" value="<?= e($_POST['title'] ?? '') ?>" required>

        <label class="form-label">รายละเอียด</label>
        <textarea class="form-control mb-3" name="description" rows="4" required><?= e($_POST['description'] ?? '') ?></textarea>

        <div class="row">
          <div class="col-6">
            <label class="form-label">ราคา (บาท)</label>
            <input type="number" min="0" class="form-control mb-3" name="price" value="<?= e($_POST['price'] ?? '') ?>" required>
          </div>
          <div class="col-6">
            <label class="form-label">หมวดหมู่</label>
            <select class="form-select mb-3" name="category">
              <?php foreach ($categories as $c): ?>
                <option><?= e($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <button class="btn btn-pin w-100">ติดประกาศ</button>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

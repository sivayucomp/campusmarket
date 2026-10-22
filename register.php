<?php
// =====================================================
// register.php : สมัครสมาชิก (ผู้เยี่ยมชม -> สมาชิก)
// =====================================================
require 'config.php';

$error = '';

// 1. เมื่อกดปุ่มสมัคร (ส่งฟอร์มแบบ POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1.1 รับค่าจากฟอร์ม
    $name       = trim($_POST['name']);
    $student_id = trim($_POST['student_id']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $contact    = trim($_POST['contact']);

    // 1.2 ตรวจว่ากรอกครบไหม
    // [แก้ได้] เปลี่ยนเลข 4 เป็นความยาวรหัสผ่านขั้นต่ำที่ต้องการ
    if ($name === '' || $student_id === '' || $email === '' || $contact === '') {
        $error = 'กรอกข้อมูลให้ครบทุกช่องนะ';
    } elseif (strlen($password) < 4) {
        $error = 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร';
    } else {
        // 1.3 เช็คว่าอีเมลนี้เคยสมัครแล้วหรือยัง
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'อีเมลนี้ถูกใช้แล้ว';
        } else {
            // 1.4 เข้ารหัสผ่านก่อนเก็บ (ห้ามเก็บรหัสจริง)
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // 1.5 บันทึกลงตาราง users (role เริ่มต้นเป็น member)
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, student_id, email, password, contact)
                 VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $student_id, $email, $hash, $contact]);

            // 1.6 สมัครเสร็จ ส่งไปหน้า login
            flash('สมัครสมาชิกสำเร็จ เข้าสู่ระบบได้เลย');
            header('Location: login.php');
            exit;
        }
    }
}

// 2. ส่วนแสดงหน้าเว็บ
$page_title = 'สมัครสมาชิก';
include 'header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="paper-card">
      <h2 class="mb-3">📝 สมัครสมาชิก</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <label class="form-label">ชื่อ-นามสกุล</label>
        <input class="form-control mb-3" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>

        <label class="form-label">รหัสนักศึกษา</label>
        <input class="form-control mb-3" name="student_id" value="<?= e($_POST['student_id'] ?? '') ?>" required>

        <label class="form-label">อีเมล</label>
        <input type="email" class="form-control mb-3" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>

        <label class="form-label">รหัสผ่าน</label>
        <input type="password" class="form-control mb-3" name="password" required>

        <label class="form-label">ช่องทางติดต่อ (LINE ID หรือเบอร์โทร)</label>
        <input class="form-control mb-4" name="contact" value="<?= e($_POST['contact'] ?? '') ?>" required>

        <button class="btn btn-pin w-100">สมัครสมาชิก</button>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

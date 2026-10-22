<?php
// =====================================================
// login.php : เข้าสู่ระบบ
// =====================================================
require 'config.php';

$error = '';

// 1. เมื่อกดปุ่มเข้าสู่ระบบ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1.1 รับอีเมลและรหัสผ่าน
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // 1.2 หาผู้ใช้จากอีเมล
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 1.3 ตรวจรหัสผ่านเทียบกับค่า hash ในฐานข้อมูล
    if ($user && password_verify($password, $user['password'])) {

        // 1.4 จำข้อมูลผู้ใช้ไว้ใน session (ไม่เก็บรหัสผ่าน)
        $_SESSION['user'] = [
            'id'   => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],   // 'member' หรือ 'admin'
        ];

        flash('ยินดีต้อนรับ ' . $user['name']);
        header('Location: index.php');
        exit;
    } else {
        $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
    }
}

// 2. ส่วนแสดงหน้าเว็บ
$page_title = 'เข้าสู่ระบบ';
include 'header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-4">
    <div class="paper-card">
      <h2 class="mb-3">🔑 เข้าสู่ระบบ</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <label class="form-label">อีเมล</label>
        <input type="email" class="form-control mb-3" name="email" required>

        <label class="form-label">รหัสผ่าน</label>
        <input type="password" class="form-control mb-4" name="password" required>

        <button class="btn btn-pin w-100">เข้าสู่ระบบ</button>
      </form>

      <p class="text-center mt-3 mb-0">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

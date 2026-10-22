<?php
// header.php : ส่วนหัวของทุกหน้า (เมนู + ข้อความแจ้งเตือน)
// 1. ชื่อหน้า (แต่ละหน้ากำหนดตัวแปร $page_title ก่อน include ไฟล์นี้)
$page_title = $page_title ?? 'CampusMarket';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title) ?> | CampusMarket</title>
  <!-- 2. Bootstrap (Responsive) + ฟอนต์ไทย -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;600;700&family=Sarabun:wght@400;600&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>

<!-- 3. แถบเมนูด้านบน (หน้าตาเหมือนป้ายไม้) -->
<nav class="navbar navbar-expand-md board-nav">
  <div class="container">
    <a class="navbar-brand brand" href="index.php">📌 CampusMarket</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto align-items-md-center gap-md-2">
        <li class="nav-item"><a class="nav-link" href="index.php">บอร์ดประกาศ</a></li>
        <?php if (is_login()): ?>
          <!-- 3.1 เมนูสำหรับคนที่ล็อกอินแล้ว -->
          <li class="nav-item"><a class="nav-link" href="listing_add.php">+ ติดประกาศ</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?mine=1">ของฉัน</a></li>
          <li class="nav-item">
            <span class="nav-link user-chip">
              <?= e($_SESSION['user']['name']) ?>
              <?php if (is_admin()): ?><span class="badge text-bg-danger">ADMIN</span><?php endif; ?>
            </span>
          </li>
          <li class="nav-item"><a class="nav-link" href="logout.php">ออกจากระบบ</a></li>
        <?php else: ?>
          <!-- 3.2 เมนูสำหรับผู้เยี่ยมชม -->
          <li class="nav-item"><a class="nav-link" href="login.php">เข้าสู่ระบบ</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-pin" href="register.php">สมัครสมาชิก</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-4">
<?php
// 4. แสดงข้อความแจ้งเตือน (flash) แล้วลบทิ้ง
if (isset($_SESSION['flash'])):
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
?>
  <div class="alert alert-<?= e($f['type']) ?> note-alert"><?= e($f['msg']) ?></div>
<?php endif; ?>

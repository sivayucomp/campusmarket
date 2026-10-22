<?php
// =====================================================
// config.php : ไฟล์ตั้งค่า (ทุกหน้า include ไฟล์นี้เป็นอันดับแรก)
// =====================================================

// 1. เริ่ม session เพื่อจำว่าใครล็อกอินอยู่
session_start();

// 2. เชื่อมต่อฐานข้อมูล MySQL
// 2.1 [แก้ได้] ถ้า XAMPP ตั้งรหัสผ่าน root ไว้ ให้เปลี่ยนค่าตรง '' (ช่องที่สอง)
$pdo = new PDO('mysql:host=localhost;dbname=campusmarket;charset=utf8mb4', 'root', '');
// 2.2 ให้แสดง error ชัดเจนเวลา SQL ผิด (ช่วยตอน debug)
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 3. รายการหมวดหมู่สินค้า
// 3.1 [แก้ได้] อยากเพิ่ม/ลดหมวดหมู่ แก้ในอาร์เรย์นี้ที่เดียว ทุกหน้าเปลี่ยนตาม
$categories = ['หนังสือเรียน', 'เครื่องคิดเลข', 'ของใช้ในหอ', 'เครื่องใช้ไฟฟ้า', 'อื่นๆ'];

// 4. ฟังก์ชันช่วยเล็กๆ
// 4.1 e() = ป้องกัน XSS ก่อนแสดงข้อความของผู้ใช้บนหน้าเว็บ
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
// 4.2 เช็คว่าล็อกอินหรือยัง
function is_login() {
    return isset($_SESSION['user']);
}
// 4.3 เช็คว่าเป็นแอดมินไหม
function is_admin() {
    return is_login() && $_SESSION['user']['role'] === 'admin';
}
// 4.4 บังคับให้ล็อกอินก่อนเข้าหน้านี้ ถ้ายังไม่ล็อกอินเด้งไปหน้า login
function require_login() {
    if (!is_login()) {
        flash('กรุณาเข้าสู่ระบบก่อนนะ', 'warning');
        header('Location: login.php');
        exit;
    }
}
// 4.5 ข้อความแจ้งเตือนสั้นๆ (แสดงครั้งเดียวแล้วหาย)
function flash($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

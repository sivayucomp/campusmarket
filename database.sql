-- =====================================================
-- CampusMarket : ไฟล์ฐานข้อมูลสำหรับ XAMPP (phpMyAdmin > Import)
-- ทำตามหัวข้อ "5. The data" ใน Project Proposal
-- วิชา 477-301 Full Stack Web Development
-- =====================================================
--
-- ความสัมพันธ์ (ตามที่เขียนไว้ใน Proposal):
--
--   users (1) ───────< listings (หลายรายการ)
--   สมาชิก 1 คน ลงประกาศได้หลายรายการ
--
--   listings (1) ────< comments (หลายรายการ)
--   1 ประกาศ มีความเห็นได้หลายรายการ
--
--   users (1) ───────< comments
--   สมาชิก 1 คน แสดงความเห็นได้หลายครั้ง
--
-- =====================================================

-- 1. สร้างฐานข้อมูล (นำเข้าซ้ำได้: จะลบของเก่าแล้วสร้างใหม่)
DROP DATABASE IF EXISTS campusmarket;
CREATE DATABASE campusmarket
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE campusmarket;

-- 2. ข้อมูลสมาชิก (Users)
-- 2.1 เก็บ: ชื่อ, รหัสนักศึกษา, อีเมล, รหัสผ่าน, ช่องทางติดต่อ, สถานะผู้ใช้งาน
-- 2.2 role = สถานะผู้ใช้งาน: 'member' สมาชิกทั่วไป / 'admin' ผู้ดูแลระบบ
CREATE TABLE users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,                      -- ชื่อ
  student_id  VARCHAR(15)  NOT NULL,                      -- รหัสนักศึกษา
  email       VARCHAR(120) NOT NULL UNIQUE,               -- อีเมล (ห้ามซ้ำ)
  password    VARCHAR(255) NOT NULL,                      -- รหัสผ่าน (เก็บเป็น hash)
  contact     VARCHAR(100) NOT NULL,                      -- ช่องทางติดต่อ (LINE ID / เบอร์โทร)
  role        ENUM('member','admin') NOT NULL DEFAULT 'member',  -- สถานะผู้ใช้งาน
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. ข้อมูลประกาศสินค้า (Listings)
-- 3.1 เก็บ: ชื่อสินค้า, รายละเอียด, ราคา, หมวดหมู่, สถานะสินค้า และเจ้าของโพสต์
-- 3.2 status = สถานะสินค้า: 'selling' ขายอยู่ / 'sold' ขายแล้ว
-- 3.3 user_id = เชื่อมโยงกับเจ้าของโพสต์ (Foreign Key ไปตาราง users)
CREATE TABLE listings (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,                              -- เจ้าของโพสต์
  title        VARCHAR(150) NOT NULL,                     -- ชื่อสินค้า
  description  TEXT NOT NULL,                             -- รายละเอียด
  price        DECIMAL(10,2) NOT NULL,                    -- ราคา
  category     VARCHAR(50) NOT NULL,                      -- หมวดหมู่
  status       ENUM('selling','sold') NOT NULL DEFAULT 'selling',  -- สถานะสินค้า
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. ข้อมูลความคิดเห็น (Comments - Optional)
-- 4.1 เก็บข้อความความคิดเห็นใต้ประกาศสินค้าแต่ละชิ้น
-- 4.2 ลบประกาศ -> ความเห็นของประกาศนั้นถูกลบตามอัตโนมัติ (ON DELETE CASCADE)
CREATE TABLE comments (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  listing_id  INT NOT NULL,                               -- ความเห็นอยู่ใต้ประกาศไหน
  user_id     INT NOT NULL,                               -- ใครเป็นคนเขียน
  body        VARCHAR(500) NOT NULL,                      -- ข้อความความเห็น
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. ข้อมูลผู้ใช้ตัวอย่าง
-- 5.1 รหัสผ่านสมาชิกทุกคน = 1234  /  แอดมิน = admin1234
INSERT INTO users (name, student_id, email, password, contact, role) VALUES
('ปวริศ แสงทอง',   '6710510101', 'pawarit@example.com',   '$2y$10$FPReTVEfozrOZEtuVO3Xa.7WrVe8o7iP1Hevx20w84/bHSYlu/FSG', 'LINE: pawarit.s',  'member'),
('ณิชากานต์ ใจดี',  '6710510102', 'nichakarn@example.com', '$2y$10$FPReTVEfozrOZEtuVO3Xa.7WrVe8o7iP1Hevx20w84/bHSYlu/FSG', 'LINE: nicha_k',    'member'),
('ธีรภัทร มั่นคง',  '6710510103', 'theeraphat@example.com','$2y$10$FPReTVEfozrOZEtuVO3Xa.7WrVe8o7iP1Hevx20w84/bHSYlu/FSG', '081-555-0142',     'member'),
('อชิรญา พรมเมศ',  '6710510104', 'achiraya@example.com',  '$2y$10$FPReTVEfozrOZEtuVO3Xa.7WrVe8o7iP1Hevx20w84/bHSYlu/FSG', 'LINE: achi.p',      'member'),
('แอดมิน ตะวัน',    '6710510000', 'admin@example.com',     '$2y$10$XtGnw/7mDpDamEdt7jP0T.C.IqP1YBjdKJDBZ8DMPcpxQ44O4HUjC', 'LINE: admin.tawan', 'admin');

-- 6. ประกาศสินค้าตัวอย่าง (มีทั้งขายอยู่และขายแล้ว)
INSERT INTO listings (user_id, title, description, price, category, status) VALUES
(1, 'หนังสือแคลคูลัส 1 (Thomas)',        'สภาพดี มีไฮไลต์นิดหน่อย ปีที่แล้วใช้เรียนเทอมเดียว', 250, 'หนังสือเรียน',   'selling'),
(1, 'เครื่องคิดเลข Casio fx-991ES Plus',  'ใช้งานปกติ ฝาหลังมีรอยขีดข่วนเล็กน้อย',               320, 'เครื่องคิดเลข',   'selling'),
(2, 'พัดลมตั้งโต๊ะ 12 นิ้ว',              'ใช้ในหอ 1 ปี ลมแรงดี ส่งมอบที่หอ 3',                   180, 'ของใช้ในหอ',     'selling'),
(2, 'ชุดสมุดเลคเชอร์ + ชีทสรุป',          'สรุปครบทุกบท พร้อมข้อสอบเก่า',                         90,  'หนังสือเรียน',   'sold'),
(3, 'หม้อหุงข้าวไฟฟ้า 1 ลิตร',            'ซื้อมา 6 เดือน เพิ่งย้ายออกจากหอ เลยขายต่อ',           290, 'เครื่องใช้ไฟฟ้า', 'selling'),
(3, 'โคมไฟอ่านหนังสือ LED',               'ปรับความสว่างได้ 3 ระดับ ชาร์จผ่าน USB',               150, 'ของใช้ในหอ',     'selling'),
(4, 'หนังสือ Data Structures with C',     'มือสองสภาพ 85% ไม่มีรอยเขียน',                         200, 'หนังสือเรียน',   'selling'),
(4, 'ไม้บรรทัดสถาปัตย์ + ชุดเครื่องเขียนเขียนแบบ', 'ครบชุด ใช้ครั้งเดียวในวิชา Drawing',            120, 'อื่นๆ',         'selling');

-- 7. ความเห็นตัวอย่าง
INSERT INTO comments (listing_id, user_id, body) VALUES
(1, 2, 'ยังอยู่ไหมคะ สนใจค่ะ'),
(1, 1, 'ยังอยู่ครับ ทักไลน์มาได้เลย'),
(3, 3, 'ลดได้อีกนิดไหมครับ');

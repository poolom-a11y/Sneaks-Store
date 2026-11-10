<?php
/* ============================================================
   Sneaks Store - Installer
   สร้างโฟลเดอร์ shoe-shop + ฐานข้อมูล + บัญชีแอดมิน + shoe-shop.zip
   ============================================================ */
set_time_limit(120);
$target = __DIR__ . '/shoe-shop';
$files = [];

function seed_catalog(PDO $db, array $seed): int {
    $ids = [];
    foreach ($seed['cats'] as $name) {
        $st = $db->prepare("SELECT id FROM categories WHERE name=?"); $st->execute([$name]);
        $id = $st->fetchColumn();
        if (!$id) { $db->prepare("INSERT INTO categories(name) VALUES(?)")->execute([$name]); $id = $db->lastInsertId(); }
        $ids[$name] = $id;
    }
    $n = 0;
    foreach ($seed['products'] as $p) {
        $st = $db->prepare("SELECT id FROM products WHERE image=? OR name=?"); $st->execute([$p['image'], $p['name']]);
        if ($st->fetchColumn()) continue;
        $db->prepare("INSERT INTO products(category_id,name,description,price,stock,sizes,image,sold) VALUES(?,?,?,?,?,?,?,?)")
           ->execute([$ids[$p['cat']], $p['name'], $p['desc'], $p['price'], $p['stock'], $p['sizes'], $p['image'], $p['sold']]);
        $n++;
    }
    foreach ($seed['legacy'] as $old => $img)
        $db->prepare("UPDATE products SET image=? WHERE name=? AND (image IS NULL OR image='')")->execute([$img, $old]);
    return $n;
}
$RENAME_IMAGES = [
    'products/seed_01.svg' => 'products/canvas-sneakers-01.svg',
    'products/seed_02.svg' => 'products/canvas-sneakers-02.svg',
    'products/seed_03.svg' => 'products/canvas-sneakers-03.svg',
    'products/seed_04.svg' => 'products/canvas-sneakers-04.svg',
    'products/seed_05.svg' => 'products/running-shoes-01.svg',
    'products/seed_06.svg' => 'products/running-shoes-02.svg',
    'products/seed_07.svg' => 'products/running-shoes-03.svg',
    'products/seed_08.svg' => 'products/running-shoes-04.svg',
    'products/seed_09.svg' => 'products/high-heels-01.svg',
    'products/seed_10.svg' => 'products/high-heels-02.svg',
    'products/seed_11.svg' => 'products/high-heels-03.svg',
    'products/seed_12.svg' => 'products/high-heels-04.svg',
    'products/seed_13.svg' => 'products/sandals-01.svg',
    'products/seed_14.svg' => 'products/sandals-02.svg',
    'products/seed_15.svg' => 'products/sandals-03.svg',
    'products/seed_16.svg' => 'products/sandals-04.svg',
    'products/seed_17.svg' => 'products/leather-shoes-01.svg',
    'products/seed_18.svg' => 'products/leather-shoes-02.svg',
    'products/seed_19.svg' => 'products/leather-shoes-03.svg',
    'products/seed_20.svg' => 'products/leather-shoes-04.svg',
    'products/seed_21.svg' => 'products/boots-01.svg',
    'products/seed_22.svg' => 'products/boots-02.svg',
    'products/seed_23.svg' => 'products/boots-03.svg',
    'products/seed_24.svg' => 'products/boots-04.svg',
    'products/seed_25.svg' => 'products/football-boots-01.svg',
    'products/seed_26.svg' => 'products/football-boots-02.svg',
    'products/seed_27.svg' => 'products/football-boots-03.svg',
    'products/seed_28.svg' => 'products/football-boots-04.svg',
    'products/seed_29.svg' => 'products/basketball-shoes-01.svg',
    'products/seed_30.svg' => 'products/basketball-shoes-02.svg',
    'products/seed_31.svg' => 'products/basketball-shoes-03.svg',
    'products/seed_32.svg' => 'products/basketball-shoes-04.svg',
    'products/seed_33.svg' => 'products/kids-shoes-01.svg',
    'products/seed_34.svg' => 'products/kids-shoes-02.svg',
    'products/seed_35.svg' => 'products/kids-shoes-03.svg',
    'products/seed_36.svg' => 'products/kids-shoes-04.svg',
    'products/seed_37.svg' => 'products/school-shoes-01.svg',
    'products/seed_38.svg' => 'products/school-shoes-02.svg',
    'products/seed_39.svg' => 'products/school-shoes-03.svg',
    'products/seed_40.svg' => 'products/school-shoes-04.svg',
];
$SEED = json_decode(<<<'SEED_EOF'
{"cats": ["รองเท้าผู้ชาย", "รองเท้าผู้หญิง", "โปรโมชั่น", "รองเท้าผ้าใบ", "รองเท้าวิ่ง", "รองเท้าส้นสูง", "รองเท้าแตะ", "รองเท้าหนัง", "รองเท้าบูท", "รองเท้าฟุตบอล", "รองเท้าบาสเกตบอล", "รองเท้าเด็ก", "รองเท้านักเรียน"], "products": [{"cat": "รองเท้าผ้าใบ", "name": "Sneaks Cloud Low สีขาวชมพู", "desc": "ผ้าใบนุ่ม น้ำหนักเบา แมทช์ได้ทุกชุด", "price": 890, "stock": 32, "sizes": "36,37,38,39,40,41,42,43", "image": "products/canvas-sneakers-01.svg", "sold": 8}, {"cat": "รองเท้าผ้าใบ", "name": "Sneaks Pastel Canvas", "desc": "ผ้าใบสีพาสเทล พื้นยางกันลื่น", "price": 690, "stock": 37, "sizes": "36,37,38,39,40,41,42,43", "image": "products/canvas-sneakers-02.svg", "sold": 0}, {"cat": "รองเท้าผ้าใบ", "name": "Sneaks Street Black", "desc": "ทรงสตรีท ดำล้วน ใส่ได้ทั้งวัน", "price": 990, "stock": 16, "sizes": "36,37,38,39,40,41,42,43", "image": "products/canvas-sneakers-03.svg", "sold": 215}, {"cat": "รองเท้าผ้าใบ", "name": "Sneaks Mint Breeze", "desc": "สีมินต์สดใส พื้นหนานุ่ม", "price": 790, "stock": 18, "sizes": "36,37,38,39,40,41,42,43", "image": "products/canvas-sneakers-04.svg", "sold": 41}, {"cat": "รองเท้าวิ่ง", "name": "Sneaks Run Pro สีชมพู", "desc": "พื้นรองรับแรงกระแทก ระบายอากาศดี", "price": 1590, "stock": 49, "sizes": "37,38,39,40,41,42,43,44", "image": "products/running-shoes-01.svg", "sold": 0}, {"cat": "รองเท้าวิ่ง", "name": "Sneaks Air Runner", "desc": "น้ำหนักเบา ผ้าตาข่ายระบายอากาศ", "price": 1790, "stock": 44, "sizes": "37,38,39,40,41,42,43,44", "image": "products/running-shoes-02.svg", "sold": 15}, {"cat": "รองเท้าวิ่ง", "name": "Sneaks Marathon Light", "desc": "เหมาะกับวิ่งระยะไกล ยึดเกาะดี", "price": 1990, "stock": 14, "sizes": "37,38,39,40,41,42,43,44", "image": "products/running-shoes-03.svg", "sold": 3}, {"cat": "รองเท้าวิ่ง", "name": "Sneaks Jog Cloud", "desc": "นุ่มสบาย เหมาะวิ่งเบา ๆ ทุกเช้า", "price": 1290, "stock": 39, "sizes": "37,38,39,40,41,42,43,44", "image": "products/running-shoes-04.svg", "sold": 67}, {"cat": "รองเท้าส้นสูง", "name": "ส้นสูงสีนู้ด 2 นิ้ว", "desc": "ส้นไม่สูงมาก ใส่ทำงานได้ทั้งวัน", "price": 890, "stock": 16, "sizes": "35,36,37,38,39,40", "image": "products/high-heels-01.svg", "sold": 15}, {"cat": "รองเท้าส้นสูง", "name": "ส้นสูงชมพูบลัช 3 นิ้ว", "desc": "หัวแหลม ทรงสวย เพิ่มความมั่นใจ", "price": 1190, "stock": 17, "sizes": "35,36,37,38,39,40", "image": "products/high-heels-02.svg", "sold": 215}, {"cat": "รองเท้าส้นสูง", "name": "ส้นสูงสีดำคลาสสิก", "desc": "เรียบหรู ใส่ได้ทุกงาน", "price": 1090, "stock": 39, "sizes": "35,36,37,38,39,40", "image": "products/high-heels-03.svg", "sold": 0}, {"cat": "รองเท้าส้นสูง", "name": "ส้นสูงแดงเลดี้", "desc": "สีแดงสด โดดเด่นทุกสายตา", "price": 1290, "stock": 48, "sizes": "35,36,37,38,39,40", "image": "products/high-heels-04.svg", "sold": 3}, {"cat": "รองเท้าแตะ", "name": "รองเท้าแตะหูหนีบสีชมพู", "desc": "พื้นยางนุ่ม กันลื่น", "price": 199, "stock": 26, "sizes": "36,37,38,39,40,41,42,43", "image": "products/sandals-01.svg", "sold": 380}, {"cat": "รองเท้าแตะ", "name": "รองเท้าแตะแบบสวม Soft Slide", "desc": "พื้นหนานุ่ม ใส่สบายเหมือนเดินบนเมฆ", "price": 350, "stock": 15, "sizes": "36,37,38,39,40,41,42,43", "image": "products/sandals-02.svg", "sold": 380}, {"cat": "รองเท้าแตะ", "name": "รองเท้าแตะสวมสีฟ้า Ocean", "desc": "เบา กันน้ำ เหมาะใส่ในบ้านและนอกบ้าน", "price": 390, "stock": 49, "sizes": "36,37,38,39,40,41,42,43", "image": "products/sandals-03.svg", "sold": 67}, {"cat": "รองเท้าแตะ", "name": "รองเท้าแตะหูหนีบสีดำ", "desc": "ทนทาน ใช้ได้ทุกวัน", "price": 229, "stock": 15, "sizes": "36,37,38,39,40,41,42,43", "image": "products/sandals-04.svg", "sold": 15}, {"cat": "รองเท้าหนัง", "name": "คัทชูหนังสีขาวงาช้าง", "desc": "หนังนิ่ม หัวมน ใส่สบาย", "price": 990, "stock": 14, "sizes": "38,39,40,41,42,43,44", "image": "products/leather-shoes-01.svg", "sold": 215}, {"cat": "รองเท้าหนัง", "name": "โลฟเฟอร์หนังสีน้ำตาล", "desc": "หนังแท้เกรดดี ทรงคลาสสิก", "price": 1790, "stock": 20, "sizes": "38,39,40,41,42,43,44", "image": "products/leather-shoes-02.svg", "sold": 24}, {"cat": "รองเท้าหนัง", "name": "ออกซ์ฟอร์ดหนังดำ", "desc": "ผูกเชือก ใส่ทำงานเรียบหรู", "price": 1990, "stock": 38, "sizes": "38,39,40,41,42,43,44", "image": "products/leather-shoes-03.svg", "sold": 8}, {"cat": "รองเท้าหนัง", "name": "โลฟเฟอร์หนังชมพูพาสเทล", "desc": "หนังสีชมพู น่ารักแต่ดูดี", "price": 1490, "stock": 46, "sizes": "38,39,40,41,42,43,44", "image": "products/leather-shoes-04.svg", "sold": 3}, {"cat": "รองเท้าบูท", "name": "บูทหนังสั้นสีดำ", "desc": "ซิปข้าง ใส่ง่าย ทรงเรียบ", "price": 1890, "stock": 48, "sizes": "36,37,38,39,40,41,42", "image": "products/boots-01.svg", "sold": 24}, {"cat": "รองเท้าบูท", "name": "บูทสีน้ำตาลคาราเมล", "desc": "หนังนุ่ม ลุคเรโทร", "price": 2190, "stock": 47, "sizes": "36,37,38,39,40,41,42", "image": "products/boots-02.svg", "sold": 8}, {"cat": "รองเท้าบูท", "name": "บูทชมพูนู้ดส้นเตี้ย", "desc": "ส้นเตี้ยเดินสบาย สีหวาน", "price": 1690, "stock": 18, "sizes": "36,37,38,39,40,41,42", "image": "products/boots-03.svg", "sold": 380}, {"cat": "รองเท้าบูท", "name": "บูทหุ้มข้อสีเทา", "desc": "ลุคสตรีท ทนทาน", "price": 1990, "stock": 48, "sizes": "36,37,38,39,40,41,42", "image": "products/boots-04.svg", "sold": 15}, {"cat": "รองเท้าฟุตบอล", "name": "Sneaks Striker สีขาว", "desc": "สตั๊ดน้ำหนักเบา เหมาะสนามหญ้าเทียม", "price": 1690, "stock": 35, "sizes": "38,39,40,41,42,43,44,45", "image": "products/football-boots-01.svg", "sold": 3}, {"cat": "รองเท้าฟุตบอล", "name": "Sneaks Goal สีเขียว", "desc": "ปุ่มยึดเกาะดี คุมบอลแม่นยำ", "price": 1890, "stock": 47, "sizes": "38,39,40,41,42,43,44,45", "image": "products/football-boots-02.svg", "sold": 3}, {"cat": "รองเท้าฟุตบอล", "name": "Sneaks Speed สีดำ", "desc": "เร็ว เบา คล่องตัว", "price": 2190, "stock": 48, "sizes": "38,39,40,41,42,43,44,45", "image": "products/football-boots-03.svg", "sold": 0}, {"cat": "รองเท้าฟุตบอล", "name": "Sneaks Futsal Pink", "desc": "เหมาะสนามฟุตซอล พื้นกันลื่น", "price": 1290, "stock": 51, "sizes": "38,39,40,41,42,43,44,45", "image": "products/football-boots-04.svg", "sold": 15}, {"cat": "รองเท้าบาสเกตบอล", "name": "Sneaks Court High สีขาว", "desc": "ข้อสูง รองรับข้อเท้า พื้นเด้ง", "price": 2290, "stock": 43, "sizes": "39,40,41,42,43,44,45", "image": "products/basketball-shoes-01.svg", "sold": 215}, {"cat": "รองเท้าบาสเกตบอล", "name": "Sneaks Dunk สีแดง", "desc": "ลุคสปอร์ต โดดเด่นกลางสนาม", "price": 2490, "stock": 39, "sizes": "39,40,41,42,43,44,45", "image": "products/basketball-shoes-02.svg", "sold": 41}, {"cat": "รองเท้าบาสเกตบอล", "name": "Sneaks Hoop สีดำทอง", "desc": "ทรงสูง ใส่เท่ ยึดเกาะดี", "price": 2690, "stock": 41, "sizes": "39,40,41,42,43,44,45", "image": "products/basketball-shoes-03.svg", "sold": 380}, {"cat": "รองเท้าบาสเกตบอล", "name": "Sneaks Air High ชมพู", "desc": "ข้อสูง น้ำหนักเบา ใส่ได้ทุกวัน", "price": 1990, "stock": 41, "sizes": "39,40,41,42,43,44,45", "image": "products/basketball-shoes-04.svg", "sold": 41}, {"cat": "รองเท้าเด็ก", "name": "Sneaks Kids ผ้าใบชมพู", "desc": "แถบเมจิกเทป ใส่ง่ายสำหรับเด็ก", "price": 490, "stock": 31, "sizes": "26,27,28,29,30,31,32,33,34", "image": "products/kids-shoes-01.svg", "sold": 15}, {"cat": "รองเท้าเด็ก", "name": "Sneaks Kids สีฟ้า", "desc": "เบา นุ่ม เด็กวิ่งเล่นได้สบาย", "price": 490, "stock": 23, "sizes": "26,27,28,29,30,31,32,33,34", "image": "products/kids-shoes-02.svg", "sold": 15}, {"cat": "รองเท้าเด็ก", "name": "Sneaks Kids สีเหลืองสดใส", "desc": "พื้นกันลื่น ลายดาวน่ารัก", "price": 520, "stock": 17, "sizes": "26,27,28,29,30,31,32,33,34", "image": "products/kids-shoes-03.svg", "sold": 380}, {"cat": "รองเท้าเด็ก", "name": "Sneaks Kids สีม่วงพาสเทล", "desc": "ใส่สบาย ซักได้", "price": 550, "stock": 31, "sizes": "26,27,28,29,30,31,32,33,34", "image": "products/kids-shoes-04.svg", "sold": 215}, {"cat": "รองเท้านักเรียน", "name": "รองเท้านักเรียนหนังดำผูกเชือก", "desc": "ทนทาน ขัดเงาง่าย ใส่ได้ทั้งปี", "price": 690, "stock": 43, "sizes": "30,32,34,36,37,38,39,40,41", "image": "products/school-shoes-01.svg", "sold": 41}, {"cat": "รองเท้านักเรียน", "name": "รองเท้านักเรียนหญิงคัทชูสายคาด", "desc": "สายคาดเข็มขัด ทรงสุภาพ", "price": 590, "stock": 58, "sizes": "30,32,34,36,37,38,39,40,41", "image": "products/school-shoes-02.svg", "sold": 120}, {"cat": "รองเท้านักเรียน", "name": "รองเท้าผ้าใบนักเรียนสีขาว", "desc": "ผ้าใบล้วน ซักง่าย พื้นนุ่ม", "price": 450, "stock": 30, "sizes": "30,32,34,36,37,38,39,40,41", "image": "products/school-shoes-03.svg", "sold": 380}, {"cat": "รองเท้านักเรียน", "name": "รองเท้านักเรียนหนังดำ พื้นนุ่ม", "desc": "พื้นรองรับแรงกระแทก เดินสบาย", "price": 790, "stock": 16, "sizes": "30,32,34,36,37,38,39,40,41", "image": "products/school-shoes-04.svg", "sold": 3}], "legacy": {"รองเท้าผ้าใบสีชมพูพาสเทล": "products/canvas-sneakers-02.svg", "รองเท้าผ้าใบขาวคลาสสิก": "products/canvas-sneakers-01.svg", "รองเท้าวิ่ง Pinky Run": "products/running-shoes-01.svg", "ส้นสูงสีนู้ด 2 นิ้ว": "products/high-heels-01.svg", "รองเท้าแตะหูหนีบสีชมพู": "products/sandals-01.svg", "คัทชูหนังสีขาวงาช้าง": "products/leather-shoes-01.svg"}}
SEED_EOF, true);

/* ==================== config.php ==================== */
$files['config.php'] = <<<'PINKY_EOF'
<?php
// ===== ความปลอดภัยพื้นฐาน: บังคับ HTTPS (ยกเว้นเครื่อง localhost) + คุกกี้/เฮดเดอร์ปลอดภัย =====
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
$isLocal = PHP_SAPI === 'cli' || in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'])
    || preg_match('/\.(local|test|localhost)$/', $_SERVER['SERVER_NAME'] ?? '');
if (!$https && !$isLocal && isset($_SERVER['HTTP_HOST'])) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301); exit;
}
if (PHP_SAPI !== 'cli') {
    if ($https) header('Strict-Transport-Security: max-age=31536000');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => $https]);
session_start();

// ===== จัดการข้อผิดพลาด: แสดงหน้า 500 สวยงาม แทนหน้าขาว/ข้อความ error =====
set_exception_handler(function ($ex) {
    error_log((string)$ex);
    if (!headers_sent()) http_response_code(500);
    include __DIR__ . '/500.php';
    exit;
});

// ===== ฐานข้อมูล =====
define('DB_HOST', '__DB_HOST__');
define('DB_NAME', '__DB_NAME__');
define('DB_USER', '__DB_USER__');
define('DB_PASS', '__DB_PASS__');

// ===== ร้านค้า =====
define('BASE_URL', '__BASE_URL__');      // โฟลเดอร์โปรเจกต์ เช่น /shoe-shop
define('SITE_URL', '__SITE_URL__');      // URL เต็ม (ใช้ตอนกลับจากหน้า 3-D Secure)
define('PROMPTPAY_ID', '0812345678');    // เบอร์/เลขบัตรพร้อมเพย์ของร้าน
define('SHIPPING_FEE', 40);
define('VAT_RATE', 0.07);                 // ภาษีมูลค่าเพิ่ม 7% (ราคาสินค้าไม่รวม VAT คิดเพิ่มตอนชำระเงิน)
define('SHOP_LAT', 13.7563);             // พิกัดร้าน (ต้นทางพัสดุ)
define('SHOP_LNG', 100.5018);
define('SHOP_PHONE', '062565580');       // เบอร์โทรติดต่อร้าน
define('SHOP_EMAIL', 'jarinya@gmail.com');
define('SHOP_POSTCODE', '11220');

// ===== เครื่องมือวัดผล (ใส่ ID แล้วระบบจะโหลดหลังผู้ใช้กดยอมรับคุกกี้ ตาม PDPA) =====
define('GA_ID', '');                     // Google Analytics 4 เช่น G-XXXXXXXXXX
define('FB_PIXEL_ID', '');               // Facebook Pixel เช่น 1234567890123456

// ===== โซเชียลมีเดียของร้าน (ใส่ลิงก์เต็ม ถ้าเว้นว่างจะไม่แสดงไอคอน) =====
define('SOCIAL_FACEBOOK', '');
define('SOCIAL_INSTAGRAM', '');
define('SOCIAL_TIKTOK', '');
define('SOCIAL_YOUTUBE', '');
define('SOCIAL_LINE', '');
define('PDPA_CONTACT', SHOP_EMAIL);      // อีเมลสำหรับผู้ใช้ขอใช้สิทธิตาม PDPA

// ===== Omise (ถ้ายังไม่ใส่ ระบบจะใช้ QR + แนบสลิปแทน) =====
define('OMISE_PUBLIC_KEY', 'pkey_test_xxx');
define('OMISE_SECRET_KEY', 'skey_test_xxx');

$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// ===== ป้องกัน CSRF (ทุกฟอร์ม POST จะได้ token อัตโนมัติจาก footer) =====
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !defined('NO_CSRF')
    && !hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) {
    http_response_code(419); exit('หน้าเว็บหมดอายุ กรุณารีเฟรชแล้วลองใหม่');
}

const STATUS = [
    'pending'   => 'รอชำระเงิน',
    'verifying' => 'รอร้านค้าตรวจสอบ',
    'confirmed' => 'กำลังเตรียมสินค้า',
    'shipping'  => 'กำลังจัดส่ง',
    'delivered' => 'จัดส่งสำเร็จ',
    'cancelled' => 'ยกเลิกแล้ว',
];
const STATUS_COLOR = [
    'pending'=>'warning','verifying'=>'info','confirmed'=>'primary',
    'shipping'=>'pink','delivered'=>'success','cancelled'=>'secondary',
];
const PAY = ['promptpay' => 'พร้อมเพย์', 'card' => 'บัตรเครดิต', 'cod' => 'เก็บเงินปลายทาง'];
const DEFECT_REASONS = ['damaged' => 'สินค้าแตก/ฉีกขาด', 'defect' => 'งานผลิตมีตำหนิ', 'dirty' => 'สินค้าสกปรก/มีรอย',
                        'wrong' => 'ได้รับสินค้าไม่ตรงที่สั่ง', 'other' => 'อื่น ๆ'];
const DEFECT_STATUS = ['open' => 'รอตรวจสอบ', 'reviewing' => 'กำลังตรวจสอบ', 'resolved' => 'แก้ไขแล้ว', 'rejected' => 'ไม่อนุมัติ'];
const DEFECT_COLOR = ['open' => 'warning', 'reviewing' => 'info', 'resolved' => 'success', 'rejected' => 'secondary'];
function defect_badge($s) { return '<span class="badge bg-' . DEFECT_COLOR[$s] . '">' . DEFECT_STATUS[$s] . '</span>'; }

function e($s) { return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
function money($n) { return '฿' . number_format((float)$n, 2); }
function calc_vat($base) { return round(max((float)$base, 0) * VAT_RATE, 2); }   // VAT คิดจากยอดสินค้าหลังหักส่วนลด
function user() { return $_SESSION['user'] ?? null; }
function img($f) { return $f ? BASE_URL . '/uploads/' . $f : BASE_URL . '/uploads/products/default.svg'; }
function badge($s) { return '<span class="badge bg-' . STATUS_COLOR[$s] . '">' . STATUS[$s] . '</span>'; }
function redirect($url) { header("Location: $url"); exit; }
function not_found() {                   // หน้า 404 สวยงาม (ไม่ใช่หน้าขาว)
    global $pdo;
    include __DIR__ . '/404.php';
    exit;
}

function require_login() {
    if (!user()) { $_SESSION['redirect'] = $_SERVER['REQUEST_URI']; redirect(BASE_URL . '/login.php'); }
}
function require_admin() {
    if ((user()['role'] ?? '') !== 'admin') redirect(BASE_URL . '/login.php');
}
function require_rider() {
    if (!in_array(user()['role'] ?? '', ['rider', 'admin'])) redirect(BASE_URL . '/login.php');
}

function flash($msg = null, $type = 'success') {
    if ($msg) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}

// ===== PDPA =====
define('PDPA_VERSION', '1.0');           // เปลี่ยนเลขนี้เมื่อแก้นโยบายความเป็นส่วนตัว
define('UPLOAD_MAX_BYTES', 5 * 1024 * 1024);

function pdpa_log($pdo, $userId, $purpose) {
    $pdo->prepare("INSERT INTO pdpa_consents(user_id,purpose,version,ip) VALUES(?,?,?,?)")
        ->execute([$userId, $purpose, PDPA_VERSION, $_SERVER['REMOTE_ADDR'] ?? null]);
}

// ===== อัปโหลดรูป: ตรวจว่าเป็นรูปภาพจริง + ลบข้อมูลแฝง (EXIF/GPS) ตาม PDPA =====
$GLOBALS['upload_error'] = '';
function upload_error() { return $GLOBALS['upload_error']; }

function upload_image($field, $sub = '') {
    $f = $_FILES[$field] ?? null;
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    $fail = function ($m) { $GLOBALS['upload_error'] = $m; return null; };
    if ($f['error'] !== UPLOAD_ERR_OK) return $fail('อัปโหลดไฟล์ไม่สำเร็จ กรุณาลองใหม่');
    if (!is_uploaded_file($f['tmp_name'])) return $fail('ไฟล์ไม่ถูกต้อง');
    if ($f['size'] > UPLOAD_MAX_BYTES) return $fail('ไฟล์ใหญ่เกิน 5 MB');

    // 1) ตรวจ MIME จากเนื้อไฟล์จริง (ไม่เชื่อนามสกุล / Content-Type ที่ผู้ใช้ส่งมา)
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = class_exists('finfo') ? (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']) : (@getimagesize($f['tmp_name'])['mime'] ?? '');
    if (!isset($types[$mime])) return $fail('ไฟล์นี้ไม่ใช่รูปภาพจริง (รองรับเฉพาะ JPG, PNG, WEBP)');

    // 2) ตรวจโครงสร้างภาพ: ต้องอ่านขนาดได้ และชนิดต้องตรงกับ MIME
    $info = @getimagesize($f['tmp_name']);
    if (!$info || $info['mime'] !== $mime || $info[0] < 1 || $info[1] < 1) return $fail('ไฟล์รูปภาพเสียหายหรือไม่ใช่รูปจริง');
    if ($info[0] * $info[1] > 40000000) return $fail('ความละเอียดรูปสูงเกินไป');

    // 3) วาดภาพใหม่ด้วย GD: พิสูจน์ว่า decode เป็นภาพได้จริง และทิ้ง EXIF/GPS/โค้ดแฝงทั้งหมด
    $dir = __DIR__ . '/uploads/' . ($sub ? "$sub/" : '');
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $prefix = ['products' => 'product', 'slips' => 'payment-slip', 'defects' => 'defect-photo'][$sub] ?? 'image';
    $name = $prefix . '-' . date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $types[$mime];
    $dest = $dir . $name;

    if (function_exists('imagecreatefromstring')) {
        $im = @imagecreatefromstring((string)file_get_contents($f['tmp_name']));
        if (!$im) return $fail('ไม่สามารถอ่านไฟล์เป็นรูปภาพได้ (ไม่ใช่รูปจริง)');
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {          // หมุนภาพตาม EXIF ก่อนทิ้งข้อมูล
            $ex = @exif_read_data($f['tmp_name']);
            $rot = [3 => 180, 6 => -90, 8 => 90][$ex['Orientation'] ?? 1] ?? 0;
            if ($rot && ($r = imagerotate($im, $rot, 0))) { imagedestroy($im); $im = $r; }
        }
        if ($mime !== 'image/jpeg') { imagealphablending($im, false); imagesavealpha($im, true); }
        $ok = match ($mime) {
            'image/jpeg' => imagejpeg($im, $dest, 88),
            'image/png'  => imagepng($im, $dest, 6),
            'image/webp' => function_exists('imagewebp') && imagewebp($im, $dest, 88),
        };
        imagedestroy($im);
        if (!$ok) { @unlink($dest); return $fail('บันทึกรูปไม่สำเร็จ'); }
    } elseif (!move_uploaded_file($f['tmp_name'], $dest)) {
        return $fail('บันทึกรูปไม่สำเร็จ');
    }
    @chmod($dest, 0644);
    return ($sub ? "$sub/" : '') . $name;
}

// อัปโหลดหลายรูป (ช่อง img1..imgN) ผ่านการตรวจรูปจริงทุกไฟล์ : คืน array ของพาธ, [] ถ้าไม่ได้เลือกรูป, null ถ้ามีไฟล์ไม่ผ่าน
function upload_images($prefix, $n, $sub) {
    $out = [];
    for ($i = 1; $i <= $n; $i++) {
        if (empty($_FILES[$prefix . $i]['name'])) continue;
        $f = upload_image($prefix . $i, $sub);
        if (!$f) {
            foreach ($out as $o) @unlink(__DIR__ . '/uploads/' . $o);
            if (!upload_error()) $GLOBALS['upload_error'] = 'อัปโหลดรูปไม่สำเร็จ';
            return null;
        }
        $out[] = $f;
    }
    return $out;
}

function cart_items($pdo) {
    $items = []; $total = 0;
    $st = $pdo->prepare("SELECT * FROM products WHERE id=?");
    foreach ($_SESSION['cart'] ?? [] as $key => $qty) {
        [$id, $size] = explode('|', $key, 2);
        $st->execute([$id]);
        if ($p = $st->fetch()) {
            if ($p['sale_price'] !== null) $p['price'] = $p['sale_price'];     // ราคาโปรโมชั่น
            $p += ['key'=>$key, 'size'=>$size, 'qty'=>$qty, 'subtotal'=>$p['price'] * $qty];
            $total += $p['subtotal']; $items[] = $p;
        }
    }
    return [$items, $total];
}

function get_order($pdo, $id) {
    $st = $pdo->prepare("SELECT * FROM orders WHERE id=?"); $st->execute([$id]); $o = $st->fetch();
    if (!$o || ($o['user_id'] != user()['id'] && user()['role'] !== 'admin')) { not_found(); }
    return $o;
}

function set_status($pdo, $oid, $status, $note = '', $lat = null, $lng = null) {
    $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status, $oid]);
    $pdo->prepare("INSERT INTO order_tracking(order_id,status,note,lat,lng) VALUES(?,?,?,?,?)")
        ->execute([$oid, $status, $note, $lat ?: null, $lng ?: null]);
}

// ===== คูปอง: คืนค่า array ถ้าใช้ได้ หรือข้อความ error =====
function check_coupon($pdo, $code, $subtotal) {
    $st = $pdo->prepare("SELECT * FROM coupons WHERE code=? AND active=1");
    $st->execute([strtoupper(trim($code))]);
    $c = $st->fetch();
    if (!$c) return 'ไม่พบคูปองนี้';
    if ($c['expires_at'] && $c['expires_at'] < date('Y-m-d')) return 'คูปองหมดอายุแล้ว';
    if ($c['max_uses'] !== null && $c['used'] >= $c['max_uses']) return 'คูปองถูกใช้ครบจำนวนแล้ว';
    if ($subtotal < $c['min_total']) return 'ต้องซื้อขั้นต่ำ ' . money($c['min_total']);
    $d = $c['type'] === 'percent' ? $subtotal * $c['value'] / 100 : $c['value'];
    $c['discount'] = round(min($d, $subtotal), 2);
    return $c;
}

// ===== ดาวรีวิว =====
function stars($n) {
    $h = '';
    for ($i = 1; $i <= 5; $i++)
        $h .= '<i class="bi bi-star' . ($n >= $i ? '-fill' : ($n >= $i - 0.5 ? '-half' : '')) . ' star-on"></i>';
    return $h;
}

// ===== Omise API =====
function omise_on() { return OMISE_SECRET_KEY !== '' && !str_contains(OMISE_SECRET_KEY, 'xxx'); }

function omise($method, $path, $data = []) {
    $ch = curl_init('https://api.omise.co' . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_USERPWD => OMISE_SECRET_KEY . ':',
        CURLOPT_CUSTOMREQUEST => $method, CURLOPT_TIMEOUT => 30,
    ]);
    if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $res = json_decode(curl_exec($ch) ?: '[]', true) ?: [];
    curl_close($ch);
    return $res;
}

// ยืนยันการชำระเงิน: เช็ก charge id + สถานะ + ยอดเงินให้ตรงกัน
function mark_paid($pdo, $oid, $ch) {
    if (($ch['object'] ?? '') !== 'charge' || $ch['status'] !== 'successful') return false;
    $st = $pdo->prepare("UPDATE orders SET status='confirmed'
                         WHERE id=? AND status='pending' AND charge_id=? AND ROUND(total*100)=?");
    $st->execute([$oid, $ch['id'], $ch['amount']]);
    if (!$st->rowCount()) return false;
    $pdo->prepare("INSERT INTO order_tracking(order_id,status,note) VALUES(?, 'confirmed', ?)")
        ->execute([$oid, 'ชำระเงินสำเร็จผ่าน Omise (' . $ch['id'] . ')']);
    return true;
}
PINKY_EOF;

/* ==================== database.sql ==================== */
$files['database.sql'] = <<<'PINKY_EOF'
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  role ENUM('member','admin','rider') DEFAULT 'member',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  sizes VARCHAR(100) DEFAULT '36,37,38,39,40,41,42',
  image VARCHAR(255),
  audience VARCHAR(30) NULL,
  sale_price DECIMAL(10,2) NULL,
  sold INT DEFAULT 0,
  rating_avg DECIMAL(2,1) DEFAULT 0,
  rating_count INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  receiver VARCHAR(100), phone VARCHAR(20), address TEXT,
  lat DECIMAL(10,7), lng DECIMAL(10,7),
  total DECIMAL(10,2) NOT NULL,
  discount DECIMAL(10,2) DEFAULT 0,
  vat DECIMAL(10,2) DEFAULT 0,
  coupon_code VARCHAR(50) NULL,
  payment_method ENUM('promptpay','card','cod') NOT NULL,
  charge_id VARCHAR(100) NULL,
  slip VARCHAR(255),
  tracking_no VARCHAR(50),
  rider_id INT NULL,
  cur_lat DECIMAL(10,7) NULL, cur_lng DECIMAL(10,7) NULL, cur_at DATETIME NULL,
  status ENUM('pending','verifying','confirmed','shipping','delivered','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (charge_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (rider_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT,
  name VARCHAR(200), size VARCHAR(10),
  qty INT NOT NULL, price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_tracking (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  status VARCHAR(20), note VARCHAR(255),
  lat DECIMAL(10,7), lng DECIMAL(10,7),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  type ENUM('percent','fixed') NOT NULL,
  value DECIMAL(10,2) NOT NULL,
  min_total DECIMAL(10,2) DEFAULT 0,
  max_uses INT NULL,
  used INT DEFAULT 0,
  expires_at DATE NULL,
  active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  order_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY one_review (order_id, product_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pdpa_consents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  purpose VARCHAR(50) NOT NULL,
  version VARCHAR(10) NOT NULL,
  ip VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS defect_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_id INT NULL,
  product_name VARCHAR(200), size VARCHAR(10),
  reason VARCHAR(20) NOT NULL,
  status ENUM('open','reviewing','resolved','rejected') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (user_id), INDEX (status),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS defect_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  report_id INT NOT NULL,
  sender ENUM('customer','admin') NOT NULL,
  message TEXT,
  images TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (report_id),
  FOREIGN KEY (report_id) REFERENCES defect_reports(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO coupons (code,type,value,min_total) VALUES
('PINK10','percent',10,500), ('WELCOME50','fixed',50,0);
PINKY_EOF;

/* ==================== assets/style.css ==================== */
$files['assets/style.css'] = <<<'PINKY_EOF'
:root{--pink:#ffd1dc;--pink-light:#fff5f8;--pink-dark:#ff6b9d;--pink-deep:#e84a7f}
body{font-family:'Prompt',sans-serif;background:var(--pink-light);color:#444}
a{color:var(--pink-deep)}
.topbar{background:linear-gradient(90deg,var(--pink-dark),#ffa3c0)}
.topbar .navbar{padding:.7rem 0}
.brand{color:#fff;font-weight:600;font-size:1.5rem;text-decoration:none;white-space:nowrap}
.btn-pink{background:var(--pink-deep);color:#fff;border:none}
.btn-pink:hover{background:#d13a6d;color:#fff}
.btn-outline-pink{border:1px solid var(--pink-deep);color:var(--pink-deep);background:var(--pink-light)}
.btn-outline-pink:hover{background:var(--pink);color:var(--pink-deep)}
.bg-pink{background:var(--pink-deep)!important}
.badge-count{position:absolute;top:-6px;right:-10px;background:#fff;color:var(--pink-deep);border-radius:10px;font-size:.7rem;padding:1px 6px;font-weight:600}
.infobar{background:#fff;border-bottom:1px solid var(--pink);font-size:.8rem;color:#666;padding:.4rem 0}
.infobar i{color:var(--pink-deep);margin-right:.35rem}
.infobar .social a{color:#777;margin-left:.9rem;font-size:1rem}.infobar .social a:hover{color:var(--pink-deep)}
.topbar .nav-link{color:#fff!important;font-weight:500;padding:.45rem 1rem!important;border-radius:999px;transition:.15s}
.topbar .nav-link:hover{background:rgba(255,255,255,.22)}
.topbar .nav-link.active{background:#fff;color:var(--pink-deep)!important}
.topbar .navbar-toggler{border-color:rgba(255,255,255,.7)}
.search-pill{background:#fff;border-radius:999px;padding:.1rem .35rem .1rem 1rem;min-width:210px}
.search-pill input{border:none;box-shadow:none;background:transparent;padding:.35rem 0;font-size:.9rem}
.search-pill button{border:none;background:transparent;color:var(--pink-deep);padding:.3rem .6rem}
.catmenu{max-height:70vh;overflow:auto;min-width:230px;border:1px solid var(--pink)}
.catmenu .dropdown-item.active,.catmenu .dropdown-item:active{background:var(--pink-deep);color:#fff}
.hero{border-radius:14px;overflow:hidden;background:linear-gradient(135deg,var(--pink),#fff);position:relative}
.hero-slide{display:flex;align-items:center;justify-content:space-between;gap:1.5rem;padding:2.2rem 4rem;min-height:340px}
.hero-eyebrow{letter-spacing:.35em;font-size:.8rem;color:#999;margin-bottom:.4rem}
.hero-text h1{font-size:clamp(1.8rem,4.2vw,3.2rem);font-weight:600;color:var(--pink-deep);letter-spacing:.03em;line-height:1.15;margin-bottom:.6rem}
.hero-text p{color:#666;margin-bottom:1.2rem}
.btn-hero{border:2px solid var(--pink-deep);color:var(--pink-deep);border-radius:999px;padding:.5rem 1.6rem;background:#fff;font-weight:500}
.btn-hero:hover{background:var(--pink-deep);color:#fff}
.hero-img{width:min(300px,38%);aspect-ratio:1;object-fit:cover;border-radius:24px;background:#fff;box-shadow:0 10px 28px rgba(232,74,127,.25)}
.hero .ctrl{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid var(--pink-dark);color:var(--pink-deep);z-index:5;display:flex;align-items:center;justify-content:center}
.hero .ctrl:hover{background:var(--pink-deep);color:#fff}
.hero .ctrl.prev{left:14px}.hero .ctrl.next{right:14px}
.hero .carousel-indicators{margin-bottom:.8rem}
.hero .carousel-indicators [data-bs-target]{background-color:var(--pink-deep);width:10px;height:10px;border-radius:50%;border:0;margin:0 4px}
.feat{background:#fff;border-radius:12px;margin-top:1rem;box-shadow:0 2px 10px rgba(232,74,127,.08)}
.feat .it{display:flex;align-items:center;gap:.9rem;padding:1.1rem 1.3rem}
.feat i{font-size:2rem;color:var(--pink-deep)}
.feat .t{font-weight:600;font-size:.95rem;line-height:1.3}.feat .s{font-size:.8rem;color:#777}
.sec-title{font-weight:600;font-size:1.5rem;margin:0}
.sec-title .sl{color:var(--pink-deep);font-style:italic;letter-spacing:-3px;margin:0 .5rem}
.btn-all{border:1px solid #bbb;border-radius:999px;padding:.35rem 1.1rem;color:#444;background:#fff;font-size:.9rem;text-decoration:none}
.btn-all:hover{border-color:var(--pink-deep);color:var(--pink-deep)}
.pc-link{display:block;color:#333;text-decoration:none}
.promo-card{display:flex;align-items:center;justify-content:space-between;gap:1rem;border-radius:14px;padding:1.4rem 2rem;min-height:170px;overflow:hidden}
.promo-card img{width:150px;height:150px;object-fit:cover;border-radius:16px;background:#fff}
.promo-hot{background:var(--pink-deep);color:#fff}
.promo-new{background:var(--pink);color:var(--pink-deep)}
.pc-eyebrow{letter-spacing:.3em;font-size:.8rem;opacity:.85}
.pc-big{font-size:2rem;font-weight:600;line-height:1.15;margin:.2rem 0 .9rem}
.sitefoot{background:#fff;border-top:3px solid var(--pink);color:#555}
.sitefoot h6{font-weight:600;color:#333;margin-bottom:.8rem}
.brand-f{color:var(--pink-deep);font-weight:600;font-size:1.4rem}
.footlinks li{margin-bottom:.35rem}.footlinks a{color:#555;text-decoration:none}.footlinks a:hover{color:var(--pink-deep)}
.footlinks i{color:var(--pink-deep);margin-right:.3rem}
.footsocial a{color:#777;font-size:1.2rem;margin-right:.9rem}.footsocial a:hover{color:var(--pink-deep)}
.newsletter{position:relative;display:flex}
.newsletter input[type=email]{border-radius:999px;padding-right:3.4rem;border-color:var(--pink)}
.newsletter button{position:absolute;right:4px;top:4px;bottom:4px;width:40px;border:none;border-radius:50%;background:var(--pink-deep);color:#fff}
.subfoot{background:var(--pink-light);border-top:1px solid var(--pink);padding:.8rem 0;color:#777}
.subfoot a{color:#777;text-decoration:none}.subfoot a:hover{color:var(--pink-deep)}
@media(max-width:767px){.hero-slide{padding:1.6rem 1.4rem 2.6rem;flex-direction:column-reverse;text-align:center;min-height:0}.hero-img{width:55%}.hero .ctrl{display:none}.promo-card img{width:100px;height:100px}}
.card-box{background:#fff;border-radius:12px;padding:1.25rem;box-shadow:0 2px 10px rgba(232,74,127,.08)}
.banner{background:linear-gradient(135deg,var(--pink),#fff);border-radius:14px;padding:2.5rem}
.product-card{display:block;background:#fff;border-radius:8px;overflow:hidden;height:100%;color:#333;text-decoration:none;border:1px solid transparent;transition:.2s}
.product-card:hover{border-color:var(--pink-dark);transform:translateY(-3px);box-shadow:0 6px 14px rgba(232,74,127,.2)}
.product-card img{display:block;width:calc(100% - 16px);margin:8px 8px 0;aspect-ratio:4/3;object-fit:cover;border-radius:8px}
.product-card .name{font-size:.85rem;height:2.6em;overflow:hidden;line-height:1.3}
.product-detail-img{max-width:420px;margin:0 auto;display:block}
.price{color:var(--pink-deep);font-weight:600}
.size-btn input{display:none}
.size-btn span{display:inline-block;border:1px solid #ddd;padding:.35rem .9rem;border-radius:4px;cursor:pointer;margin:0 .3rem .4rem 0}
.size-btn input:checked+span{border-color:var(--pink-deep);color:var(--pink-deep);background:var(--pink-light)}
.map{height:400px;border-radius:12px;z-index:0}
.map-pin{width:34px;height:34px;border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3)}
.timeline{list-style:none;padding-left:1rem;border-left:3px solid var(--pink);margin:0}
.timeline li{position:relative;padding:0 0 1rem 1rem}
.timeline li::before{content:'';position:absolute;left:-1.55rem;top:.35rem;width:14px;height:14px;border-radius:50%;background:var(--pink-deep);border:3px solid #fff}
.stepper{display:flex;justify-content:space-between}
.stepper .step{text-align:center;flex:1;color:#bbb;font-size:.85rem}
.stepper .step i{font-size:1.6rem;display:block}
.stepper .step.done{color:var(--pink-deep)}
.star-on{color:#ffb400}
.stars{display:inline-flex;flex-direction:row-reverse}
.stars input{display:none}
.stars label{font-size:1.8rem;color:#ddd;cursor:pointer;padding:0 2px}
.stars input:checked~label,.stars label:hover,.stars label:hover~label{color:#ffb400}
.coupon-tag{border:1px dashed var(--pink-deep);color:var(--pink-deep);background:var(--pink-light);padding:.2rem .6rem;border-radius:4px}
.job-card{border-left:5px solid var(--pink-deep)}
footer{background:#fff;border-top:3px solid var(--pink)}
PINKY_EOF;

/* ==================== includes/header.php ==================== */
$files['includes/header.php'] = <<<'PINKY_EOF'
<?php
$cartCount = array_sum($_SESSION['cart'] ?? []);
// ---- SEO: title / meta description / robots / canonical ----
$self = basename($_SERVER['PHP_SELF']); $dir = basename(dirname($_SERVER['PHP_SELF']));
$noindex = !empty($noindex) || in_array($dir, ['admin', 'rider'])
    || in_array($self, ['cart.php','checkout.php','payment.php','orders.php','track.php','review.php','defect.php','defect_view.php','login.php','register.php','404.php','500.php','newsletter.php']);
$pageTitle = $title ?? 'Sneaks Store';
$fullTitle = mb_stripos($pageTitle, 'Sneaks') !== false ? $pageTitle : $pageTitle . ' | Sneaks Store';
$metaDesc = trim(preg_replace('/\s+/u', ' ', $desc ?? ($pageTitle . ' - Sneaks Store ร้านรองเท้าออนไลน์ รองเท้าผ้าใบ รองเท้าวิ่ง ส้นสูง ส่งไวทั่วไทย ชำระพร้อมเพย์ได้ ติดตามพัสดุบนแผนที่')));
if (mb_strlen($metaDesc) > 160) $metaDesc = mb_substr($metaDesc, 0, 157) . '...';
$canon = str_replace(' ', '%20', SITE_URL) . substr(rawurldecode(strtok($_SERVER['REQUEST_URI'], '?')), strlen(BASE_URL));
if ($self === 'product.php' && !empty($_GET['id'])) $canon .= '?id=' . (int)$_GET['id'];
elseif ($self === 'index.php' && !empty($_GET['cat'])) $canon .= '?cat=' . (int)$_GET['cat'];
$cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();
$f = flash();
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($metaDesc) ?>">
<meta name="robots" content="<?= $noindex ? 'noindex,nofollow' : 'index,follow' ?>">
<link rel="canonical" href="<?= e($canon) ?>">
<meta name="theme-color" content="#e84a7f">
<meta property="og:type" content="website"><meta property="og:site_name" content="Sneaks Store">
<meta property="og:title" content="<?= e($fullTitle) ?>"><meta property="og:description" content="<?= e($metaDesc) ?>">
<meta property="og:url" content="<?= e($canon) ?>">
<?php if (!empty($ogImage)): ?><meta property="og:image" content="<?= e($ogImage) ?>"><?php endif; ?>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%91%9F%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<?php if (!empty($needMap)): ?><link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"><?php endif; ?>
<link href="<?= BASE_URL ?>/assets/style.css" rel="stylesheet">
<?php if (!empty($needMap)): ?><script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script><?php endif; ?>
</head>
<body>
<div class="infobar d-none d-md-block"><div class="container d-flex justify-content-between align-items-center gap-3">
  <div class="d-flex gap-4 flex-wrap">
    <span><i class="bi bi-geo-alt"></i> ติดตามพัสดุบนแผนที่ได้ทุกออเดอร์</span>
    <span><i class="bi bi-shield-check"></i> ชำระเงินปลอดภัย พร้อมเพย์ / บัตร</span>
    <span><i class="bi bi-headset"></i> บริการหลังการขาย โทร <?= e(SHOP_PHONE) ?></span>
  </div>
  <div class="social">
    <?php foreach (['facebook' => SOCIAL_FACEBOOK, 'instagram' => SOCIAL_INSTAGRAM, 'tiktok' => SOCIAL_TIKTOK, 'youtube' => SOCIAL_YOUTUBE, 'line' => SOCIAL_LINE] as $ic => $url): if ($url): ?>
      <a href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= $ic ?>"><i class="bi bi-<?= $ic ?>"></i></a>
    <?php endif; endforeach; ?>
  </div>
</div></div>
<?php
$onIndex = $self === 'index.php' && $dir !== 'admin';
$isPromo = $onIndex && !empty($_GET['promo']);
$isHome  = $onIndex && !$isPromo && empty($_GET['cat']) && empty($_GET['q']) && empty($_GET['sort']);
$isProd  = ($onIndex && !$isPromo && !$isHome) || $self === 'product.php';
$rank = function ($n) { foreach (['ผู้ชาย', 'ผู้หญิง', 'เด็ก', 'โปรโมชั่น'] as $i => $k) if (mb_strpos($n, $k) !== false) return $i; return 9; };
usort($cats, fn($a, $b) => $rank($a['name']) <=> $rank($b['name']));
?>
<header class="topbar">
  <nav class="navbar navbar-expand-lg navbar-dark"><div class="container">
    <a href="<?= BASE_URL ?>/" class="brand"><i class="bi bi-bag-heart-fill"></i> Sneaks Store</a>
    <a href="<?= BASE_URL ?>/cart.php" class="text-white position-relative order-lg-last ms-auto ms-lg-3 me-3 me-lg-0" aria-label="ตะกร้าสินค้า">
      <i class="bi bi-cart3 fs-4"></i>
      <?php if ($cartCount): ?><span class="badge-count"><?= $cartCount ?></span><?php endif; ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-label="เมนู"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-lg-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link <?= $isHome ? 'active' : '' ?>" href="<?= BASE_URL ?>/">หน้าหลัก</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= $isProd ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown" aria-expanded="false">สินค้า</a>
          <ul class="dropdown-menu catmenu">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php?sort=latest">สินค้าทั้งหมด</a></li>
            <li><hr class="dropdown-divider"></li>
            <?php foreach ($cats as $c): ?>
              <li><a class="dropdown-item <?= ($_GET['cat'] ?? '') == $c['id'] ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?cat=<?= $c['id'] ?>"><?= e($c['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?= $isPromo ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?promo=1">โปรโมชั่น</a></li>
        <li class="nav-item"><a class="nav-link <?= $self === 'about.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/about.php">เกี่ยวกับเรา</a></li>
        <li class="nav-item"><a class="nav-link <?= $self === 'contact.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/contact.php">ติดต่อเรา</a></li>
      </ul>
      <form class="search-pill d-flex align-items-center my-2 my-lg-0" action="<?= BASE_URL ?>/index.php">
        <input name="q" class="form-control" placeholder="ค้นหารองเท้า..." value="<?= e($_GET['q'] ?? '') ?>" aria-label="ค้นหาสินค้า">
        <button aria-label="ค้นหา"><i class="bi bi-search"></i></button>
      </form>
      <ul class="navbar-nav ms-lg-2">
      <?php if (user()): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> <span class="d-lg-none d-xl-inline"><?= e(user()['name']) ?></span></a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/orders.php"><i class="bi bi-box-seam"></i> คำสั่งซื้อของฉัน</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/defect.php"><i class="bi bi-exclamation-triangle"></i> แจ้งสินค้าชำรุด</a></li>
            <?php if (in_array(user()['role'], ['rider', 'admin'])): ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/rider/"><i class="bi bi-scooter"></i> งานจัดส่ง</a></li>
            <?php endif; ?>
            <?php if (user()['role'] === 'admin'): ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/"><i class="bi bi-speedometer2"></i> ระบบหลังบ้าน</a></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">ออกจากระบบ</a></li>
          </ul>
        </li>
      <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php"><i class="bi bi-person"></i> เข้าสู่ระบบ</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/register.php">สมัครสมาชิก</a></li>
      <?php endif; ?>
      </ul>
    </div>
  </div></nav>
</header>
<main class="container py-4">
<?php if ($f): ?><div class="alert alert-<?= $f[1] ?> alert-dismissible fade show"><?= e($f[0]) ?>
  <button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
PINKY_EOF;

/* ==================== includes/footer.php ==================== */
$files['includes/footer.php'] = <<<'PINKY_EOF'
</main>
<footer class="sitefoot mt-5">
  <div class="container py-4"><div class="row g-4">
    <div class="col-lg-3">
      <div class="brand-f"><i class="bi bi-bag-heart-fill"></i> Sneaks Store</div>
      <p class="small text-muted mt-2 mb-0">More Than Just Sneakers.<br>ร้านรองเท้าออนไลน์ ส่งไวทั่วไทย</p>
    </div>
    <div class="col-6 col-lg-2">
      <h6>เมนูหลัก</h6>
      <ul class="list-unstyled small footlinks">
        <li><a href="<?= BASE_URL ?>/">หน้าหลัก</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?sort=latest">สินค้า</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?promo=1">โปรโมชั่น</a></li>
        <li><a href="<?= BASE_URL ?>/about.php">เกี่ยวกับเรา</a></li>
        <li><a href="<?= BASE_URL ?>/contact.php">ติดต่อเรา</a></li>
      </ul>
    </div>
    <div class="col-6 col-lg-3">
      <h6>ช่องทางการติดต่อ</h6>
      <ul class="list-unstyled small footlinks mb-2">
        <li><i class="bi bi-telephone"></i> <a href="tel:<?= e(SHOP_PHONE) ?>"><?= e(SHOP_PHONE) ?></a></li>
        <li><i class="bi bi-envelope"></i> <a href="mailto:<?= e(SHOP_EMAIL) ?>"><?= e(SHOP_EMAIL) ?></a></li>
        <li><i class="bi bi-mailbox"></i> รหัสไปรษณีย์ <?= e(SHOP_POSTCODE) ?></li>
      </ul>
      <div class="footsocial">
        <?php foreach (['facebook' => SOCIAL_FACEBOOK, 'instagram' => SOCIAL_INSTAGRAM, 'tiktok' => SOCIAL_TIKTOK, 'youtube' => SOCIAL_YOUTUBE, 'line' => SOCIAL_LINE] as $ic => $url): if ($url): ?>
          <a href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= $ic ?>"><i class="bi bi-<?= $ic ?>"></i></a>
        <?php endif; endforeach; ?>
      </div>
    </div>
    <div class="col-lg-4">
      <h6>สมัครรับข่าวสาร</h6>
      <p class="small text-muted mb-2">รับโปรโมชั่นและสินค้าใหม่ก่อนใคร</p>
      <form method="post" action="<?= BASE_URL ?>/newsletter.php" class="newsletter">
        <input type="email" name="email" class="form-control" placeholder="กรอกอีเมลของคุณ" required aria-label="อีเมลสำหรับรับข่าวสาร">
        <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
        <button aria-label="สมัครรับข่าวสาร"><i class="bi bi-arrow-right"></i></button>
      </form>
      <small class="text-muted d-block mt-1" style="font-size:.75rem">การสมัครถือว่ายินยอมให้ใช้อีเมลเพื่อส่งข่าวสาร ตาม<a href="<?= BASE_URL ?>/privacy.php">นโยบาย PDPA</a> ยกเลิกได้ตลอดเวลา</small>
    </div>
  </div></div>
  <div class="subfoot"><div class="container d-flex flex-wrap justify-content-between gap-2 small">
    <span>© <?= date('Y') ?> Sneaks Store. สงวนสิทธิ์ทุกประการ</span>
    <span><a href="<?= BASE_URL ?>/privacy.php">นโยบายความเป็นส่วนตัว (PDPA)</a> &nbsp;|&nbsp; <a href="<?= BASE_URL ?>/defect.php">แจ้งสินค้าชำรุด</a> &nbsp;|&nbsp; <a href="<?= BASE_URL ?>/contact.php">ติดต่อเรา</a></span>
  </div></div>
</footer>
<?php if (GA_ID || FB_PIXEL_ID): ?>
<div id="ckBar" class="position-fixed bottom-0 start-0 end-0 bg-white border-top shadow p-3" style="z-index:2000;display:none">
  <div class="container d-flex flex-wrap align-items-center gap-2 justify-content-between small">
    <span>เว็บไซต์นี้ใช้คุกกี้เพื่อวิเคราะห์การใช้งาน (<?= GA_ID ? 'Google Analytics' : '' ?><?= GA_ID && FB_PIXEL_ID ? ' / ' : '' ?><?= FB_PIXEL_ID ? 'Facebook Pixel' : '' ?>) อ่านรายละเอียดที่ <a href="<?= BASE_URL ?>/privacy.php">นโยบายความเป็นส่วนตัว (PDPA)</a></span>
    <span class="text-nowrap"><button id="ckNo" class="btn btn-sm btn-outline-secondary">ปฏิเสธ</button> <button id="ckYes" class="btn btn-sm btn-pink">ยอมรับ</button></span>
  </div>
</div>
<script>
(function () {
  const GA = <?= json_encode(GA_ID) ?>, FB = <?= json_encode(FB_PIXEL_ID) ?>;
  function load() {
    if (GA) {
      const s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + GA; document.head.appendChild(s);
      window.dataLayer = window.dataLayer || []; window.gtag = function () { dataLayer.push(arguments); };
      gtag('js', new Date()); gtag('config', GA, {anonymize_ip: true});
    }
    if (FB) {
      !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', FB); fbq('track', 'PageView');
    }
  }
  let c = null; try { c = localStorage.getItem('ck_consent'); } catch (e) {}
  if (c === 'yes') load();
  else if (!c) {
    const bar = document.getElementById('ckBar'); bar.style.display = 'block';
    const set = v => { try { localStorage.setItem('ck_consent', v); } catch (e) {} bar.remove(); if (v === 'yes') load(); };
    document.getElementById('ckYes').onclick = () => set('yes');
    document.getElementById('ckNo').onclick = () => set('no');
  }
})();
</script>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script>
document.querySelectorAll('form[method="post"]').forEach(f => {
  const i = document.createElement('input');
  i.type = 'hidden'; i.name = 'csrf'; i.value = '<?= $_SESSION['csrf'] ?>';
  f.appendChild(i);
});
</script>
</body></html>
PINKY_EOF;

/* ==================== index.php ==================== */
$files['index.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
$where = []; $params = [];
$catName = null; $catId = (int)($_GET['cat'] ?? 0);
if ($catId) {
    $s = $pdo->prepare("SELECT name FROM categories WHERE id=?"); $s->execute([$catId]); $catName = $s->fetchColumn() ?: null;
    $n = (string)$catName;
    // หมวดตามกลุ่มลูกค้า/โปรโมชั่น ดึงสินค้าอัตโนมัติ (รวมสินค้าที่ผูกกับหมวดนี้โดยตรงด้วย)
    if (preg_match('/โปรโมชั่น|โปรโมชัน|ลดราคา|sale/iu', $n)) { $where[] = '(p.sale_price IS NOT NULL OR p.category_id=?)'; $params[] = $catId; }
    elseif (mb_strpos($n, 'ผู้หญิง') !== false) { $where[] = "(FIND_IN_SET('women', p.audience) OR p.category_id=?)"; $params[] = $catId; }
    elseif (mb_strpos($n, 'ผู้ชาย') !== false)  { $where[] = "(FIND_IN_SET('men', p.audience) OR p.category_id=?)"; $params[] = $catId; }
    elseif (mb_strpos($n, 'เด็ก') !== false)    { $where[] = "(FIND_IN_SET('kids', p.audience) OR p.category_id=?)"; $params[] = $catId; }
    else { $where[] = 'p.category_id=?'; $params[] = $catId; }
}
if (!empty($_GET['q']))   { $where[] = 'p.name LIKE ?';   $params[] = '%' . $_GET['q'] . '%'; }
if (!empty($_GET['promo'])) $where[] = 'p.sale_price IS NOT NULL';
$sort = match ($_GET['sort'] ?? '') {
    'price_asc' => 'COALESCE(p.sale_price,p.price) ASC', 'price_desc' => 'COALESCE(p.sale_price,p.price) DESC', 'sold' => 'p.sold DESC', default => 'p.id DESC'
};
$st = $pdo->prepare("SELECT p.*, c.name cat FROM products p LEFT JOIN categories c ON c.id=p.category_id"
    . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY $sort");
$st->execute($params); $products = $st->fetchAll();
if ($catName) { $title = $catName . ' - Sneaks Store'; $desc = 'เลือกซื้อ' . $catName . ' คุณภาพดี ราคาดี มีหลายไซซ์ ส่งไวทั่วไทย ชำระพร้อมเพย์หรือบัตรได้ที่ Sneaks Store'; }
elseif (!empty($_GET['promo'])) { $title = 'โปรโมชั่น - Sneaks Store'; $desc = 'รวมรองเท้าลดราคาและโปรโมชั่นพิเศษ ราคาดี จำนวนจำกัด สั่งซื้อได้ที่ Sneaks Store ส่งไวทั่วไทย'; }
elseif (!empty($_GET['q'])) { $title = 'ค้นหา: ' . mb_substr($_GET['q'], 0, 40) . ' - Sneaks Store'; $desc = 'ผลการค้นหารองเท้า "' . mb_substr($_GET['q'], 0, 40) . '" พบ ' . count($products) . ' รายการที่ Sneaks Store'; }
else { $title = 'Sneaks Store - ร้านรองเท้าออนไลน์'; $desc = 'Sneaks Store ร้านรองเท้าออนไลน์ รองเท้าผ้าใบ รองเท้าวิ่ง ส้นสูง รองเท้าแตะ รองเท้าหนัง บูท ฟุตบอล บาสเกตบอล รองเท้าเด็ก และนักเรียน ส่งไวทั่วไทย ติดตามพัสดุบนแผนที่'; }
$home = !$catId && empty($_GET['q']) && empty($_GET['promo']) && empty($_GET['sort']);
$best = $newest = $promoTop = null; $maxPct = 0;
if ($home) {
    $base = "SELECT p.*, c.name cat FROM products p LEFT JOIN categories c ON c.id=p.category_id";
    $best = $pdo->query("$base ORDER BY p.sold DESC, p.id DESC LIMIT 5")->fetchAll();
    $newest = $pdo->query("$base ORDER BY p.id DESC LIMIT 1")->fetch();
    $promoTop = $pdo->query("SELECT p.*, c.name cat, ROUND(100 - p.sale_price / p.price * 100) pct FROM products p LEFT JOIN categories c ON c.id=p.category_id
                             WHERE p.sale_price IS NOT NULL ORDER BY pct DESC, p.id DESC LIMIT 1")->fetch();
    $maxPct = (int)($promoTop['pct'] ?? 0);
}
function product_card($p, $btn = false) { ?>
  <div class="product-card">
    <a href="product.php?id=<?= $p['id'] ?>" class="pc-link">
      <img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?> - <?= e($p['cat']) ?>" width="300" height="225" loading="lazy" decoding="async">
      <div class="p-2">
        <div class="name"><?= e($p['name']) ?></div>
        <?php if ($p['rating_count']): ?><div class="small"><?= stars($p['rating_avg']) ?> <span class="text-muted">(<?= (int)$p['rating_count'] ?>)</span></div><?php endif; ?>
        <div class="mt-1">
          <?php if ($p['sale_price'] !== null): ?>
            <span class="price"><?= money($p['sale_price']) ?></span> <s class="small text-muted"><?= money($p['price']) ?></s>
            <span class="badge bg-danger">-<?= round(100 - $p['sale_price'] / $p['price'] * 100) ?>%</span>
          <?php else: ?><span class="price"><?= money($p['price']) ?></span><?php endif; ?>
          <div class="small text-muted">ขายแล้ว <?= $p['sold'] ?></div>
        </div>
      </div>
    </a>
    <?php if ($btn): ?><div class="px-2 pb-2"><a href="product.php?id=<?= $p['id'] ?>" class="btn btn-pink btn-sm w-100"><i class="bi bi-cart-plus"></i> เพิ่มใส่ตะกร้า</a></div><?php endif; ?>
  </div>
<?php }
include 'includes/header.php'; ?>

<?php if ($home && $best): ?>
<?php
$slides = [[ 'eyebrow' => 'SNEAKS STORE', 'h' => 'STEP UP YOUR STYLE', 'p' => 'ก้าวสู่สไตล์ที่เป็นคุณ ด้วยรองเท้าคุณภาพ ส่งไวทั่วไทย', 'btn' => 'ช้อปเลย', 'href' => '#products', 'p0' => $best[0] ]];
if ($promoTop) $slides[] = ['eyebrow' => 'PROMOTION', 'h' => 'ลดสูงสุด ' . $maxPct . '%', 'p' => 'โปรโมชั่นรองเท้าราคาพิเศษ จำนวนจำกัด', 'btn' => 'ช้อปเลย', 'href' => 'index.php?promo=1', 'p0' => $promoTop];
$slides[] = ['eyebrow' => 'NEW ARRIVAL', 'h' => 'รองเท้ารุ่นใหม่ล่าสุด', 'p' => 'อัปเดตสินค้าใหม่ ที่นี่ที่เดียว', 'btn' => 'ดูสินค้า', 'href' => 'index.php?sort=latest', 'p0' => $newest];
?>
<div id="heroCarousel" class="carousel slide hero" data-bs-ride="carousel" data-bs-interval="5000">
  <div class="carousel-indicators">
    <?php foreach ($slides as $i => $s): ?><button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i ? '' : 'active' ?>" aria-label="สไลด์ <?= $i + 1 ?>"></button><?php endforeach; ?>
  </div>
  <div class="carousel-inner">
    <?php foreach ($slides as $i => $s): ?>
    <div class="carousel-item <?= $i ? '' : 'active' ?>">
      <div class="hero-slide">
        <div class="hero-text">
          <div class="hero-eyebrow"><?= e($s['eyebrow']) ?></div>
          <h1><?= e($s['h']) ?></h1>
          <p><?= e($s['p']) ?></p>
          <a href="<?= e($s['href']) ?>" class="btn btn-hero"><?= e($s['btn']) ?> <i class="bi bi-arrow-right"></i></a>
        </div>
        <img src="<?= img($s['p0']['image']) ?>" alt="<?= e($s['p0']['name']) ?>" class="hero-img" width="300" height="300" <?= $i ? 'loading="lazy"' : 'fetchpriority="high"' ?>>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <button class="ctrl prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="ก่อนหน้า"><i class="bi bi-chevron-left"></i></button>
  <button class="ctrl next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="ถัดไป"><i class="bi bi-chevron-right"></i></button>
</div>

<div class="feat"><div class="row g-0">
  <div class="col-6 col-lg-3"><div class="it"><i class="bi bi-geo-alt"></i><div><div class="t">ติดตามพัสดุบนแผนที่</div><div class="s">ดูตำแหน่งสินค้าได้ทุกออเดอร์</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="it"><i class="bi bi-shield-check"></i><div><div class="t">ชำระเงินปลอดภัย</div><div class="s">พร้อมเพย์ / บัตร / ปลายทาง</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="it"><i class="bi bi-camera"></i><div><div class="t">แจ้งสินค้าชำรุดได้</div><div class="s">แนบรูปและแชทกับร้าน</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="it"><i class="bi bi-headset"></i><div><div class="t">บริการหลังการขาย</div><div class="s">โทร <?= e(SHOP_PHONE) ?></div></div></div></div>
</div></div>

<section class="mt-5">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <div>
      <h2 class="sec-title"><span class="sl">///</span>สินค้าขายดี<span class="sl">///</span></h2>
      <div class="small text-muted">ยอดนิยมที่ลูกค้าเลือกซื้อมากที่สุด</div>
    </div>
    <a href="index.php?sort=sold" class="btn-all">ดูทั้งหมด <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
    <?php foreach ($best as $p): ?><div class="col"><?php product_card($p, true); ?></div><?php endforeach; ?>
  </div>
</section>

<div class="row g-3 my-4">
  <?php if ($promoTop): ?>
  <div class="col-md-6"><div class="promo-card promo-hot">
    <div><div class="pc-eyebrow">FLASH SALE</div><div class="pc-big">ลดสูงสุด <?= $maxPct ?>%</div><a href="index.php?promo=1" class="btn btn-light btn-sm rounded-pill px-3">ช้อปเลย <i class="bi bi-arrow-right"></i></a></div>
    <img src="<?= img($promoTop['image']) ?>" alt="<?= e($promoTop['name']) ?>" width="150" height="150" loading="lazy">
  </div></div>
  <?php endif; ?>
  <div class="<?= $promoTop ? 'col-md-6' : 'col-12' ?>"><div class="promo-card promo-new">
    <div><div class="pc-eyebrow">NEW ARRIVAL</div><div class="pc-big">รองเท้ารุ่นใหม่ล่าสุด<br>ที่นี่ที่เดียว</div><a href="index.php?sort=latest" class="btn btn-pink btn-sm rounded-pill px-3">ดูสินค้า <i class="bi bi-arrow-right"></i></a></div>
    <img src="<?= img($newest['image']) ?>" alt="<?= e($newest['name']) ?>" width="150" height="150" loading="lazy">
  </div></div>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3 <?= $home ? 'mt-4' : '' ?>" id="products">
  <h5 class="mb-0"><?= $home ? 'สินค้าทั้งหมด' : e($catName ?: (!empty($_GET['promo']) ? 'โปรโมชั่น' : 'สินค้า')) ?> (<?= count($products) ?>)</h5>
  <form class="d-flex gap-2">
    <?php foreach (['cat','q','promo'] as $k) if (!empty($_GET[$k])): ?><input type="hidden" name="<?= $k ?>" value="<?= e($_GET[$k]) ?>"><?php endif; ?>
    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">ล่าสุด</option>
      <option value="sold" <?= ($_GET['sort'] ?? '') == 'sold' ? 'selected' : '' ?>>ขายดี</option>
      <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>ราคา: ต่ำ → สูง</option>
      <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>ราคา: สูง → ต่ำ</option>
    </select>
  </form>
</div>

<div class="row g-3">
<?php foreach ($products as $p): ?>
  <div class="col-6 col-md-4 col-lg-2"><?php product_card($p); ?></div>
<?php endforeach; ?>
<?php if (!$products): ?><p class="text-center text-muted py-5">ไม่พบสินค้า</p><?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== product.php ==================== */
$files['product.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
$st = $pdo->prepare("SELECT p.*, c.name cat FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=?");
$st->execute([$_GET['id'] ?? 0]); $p = $st->fetch();
if (!$p) { not_found(); }
$sizes = array_values(array_filter(array_map('trim', explode(',', $p['sizes']))));
$rv = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON u.id=r.user_id WHERE r.product_id=? ORDER BY r.id DESC LIMIT 20");
$rv->execute([$p['id']]); $reviews = $rv->fetchAll();
$title = $p['name'] . ' - ' . $p['cat'];
$desc = $p['name'] . ' ราคา ' . number_format($p['sale_price'] ?? $p['price']) . ' บาท ' . ($p['description'] ?: 'สั่งซื้อได้ที่ Sneaks Store ส่งไวทั่วไทย');
$ogImage = SITE_URL . '/uploads/' . $p['image'];
include 'includes/header.php'; ?>
<div class="card-box"><div class="row g-4">
  <div class="col-md-5"><img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?> - <?= e($p['cat']) ?>" width="420" height="420" fetchpriority="high" class="img-fluid rounded product-detail-img"></div>
  <div class="col-md-7">
    <small class="text-muted"><?= e($p['cat']) ?></small>
    <h3><?= e($p['name']) ?></h3>
    <div class="text-muted mb-2">
      <span class="price"><?= $p['rating_avg'] ?></span> <?= stars($p['rating_avg']) ?>
      <span class="mx-2">|</span><?= $p['rating_count'] ?> รีวิว <span class="mx-2">|</span>ขายแล้ว <?= $p['sold'] ?> ชิ้น
    </div>
    <div class="p-3 mb-3 rounded" style="background:var(--pink-light)"><?php if ($p['sale_price'] !== null): ?>
      <span class="price fs-2"><?= money($p['sale_price']) ?></span> <s class="text-muted ms-2"><?= money($p['price']) ?></s>
      <span class="badge bg-danger ms-2">ลด <?= round(100 - $p['sale_price'] / $p['price'] * 100) ?>%</span>
    <?php else: ?><span class="price fs-2"><?= money($p['price']) ?></span><?php endif; ?>
    <small class="text-muted d-block">ราคายังไม่รวมภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>% (คิดเพิ่มตอนชำระเงิน)</small></div>
    <form method="post" action="cart.php">
      <input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?= $p['id'] ?>">
      <label class="form-label d-block">ไซซ์</label>
      <?php foreach ($sizes as $i => $s): ?>
        <label class="size-btn"><input type="radio" name="size" value="<?= e($s) ?>" <?= $i ? '' : 'checked' ?>><span><?= e($s) ?></span></label>
      <?php endforeach; ?>
      <div class="d-flex align-items-center gap-2 my-3">
        <label>จำนวน</label>
        <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>" class="form-control" style="width:90px">
        <small class="text-muted">มีสินค้า <?= $p['stock'] ?> ชิ้น</small>
      </div>
      <?php if ($p['stock'] > 0): ?>
        <button name="buy" value="0" class="btn btn-outline-pink btn-lg"><i class="bi bi-cart-plus"></i> เพิ่มไปยังรถเข็น</button>
        <button name="buy" value="1" class="btn btn-pink btn-lg px-5">ซื้อสินค้า</button>
      <?php else: ?>
        <button type="button" class="btn btn-secondary btn-lg" disabled>สินค้าหมด</button>
      <?php endif; ?>
    </form>
    <hr><h6>รายละเอียดสินค้า</h6>
    <p class="text-muted"><?= nl2br(e($p['description'])) ?></p>
  </div>
</div></div>

<div class="card-box mt-4">
  <h5>คะแนนของสินค้า <span class="price"><?= $p['rating_avg'] ?>/5</span> <?= stars($p['rating_avg']) ?></h5>
  <?php foreach ($reviews as $r): ?>
    <div class="border-bottom py-3">
      <div class="small fw-semibold"><?= e(mb_substr($r['name'], 0, 1)) ?>***</div>
      <div class="small"><?= stars($r['rating']) ?></div>
      <small class="text-muted"><?= $r['created_at'] ?></small>
      <p class="mb-0 mt-1"><?= nl2br(e($r['comment'])) ?></p>
    </div>
  <?php endforeach; ?>
  <?php if (!$reviews): ?><p class="text-muted text-center py-3 mb-0">ยังไม่มีรีวิว</p><?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== cart.php ==================== */
$files['cart.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
$_SESSION['cart'] ??= [];
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $id = (int)$_POST['id'];
    $key = $id . '|' . ($_POST['size'] ?? '-');
    $_SESSION['cart'][$key] = ($_SESSION['cart'][$key] ?? 0) + max(1, (int)$_POST['qty']);
    if (!empty($_POST['buy'])) redirect('checkout.php');
    flash('เพิ่มสินค้าลงรถเข็นแล้ว'); redirect("product.php?id=$id");
}
if ($action === 'update') {
    if (isset($_POST['remove'])) unset($_SESSION['cart'][$_POST['remove']]);
    else foreach ($_POST['qty'] ?? [] as $k => $q) {
        if ((int)$q <= 0) unset($_SESSION['cart'][$k]); else $_SESSION['cart'][$k] = (int)$q;
    }
    redirect('cart.php');
}
if ($action === 'coupon') {
    if (isset($_POST['remove_coupon'])) { unset($_SESSION['coupon']); redirect('cart.php'); }
    [, $sub] = cart_items($pdo);
    $c = check_coupon($pdo, $_POST['code'] ?? '', $sub);
    if (is_array($c)) { $_SESSION['coupon'] = $c['code']; flash('ใช้คูปอง ' . $c['code'] . ' ลด ' . money($c['discount'])); }
    else flash($c, 'danger');
    redirect('cart.php');
}

[$items, $total] = cart_items($pdo);
$coupon = !empty($_SESSION['coupon']) ? check_coupon($pdo, $_SESSION['coupon'], $total) : null;
$discount = is_array($coupon) ? $coupon['discount'] : 0;
$vat = calc_vat($total - $discount);
$title = 'รถเข็นของฉัน'; include 'includes/header.php'; ?>
<div class="card-box">
  <h4 class="mb-3"><i class="bi bi-cart3"></i> รถเข็นของฉัน</h4>
  <?php if (!$items): ?>
    <p class="text-center text-muted py-5">ยังไม่มีสินค้าในรถเข็น<br><a href="index.php" class="btn btn-pink mt-3">ไปช้อปเลย</a></p>
  <?php else: ?>
  <form method="post"><input type="hidden" name="action" value="update">
  <div class="table-responsive"><table class="table align-middle">
    <thead><tr><th>สินค้า</th><th>ไซซ์</th><th>ราคา</th><th width="110">จำนวน</th><th>รวม</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><img src="<?= img($it['image']) ?>" alt="<?= e($it['name']) ?>" width="60" height="60" loading="lazy" class="rounded me-2"><?= e($it['name']) ?></td>
        <td><?= e($it['size']) ?></td>
        <td><?= money($it['price']) ?></td>
        <td><input type="number" name="qty[<?= e($it['key']) ?>]" value="<?= $it['qty'] ?>" min="0" max="<?= $it['stock'] ?>" class="form-control form-control-sm"></td>
        <td class="price"><?= money($it['subtotal']) ?></td>
        <td><button name="remove" value="<?= e($it['key']) ?>" class="btn btn-sm btn-link text-danger"><i class="bi bi-trash"></i></button></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <button class="btn btn-outline-pink btn-sm">อัปเดตรถเข็น</button>
  </form>

  <hr>
  <div class="row g-3 align-items-center">
    <div class="col-md-6">
      <form method="post" class="d-flex gap-2 align-items-center">
        <input type="hidden" name="action" value="coupon">
        <i class="bi bi-ticket-perforated fs-4 price"></i>
        <?php if (is_array($coupon)): ?>
          <span class="coupon-tag"><?= e($coupon['code']) ?> (-<?= money($discount) ?>)</span>
          <button name="remove_coupon" class="btn btn-sm btn-link text-danger">ยกเลิกคูปอง</button>
        <?php else: ?>
          <input name="code" class="form-control text-uppercase" placeholder="ใส่โค้ดส่วนลด">
          <button class="btn btn-outline-pink text-nowrap">ใช้โค้ด</button>
        <?php endif; ?>
      </form>
      <?php if (is_string($coupon)): ?><small class="text-danger">คูปอง <?= e($_SESSION['coupon']) ?>: <?= e($coupon) ?></small><?php endif; ?>
    </div>
    <div class="col-md-6 text-md-end">
      <?php if ($discount): ?><div class="small text-muted">ส่วนลด -<?= money($discount) ?></div><?php endif; ?>
      <div class="small text-muted">ภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>% +<?= money($vat) ?></div>
      รวมทั้งหมด <span class="price fs-4 me-3"><?= money($total - $discount + $vat) ?></span>
      <div class="small text-muted mb-2">ยังไม่รวมค่าจัดส่ง</div>
      <a href="checkout.php" class="btn btn-pink btn-lg">สั่งซื้อสินค้า</a>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== checkout.php ==================== */
$files['checkout.php'] = <<<'PINKY_EOF'
<?php $needMap = true; require 'config.php'; require_login();
[$items, $total] = cart_items($pdo);
if (!$items) redirect('cart.php');

$coupon = !empty($_SESSION['coupon']) ? check_coupon($pdo, $_SESSION['coupon'], $total) : null;
if (!is_array($coupon)) { unset($_SESSION['coupon']); $coupon = null; }
$discount = $coupon['discount'] ?? 0;
$vat = calc_vat($total - $discount);
$grand = $total - $discount + $vat + SHIPPING_FEE;

$methods = ['promptpay' => ['qr-code', 'QR พร้อมเพย์' . (omise_on() ? ' (ยืนยันยอดอัตโนมัติ)' : ' (แนบสลิป)')]];
if (omise_on()) $methods['card'] = ['credit-card', 'บัตรเครดิต / เดบิต'];
$methods['cod'] = ['cash-coin', 'เก็บเงินปลายทาง'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        $upd = $pdo->prepare("UPDATE products SET stock=stock-?, sold=sold+? WHERE id=? AND stock>=?");
        foreach ($items as $it) {
            $upd->execute([$it['qty'], $it['qty'], $it['id'], $it['qty']]);
            if (!$upd->rowCount()) throw new Exception('สินค้า "' . $it['name'] . '" มีไม่พอในสต็อก');
        }
        if ($coupon) {
            $u = $pdo->prepare("UPDATE coupons SET used=used+1 WHERE id=? AND (max_uses IS NULL OR used<max_uses)");
            $u->execute([$coupon['id']]);
            if (!$u->rowCount()) throw new Exception('คูปองถูกใช้ครบจำนวนแล้ว');
        }
        $pm = isset($methods[$_POST['payment'] ?? '']) ? $_POST['payment'] : 'promptpay';
        if (empty($_POST['pdpa'])) throw new Exception('กรุณายอมรับการเก็บและใช้ข้อมูลที่อยู่/ตำแหน่งจัดส่ง (PDPA)');
        $la = is_numeric($_POST['lat'] ?? '') ? (float)$_POST['lat'] : null;
        $ln = is_numeric($_POST['lng'] ?? '') ? (float)$_POST['lng'] : null;
        if ($la === null || $ln === null || abs($la) > 90 || abs($ln) > 180) throw new Exception('พิกัดจัดส่งไม่ถูกต้อง กรุณาปักหมุดบนแผนที่');
        $status = $pm === 'cod' ? 'verifying' : 'pending';
        $pdo->prepare("INSERT INTO orders(user_id,receiver,phone,address,lat,lng,total,discount,vat,coupon_code,payment_method,status)
                       VALUES(?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([user()['id'], $_POST['receiver'], $_POST['phone'], $_POST['address'],
                       $la, $ln, $grand, $discount, $vat, $coupon['code'] ?? null, $pm, $status]);
        $oid = $pdo->lastInsertId();
        $ins = $pdo->prepare("INSERT INTO order_items(order_id,product_id,name,size,qty,price) VALUES(?,?,?,?,?,?)");
        foreach ($items as $it) $ins->execute([$oid, $it['id'], $it['name'], $it['size'], $it['qty'], $it['price']]);
        $pdo->prepare("INSERT INTO order_tracking(order_id,status,note) VALUES(?,?,?)")
            ->execute([$oid, $status, 'สร้างคำสั่งซื้อ (' . PAY[$pm] . ')']);
        pdpa_log($pdo, user()['id'], 'checkout_address_location');
        $slipOk = false;
        if ($pm === 'promptpay' && !omise_on() && !empty($_FILES['slip']['name'])) {    // แนบสลิปมาพร้อมการสั่งซื้อ
            $f = upload_image('slip', 'slips');
            if (!$f) throw new Exception(upload_error() ?: 'แนบสลิปไม่สำเร็จ');
            $pdo->prepare("UPDATE orders SET slip=?, status='verifying' WHERE id=?")->execute([$f, $oid]);
            $pdo->prepare("INSERT INTO order_tracking(order_id,status,note) VALUES(?,?,?)")->execute([$oid, 'verifying', 'ลูกค้าแนบสลิปการโอนเงินแล้ว']);
            $slipOk = true;
        }
        $pdo->commit();
        unset($_SESSION['cart'], $_SESSION['coupon']);
        if ($slipOk) flash('ส่งหลักฐานการชำระเงินเรียบร้อย รอร้านค้าตรวจสอบ');
        redirect($pm === 'cod' || $slipOk ? "track.php?id=$oid" : "payment.php?id=$oid");
    } catch (Exception $ex) { $pdo->rollBack(); $error = $ex->getMessage(); }
}
$title = 'ทำการสั่งซื้อ'; include 'includes/header.php'; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" onsubmit="if(!lat.value){alert('กรุณาปักหมุดที่อยู่จัดส่งบนแผนที่');return false}">
<div class="row g-4">
  <div class="col-lg-7"><div class="card-box">
    <h5 class="price"><i class="bi bi-geo-alt-fill"></i> ที่อยู่ในการจัดส่ง</h5>
    <div class="row g-2 mb-2">
      <div class="col"><input name="receiver" class="form-control" placeholder="ชื่อผู้รับ" required value="<?= e(user()['name']) ?>"></div>
      <div class="col"><input name="phone" class="form-control" placeholder="เบอร์โทร" required value="<?= e(user()['phone']) ?>"></div>
    </div>
    <textarea name="address" id="address" class="form-control mb-2" rows="2" placeholder="บ้านเลขที่ ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์" required><?= e($_POST['address'] ?? '') ?></textarea>
    <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
      <small class="text-muted">พิมพ์ที่อยู่แล้วหมุดจะปักให้อัตโนมัติ (คลิกแผนที่/ลากหมุดเพื่อปรับให้ตรงบ้านได้)</small>
      <span class="text-nowrap">
        <button type="button" id="findBtn" class="btn btn-sm btn-outline-pink"><i class="bi bi-search"></i> ค้นหาที่อยู่</button>
        <button type="button" id="locBtn" class="btn btn-sm btn-outline-pink"><i class="bi bi-crosshair"></i> ตำแหน่งปัจจุบัน</button>
      </span>
    </div>
    <div id="geoStatus" class="small mb-2 text-muted" style="min-height:1.3em"></div>
    <div id="map" class="map"></div>
    <input type="hidden" name="lat" id="lat"><input type="hidden" name="lng" id="lng">
  </div></div>

  <div class="col-lg-5"><div class="card-box">
    <h5>สรุปคำสั่งซื้อ</h5>
    <?php foreach ($items as $it): ?>
      <div class="d-flex justify-content-between small mb-1">
        <span><?= e($it['name']) ?> (ไซซ์ <?= e($it['size']) ?>) × <?= $it['qty'] ?></span><span><?= money($it['subtotal']) ?></span>
      </div>
    <?php endforeach; ?>
    <hr>
    <div class="d-flex justify-content-between"><span>ค่าสินค้า</span><span><?= money($total) ?></span></div>
    <?php if ($coupon): ?>
      <div class="d-flex justify-content-between price"><span>ส่วนลด <span class="coupon-tag small"><?= e($coupon['code']) ?></span></span><span>-<?= money($discount) ?></span></div>
    <?php endif; ?>
    <div class="d-flex justify-content-between"><span>ภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>%</span><span><?= money($vat) ?></span></div>
    <div class="d-flex justify-content-between"><span>ค่าจัดส่ง</span><span><?= money(SHIPPING_FEE) ?></span></div>
    <div class="d-flex justify-content-between fs-5 mt-2"><b>ยอดชำระ</b><b class="price"><?= money($grand) ?></b></div>
    <hr><h6>วิธีการชำระเงิน</h6>
    <?php foreach ($methods as $k => [$icon, $label]): ?>
      <div class="form-check mb-1"><input class="form-check-input" type="radio" name="payment" value="<?= $k ?>" id="pm_<?= $k ?>" <?= $k === 'promptpay' ? 'checked' : '' ?>>
        <label class="form-check-label" for="pm_<?= $k ?>"><i class="bi bi-<?= $icon ?>"></i> <?= $label ?></label></div>
    <?php endforeach; ?>
    <?php if (!omise_on()): ?>
    <div id="slipBox" class="border rounded p-3 mt-2" style="background:#fff8fb">
      <div class="text-center mb-2">
        <div class="small text-muted mb-1">สแกนจ่ายยอด <b class="price"><?= money($grand) ?></b> ด้วยแอปธนาคาร</div>
        <img src="https://promptpay.io/<?= PROMPTPAY_ID ?>/<?= $grand ?>.png" alt="QR Code พร้อมเพย์ ยอด <?= money($grand) ?>" width="180" height="180" loading="lazy" style="max-width:180px">
      </div>
      <label class="form-label small mb-1" for="slipFile"><i class="bi bi-paperclip"></i> แนบสลิปการโอนเงิน (JPG / PNG / WEBP ไม่เกิน 5 MB)</label>
      <input type="file" id="slipFile" name="slip" accept="image/jpeg,image/png,image/webp" class="form-control mb-2">
      <div id="slipPrev" class="text-center mb-2" style="display:none">
        <img id="slipImg" alt="ตัวอย่างสลิปที่เลือก" style="max-width:200px;max-height:260px;border-radius:8px;border:1px solid #ffd1dc">
        <div class="small text-muted mt-1" id="slipName"></div>
      </div>
      <small class="text-muted d-block"><i class="bi bi-info-circle"></i> แนบตอนนี้ได้เลย หรือกดสั่งสินค้าก่อนแล้วแนบที่หน้าชำระเงินทีหลังก็ได้ ระบบตรวจว่าเป็นรูปจริงและลบพิกัด GPS ออกจากรูปอัตโนมัติ</small>
    </div>
    <?php endif; ?>
    <div class="form-check mt-3 small">
      <input class="form-check-input" type="checkbox" name="pdpa" value="1" id="pdpa" required>
      <label class="form-check-label" for="pdpa">ข้าพเจ้ายินยอมให้ร้านเก็บและใช้ชื่อ เบอร์โทร ที่อยู่ และพิกัดจัดส่ง เพื่อจัดส่งสินค้าเท่านั้น ตาม<a href="privacy.php" target="_blank">นโยบายความเป็นส่วนตัว (PDPA)</a></label>
    </div>
    <button class="btn btn-pink btn-lg w-100 mt-3">สั่งสินค้า</button>
  </div></div>
</div>
</form>
<script>
const map = L.map('map').setView([<?= SHOP_LAT ?>, <?= SHOP_LNG ?>], 11);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
let marker, manual = false, timer, reqId = 0;
const addrEl = document.getElementById('address'), geoStatus = document.getElementById('geoStatus');
function say(msg, cls) { geoStatus.className = 'small mb-2 ' + (cls || 'text-muted'); geoStatus.textContent = msg; }
function setPos(la, ln, byUser) {
  if (byUser) manual = true;                     // ผู้ใช้ปรับหมุดเอง -> ไม่ให้ระบบอัตโนมัติมาย้ายทับ
  if (marker) marker.setLatLng([la, ln]);
  else marker = L.marker([la, ln], {draggable: true}).addTo(map)
      .on('dragend', e => { const p = e.target.getLatLng(); setPos(p.lat, p.lng, true); say('ปรับตำแหน่งหมุดเองแล้ว', 'text-success'); });
  lat.value = la.toFixed(7); lng.value = ln.toFixed(7);
}
map.on('click', e => { setPos(e.latlng.lat, e.latlng.lng, true); say('ปักหมุดตามที่คลิกแล้ว', 'text-success'); });
locBtn.onclick = () => navigator.geolocation.getCurrentPosition(
  p => { setPos(p.coords.latitude, p.coords.longitude, true); map.setView([p.coords.latitude, p.coords.longitude], 16); say('ใช้ตำแหน่งปัจจุบันแล้ว', 'text-success'); },
  () => alert('ไม่สามารถเข้าถึงตำแหน่งได้'));

// ---- ปักหมุดอัตโนมัติจากที่อยู่ที่พิมพ์ (OpenStreetMap Nominatim) ----
function candidates(text) {
  const clean = text.replace(/[,\n]+/g, ' ').replace(/\s+/g, ' ').trim();
  const parts = clean.split(' ').filter(Boolean);
  const out = [clean];
  for (let i = 1; i < parts.length - 1 && out.length < 5; i++) out.push(parts.slice(i).join(' ')); // ตัดบ้านเลขที่/ซอยออกทีละคำ
  const zip = (clean.match(/\b\d{5}\b/) || [])[0];
  if (zip) out.push(zip);
  return [...new Set(out)];
}
async function geocode(auto) {
  const text = addrEl.value.trim();
  if (text.length < 8) { if (!auto) say('กรุณากรอกที่อยู่ให้ครบขึ้นอีกนิด', 'text-danger'); return; }
  const my = ++reqId, list = candidates(text);
  say('กำลังค้นหาตำแหน่งจากที่อยู่...');
  try {
    for (let i = 0; i < list.length; i++) {
      if (my !== reqId) return;                  // มีการพิมพ์ใหม่แล้ว ทิ้งการค้นหาเก่า
      const r = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=th&accept-language=th&q=' + encodeURIComponent(list[i]));
      if (!r.ok) throw new Error('http ' + r.status);
      const res = await r.json();
      if (my !== reqId) return;
      if (res.length) {
        const la = parseFloat(res[0].lat), ln = parseFloat(res[0].lon);
        setPos(la, ln, false);
        map.setView([la, ln], i === 0 ? 17 : 15);
        say(i === 0 ? 'ปักหมุดตามที่อยู่แล้ว ลากหมุดเพื่อปรับให้ตรงบ้านได้' : 'ปักหมุดโดยประมาณจากระดับตำบล/อำเภอ กรุณาลากหมุดให้ตรงบ้าน', i === 0 ? 'text-success' : 'text-warning');
        return;
      }
      await new Promise(ok => setTimeout(ok, 1100)); // Nominatim จำกัด 1 คำขอ/วินาที
    }
    say('ไม่พบที่อยู่นี้ในแผนที่ กรุณาคลิกบนแผนที่เพื่อปักหมุดเอง', 'text-danger');
  } catch (e) {
    say('ค้นหาอัตโนมัติไม่สำเร็จ กรุณาคลิกบนแผนที่เพื่อปักหมุดเอง', 'text-danger');
  }
}
addrEl.addEventListener('input', () => { if (manual) return; clearTimeout(timer); timer = setTimeout(() => geocode(true), 1200); });
document.getElementById('findBtn').onclick = () => { manual = false; geocode(false); };
if (addrEl.value.trim().length >= 8) geocode(true);

// ---- ช่องแนบสลิป: แสดงเฉพาะเมื่อเลือก QR พร้อมเพย์ ----
const slipBox = document.getElementById('slipBox');
if (slipBox) {
  const sync = () => { const r = document.querySelector('input[name=payment]:checked'); slipBox.style.display = r && r.value === 'promptpay' ? 'block' : 'none'; };
  document.querySelectorAll('input[name=payment]').forEach(r => r.addEventListener('change', sync)); sync();
  document.getElementById('slipFile').addEventListener('change', function () {
    const f = this.files[0], box = document.getElementById('slipPrev');
    if (!f) { box.style.display = 'none'; return; }
    if (f.size > 5 * 1024 * 1024) { alert('ไฟล์ใหญ่เกิน 5 MB กรุณาเลือกไฟล์ใหม่'); this.value = ''; box.style.display = 'none'; return; }
    if (!/^image\/(jpeg|png|webp)$/.test(f.type)) { alert('รองรับเฉพาะรูปภาพ JPG, PNG, WEBP'); this.value = ''; box.style.display = 'none'; return; }
    document.getElementById('slipImg').src = URL.createObjectURL(f);
    document.getElementById('slipName').textContent = f.name;
    box.style.display = 'block';
  });
}
</script>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== payment.php ==================== */
$files['payment.php'] = <<<'PINKY_EOF'
<?php require 'config.php'; require_login();
$o = get_order($pdo, $_GET['id'] ?? 0);
$oid = $o['id'];
$amount = (int)round($o['total'] * 100);   // Omise ใช้หน่วยสตางค์

// ---- JavaScript เช็กสถานะ หรือลูกค้ากลับมาจากหน้า 3-D Secure ----
if ((isset($_GET['check']) || isset($_GET['return'])) && omise_on() && $o['charge_id']) {
    $ch = omise('GET', '/charges/' . $o['charge_id']);
    $paid = mark_paid($pdo, $oid, $ch) || ($ch['status'] ?? '') === 'successful';
    if (isset($_GET['check'])) { header('Content-Type: application/json'); exit(json_encode(['paid' => $paid])); }
    if ($paid) { flash('ชำระเงินสำเร็จ ขอบคุณค่ะ'); redirect("track.php?id=$oid"); }
    flash('ชำระเงินไม่สำเร็จ: ' . ($ch['failure_message'] ?? 'กรุณาลองใหม่'), 'danger');
    redirect("payment.php?id=$oid");
}

$canResend = $o['status'] === 'verifying' && $o['payment_method'] === 'promptpay';   // ส่งสลิปแล้วแต่ยังไม่ยืนยัน: แนบใหม่ได้
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($o['status'] === 'pending' || $canResend)) {
    if ($o['status'] === 'pending' && !empty($_POST['omiseToken']) && omise_on()) {                // บัตรเครดิต
        $ch = omise('POST', '/charges', [
            'amount' => $amount, 'currency' => 'thb', 'card' => $_POST['omiseToken'],
            'return_uri' => SITE_URL . "/payment.php?id=$oid&return=1",
            'metadata' => ['order_id' => $oid],
        ]);
        if (($ch['object'] ?? '') !== 'charge') $error = $ch['message'] ?? 'เชื่อมต่อ Omise ไม่สำเร็จ';
        else {
            $pdo->prepare("UPDATE orders SET charge_id=? WHERE id=?")->execute([$ch['id'], $oid]);
            if ($ch['status'] === 'pending' && !empty($ch['authorize_uri'])) redirect($ch['authorize_uri']);
            if (mark_paid($pdo, $oid, $ch)) { flash('ชำระเงินสำเร็จ ขอบคุณค่ะ'); redirect("track.php?id=$oid"); }
            $error = $ch['failure_message'] ?? 'บัตรถูกปฏิเสธ กรุณาลองใหม่';
        }
    } elseif ($f = upload_image('slip', 'slips')) {                   // แนบสลิป
        $pdo->prepare("UPDATE orders SET slip=? WHERE id=?")->execute([$f, $oid]);
        set_status($pdo, $oid, 'verifying', 'ลูกค้าแนบสลิปการโอนเงินแล้ว');
        flash('ส่งหลักฐานการชำระเงินเรียบร้อย รอร้านค้าตรวจสอบ');
        redirect("track.php?id=$oid");
    } else $error = upload_error() ?: 'กรุณาแนบไฟล์รูปภาพ (jpg, png, webp)';
}

// ---- QR พร้อมเพย์ผ่าน Omise (สร้างใหม่ถ้า QR เดิมหมดอายุ) ----
$qr = null;
if ($o['status'] === 'pending' && $o['payment_method'] === 'promptpay' && omise_on()) {
    $ch = $o['charge_id'] ? omise('GET', '/charges/' . $o['charge_id']) : null;
    if (($ch['status'] ?? '') === 'successful' && mark_paid($pdo, $oid, $ch)) redirect("track.php?id=$oid");
    if (($ch['source']['type'] ?? '') !== 'promptpay' || $ch['status'] !== 'pending') {
        $ch = omise('POST', '/charges', ['amount' => $amount, 'currency' => 'thb',
              'source' => ['type' => 'promptpay'], 'metadata' => ['order_id' => $oid]]);
        if (($ch['object'] ?? '') === 'charge') $pdo->prepare("UPDATE orders SET charge_id=? WHERE id=?")->execute([$ch['id'], $oid]);
        else $error = $ch['message'] ?? 'สร้าง QR ไม่สำเร็จ';
    }
    $qr = $ch['source']['scannable_code']['image']['download_uri'] ?? null;
}

$title = 'ชำระเงิน'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card-box text-center">
  <h4 class="price">ชำระเงินคำสั่งซื้อ #<?= $oid ?></h4>
  <div class="fs-3 price"><?= money($o['total']) ?></div>
  <?php if (($o['vat'] ?? 0) > 0): ?><div class="small text-muted mb-2">รวมภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>% <?= money($o['vat']) ?> และค่าจัดส่งแล้ว</div><?php else: ?><div class="mb-2"></div><?php endif; ?>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

  <?php if ($o['status'] !== 'pending' && !$canResend): ?>
    <p class="my-4">คำสั่งซื้อนี้อยู่ในสถานะ <?= badge($o['status']) ?></p>

  <?php elseif ($o['payment_method'] === 'card'): ?>
    <form id="cardForm" method="post"><input type="hidden" name="omiseToken"></form>
    <button id="payCard" class="btn btn-pink btn-lg w-100"><i class="bi bi-credit-card"></i> กรอกข้อมูลบัตรและชำระเงิน</button>
    <small class="text-muted d-block mt-2"><i class="bi bi-shield-lock"></i> ข้อมูลบัตรส่งตรงไปที่ Omise ร้านค้าไม่ได้เก็บเลขบัตร</small>
    <script src="https://cdn.omise.co/omise.js"></script>
    <script>
    OmiseCard.configure({publicKey: '<?= OMISE_PUBLIC_KEY ?>'});
    payCard.onclick = () => OmiseCard.open({
      amount: <?= $amount ?>, currency: 'THB', frameLabel: 'Sneaks Store', submitLabel: 'ชำระเงิน',
      defaultPaymentMethod: 'credit_card',
      onCreateTokenSuccess: t => { cardForm.omiseToken.value = t; cardForm.submit(); }
    });
    </script>

  <?php elseif ($qr): ?>
    <p class="text-muted mb-1">สแกนด้วยแอปธนาคาร ระบบจะยืนยันยอดให้อัตโนมัติ</p>
    <img src="<?= e($qr) ?>" alt="QR Code พร้อมเพย์สำหรับชำระเงินคำสั่งซื้อ #<?= $oid ?>" class="img-fluid my-2" style="max-width:260px">
    <p class="small text-muted"><span class="spinner-border spinner-border-sm"></span> กำลังรอการชำระเงิน...</p>
    <details class="text-start mt-2"><summary class="small text-muted" style="cursor:pointer">โอนแล้วแต่ระบบยังไม่ยืนยัน? แนบสลิปที่นี่</summary><?php include 'includes/slip_form.php'; ?></details>
    <script>
    setInterval(() => fetch('payment.php?id=<?= $oid ?>&check=1').then(r => r.json())
      .then(d => { if (d.paid) location.href = 'track.php?id=<?= $oid ?>'; }).catch(() => {}), 5000);
    </script>

  <?php else: ?>
    <?php if ($canResend): ?><div class="alert alert-info py-2 small">ส่งสลิปแล้ว กำลังรอร้านตรวจสอบ หากแนบผิด แนบใหม่ได้ที่นี่<?php if ($o['slip']): ?><br><a href="<?= e(img($o['slip'])) ?>" target="_blank">ดูสลิปที่ส่งไว้</a><?php endif; ?></div><?php endif; ?>
    <p class="text-muted mb-1">สแกนเพื่อชำระผ่านแอปธนาคาร แล้วแนบสลิป</p>
    <img src="https://promptpay.io/<?= PROMPTPAY_ID ?>/<?= $o['total'] ?>.png" alt="QR Code พร้อมเพย์ ยอด <?= money($o['total']) ?>" class="img-fluid my-2" style="max-width:260px">
    <?php include 'includes/slip_form.php'; ?>
  <?php endif; ?>
  <a href="track.php?id=<?= $oid ?>" class="d-block mt-3">ดูสถานะคำสั่งซื้อ</a>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

$files['includes/slip_form.php'] = <<<'PINKY_EOF'
<form method="post" enctype="multipart/form-data" class="text-start mt-3">
  <label class="form-label small mb-1" for="slipFile"><i class="bi bi-paperclip"></i> แนบสลิปการโอนเงิน (JPG / PNG / WEBP ไม่เกิน 5 MB)</label>
  <input type="file" id="slipFile" name="slip" accept="image/jpeg,image/png,image/webp" class="form-control mb-2" required>
  <div id="slipPrev" class="text-center mb-2" style="display:none">
    <img id="slipImg" alt="ตัวอย่างสลิปที่เลือก" style="max-width:220px;max-height:280px;border-radius:8px;border:1px solid #ffd1dc">
    <div class="small text-muted mt-1" id="slipName"></div>
  </div>
  <small class="text-muted d-block mb-3"><i class="bi bi-shield-lock"></i> ระบบตรวจว่าเป็นรูปภาพจริง และลบข้อมูลแฝง (เช่น พิกัด GPS) ออกจากรูปอัตโนมัติ แนะนำให้ปิดบังเลขบัญชีที่ไม่เกี่ยวข้องก่อนอัปโหลด</small>
  <button class="btn btn-pink w-100"><i class="bi bi-upload"></i> แจ้งชำระเงิน (แนบสลิป)</button>
</form>
<script>
document.getElementById('slipFile').addEventListener('change', function () {
  const f = this.files[0], box = document.getElementById('slipPrev');
  if (!f) { box.style.display = 'none'; return; }
  if (f.size > 5 * 1024 * 1024) { alert('ไฟล์ใหญ่เกิน 5 MB กรุณาเลือกไฟล์ใหม่'); this.value = ''; box.style.display = 'none'; return; }
  if (!/^image\/(jpeg|png|webp)$/.test(f.type)) { alert('รองรับเฉพาะรูปภาพ JPG, PNG, WEBP'); this.value = ''; box.style.display = 'none'; return; }
  document.getElementById('slipImg').src = URL.createObjectURL(f);
  document.getElementById('slipName').textContent = f.name;
  box.style.display = 'block';
});
</script>
PINKY_EOF;

/* ==================== webhook.php ==================== */
$files['webhook.php'] = <<<'PINKY_EOF'
<?php define('NO_CSRF', true); require 'config.php';
// Omise แจ้งผลการชำระเงิน: ดึงข้อมูล charge จาก Omise ใหม่เสมอ ไม่เชื่อข้อมูลที่ส่งเข้ามาตรง ๆ
$ev = json_decode(file_get_contents('php://input'), true) ?: [];
if (($ev['key'] ?? '') === 'charge.complete' && !empty($ev['data']['id'])) {
    $ch = omise('GET', '/charges/' . urlencode($ev['data']['id']));
    $st = $pdo->prepare("SELECT id FROM orders WHERE charge_id=?");
    $st->execute([$ch['id'] ?? '']);
    if ($oid = $st->fetchColumn()) mark_paid($pdo, $oid, $ch);
}
http_response_code(200);
echo 'ok';
PINKY_EOF;

/* ==================== orders.php ==================== */
$files['orders.php'] = <<<'PINKY_EOF'
<?php require 'config.php'; require_login();
$st = $pdo->prepare("SELECT o.*, (SELECT GROUP_CONCAT(CONCAT(name,' x',qty) SEPARATOR ', ') FROM order_items WHERE order_id=o.id) items
                     FROM orders o WHERE user_id=? ORDER BY id DESC");
$st->execute([user()['id']]); $orders = $st->fetchAll();
$title = 'คำสั่งซื้อของฉัน'; include 'includes/header.php'; ?>
<h4 class="mb-3"><i class="bi bi-box-seam"></i> คำสั่งซื้อของฉัน</h4>
<?php foreach ($orders as $o): ?>
<div class="card-box mb-3">
  <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
    <span>คำสั่งซื้อ #<?= $o['id'] ?> · <small class="text-muted"><?= $o['created_at'] ?></small></span>
    <?= badge($o['status']) ?>
  </div>
  <p class="small text-muted mb-2"><?= e($o['items']) ?></p>
  <div class="d-flex justify-content-between align-items-center">
    <span>ยอดชำระ <b class="price"><?= money($o['total']) ?></b> <small class="text-muted">(<?= PAY[$o['payment_method']] ?>)</small></span>
    <div>
      <?php if ($o['status'] === 'pending' && $o['payment_method'] !== 'cod'): ?>
        <a href="payment.php?id=<?= $o['id'] ?>" class="btn btn-pink btn-sm">ชำระเงิน</a>
      <?php endif; ?>
      <?php if ($o['status'] === 'delivered'): ?>
        <a href="review.php?id=<?= $o['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-star"></i> รีวิวสินค้า</a>
      <?php endif; ?>
      <?php if (in_array($o['status'], ['shipping', 'delivered'])): ?>
        <a href="defect.php?order=<?= $o['id'] ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-exclamation-triangle"></i> แจ้งสินค้าชำรุด</a>
      <?php endif; ?>
      <a href="track.php?id=<?= $o['id'] ?>" class="btn btn-outline-pink btn-sm"><i class="bi bi-geo-alt"></i> ติดตามสถานะ</a>
    </div>
  </div>
</div>
<?php endforeach; ?>
<?php if (!$orders): ?><p class="text-muted text-center py-5">ยังไม่มีคำสั่งซื้อ</p><?php endif; ?>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== track.php ==================== */
$files['track.php'] = <<<'PINKY_EOF'
<?php $needMap = true; require 'config.php'; require_login();
$o = get_order($pdo, $_GET['id'] ?? 0);
if (isset($_GET['live'])) {
    header('Content-Type: application/json');
    exit(json_encode(['status' => $o['status'], 'at' => $o['cur_at'],
        'pos' => $o['cur_lat'] ? [(float)$o['cur_lat'], (float)$o['cur_lng']] : null]));
}
$st = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?"); $st->execute([$o['id']]); $items = $st->fetchAll();
$st = $pdo->prepare("SELECT * FROM order_tracking WHERE order_id=? ORDER BY id"); $st->execute([$o['id']]); $tracks = $st->fetchAll();
$points = array_values(array_map(fn($t) => [(float)$t['lat'], (float)$t['lng']], array_filter($tracks, fn($t) => $t['lat'] !== null)));
$steps = ['pending'=>'bi-receipt', 'verifying'=>'bi-cash-coin', 'confirmed'=>'bi-box-seam', 'shipping'=>'bi-truck', 'delivered'=>'bi-house-check'];
$idx = array_search($o['status'], array_keys($steps));
$title = 'ติดตามคำสั่งซื้อ #' . $o['id']; include 'includes/header.php'; ?>

<div class="card-box mb-4">
  <div class="d-flex justify-content-between mb-3">
    <h5 class="mb-0">คำสั่งซื้อ #<?= $o['id'] ?></h5>
    <div><?= $o['tracking_no'] ? 'เลขพัสดุ: <b>' . e($o['tracking_no']) . '</b> ' : '' ?><?= badge($o['status']) ?></div>
  </div>
  <?php if ($o['status'] !== 'cancelled'): ?>
  <div class="stepper">
    <?php $i = 0; foreach ($steps as $k => $icon): ?>
      <div class="step <?= $idx !== false && $i <= $idx ? 'done' : '' ?>"><i class="bi <?= $icon ?>"></i><?= STATUS[$k] ?></div>
    <?php $i++; endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<div class="row g-4">
  <div class="col-lg-8"><div class="card-box">
    <h6><i class="bi bi-map"></i> ตำแหน่งพัสดุ</h6>
    <div id="map" class="map"></div>
    <small class="text-muted"><i class="bi bi-shop" style="color:#ff6b9d"></i> ร้านค้า ·
      <i class="bi bi-truck" style="color:#e84a7f"></i> จุดอัปเดตล่าสุด ·
      <i class="bi bi-scooter" style="color:#7c4dff"></i> ไรเดอร์ (สด) ·
      <i class="bi bi-house-heart" style="color:#28a745"></i> ปลายทาง</small>
  </div></div>
  <div class="col-lg-4">
    <div class="card-box mb-3">
      <h6>ประวัติการจัดส่ง</h6>
      <ul class="timeline mt-3">
        <?php foreach (array_reverse($tracks) as $t): ?>
          <li><b><?= STATUS[$t['status']] ?? e($t['status']) ?></b><br>
            <small><?= e($t['note']) ?></small><br><small class="text-muted"><?= $t['created_at'] ?></small></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="card-box">
      <h6>ผู้รับ</h6>
      <p class="small mb-2"><?= e($o['receiver']) ?> · <?= e($o['phone']) ?><br><?= e($o['address']) ?></p>
      <?php foreach ($items as $it): ?>
        <div class="d-flex justify-content-between small"><span><?= e($it['name']) ?> (<?= e($it['size']) ?>) × <?= $it['qty'] ?></span><span><?= money($it['price'] * $it['qty']) ?></span></div>
      <?php endforeach; ?>
      <?php if ($o['discount'] > 0): ?><div class="d-flex justify-content-between small price"><span>ส่วนลด <?= e($o['coupon_code']) ?></span><span>-<?= money($o['discount']) ?></span></div><?php endif; ?>
      <?php if (($o['vat'] ?? 0) > 0): ?><div class="d-flex justify-content-between small"><span>ภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>%</span><span><?= money($o['vat']) ?></span></div><?php endif; ?>
      <div class="text-end mt-2">รวม <b class="price"><?= money($o['total']) ?></b></div>
    </div>
  </div>
</div>

<script>
const shop = [<?= SHOP_LAT ?>, <?= SHOP_LNG ?>];
const dest = <?= $o['lat'] ? json_encode([(float)$o['lat'], (float)$o['lng']]) : 'null' ?>;
const pts  = <?= json_encode($points) ?>;
const map = L.map('map');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
const pin = (c, i) => L.divIcon({className: '', html: `<div class="map-pin" style="background:${c}"><i class="bi bi-${i}"></i></div>`, iconSize: [34, 34], iconAnchor: [17, 17]});
L.marker(shop, {icon: pin('#ff6b9d', 'shop')}).addTo(map).bindPopup('ร้าน Sneaks Store');
if (dest) L.marker(dest, {icon: pin('#28a745', 'house-heart')}).addTo(map).bindPopup('ที่อยู่จัดส่ง');
if (pts.length) {
  L.polyline([shop, ...pts], {color: '#e84a7f', weight: 4}).addTo(map);
  if (dest) L.polyline([pts[pts.length - 1], dest], {color: '#e84a7f', weight: 3, dashArray: '6 8', opacity: .6}).addTo(map);
  L.marker(pts[pts.length - 1], {icon: pin('#e84a7f', 'truck')}).addTo(map).bindPopup('จุดอัปเดตล่าสุด');
}
const all = [shop, ...pts]; if (dest) all.push(dest);
map.fitBounds(all, {padding: [40, 40], maxZoom: 15});
<?php if ($o['status'] === 'shipping'): ?>
let live;
const poll = () => fetch('track.php?id=<?= $o['id'] ?>&live=1').then(r => r.json()).then(d => {
  if (d.status !== 'shipping') return location.reload();
  if (!d.pos) return;
  if (live) live.setLatLng(d.pos);
  else live = L.marker(d.pos, {icon: pin('#7c4dff', 'scooter')}).addTo(map).bindPopup('ไรเดอร์กำลังไปหาคุณ').openPopup();
}).catch(() => {});
poll(); setInterval(poll, 10000);
<?php endif; ?>
</script>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== review.php ==================== */
$files['review.php'] = <<<'PINKY_EOF'
<?php require 'config.php'; require_login();
$o = get_order($pdo, $_GET['id'] ?? 0);
if ($o['status'] !== 'delivered' || $o['user_id'] != user()['id']) exit('รีวิวได้เมื่อได้รับสินค้าแล้วเท่านั้น');

$st = $pdo->prepare("SELECT DISTINCT oi.product_id, oi.name, p.image, r.rating, r.comment
    FROM order_items oi JOIN products p ON p.id=oi.product_id
    LEFT JOIN reviews r ON r.order_id=oi.order_id AND r.product_id=oi.product_id
    WHERE oi.order_id=?");
$st->execute([$o['id']]); $items = $st->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed = array_column($items, 'product_id');
    $ins = $pdo->prepare("INSERT IGNORE INTO reviews(product_id,user_id,order_id,rating,comment) VALUES(?,?,?,?,?)");
    $upd = $pdo->prepare("UPDATE products SET
        rating_count=(SELECT COUNT(*) FROM reviews WHERE product_id=?),
        rating_avg=(SELECT COALESCE(AVG(rating),0) FROM reviews WHERE product_id=?) WHERE id=?");
    foreach ($_POST['rating'] ?? [] as $pid => $r) {
        if (!in_array($pid, $allowed)) continue;
        $ins->execute([$pid, user()['id'], $o['id'], max(1, min(5, (int)$r)), trim($_POST['comment'][$pid] ?? '')]);
        $upd->execute([$pid, $pid, $pid]);
    }
    flash('ขอบคุณสำหรับรีวิวค่ะ'); redirect('orders.php');
}
$title = 'รีวิวสินค้า'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-7"><div class="card-box">
  <h4 class="price mb-3"><i class="bi bi-star"></i> รีวิวสินค้า คำสั่งซื้อ #<?= $o['id'] ?></h4>
  <form method="post">
  <?php foreach ($items as $it): $pid = $it['product_id']; ?>
    <div class="d-flex gap-3 border-bottom py-3">
      <img src="<?= img($it['image']) ?>" alt="<?= e($it['name']) ?>" width="80" height="80" class="rounded" style="object-fit:cover">
      <div class="flex-grow-1">
        <div class="fw-semibold"><?= e($it['name']) ?></div>
        <?php if ($it['rating']): ?>
          <div><?= stars($it['rating']) ?></div><small class="text-muted"><?= e($it['comment']) ?> (รีวิวแล้ว)</small>
        <?php else: ?>
          <div class="stars">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" name="rating[<?= $pid ?>]" value="<?= $i ?>" id="r<?= $pid ?>_<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
              <label for="r<?= $pid ?>_<?= $i ?>"><i class="bi bi-star-fill"></i></label>
            <?php endfor; ?>
          </div>
          <textarea name="comment[<?= $pid ?>]" class="form-control mt-1" rows="2" placeholder="เล่าความประทับใจ เช่น ใส่สบาย ไซซ์ตรง"></textarea>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <button class="btn btn-pink w-100 mt-3">ส่งรีวิว</button>
  </form>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== register.php ==================== */
$files['register.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); $email = trim($_POST['email']); $phone = trim($_POST['phone']); $pw = $_POST['password'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'อีเมลไม่ถูกต้อง';
    elseif (strlen($pw) < 6) $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    elseif ($pw !== $_POST['password2']) $error = 'รหัสผ่านไม่ตรงกัน';
    elseif (empty($_POST['pdpa'])) $error = 'กรุณายอมรับนโยบายความเป็นส่วนตัว (PDPA) ก่อนสมัครสมาชิก';
    else {
        $st = $pdo->prepare("SELECT id FROM users WHERE email=?"); $st->execute([$email]);
        if ($st->fetch()) $error = 'อีเมลนี้ถูกใช้งานแล้ว';
        else {
            $pdo->prepare("INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)")
                ->execute([$name, $email, $phone, password_hash($pw, PASSWORD_DEFAULT)]);
            pdpa_log($pdo, $pdo->lastInsertId(), 'register');
            flash('สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ');
            redirect('login.php');
        }
    }
}
$title = 'สมัครสมาชิก'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card-box">
  <h4 class="text-center mb-3 price">สมัครสมาชิก</h4>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input name="name" class="form-control mb-2" placeholder="ชื่อ-นามสกุล" required value="<?= e($_POST['name'] ?? '') ?>">
    <input name="email" type="email" class="form-control mb-2" placeholder="อีเมล" required value="<?= e($_POST['email'] ?? '') ?>">
    <input name="phone" class="form-control mb-2" placeholder="เบอร์โทรศัพท์" value="<?= e($_POST['phone'] ?? '') ?>">
    <input name="password" type="password" class="form-control mb-2" placeholder="รหัสผ่าน" required>
    <input name="password2" type="password" class="form-control mb-3" placeholder="ยืนยันรหัสผ่าน" required>
    <div class="form-check mb-3 small">
      <input class="form-check-input" type="checkbox" name="pdpa" value="1" id="pdpa" required>
      <label class="form-check-label" for="pdpa">ข้าพเจ้ายินยอมให้เก็บและใช้ข้อมูลส่วนบุคคล (ชื่อ อีเมล เบอร์โทร) เพื่อการสมัครสมาชิกและการสั่งซื้อ ตาม<a href="privacy.php" target="_blank">นโยบายความเป็นส่วนตัว (PDPA)</a></label>
    </div>
    <button class="btn btn-pink w-100">สมัครสมาชิก</button>
  </form>
  <p class="text-center mt-3 small">มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== login.php ==================== */
$files['login.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare("SELECT * FROM users WHERE email=?"); $st->execute([trim($_POST['email'])]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'], $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'], 'name'=>$u['name'], 'email'=>$u['email'], 'phone'=>$u['phone'], 'role'=>$u['role']];
        $home = ['admin' => '/admin/', 'rider' => '/rider/'][$u['role']] ?? '/';
        $go = $_SESSION['redirect'] ?? BASE_URL . $home;
        unset($_SESSION['redirect']);
        redirect($go);
    }
    $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
}
$title = 'เข้าสู่ระบบ'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-4"><div class="card-box">
  <h4 class="text-center mb-3 price">เข้าสู่ระบบ</h4>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input name="email" type="email" class="form-control mb-2" placeholder="อีเมล" required>
    <input name="password" type="password" class="form-control mb-3" placeholder="รหัสผ่าน" required>
    <button class="btn btn-pink w-100">เข้าสู่ระบบ</button>
  </form>
  <p class="text-center mt-3 small">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

/* ==================== logout.php ==================== */
$files['logout.php'] = <<<'PINKY_EOF'
<?php require 'config.php'; session_destroy(); redirect(BASE_URL . '/');
PINKY_EOF;

/* ==================== admin/_nav.php ==================== */
$files['admin/_nav.php'] = <<<'PINKY_EOF'
<?php $cur = basename($_SERVER['PHP_SELF']);
$menu = ['index.php'=>['speedometer2','แดชบอร์ด'], 'orders.php'=>['receipt','คำสั่งซื้อ'],
         'products.php'=>['bag','สินค้า'], 'categories.php'=>['tags','หมวดหมู่'],
         'coupons.php'=>['ticket-perforated','คูปอง'], 'members.php'=>['people','สมาชิก'],
         'defects.php'=>['exclamation-triangle','แจ้งชำรุด']]; ?>
<div class="card-box mb-4 d-flex gap-2 flex-wrap">
  <?php foreach ($menu as $file => [$icon, $label]): ?>
    <a href="<?= $file ?>" class="btn <?= $cur === $file || ($cur === 'order.php' && $file === 'orders.php') ? 'btn-pink' : 'btn-outline-pink' ?>">
      <i class="bi bi-<?= $icon ?>"></i> <?= $label ?></a>
  <?php endforeach; ?>
</div>
PINKY_EOF;

/* ==================== admin/index.php ==================== */
$files['admin/index.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_admin();
$q = fn($sql) => $pdo->query($sql)->fetchColumn();
$stats = [
    ['ยอดขาย', money($q("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN('confirmed','shipping','delivered')")), 'cash-stack'],
    ['คำสั่งซื้อทั้งหมด', $q("SELECT COUNT(*) FROM orders"), 'receipt'],
    ['รอตรวจสอบ', $q("SELECT COUNT(*) FROM orders WHERE status='verifying'"), 'hourglass-split'],
    ['สมาชิก', $q("SELECT COUNT(*) FROM users WHERE role='member'"), 'people'],
];
$recent = $pdo->query("SELECT o.*, u.name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 10")->fetchAll();
$low = $pdo->query("SELECT id, name, stock FROM products WHERE stock < 5 ORDER BY stock")->fetchAll();
$title = 'แดชบอร์ด'; include '../includes/header.php'; include '_nav.php'; ?>
<div class="row g-3 mb-4">
  <?php foreach ($stats as [$label, $val, $icon]): ?>
  <div class="col-6 col-lg-3"><div class="card-box d-flex justify-content-between align-items-center">
    <div><small class="text-muted"><?= $label ?></small><div class="fs-4 price"><?= $val ?></div></div>
    <i class="bi bi-<?= $icon ?> fs-1" style="color:var(--pink)"></i>
  </div></div>
  <?php endforeach; ?>
</div>
<?php if ($low): ?>
<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> สินค้าใกล้หมด:
  <?php foreach ($low as $l): ?><a href="products.php?edit=<?= $l['id'] ?>" class="me-2"><?= e($l['name']) ?> (<?= $l['stock'] ?>)</a><?php endforeach; ?>
</div>
<?php endif; ?>
<div class="card-box table-responsive">
  <h5>คำสั่งซื้อล่าสุด</h5>
  <table class="table align-middle">
    <tr><th>#</th><th>ลูกค้า</th><th>ยอด</th><th>ชำระ</th><th>สถานะ</th><th></th></tr>
    <?php foreach ($recent as $o): ?>
    <tr><td><?= $o['id'] ?></td><td><?= e($o['name']) ?></td><td><?= money($o['total']) ?></td>
      <td><?= PAY[$o['payment_method']] ?></td><td><?= badge($o['status']) ?></td>
      <td><a href="order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-pink">จัดการ</a></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== admin/orders.php ==================== */
$files['admin/orders.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_admin();
$s = $_GET['status'] ?? '';
$sql = "SELECT o.*, u.name FROM orders o JOIN users u ON u.id=o.user_id" . (isset(STATUS[$s]) ? " WHERE o.status=?" : '') . " ORDER BY o.id DESC";
$st = $pdo->prepare($sql); $st->execute(isset(STATUS[$s]) ? [$s] : []); $orders = $st->fetchAll();
$title = 'คำสั่งซื้อ'; include '../includes/header.php'; include '_nav.php'; ?>
<div class="card-box">
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= !$s ? 'active' : '' ?>" href="orders.php">ทั้งหมด</a></li>
    <?php foreach (STATUS as $k => $v): ?>
      <li class="nav-item"><a class="nav-link <?= $s === $k ? 'active' : '' ?>" href="?status=<?= $k ?>"><?= $v ?></a></li>
    <?php endforeach; ?>
  </ul>
  <div class="table-responsive"><table class="table align-middle">
    <tr><th>#</th><th>วันที่</th><th>ลูกค้า</th><th>ยอด</th><th>ชำระ</th><th>สถานะ</th><th></th></tr>
    <?php foreach ($orders as $o): ?>
    <tr><td><?= $o['id'] ?></td><td><small><?= $o['created_at'] ?></small></td><td><?= e($o['name']) ?></td>
      <td><?= money($o['total']) ?></td><td><?= PAY[$o['payment_method']] ?></td>
      <td><?= badge($o['status']) ?></td>
      <td><a href="order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-pink">จัดการ</a></td></tr>
    <?php endforeach; ?>
  </table></div>
  <?php if (!$orders): ?><p class="text-center text-muted py-4 mb-0">ไม่มีคำสั่งซื้อ</p><?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== admin/order.php ==================== */
$files['admin/order.php'] = <<<'PINKY_EOF'
<?php $needMap = true; require '../config.php'; require_admin();
$o = get_order($pdo, $_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?"); $st->execute([$o['id']]); $items = $st->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset(STATUS[$_POST['status'] ?? ''])) {
    $new = $_POST['status'];
    $pdo->beginTransaction();
    if ($new === 'cancelled' && $o['status'] !== 'cancelled') {        // คืนสต็อกและคืนสิทธิ์คูปอง
        $r = $pdo->prepare("UPDATE products SET stock=stock+?, sold=GREATEST(sold-?,0) WHERE id=?");
        foreach ($items as $it) $r->execute([$it['qty'], $it['qty'], $it['product_id']]);
        if ($o['coupon_code']) $pdo->prepare("UPDATE coupons SET used=GREATEST(used-1,0) WHERE code=?")->execute([$o['coupon_code']]);
    }
    $pdo->prepare("UPDATE orders SET tracking_no=?, rider_id=? WHERE id=?")
        ->execute([trim($_POST['tracking_no']) ?: null, $_POST['rider_id'] ?: null, $o['id']]);
    set_status($pdo, $o['id'], $new, trim($_POST['note']) ?: STATUS[$new], $_POST['lat'], $_POST['lng']);
    $pdo->commit();
    flash('อัปเดตคำสั่งซื้อเรียบร้อย'); redirect("order.php?id={$o['id']}");
}
$st = $pdo->prepare("SELECT * FROM order_tracking WHERE order_id=? ORDER BY id"); $st->execute([$o['id']]); $tracks = $st->fetchAll();
$points = array_values(array_map(fn($t) => [(float)$t['lat'], (float)$t['lng']], array_filter($tracks, fn($t) => $t['lat'] !== null)));
$riders = $pdo->query("SELECT id, name FROM users WHERE role='rider' ORDER BY name")->fetchAll();
$title = 'จัดการคำสั่งซื้อ #' . $o['id']; include '../includes/header.php'; include '_nav.php'; ?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card-box mb-3">
      <div class="d-flex justify-content-between"><h5>คำสั่งซื้อ #<?= $o['id'] ?></h5><?= badge($o['status']) ?></div>
      <p class="small mb-2"><b><?= e($o['receiver']) ?></b> · <?= e($o['phone']) ?><br><?= e($o['address']) ?></p>
      <?php foreach ($items as $it): ?>
        <div class="d-flex justify-content-between small"><span><?= e($it['name']) ?> (<?= e($it['size']) ?>) × <?= $it['qty'] ?></span><span><?= money($it['price'] * $it['qty']) ?></span></div>
      <?php endforeach; ?>
      <?php if ($o['discount'] > 0): ?><div class="d-flex justify-content-between small price"><span>คูปอง <?= e($o['coupon_code']) ?></span><span>-<?= money($o['discount']) ?></span></div><?php endif; ?>
      <?php if (($o['vat'] ?? 0) > 0): ?><div class="d-flex justify-content-between small"><span>ภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>%</span><span><?= money($o['vat']) ?></span></div><?php endif; ?>
      <div class="text-end mt-2">ยอดรวม <b class="price"><?= money($o['total']) ?></b> (<?= PAY[$o['payment_method']] ?>)</div>
      <?php if ($o['charge_id']): ?><div class="text-end small text-muted">Omise: <?= e($o['charge_id']) ?></div><?php endif; ?>
    </div>
    <?php if ($o['slip']): ?>
    <div class="card-box mb-3 text-center"><h6>สลิปการโอน</h6>
      <a href="<?= img($o['slip']) ?>" target="_blank"><img src="<?= img($o['slip']) ?>" alt="สลิปการโอนเงินคำสั่งซื้อ #<?= $o['id'] ?>" class="img-fluid rounded" style="max-height:300px"></a></div>
    <?php endif; ?>
    <div class="card-box">
      <h6>ประวัติสถานะ</h6>
      <ul class="timeline mt-3">
        <?php foreach (array_reverse($tracks) as $t): ?>
          <li><b><?= STATUS[$t['status']] ?? e($t['status']) ?></b> <small class="text-muted"><?= $t['created_at'] ?></small><br>
            <small><?= e($t['note']) ?><?= $t['lat'] ? ' <i class="bi bi-geo-alt-fill price"></i>' : '' ?></small></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div class="col-lg-7"><div class="card-box">
    <h5 class="price">อัปเดตสถานะ / ตำแหน่งพัสดุ</h5>
    <form method="post">
      <div class="row g-2 mb-2">
        <div class="col-md-6"><select name="status" class="form-select">
          <?php foreach (STATUS as $k => $v): ?><option value="<?= $k ?>" <?= $o['status'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
        </select></div>
        <div class="col-md-6"><input name="tracking_no" class="form-control" placeholder="เลขพัสดุ (ถ้ามี)" value="<?= e($o['tracking_no']) ?>"></div>
      </div>
      <select name="rider_id" class="form-select mb-2">
        <option value="">-- มอบหมายไรเดอร์ (ไม่บังคับ) --</option>
        <?php foreach ($riders as $r): ?><option value="<?= $r['id'] ?>" <?= $o['rider_id'] == $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option><?php endforeach; ?>
      </select>
      <input name="note" class="form-control mb-2" placeholder="หมายเหตุ เช่น พัสดุถึงศูนย์กระจายสินค้าบางนา">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">คลิกแผนที่เพื่อระบุตำแหน่งพัสดุปัจจุบัน (ไม่บังคับ)</small>
        <button type="button" id="gpsBtn" class="btn btn-sm btn-outline-pink"><i class="bi bi-crosshair"></i> ใช้ GPS เครื่องนี้</button>
      </div>
      <div id="map" class="map mb-3"></div>
      <input type="hidden" name="lat" id="lat"><input type="hidden" name="lng" id="lng">
      <button class="btn btn-pink w-100">บันทึกการอัปเดต</button>
    </form>
  </div></div>
</div>
<script>
const shop = [<?= SHOP_LAT ?>, <?= SHOP_LNG ?>];
const dest = <?= $o['lat'] ? json_encode([(float)$o['lat'], (float)$o['lng']]) : 'null' ?>;
const pts  = <?= json_encode($points) ?>;
const map = L.map('map');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
const pin = (c, i) => L.divIcon({className: '', html: `<div class="map-pin" style="background:${c}"><i class="bi bi-${i}"></i></div>`, iconSize: [34, 34], iconAnchor: [17, 17]});
L.marker(shop, {icon: pin('#ff6b9d', 'shop')}).addTo(map);
if (dest) L.marker(dest, {icon: pin('#28a745', 'house-heart')}).addTo(map).bindPopup('ปลายทาง');
if (pts.length) L.polyline([shop, ...pts], {color: '#e84a7f', weight: 4}).addTo(map);
const all = [shop, ...pts]; if (dest) all.push(dest);
map.fitBounds(all, {padding: [40, 40], maxZoom: 14});
let cur;
function setCur(la, ln) {
  if (cur) cur.setLatLng([la, ln]); else cur = L.marker([la, ln], {icon: pin('#e84a7f', 'truck')}).addTo(map);
  lat.value = la.toFixed(7); lng.value = ln.toFixed(7);
}
map.on('click', e => setCur(e.latlng.lat, e.latlng.lng));
gpsBtn.onclick = () => navigator.geolocation.getCurrentPosition(p => { setCur(p.coords.latitude, p.coords.longitude); map.setView([p.coords.latitude, p.coords.longitude], 15); });
</script>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== admin/products.php ==================== */
$files['admin/products.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['delete'])) {
        $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$_POST['delete']]);
    } else {
        $audPost = implode(',', array_intersect(['men', 'women', 'kids'], (array)($_POST['audience'] ?? []))) ?: 'men,women';
        $salePost = ($_POST['sale_price'] ?? '') !== '' && (float)$_POST['sale_price'] > 0 && (float)$_POST['sale_price'] < (float)$_POST['price'] ? $_POST['sale_price'] : null;
        $d = [$_POST['category_id'] ?: null, trim($_POST['name']), $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['sizes'], $audPost, $salePost];
        $image = upload_image('image', 'products');
        if (!$image && upload_error()) { flash(upload_error(), 'danger'); redirect('products.php'); }
        if (!empty($_POST['id'])) {
            $pdo->prepare("UPDATE products SET category_id=?,name=?,description=?,price=?,stock=?,sizes=?,audience=?,sale_price=? WHERE id=?")->execute([...$d, $_POST['id']]);
            if ($image) $pdo->prepare("UPDATE products SET image=? WHERE id=?")->execute([$image, $_POST['id']]);
        } else {
            $pdo->prepare("INSERT INTO products(category_id,name,description,price,stock,sizes,audience,sale_price,image) VALUES(?,?,?,?,?,?,?,?,?)")->execute([...$d, $image]);
        }
    }
    flash('บันทึกสินค้าเรียบร้อย'); redirect('products.php');
}
$edit = null;
if (!empty($_GET['edit'])) { $st = $pdo->prepare("SELECT * FROM products WHERE id=?"); $st->execute([$_GET['edit']]); $edit = $st->fetch(); }
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name cat FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
$title = 'จัดการสินค้า'; include '../includes/header.php'; include '_nav.php'; ?>
<div class="row g-4">
  <div class="col-lg-4"><div class="card-box">
    <h5><?= $edit ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่' ?></h5>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
      <input name="name" class="form-control mb-2" placeholder="ชื่อสินค้า" required value="<?= e($edit['name'] ?? '') ?>">
      <select name="category_id" class="form-select mb-2">
        <option value="">-- เลือกหมวดหมู่ --</option>
        <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= ($edit['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
      </select>
      <div class="row g-2 mb-2">
        <div class="col"><input name="price" type="number" step="0.01" class="form-control" placeholder="ราคา" required value="<?= $edit['price'] ?? '' ?>"></div>
        <div class="col"><input name="stock" type="number" class="form-control" placeholder="สต็อก" required value="<?= $edit['stock'] ?? '' ?>"></div>
      </div>
      <input name="sizes" class="form-control mb-2" placeholder="ไซซ์ คั่นด้วย , เช่น 36,37,38" value="<?= e($edit['sizes'] ?? '36,37,38,39,40,41,42') ?>">
      <?php $audSel = explode(',', $edit['audience'] ?? 'men,women'); ?>
      <div class="mb-2 small">แสดงในหมวด:
        <?php foreach (['men' => 'ผู้ชาย', 'women' => 'ผู้หญิง', 'kids' => 'เด็ก'] as $k => $v): ?>
          <label class="ms-2"><input type="checkbox" name="audience[]" value="<?= $k ?>" <?= in_array($k, $audSel) ? 'checked' : '' ?>> <?= $v ?></label>
        <?php endforeach; ?>
      </div>
      <input name="sale_price" type="number" step="0.01" min="0" class="form-control mb-2" placeholder="ราคาโปรโมชั่น (เว้นว่าง = ไม่ลดราคา)" value="<?= e($edit['sale_price'] ?? '') ?>">
      <textarea name="description" class="form-control mb-2" rows="4" placeholder="รายละเอียดสินค้า"><?= e($edit['description'] ?? '') ?></textarea>
      <?php if ($edit): ?><img src="<?= img($edit['image']) ?>" alt="<?= e($edit['name']) ?>" width="80" class="rounded mb-2"><?php endif; ?>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control mb-3">
      <button class="btn btn-pink w-100">บันทึก</button>
      <?php if ($edit): ?><a href="products.php" class="btn btn-link w-100">ยกเลิก</a><?php endif; ?>
    </form>
  </div></div>
  <div class="col-lg-8"><div class="card-box table-responsive">
    <table class="table align-middle">
      <tr><th></th><th>สินค้า</th><th>หมวด</th><th>ราคา</th><th>สต็อก</th><th>ขายแล้ว</th><th>ดาว</th><th></th></tr>
      <?php foreach ($products as $p): ?>
      <tr><td><img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?>" width="50" height="50" loading="lazy" class="rounded"></td>
        <td><?= e($p['name']) ?></td><td><?= e($p['cat']) ?></td><td><?= $p['sale_price'] !== null ? '<s class="text-muted small">' . money($p['price']) . '</s> <span class="text-danger">' . money($p['sale_price']) . '</span>' : money($p['price']) ?></td>
        <td class="<?= $p['stock'] < 5 ? 'text-danger fw-bold' : '' ?>"><?= $p['stock'] ?></td><td><?= $p['sold'] ?></td>
        <td><small><?= $p['rating_avg'] ?> (<?= $p['rating_count'] ?>)</small></td>
        <td class="text-nowrap"><a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-pink"><i class="bi bi-pencil"></i></a>
          <form method="post" class="d-inline"><button name="delete" value="<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบสินค้านี้?')"><i class="bi bi-trash"></i></button></form></td></tr>
      <?php endforeach; ?>
    </table>
  </div></div>
</div>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== admin/categories.php ==================== */
$files['admin/categories.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['delete'])) $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$_POST['delete']]);
    elseif (!empty($_POST['id'])) $pdo->prepare("UPDATE categories SET name=? WHERE id=?")->execute([trim($_POST['name']), $_POST['id']]);
    else $pdo->prepare("INSERT INTO categories(name) VALUES(?)")->execute([trim($_POST['name'])]);
    flash('บันทึกเรียบร้อย'); redirect('categories.php');
}
$cats = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id=c.id) n FROM categories c ORDER BY id")->fetchAll();
$title = 'หมวดหมู่สินค้า'; include '../includes/header.php'; include '_nav.php'; ?>
<div class="card-box">
  <form method="post" class="d-flex gap-2 mb-4">
    <input name="name" class="form-control" placeholder="ชื่อหมวดหมู่ใหม่" required>
    <button class="btn btn-pink text-nowrap"><i class="bi bi-plus"></i> เพิ่ม</button>
  </form>
  <?php foreach ($cats as $c): ?>
  <form method="post" class="d-flex gap-2 mb-2 align-items-center">
    <input type="hidden" name="id" value="<?= $c['id'] ?>">
    <input name="name" class="form-control" value="<?= e($c['name']) ?>">
    <small class="text-muted text-nowrap"><?= $c['n'] ?> สินค้า</small>
    <button class="btn btn-outline-pink">บันทึก</button>
    <button name="delete" value="<?= $c['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('ลบหมวดหมู่นี้?')"><i class="bi bi-trash"></i></button>
  </form>
  <?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== admin/coupons.php ==================== */
$files['admin/coupons.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['toggle'])) {
        $pdo->prepare("UPDATE coupons SET active=1-active WHERE id=?")->execute([$_POST['toggle']]);
    } elseif (!empty($_POST['delete'])) {
        $pdo->prepare("DELETE FROM coupons WHERE id=?")->execute([$_POST['delete']]);
    } else {
        $type  = $_POST['type'] === 'percent' ? 'percent' : 'fixed';
        $value = (float)$_POST['value'];
        if ($type === 'percent') $value = min($value, 100);
        try {
            $pdo->prepare("INSERT INTO coupons(code,type,value,min_total,max_uses,expires_at) VALUES(?,?,?,?,?,?)")
                ->execute([strtoupper(trim($_POST['code'])), $type, $value, (float)$_POST['min_total'],
                           $_POST['max_uses'] ?: null, $_POST['expires_at'] ?: null]);
        } catch (PDOException) { flash('รหัสคูปองนี้มีอยู่แล้ว', 'danger'); redirect('coupons.php'); }
    }
    flash('บันทึกเรียบร้อย'); redirect('coupons.php');
}
$coupons = $pdo->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();
$title = 'คูปองส่วนลด'; include '../includes/header.php'; include '_nav.php'; ?>
<div class="row g-4">
  <div class="col-lg-4"><div class="card-box">
    <h5>สร้างคูปองใหม่</h5>
    <form method="post">
      <input name="code" class="form-control mb-2 text-uppercase" placeholder="รหัสคูปอง เช่น SALE20" required>
      <div class="row g-2 mb-2">
        <div class="col"><select name="type" class="form-select">
          <option value="percent">ลดเป็น %</option><option value="fixed">ลดเป็นบาท</option></select></div>
        <div class="col"><input name="value" type="number" step="0.01" min="1" class="form-control" placeholder="มูลค่า" required></div>
      </div>
      <input name="min_total" type="number" step="0.01" class="form-control mb-2" placeholder="ยอดซื้อขั้นต่ำ (บาท)" value="0">
      <input name="max_uses" type="number" class="form-control mb-2" placeholder="จำกัดจำนวนครั้ง (เว้นว่าง = ไม่จำกัด)">
      <label class="small text-muted">วันหมดอายุ (ไม่บังคับ)</label>
      <input name="expires_at" type="date" class="form-control mb-3">
      <button class="btn btn-pink w-100">สร้างคูปอง</button>
    </form>
  </div></div>
  <div class="col-lg-8"><div class="card-box table-responsive">
    <table class="table align-middle">
      <tr><th>รหัส</th><th>ส่วนลด</th><th>ขั้นต่ำ</th><th>ใช้แล้ว</th><th>หมดอายุ</th><th>สถานะ</th><th></th></tr>
      <?php foreach ($coupons as $c): ?>
      <tr>
        <td><span class="coupon-tag"><?= e($c['code']) ?></span></td>
        <td><?= $c['type'] === 'percent' ? (float)$c['value'] . '%' : money($c['value']) ?></td>
        <td><?= money($c['min_total']) ?></td>
        <td><?= $c['used'] ?> / <?= $c['max_uses'] ?? '∞' ?></td>
        <td><small><?= $c['expires_at'] ?? '-' ?></small></td>
        <td><span class="badge <?= $c['active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $c['active'] ? 'เปิด' : 'ปิด' ?></span></td>
        <td class="text-nowrap"><form method="post" class="d-inline">
          <button name="toggle" value="<?= $c['id'] ?>" class="btn btn-sm btn-outline-pink"><i class="bi bi-power"></i></button>
          <button name="delete" value="<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบคูปองนี้?')"><i class="bi bi-trash"></i></button>
        </form></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div></div>
</div>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== admin/members.php ==================== */
$files['admin/members.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_admin();
$roles = ['member' => 'สมาชิก', 'rider' => 'ไรเดอร์', 'admin' => 'แอดมิน'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['id'] != user()['id'] && isset($roles[$_POST['role'] ?? ''])) {
    $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$_POST['role'], $_POST['id']]);
    flash('เปลี่ยนสิทธิ์เรียบร้อย (ผู้ใช้ต้องออกจากระบบแล้วเข้าใหม่)'); redirect('members.php');
}
$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id=u.id) n,
  (SELECT COALESCE(SUM(total),0) FROM orders WHERE user_id=u.id AND status<>'cancelled') spent FROM users u ORDER BY id DESC")->fetchAll();
$title = 'สมาชิก'; include '../includes/header.php'; include '_nav.php'; ?>
<div class="card-box table-responsive"><table class="table align-middle">
  <tr><th>ชื่อ</th><th>อีเมล</th><th>โทร</th><th>คำสั่งซื้อ</th><th>ยอดซื้อ</th><th width="160">สิทธิ์</th></tr>
  <?php foreach ($users as $u): ?>
  <tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['phone']) ?></td>
    <td><?= $u['n'] ?></td><td><?= money($u['spent']) ?></td>
    <td><?php if ($u['id'] == user()['id']): ?><span class="badge bg-pink">แอดมิน (คุณ)</span>
      <?php else: ?><form method="post"><input type="hidden" name="id" value="<?= $u['id'] ?>">
        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
          <?php foreach ($roles as $k => $v): ?><option value="<?= $k ?>" <?= $u['role'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
        </select></form><?php endif; ?></td></tr>
  <?php endforeach; ?>
</table></div>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== rider/index.php ==================== */
$files['rider/index.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_rider();
$st = $pdo->prepare("SELECT * FROM orders WHERE rider_id=? AND status IN('confirmed','shipping') ORDER BY status DESC, id");
$st->execute([user()['id']]); $jobs = $st->fetchAll();
$title = 'งานจัดส่งของฉัน'; include '../includes/header.php'; ?>
<h4 class="mb-3"><i class="bi bi-scooter"></i> งานจัดส่งของฉัน (<?= count($jobs) ?>)</h4>
<?php foreach ($jobs as $j): ?>
  <div class="card-box job-card mb-3">
    <div class="d-flex justify-content-between mb-2"><b>#<?= $j['id'] ?> · <?= e($j['receiver']) ?></b><?= badge($j['status']) ?></div>
    <p class="small mb-2"><?= e($j['address']) ?></p>
    <?php if ($j['payment_method'] === 'cod'): ?>
      <div class="alert alert-warning py-1 small mb-2"><i class="bi bi-cash-coin"></i> เก็บเงินปลายทาง <?= money($j['total']) ?></div>
    <?php endif; ?>
    <div class="d-flex gap-2">
      <a href="tel:<?= e($j['phone']) ?>" class="btn btn-outline-pink btn-sm"><i class="bi bi-telephone"></i> โทร</a>
      <a href="run.php?id=<?= $j['id'] ?>" class="btn btn-pink btn-sm flex-grow-1">เปิดงานจัดส่ง</a>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$jobs): ?><p class="text-center text-muted py-5">ยังไม่มีงานที่ได้รับมอบหมาย</p><?php endif; ?>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== rider/run.php ==================== */
$files['rider/run.php'] = <<<'PINKY_EOF'
<?php $needMap = true; require '../config.php'; require_rider();
$st = $pdo->prepare("SELECT * FROM orders WHERE id=? AND rider_id=? AND status IN('confirmed','shipping')");
$st->execute([$_GET['id'] ?? 0, user()['id']]);
$o = $st->fetch();
if (!$o) not_found();

$a = $_POST['action'] ?? '';
$lat = $_POST['lat'] ?? null; $lng = $_POST['lng'] ?? null;
if ($a === 'ping' && $lat) {
    $pdo->prepare("UPDATE orders SET cur_lat=?, cur_lng=?, cur_at=NOW() WHERE id=?")->execute([(float)$lat, (float)$lng, $o['id']]);
    exit('ok');
}
if ($a === 'start' && $o['status'] === 'confirmed') {
    set_status($pdo, $o['id'], 'shipping', 'ไรเดอร์ ' . user()['name'] . ' รับพัสดุแล้ว กำลังนำส่ง', $lat, $lng);
    redirect("run.php?id={$o['id']}");
}
if ($a === 'done' && $o['status'] === 'shipping') {
    $note = 'ส่งถึงผู้รับเรียบร้อย' . ($o['payment_method'] === 'cod' ? ' · เก็บเงินปลายทาง ' . money($o['total']) . ' แล้ว' : '');
    set_status($pdo, $o['id'], 'delivered', $note, $lat, $lng);
    flash('ปิดงาน #' . $o['id'] . ' เรียบร้อย'); redirect('index.php');
}
$title = 'จัดส่ง #' . $o['id']; include '../includes/header.php'; ?>
<div class="card-box mb-3">
  <div class="d-flex justify-content-between"><h5>#<?= $o['id'] ?> · <?= e($o['receiver']) ?></h5><?= badge($o['status']) ?></div>
  <p class="small mb-2"><?= e($o['address']) ?></p>
  <?php if ($o['payment_method'] === 'cod'): ?>
    <div class="alert alert-warning py-2 mb-2"><i class="bi bi-cash-coin"></i> เก็บเงินปลายทาง <b><?= money($o['total']) ?></b></div>
  <?php endif; ?>
  <div class="d-flex gap-2">
    <a href="tel:<?= e($o['phone']) ?>" class="btn btn-outline-pink"><i class="bi bi-telephone"></i> โทรหาลูกค้า</a>
    <?php if ($o['lat']): ?>
      <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $o['lat'] ?>,<?= $o['lng'] ?>" target="_blank" class="btn btn-outline-pink flex-grow-1"><i class="bi bi-sign-turn-right"></i> นำทางด้วย Google Maps</a>
    <?php endif; ?>
  </div>
</div>
<div class="card-box">
  <div id="map" class="map mb-2"></div>
  <p id="gps" class="small text-muted"><i class="bi bi-broadcast"></i> กำลังหาตำแหน่ง GPS...</p>
  <form method="post">
    <input type="hidden" name="lat"><input type="hidden" name="lng">
    <?php if ($o['status'] === 'confirmed'): ?>
      <button name="action" value="start" class="btn btn-pink btn-lg w-100"><i class="bi bi-box-arrow-right"></i> รับพัสดุแล้ว เริ่มจัดส่ง</button>
    <?php else: ?>
      <button name="action" value="done" class="btn btn-success btn-lg w-100" onclick="return confirm('ยืนยันว่าส่งถึงผู้รับแล้ว?')"><i class="bi bi-check2-circle"></i> ส่งสำเร็จ</button>
    <?php endif; ?>
  </form>
  <small class="text-muted d-block mt-2">เปิดหน้านี้ค้างไว้ระหว่างจัดส่ง ลูกค้าจะเห็นตำแหน่งของคุณทุก ~15 วินาที</small>
</div>
<script>
const shop = [<?= SHOP_LAT ?>, <?= SHOP_LNG ?>];
const dest = <?= $o['lat'] ? json_encode([(float)$o['lat'], (float)$o['lng']]) : 'null' ?>;
const shipping = <?= json_encode($o['status'] === 'shipping') ?>;
const csrf = '<?= $_SESSION['csrf'] ?>';
const map = L.map('map').setView(dest || shop, 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
const pin = (c, i) => L.divIcon({className: '', html: `<div class="map-pin" style="background:${c}"><i class="bi bi-${i}"></i></div>`, iconSize: [34, 34], iconAnchor: [17, 17]});
if (dest) L.marker(dest, {icon: pin('#28a745', 'house-heart')}).addTo(map).bindPopup('ปลายทาง');
let me, last = 0;
navigator.geolocation.watchPosition(p => {
  const ll = [p.coords.latitude, p.coords.longitude];
  if (me) me.setLatLng(ll);
  else { me = L.marker(ll, {icon: pin('#7c4dff', 'scooter')}).addTo(map); map.fitBounds(dest ? [ll, dest] : [ll], {padding: [40, 40], maxZoom: 16}); }
  document.querySelector('[name=lat]').value = ll[0];
  document.querySelector('[name=lng]').value = ll[1];
  gps.innerHTML = '<i class="bi bi-broadcast text-success"></i> GPS พร้อม (แม่นยำ ±' + Math.round(p.coords.accuracy) + ' ม.)';
  if (shipping && Date.now() - last > 15000) {
    last = Date.now();
    fetch(location.href, {method: 'POST', body: new URLSearchParams({action: 'ping', lat: ll[0], lng: ll[1], csrf})});
  }
}, () => gps.textContent = 'ใช้ GPS ไม่ได้ กรุณาอนุญาตการเข้าถึงตำแหน่ง (เว็บต้องเป็น HTTPS)', {enableHighAccuracy: true});
</script>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

/* ==================== รูปสินค้า (SVG) ==================== */
$files['uploads/products/canvas-sneakers-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe3ec"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b1)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M55 300Q55 322 80 324L330 324Q352 322 352 302L352 285L55 285Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M60 285L62 205Q64 190 82 188L120 186Q150 160 175 158L200 175Q235 200 290 215Q345 228 352 262L352 285Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M290 215Q345 228 352 262L352 285L285 285Q300 250 290 215Z" fill="#ffb3cc" stroke="#b7b7b7" stroke-width="3"/><path d="M120 186Q140 150 172 150L184 168Q150 170 135 196Z" fill="#ffb3cc" stroke="#b7b7b7" stroke-width="3"/><path d="M168 174L190 198M184 165L208 192M198 170L224 198" stroke="#fff" stroke-width="5" fill="none"/><path d="M105 245Q170 215 245 252" stroke="#ffb3cc" stroke-width="10" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/canvas-sneakers-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe3ec"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b2)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M55 300Q55 322 80 324L330 324Q352 322 352 302L352 285L55 285Z" fill="#fffafc" stroke="#b7b4b5" stroke-width="3"/><path d="M60 285L62 205Q64 190 82 188L120 186Q150 160 175 158L200 175Q235 200 290 215Q345 228 352 262L352 285Z" fill="#ffc2d6" stroke="#b78b9a" stroke-width="3"/><path d="M290 215Q345 228 352 262L352 285L285 285Q300 250 290 215Z" fill="#ffffff" stroke="#b78b9a" stroke-width="3"/><path d="M120 186Q140 150 172 150L184 168Q150 170 135 196Z" fill="#ffffff" stroke="#b78b9a" stroke-width="3"/><path d="M168 174L190 198M184 165L208 192M198 170L224 198" stroke="#fff" stroke-width="5" fill="none"/><path d="M105 245Q170 215 245 252" stroke="#ffffff" stroke-width="10" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/canvas-sneakers-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b3" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe3ec"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b3)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M55 300Q55 322 80 324L330 324Q352 322 352 302L352 285L55 285Z" fill="#f4f4f4" stroke="#afafaf" stroke-width="3"/><path d="M60 285L62 205Q64 190 82 188L120 186Q150 160 175 158L200 175Q235 200 290 215Q345 228 352 262L352 285Z" fill="#3b3b45" stroke="#2a2a31" stroke-width="3"/><path d="M290 215Q345 228 352 262L352 285L285 285Q300 250 290 215Z" fill="#ff7fa8" stroke="#2a2a31" stroke-width="3"/><path d="M120 186Q140 150 172 150L184 168Q150 170 135 196Z" fill="#ff7fa8" stroke="#2a2a31" stroke-width="3"/><path d="M168 174L190 198M184 165L208 192M198 170L224 198" stroke="#fff" stroke-width="5" fill="none"/><path d="M105 245Q170 215 245 252" stroke="#ff7fa8" stroke-width="10" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/canvas-sneakers-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b4" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe3ec"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b4)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M55 300Q55 322 80 324L330 324Q352 322 352 302L352 285L55 285Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M60 285L62 205Q64 190 82 188L120 186Q150 160 175 158L200 175Q235 200 290 215Q345 228 352 262L352 285Z" fill="#bfeedd" stroke="#89ab9f" stroke-width="3"/><path d="M290 215Q345 228 352 262L352 285L285 285Q300 250 290 215Z" fill="#ffffff" stroke="#89ab9f" stroke-width="3"/><path d="M120 186Q140 150 172 150L184 168Q150 170 135 196Z" fill="#ffffff" stroke="#89ab9f" stroke-width="3"/><path d="M168 174L190 198M184 165L208 192M198 170L224 198" stroke="#fff" stroke-width="5" fill="none"/><path d="M105 245Q170 215 245 252" stroke="#ffffff" stroke-width="10" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/running-shoes-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b5" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e6f3ff"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b5)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M48 292Q48 326 85 326L335 326Q356 322 358 298L358 270L48 270Z" fill="#fff0f5" stroke="#b7acb0" stroke-width="3"/><path d="M55 272Q52 215 75 195L118 188Q148 170 180 165Q215 195 260 205Q335 220 358 262L358 272Z" fill="#ff9ebd" stroke="#b77188" stroke-width="3"/><circle cx="95" cy="225" r="3" fill="#ffbbd0"/><circle cx="95" cy="245" r="3" fill="#ffbbd0"/><circle cx="117" cy="225" r="3" fill="#ffbbd0"/><circle cx="117" cy="245" r="3" fill="#ffbbd0"/><circle cx="139" cy="225" r="3" fill="#ffbbd0"/><circle cx="139" cy="245" r="3" fill="#ffbbd0"/><circle cx="161" cy="225" r="3" fill="#ffbbd0"/><circle cx="161" cy="245" r="3" fill="#ffbbd0"/><circle cx="183" cy="225" r="3" fill="#ffbbd0"/><circle cx="183" cy="245" r="3" fill="#ffbbd0"/><circle cx="205" cy="225" r="3" fill="#ffbbd0"/><circle cx="205" cy="245" r="3" fill="#ffbbd0"/><circle cx="227" cy="225" r="3" fill="#ffbbd0"/><circle cx="227" cy="245" r="3" fill="#ffbbd0"/><circle cx="249" cy="225" r="3" fill="#ffbbd0"/><circle cx="249" cy="245" r="3" fill="#ffbbd0"/><circle cx="271" cy="225" r="3" fill="#ffbbd0"/><circle cx="271" cy="245" r="3" fill="#ffbbd0"/><circle cx="293" cy="225" r="3" fill="#ffbbd0"/><circle cx="293" cy="245" r="3" fill="#ffbbd0"/><path d="M90 262Q170 200 305 240Q215 238 90 262Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="2"/><path d="M64 200L52 172L76 174Z" fill="#ffffff"/><path d="M60 300H345" stroke="#ffffff" stroke-width="6" opacity=".8"/><path d="M170 168L190 192M186 162L210 188" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/running-shoes-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b6" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e6f3ff"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b6)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M48 292Q48 326 85 326L335 326Q356 322 358 298L358 270L48 270Z" fill="#f4f7ff" stroke="#afb1b7" stroke-width="3"/><path d="M55 272Q52 215 75 195L118 188Q148 170 180 165Q215 195 260 205Q335 220 358 262L358 272Z" fill="#6ea8ff" stroke="#4f78b7" stroke-width="3"/><circle cx="95" cy="225" r="3" fill="#99c2ff"/><circle cx="95" cy="245" r="3" fill="#99c2ff"/><circle cx="117" cy="225" r="3" fill="#99c2ff"/><circle cx="117" cy="245" r="3" fill="#99c2ff"/><circle cx="139" cy="225" r="3" fill="#99c2ff"/><circle cx="139" cy="245" r="3" fill="#99c2ff"/><circle cx="161" cy="225" r="3" fill="#99c2ff"/><circle cx="161" cy="245" r="3" fill="#99c2ff"/><circle cx="183" cy="225" r="3" fill="#99c2ff"/><circle cx="183" cy="245" r="3" fill="#99c2ff"/><circle cx="205" cy="225" r="3" fill="#99c2ff"/><circle cx="205" cy="245" r="3" fill="#99c2ff"/><circle cx="227" cy="225" r="3" fill="#99c2ff"/><circle cx="227" cy="245" r="3" fill="#99c2ff"/><circle cx="249" cy="225" r="3" fill="#99c2ff"/><circle cx="249" cy="245" r="3" fill="#99c2ff"/><circle cx="271" cy="225" r="3" fill="#99c2ff"/><circle cx="271" cy="245" r="3" fill="#99c2ff"/><circle cx="293" cy="225" r="3" fill="#99c2ff"/><circle cx="293" cy="245" r="3" fill="#99c2ff"/><path d="M90 262Q170 200 305 240Q215 238 90 262Z" fill="#ffe14d" stroke="#b7a237" stroke-width="2"/><path d="M64 200L52 172L76 174Z" fill="#ffe14d"/><path d="M60 300H345" stroke="#ffe14d" stroke-width="6" opacity=".8"/><path d="M170 168L190 192M186 162L210 188" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/running-shoes-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b7" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e6f3ff"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b7)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M48 292Q48 326 85 326L335 326Q356 322 358 298L358 270L48 270Z" fill="#e8e8f0" stroke="#a7a7ac" stroke-width="3"/><path d="M55 272Q52 215 75 195L118 188Q148 170 180 165Q215 195 260 205Q335 220 358 262L358 272Z" fill="#2e2e3a" stroke="#212129" stroke-width="3"/><circle cx="95" cy="225" r="3" fill="#6c6c75"/><circle cx="95" cy="245" r="3" fill="#6c6c75"/><circle cx="117" cy="225" r="3" fill="#6c6c75"/><circle cx="117" cy="245" r="3" fill="#6c6c75"/><circle cx="139" cy="225" r="3" fill="#6c6c75"/><circle cx="139" cy="245" r="3" fill="#6c6c75"/><circle cx="161" cy="225" r="3" fill="#6c6c75"/><circle cx="161" cy="245" r="3" fill="#6c6c75"/><circle cx="183" cy="225" r="3" fill="#6c6c75"/><circle cx="183" cy="245" r="3" fill="#6c6c75"/><circle cx="205" cy="225" r="3" fill="#6c6c75"/><circle cx="205" cy="245" r="3" fill="#6c6c75"/><circle cx="227" cy="225" r="3" fill="#6c6c75"/><circle cx="227" cy="245" r="3" fill="#6c6c75"/><circle cx="249" cy="225" r="3" fill="#6c6c75"/><circle cx="249" cy="245" r="3" fill="#6c6c75"/><circle cx="271" cy="225" r="3" fill="#6c6c75"/><circle cx="271" cy="245" r="3" fill="#6c6c75"/><circle cx="293" cy="225" r="3" fill="#6c6c75"/><circle cx="293" cy="245" r="3" fill="#6c6c75"/><path d="M90 262Q170 200 305 240Q215 238 90 262Z" fill="#ff6b9d" stroke="#b74d71" stroke-width="2"/><path d="M64 200L52 172L76 174Z" fill="#ff6b9d"/><path d="M60 300H345" stroke="#ff6b9d" stroke-width="6" opacity=".8"/><path d="M170 168L190 192M186 162L210 188" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/running-shoes-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b8" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e6f3ff"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b8)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M48 292Q48 326 85 326L335 326Q356 322 358 298L358 270L48 270Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M55 272Q52 215 75 195L118 188Q148 170 180 165Q215 195 260 205Q335 220 358 262L358 272Z" fill="#e5d9ff" stroke="#a49cb7" stroke-width="3"/><circle cx="95" cy="225" r="3" fill="#ece4ff"/><circle cx="95" cy="245" r="3" fill="#ece4ff"/><circle cx="117" cy="225" r="3" fill="#ece4ff"/><circle cx="117" cy="245" r="3" fill="#ece4ff"/><circle cx="139" cy="225" r="3" fill="#ece4ff"/><circle cx="139" cy="245" r="3" fill="#ece4ff"/><circle cx="161" cy="225" r="3" fill="#ece4ff"/><circle cx="161" cy="245" r="3" fill="#ece4ff"/><circle cx="183" cy="225" r="3" fill="#ece4ff"/><circle cx="183" cy="245" r="3" fill="#ece4ff"/><circle cx="205" cy="225" r="3" fill="#ece4ff"/><circle cx="205" cy="245" r="3" fill="#ece4ff"/><circle cx="227" cy="225" r="3" fill="#ece4ff"/><circle cx="227" cy="245" r="3" fill="#ece4ff"/><circle cx="249" cy="225" r="3" fill="#ece4ff"/><circle cx="249" cy="245" r="3" fill="#ece4ff"/><circle cx="271" cy="225" r="3" fill="#ece4ff"/><circle cx="271" cy="245" r="3" fill="#ece4ff"/><circle cx="293" cy="225" r="3" fill="#ece4ff"/><circle cx="293" cy="245" r="3" fill="#ece4ff"/><path d="M90 262Q170 200 305 240Q215 238 90 262Z" fill="#a58bff" stroke="#7664b7" stroke-width="2"/><path d="M64 200L52 172L76 174Z" fill="#a58bff"/><path d="M60 300H345" stroke="#a58bff" stroke-width="6" opacity=".8"/><path d="M170 168L190 192M186 162L210 188" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/high-heels-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b9" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff0f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b9)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M90 268L114 268L106 326L96 326Z" fill="#c79a82" stroke="#8f6e5d" stroke-width="3"/><path d="M66 150Q60 215 84 262Q100 276 130 276Q215 268 268 292Q335 312 352 320Q356 330 336 328Q245 326 150 302Q96 292 80 268Q50 215 56 148Z" fill="#e9c8b4" stroke="#a79081" stroke-width="3"/><path d="M66 150Q95 130 120 148Q150 190 200 218Q235 240 268 292" fill="none" stroke="#a79081" stroke-width="3"/><path d="M96 270Q170 262 250 284" stroke="#c79a82" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/high-heels-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b10" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff0f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b10)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M90 268L114 268L106 326L96 326Z" fill="#e84a7f" stroke="#a7355b" stroke-width="3"/><path d="M66 150Q60 215 84 262Q100 276 130 276Q215 268 268 292Q335 312 352 320Q356 330 336 328Q245 326 150 302Q96 292 80 268Q50 215 56 148Z" fill="#ff9ebd" stroke="#b77188" stroke-width="3"/><path d="M66 150Q95 130 120 148Q150 190 200 218Q235 240 268 292" fill="none" stroke="#b77188" stroke-width="3"/><path d="M96 270Q170 262 250 284" stroke="#e84a7f" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/high-heels-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b11" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff0f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b11)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M90 268L114 268L106 326L96 326Z" fill="#555566" stroke="#3d3d49" stroke-width="3"/><path d="M66 150Q60 215 84 262Q100 276 130 276Q215 268 268 292Q335 312 352 320Q356 330 336 328Q245 326 150 302Q96 292 80 268Q50 215 56 148Z" fill="#2b2b33" stroke="#1e1e24" stroke-width="3"/><path d="M66 150Q95 130 120 148Q150 190 200 218Q235 240 268 292" fill="none" stroke="#1e1e24" stroke-width="3"/><path d="M96 270Q170 262 250 284" stroke="#555566" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/high-heels-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b12" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff0f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b12)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M90 268L114 268L106 326L96 326Z" fill="#9c1230" stroke="#700c22" stroke-width="3"/><path d="M66 150Q60 215 84 262Q100 276 130 276Q215 268 268 292Q335 312 352 320Q356 330 336 328Q245 326 150 302Q96 292 80 268Q50 215 56 148Z" fill="#d92b4b" stroke="#9c1e36" stroke-width="3"/><path d="M66 150Q95 130 120 148Q150 190 200 218Q235 240 268 292" fill="none" stroke="#9c1e36" stroke-width="3"/><path d="M96 270Q170 262 250 284" stroke="#9c1230" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/sandals-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b13" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff7e0"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b13)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M58 300Q54 324 90 326L330 326Q354 322 350 300L345 284L66 284Z" fill="#ffd1dc" stroke="#b7969e" stroke-width="3"/><path d="M66 284Q70 262 100 262L320 262Q350 266 345 284Z" fill="#ffd7e1" stroke="#b7969e" stroke-width="3"/><path d="M290 270L215 215M290 270L255 205M215 215Q235 195 255 205" stroke="#ff9ebd" stroke-width="12" fill="none"/><circle cx="292" cy="268" r="8" fill="#ffffff"/></g></svg>
PINKY_EOF;

$files['uploads/products/sandals-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b14" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff7e0"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b14)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M58 300Q54 324 90 326L330 326Q354 322 350 300L345 284L66 284Z" fill="#ffd1dc" stroke="#b7969e" stroke-width="3"/><path d="M66 284Q70 262 100 262L320 262Q350 266 345 284Z" fill="#ffd7e1" stroke="#b7969e" stroke-width="3"/><path d="M150 266Q200 150 300 262L300 276Q215 205 165 276Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M175 240Q215 205 262 245" stroke="#ffb3cc" stroke-width="6" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/sandals-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b15" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff7e0"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b15)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M58 300Q54 324 90 326L330 326Q354 322 350 300L345 284L66 284Z" fill="#e3f3ff" stroke="#a3aeb7" stroke-width="3"/><path d="M66 284Q70 262 100 262L320 262Q350 266 345 284Z" fill="#e7f4ff" stroke="#a3aeb7" stroke-width="3"/><path d="M150 266Q200 150 300 262L300 276Q215 205 165 276Z" fill="#7cc4ff" stroke="#598db7" stroke-width="3"/><path d="M175 240Q215 205 262 245" stroke="#ffffff" stroke-width="6" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/sandals-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b16" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff7e0"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b16)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M58 300Q54 324 90 326L330 326Q354 322 350 300L345 284L66 284Z" fill="#e8e8ee" stroke="#a7a7ab" stroke-width="3"/><path d="M66 284Q70 262 100 262L320 262Q350 266 345 284Z" fill="#ebebf0" stroke="#a7a7ab" stroke-width="3"/><path d="M290 270L215 215M290 270L255 205M215 215Q235 195 255 205" stroke="#3b3b45" stroke-width="12" fill="none"/><circle cx="292" cy="268" r="8" fill="#ff7fa8"/></g></svg>
PINKY_EOF;

$files['uploads/products/leather-shoes-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b17" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f3ece6"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b17)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 305Q58 322 85 324L335 324Q352 320 350 305L350 292L62 292Z" fill="#c9b48c" stroke="#908164" stroke-width="3"/><path d="M62 292L62 314L112 314L112 292Z" fill="#908164"/><path d="M64 292L66 235Q70 215 95 212L140 214Q175 205 215 225Q250 238 300 250Q352 262 352 292Z" fill="#f4ecdc" stroke="#afa99e" stroke-width="3"/><path d="M150 214Q190 225 215 252" fill="none" stroke="#afa99e" stroke-width="3"/><path d="M175 222L215 232L212 254L172 244Z" fill="#d9c9a8" stroke="#afa99e" stroke-width="3"/><path d="M185 238H203" stroke="#f3d27a" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/leather-shoes-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b18" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f3ece6"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b18)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 305Q58 322 85 324L335 324Q352 320 350 305L350 292L62 292Z" fill="#3d2a1d" stroke="#2b1e14" stroke-width="3"/><path d="M62 292L62 314L112 314L112 292Z" fill="#2b1e14"/><path d="M64 292L66 235Q70 215 95 212L140 214Q175 205 215 225Q250 238 300 250Q352 262 352 292Z" fill="#8a5a3b" stroke="#63402a" stroke-width="3"/><path d="M150 214Q190 225 215 252" fill="none" stroke="#63402a" stroke-width="3"/><path d="M175 222L215 232L212 254L172 244Z" fill="#6b4129" stroke="#63402a" stroke-width="3"/><path d="M185 238H203" stroke="#f3d27a" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/leather-shoes-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b19" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f3ece6"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b19)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 305Q58 322 85 324L335 324Q352 320 350 305L350 292L62 292Z" fill="#111114" stroke="#0c0c0e" stroke-width="3"/><path d="M62 292L62 314L112 314L112 292Z" fill="#0c0c0e"/><path d="M64 292L66 235Q70 215 95 212L140 214Q175 205 215 225Q250 238 300 250Q352 262 352 292Z" fill="#25252b" stroke="#1a1a1e" stroke-width="3"/><path d="M150 214Q190 225 215 252" fill="none" stroke="#1a1a1e" stroke-width="3"/><path d="M150 216l16 8" stroke="#fff" stroke-width="4"/><path d="M166 224l16 8" stroke="#fff" stroke-width="4"/><path d="M182 232l16 8" stroke="#fff" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/leather-shoes-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b20" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f3ece6"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b20)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 305Q58 322 85 324L335 324Q352 320 350 305L350 292L62 292Z" fill="#c06080" stroke="#8a455c" stroke-width="3"/><path d="M62 292L62 314L112 314L112 292Z" fill="#8a455c"/><path d="M64 292L66 235Q70 215 95 212L140 214Q175 205 215 225Q250 238 300 250Q352 262 352 292Z" fill="#ffc2d6" stroke="#b78b9a" stroke-width="3"/><path d="M150 214Q190 225 215 252" fill="none" stroke="#b78b9a" stroke-width="3"/><path d="M175 222L215 232L212 254L172 244Z" fill="#ff9ebd" stroke="#b78b9a" stroke-width="3"/><path d="M185 238H203" stroke="#f3d27a" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/boots-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b21" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ece6f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b21)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 296L352 296L352 312Q350 326 330 326L80 326Q60 324 60 308Z" fill="#111114" stroke="#0c0c0e" stroke-width="3"/><path d="M62 296L62 322L120 322L120 296Z" fill="#0c0c0e"/><path d="M84 90L172 90L176 190Q215 205 270 218Q345 232 352 268L352 296L70 296Q66 190 84 90Z" fill="#26262c" stroke="#1b1b1f" stroke-width="3"/><path d="M84 90L172 90L173 112L83 112Z" fill="#444450" stroke="#1b1b1f" stroke-width="3"/><path d="M176 190Q215 230 270 240" fill="none" stroke="#1b1b1f" stroke-width="3"/><path d="M150 120L152 180" stroke="#444450" stroke-width="5"/></g></svg>
PINKY_EOF;

$files['uploads/products/boots-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b22" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ece6f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b22)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 296L352 296L352 312Q350 326 330 326L80 326Q60 324 60 308Z" fill="#4a3220" stroke="#352417" stroke-width="3"/><path d="M62 296L62 322L120 322L120 296Z" fill="#352417"/><path d="M84 90L172 90L176 190Q215 205 270 218Q345 232 352 268L352 296L70 296Q66 190 84 90Z" fill="#b5763f" stroke="#82542d" stroke-width="3"/><path d="M84 90L172 90L173 112L83 112Z" fill="#8f5a2c" stroke="#82542d" stroke-width="3"/><path d="M176 190Q215 230 270 240" fill="none" stroke="#82542d" stroke-width="3"/><path d="M150 120L152 180" stroke="#8f5a2c" stroke-width="5"/></g></svg>
PINKY_EOF;

$files['uploads/products/boots-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b23" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ece6f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b23)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 296L352 296L352 312Q350 326 330 326L80 326Q60 324 60 308Z" fill="#b5677f" stroke="#824a5b" stroke-width="3"/><path d="M62 296L62 322L120 322L120 296Z" fill="#824a5b"/><path d="M84 90L172 90L176 190Q215 205 270 218Q345 232 352 268L352 296L70 296Q66 190 84 90Z" fill="#f2b8c6" stroke="#ae848e" stroke-width="3"/><path d="M84 90L172 90L173 112L83 112Z" fill="#e48aa5" stroke="#ae848e" stroke-width="3"/><path d="M176 190Q215 230 270 240" fill="none" stroke="#ae848e" stroke-width="3"/><path d="M150 120L152 180" stroke="#e48aa5" stroke-width="5"/></g></svg>
PINKY_EOF;

$files['uploads/products/boots-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b24" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ece6f5"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b24)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 296L352 296L352 312Q350 326 330 326L80 326Q60 324 60 308Z" fill="#2e2e36" stroke="#212126" stroke-width="3"/><path d="M62 296L62 322L120 322L120 296Z" fill="#212126"/><path d="M84 90L172 90L176 190Q215 205 270 218Q345 232 352 268L352 296L70 296Q66 190 84 90Z" fill="#8d8d99" stroke="#65656e" stroke-width="3"/><path d="M84 90L172 90L173 112L83 112Z" fill="#5c5c68" stroke="#65656e" stroke-width="3"/><path d="M176 190Q215 230 270 240" fill="none" stroke="#65656e" stroke-width="3"/><path d="M150 120L152 180" stroke="#5c5c68" stroke-width="5"/></g></svg>
PINKY_EOF;

$files['uploads/products/football-boots-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b25" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e3f6e8"/><stop offset="1" stop-color="#fff5f8"/></linearGradient></defs><rect width="400" height="400" fill="url(#b25)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M82 316L96 316L93 338L85 338Z" fill="#212126"/><path d="M132 316L146 316L143 338L135 338Z" fill="#212126"/><path d="M200 316L214 316L211 338L203 338Z" fill="#212126"/><path d="M258 316L272 316L269 338L261 338Z" fill="#212126"/><path d="M310 316L324 316L321 338L313 338Z" fill="#212126"/><path d="M58 300L350 300Q354 316 340 318L70 318Q56 316 58 300Z" fill="#2e2e36" stroke="#212126" stroke-width="3"/><path d="M62 300L66 215Q70 196 92 194L130 192Q160 170 185 168Q225 205 285 220Q348 232 350 268L350 300Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M285 220Q348 232 350 268L350 300L280 300Q295 255 285 220Z" fill="#ff7fa8" stroke="#b7b7b7" stroke-width="3"/><path d="M100 270Q180 232 260 275" stroke="#ff7fa8" stroke-width="9" fill="none"/><path d="M178 180L198 204M194 174L216 200" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/football-boots-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b26" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e3f6e8"/><stop offset="1" stop-color="#fff5f8"/></linearGradient></defs><rect width="400" height="400" fill="url(#b26)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M82 316L96 316L93 338L85 338Z" fill="#16161c"/><path d="M132 316L146 316L143 338L135 338Z" fill="#16161c"/><path d="M200 316L214 316L211 338L203 338Z" fill="#16161c"/><path d="M258 316L272 316L269 338L261 338Z" fill="#16161c"/><path d="M310 316L324 316L321 338L313 338Z" fill="#16161c"/><path d="M58 300L350 300Q354 316 340 318L70 318Q56 316 58 300Z" fill="#1f1f27" stroke="#16161c" stroke-width="3"/><path d="M62 300L66 215Q70 196 92 194L130 192Q160 170 185 168Q225 205 285 220Q348 232 350 268L350 300Z" fill="#35c26b" stroke="#268b4d" stroke-width="3"/><path d="M285 220Q348 232 350 268L350 300L280 300Q295 255 285 220Z" fill="#ffffff" stroke="#268b4d" stroke-width="3"/><path d="M100 270Q180 232 260 275" stroke="#ffffff" stroke-width="9" fill="none"/><path d="M178 180L198 204M194 174L216 200" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/football-boots-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b27" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e3f6e8"/><stop offset="1" stop-color="#fff5f8"/></linearGradient></defs><rect width="400" height="400" fill="url(#b27)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M82 316L96 316L93 338L85 338Z" fill="#0c0c0e"/><path d="M132 316L146 316L143 338L135 338Z" fill="#0c0c0e"/><path d="M200 316L214 316L211 338L203 338Z" fill="#0c0c0e"/><path d="M258 316L272 316L269 338L261 338Z" fill="#0c0c0e"/><path d="M310 316L324 316L321 338L313 338Z" fill="#0c0c0e"/><path d="M58 300L350 300Q354 316 340 318L70 318Q56 316 58 300Z" fill="#111114" stroke="#0c0c0e" stroke-width="3"/><path d="M62 300L66 215Q70 196 92 194L130 192Q160 170 185 168Q225 205 285 220Q348 232 350 268L350 300Z" fill="#25252b" stroke="#1a1a1e" stroke-width="3"/><path d="M285 220Q348 232 350 268L350 300L280 300Q295 255 285 220Z" fill="#ffe14d" stroke="#1a1a1e" stroke-width="3"/><path d="M100 270Q180 232 260 275" stroke="#ffe14d" stroke-width="9" fill="none"/><path d="M178 180L198 204M194 174L216 200" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/football-boots-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b28" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e3f6e8"/><stop offset="1" stop-color="#fff5f8"/></linearGradient></defs><rect width="400" height="400" fill="url(#b28)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M82 316L96 316L93 338L85 338Z" fill="#a7355b"/><path d="M132 316L146 316L143 338L135 338Z" fill="#a7355b"/><path d="M200 316L214 316L211 338L203 338Z" fill="#a7355b"/><path d="M258 316L272 316L269 338L261 338Z" fill="#a7355b"/><path d="M310 316L324 316L321 338L313 338Z" fill="#a7355b"/><path d="M58 300L350 300Q354 316 340 318L70 318Q56 316 58 300Z" fill="#e84a7f" stroke="#a7355b" stroke-width="3"/><path d="M62 300L66 215Q70 196 92 194L130 192Q160 170 185 168Q225 205 285 220Q348 232 350 268L350 300Z" fill="#ff9ebd" stroke="#b77188" stroke-width="3"/><path d="M285 220Q348 232 350 268L350 300L280 300Q295 255 285 220Z" fill="#ffffff" stroke="#b77188" stroke-width="3"/><path d="M100 270Q180 232 260 275" stroke="#ffffff" stroke-width="9" fill="none"/><path d="M178 180L198 204M194 174L216 200" stroke="#fff" stroke-width="5" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/basketball-shoes-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b29" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe9d6"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b29)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M52 296Q52 326 88 326L332 326Q356 322 356 298L356 266L52 266Z" fill="#ffd1dc" stroke="#b7969e" stroke-width="3"/><path d="M60 268L62 122Q64 100 92 100L150 100L172 150Q205 182 252 208Q350 224 356 262L356 268Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M62 135L150 135" stroke="#ff6b9d" stroke-width="22"/><circle cx="125" cy="215" r="24" fill="#ff6b9d" stroke="#b7b7b7" stroke-width="3"/><path d="M125 200L130 212L143 213L133 221L136 233L125 226L114 233L117 221L107 213L120 212Z" fill="#fff"/><path d="M170 158L194 186M186 152L212 182M200 160L228 192" stroke="#fff" stroke-width="5" fill="none"/><path d="M290 214Q350 226 356 262L356 268L282 268Q300 240 290 214Z" fill="#ff6b9d" stroke="#b7b7b7" stroke-width="3"/></g></svg>
PINKY_EOF;

$files['uploads/products/basketball-shoes-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b30" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe9d6"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b30)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M52 296Q52 326 88 326L332 326Q356 322 356 298L356 266L52 266Z" fill="#fff0f2" stroke="#b7acae" stroke-width="3"/><path d="M60 268L62 122Q64 100 92 100L150 100L172 150Q205 182 252 208Q350 224 356 262L356 268Z" fill="#e63950" stroke="#a52939" stroke-width="3"/><path d="M62 135L150 135" stroke="#ffffff" stroke-width="22"/><circle cx="125" cy="215" r="24" fill="#ffffff" stroke="#a52939" stroke-width="3"/><path d="M125 200L130 212L143 213L133 221L136 233L125 226L114 233L117 221L107 213L120 212Z" fill="#fff"/><path d="M170 158L194 186M186 152L212 182M200 160L228 192" stroke="#fff" stroke-width="5" fill="none"/><path d="M290 214Q350 226 356 262L356 268L282 268Q300 240 290 214Z" fill="#ffffff" stroke="#a52939" stroke-width="3"/></g></svg>
PINKY_EOF;

$files['uploads/products/basketball-shoes-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b31" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe9d6"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b31)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M52 296Q52 326 88 326L332 326Q356 322 356 298L356 266L52 266Z" fill="#e8e8ee" stroke="#a7a7ab" stroke-width="3"/><path d="M60 268L62 122Q64 100 92 100L150 100L172 150Q205 182 252 208Q350 224 356 262L356 268Z" fill="#25252b" stroke="#1a1a1e" stroke-width="3"/><path d="M62 135L150 135" stroke="#f3c14a" stroke-width="22"/><circle cx="125" cy="215" r="24" fill="#f3c14a" stroke="#1a1a1e" stroke-width="3"/><path d="M125 200L130 212L143 213L133 221L136 233L125 226L114 233L117 221L107 213L120 212Z" fill="#fff"/><path d="M170 158L194 186M186 152L212 182M200 160L228 192" stroke="#fff" stroke-width="5" fill="none"/><path d="M290 214Q350 226 356 262L356 268L282 268Q300 240 290 214Z" fill="#f3c14a" stroke="#1a1a1e" stroke-width="3"/></g></svg>
PINKY_EOF;

$files['uploads/products/basketball-shoes-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b32" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe9d6"/><stop offset="1" stop-color="#ffd1dc"/></linearGradient></defs><rect width="400" height="400" fill="url(#b32)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M52 296Q52 326 88 326L332 326Q356 322 356 298L356 266L52 266Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M60 268L62 122Q64 100 92 100L150 100L172 150Q205 182 252 208Q350 224 356 262L356 268Z" fill="#ffb3cc" stroke="#b78092" stroke-width="3"/><path d="M62 135L150 135" stroke="#ffffff" stroke-width="22"/><circle cx="125" cy="215" r="24" fill="#ffffff" stroke="#b78092" stroke-width="3"/><path d="M125 200L130 212L143 213L133 221L136 233L125 226L114 233L117 221L107 213L120 212Z" fill="#fff"/><path d="M170 158L194 186M186 152L212 182M200 160L228 192" stroke="#fff" stroke-width="5" fill="none"/><path d="M290 214Q350 226 356 262L356 268L282 268Q300 240 290 214Z" fill="#ffffff" stroke="#b78092" stroke-width="3"/></g></svg>
PINKY_EOF;

$files['uploads/products/kids-shoes-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b33" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e0f4ff"/><stop offset="1" stop-color="#fff0f5"/></linearGradient></defs><rect width="400" height="400" fill="url(#b33)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M62 300Q60 326 90 326L335 326Q356 322 356 300L356 280L62 280Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M66 280L68 215Q72 198 95 196L135 194Q165 180 200 190Q240 215 300 228Q356 240 356 270L356 280Z" fill="#ffb3cc" stroke="#b78092" stroke-width="3"/><path d="M290 226Q356 240 356 270L356 280L280 280Q300 250 290 226Z" fill="#ffffff" stroke="#b78092" stroke-width="3"/><path d="M130 228L138 246L158 248L143 261L148 280L130 270L112 280L117 261L102 248L122 246Z" fill="#ffe14d" stroke="#e0a800" stroke-width="2"/><path d="M165 192L238 236L228 252L160 212Z" fill="#ffffff" stroke="#b78092" stroke-width="3"/><path d="M180 212L222 236" stroke="#fff" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/kids-shoes-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b34" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e0f4ff"/><stop offset="1" stop-color="#fff0f5"/></linearGradient></defs><rect width="400" height="400" fill="url(#b34)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M62 300Q60 326 90 326L335 326Q356 322 356 300L356 280L62 280Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M66 280L68 215Q72 198 95 196L135 194Q165 180 200 190Q240 215 300 228Q356 240 356 270L356 280Z" fill="#7cc4ff" stroke="#598db7" stroke-width="3"/><path d="M290 226Q356 240 356 270L356 280L280 280Q300 250 290 226Z" fill="#ffffff" stroke="#598db7" stroke-width="3"/><path d="M130 228L138 246L158 248L143 261L148 280L130 270L112 280L117 261L102 248L122 246Z" fill="#ffe14d" stroke="#e0a800" stroke-width="2"/><path d="M165 192L238 236L228 252L160 212Z" fill="#ffffff" stroke="#598db7" stroke-width="3"/><path d="M180 212L222 236" stroke="#fff" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/kids-shoes-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b35" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e0f4ff"/><stop offset="1" stop-color="#fff0f5"/></linearGradient></defs><rect width="400" height="400" fill="url(#b35)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M62 300Q60 326 90 326L335 326Q356 322 356 300L356 280L62 280Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M66 280L68 215Q72 198 95 196L135 194Q165 180 200 190Q240 215 300 228Q356 240 356 270L356 280Z" fill="#ffe14d" stroke="#b7a237" stroke-width="3"/><path d="M290 226Q356 240 356 270L356 280L280 280Q300 250 290 226Z" fill="#ff7fa8" stroke="#b7a237" stroke-width="3"/><path d="M130 228L138 246L158 248L143 261L148 280L130 270L112 280L117 261L102 248L122 246Z" fill="#ffe14d" stroke="#e0a800" stroke-width="2"/><path d="M165 192L238 236L228 252L160 212Z" fill="#ff7fa8" stroke="#b7a237" stroke-width="3"/><path d="M180 212L222 236" stroke="#fff" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/kids-shoes-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b36" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e0f4ff"/><stop offset="1" stop-color="#fff0f5"/></linearGradient></defs><rect width="400" height="400" fill="url(#b36)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M62 300Q60 326 90 326L335 326Q356 322 356 300L356 280L62 280Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M66 280L68 215Q72 198 95 196L135 194Q165 180 200 190Q240 215 300 228Q356 240 356 270L356 280Z" fill="#d8c8ff" stroke="#9b90b7" stroke-width="3"/><path d="M290 226Q356 240 356 270L356 280L280 280Q300 250 290 226Z" fill="#ffffff" stroke="#9b90b7" stroke-width="3"/><path d="M130 228L138 246L158 248L143 261L148 280L130 270L112 280L117 261L102 248L122 246Z" fill="#ffe14d" stroke="#e0a800" stroke-width="2"/><path d="M165 192L238 236L228 252L160 212Z" fill="#ffffff" stroke="#9b90b7" stroke-width="3"/><path d="M180 212L222 236" stroke="#fff" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/school-shoes-01.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b37" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#eef0f5"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b37)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 305Q58 322 85 324L335 324Q352 320 350 305L350 292L62 292Z" fill="#0d0d10" stroke="#09090b" stroke-width="3"/><path d="M62 292L62 314L112 314L112 292Z" fill="#09090b"/><path d="M64 292L66 235Q70 215 95 212L140 214Q175 205 215 225Q250 238 300 250Q352 262 352 292Z" fill="#1d1d22" stroke="#141418" stroke-width="3"/><path d="M150 214Q190 225 215 252" fill="none" stroke="#141418" stroke-width="3"/><path d="M150 216l16 8" stroke="#fff" stroke-width="4"/><path d="M166 224l16 8" stroke="#fff" stroke-width="4"/><path d="M182 232l16 8" stroke="#fff" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/school-shoes-02.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b38" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#eef0f5"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b38)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 305Q58 322 85 324L335 324Q352 320 350 305L350 292L62 292Z" fill="#0d0d10" stroke="#09090b" stroke-width="3"/><path d="M62 292L62 314L112 314L112 292Z" fill="#09090b"/><path d="M64 292L66 235Q70 215 95 212L140 214Q175 205 215 225Q250 238 300 250Q352 262 352 292Z" fill="#1d1d22" stroke="#141418" stroke-width="3"/><path d="M150 214Q190 225 215 252" fill="none" stroke="#141418" stroke-width="3"/><path d="M150 218L205 252L196 268L142 234Z" fill="#33333b" stroke="#141418" stroke-width="3"/><circle cx="172" cy="244" r="5" fill="#f3d27a"/></g></svg>
PINKY_EOF;

$files['uploads/products/school-shoes-03.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b39" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#eef0f5"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b39)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M55 300Q55 322 80 324L330 324Q352 322 352 302L352 285L55 285Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M60 285L62 205Q64 190 82 188L120 186Q150 160 175 158L200 175Q235 200 290 215Q345 228 352 262L352 285Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M290 215Q345 228 352 262L352 285L285 285Q300 250 290 215Z" fill="#f1f1f1" stroke="#b7b7b7" stroke-width="3"/><path d="M120 186Q140 150 172 150L184 168Q150 170 135 196Z" fill="#f1f1f1" stroke="#b7b7b7" stroke-width="3"/><path d="M168 174L190 198M184 165L208 192M198 170L224 198" stroke="#fff" stroke-width="5" fill="none"/><path d="M105 245Q170 215 245 252" stroke="#f1f1f1" stroke-width="10" fill="none"/></g></svg>
PINKY_EOF;

$files['uploads/products/school-shoes-04.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="b40" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#eef0f5"/><stop offset="1" stop-color="#ffe3ec"/></linearGradient></defs><rect width="400" height="400" fill="url(#b40)"/><circle cx="310" cy="90" r="70" fill="#fff" opacity=".35"/><ellipse cx="200" cy="332" rx="150" ry="11" fill="#000" opacity=".12"/><g stroke-linejoin="round" stroke-linecap="round"><path d="M60 305Q58 322 85 324L335 324Q352 320 350 305L350 292L62 292Z" fill="#0d0d10" stroke="#09090b" stroke-width="3"/><path d="M62 292L62 314L112 314L112 292Z" fill="#09090b"/><path d="M64 292L66 235Q70 215 95 212L140 214Q175 205 215 225Q250 238 300 250Q352 262 352 292Z" fill="#1d1d22" stroke="#141418" stroke-width="3"/><path d="M150 214Q190 225 215 252" fill="none" stroke="#141418" stroke-width="3"/><path d="M175 222L215 232L212 254L172 244Z" fill="#33333b" stroke="#141418" stroke-width="3"/><path d="M185 238H203" stroke="#f3d27a" stroke-width="4"/></g></svg>
PINKY_EOF;

$files['uploads/products/default.svg'] = <<<'PINKY_EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><rect width="400" height="400" fill="#ffe3ec"/><path d="M55 300Q55 322 80 324L330 324Q352 322 352 302L352 285L55 285Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M60 285L62 205Q64 190 82 188L120 186Q150 160 175 158L200 175Q235 200 290 215Q345 228 352 262L352 285Z" fill="#ffffff" stroke="#b7b7b7" stroke-width="3"/><path d="M290 215Q345 228 352 262L352 285L285 285Q300 250 290 215Z" fill="#ffb3cc" stroke="#b7b7b7" stroke-width="3"/><path d="M120 186Q140 150 172 150L184 168Q150 170 135 196Z" fill="#ffb3cc" stroke="#b7b7b7" stroke-width="3"/><path d="M168 174L190 198M184 165L208 192M198 170L224 198" stroke="#fff" stroke-width="5" fill="none"/><path d="M105 245Q170 215 245 252" stroke="#ffb3cc" stroke-width="10" fill="none"/></svg>
PINKY_EOF;

/* ==================== uploads/.htaccess (กันรันสคริปต์ในโฟลเดอร์อัปโหลด) ==================== */
$files['uploads/.htaccess'] = <<<'PINKY_EOF'
<FilesMatch "\.(php|phtml|phar|php\d)$">
  Require all denied
</FilesMatch>
Options -Indexes
PINKY_EOF;

$files['privacy.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
$title = 'นโยบายความเป็นส่วนตัว (PDPA)'; $desc = 'นโยบายความเป็นส่วนตัวของ Sneaks Store ตาม พ.ร.บ.คุ้มครองข้อมูลส่วนบุคคล (PDPA) ข้อมูลที่เก็บ วัตถุประสงค์ ระยะเวลา และสิทธิของเจ้าของข้อมูล'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="card-box">
  <h4 class="price"><i class="bi bi-shield-check"></i> นโยบายความเป็นส่วนตัว (PDPA)</h4>
  <p class="text-muted small">ตาม พ.ร.บ.คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 · เวอร์ชัน <?= e(PDPA_VERSION) ?></p>

  <h6 class="mt-4">1. ข้อมูลที่เราเก็บ</h6>
  <ul>
    <li>ข้อมูลบัญชี: ชื่อ อีเมล เบอร์โทร รหัสผ่าน (เก็บแบบเข้ารหัสทางเดียว)</li>
    <li>ข้อมูลจัดส่ง: ชื่อผู้รับ เบอร์โทร ที่อยู่ และพิกัดบนแผนที่</li>
    <li>ข้อมูลการชำระเงิน: ประวัติคำสั่งซื้อ และรูปสลิป (ข้อมูลบัตรส่งตรงถึง Omise ร้านไม่เก็บเลขบัตร)</li>
    <li>ตำแหน่งสดของไรเดอร์ขณะจัดส่ง (เฉพาะพนักงานจัดส่ง)</li>
    <li>รายงานสินค้าชำรุด: ข้อความ และรูปถ่ายที่คุณแนบ (ไม่ควรมีใบหน้าหรือข้อมูลส่วนตัวอื่นในรูป)</li>
  </ul>

  <h6 class="mt-4">2. วัตถุประสงค์</h6>
  <p>เพื่อสร้างบัญชี ประมวลผลและจัดส่งคำสั่งซื้อ ติดตามพัสดุ ตรวจสอบการชำระเงิน และปฏิบัติตามกฎหมาย เราไม่ขายข้อมูลของคุณให้บุคคลอื่น</p>

  <h6 class="mt-4">3. รูปภาพที่อัปโหลด</h6>
  <p>ระบบตรวจสอบว่าไฟล์เป็นรูปภาพจริง (ตรวจจากเนื้อไฟล์ ไม่ใช่แค่นามสกุล) รองรับ JPG, PNG, WEBP ขนาดไม่เกิน 5 MB และสร้างภาพใหม่ทุกครั้งเพื่อลบข้อมูลแฝง เช่น พิกัด GPS ชื่ออุปกรณ์ และเวลาถ่ายภาพ ออกจากไฟล์</p>

  <h6 class="mt-4">4. บริการภายนอกที่เกี่ยวข้อง</h6>
  <ul>
    <li>OpenStreetMap / Nominatim: เมื่อคุณพิมพ์ที่อยู่ ข้อความที่อยู่จะถูกส่งไปค้นหาพิกัดเพื่อปักหมุดอัตโนมัติ</li>
    <li>Omise: ประมวลผลการชำระเงินด้วยบัตรและ QR พร้อมเพย์</li>
  </ul>

  <h6 class="mt-4">5. ระยะเวลาเก็บรักษา</h6>
  <p>เก็บข้อมูลตราบเท่าที่บัญชียังใช้งานอยู่ และเก็บข้อมูลคำสั่งซื้อตามระยะเวลาที่กฎหมายภาษีและบัญชีกำหนด</p>

  <h6 class="mt-4">6. สิทธิของเจ้าของข้อมูล</h6>
  <p>คุณมีสิทธิขอเข้าถึง แก้ไข ลบ ระงับการใช้ โอนย้าย คัดค้าน และเพิกถอนความยินยอมได้ตลอดเวลา โดยติดต่อ
     <a href="mailto:<?= e(PDPA_CONTACT) ?>"><?= e(PDPA_CONTACT) ?></a> การเพิกถอนไม่กระทบการประมวลผลที่ทำไปแล้วก่อนหน้า</p>

  <h6 class="mt-4">7. บันทึกการให้ความยินยอม</h6>
  <p class="mb-0">ระบบบันทึกวันเวลา เวอร์ชันนโยบาย และ IP ทุกครั้งที่คุณยินยอม (ตอนสมัครสมาชิกและตอนสั่งซื้อ) เพื่อใช้เป็นหลักฐานตามกฎหมาย</p>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

$files['contact.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
$title = 'ติดต่อเรา'; $desc = 'ติดต่อ Sneaks Store โทร ' . SHOP_PHONE . ' อีเมล ' . SHOP_EMAIL . ' รหัสไปรษณีย์ ' . SHOP_POSTCODE . ' สอบถามสินค้า การสั่งซื้อ และแจ้งสินค้าชำรุด'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-6"><div class="card-box">
  <h4 class="price mb-3"><i class="bi bi-telephone-fill"></i> ติดต่อเรา</h4>
  <p class="text-muted">Sneaks Store ยินดีให้บริการ หากมีข้อสงสัยเรื่องสินค้า การสั่งซื้อ หรือการจัดส่ง ติดต่อเราได้ตามช่องทางด้านล่าง</p>
  <ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex align-items-center gap-3">
      <i class="bi bi-telephone fs-4 price"></i>
      <div><small class="text-muted d-block">เบอร์โทร</small><a href="tel:<?= e(SHOP_PHONE) ?>" class="fs-5"><?= e(SHOP_PHONE) ?></a></div>
    </li>
    <li class="list-group-item d-flex align-items-center gap-3">
      <i class="bi bi-envelope fs-4 price"></i>
      <div><small class="text-muted d-block">อีเมล</small><a href="mailto:<?= e(SHOP_EMAIL) ?>" class="fs-5"><?= e(SHOP_EMAIL) ?></a></div>
    </li>
    <li class="list-group-item d-flex align-items-center gap-3">
      <i class="bi bi-mailbox fs-4 price"></i>
      <div><small class="text-muted d-block">รหัสไปรษณีย์</small><span class="fs-5"><?= e(SHOP_POSTCODE) ?></span></div>
    </li>
  </ul>
  <div class="d-grid gap-2">
    <a href="defect.php" class="btn btn-pink"><i class="bi bi-exclamation-triangle"></i> แจ้งสินค้าชำรุด / แนบรูป / แชทกับร้าน</a>
    <a href="privacy.php" class="btn btn-outline-pink btn-sm"><i class="bi bi-shield-check"></i> นโยบายความเป็นส่วนตัว (PDPA)</a>
  </div>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

$files['includes/defect_thread.php'] = <<<'PINKY_EOF'
<?php /* แสดงแชทของรายงานสินค้าชำรุด : ต้องมี $msgs และ $viewer ('customer' | 'admin') */ ?>
<style>
.chat{max-height:460px;overflow-y:auto;background:#fff8fb;border-radius:12px;padding:1rem}
.bub{max-width:80%;padding:.6rem .9rem;border-radius:14px;margin-bottom:.8rem;word-break:break-word}
.bub.me{background:#e84a7f;color:#fff;margin-left:auto;border-bottom-right-radius:4px}
.bub.them{background:#fff;border:1px solid #ffd1dc;border-bottom-left-radius:4px}
.bub img{width:120px;height:120px;border-radius:8px;margin:.4rem .4rem 0 0;object-fit:cover}
.bub small.t{opacity:.75;display:block;margin-top:.3rem;font-size:.7rem}
</style>
<div class="chat" id="chat">
<?php foreach ($msgs as $m):
    $mine = $m['sender'] === $viewer;
    $who = $m['sender'] === 'admin' ? 'ร้านค้า' : ($viewer === 'admin' ? 'ลูกค้า' : 'คุณ'); ?>
  <div class="bub <?= $mine ? 'me' : 'them' ?>">
    <b class="small"><?= e($who) ?></b><br>
    <?php if ($m['message'] !== null && $m['message'] !== ''): ?><?= nl2br(e($m['message'])) ?><?php endif; ?>
    <?php if (!empty($m['images'])): ?><div>
      <?php foreach (explode(',', $m['images']) as $im): ?>
        <a href="<?= e(img($im)) ?>" target="_blank"><img src="<?= e(img($im)) ?>" alt="รูปแนบ"></a>
      <?php endforeach; ?></div>
    <?php endif; ?>
    <small class="t"><?= e($m['created_at']) ?></small>
  </div>
<?php endforeach; ?>
</div>
<script>const chat = document.getElementById('chat'); chat.scrollTop = chat.scrollHeight;</script>
PINKY_EOF;

$files['defect.php'] = <<<'PINKY_EOF'
<?php require 'config.php'; require_login();
$uid = user()['id'];
$st = $pdo->prepare("SELECT oi.id, oi.order_id, oi.name, oi.size FROM order_items oi JOIN orders o ON o.id=oi.order_id
                     WHERE o.user_id=? AND o.status IN ('shipping','delivered') ORDER BY oi.order_id DESC, oi.id");
$st->execute([$uid]); $items = $st->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $picked = null;
    foreach ($items as $it) if ((string)$it['id'] === ($_POST['item'] ?? '')) $picked = $it;
    $reason = $_POST['reason'] ?? ''; $detail = trim($_POST['detail'] ?? '');
    if (!$picked) $error = 'กรุณาเลือกสินค้าที่ต้องการแจ้ง';
    elseif (!isset(DEFECT_REASONS[$reason])) $error = 'กรุณาเลือกลักษณะปัญหา';
    elseif (mb_strlen($detail) < 10) $error = 'กรุณาพิมพ์รายละเอียดปัญหาอย่างน้อย 10 ตัวอักษร';
    elseif (mb_strlen($detail) > 2000) $error = 'รายละเอียดยาวเกิน 2,000 ตัวอักษร';
    elseif (empty($_POST['pdpa'])) $error = 'กรุณายินยอมให้ร้านเก็บข้อความและรูปภาพที่แนบ (PDPA)';
    else {
        $imgs = upload_images('img', 3, 'defects');
        if ($imgs === null) $error = upload_error();
        else {
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO defect_reports(user_id,order_id,product_name,size,reason) VALUES(?,?,?,?,?)")
                ->execute([$uid, $picked['order_id'], $picked['name'], $picked['size'], $reason]);
            $rid = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO defect_messages(report_id,sender,message,images) VALUES(?,?,?,?)")
                ->execute([$rid, 'customer', $detail, $imgs ? implode(',', $imgs) : null]);
            pdpa_log($pdo, $uid, 'defect_report');
            $pdo->commit();
            flash('ส่งเรื่องแจ้งสินค้าชำรุดเรียบร้อย ร้านค้าจะตอบกลับในแชทด้านล่าง');
            redirect("defect_view.php?id=$rid");
        }
    }
}
$st = $pdo->prepare("SELECT * FROM defect_reports WHERE user_id=? ORDER BY id DESC"); $st->execute([$uid]); $reports = $st->fetchAll();
$pre = (int)($_GET['order'] ?? 0);
$title = 'แจ้งสินค้าชำรุด'; include 'includes/header.php'; ?>
<div class="row g-4">
  <div class="col-lg-7"><div class="card-box">
    <h5 class="price"><i class="bi bi-exclamation-triangle-fill"></i> แจ้งสินค้าชำรุด</h5>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <?php if (!$items): ?>
      <p class="text-muted my-4">แจ้งได้เมื่อมีคำสั่งซื้อที่อยู่ระหว่างจัดส่งหรือจัดส่งสำเร็จแล้ว</p>
    <?php else: ?>
    <form method="post" enctype="multipart/form-data">
      <label class="form-label small mb-1">สินค้าที่มีปัญหา</label>
      <select name="item" class="form-select mb-2" required>
        <option value="">-- เลือกสินค้า --</option>
        <?php $sel = false; foreach ($items as $it):
            $s = (!$sel && ($_POST['item'] ?? '') === (string)$it['id']) || (!$sel && !isset($_POST['item']) && $pre === (int)$it['order_id']); if ($s) $sel = true; ?>
          <option value="<?= $it['id'] ?>" <?= $s ? 'selected' : '' ?>>#<?= $it['order_id'] ?> · <?= e($it['name']) ?> (ไซซ์ <?= e($it['size']) ?>)</option>
        <?php endforeach; ?>
      </select>
      <label class="form-label small mb-1">ลักษณะปัญหา</label>
      <select name="reason" class="form-select mb-2" required>
        <option value="">-- เลือกลักษณะปัญหา --</option>
        <?php foreach (DEFECT_REASONS as $k => $v): ?><option value="<?= $k ?>" <?= ($_POST['reason'] ?? '') === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?>
      </select>
      <label class="form-label small mb-1">พิมพ์ปัญหาเพิ่มเติม</label>
      <textarea name="detail" class="form-control mb-2" rows="4" maxlength="2000" required placeholder="เช่น พื้นรองเท้าด้านซ้ายหลุดออกจากตัวรองเท้า พบตอนแกะกล่อง"><?= e($_POST['detail'] ?? '') ?></textarea>
      <label class="form-label small mb-1">แนบรูปสินค้า (สูงสุด 3 รูป · JPG/PNG/WEBP · ไม่เกิน 5 MB ต่อรูป)</label>
      <input type="file" name="img1" accept="image/jpeg,image/png,image/webp" class="form-control mb-1">
      <input type="file" name="img2" accept="image/jpeg,image/png,image/webp" class="form-control mb-1">
      <input type="file" name="img3" accept="image/jpeg,image/png,image/webp" class="form-control mb-2">
      <small class="text-muted d-block mb-2"><i class="bi bi-shield-lock"></i> ระบบตรวจว่าเป็นรูปจริงและลบพิกัด GPS ออกจากรูปอัตโนมัติ</small>
      <div class="form-check mb-3 small">
        <input class="form-check-input" type="checkbox" name="pdpa" value="1" id="pdpa" required>
        <label class="form-check-label" for="pdpa">ยินยอมให้ร้านเก็บข้อความและรูปที่แนบเพื่อตรวจสอบและแก้ไขปัญหา ตาม<a href="privacy.php" target="_blank">นโยบายความเป็นส่วนตัว (PDPA)</a></label>
      </div>
      <button class="btn btn-pink w-100"><i class="bi bi-send"></i> ส่งเรื่องแจ้งชำรุด</button>
    </form>
    <?php endif; ?>
  </div></div>

  <div class="col-lg-5"><div class="card-box">
    <h5>เรื่องที่แจ้งไว้</h5>
    <?php foreach ($reports as $r): ?>
      <a href="defect_view.php?id=<?= $r['id'] ?>" class="d-block text-decoration-none text-dark border rounded p-2 mb-2">
        <div class="d-flex justify-content-between"><b>#<?= $r['id'] ?> <?= e($r['product_name']) ?></b><?= defect_badge($r['status']) ?></div>
        <small class="text-muted"><?= e(DEFECT_REASONS[$r['reason']] ?? '') ?> · <?= e($r['created_at']) ?></small>
      </a>
    <?php endforeach; ?>
    <?php if (!$reports): ?><p class="text-muted small mb-0">ยังไม่มีเรื่องที่แจ้ง</p><?php endif; ?>
    <hr><small class="text-muted">ต้องการคุยด่วน โทร <a href="tel:<?= e(SHOP_PHONE) ?>"><?= e(SHOP_PHONE) ?></a> หรืออีเมล <a href="mailto:<?= e(SHOP_EMAIL) ?>"><?= e(SHOP_EMAIL) ?></a></small>
  </div></div>
</div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

$files['defect_view.php'] = <<<'PINKY_EOF'
<?php require 'config.php'; require_login();
$st = $pdo->prepare("SELECT * FROM defect_reports WHERE id=?"); $st->execute([(int)($_GET['id'] ?? 0)]); $r = $st->fetch();
if (!$r || ($r['user_id'] != user()['id'] && user()['role'] !== 'admin')) { not_found(); }
$closed = in_array($r['status'], ['resolved', 'rejected']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$closed) {
    $msg = trim($_POST['message'] ?? '');
    if (mb_strlen($msg) > 2000) $error = 'ข้อความยาวเกิน 2,000 ตัวอักษร';
    else {
        $imgs = upload_images('img', 3, 'defects');
        if ($imgs === null) $error = upload_error();
        elseif ($msg === '' && !$imgs) $error = 'กรุณาพิมพ์ข้อความหรือแนบรูปภาพ';
        else {
            $pdo->prepare("INSERT INTO defect_messages(report_id,sender,message,images) VALUES(?,?,?,?)")
                ->execute([$r['id'], $r['user_id'] == user()['id'] ? 'customer' : 'admin', $msg ?: null, $imgs ? implode(',', $imgs) : null]);
            $pdo->prepare("UPDATE defect_reports SET updated_at=NOW() WHERE id=?")->execute([$r['id']]);
            redirect('defect_view.php?id=' . $r['id']);
        }
    }
}
$st = $pdo->prepare("SELECT * FROM defect_messages WHERE report_id=? ORDER BY id"); $st->execute([$r['id']]); $msgs = $st->fetchAll();
$viewer = $r['user_id'] == user()['id'] ? 'customer' : 'admin';
$title = 'แจ้งสินค้าชำรุด #' . $r['id']; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="card-box">
  <div class="d-flex justify-content-between align-items-start mb-2">
    <div>
      <h5 class="price mb-0">แจ้งสินค้าชำรุด #<?= $r['id'] ?></h5>
      <small class="text-muted">คำสั่งซื้อ #<?= (int)$r['order_id'] ?> · <?= e($r['product_name']) ?> (ไซซ์ <?= e($r['size']) ?>) · <?= e(DEFECT_REASONS[$r['reason']] ?? '') ?></small>
    </div>
    <?= defect_badge($r['status']) ?>
  </div>
  <?php include 'includes/defect_thread.php'; ?>
  <?php if (!empty($error)): ?><div class="alert alert-danger mt-3 mb-0"><?= e($error) ?></div><?php endif; ?>
  <?php if ($closed): ?>
    <p class="text-center text-muted mt-3 mb-0">เรื่องนี้ปิดแล้ว (<?= e(DEFECT_STATUS[$r['status']]) ?>) หากมีปัญหาเพิ่มเติม <a href="defect.php">แจ้งเรื่องใหม่</a> หรือโทร <?= e(SHOP_PHONE) ?></p>
  <?php else: ?>
  <form method="post" enctype="multipart/form-data" class="mt-3">
    <textarea name="message" class="form-control mb-2" rows="3" maxlength="2000" placeholder="พิมพ์ข้อความถึงร้านค้า..."></textarea>
    <div class="row g-1 mb-2">
      <div class="col-4"><input type="file" name="img1" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm"></div>
      <div class="col-4"><input type="file" name="img2" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm"></div>
      <div class="col-4"><input type="file" name="img3" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm"></div>
    </div>
    <button class="btn btn-pink w-100"><i class="bi bi-send"></i> ส่งข้อความ</button>
  </form>
  <?php endif; ?>
  <a href="defect.php" class="d-block mt-3 small">&laquo; กลับไปรายการแจ้งชำรุด</a>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

$files['admin/defects.php'] = <<<'PINKY_EOF'
<?php require '../config.php'; require_admin();
$id = (int)($_GET['id'] ?? 0); $r = null;
if ($id) {
    $st = $pdo->prepare("SELECT d.*, u.name uname, u.phone uphone, u.email uemail FROM defect_reports d JOIN users u ON u.id=d.user_id WHERE d.id=?");
    $st->execute([$id]); $r = $st->fetch();
    if (!$r) { not_found(); }
}
if ($r && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = trim($_POST['message'] ?? ''); $new = $_POST['status'] ?? $r['status'];
    if (!isset(DEFECT_STATUS[$new])) $new = $r['status'];
    if (mb_strlen($msg) > 2000) { flash('ข้อความยาวเกิน 2,000 ตัวอักษร', 'danger'); redirect("defects.php?id=$id"); }
    $imgs = upload_images('img', 3, 'defects');
    if ($imgs === null) { flash(upload_error(), 'danger'); redirect("defects.php?id=$id"); }
    if ($msg !== '' || $imgs)
        $pdo->prepare("INSERT INTO defect_messages(report_id,sender,message,images) VALUES(?,?,?,?)")
            ->execute([$id, 'admin', $msg ?: null, $imgs ? implode(',', $imgs) : null]);
    $pdo->prepare("UPDATE defect_reports SET status=?, updated_at=NOW() WHERE id=?")->execute([$new, $id]);
    flash('บันทึกเรียบร้อย'); redirect("defects.php?id=$id");
}
$title = 'แจ้งสินค้าชำรุด'; include '../includes/header.php'; include '_nav.php';
if ($r):
    $st = $pdo->prepare("SELECT * FROM defect_messages WHERE report_id=? ORDER BY id"); $st->execute([$id]); $msgs = $st->fetchAll();
    $viewer = 'admin'; ?>
<div class="row g-4">
  <div class="col-lg-8"><div class="card-box">
    <div class="d-flex justify-content-between mb-2">
      <h5 class="price mb-0">แจ้งชำรุด #<?= $r['id'] ?> · <?= e($r['product_name']) ?> (ไซซ์ <?= e($r['size']) ?>)</h5><?= defect_badge($r['status']) ?>
    </div>
    <?php include '../includes/defect_thread.php'; ?>
    <form method="post" enctype="multipart/form-data" class="mt-3">
      <textarea name="message" class="form-control mb-2" rows="3" maxlength="2000" placeholder="ตอบกลับลูกค้า..."></textarea>
      <div class="row g-1 mb-2">
        <div class="col-4"><input type="file" name="img1" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm"></div>
        <div class="col-4"><input type="file" name="img2" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm"></div>
        <div class="col-4"><input type="file" name="img3" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm"></div>
      </div>
      <div class="input-group">
        <select name="status" class="form-select">
          <?php foreach (DEFECT_STATUS as $k => $v): ?><option value="<?= $k ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?>
        </select>
        <button class="btn btn-pink"><i class="bi bi-send"></i> ส่ง / บันทึกสถานะ</button>
      </div>
    </form>
  </div></div>
  <div class="col-lg-4"><div class="card-box small">
    <h6>ข้อมูลลูกค้า</h6>
    <p class="mb-2"><b><?= e($r['uname']) ?></b><br><?= e($r['uphone']) ?><br><?= e($r['uemail']) ?></p>
    <p class="mb-2">ลักษณะปัญหา: <b><?= e(DEFECT_REASONS[$r['reason']] ?? '') ?></b></p>
    <?php if ($r['order_id']): ?><a href="order.php?id=<?= (int)$r['order_id'] ?>" class="btn btn-outline-pink btn-sm">ดูคำสั่งซื้อ #<?= (int)$r['order_id'] ?></a><?php endif; ?>
    <a href="defects.php" class="btn btn-link btn-sm">&laquo; กลับ</a>
  </div></div>
</div>
<?php else:
    $list = $pdo->query("SELECT d.*, u.name uname, (SELECT COUNT(*) FROM defect_messages WHERE report_id=d.id) n
                         FROM defect_reports d JOIN users u ON u.id=d.user_id
                         ORDER BY FIELD(d.status,'open','reviewing','resolved','rejected'), d.updated_at DESC")->fetchAll(); ?>
<div class="card-box table-responsive">
  <table class="table align-middle">
    <tr><th>#</th><th>ลูกค้า</th><th>สินค้า</th><th>ปัญหา</th><th>ข้อความ</th><th>อัปเดตล่าสุด</th><th>สถานะ</th><th></th></tr>
    <?php foreach ($list as $d): ?>
    <tr><td><?= $d['id'] ?></td><td><?= e($d['uname']) ?></td><td><?= e($d['product_name']) ?> <small class="text-muted">(<?= e($d['size']) ?>)</small></td>
      <td><?= e(DEFECT_REASONS[$d['reason']] ?? '') ?></td><td><?= $d['n'] ?></td><td><small><?= e($d['updated_at']) ?></small></td>
      <td><?= defect_badge($d['status']) ?></td>
      <td><a href="?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-pink">เปิดแชท</a></td></tr>
    <?php endforeach; ?>
  </table>
  <?php if (!$list): ?><p class="text-muted text-center py-4 mb-0">ยังไม่มีรายการแจ้งสินค้าชำรุด</p><?php endif; ?>
</div>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>
PINKY_EOF;

$files['404.php'] = <<<'PINKY_EOF'
<?php require_once __DIR__ . '/config.php';
http_response_code(404);
$title = 'ไม่พบหน้าที่คุณต้องการ (Error 404)'; $desc = 'ไม่พบหน้าที่คุณต้องการ ลิงก์อาจผิดหรือสินค้าถูกนำออกแล้ว กลับไปเลือกซื้อรองเท้าที่ Sneaks Store หรือติดต่อเรา';
$top = $pdo->query("SELECT id,name,price,image FROM products ORDER BY sold DESC, id DESC LIMIT 4")->fetchAll();
include __DIR__ . '/includes/header.php'; ?>
<style>
.e404{font-size:7rem;line-height:1;font-weight:700;background:linear-gradient(135deg,#e84a7f,#ff8fb3);-webkit-background-clip:text;background-clip:text;color:transparent}
@keyframes wob404{0%,100%{transform:rotate(-10deg)}50%{transform:rotate(10deg)}}
.shoe404{display:inline-block;font-size:3.2rem;animation:wob404 2s ease-in-out infinite}
</style>
<div class="card-box text-center py-5 mb-4">
  <div class="shoe404">&#128095;</div>
  <div class="e404">404</div>
  <h4 class="mt-2">ขออภัย ไม่พบหน้าที่คุณต้องการ</h4>
  <p class="text-muted mb-1">ลิงก์อาจไม่ถูกต้อง หรือสินค้า/หน้านี้ถูกย้ายหรือลบไปแล้ว</p>
  <p class="small text-muted mb-4"><?= e(rawurldecode(strtok($_SERVER['REQUEST_URI'] ?? '', '?'))) ?></p>
  <form class="d-flex justify-content-center gap-2 mb-4" action="<?= BASE_URL ?>/index.php">
    <input name="q" class="form-control" style="max-width:340px" placeholder="ค้นหารองเท้าที่ต้องการ...">
    <button class="btn btn-pink"><i class="bi bi-search"></i> ค้นหา</button>
  </form>
  <a href="<?= BASE_URL ?>/" class="btn btn-pink btn-lg"><i class="bi bi-shop"></i> กลับหน้าแรก</a>
  <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-pink btn-lg"><i class="bi bi-telephone"></i> ติดต่อเรา</a>
</div>
<?php if ($top): ?>
<h6 class="text-center mb-3">สินค้าขายดีที่คุณอาจสนใจ</h6>
<div class="row g-2 justify-content-center">
<?php foreach ($top as $p): ?>
  <div class="col-6 col-md-3 col-lg-2">
    <a href="<?= BASE_URL ?>/product.php?id=<?= $p['id'] ?>" class="product-card">
      <img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?>" width="300" height="225" loading="lazy">
      <div class="p-2"><div class="name"><?= e($p['name']) ?></div><span class="price"><?= money($p['price']) ?></span></div>
    </a>
  </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
PINKY_EOF;

$files['500.php'] = <<<'PINKY_EOF'
<?php if (!headers_sent()) http_response_code(500); ?>
<!doctype html>
<html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ระบบขัดข้องชั่วคราว (Error 500) | Sneaks Store</title>
<meta name="description" content="ระบบขัดข้องชั่วคราว กรุณาลองใหม่อีกครั้งหรือติดต่อ Sneaks Store"><meta name="robots" content="noindex">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{font-family:'Prompt',sans-serif;background:linear-gradient(135deg,#ffd1dc,#fff5f8 60%);min-height:100vh}.pk{color:#e84a7f}.btn-pk{background:#e84a7f;color:#fff}.btn-pk:hover{background:#d13a6d;color:#fff}</style>
</head><body><div class="container text-center py-5">
  <div class="pk fw-bold" style="font-size:6rem;line-height:1">500</div>
  <h4 class="mt-3">ขออภัย ระบบขัดข้องชั่วคราว</h4>
  <p class="text-muted">เรากำลังแก้ไขอยู่ กรุณาลองใหม่อีกครั้งในอีกสักครู่<br>หากยังไม่หาย ติดต่อเราได้ที่ <?= defined('SHOP_PHONE') ? htmlspecialchars(SHOP_PHONE) : '' ?> <?= defined('SHOP_EMAIL') ? htmlspecialchars(SHOP_EMAIL) : '' ?></p>
  <a href="javascript:location.reload()" class="btn btn-pk btn-lg">ลองใหม่อีกครั้ง</a>
  <a href="./" class="btn btn-outline-secondary btn-lg">กลับหน้าแรก</a>
</div></body></html>
PINKY_EOF;

$files['sitemap.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach (['/', '/about.php', '/contact.php', '/privacy.php', '/index.php?promo=1'] as $u): ?>
  <url><loc><?= e(SITE_URL . $u) ?></loc></url>
<?php endforeach; ?>
<?php foreach ($pdo->query("SELECT id FROM categories") as $c): ?>
  <url><loc><?= e(SITE_URL . '/index.php?cat=' . $c['id']) ?></loc></url>
<?php endforeach; ?>
<?php foreach ($pdo->query("SELECT id, created_at FROM products ORDER BY id") as $p): ?>
  <url><loc><?= e(SITE_URL . '/product.php?id=' . $p['id']) ?></loc><lastmod><?= date('Y-m-d', strtotime($p['created_at'])) ?></lastmod></url>
<?php endforeach; ?>
</urlset>
PINKY_EOF;

$files['robots.txt'] = <<<'PINKY_EOF'
User-agent: *
Disallow: __BASE_URL__/admin/
Disallow: __BASE_URL__/rider/
Disallow: __BASE_URL__/cart.php
Disallow: __BASE_URL__/checkout.php
Disallow: __BASE_URL__/payment.php
Disallow: __BASE_URL__/orders.php
Disallow: __BASE_URL__/track.php
Sitemap: __SITE_URL__/sitemap.php
PINKY_EOF;

$files['.htaccess'] = <<<'PINKY_EOF'
Options -Indexes
ErrorDocument 404 __BASE_URL__/404.php
ErrorDocument 500 __BASE_URL__/500.php

# ซ่อนไฟล์ที่ไม่ควรเปิดสาธารณะ
<FilesMatch "\.(sql|md)$">
  Require all denied
</FilesMatch>

# บังคับ HTTPS (ยกเว้น localhost)
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteCond %{HTTP:X-Forwarded-Proto} !https
  RewriteCond %{HTTP_HOST} !^(localhost|127\.0\.0\.1) [NC]
  RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# เร่งความเร็ว: บีบอัด + แคช
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css text/plain application/javascript application/json application/xml image/svg+xml
</IfModule>
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/svg+xml "access plus 30 days"
  ExpiresByType image/jpeg "access plus 30 days"
  ExpiresByType image/png "access plus 30 days"
  ExpiresByType image/webp "access plus 30 days"
  ExpiresByType text/css "access plus 7 days"
</IfModule>
<IfModule mod_headers.c>
  Header always set X-Content-Type-Options "nosniff"
  Header always set X-Frame-Options "SAMEORIGIN"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
PINKY_EOF;

$files['about.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
$title = 'เกี่ยวกับเรา'; $desc = 'รู้จัก Sneaks Store ร้านรองเท้าออนไลน์ รองเท้าหลากหลายหมวด ชำระเงินสะดวก ติดตามพัสดุบนแผนที่ และดูแลหลังการขาย';
include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="card-box">
  <h4 class="price mb-3"><i class="bi bi-bag-heart-fill"></i> เกี่ยวกับ Sneaks Store</h4>
  <p>Sneaks Store คือร้านรองเท้าออนไลน์ที่รวมรองเท้าหลายแบบไว้ในที่เดียว ทั้งรองเท้าผ้าใบ รองเท้าวิ่ง ส้นสูง รองเท้าแตะ รองเท้าหนัง บูท รองเท้ากีฬา รวมถึงรองเท้าเด็กและรองเท้านักเรียน เลือกดูตามกลุ่มผู้ชาย ผู้หญิง เด็ก หรือโปรโมชั่นได้ง่าย</p>
  <div class="row g-3 my-2">
    <div class="col-sm-6"><i class="bi bi-credit-card price"></i> <b>ชำระเงินสะดวก</b><div class="small text-muted">พร้อมเพย์ บัตรเครดิต/เดบิต หรือเก็บเงินปลายทาง</div></div>
    <div class="col-sm-6"><i class="bi bi-geo-alt price"></i> <b>ติดตามพัสดุบนแผนที่</b><div class="small text-muted">ดูตำแหน่งการจัดส่งของคำสั่งซื้อได้</div></div>
    <div class="col-sm-6"><i class="bi bi-camera price"></i> <b>แจ้งสินค้าชำรุดได้</b><div class="small text-muted">แนบรูปและแชทกับร้านได้โดยตรง</div></div>
    <div class="col-sm-6"><i class="bi bi-shield-lock price"></i> <b>ดูแลข้อมูลส่วนบุคคล</b><div class="small text-muted">ปฏิบัติตาม PDPA อ่านรายละเอียดในนโยบายความเป็นส่วนตัว</div></div>
  </div>
  <a href="index.php?sort=latest" class="btn btn-pink"><i class="bi bi-bag-heart"></i> เลือกซื้อสินค้า</a>
  <a href="contact.php" class="btn btn-outline-pink"><i class="bi bi-telephone"></i> ติดต่อเรา</a>
</div></div></div>
<?php include 'includes/footer.php'; ?>
PINKY_EOF;

$files['newsletter.php'] = <<<'PINKY_EOF'
<?php require 'config.php';
$email = trim($_POST['email'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['website']) && filter_var($email, FILTER_VALIDATE_EMAIL) && mb_strlen($email) <= 190) {
    $pdo->prepare("INSERT IGNORE INTO newsletter_subscribers(email) VALUES(?)")->execute([$email]);
    pdpa_log($pdo, user()['id'] ?? null, 'newsletter');
    flash('สมัครรับข่าวสารเรียบร้อย ขอบคุณค่ะ');
} else {
    flash('กรุณากรอกอีเมลให้ถูกต้อง', 'danger');
}
$ref = $_SERVER['HTTP_REFERER'] ?? '';
redirect(strpos($ref, SITE_URL) === 0 ? $ref : BASE_URL . '/');
PINKY_EOF;

/* ==================== README.md ==================== */
$files['README.md'] = <<<'PINKY_EOF'
# Sneaks Store - ร้านรองเท้าออนไลน์

## ติดตั้งบนเครื่องอื่น / โฮสติ้ง
1. แตกไฟล์ zip ไว้ในโฟลเดอร์เว็บ (เช่น htdocs/shoe-shop)
2. สร้างฐานข้อมูลใหม่ (utf8mb4) แล้ว Import ไฟล์ database.sql
3. แก้ config.php: ค่า DB, BASE_URL, SITE_URL, PROMPTPAY_ID, พิกัดร้าน, คีย์ Omise
4. สมัครสมาชิก แล้วรัน SQL: UPDATE users SET role='admin' WHERE email='อีเมลของคุณ';

## บัญชีและสิทธิ์
- member: ซื้อสินค้า ติดตามพัสดุ รีวิว
- rider: เห็นงานจัดส่งที่ได้รับมอบหมาย ส่งพิกัด GPS สด (หน้า /rider/)
- admin: ระบบหลังบ้านทั้งหมด (หน้า /admin/)

## คูปองตัวอย่าง
- PINK10 ลด 10% เมื่อซื้อครบ 500 บาท
- WELCOME50 ลด 50 บาท

## PDPA และระบบตรวจรูปภาพ
- สมัครสมาชิก/สั่งซื้อ ต้องติ๊กยินยอม PDPA และบันทึกลงตาราง pdpa_consents (เวลา, เวอร์ชันนโยบาย, IP)
- แก้อีเมลติดต่อ PDPA ที่ PDPA_CONTACT ใน config.php และแก้ข้อความนโยบายที่ privacy.php
- อัปโหลดรูป (สินค้า/สลิป): ตรวจ MIME จากเนื้อไฟล์, ตรวจโครงสร้างภาพ, จำกัด 5 MB, วาดภาพใหม่ด้วย GD เพื่อลบ EXIF/GPS (ต้องเปิด extension gd และ fileinfo)
- หน้าชำระเงิน: พิมพ์ที่อยู่แล้วระบบปักหมุดบนแผนที่ให้อัตโนมัติ (OpenStreetMap Nominatim) ลากหมุดปรับเองได้

## ติดต่อเรา / แจ้งสินค้าชำรุด
- หน้า /contact.php แสดงเบอร์โทร อีเมล รหัสไปรษณีย์ (แก้ที่ SHOP_PHONE, SHOP_EMAIL, SHOP_POSTCODE ใน config.php)
- ลูกค้าแจ้งสินค้าชำรุดได้ที่ /defect.php (เลือกสินค้าจากออเดอร์ที่กำลังส่ง/ส่งแล้ว, พิมพ์ปัญหา, แนบรูปได้ 3 รูป) แล้วคุยต่อในแชท
- แอดมินตอบแชทและเปลี่ยนสถานะที่ /admin/defects.php

## เช็กลิสต์มาตรฐานเว็บไซต์ (Scorecard)
1. HTTPS: บังคับ https อัตโนมัติ (ยกเว้น localhost) แต่ต้องติดตั้งใบรับรอง SSL ที่โฮสติ้งก่อน (เช่น Let's Encrypt) กุญแจเขียวจึงจะขึ้น
2. ความเร็ว: โหลดแผนที่เฉพาะหน้าที่ใช้, รูป lazy-load, บีบอัด + แคชผ่าน .htaccess
3. มือถือ: Bootstrap responsive + viewport
4. รูปภาพ: ทุกรูปมี alt และไฟล์อัปโหลดตั้งชื่อสื่อความหมาย (product-..., payment-slip-..., defect-photo-...)
5. SEO: title + meta description ไม่ซ้ำกันทุกหน้า, canonical, Open Graph, robots.txt, sitemap.php
6. ติดต่อเรา: /contact.php  7. Privacy Policy: /privacy.php
8. Analytics: ใส่ GA_ID / FB_PIXEL_ID ใน config.php (โหลดหลังผู้ใช้กดยอมรับคุกกี้)
9. Error: หน้า 404 และ 500 สวยงาม  10. ปุ่ม CTA: สั่งซื้อสินค้า / สมัครสมาชิก / ติดต่อเรา
11. สินค้าตัวอย่าง 40 ตัว  12. หมวดหมู่ 10 หมวด

## หมวดผู้ชาย / ผู้หญิง / เด็ก / โปรโมชั่น
- หมวดที่ชื่อมีคำว่า "ผู้ชาย", "ผู้หญิง", "เด็ก" จะแสดงสินค้าตามช่อง "แสดงในหมวด" ที่ติ๊กไว้ในหน้า admin/products.php
- หมวดที่ชื่อมีคำว่า "โปรโมชั่น" จะแสดงสินค้าทุกตัวที่ตั้ง "ราคาโปรโมชั่น" (แสดงราคาเดิมขีดฆ่า + % ที่ลด และคิดเงินตามราคาโปรโมชั่น)

## ภาษีมูลค่าเพิ่ม (VAT 7%)
- ราคาสินค้าที่แสดงไม่รวม VAT ระบบคิด VAT 7% จากยอดสินค้าหลังหักส่วนลด แล้วบวกค่าจัดส่ง (ค่าจัดส่งไม่คิด VAT) ปรับอัตราที่ VAT_RATE ใน config.php
- แสดงบรรทัด VAT ในตะกร้า หน้าสั่งซื้อ หน้าชำระเงิน ติดตามพัสดุ และหลังบ้าน ยอดที่ส่งไป Omise/QR คือยอดรวม VAT แล้ว

## หน้าตาเว็บไซต์ (เลย์เอาต์ใหม่)
- แถบข้อมูลด้านบน + เมนู หน้าหลัก / สินค้า (เมนูย่อยหมวดหมู่) / โปรโมชั่น / เกี่ยวกับเรา / ติดต่อเรา + ช่องค้นหา + ตะกร้า
- หน้าแรก: สไลด์แบนเนอร์ (ใช้รูปสินค้าในร้าน) · แถบจุดเด่น · สินค้าขายดี · แบนเนอร์ Flash Sale / New Arrival · สินค้าทั้งหมด
- Footer: เมนู ช่องทางติดต่อ สมัครรับข่าวสาร (เก็บอีเมลในตาราง newsletter_subscribers พร้อมบันทึกความยินยอม PDPA)
- ใส่ลิงก์โซเชียลที่ SOCIAL_FACEBOOK / SOCIAL_INSTAGRAM / SOCIAL_TIKTOK / SOCIAL_YOUTUBE / SOCIAL_LINE ใน config.php

## Omise
ใส่ OMISE_PUBLIC_KEY / OMISE_SECRET_KEY ใน config.php และตั้ง Webhook ไปที่ SITE_URL/webhook.php
ถ้ายังไม่ใส่คีย์ ระบบจะใช้ QR พร้อมเพย์ + แนบสลิปแทน
บัตรทดสอบ: 4242 4242 4242 4242
PINKY_EOF;


/* ============================================================
   ส่วนติดตั้ง
   ============================================================ */
function make_zip($src, $zipFile) {
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) return false;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
                                        RecursiveIteratorIterator::SELF_FIRST);
    foreach ($it as $f) {
        $rel = 'shoe-shop/' . str_replace('\\', '/', substr($f->getPathname(), strlen($src) + 1));
        $f->isDir() ? $zip->addEmptyDir($rel) : $zip->addFile($f->getPathname(), $rel);
    }
    return $zip->close();
}

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$exists = is_file($target . '/config.php');
$log = []; $errors = []; $done = false; $zipOk = false;
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/shoe-shop';
$site = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $base;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = fn($k) => trim($_POST[$k] ?? '');
    if ($exists && empty($_POST['overwrite'])) $errors[] = 'พบโฟลเดอร์ shoe-shop อยู่แล้ว ติ๊ก "เขียนทับไฟล์เดิม" หากต้องการติดตั้งใหม่';
    elseif (!preg_match('/^\w+$/', $in('db_name'))) $errors[] = 'ชื่อฐานข้อมูลใช้ได้เฉพาะ a-z, 0-9 และ _';
    elseif (!filter_var($in('admin_email'), FILTER_VALIDATE_EMAIL)) $errors[] = 'อีเมลแอดมินไม่ถูกต้อง';
    elseif (strlen($_POST['admin_pass'] ?? '') < 6) $errors[] = 'รหัสผ่านแอดมินต้องมีอย่างน้อย 6 ตัวอักษร';
    else {
        // 1) เขียนไฟล์ทั้งหมด
        $rep = [
            "'__DB_HOST__'"  => var_export($in('db_host'), true),
            "'__DB_NAME__'"  => var_export($in('db_name'), true),
            "'__DB_USER__'"  => var_export($in('db_user'), true),
            "'__DB_PASS__'"  => var_export($_POST['db_pass'] ?? '', true),
            "'__BASE_URL__'" => var_export($base, true),
            "'__SITE_URL__'" => var_export($site, true),
        ];
        foreach ($files as $path => $code) {
            $full = $target . '/' . $path;
            if (!is_dir(dirname($full))) mkdir(dirname($full), 0775, true);
            if ($path === 'config.php') $code = strtr($code, $rep);
            if (in_array($path, ['.htaccess', 'robots.txt'])) $code = str_replace(['__BASE_URL__', '__SITE_URL__'], [str_replace(' ', '%20', $base), str_replace(' ', '%20', $site)], $code);   // ช่องว่างในชื่อโฟลเดอร์ต้องเข้ารหัสเป็น %20 ไม่งั้น Apache ขึ้น 500
            file_put_contents($full, $code);
        }
        if (!is_file(__DIR__ . '/.htaccess'))     // หน้า 404 สวยงามสำหรับลิงก์ผิดนอกโฟลเดอร์ร้านด้วย (ไม่ทับไฟล์เดิมของคุณ)
            file_put_contents(__DIR__ . '/.htaccess', "ErrorDocument 404 " . str_replace(' ', '%20', $base) . "/404.php\n");
        foreach (['uploads/products', 'uploads/slips', 'uploads/defects'] as $d) if (!is_dir("$target/$d")) mkdir("$target/$d", 0775, true);
        $log[] = 'สร้างไฟล์ ' . count($files) . ' ไฟล์ในโฟลเดอร์ shoe-shop';

        // 2) ฐานข้อมูล + บัญชีแอดมิน
        try {
            $db = new PDO('mysql:host=' . $in('db_host') . ';charset=utf8mb4', $in('db_user'), $_POST['db_pass'] ?? '',
                          [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $name = $in('db_name');
            $db->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $db->exec("USE `$name`");
            $fresh = !$db->query("SHOW TABLES LIKE 'users'")->fetch();
            if (!$fresh) {
                $log[] = 'พบตารางเดิมในฐานข้อมูล ' . $name . ' แล้ว จึงข้ามการสร้างตาราง';
            } else {
                foreach (array_filter(array_map('trim', preg_split('/;\s*\n/', $files['database.sql']))) as $sql) $db->exec($sql);
                $log[] = 'สร้างฐานข้อมูล ' . $name . ' พร้อมตาราง สินค้าตัวอย่าง และคูปองตัวอย่าง';
            }
            if (preg_match_all('/CREATE TABLE IF NOT EXISTS .*?;/s', $files['database.sql'], $m)) foreach ($m[0] as $q) $db->exec($q);
            $log[] = 'ตรวจ/สร้างตาราง PDPA และแจ้งสินค้าชำรุด (pdpa_consents, defect_reports, defect_messages) เรียบร้อย';
            foreach (['gd' => 'ลบข้อมูล EXIF/GPS และตรวจรูปจริง', 'fileinfo' => 'ตรวจชนิดไฟล์จากเนื้อไฟล์'] as $ext => $why)
                if (!extension_loaded($ext)) $errors[] = "PHP ยังไม่เปิด extension $ext ($why) เปิดใน php.ini แล้วรีสตาร์ต Apache เพื่อให้ระบบตรวจรูปทำงานเต็มประสิทธิภาพ";
            foreach ($RENAME_IMAGES as $o => $nw)     // อัปเดตชื่อรูปสินค้าตัวอย่างเดิมในฐานข้อมูลให้เป็นชื่อสื่อความหมาย
                $db->prepare("UPDATE products SET image=? WHERE image=?")->execute([$nw, $o]);
            if (!$db->query("SHOW COLUMNS FROM orders LIKE 'vat'")->fetch())        // ฐานข้อมูลเดิม: เพิ่มคอลัมน์ VAT
                $db->exec("ALTER TABLE orders ADD vat DECIMAL(10,2) DEFAULT 0 AFTER discount");
            $colAdded = false;                          // ฐานข้อมูลเดิม: เพิ่มคอลัมน์กลุ่มลูกค้า + ราคาโปรโมชั่น
            if (!$db->query("SHOW COLUMNS FROM products LIKE 'audience'")->fetch()) {
                $db->exec("ALTER TABLE products ADD audience VARCHAR(30) NULL, ADD sale_price DECIMAL(10,2) NULL");
                $colAdded = true;
            }
            $n = seed_catalog($db, $SEED);
            $log[] = "เพิ่มหมวดหมู่และสินค้าตัวอย่างพร้อมรูป $n รายการ";
            // จัดสินค้าที่ยังไม่ระบุกลุ่มลูกค้า เข้าหมวด ผู้ชาย / ผู้หญิง / เด็ก จากชื่อสินค้า (แก้ไขได้ในหน้าแอดมิน)
            $kid = "(name LIKE '%นักเรียน%' OR name LIKE '%เด็ก%' OR name LIKE '%Kids%')";
            $db->exec("UPDATE products SET audience = CASE
                WHEN $kid AND name LIKE '%หญิง%' THEN 'kids,women'
                WHEN $kid AND name LIKE '%ชาย%' THEN 'kids,men'
                WHEN $kid THEN 'kids'
                WHEN name LIKE '%หญิง%' OR name LIKE '%ส้นสูง%' OR name LIKE '%เลดี้%' OR name LIKE '%คัทชู%' OR name LIKE '%Lady%' THEN 'women'
                ELSE 'men,women' END WHERE audience IS NULL OR audience=''");
            // สินค้าโปรโมชั่นตัวอย่าง (ลด 15%) ตอนติดตั้งครั้งแรก / อัปเกรดครั้งแรกเท่านั้น
            if (($fresh || $colAdded) && !$db->query("SELECT COUNT(*) FROM products WHERE sale_price IS NOT NULL")->fetchColumn())
                $db->exec("UPDATE products SET sale_price=ROUND(price*0.85) WHERE id % 4 = 0 AND price >= 500");
            $log[] = 'จัดกลุ่มสินค้าเข้าหมวดผู้ชาย/ผู้หญิง/เด็ก และตั้งสินค้าโปรโมชั่นตัวอย่างเรียบร้อย';
            $hash = password_hash($_POST['admin_pass'], PASSWORD_DEFAULT);
            $st = $db->prepare("SELECT id FROM users WHERE email=?"); $st->execute([$in('admin_email')]);
            if ($st->fetchColumn()) {
                $db->prepare("UPDATE users SET role='admin', password=? WHERE email=?")->execute([$hash, $in('admin_email')]);
            } else {
                $db->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'admin')")
                   ->execute([$in('admin_name') ?: 'Admin', $in('admin_email'), $hash]);
            }
            $log[] = 'ตั้งค่าบัญชีแอดมิน ' . $in('admin_email') . ' เรียบร้อย';
        } catch (PDOException $e) {
            $errors[] = 'ติดตั้งฐานข้อมูลไม่สำเร็จ: ' . $e->getMessage() . ' (ไฟล์ถูกสร้างแล้ว แก้ค่าแล้วกดติดตั้งซ้ำได้)';
        }

        // 3) บีบเป็น ZIP
        if (class_exists('ZipArchive')) {
            $zipOk = make_zip($target, __DIR__ . '/shoe-shop.zip');
            $zipOk ? $log[] = 'สร้างไฟล์ shoe-shop.zip เรียบร้อย' : $errors[] = 'สร้าง ZIP ไม่สำเร็จ (ตรวจสอบสิทธิ์การเขียนไฟล์)';
        } else {
            $errors[] = 'PHP ไม่มี ZipArchive: เปิด php.ini แล้วลบ ; หน้าบรรทัด extension=zip จากนั้นรีสตาร์ต Apache และกดติดตั้งอีกครั้ง';
        }
        $done = true;
        $exists = true;
    }
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ติดตั้ง Sneaks Store</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
body{font-family:'Prompt',sans-serif;background:linear-gradient(135deg,#ffd1dc,#fff5f8 60%);min-height:100vh}
.box{background:#fff;border-radius:16px;box-shadow:0 6px 24px rgba(232,74,127,.15);padding:2rem}
.btn-pink{background:#e84a7f;color:#fff;border:none}.btn-pink:hover{background:#d13a6d;color:#fff}
.btn-outline-pink{border:1px solid #e84a7f;color:#e84a7f}.btn-outline-pink:hover{background:#ffd1dc;color:#e84a7f}
h2,.pink{color:#e84a7f}
</style>
</head>
<body>
<div class="container py-5" style="max-width:640px">
  <div class="box">
    <h2 class="text-center mb-1"><i class="bi bi-bag-heart-fill"></i> ติดตั้ง Sneaks Store</h2>
    <p class="text-center text-muted mb-4">สร้างไฟล์ทั้งหมด + ฐานข้อมูล + ไฟล์ ZIP ในคลิกเดียว</p>

    <?php foreach ($errors as $er): ?><div class="alert alert-danger"><?= $h($er) ?></div><?php endforeach; ?>

    <?php if ($done): ?>
      <ul class="list-group mb-4">
        <?php foreach ($log as $l): ?><li class="list-group-item"><i class="bi bi-check-circle-fill text-success"></i> <?= $h($l) ?></li><?php endforeach; ?>
      </ul>
      <div class="d-grid gap-2">
        <?php if ($zipOk): ?><a href="shoe-shop.zip" class="btn btn-pink btn-lg"><i class="bi bi-file-earmark-zip"></i> ดาวน์โหลด shoe-shop.zip</a><?php endif; ?>
        <a href="<?= $h($base) ?>/" class="btn btn-outline-pink btn-lg" target="_blank"><i class="bi bi-shop"></i> เปิดร้านค้า</a>
        <a href="<?= $h($base) ?>/login.php" class="btn btn-outline-pink" target="_blank"><i class="bi bi-speedometer2"></i> เข้าสู่ระบบหลังบ้าน</a>
      </div>
      <div class="alert alert-warning mt-4 small mb-0">
        <b>สำคัญ:</b> ลบไฟล์ <code>install.php</code> และ <code>shoe-shop.zip</code> ออกจากโฟลเดอร์เว็บหลังติดตั้งเสร็จ
        และแก้ค่าพร้อมเพย์/พิกัดร้าน/คีย์ Omise ใน <code>shoe-shop/config.php</code>
      </div>
    <?php else: ?>
      <form method="post">
        <h6 class="pink">ฐานข้อมูล MySQL</h6>
        <div class="row g-2 mb-3">
          <div class="col-6"><input name="db_host" class="form-control" value="<?= $h($_POST['db_host'] ?? 'localhost') ?>" placeholder="Host"></div>
          <div class="col-6"><input name="db_name" class="form-control" value="<?= $h($_POST['db_name'] ?? 'shoe_shop') ?>" placeholder="ชื่อฐานข้อมูล"></div>
          <div class="col-6"><input name="db_user" class="form-control" value="<?= $h($_POST['db_user'] ?? 'root') ?>" placeholder="User"></div>
          <div class="col-6"><input name="db_pass" type="password" class="form-control" placeholder="Password (XAMPP เว้นว่าง)"></div>
        </div>
        <h6 class="pink">บัญชีแอดมิน</h6>
        <input name="admin_name" class="form-control mb-2" value="<?= $h($_POST['admin_name'] ?? 'Admin') ?>" placeholder="ชื่อแอดมิน">
        <input name="admin_email" type="email" class="form-control mb-2" value="<?= $h($_POST['admin_email'] ?? '') ?>" placeholder="อีเมลแอดมิน" required>
        <input name="admin_pass" type="password" class="form-control mb-3" placeholder="รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)" required>
        <?php if ($exists): ?>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="overwrite" value="1" id="ow">
            <label class="form-check-label text-danger" for="ow">พบโฟลเดอร์ shoe-shop อยู่แล้ว: เขียนทับไฟล์เดิม</label>
          </div>
        <?php endif; ?>
        <p class="small text-muted">ติดตั้งไปที่: <code><?= $h($target) ?></code><br>URL ร้าน: <code><?= $h($site) ?>/</code></p>
        <button class="btn btn-pink btn-lg w-100"><i class="bi bi-magic"></i> ติดตั้ง</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
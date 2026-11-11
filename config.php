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
define('DB_HOST', 'localhost');
define('DB_NAME', 'shoe_shop');
define('DB_USER', 'root');
define('DB_PASS', '');

// ===== ร้านค้า =====
define('BASE_URL', '/Sneaks Store/shoe-shop');      // โฟลเดอร์โปรเจกต์ เช่น /shoe-shop
define('SITE_URL', 'http://localhost/Sneaks Store/shoe-shop');      // URL เต็ม (ใช้ตอนกลับจากหน้า 3-D Secure)
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
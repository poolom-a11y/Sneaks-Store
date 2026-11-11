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
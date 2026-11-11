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
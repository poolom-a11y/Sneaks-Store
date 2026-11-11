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
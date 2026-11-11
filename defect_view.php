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
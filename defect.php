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
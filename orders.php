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
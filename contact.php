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
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
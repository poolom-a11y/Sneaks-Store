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
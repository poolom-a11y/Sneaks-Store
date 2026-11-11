<?php require_once __DIR__ . '/config.php';
http_response_code(404);
$title = 'ไม่พบหน้าที่คุณต้องการ (Error 404)'; $desc = 'ไม่พบหน้าที่คุณต้องการ ลิงก์อาจผิดหรือสินค้าถูกนำออกแล้ว กลับไปเลือกซื้อรองเท้าที่ Sneaks Store หรือติดต่อเรา';
$top = $pdo->query("SELECT id,name,price,image FROM products ORDER BY sold DESC, id DESC LIMIT 4")->fetchAll();
include __DIR__ . '/includes/header.php'; ?>
<style>
.e404{font-size:7rem;line-height:1;font-weight:700;background:linear-gradient(135deg,#e84a7f,#ff8fb3);-webkit-background-clip:text;background-clip:text;color:transparent}
@keyframes wob404{0%,100%{transform:rotate(-10deg)}50%{transform:rotate(10deg)}}
.shoe404{display:inline-block;font-size:3.2rem;animation:wob404 2s ease-in-out infinite}
</style>
<div class="card-box text-center py-5 mb-4">
  <div class="shoe404">&#128095;</div>
  <div class="e404">404</div>
  <h4 class="mt-2">ขออภัย ไม่พบหน้าที่คุณต้องการ</h4>
  <p class="text-muted mb-1">ลิงก์อาจไม่ถูกต้อง หรือสินค้า/หน้านี้ถูกย้ายหรือลบไปแล้ว</p>
  <p class="small text-muted mb-4"><?= e(rawurldecode(strtok($_SERVER['REQUEST_URI'] ?? '', '?'))) ?></p>
  <form class="d-flex justify-content-center gap-2 mb-4" action="<?= BASE_URL ?>/index.php">
    <input name="q" class="form-control" style="max-width:340px" placeholder="ค้นหารองเท้าที่ต้องการ...">
    <button class="btn btn-pink"><i class="bi bi-search"></i> ค้นหา</button>
  </form>
  <a href="<?= BASE_URL ?>/" class="btn btn-pink btn-lg"><i class="bi bi-shop"></i> กลับหน้าแรก</a>
  <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-pink btn-lg"><i class="bi bi-telephone"></i> ติดต่อเรา</a>
</div>
<?php if ($top): ?>
<h6 class="text-center mb-3">สินค้าขายดีที่คุณอาจสนใจ</h6>
<div class="row g-2 justify-content-center">
<?php foreach ($top as $p): ?>
  <div class="col-6 col-md-3 col-lg-2">
    <a href="<?= BASE_URL ?>/product.php?id=<?= $p['id'] ?>" class="product-card">
      <img src="<?= img($p['image']) ?>" alt="<?= e($p['name']) ?>" width="300" height="225" loading="lazy">
      <div class="p-2"><div class="name"><?= e($p['name']) ?></div><span class="price"><?= money($p['price']) ?></span></div>
    </a>
  </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
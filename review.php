<?php require 'config.php'; require_login();
$o = get_order($pdo, $_GET['id'] ?? 0);
if ($o['status'] !== 'delivered' || $o['user_id'] != user()['id']) exit('รีวิวได้เมื่อได้รับสินค้าแล้วเท่านั้น');

$st = $pdo->prepare("SELECT DISTINCT oi.product_id, oi.name, p.image, r.rating, r.comment
    FROM order_items oi JOIN products p ON p.id=oi.product_id
    LEFT JOIN reviews r ON r.order_id=oi.order_id AND r.product_id=oi.product_id
    WHERE oi.order_id=?");
$st->execute([$o['id']]); $items = $st->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed = array_column($items, 'product_id');
    $ins = $pdo->prepare("INSERT IGNORE INTO reviews(product_id,user_id,order_id,rating,comment) VALUES(?,?,?,?,?)");
    $upd = $pdo->prepare("UPDATE products SET
        rating_count=(SELECT COUNT(*) FROM reviews WHERE product_id=?),
        rating_avg=(SELECT COALESCE(AVG(rating),0) FROM reviews WHERE product_id=?) WHERE id=?");
    foreach ($_POST['rating'] ?? [] as $pid => $r) {
        if (!in_array($pid, $allowed)) continue;
        $ins->execute([$pid, user()['id'], $o['id'], max(1, min(5, (int)$r)), trim($_POST['comment'][$pid] ?? '')]);
        $upd->execute([$pid, $pid, $pid]);
    }
    flash('ขอบคุณสำหรับรีวิวค่ะ'); redirect('orders.php');
}
$title = 'รีวิวสินค้า'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-7"><div class="card-box">
  <h4 class="price mb-3"><i class="bi bi-star"></i> รีวิวสินค้า คำสั่งซื้อ #<?= $o['id'] ?></h4>
  <form method="post">
  <?php foreach ($items as $it): $pid = $it['product_id']; ?>
    <div class="d-flex gap-3 border-bottom py-3">
      <img src="<?= img($it['image']) ?>" alt="<?= e($it['name']) ?>" width="80" height="80" class="rounded" style="object-fit:cover">
      <div class="flex-grow-1">
        <div class="fw-semibold"><?= e($it['name']) ?></div>
        <?php if ($it['rating']): ?>
          <div><?= stars($it['rating']) ?></div><small class="text-muted"><?= e($it['comment']) ?> (รีวิวแล้ว)</small>
        <?php else: ?>
          <div class="stars">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" name="rating[<?= $pid ?>]" value="<?= $i ?>" id="r<?= $pid ?>_<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
              <label for="r<?= $pid ?>_<?= $i ?>"><i class="bi bi-star-fill"></i></label>
            <?php endfor; ?>
          </div>
          <textarea name="comment[<?= $pid ?>]" class="form-control mt-1" rows="2" placeholder="เล่าความประทับใจ เช่น ใส่สบาย ไซซ์ตรง"></textarea>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <button class="btn btn-pink w-100 mt-3">ส่งรีวิว</button>
  </form>
</div></div></div>
<?php include 'includes/footer.php'; ?>
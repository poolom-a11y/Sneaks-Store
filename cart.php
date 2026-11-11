<?php require 'config.php';
$_SESSION['cart'] ??= [];
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $id = (int)$_POST['id'];
    $key = $id . '|' . ($_POST['size'] ?? '-');
    $_SESSION['cart'][$key] = ($_SESSION['cart'][$key] ?? 0) + max(1, (int)$_POST['qty']);
    if (!empty($_POST['buy'])) redirect('checkout.php');
    flash('เพิ่มสินค้าลงรถเข็นแล้ว'); redirect("product.php?id=$id");
}
if ($action === 'update') {
    if (isset($_POST['remove'])) unset($_SESSION['cart'][$_POST['remove']]);
    else foreach ($_POST['qty'] ?? [] as $k => $q) {
        if ((int)$q <= 0) unset($_SESSION['cart'][$k]); else $_SESSION['cart'][$k] = (int)$q;
    }
    redirect('cart.php');
}
if ($action === 'coupon') {
    if (isset($_POST['remove_coupon'])) { unset($_SESSION['coupon']); redirect('cart.php'); }
    [, $sub] = cart_items($pdo);
    $c = check_coupon($pdo, $_POST['code'] ?? '', $sub);
    if (is_array($c)) { $_SESSION['coupon'] = $c['code']; flash('ใช้คูปอง ' . $c['code'] . ' ลด ' . money($c['discount'])); }
    else flash($c, 'danger');
    redirect('cart.php');
}

[$items, $total] = cart_items($pdo);
$coupon = !empty($_SESSION['coupon']) ? check_coupon($pdo, $_SESSION['coupon'], $total) : null;
$discount = is_array($coupon) ? $coupon['discount'] : 0;
$vat = calc_vat($total - $discount);
$title = 'รถเข็นของฉัน'; include 'includes/header.php'; ?>
<div class="card-box">
  <h4 class="mb-3"><i class="bi bi-cart3"></i> รถเข็นของฉัน</h4>
  <?php if (!$items): ?>
    <p class="text-center text-muted py-5">ยังไม่มีสินค้าในรถเข็น<br><a href="index.php" class="btn btn-pink mt-3">ไปช้อปเลย</a></p>
  <?php else: ?>
  <form method="post"><input type="hidden" name="action" value="update">
  <div class="table-responsive"><table class="table align-middle">
    <thead><tr><th>สินค้า</th><th>ไซซ์</th><th>ราคา</th><th width="110">จำนวน</th><th>รวม</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><img src="<?= img($it['image']) ?>" alt="<?= e($it['name']) ?>" width="60" height="60" loading="lazy" class="rounded me-2"><?= e($it['name']) ?></td>
        <td><?= e($it['size']) ?></td>
        <td><?= money($it['price']) ?></td>
        <td><input type="number" name="qty[<?= e($it['key']) ?>]" value="<?= $it['qty'] ?>" min="0" max="<?= $it['stock'] ?>" class="form-control form-control-sm"></td>
        <td class="price"><?= money($it['subtotal']) ?></td>
        <td><button name="remove" value="<?= e($it['key']) ?>" class="btn btn-sm btn-link text-danger"><i class="bi bi-trash"></i></button></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <button class="btn btn-outline-pink btn-sm">อัปเดตรถเข็น</button>
  </form>

  <hr>
  <div class="row g-3 align-items-center">
    <div class="col-md-6">
      <form method="post" class="d-flex gap-2 align-items-center">
        <input type="hidden" name="action" value="coupon">
        <i class="bi bi-ticket-perforated fs-4 price"></i>
        <?php if (is_array($coupon)): ?>
          <span class="coupon-tag"><?= e($coupon['code']) ?> (-<?= money($discount) ?>)</span>
          <button name="remove_coupon" class="btn btn-sm btn-link text-danger">ยกเลิกคูปอง</button>
        <?php else: ?>
          <input name="code" class="form-control text-uppercase" placeholder="ใส่โค้ดส่วนลด">
          <button class="btn btn-outline-pink text-nowrap">ใช้โค้ด</button>
        <?php endif; ?>
      </form>
      <?php if (is_string($coupon)): ?><small class="text-danger">คูปอง <?= e($_SESSION['coupon']) ?>: <?= e($coupon) ?></small><?php endif; ?>
    </div>
    <div class="col-md-6 text-md-end">
      <?php if ($discount): ?><div class="small text-muted">ส่วนลด -<?= money($discount) ?></div><?php endif; ?>
      <div class="small text-muted">ภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>% +<?= money($vat) ?></div>
      รวมทั้งหมด <span class="price fs-4 me-3"><?= money($total - $discount + $vat) ?></span>
      <div class="small text-muted mb-2">ยังไม่รวมค่าจัดส่ง</div>
      <a href="checkout.php" class="btn btn-pink btn-lg">สั่งซื้อสินค้า</a>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
<?php $needMap = true; require 'config.php'; require_login();
[$items, $total] = cart_items($pdo);
if (!$items) redirect('cart.php');

$coupon = !empty($_SESSION['coupon']) ? check_coupon($pdo, $_SESSION['coupon'], $total) : null;
if (!is_array($coupon)) { unset($_SESSION['coupon']); $coupon = null; }
$discount = $coupon['discount'] ?? 0;
$vat = calc_vat($total - $discount);
$grand = $total - $discount + $vat + SHIPPING_FEE;

$methods = ['promptpay' => ['qr-code', 'QR พร้อมเพย์' . (omise_on() ? ' (ยืนยันยอดอัตโนมัติ)' : ' (แนบสลิป)')]];
if (omise_on()) $methods['card'] = ['credit-card', 'บัตรเครดิต / เดบิต'];
$methods['cod'] = ['cash-coin', 'เก็บเงินปลายทาง'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        $upd = $pdo->prepare("UPDATE products SET stock=stock-?, sold=sold+? WHERE id=? AND stock>=?");
        foreach ($items as $it) {
            $upd->execute([$it['qty'], $it['qty'], $it['id'], $it['qty']]);
            if (!$upd->rowCount()) throw new Exception('สินค้า "' . $it['name'] . '" มีไม่พอในสต็อก');
        }
        if ($coupon) {
            $u = $pdo->prepare("UPDATE coupons SET used=used+1 WHERE id=? AND (max_uses IS NULL OR used<max_uses)");
            $u->execute([$coupon['id']]);
            if (!$u->rowCount()) throw new Exception('คูปองถูกใช้ครบจำนวนแล้ว');
        }
        $pm = isset($methods[$_POST['payment'] ?? '']) ? $_POST['payment'] : 'promptpay';
        if (empty($_POST['pdpa'])) throw new Exception('กรุณายอมรับการเก็บและใช้ข้อมูลที่อยู่/ตำแหน่งจัดส่ง (PDPA)');
        $la = is_numeric($_POST['lat'] ?? '') ? (float)$_POST['lat'] : null;
        $ln = is_numeric($_POST['lng'] ?? '') ? (float)$_POST['lng'] : null;
        if ($la === null || $ln === null || abs($la) > 90 || abs($ln) > 180) throw new Exception('พิกัดจัดส่งไม่ถูกต้อง กรุณาปักหมุดบนแผนที่');
        $status = $pm === 'cod' ? 'verifying' : 'pending';
        $pdo->prepare("INSERT INTO orders(user_id,receiver,phone,address,lat,lng,total,discount,vat,coupon_code,payment_method,status)
                       VALUES(?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([user()['id'], $_POST['receiver'], $_POST['phone'], $_POST['address'],
                       $la, $ln, $grand, $discount, $vat, $coupon['code'] ?? null, $pm, $status]);
        $oid = $pdo->lastInsertId();
        $ins = $pdo->prepare("INSERT INTO order_items(order_id,product_id,name,size,qty,price) VALUES(?,?,?,?,?,?)");
        foreach ($items as $it) $ins->execute([$oid, $it['id'], $it['name'], $it['size'], $it['qty'], $it['price']]);
        $pdo->prepare("INSERT INTO order_tracking(order_id,status,note) VALUES(?,?,?)")
            ->execute([$oid, $status, 'สร้างคำสั่งซื้อ (' . PAY[$pm] . ')']);
        pdpa_log($pdo, user()['id'], 'checkout_address_location');
        $slipOk = false;
        if ($pm === 'promptpay' && !omise_on() && !empty($_FILES['slip']['name'])) {    // แนบสลิปมาพร้อมการสั่งซื้อ
            $f = upload_image('slip', 'slips');
            if (!$f) throw new Exception(upload_error() ?: 'แนบสลิปไม่สำเร็จ');
            $pdo->prepare("UPDATE orders SET slip=?, status='verifying' WHERE id=?")->execute([$f, $oid]);
            $pdo->prepare("INSERT INTO order_tracking(order_id,status,note) VALUES(?,?,?)")->execute([$oid, 'verifying', 'ลูกค้าแนบสลิปการโอนเงินแล้ว']);
            $slipOk = true;
        }
        $pdo->commit();
        unset($_SESSION['cart'], $_SESSION['coupon']);
        if ($slipOk) flash('ส่งหลักฐานการชำระเงินเรียบร้อย รอร้านค้าตรวจสอบ');
        redirect($pm === 'cod' || $slipOk ? "track.php?id=$oid" : "payment.php?id=$oid");
    } catch (Exception $ex) { $pdo->rollBack(); $error = $ex->getMessage(); }
}
$title = 'ทำการสั่งซื้อ'; include 'includes/header.php'; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" onsubmit="if(!lat.value){alert('กรุณาปักหมุดที่อยู่จัดส่งบนแผนที่');return false}">
<div class="row g-4">
  <div class="col-lg-7"><div class="card-box">
    <h5 class="price"><i class="bi bi-geo-alt-fill"></i> ที่อยู่ในการจัดส่ง</h5>
    <div class="row g-2 mb-2">
      <div class="col"><input name="receiver" class="form-control" placeholder="ชื่อผู้รับ" required value="<?= e(user()['name']) ?>"></div>
      <div class="col"><input name="phone" class="form-control" placeholder="เบอร์โทร" required value="<?= e(user()['phone']) ?>"></div>
    </div>
    <textarea name="address" id="address" class="form-control mb-2" rows="2" placeholder="บ้านเลขที่ ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์" required><?= e($_POST['address'] ?? '') ?></textarea>
    <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
      <small class="text-muted">พิมพ์ที่อยู่แล้วหมุดจะปักให้อัตโนมัติ (คลิกแผนที่/ลากหมุดเพื่อปรับให้ตรงบ้านได้)</small>
      <span class="text-nowrap">
        <button type="button" id="findBtn" class="btn btn-sm btn-outline-pink"><i class="bi bi-search"></i> ค้นหาที่อยู่</button>
        <button type="button" id="locBtn" class="btn btn-sm btn-outline-pink"><i class="bi bi-crosshair"></i> ตำแหน่งปัจจุบัน</button>
      </span>
    </div>
    <div id="geoStatus" class="small mb-2 text-muted" style="min-height:1.3em"></div>
    <div id="map" class="map"></div>
    <input type="hidden" name="lat" id="lat"><input type="hidden" name="lng" id="lng">
  </div></div>

  <div class="col-lg-5"><div class="card-box">
    <h5>สรุปคำสั่งซื้อ</h5>
    <?php foreach ($items as $it): ?>
      <div class="d-flex justify-content-between small mb-1">
        <span><?= e($it['name']) ?> (ไซซ์ <?= e($it['size']) ?>) × <?= $it['qty'] ?></span><span><?= money($it['subtotal']) ?></span>
      </div>
    <?php endforeach; ?>
    <hr>
    <div class="d-flex justify-content-between"><span>ค่าสินค้า</span><span><?= money($total) ?></span></div>
    <?php if ($coupon): ?>
      <div class="d-flex justify-content-between price"><span>ส่วนลด <span class="coupon-tag small"><?= e($coupon['code']) ?></span></span><span>-<?= money($discount) ?></span></div>
    <?php endif; ?>
    <div class="d-flex justify-content-between"><span>ภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>%</span><span><?= money($vat) ?></span></div>
    <div class="d-flex justify-content-between"><span>ค่าจัดส่ง</span><span><?= money(SHIPPING_FEE) ?></span></div>
    <div class="d-flex justify-content-between fs-5 mt-2"><b>ยอดชำระ</b><b class="price"><?= money($grand) ?></b></div>
    <hr><h6>วิธีการชำระเงิน</h6>
    <?php foreach ($methods as $k => [$icon, $label]): ?>
      <div class="form-check mb-1"><input class="form-check-input" type="radio" name="payment" value="<?= $k ?>" id="pm_<?= $k ?>" <?= $k === 'promptpay' ? 'checked' : '' ?>>
        <label class="form-check-label" for="pm_<?= $k ?>"><i class="bi bi-<?= $icon ?>"></i> <?= $label ?></label></div>
    <?php endforeach; ?>
    <?php if (!omise_on()): ?>
    <div id="slipBox" class="border rounded p-3 mt-2" style="background:#fff8fb">
      <div class="text-center mb-2">
        <div class="small text-muted mb-1">สแกนจ่ายยอด <b class="price"><?= money($grand) ?></b> ด้วยแอปธนาคาร</div>
        <img src="https://promptpay.io/<?= PROMPTPAY_ID ?>/<?= $grand ?>.png" alt="QR Code พร้อมเพย์ ยอด <?= money($grand) ?>" width="180" height="180" loading="lazy" style="max-width:180px">
      </div>
      <label class="form-label small mb-1" for="slipFile"><i class="bi bi-paperclip"></i> แนบสลิปการโอนเงิน (JPG / PNG / WEBP ไม่เกิน 5 MB)</label>
      <input type="file" id="slipFile" name="slip" accept="image/jpeg,image/png,image/webp" class="form-control mb-2">
      <div id="slipPrev" class="text-center mb-2" style="display:none">
        <img id="slipImg" alt="ตัวอย่างสลิปที่เลือก" style="max-width:200px;max-height:260px;border-radius:8px;border:1px solid #ffd1dc">
        <div class="small text-muted mt-1" id="slipName"></div>
      </div>
      <small class="text-muted d-block"><i class="bi bi-info-circle"></i> แนบตอนนี้ได้เลย หรือกดสั่งสินค้าก่อนแล้วแนบที่หน้าชำระเงินทีหลังก็ได้ ระบบตรวจว่าเป็นรูปจริงและลบพิกัด GPS ออกจากรูปอัตโนมัติ</small>
    </div>
    <?php endif; ?>
    <div class="form-check mt-3 small">
      <input class="form-check-input" type="checkbox" name="pdpa" value="1" id="pdpa" required>
      <label class="form-check-label" for="pdpa">ข้าพเจ้ายินยอมให้ร้านเก็บและใช้ชื่อ เบอร์โทร ที่อยู่ และพิกัดจัดส่ง เพื่อจัดส่งสินค้าเท่านั้น ตาม<a href="privacy.php" target="_blank">นโยบายความเป็นส่วนตัว (PDPA)</a></label>
    </div>
    <button class="btn btn-pink btn-lg w-100 mt-3">สั่งสินค้า</button>
  </div></div>
</div>
</form>
<script>
const map = L.map('map').setView([<?= SHOP_LAT ?>, <?= SHOP_LNG ?>], 11);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
let marker, manual = false, timer, reqId = 0;
const addrEl = document.getElementById('address'), geoStatus = document.getElementById('geoStatus');
function say(msg, cls) { geoStatus.className = 'small mb-2 ' + (cls || 'text-muted'); geoStatus.textContent = msg; }
function setPos(la, ln, byUser) {
  if (byUser) manual = true;                     // ผู้ใช้ปรับหมุดเอง -> ไม่ให้ระบบอัตโนมัติมาย้ายทับ
  if (marker) marker.setLatLng([la, ln]);
  else marker = L.marker([la, ln], {draggable: true}).addTo(map)
      .on('dragend', e => { const p = e.target.getLatLng(); setPos(p.lat, p.lng, true); say('ปรับตำแหน่งหมุดเองแล้ว', 'text-success'); });
  lat.value = la.toFixed(7); lng.value = ln.toFixed(7);
}
map.on('click', e => { setPos(e.latlng.lat, e.latlng.lng, true); say('ปักหมุดตามที่คลิกแล้ว', 'text-success'); });
locBtn.onclick = () => navigator.geolocation.getCurrentPosition(
  p => { setPos(p.coords.latitude, p.coords.longitude, true); map.setView([p.coords.latitude, p.coords.longitude], 16); say('ใช้ตำแหน่งปัจจุบันแล้ว', 'text-success'); },
  () => alert('ไม่สามารถเข้าถึงตำแหน่งได้'));

// ---- ปักหมุดอัตโนมัติจากที่อยู่ที่พิมพ์ (OpenStreetMap Nominatim) ----
function candidates(text) {
  const clean = text.replace(/[,\n]+/g, ' ').replace(/\s+/g, ' ').trim();
  const parts = clean.split(' ').filter(Boolean);
  const out = [clean];
  for (let i = 1; i < parts.length - 1 && out.length < 5; i++) out.push(parts.slice(i).join(' ')); // ตัดบ้านเลขที่/ซอยออกทีละคำ
  const zip = (clean.match(/\b\d{5}\b/) || [])[0];
  if (zip) out.push(zip);
  return [...new Set(out)];
}
async function geocode(auto) {
  const text = addrEl.value.trim();
  if (text.length < 8) { if (!auto) say('กรุณากรอกที่อยู่ให้ครบขึ้นอีกนิด', 'text-danger'); return; }
  const my = ++reqId, list = candidates(text);
  say('กำลังค้นหาตำแหน่งจากที่อยู่...');
  try {
    for (let i = 0; i < list.length; i++) {
      if (my !== reqId) return;                  // มีการพิมพ์ใหม่แล้ว ทิ้งการค้นหาเก่า
      const r = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=th&accept-language=th&q=' + encodeURIComponent(list[i]));
      if (!r.ok) throw new Error('http ' + r.status);
      const res = await r.json();
      if (my !== reqId) return;
      if (res.length) {
        const la = parseFloat(res[0].lat), ln = parseFloat(res[0].lon);
        setPos(la, ln, false);
        map.setView([la, ln], i === 0 ? 17 : 15);
        say(i === 0 ? 'ปักหมุดตามที่อยู่แล้ว ลากหมุดเพื่อปรับให้ตรงบ้านได้' : 'ปักหมุดโดยประมาณจากระดับตำบล/อำเภอ กรุณาลากหมุดให้ตรงบ้าน', i === 0 ? 'text-success' : 'text-warning');
        return;
      }
      await new Promise(ok => setTimeout(ok, 1100)); // Nominatim จำกัด 1 คำขอ/วินาที
    }
    say('ไม่พบที่อยู่นี้ในแผนที่ กรุณาคลิกบนแผนที่เพื่อปักหมุดเอง', 'text-danger');
  } catch (e) {
    say('ค้นหาอัตโนมัติไม่สำเร็จ กรุณาคลิกบนแผนที่เพื่อปักหมุดเอง', 'text-danger');
  }
}
addrEl.addEventListener('input', () => { if (manual) return; clearTimeout(timer); timer = setTimeout(() => geocode(true), 1200); });
document.getElementById('findBtn').onclick = () => { manual = false; geocode(false); };
if (addrEl.value.trim().length >= 8) geocode(true);

// ---- ช่องแนบสลิป: แสดงเฉพาะเมื่อเลือก QR พร้อมเพย์ ----
const slipBox = document.getElementById('slipBox');
if (slipBox) {
  const sync = () => { const r = document.querySelector('input[name=payment]:checked'); slipBox.style.display = r && r.value === 'promptpay' ? 'block' : 'none'; };
  document.querySelectorAll('input[name=payment]').forEach(r => r.addEventListener('change', sync)); sync();
  document.getElementById('slipFile').addEventListener('change', function () {
    const f = this.files[0], box = document.getElementById('slipPrev');
    if (!f) { box.style.display = 'none'; return; }
    if (f.size > 5 * 1024 * 1024) { alert('ไฟล์ใหญ่เกิน 5 MB กรุณาเลือกไฟล์ใหม่'); this.value = ''; box.style.display = 'none'; return; }
    if (!/^image\/(jpeg|png|webp)$/.test(f.type)) { alert('รองรับเฉพาะรูปภาพ JPG, PNG, WEBP'); this.value = ''; box.style.display = 'none'; return; }
    document.getElementById('slipImg').src = URL.createObjectURL(f);
    document.getElementById('slipName').textContent = f.name;
    box.style.display = 'block';
  });
}
</script>
<?php include 'includes/footer.php'; ?>
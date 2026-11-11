<?php $needMap = true; require 'config.php'; require_login();
$o = get_order($pdo, $_GET['id'] ?? 0);
if (isset($_GET['live'])) {
    header('Content-Type: application/json');
    exit(json_encode(['status' => $o['status'], 'at' => $o['cur_at'],
        'pos' => $o['cur_lat'] ? [(float)$o['cur_lat'], (float)$o['cur_lng']] : null]));
}
$st = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?"); $st->execute([$o['id']]); $items = $st->fetchAll();
$st = $pdo->prepare("SELECT * FROM order_tracking WHERE order_id=? ORDER BY id"); $st->execute([$o['id']]); $tracks = $st->fetchAll();
$points = array_values(array_map(fn($t) => [(float)$t['lat'], (float)$t['lng']], array_filter($tracks, fn($t) => $t['lat'] !== null)));
$steps = ['pending'=>'bi-receipt', 'verifying'=>'bi-cash-coin', 'confirmed'=>'bi-box-seam', 'shipping'=>'bi-truck', 'delivered'=>'bi-house-check'];
$idx = array_search($o['status'], array_keys($steps));
$title = 'ติดตามคำสั่งซื้อ #' . $o['id']; include 'includes/header.php'; ?>

<div class="card-box mb-4">
  <div class="d-flex justify-content-between mb-3">
    <h5 class="mb-0">คำสั่งซื้อ #<?= $o['id'] ?></h5>
    <div><?= $o['tracking_no'] ? 'เลขพัสดุ: <b>' . e($o['tracking_no']) . '</b> ' : '' ?><?= badge($o['status']) ?></div>
  </div>
  <?php if ($o['status'] !== 'cancelled'): ?>
  <div class="stepper">
    <?php $i = 0; foreach ($steps as $k => $icon): ?>
      <div class="step <?= $idx !== false && $i <= $idx ? 'done' : '' ?>"><i class="bi <?= $icon ?>"></i><?= STATUS[$k] ?></div>
    <?php $i++; endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<div class="row g-4">
  <div class="col-lg-8"><div class="card-box">
    <h6><i class="bi bi-map"></i> ตำแหน่งพัสดุ</h6>
    <div id="map" class="map"></div>
    <small class="text-muted"><i class="bi bi-shop" style="color:#ff6b9d"></i> ร้านค้า ·
      <i class="bi bi-truck" style="color:#e84a7f"></i> จุดอัปเดตล่าสุด ·
      <i class="bi bi-scooter" style="color:#7c4dff"></i> ไรเดอร์ (สด) ·
      <i class="bi bi-house-heart" style="color:#28a745"></i> ปลายทาง</small>
  </div></div>
  <div class="col-lg-4">
    <div class="card-box mb-3">
      <h6>ประวัติการจัดส่ง</h6>
      <ul class="timeline mt-3">
        <?php foreach (array_reverse($tracks) as $t): ?>
          <li><b><?= STATUS[$t['status']] ?? e($t['status']) ?></b><br>
            <small><?= e($t['note']) ?></small><br><small class="text-muted"><?= $t['created_at'] ?></small></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="card-box">
      <h6>ผู้รับ</h6>
      <p class="small mb-2"><?= e($o['receiver']) ?> · <?= e($o['phone']) ?><br><?= e($o['address']) ?></p>
      <?php foreach ($items as $it): ?>
        <div class="d-flex justify-content-between small"><span><?= e($it['name']) ?> (<?= e($it['size']) ?>) × <?= $it['qty'] ?></span><span><?= money($it['price'] * $it['qty']) ?></span></div>
      <?php endforeach; ?>
      <?php if ($o['discount'] > 0): ?><div class="d-flex justify-content-between small price"><span>ส่วนลด <?= e($o['coupon_code']) ?></span><span>-<?= money($o['discount']) ?></span></div><?php endif; ?>
      <?php if (($o['vat'] ?? 0) > 0): ?><div class="d-flex justify-content-between small"><span>ภาษีมูลค่าเพิ่ม <?= round(VAT_RATE * 100) ?>%</span><span><?= money($o['vat']) ?></span></div><?php endif; ?>
      <div class="text-end mt-2">รวม <b class="price"><?= money($o['total']) ?></b></div>
    </div>
  </div>
</div>

<script>
const shop = [<?= SHOP_LAT ?>, <?= SHOP_LNG ?>];
const dest = <?= $o['lat'] ? json_encode([(float)$o['lat'], (float)$o['lng']]) : 'null' ?>;
const pts  = <?= json_encode($points) ?>;
const map = L.map('map');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
const pin = (c, i) => L.divIcon({className: '', html: `<div class="map-pin" style="background:${c}"><i class="bi bi-${i}"></i></div>`, iconSize: [34, 34], iconAnchor: [17, 17]});
L.marker(shop, {icon: pin('#ff6b9d', 'shop')}).addTo(map).bindPopup('ร้าน Sneaks Store');
if (dest) L.marker(dest, {icon: pin('#28a745', 'house-heart')}).addTo(map).bindPopup('ที่อยู่จัดส่ง');
if (pts.length) {
  L.polyline([shop, ...pts], {color: '#e84a7f', weight: 4}).addTo(map);
  if (dest) L.polyline([pts[pts.length - 1], dest], {color: '#e84a7f', weight: 3, dashArray: '6 8', opacity: .6}).addTo(map);
  L.marker(pts[pts.length - 1], {icon: pin('#e84a7f', 'truck')}).addTo(map).bindPopup('จุดอัปเดตล่าสุด');
}
const all = [shop, ...pts]; if (dest) all.push(dest);
map.fitBounds(all, {padding: [40, 40], maxZoom: 15});
<?php if ($o['status'] === 'shipping'): ?>
let live;
const poll = () => fetch('track.php?id=<?= $o['id'] ?>&live=1').then(r => r.json()).then(d => {
  if (d.status !== 'shipping') return location.reload();
  if (!d.pos) return;
  if (live) live.setLatLng(d.pos);
  else live = L.marker(d.pos, {icon: pin('#7c4dff', 'scooter')}).addTo(map).bindPopup('ไรเดอร์กำลังไปหาคุณ').openPopup();
}).catch(() => {});
poll(); setInterval(poll, 10000);
<?php endif; ?>
</script>
<?php include 'includes/footer.php'; ?>
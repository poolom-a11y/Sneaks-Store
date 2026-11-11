<?php if (!headers_sent()) http_response_code(500); ?>
<!doctype html>
<html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ระบบขัดข้องชั่วคราว (Error 500) | Sneaks Store</title>
<meta name="description" content="ระบบขัดข้องชั่วคราว กรุณาลองใหม่อีกครั้งหรือติดต่อ Sneaks Store"><meta name="robots" content="noindex">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{font-family:'Prompt',sans-serif;background:linear-gradient(135deg,#ffd1dc,#fff5f8 60%);min-height:100vh}.pk{color:#e84a7f}.btn-pk{background:#e84a7f;color:#fff}.btn-pk:hover{background:#d13a6d;color:#fff}</style>
</head><body><div class="container text-center py-5">
  <div class="pk fw-bold" style="font-size:6rem;line-height:1">500</div>
  <h4 class="mt-3">ขออภัย ระบบขัดข้องชั่วคราว</h4>
  <p class="text-muted">เรากำลังแก้ไขอยู่ กรุณาลองใหม่อีกครั้งในอีกสักครู่<br>หากยังไม่หาย ติดต่อเราได้ที่ <?= defined('SHOP_PHONE') ? htmlspecialchars(SHOP_PHONE) : '' ?> <?= defined('SHOP_EMAIL') ? htmlspecialchars(SHOP_EMAIL) : '' ?></p>
  <a href="javascript:location.reload()" class="btn btn-pk btn-lg">ลองใหม่อีกครั้ง</a>
  <a href="./" class="btn btn-outline-secondary btn-lg">กลับหน้าแรก</a>
</div></body></html>
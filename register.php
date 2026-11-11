<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); $email = trim($_POST['email']); $phone = trim($_POST['phone']); $pw = $_POST['password'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'อีเมลไม่ถูกต้อง';
    elseif (strlen($pw) < 6) $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    elseif ($pw !== $_POST['password2']) $error = 'รหัสผ่านไม่ตรงกัน';
    elseif (empty($_POST['pdpa'])) $error = 'กรุณายอมรับนโยบายความเป็นส่วนตัว (PDPA) ก่อนสมัครสมาชิก';
    else {
        $st = $pdo->prepare("SELECT id FROM users WHERE email=?"); $st->execute([$email]);
        if ($st->fetch()) $error = 'อีเมลนี้ถูกใช้งานแล้ว';
        else {
            $pdo->prepare("INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)")
                ->execute([$name, $email, $phone, password_hash($pw, PASSWORD_DEFAULT)]);
            pdpa_log($pdo, $pdo->lastInsertId(), 'register');
            flash('สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ');
            redirect('login.php');
        }
    }
}
$title = 'สมัครสมาชิก'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card-box">
  <h4 class="text-center mb-3 price">สมัครสมาชิก</h4>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input name="name" class="form-control mb-2" placeholder="ชื่อ-นามสกุล" required value="<?= e($_POST['name'] ?? '') ?>">
    <input name="email" type="email" class="form-control mb-2" placeholder="อีเมล" required value="<?= e($_POST['email'] ?? '') ?>">
    <input name="phone" class="form-control mb-2" placeholder="เบอร์โทรศัพท์" value="<?= e($_POST['phone'] ?? '') ?>">
    <input name="password" type="password" class="form-control mb-2" placeholder="รหัสผ่าน" required>
    <input name="password2" type="password" class="form-control mb-3" placeholder="ยืนยันรหัสผ่าน" required>
    <div class="form-check mb-3 small">
      <input class="form-check-input" type="checkbox" name="pdpa" value="1" id="pdpa" required>
      <label class="form-check-label" for="pdpa">ข้าพเจ้ายินยอมให้เก็บและใช้ข้อมูลส่วนบุคคล (ชื่อ อีเมล เบอร์โทร) เพื่อการสมัครสมาชิกและการสั่งซื้อ ตาม<a href="privacy.php" target="_blank">นโยบายความเป็นส่วนตัว (PDPA)</a></label>
    </div>
    <button class="btn btn-pink w-100">สมัครสมาชิก</button>
  </form>
  <p class="text-center mt-3 small">มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
</div></div></div>
<?php include 'includes/footer.php'; ?>
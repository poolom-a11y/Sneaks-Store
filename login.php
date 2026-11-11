<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare("SELECT * FROM users WHERE email=?"); $st->execute([trim($_POST['email'])]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'], $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'], 'name'=>$u['name'], 'email'=>$u['email'], 'phone'=>$u['phone'], 'role'=>$u['role']];
        $home = ['admin' => '/admin/', 'rider' => '/rider/'][$u['role']] ?? '/';
        $go = $_SESSION['redirect'] ?? BASE_URL . $home;
        unset($_SESSION['redirect']);
        redirect($go);
    }
    $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
}
$title = 'เข้าสู่ระบบ'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-4"><div class="card-box">
  <h4 class="text-center mb-3 price">เข้าสู่ระบบ</h4>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input name="email" type="email" class="form-control mb-2" placeholder="อีเมล" required>
    <input name="password" type="password" class="form-control mb-3" placeholder="รหัสผ่าน" required>
    <button class="btn btn-pink w-100">เข้าสู่ระบบ</button>
  </form>
  <p class="text-center mt-3 small">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
</div></div></div>
<?php include 'includes/footer.php'; ?>
<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    redirect('profile.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '' || $phone === '' || $password === '' || $confirmPassword === '') {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $error = 'Số điện thoại không hợp lệ.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu cần tối thiểu 6 ký tự.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        $checkStmt = getDB()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $checkStmt->execute([$email]);

        if ($checkStmt->fetch()) {
            $error = 'Email này đã được đăng ký.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $hash = password_hash($password, PASSWORD_DEFAULT);
$defaultAvatar = 'assets/images/avatardefault.jpg';

$stmt = getDB()->prepare('
    INSERT INTO users (name, email, phone, password, avatar, role, status)
    VALUES (?, ?, ?, ?, ?, "user", 1)
');
$stmt->execute([$name, $email, $phone, $hash, $defaultAvatar]);
            set_flash('success', 'Đăng ký thành công. Bạn có thể đăng nhập ngay bây giờ.');
            redirect('login.php');
        }
    }
}

$title = 'TechMart - Đăng ký';
$extraCss = [];
require __DIR__ . '/includes/header.php';
?>
<main class="container" style="padding: 40px 0">
  <div class="card auth-card">
    <div class="auth-switch">
      <button type="button" class="active">Đăng ký</button>
      <button type="button" onclick="window.location.href='<?= url('login.php') ?>'">Đăng nhập</button>
    </div>

    <h2 style="margin: 20px 0">Đăng ký tài khoản</h2>

    <?php if ($error !== ''): ?>
      <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label>Họ và tên</label>
        <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required />
      </div>

      <div class="form-group">
        <label>Số điện thoại</label>
        <input type="text" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" required />
      </div>

      <div class="form-group">
        <label>Mật khẩu</label>
        <input type="password" name="password" required />
      </div>

      <div class="form-group">
        <label>Nhập lại mật khẩu</label>
        <input type="password" name="confirm_password" required />
      </div>

      <button class="btn btn-primary" style="width: 100%" type="submit">Đăng ký</button>
    </form>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
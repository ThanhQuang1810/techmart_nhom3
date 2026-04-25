<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    $user = current_user();
    redirect($user && $user['role'] === 'admin' ? url('admin/index.php') : url('index.php'));
}

$error          = '';
$redirectTarget = trim((string)($_GET['redirect'] ?? $_POST['redirect'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim((string)($_POST['email']    ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $stmt = getDB()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Sai tài khoản hoặc mật khẩu.';
        } elseif ((int)$user['status'] !== 1) {
            $error = 'Tài khoản của bạn đang bị khoá.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            set_flash('success', 'Đăng nhập thành công. Xin chào, ' . $user['name'] . '!');

            if ($user['role'] === 'admin') {
                redirect(url('admin/index.php'));
            }

            $safeRedirect = safe_redirect_target($redirectTarget);
            redirect($safeRedirect !== '' ? $safeRedirect : url('index.php'));
        }
    }
}

$title    = 'TechMart - Đăng nhập';
$extraCss = [];
require __DIR__ . '/includes/header.php';
?>
<main class="container" style="padding:40px 0;">
  <div class="card auth-card">
    <div class="auth-switch">
      <button type="button" onclick="window.location.href='<?= url('register.php') ?>'">Đăng ký</button>
      <button type="button" class="active">Đăng nhập</button>
    </div>
    <h2 style="margin:20px 0;">Đăng nhập tài khoản</h2>
    <?php if ($redirectTarget !== ''): ?>
      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#1d4ed8;">
        <i class="fa-solid fa-circle-info" style="margin-right:6px;"></i>
        Vui lòng đăng nhập để tiếp tục.
      </div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="hidden" name="redirect" value="<?= e($redirectTarget) ?>">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" placeholder="Nhập email" required>
      </div>
      <div class="form-group">
        <label>Mật khẩu</label>
        <input type="password" name="password" placeholder="Nhập mật khẩu" required>
      </div>
      <button class="btn btn-primary" style="width:100%;" type="submit">Đăng nhập</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:14px;color:var(--muted);">
      Chưa có tài khoản?
      <a href="<?= url('register.php') ?>" style="color:var(--primary);font-weight:600;">Đăng ký ngay</a>
    </p>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

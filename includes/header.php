<?php
require_once __DIR__ . '/../config.php';

$title = $title ?? 'TechMart';
$extraCss = $extraCss ?? [];
$user = current_user();
$flash = get_flash();
$searchValue = trim((string) ($_GET['search'] ?? ''));
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');

function nav_active(array $pages, string $currentScript): string
{
    return in_array($currentScript, $pages, true) ? ' style="color: var(--primary); font-weight: 700;"' : '';
}
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<link rel="stylesheet" href="<?= url('css/shared.css') ?>?v=999" />
    <?php foreach ($extraCss as $cssFile): ?>
      <link rel="stylesheet" href="<?= url($cssFile) ?>" />
    <?php endforeach; ?>
  </head>
  <body>
    <button id="backToTop" type="button">
      <i class="fa-solid fa-arrow-up"></i>
    </button>

    <header class="site-header">
      <div class="container header-top">
        <a href="<?= url('index.php') ?>" class="logo">Tech<span>Mart</span></a>

        <form class="search-bar" action="<?= url('category.php') ?>" method="get">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" value="<?= e($searchValue) ?>" placeholder="Tìm điện thoại, laptop, phụ kiện..." />
          <button type="submit">Tìm kiếm</button>
        </form>

        <div class="header-actions">
          <?php if ($user): ?>
            <?php if ($user['role'] === 'admin'): ?>
              <a href="<?= url('admin/index.php') ?>" class="action-link">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span>Admin</span>
              </a>
            <?php endif; ?>
            <a href="<?= url('profile.php') ?>" class="action-link" style="display:flex;align-items:center;gap:8px;">
  <img
    src="<?= url(!empty($user['avatar']) ? $user['avatar'] : 'assets/images/avatar.jpg') ?>"
    alt="Avatar"
    style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:1px solid #ddd;"
  >
  <span><?= e($user['name']) ?></span>
</a>
            <a href="<?= url('logout.php') ?>" class="action-link">
              <i class="fa-solid fa-right-from-bracket"></i>
              <span>Đăng xuất</span>
            </a>
          <?php else: ?>
            <a href="<?= url('login.php') ?>" class="action-link">
              <i class="fa-regular fa-user"></i>
              <span>Đăng nhập</span>
            </a>
            <a href="<?= url('register.php') ?>" class="action-link">
              <i class="fa-solid fa-user-plus"></i>
              <span>Đăng ký</span>
            </a>
          <?php endif; ?>

          <a href="<?= url('cart.php') ?>" class="cart-link action-link">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Giỏ hàng</span>
            <b class="cart-count"><?= cart_count() ?></b>
          </a>

          <button id="themeToggle" class="action-link theme-toggle" type="button" aria-label="Đổi giao diện" title="Đổi giao diện">
            <i class="fa-solid fa-moon"></i>
          </button>
        </div>
      </div>

      <div class="header-bottom">
        <div class="container nav-links">
          <a href="<?= url('index.php') ?>"<?= nav_active(['index.php'], $currentScript) ?>>Trang chủ</a>
          <a href="<?= url('category.php') ?>"<?= nav_active(['category.php', 'product-detail.php'], $currentScript) ?>>Danh mục</a>
          <a href="<?= url('about.php') ?>"<?= nav_active(['about.php'], $currentScript) ?>>Giới thiệu</a>
         </div>
      </div>
    </header>

    <?php if ($flash): ?>
  <div class="flash flash-<?= e($flash['type']) ?>">
    <?= e($flash['message']) ?>
  </div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const flash = document.querySelector('.flash');
    if (!flash) return;
    setTimeout(function () {
        flash.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        flash.style.opacity    = '0';
        flash.style.transform  = 'translateX(120px)';
        setTimeout(() => flash.remove(), 500);
    }, 3000);
});
</script>
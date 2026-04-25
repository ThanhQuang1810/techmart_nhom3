<?php
require_once __DIR__ . '/../config.php';

require_admin();
$adminTitle = $adminTitle ?? 'Quản trị hệ thống';
$currentAdminScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$flash = get_flash();
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($adminTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="<?= url('css/shared.css') ?>" />
    <link rel="stylesheet" href="<?= url('css/admin.css') ?>" />
  </head>
  <body class="admin-page">
    <header class="admin-topbar">
      <div class="container">
        <a href="<?= url('admin/index.php') ?>" class="admin-brand">Tech<span>Mart Admin</span></a>
       <div class="admin-topbar-actions">
  <a href="<?= url('logout.php') ?>"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
</div>
      </div>
    </header>

    <div class="container admin-layout">
      <aside class="admin-sidebar">
        <h3>Quản trị</h3>
        <a href="<?= url('admin/index.php') ?>" class="<?= $currentAdminScript === 'index.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="<?= url('admin/categories.php') ?>" class="<?= $currentAdminScript === 'categories.php' ? 'active' : '' ?>">Danh mục</a>
        <a href="<?= url('admin/products.php') ?>" class="<?= $currentAdminScript === 'products.php' ? 'active' : '' ?>">Sản phẩm</a>
        <a href="<?= url('admin/orders.php') ?>" class="<?= $currentAdminScript === 'orders.php' ? 'active' : '' ?>">Đơn hàng</a>
        <a href="<?= url('admin/users.php') ?>" class="<?= $currentAdminScript === 'users.php' ? 'active' : '' ?>">Người dùng</a>
      </aside>

      <main class="admin-main">
        <?php if ($flash): ?>
          <div class="flash flash-<?= e($flash['type']) ?>" style="margin-top:0;">
            <div><?= e($flash['message']) ?></div>
          </div>
        <?php endif; ?>

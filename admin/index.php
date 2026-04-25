<?php
require_once __DIR__ . '/../config.php';
require_admin();

$adminTitle = 'Admin - Dashboard';
$pdo = getDB();

$metrics = [
    'products'   => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'categories' => (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'users'      => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'orders'     => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'revenue'    => (float)$pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders WHERE status = "Đã giao"')->fetchColumn(),
];

// Báo cáo
$totalContacts  = (int)$pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
$unreadContacts = (int)$pdo->query('SELECT COUNT(*) FROM contacts WHERE is_read = 0')->fetchColumn();

$latestOrders = $pdo->query('
    SELECT o.*, u.name AS user_name
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    ORDER BY o.id DESC LIMIT 8
')->fetchAll();

require __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-card">
  <h1>Dashboard tổng quan</h1>
  <p class="small-note">Tổng hợp nhanh các chỉ số quan trọng của hệ thống bán hàng.</p>

  <div class="metric-grid" style="margin-top:18px;">
    <div class="metric-card"><div>Sản phẩm</div><div class="value"><?= $metrics['products'] ?></div></div>
    <div class="metric-card"><div>Danh mục</div><div class="value"><?= $metrics['categories'] ?></div></div>
    <div class="metric-card"><div>Người dùng</div><div class="value"><?= $metrics['users'] - 1 ?></div></div>
    <div class="metric-card"><div>Đơn hàng</div><div class="value"><?= $metrics['orders'] ?></div></div>
  </div>

  <!-- Doanh thu -->
  <div class="metric-card" style="margin-top:16px;">
    <div>Doanh thu đã giao</div>
    <div class="value"><?= format_price($metrics['revenue']) ?></div>
  </div>

  <!-- Card báo cáo -->
  <div class="metric-card" style="margin-top:16px; border-left:4px solid #ff4d4f;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="font-size:14px; color:var(--muted);">Báo cáo & Liên hệ</div>
        <div class="value"><?= $totalContacts ?></div>
        <?php if ($unreadContacts > 0): ?>
          <div style="color:#ff4d4f; font-size:13px; font-weight:700; margin-top:6px;">
            ● <?= $unreadContacts ?> chưa đọc
          </div>
        <?php else: ?>
          <div style="color:#16a34a; font-size:13px; font-weight:700; margin-top:6px;">
            ✓ Đã đọc hết
          </div>
        <?php endif; ?>
      </div>
      <a href="<?= url('admin/contacts.php') ?>"
         style="display:inline-flex; align-items:center; gap:8px;
                background:linear-gradient(135deg,#ff4d4f,#ff7875);
                color:#fff; border-radius:12px; padding:12px 22px;
                font-weight:700; font-size:14px; text-decoration:none;
                box-shadow:0 6px 16px rgba(255,77,79,0.28);">
        <i class="fa-solid fa-flag"></i>
        Xem báo cáo
        <?php if ($unreadContacts > 0): ?>
          <span style="background:#fff; color:#ff4d4f; border-radius:999px;
                       padding:1px 8px; font-size:12px; font-weight:800;">
            <?= $unreadContacts ?>
          </span>
        <?php endif; ?>
      </a>
    </div>
  </div>
</div>

<!-- Đơn hàng mới nhất -->
<div class="admin-card admin-table">
  <h2>Đơn hàng mới nhất</h2>
  <div class="table-responsive">
    <table class="site-table">
      <thead>
        <tr>
          <th>Mã đơn</th>
          <th>Khách hàng</th>
          <th>Tổng tiền</th>
          <th>Trạng thái</th>
          <th>Ngày tạo</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($latestOrders as $order): ?>
          <tr>
            <td>#<?= (int)$order['id'] ?></td>
            <td><?= e($order['user_name'] ?: $order['full_name']) ?></td>
            <td><?= format_price($order['total']) ?></td>
            <td>
              <?php $statusClass = 'status-' . str_replace([' ', '/'], '-', $order['status']); ?>
              <span class="status-badge <?= e($statusClass) ?>"><?= e($order['status']) ?></span>
            </td>
            <td><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>

<?php
require_once __DIR__ . '/config.php';
require_admin();

$pdo = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $status = trim((string) ($_POST['status'] ?? ''));
    if (in_array($status, fetch_order_statuses(), true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $orderId]);
        set_flash('success', 'Đã cập nhật trạng thái đơn hàng #' . $orderId . '.');
    }
    redirect('admin/orders.php');
}

$sql = 'SELECT o.*, u.name AS user_name, GROUP_CONCAT(CONCAT(oi.product_name, " x", oi.quantity) SEPARATOR " || ") AS items_summary
        FROM orders o
        LEFT JOIN users u ON u.id = o.user_id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        GROUP BY o.id
        ORDER BY o.id DESC';
$orders = $pdo->query($sql)->fetchAll();
$adminTitle = 'Admin - Đơn hàng';
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="admin-card admin-table">
  <h1>Quản lý đơn hàng</h1>
  <div class="table-responsive">
    <table class="site-table">
      <thead>
        <tr>
          <th>Mã đơn</th>
          <th>Khách hàng</th>
          <th>Chi tiết</th>
          <th>Tổng tiền</th>
          <th>Địa chỉ</th>
          <th>Trạng thái</th>
          <th>Cập nhật</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td>#<?= (int) $order['id'] ?><br><span class="small-note"><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></span></td>
            <td><?= e($order['user_name'] ?: $order['full_name']) ?><br><span class="small-note"><?= e($order['phone']) ?></span></td>
            <td>
              <?php foreach (explode(' || ', (string) $order['items_summary']) as $summary): ?>
                <?php if (trim($summary) !== ''): ?><div><?= e($summary) ?></div><?php endif; ?>
              <?php endforeach; ?>
            </td>
            <td><?= format_price($order['total']) ?></td>
            <td><?= e($order['address']) ?></td>
            <td>
              <?php $statusClass = 'status-' . str_replace([' ', '/'], '-', $order['status']); ?>
              <span class="status-badge <?= e($statusClass) ?>"><?= e($order['status']) ?></span>
            </td>
            <td>
              <form method="post">
                <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                <select name="status" onchange="this.form.submit()">
                  <?php foreach (fetch_order_statuses() as $status): ?>
                    <option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>

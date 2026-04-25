<?php
require_once __DIR__ . '/../config.php';
require_admin();

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status  = trim((string)($_POST['status'] ?? ''));

    if (in_array($status, fetch_order_statuses(), true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $orderId]);
        set_flash('success', 'Đã cập nhật trạng thái đơn #' . $orderId);
    }

    redirect('admin/orders.php');
}

// Chỉ lấy thông tin đơn hàng
$sql = '
    SELECT o.*, u.name AS user_name
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    ORDER BY o.id DESC
';
$orders = $pdo->query($sql)->fetchAll();

// Lấy chi tiết sản phẩm theo từng đơn
$itemStmt = $pdo->prepare('
    SELECT
        order_id,
        product_id,
        product_name,
        selected_color,
        selected_image,
        quantity,
        price
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
');

$adminTitle = 'Admin - Đơn hàng';
require __DIR__ . '/../includes/admin_header.php';

$statusColors = [
    'Chờ xác nhận' => ['bg' => '#fff7ed', 'color' => '#c2410c', 'dot' => '#f97316'],
    'Đang xử lý'   => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'dot' => '#3b82f6'],
    'Đang giao'    => ['bg' => '#ecfeff', 'color' => '#0f766e', 'dot' => '#06b6d4'],
    'Đã giao'      => ['bg' => '#f0fdf4', 'color' => '#15803d', 'dot' => '#22c55e'],
    'Huỷ'          => ['bg' => '#fef2f2', 'color' => '#b91c1c', 'dot' => '#ef4444'],
];
?>

<style>
.orders-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}

.orders-header h1 {
    font-size: 22px;
    font-weight: 800;
}

.orders-count {
    background: #eff6ff;
    color: #1d4ed8;
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 13px;
    font-weight: 700;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
}

.order-table thead tr {
    background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
}

.order-table thead th {
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    padding: 14px 16px;
    text-align: left;
    white-space: nowrap;
}

.order-table thead th:first-child {
    border-radius: 12px 0 0 0;
}

.order-table thead th:last-child {
    border-radius: 0 12px 0 0;
}

.order-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}

.order-table tbody tr:hover {
    background: #f8faff;
}

.order-table tbody td {
    padding: 14px 16px;
    font-size: 14px;
    vertical-align: middle;
}

.order-id {
    font-weight: 800;
    font-size: 15px;
    color: #1e3a8a;
}

.order-date {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 3px;
}

.customer-name {
    font-weight: 700;
    font-size: 14px;
}

.customer-phone {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 3px;
}

.order-total {
font-weight: 800;
    font-size: 15px;
    color: #0f172a;
    white-space: nowrap;
}

.order-address {
    font-size: 13px;
    color: #64748b;
    max-width: 180px;
    line-height: 1.4;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}

.status-select {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 7px 10px;
    font-size: 13px;
    font-weight: 600;
    background: #fff;
    cursor: pointer;
    color: #334155;
    transition: border-color 0.2s;
    min-width: 140px;
}

.status-select:focus {
    outline: none;
    border-color: #3b82f6;
}

.order-products {
    min-width: 260px;
}

.admin-order-product {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
}

.admin-order-product + .admin-order-product {
    border-top: 1px solid #e5e7eb;
    margin-top: 8px;
    padding-top: 12px;
}

.admin-order-product__image img {
    width: 54px;
    height: 54px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #dbe3f0;
    background: #fff;
    display: block;
}

.admin-order-product__info {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.admin-order-product__name {
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
}

.admin-order-product__meta {
    font-size: 13px;
    color: #64748b;
    line-height: 1.4;
}

.admin-order-product__meta strong {
    color: #1e293b;
}

@media (max-width: 1100px) {
    .order-products {
        min-width: 220px;
    }
}
</style>

<div class="admin-card">
    <div class="orders-header">
        <h1>Quản lý đơn hàng</h1>
        <span class="orders-count"><?= count($orders) ?> đơn hàng</span>
    </div>

    <div class="table-responsive">
        <table class="order-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Tổng tiền</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $sc = $statusColors[$order['status']] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'dot' => '#94a3b8'];

                    $itemStmt->execute([(int)$order['id']]);
                    $items = $itemStmt->fetchAll();
                    ?>
                    <tr>
                        <td>
                            <div class="order-id">#<?= (int)$order['id'] ?></div>
                            <div class="order-date">
                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
</div>
                        </td>

                        <td>
                            <div class="customer-name">
                                <?= e($order['user_name'] ?: $order['full_name']) ?>
                            </div>
                            <div class="customer-phone"><?= e($order['phone']) ?></div>
                        </td>

                        <td class="order-products">
                            <?php if ($items): ?>
                                <?php foreach ($items as $item): ?>
                                    <div class="admin-order-product">
                                        <div class="admin-order-product__image">
                                            <img
                                                src="<?= url($item['selected_image'] ?: 'assets/images/no-image.png') ?>"
                                                alt="<?= e($item['product_name']) ?>"
                                            >
                                        </div>

                                        <div class="admin-order-product__info">
                                            <div class="admin-order-product__name">
                                                <?= e($item['product_name']) ?>
                                            </div>
                                            <div class="admin-order-product__meta">
                                                Màu: <strong><?= e($item['selected_color'] ?: 'Mặc định') ?></strong>
                                            </div>
                                            <div class="admin-order-product__meta">
                                                Số lượng: <strong>x<?= (int)$item['quantity'] ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span style="color:#94a3b8;">Không có dữ liệu sản phẩm</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="order-total"><?= format_price($order['total']) ?></div>
                        </td>

                        <td>
                            <div class="order-address"><?= e($order['address']) ?></div>
                        </td>

                        <td>
                            <span class="status-pill" style="background:<?= $sc['bg'] ?>; color:<?= $sc['color'] ?>;">
                                <span class="status-dot" style="background:<?= $sc['dot'] ?>;"></span>
                                <?= e($order['status']) ?>
                            </span>
                        </td>

                        <td>
                            <form method="POST">
<input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <?php foreach (fetch_order_statuses() as $s): ?>
                                        <option value="<?= e($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
                                            <?= e($s) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$orders): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 24px;">
                            Chưa có đơn hàng nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
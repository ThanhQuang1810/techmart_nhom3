<?php
require_once __DIR__ . '/config.php';
require_login();

$user = current_user();
$orderId = (int) ($_POST['order_id'] ?? 0);
$pdo = getDB();

try {
    $pdo->beginTransaction();

    $orderStmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = "Chờ xác nhận" LIMIT 1');
    $orderStmt->execute([$orderId, $user['id']]);
    $order = $orderStmt->fetch();

    if (!$order) {
        throw new RuntimeException('Không thể huỷ đơn hàng này.');
    }

    $itemsStmt = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id = ?');
    $itemsStmt->execute([$orderId]);
    $items = $itemsStmt->fetchAll();

    $updateProduct = $pdo->prepare('UPDATE products SET stock = stock + ?, sold = GREATEST(sold - ?, 0) WHERE id = ?');
    foreach ($items as $item) {
        $updateProduct->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
    }

    $cancelStmt = $pdo->prepare('UPDATE orders SET status = "Huỷ" WHERE id = ?');
    $cancelStmt->execute([$orderId]);

    $pdo->commit();
    set_flash('success', 'Đã huỷ đơn hàng #' . $orderId . '.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    set_flash('error', $e->getMessage());
}

redirect('orders.php');

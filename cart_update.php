<?php
require_once __DIR__ . '/config.php';
require_login();

$user = current_user();
$cartId = (int)($_POST['cart_id'] ?? 0);
$action = (string)($_POST['action'] ?? 'set');
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

$pdo = getDB();
$stmt = $pdo->prepare('
    SELECT c.id, c.quantity, p.stock
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ? AND c.id = ?
    LIMIT 1
');
$stmt->execute([$user['id'], $cartId]);
$item = $stmt->fetch();

if (!$item) {
    set_flash('error', 'Sản phẩm không còn trong giỏ hàng.');
    redirect('cart.php');
}

$currentQty = (int)$item['quantity'];
$stock = max(1, (int)$item['stock']);
$newQty = $quantity;

if ($action === 'increase') {
    $newQty = min($stock, $currentQty + 1);
} elseif ($action === 'decrease') {
    $newQty = max(1, $currentQty - 1);
}

$update = $pdo->prepare('UPDATE cart SET quantity = ? WHERE id = ?');
$update->execute([$newQty, $item['id']]);

redirect('cart.php');
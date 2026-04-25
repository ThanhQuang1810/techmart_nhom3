<?php
require_once __DIR__ . '/config.php';
require_login();

$user = current_user();
$cartId = (int)($_POST['cart_id'] ?? 0);

$stmt = getDB()->prepare('DELETE FROM cart WHERE user_id = ? AND id = ?');
$stmt->execute([$user['id'], $cartId]);

set_flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
redirect('cart.php');
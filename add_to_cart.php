<?php
require_once __DIR__ . '/config.php';

$productId      = (int)($_POST['product_id'] ?? 0);
$quantity       = max(1, (int)($_POST['quantity'] ?? 1));
$selectedColor  = trim((string)($_POST['selected_color'] ?? 'Mặc định'));
$selectedImage  = trim((string)($_POST['selected_image'] ?? ''));
$redirectTarget = trim((string)($_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? '')));

if (!is_logged_in()) {
    $backUrl = $redirectTarget !== ''
        ? $redirectTarget
        : url('product-detail.php?id=' . $productId);

    redirect(url('login.php') . '?redirect=' . urlencode($backUrl));
}

$user = current_user();
$pdo  = getDB();

$stmt = $pdo->prepare('SELECT id, stock, name, image FROM products WHERE id = ? AND status = 1 LIMIT 1');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Sản phẩm không tồn tại hoặc đã bị ẩn.');
    redirect(url('category.php'));
}

if ((int)$product['stock'] < 1) {
    set_flash('error', 'Sản phẩm hiện đã hết hàng.');
    redirect(url('product-detail.php?id=' . $productId));
}

if ($selectedColor === '') {
    $selectedColor = 'Mặc định';
}

if ($selectedImage === '') {
    $selectedImage = $product['image'];
}

$cartStmt = $pdo->prepare('
    SELECT id, quantity
    FROM cart
    WHERE user_id = ? AND product_id = ? AND selected_color = ?
    LIMIT 1
');
$cartStmt->execute([$user['id'], $productId, $selectedColor]);
$existing = $cartStmt->fetch();

if ($existing) {
    $newQty = min((int)$product['stock'], (int)$existing['quantity'] + $quantity);
    $pdo->prepare('UPDATE cart SET quantity = ?, selected_image = ? WHERE id = ?')
        ->execute([$newQty, $selectedImage, $existing['id']]);
} else {
    $pdo->prepare('
        INSERT INTO cart (user_id, product_id, selected_color, selected_image, quantity)
        VALUES (?, ?, ?, ?, ?)
    ')->execute([
        $user['id'],
        $productId,
        $selectedColor,
        $selectedImage,
        min($quantity, (int)$product['stock'])
    ]);
}

set_flash('success', 'Đã thêm "' . $product['name'] . ' - ' . $selectedColor . '" vào giỏ hàng!');

$safeRedirect = safe_redirect_target($redirectTarget);
redirect($safeRedirect !== '' ? $safeRedirect : url('cart.php'));
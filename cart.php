<?php
require_once __DIR__ . '/config.php';
require_login();

$title = 'TechMart - Giỏ hàng';
$extraCss = ['css/cart.css'];
$user = current_user();
$stmt = getDB()->prepare('
    SELECT c.id AS cart_id, c.quantity, c.selected_color, c.selected_image, p.*
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ?
    ORDER BY c.id DESC
');
$stmt->execute([$user['id']]);
$cartItems = $stmt->fetchAll();
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += (float) $item['price'] * (int) $item['quantity'];
}
require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="container cart-layout">
    <section class="cart-items card">
      <h1>Giỏ hàng</h1>
      <?php if (!$cartItems): ?>
        <div class="empty-state">
          Giỏ hàng của bạn đang trống.<br />
          <a href="<?= url('category.php') ?>" class="btn btn-primary" style="margin-top:24px; display:inline-block;">Tiếp tục mua sắm</a>
        </div>
      <?php else: ?>
        <?php foreach ($cartItems as $item): ?>
          <div class="cart-item">
           <img src="<?= url($item['selected_image'] ?: $item['image']) ?>" alt="<?= e($item['name']) ?>" />
              <p class="meta-text">Màu: <?= e($item['selected_color'] ?: 'Mặc định') ?></p>
            <div>
              <h3><a class="meta-link" href="<?= url('product-detail.php?id=' . (int) $item['id']) ?>"><?= e($item['name']) ?></a></h3>
              <p><?= format_price($item['price']) ?></p>
            </div>
            <div class="quantity-box">
             <form method="post" action="<?= url('cart_update.php') ?>">
  <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
  <input type="hidden" name="action" value="decrease">
  <button type="submit">-</button>
</form>
              <span><?= (int) $item['quantity'] ?></span>
              <form method="post" action="<?= url('cart_update.php') ?>">
  <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
  <input type="hidden" name="action" value="increase">
  <button type="submit">+</button>
</form>
            </div>
            <div>
              <p style="font-weight:700; margin-bottom:8px;"><?= format_price((float) $item['price'] * (int) $item['quantity']) ?></p>
             <form method="post" action="<?= url('cart_remove.php') ?>">
  <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
  <button class="remove-btn" type="submit">Xóa</button>
</form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <aside class="cart-summary card">
      <h2>Tóm tắt đơn hàng</h2>
      <div class="summary-row"><span>Tạm tính</span><strong><?= format_price($subtotal) ?></strong></div>
      <div class="summary-row total"><span>Tổng cộng</span><strong><?= format_price($subtotal) ?></strong></div>
      <?php if ($cartItems): ?>
        <a class="btn btn-primary checkout-btn"style="margin-top:24px" href="<?= url('checkout.php') ?>">Thanh toán</a>
         
        
      <?php else: ?>
        <button class="btn btn-primary checkout-btn" type="button" disabled style="opacity:0.5; cursor:not-allowed;">Thanh toán</button>
      <?php endif; ?>
    </aside>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

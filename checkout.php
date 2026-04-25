<?php
require_once __DIR__ . '/config.php';
require_login();

$user = current_user();
$pdo = getDB();
$cartStmt = $pdo->prepare('
    SELECT c.id AS cart_id, c.quantity, c.selected_color, c.selected_image, p.*
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ?
    ORDER BY c.id DESC
');
$cartStmt->execute([$user['id']]);
$cartItems = $cartStmt->fetchAll();

if (!$cartItems) {
    set_flash('error', 'Giỏ hàng đang trống, không thể thanh toán.');
    redirect('cart.php');
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += (float) $item['price'] * (int) $item['quantity'];
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $note = trim((string) ($_POST['note'] ?? ''));

    if ($fullName === '' || $email === '' || $phone === '' || $address === '') {
        $error = 'Vui lòng điền đầy đủ thông tin giao hàng.';
    } else {
        try {
            $pdo->beginTransaction();

            // check stock
            foreach ($cartItems as $item) {
                if ((int)$item['stock'] < (int)$item['quantity']) {
                    throw new RuntimeException('Sản phẩm "' . $item['name'] . '" không đủ tồn kho.');
                }
            }

            // ✅ 1. TẠO ORDER TRƯỚC
            $insertOrder = $pdo->prepare("
                INSERT INTO orders (user_id, full_name, email, phone, address, note, total, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Chờ xác nhận')
            ");
            $insertOrder->execute([
                $user['id'],
                $fullName,
                $email,
                $phone,
                $address,
                $note,
                $subtotal
            ]);

            // ✅ 2. LẤY ORDER_ID
            $orderId = (int)$pdo->lastInsertId();

            // ✅ 3. INSERT ORDER_ITEMS
            $insertItem = $pdo->prepare("
                INSERT INTO order_items 
                (order_id, product_id, product_name, selected_color, selected_image, quantity, price)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $updateProduct = $pdo->prepare("
                UPDATE products 
                SET stock = stock - ?, sold = sold + ? 
                WHERE id = ?
            ");

            foreach ($cartItems as $item) {
                $insertItem->execute([
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $item['selected_color'] ?: 'Mặc định',
                    $item['selected_image'] ?: $item['image'],
                    $item['quantity'],
                    $item['price']
                ]);

                $updateProduct->execute([
                    $item['quantity'],
                    $item['quantity'],
                    $item['id']
                ]);
            }

            // clear cart
            $clearCart = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
            $clearCart->execute([$user['id']]);

            $pdo->commit();

            set_flash('success', 'Đặt hàng thành công!');
            redirect(url('index.php'));

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = $e->getMessage();
        }
    }
}

$title = 'TechMart - Thanh toán';
$extraCss = [];
require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="container checkout-layout">
    <section class="checkout-form card">
      <h1 style="margin-bottom: 18px;">Thông tin giao hàng</h1>
      <?php if ($error !== ''): ?>
        <p class="form-error"><?= e($error) ?></p>
      <?php endif; ?>
      <form method="post">
        <div class="form-group">
          <label>Họ và tên</label>
          <input type="text" name="full_name" value="<?= e($_POST['full_name'] ?? $user['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= e($_POST['email'] ?? $user['email']) ?>" required>
        </div>
        <div class="form-group">
          <label>Số điện thoại</label>
          <input type="text" name="phone" value="<?= e($_POST['phone'] ?? ($user['phone'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
          <label>Địa chỉ</label>
          <input type="text" name="address" value="<?= e($_POST['address'] ?? ($user['address'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
          <label>Ghi chú</label>
          <input type="text" name="note" value="<?= e($_POST['note'] ?? '') ?>">
        </div>
        <button class="btn btn-primary" type="submit">Xác nhận đặt hàng</button>
      </form>
    </section>

    <aside class="checkout-summary card">
      <h2 style="margin-bottom: 18px;">Đơn hàng của bạn</h2>
      <?php foreach ($cartItems as $item): ?>
  <div class="summary-item">
    <div style="display:flex;gap:10px;align-items:center;">
      <img src="<?= url($item['selected_image'] ?: $item['image']) ?>"
           alt="<?= e($item['name']) ?>"
           style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
      <div>
        <strong><?= e($item['name']) ?></strong><br>
        <span>Màu: <?= e($item['selected_color'] ?: 'Mặc định') ?></span><br>
        <span>x<?= (int)$item['quantity'] ?></span>
      </div>
    </div>
    <strong><?= format_price((float)$item['price'] * (int)$item['quantity']) ?></strong>
  </div>
<?php endforeach; ?>
      <div class="summary-row total"><span>Tổng cộng</span><strong><?= format_price($subtotal) ?></strong></div>
    </aside>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

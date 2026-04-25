<?php
require_once __DIR__ . '/config.php';
require_login();

$user  = current_user();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim((string)($_POST['name'] ?? ''));
    $phone   = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));

    if ($name === '') {
        $error = 'Họ tên không được để trống.';
    } else {
        try {
            $avatar = handle_upload('avatar', $user['avatar'] ?: 'assets/images/avatardefault.jpg');

            getDB()->prepare('UPDATE users SET name = ?, phone = ?, address = ?, avatar = ? WHERE id = ?')
                ->execute([$name, $phone, $address, $avatar, $user['id']]);

            set_flash('success', 'Cập nhật thông tin thành công.');
            redirect('profile.php');
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$user = current_user();

$stmt = getDB()->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC');
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
$totalOrders = count($orders);

$itemStmt = getDB()->prepare('
    SELECT
        product_name,
        selected_color,
        selected_image,
        quantity,
        price
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
');

$statusLabel = [
    'Chờ xác nhận' => ['text' => 'Chờ xác nhận', 'color' => '#d97706'],
    'Đang xử lý'   => ['text' => 'Đang xử lý',   'color' => '#2563eb'],
    'Đang giao'    => ['text' => 'Đang giao',    'color' => '#0891b2'],
    'Đã giao'      => ['text' => 'Đã giao ✓',    'color' => '#16a34a'],
    'Huỷ'          => ['text' => 'Đã huỷ',       'color' => '#dc2626'],
];

$title    = 'TechMart - Tài khoản';
$extraCss = ['css/profile.css'];
require __DIR__ . '/includes/header.php';
?>

<main class="section">
  <div class="container" style="max-width:1000px;">

    <div class="profile-banner card">
      <div class="banner-avatar">
        <img src="<?= url(!empty($user['avatar']) ? $user['avatar'] : 'assets/images/avatardefault.jpg') ?>" alt="Avatar">
      </div>
      <div class="banner-text">
        <h1>Trang Cá Nhân</h1>
        <p>Quản lý tài khoản và theo dõi hoạt động của bạn</p>
      </div>
    </div>

    <div class="info-card card">
      <div class="info-card-header">
        <h2>Thông Tin Cá Nhân</h2>
        <p>Những thông tin cơ bản về tài khoản của bạn</p>
      </div>

      <?php if ($error !== ''): ?>
        <div class="alert-err"><?= e($error) ?></div>
      <?php endif; ?>

      <div id="viewMode">
        <table class="info-table">
          <tr>
            <th>Họ và tên:</th>
            <td><?= e($user['name']) ?></td>
          </tr>
          <tr>
            <th>Email:</th>
            <td><?= e($user['email']) ?></td>
          </tr>
          <tr>
            <th>Số điện thoại:</th>
            <td><?= e($user['phone'] ?: '—') ?></td>
          </tr>
          <tr>
<th>Địa chỉ:</th>
            <td><?= e($user['address'] ?: '—') ?></td>
          </tr>
          <tr>
            <th>Ngày tham gia:</th>
            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
          </tr>
          <tr>
            <th>Tổng đơn hàng:</th>
            <td><?= $totalOrders ?></td>
          </tr>
          <tr>
            <th>Trạng thái:</th>
            <td><span class="status-active">Đang hoạt động</span></td>
          </tr>
        </table>

        <div class="action-row">
          <button class="btn btn-primary" onclick="enableEdit()">
            <i class="fa-solid fa-pen"></i> Sửa thông tin
          </button>
          <button class="btn btn-outline" onclick="toggleOrders()" id="btnOrders">
            <i class="fa-solid fa-box"></i>
            Xem đơn hàng
            <?php if ($totalOrders > 0): ?>
              <span class="order-badge"><?= $totalOrders ?></span>
            <?php endif; ?>
          </button>
        </div>
      </div>

      <form id="editMode" method="POST" enctype="multipart/form-data" style="display:none;">
        <div class="form-group">
          <label>Ảnh đại diện hiện tại</label>
          <div style="margin-bottom:12px;">
            <img
              src="<?= url(!empty($user['avatar']) ? $user['avatar'] : 'assets/images/avatardefault.jpg') ?>"
              alt="Avatar"
              style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:1px solid #ddd;"
            >
          </div>
        </div>

        <div class="form-group">
          <label>Chọn ảnh đại diện mới</label>
          <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp,.avif">
          <small style="color:#666;">Hỗ trợ JPG, PNG, WEBP, AVIF</small>
        </div>

        <div class="form-group">
          <label>Họ và tên *</label>
          <input type="text" name="name" value="<?= e($user['name']) ?>" required>
        </div>

        <div class="form-group">
          <label>Email <span class="note">(không thể thay đổi)</span></label>
          <input type="email" value="<?= e($user['email']) ?>" disabled class="disabled-input">
        </div>

        <div class="form-group">
          <label>Số điện thoại</label>
          <input type="text" name="phone" value="<?= e($user['phone']) ?>" placeholder="Nhập số điện thoại">
        </div>

        <div class="form-group">
          <label>Địa chỉ</label>
          <input type="text" name="address" value="<?= e($user['address']) ?>" placeholder="Nhập địa chỉ">
        </div>

        <div class="action-row">
          <button class="btn btn-primary" type="submit">
            <i class="fa-solid fa-floppy-disk"></i> Lưu thông tin
          </button>
          <button class="btn btn-outline" type="button" onclick="cancelEdit()">Huỷ</button>
        </div>
      </form>
    </div>

    <div class="order-card card" id="orderSection" style="display:none;">
<div class="order-card-header">
        <h2>Đơn hàng của tôi</h2>
        <button class="btn-close-orders" onclick="toggleOrders()" title="Đóng">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <?php if (empty($orders)): ?>
        <div class="empty-orders">
          <i class="fa-solid fa-box-open"></i>
          <p>Chưa có đơn hàng nào.</p>
        </div>
      <?php else: ?>
        <table class="order-table">
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Ngày đặt</th>
              <th>Sản phẩm</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
              <th>Địa chỉ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order):
              $st = $statusLabel[$order['status']] ?? ['text' => $order['status'], 'color' => '#6b7280'];
              $itemStmt->execute([(int)$order['id']]);
              $orderItems = $itemStmt->fetchAll();
            ?>
              <tr>
                <td><strong>#<?= (int)$order['id'] ?></strong></td>

                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>

                <td class="profile-order-products">
                  <?php if ($orderItems): ?>
                    <?php foreach ($orderItems as $item): ?>
                      <div class="profile-order-item">
                        <div class="profile-order-item__image">
                          <img
                            src="<?= url($item['selected_image'] ?: 'assets/images/avatardefault.jpg') ?>"
                            alt="<?= e($item['product_name']) ?>"
                          >
                        </div>

                        <div class="profile-order-item__info">
                          <div class="profile-order-item__name">
                            <?= e($item['product_name']) ?>
                          </div>
                          <div class="profile-order-item__meta">
                            Màu: <strong><?= e($item['selected_color'] ?: 'Mặc định') ?></strong>
                          </div>
                          <div class="profile-order-item__meta">
                            Số lượng: x<?= (int)$item['quantity'] ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <span style="color:#94a3b8;">Không có sản phẩm</span>
                  <?php endif; ?>
                </td>

                <td><strong><?= format_price($order['total']) ?></strong></td>

                <td>
                  <span class="order-status-badge" style="color:<?= $st['color'] ?>;">
                    <?= e($st['text']) ?>
                  </span>
                </td>

                <td style="font-size:13px;color:var(--muted);"><?= e($order['address'] ?? '—') ?></td>
</tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>
</main>

<script>
function enableEdit() {
    document.getElementById('viewMode').style.display = 'none';
    document.getElementById('editMode').style.display = 'block';
    document.querySelector('#editMode input[name="name"]').focus();
}

function cancelEdit() {
    document.getElementById('editMode').style.display = 'none';
    document.getElementById('viewMode').style.display  = 'block';
}

let ordersVisible = false;
function toggleOrders() {
    ordersVisible = !ordersVisible;
    const section = document.getElementById('orderSection');
    const btn     = document.getElementById('btnOrders');
    section.style.display = ordersVisible ? 'block' : 'none';

    if (ordersVisible) {
        btn.innerHTML = '<i class="fa-solid fa-box"></i> Ẩn đơn hàng';
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        btn.innerHTML = `<i class="fa-solid fa-box"></i> Xem đơn hàng <?php if ($totalOrders > 0): ?><span class="order-badge"><?= $totalOrders ?></span><?php endif; ?>`;
    }
}

<?php if ($error !== ''): ?>
document.addEventListener('DOMContentLoaded', enableEdit);
<?php endif; ?>
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../config.php';
require_admin();

$pdo          = getDB();
$currentAdmin = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string)($_POST['action'] ?? 'update'));
    $userId = (int)($_POST['user_id'] ?? 0);

    if ($userId <= 0) {
        set_flash('error', 'Người dùng không hợp lệ.');
        redirect('admin/users.php');
    }

    $userStmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $userStmt->execute([$userId]);
    $targetUser = $userStmt->fetch();

    if (!$targetUser) {
        set_flash('error', 'Không tìm thấy người dùng.');
        redirect('admin/users.php');
    }

    if ($action === 'delete') {
        if (($targetUser['role'] ?? 'user') === 'admin') {
            set_flash('error', 'Không thể xóa tài khoản admin.');
            redirect('admin/users.php');
        }

        if ($userId === (int)$currentAdmin['id']) {
            set_flash('error', 'Bạn không thể tự xóa tài khoản đang đăng nhập.');
            redirect('admin/users.php');
        }

        try {
            $pdo->beginTransaction();

            $deleteOrders = $pdo->prepare('DELETE FROM orders WHERE user_id = ?');
            $deleteOrders->execute([$userId]);

            $deleteCart = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
            $deleteCart->execute([$userId]);

            $deleteUser = $pdo->prepare('DELETE FROM users WHERE id = ? AND role != "admin"');
            $deleteUser->execute([$userId]);

            $pdo->commit();

            set_flash('success', 'Đã xóa người dùng thành công.');
            redirect('admin/users.php');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            set_flash('error', 'Xóa người dùng thất bại: ' . $e->getMessage());
            redirect('admin/users.php');
        }
    }

    $role   = trim((string)($_POST['role'] ?? 'user'));
    $status = isset($_POST['status']) ? 1 : 0;

    if ($userId === (int)$currentAdmin['id'] && $status === 0) {
        set_flash('error', 'Bạn không thể tự khoá tài khoản admin đang đăng nhập.');
        redirect('admin/users.php');
    }

    $stmt = $pdo->prepare('UPDATE users SET role = ?, status = ? WHERE id = ?');
    $stmt->execute([$role === 'admin' ? 'admin' : 'user', $status, $userId]);

    set_flash('success', 'Đã cập nhật trạng thái người dùng.');
    redirect('admin/users.php');
}

$users = $pdo->query('
    SELECT u.*,
           (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS total_orders
    FROM users u
    WHERE u.role != "admin"
    ORDER BY u.id DESC
')->fetchAll();

$adminTitle = 'Admin - Người dùng';
require __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-card admin-table">
  <h1>Quản lý người dùng</h1>

  <div class="table-responsive">
    <table class="site-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Điện thoại</th>
          <th>Đơn hàng</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= (int)$user['id'] ?></td>
            <td><?= e($user['name']) ?></td>
            <td><?= e($user['email']) ?></td>
            <td><?= e($user['phone']) ?></td>
            <td><?= (int)$user['total_orders'] ?></td>

            <td>
              <?php if ((int)$user['status'] === 1): ?>
                <span style="color:#16a34a; font-weight:600;">✔ Hoạt động</span>
              <?php else: ?>
                <span style="color:#dc2626; font-weight:600;">✖ Đã khóa</span>
              <?php endif; ?>
            </td>

            <td>
              <div style="display:flex; align-items:center; gap:10px; flex-wrap:nowrap;">
                <form method="POST" style="display:flex; align-items:center; gap:10px; margin:0;">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                  <input type="hidden" name="role" value="<?= e($user['role']) ?>">

                  <input
                    type="checkbox"
                    name="status"
                    value="1"
                    <?= (int)$user['status'] === 1 ? 'checked' : '' ?>
                    title="Bật/tắt trạng thái"
                  >

                  <button class="btn btn-outline" type="submit">Lưu</button>
                </form>

                <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');" style="margin:0;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">

                  <button
                    type="submit"
                    style="background:#dc2626;color:#fff;border:none;padding:10px 16px;border-radius:10px;cursor:pointer;"
                  >
                    Xóa
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (!$users): ?>
          <tr>
            <td colspan="7" style="text-align:center;">Chưa có người dùng nào.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
<?php
require_once __DIR__ . '/../config.php';
require_admin();

$pdo = getDB();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            if ($name === '') {
                throw new RuntimeException('Tên danh mục không được để trống.');
            }
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
                $stmt->execute([$name, $description, $id]);
                set_flash('success', 'Cập nhật danh mục thành công.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
                $stmt->execute([$name, $description]);
                set_flash('success', 'Thêm danh mục thành công.');
            }
            redirect('admin/categories.php');
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
            $countStmt->execute([$id]);
            if ((int) $countStmt->fetchColumn() > 0) {
                throw new RuntimeException('Không thể xóa danh mục đang có sản phẩm.');
            }
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$id]);
            set_flash('success', 'Đã xóa danh mục.');
            redirect('admin/categories.php');
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$editId = (int) ($_GET['edit'] ?? 0);
$editing = ['id' => 0, 'name' => '', 'description' => ''];
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch() ?: $editing;
}

$categories = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM categories c ORDER BY c.id DESC')->fetchAll();
$adminTitle = 'Admin - Danh mục';
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="admin-card">
  <h1><?= (int) $editing['id'] > 0 ? 'Cập nhật danh mục' : 'Thêm danh mục' ?></h1>
  <?php if ($error !== ''): ?>
    <p class="form-error"><?= e($error) ?></p>
  <?php endif; ?>
  <form method="post" class="admin-form-grid" style="margin-top:16px;">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
    <div>
      <label>Tên danh mục</label>
      <input type="text" name="name" value="<?= e($editing['name']) ?>" required>
    </div>
    <div>
      <label>Mô tả</label>
      <input type="text" name="description" value="<?= e($editing['description']) ?>">
    </div>
    <div class="full inline-actions">
      <button class="btn btn-primary" type="submit">Lưu danh mục</button>
      <a class="btn btn-outline" href="<?= url('admin/categories.php') ?>">Làm mới</a>
    </div>
  </form>
</div>

<div class="admin-card admin-table">
  <h2>Danh sách danh mục</h2>
  <div class="table-responsive">
    <table class="site-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên</th>
          <th>Mô tả</th>
          <th>Số sản phẩm</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $category): ?>
          <tr>
            <td><?= (int) $category['id'] ?></td>
            <td><?= e($category['name']) ?></td>
            <td><?= e($category['description']) ?></td>
            <td><?= (int) $category['product_count'] ?></td>
            <td>
              <div class="inline-actions">
                <a class="btn btn-outline" href="<?= url('admin/categories.php?edit=' . (int) $category['id']) ?>">Sửa</a>
                <form method="post" class="inline-form">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                  <button class="btn btn-outline" type="submit" data-confirm="Bạn có chắc muốn xóa danh mục này?">Xóa</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>

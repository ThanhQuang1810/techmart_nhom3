<?php
require_once __DIR__ . '/../config.php';
require_admin();

$pdo   = getDB();
$error = '';

$defaultProduct = [
    'id'=>0,'category_id'=>'','name'=>'','brand'=>'',
    'price'=>'','old_price'=>'','stock'=>'0','image'=>'',
    'screen'=>'','chip'=>'','ram'=>'','storage'=>'',
    'camera'=>'','battery'=>'','description'=>'',
    'rating'=>'4.5','sold'=>'0','featured'=>'0','status'=>'1',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    try {
        if ($action === 'save') {
            $id            = (int)($_POST['id'] ?? 0);
            $existingImage = trim((string)($_POST['existing_image'] ?? ''));
            $imagePathText = trim((string)($_POST['image_path'] ?? ''));
            $image         = $existingImage;
            if ($imagePathText !== '') $image = $imagePathText;
            if (isset($_FILES['image_file']) && ($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $image = handle_upload('image_file', $image);
            }
            $data = [
                'category_id' => (int)($_POST['category_id'] ?? 0),
                'name'        => trim((string)($_POST['name'] ?? '')),
                'brand'       => trim((string)($_POST['brand'] ?? '')),
                'price'       => (float)($_POST['price'] ?? 0),
                'old_price'   => (float)($_POST['old_price'] ?? 0),
                'stock'       => (int)($_POST['stock'] ?? 0),
                'image'       => $image,
                'screen'      => trim((string)($_POST['screen'] ?? '')),
                'chip'        => trim((string)($_POST['chip'] ?? '')),
                'ram'         => trim((string)($_POST['ram'] ?? '')),
                'storage'     => trim((string)($_POST['storage'] ?? '')),
                'camera'      => trim((string)($_POST['camera'] ?? '')),
                'battery'     => trim((string)($_POST['battery'] ?? '')),
                'description' => trim((string)($_POST['description'] ?? '')),
                'rating'      => (float)($_POST['rating'] ?? 4.5),
                'sold'        => (int)($_POST['sold'] ?? 0),
                'featured'    => isset($_POST['featured']) ? 1 : 0,
                'status'      => isset($_POST['status']) ? 1 : 0,
            ];
            if ($data['category_id'] <= 0 || $data['name'] === '' || $data['price'] <= 0 || $data['image'] === '') {
                throw new RuntimeException('Vui lòng nhập đầy đủ danh mục, tên, giá và ảnh sản phẩm.');
            }
            if ($id > 0) {
                $pdo->prepare('UPDATE products SET category_id=?,name=?,brand=?,price=?,old_price=?,stock=?,image=?,screen=?,chip=?,ram=?,storage=?,camera=?,battery=?,description=?,rating=?,sold=?,featured=?,status=? WHERE id=?')
                    ->execute([$data['category_id'],$data['name'],$data['brand'],$data['price'],$data['old_price'],$data['stock'],$data['image'],$data['screen'],$data['chip'],$data['ram'],$data['storage'],$data['camera'],$data['battery'],$data['description'],$data['rating'],$data['sold'],$data['featured'],$data['status'],$id]);
                $imgMain = $pdo->prepare('SELECT id FROM product_images WHERE product_id=? AND is_main=1 LIMIT 1');
                $imgMain->execute([$id]);
                $mainRow = $imgMain->fetch();
                if ($mainRow) {
                    $pdo->prepare('UPDATE product_images SET image_url=? WHERE id=?')->execute([$data['image'], $mainRow['id']]);
                } else {
                    $pdo->prepare('INSERT INTO product_images (product_id,color,image_url,is_main) VALUES (?,?,?,1)')->execute([$id,'Mặc định',$data['image']]);
                }
                set_flash('success','Cập nhật sản phẩm thành công.');
            } else {
                $pdo->prepare('INSERT INTO products (category_id,name,brand,description,price,old_price,stock,image,screen,chip,ram,storage,camera,battery,rating,sold,featured,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
                    ->execute([$data['category_id'],$data['name'],$data['brand'],$data['description'],$data['price'],$data['old_price'],$data['stock'],$data['image'],$data['screen'],$data['chip'],$data['ram'],$data['storage'],$data['camera'],$data['battery'],$data['rating'],$data['sold'],$data['featured'],$data['status']]);
                $newId = (int)$pdo->lastInsertId();
                $pdo->prepare('INSERT INTO product_images (product_id,color,image_url,is_main) VALUES (?,?,?,1)')->execute([$newId,'Mặc định',$data['image']]);
                set_flash('success','Thêm sản phẩm thành công.');
                $id = $newId;
            }
            $colorNames = $_POST['color_names'] ?? [];
            $colorPaths = $_POST['color_paths'] ?? [];
            $colorFiles = $_FILES['color_files'] ?? [];
            foreach ($colorNames as $i => $colorName) {
                $colorName = trim((string)$colorName);
                if ($colorName === '') continue;
                $colorImg = trim((string)($colorPaths[$i] ?? ''));
                if (!empty($colorFiles['name'][$i]) && ($colorFiles['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $_FILES['_color_tmp'] = ['name'=>$colorFiles['name'][$i],'type'=>$colorFiles['type'][$i],'tmp_name'=>$colorFiles['tmp_name'][$i],'error'=>$colorFiles['error'][$i],'size'=>$colorFiles['size'][$i]];
                    $colorImg = handle_upload('_color_tmp', $colorImg);
                }
                if ($colorImg === '') continue;
                $check = $pdo->prepare('SELECT id FROM product_images WHERE product_id=? AND color=? LIMIT 1');
                $check->execute([$id, $colorName]);
                $existing = $check->fetch();
                if ($existing) {
                    $pdo->prepare('UPDATE product_images SET image_url=? WHERE id=?')->execute([$colorImg, $existing['id']]);
                } else {
                    $pdo->prepare('INSERT INTO product_images (product_id,color,image_url,is_main) VALUES (?,?,?,0)')->execute([$id,$colorName,$colorImg]);
                }
            }
            redirect('admin/products.php');
        }
        if ($action === 'delete_color_image') {
            $imgId = (int)($_POST['img_id'] ?? 0);
            $check = $pdo->prepare('SELECT is_main FROM product_images WHERE id=?');
            $check->execute([$imgId]);
            $row = $check->fetch();
            if ($row && !$row['is_main']) {
                $pdo->prepare('DELETE FROM product_images WHERE id=?')->execute([$imgId]);
                set_flash('success','Đã xoá ảnh màu.');
            }
            redirect('admin/products.php?edit=' . (int)($_POST['product_id'] ?? 0));
        }
        if ($action === 'hide') {
            $pdo->prepare('UPDATE products SET status=0 WHERE id=?')->execute([(int)($_POST['id'] ?? 0)]);
            set_flash('success','Đã ẩn sản phẩm.');
            redirect('admin/products.php');
        }
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM product_images WHERE product_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
            set_flash('success','Đã xoá sản phẩm.');
            redirect('admin/products.php');
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$editId  = (int)($_GET['edit'] ?? 0);
$editing = $defaultProduct;
$editingImages = [];
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id=? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch() ?: $defaultProduct;
    $imgStmt = $pdo->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY is_main DESC, id ASC');
    $imgStmt->execute([$editId]);
    $editingImages = $imgStmt->fetchAll();
}

$categories = fetch_categories();
$products   = $pdo->query('SELECT p.*,c.name AS category_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
$adminTitle = 'Admin - Sản phẩm';
require __DIR__ . '/../includes/admin_header.php';
?>

<style>
.modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.45); backdrop-filter:blur(4px);
    z-index:3000; align-items:flex-start; justify-content:center;
    padding:24px 16px; overflow-y:auto;
}
.modal-overlay.open { display:flex; }
.modal-box {
    background:#fff; border-radius:18px; padding:28px;
    width:100%; max-width:860px;
    box-shadow:0 24px 60px rgba(0,0,0,0.2);
    position:relative; animation:modalIn 0.25s ease; margin:auto;
}
@keyframes modalIn {
    from { opacity:0; transform:translateY(16px) scale(0.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
.modal-close {
    position:absolute; top:12px; right:12px;
    background:none; border:none; font-size:20px;
    cursor:pointer; color:#6b7280; width:34px; height:34px;
    display:flex; align-items:center; justify-content:center;
    border-radius:8px; transition:background 0.2s;
}
.modal-close:hover { background:#f5f5f5; }
.modal-box h2 { font-size:20px; font-weight:800; margin-bottom:16px; }
.color-images-section { border:1.5px dashed #d1d5db; border-radius:12px; padding:18px; margin-top:4px; }
.color-images-section h4 { font-size:14px; font-weight:700; margin-bottom:14px; color:#374151; }
.color-image-row { display:grid; grid-template-columns:1fr 1.5fr auto; gap:10px; align-items:center; margin-bottom:10px; padding:10px; background:#f9fafb; border-radius:10px; }
.btn-remove-color { background:none; border:1.5px solid #dc2626; color:#dc2626; border-radius:8px; padding:6px 10px; cursor:pointer; font-size:13px; font-weight:600; white-space:nowrap; }
.btn-remove-color:hover { background:#fee2e2; }
.btn-add-color { display:inline-flex; align-items:center; gap:6px; background:none; border:1.5px dashed #1a94ff; color:#1a94ff; border-radius:10px; padding:8px 16px; cursor:pointer; font-size:13px; font-weight:600; margin-top:8px; }
.btn-add-color:hover { background:#eff6ff; }
.existing-color-list { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:14px; }
.existing-color-item { position:relative; text-align:center; }
.existing-color-item img { width:64px; height:64px; object-fit:cover; border-radius:10px; border:2px solid #e5e7eb; display:block; }
.existing-color-item .color-name { font-size:11px; color:#6b7280; margin-top:4px; max-width:70px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.existing-color-item .main-badge { position:absolute; top:-6px; left:-6px; background:#1a94ff; color:#fff; font-size:9px; border-radius:999px; padding:2px 6px; font-weight:700; }
.btn-del-img { position:absolute; top:-6px; right:-6px; background:#dc2626; color:#fff; border:none; width:20px; height:20px; border-radius:50%; font-size:11px; cursor:pointer; display:flex; align-items:center; justify-content:center; padding:0; }

/* Toolbar tìm kiếm & sắp xếp */
.product-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:12px; margin-bottom:16px;
}
.product-toolbar h2 { font-size:18px; font-weight:800; margin:0; }
.toolbar-controls { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.toolbar-search {
    display:flex; align-items:center; gap:8px;
    border:1.5px solid #e2e8f0; border-radius:10px;
    padding:8px 14px; background:#fff;
    transition:border-color 0.2s;
}
.toolbar-search:focus-within { border-color:#3b82f6; }
.toolbar-search i { color:#94a3b8; font-size:14px; }
.toolbar-search input {
    border:none; outline:none; font-size:14px;
    background:transparent; width:180px; color:#374151;
}
.toolbar-sort {
    border:1.5px solid #e2e8f0; border-radius:10px;
    padding:8px 14px; font-size:14px; background:#fff;
    cursor:pointer; color:#374151; outline:none;
    transition:border-color 0.2s;
}
.toolbar-sort:focus { border-color:#3b82f6; }
.result-count { font-size:13px; color:#94a3b8; }
</style>

<!-- Header -->
<div class="admin-card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;">Quản lý sản phẩm</h1>
        <p class="small-note"><?= count($products) ?> sản phẩm trong hệ thống</p>
    </div>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="fa-solid fa-plus"></i> Thêm sản phẩm
    </button>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="productModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal()" type="button"><i class="fa-solid fa-xmark"></i></button>
    <h2 id="modalTitle">Thêm sản phẩm mới</h2>
    <?php if ($error !== ''): ?>
      <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:14px;"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="admin-form-grid">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <input type="hidden" name="existing_image" value="<?= e($editing['image']) ?>">
      <div><label>Danh mục *</label><select name="category_id" required><option value="">-- Chọn --</option><?php foreach ($categories as $cat): ?><option value="<?= (int)$cat['id'] ?>" <?= (int)$editing['category_id']===(int)$cat['id']?'selected':'' ?>><?= e($cat['name']) ?></option><?php endforeach; ?></select></div>
      <div><label>Tên sản phẩm *</label><input type="text" name="name" value="<?= e($editing['name']) ?>" required></div>
      <div><label>Thương hiệu</label><input type="text" name="brand" value="<?= e($editing['brand']) ?>"></div>
      <div><label>Giá bán *</label><input type="number" name="price" value="<?= e((string)$editing['price']) ?>" min="0" required></div>
      <div><label>Giá gốc</label><input type="number" name="old_price" value="<?= e((string)$editing['old_price']) ?>" min="0"></div>
      <div><label>Tồn kho</label><input type="number" name="stock" value="<?= e((string)$editing['stock']) ?>" min="0"></div>
      <div class="full"><label>Ảnh chính — đường dẫn</label><input type="text" name="image_path" value="<?= e($editing['image']) ?>" placeholder="assets/images/ten-anh.jpg"></div>
      <div class="full"><label>Hoặc tải ảnh chính mới lên</label><input type="file" name="image_file" accept="image/*"></div>
      <div><label>Màn hình</label><input type="text" name="screen" value="<?= e($editing['screen']) ?>" placeholder="Bỏ trống nếu không có"></div>
      <div><label>Chip</label><input type="text" name="chip" value="<?= e($editing['chip']) ?>" placeholder="Bỏ trống nếu không có"></div>
      <div><label>RAM</label><input type="text" name="ram" value="<?= e($editing['ram']) ?>" placeholder="Bỏ trống nếu không có"></div>
      <div><label>Bộ nhớ</label><input type="text" name="storage" value="<?= e($editing['storage']) ?>" placeholder="Bỏ trống nếu không có"></div>
      <div><label>Camera</label><input type="text" name="camera" value="<?= e($editing['camera']) ?>" placeholder="Bỏ trống nếu không có"></div>
      <div><label>Pin</label><input type="text" name="battery" value="<?= e($editing['battery']) ?>" placeholder="Bỏ trống nếu không có"></div>
      <div><label>Đánh giá</label><input type="number" step="0.1" name="rating" value="<?= e((string)$editing['rating']) ?>" min="0" max="5"></div>
      <div><label>Đã bán</label><input type="number" name="sold" value="<?= e((string)$editing['sold']) ?>" min="0"></div>
      <div class="full"><label>Mô tả</label><textarea name="description" rows="3"><?= e($editing['description']) ?></textarea></div>
      <div class="full">
        <div class="color-images-section">
          <h4><i class="fa-solid fa-palette" style="color:#1a94ff;margin-right:6px;"></i>Ảnh theo màu sắc</h4>
          <?php if (!empty($editingImages)): ?>
            <p style="font-size:13px;color:#6b7280;margin-bottom:10px;">Ảnh hiện có:</p>
            <div class="existing-color-list">
              <?php foreach ($editingImages as $eImg): ?>
                <div class="existing-color-item">
                  <?php if ($eImg['is_main']): ?><span class="main-badge">Chính</span><?php else: ?>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="action" value="delete_color_image">
                      <input type="hidden" name="img_id" value="<?= (int)$eImg['id'] ?>">
                      <input type="hidden" name="product_id" value="<?= (int)$editing['id'] ?>">
                      <button class="btn-del-img" type="submit" onclick="return confirm('Xoá ảnh này?')">✕</button>
                    </form>
                  <?php endif; ?>
                  <img src="<?= url($eImg['image_url']) ?>" alt="<?= e($eImg['color']) ?>">
                  <div class="color-name"><?= e($eImg['color']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
            <p style="font-size:13px;color:#6b7280;margin-bottom:10px;">Thêm màu mới:</p>
          <?php else: ?>
            <p style="font-size:13px;color:#6b7280;margin-bottom:10px;">Thêm các màu sắc (ảnh chính đã tự động thêm):</p>
          <?php endif; ?>
          <div id="colorRows"></div>
          <button type="button" class="btn-add-color" onclick="addColorRow()"><i class="fa-solid fa-plus"></i> Thêm màu</button>
        </div>
      </div>
      <div><label><input type="checkbox" name="featured" value="1" <?= (int)$editing['featured']===1?'checked':'' ?>> Sản phẩm nổi bật</label></div>
      <div><label><input type="checkbox" name="status" value="1" <?= (int)$editing['status']===1?'checked':'' ?>> Hiển thị sản phẩm</label></div>
      <div class="full inline-actions">
        <button class="btn btn-primary" type="submit">Lưu sản phẩm</button>
        <button class="btn btn-outline" type="button" onclick="closeModal()">Huỷ</button>
      </div>
    </form>
  </div>
</div>

<!-- BẢNG DANH SÁCH -->
<div class="admin-card admin-table">

  <!-- Toolbar tìm kiếm & sắp xếp -->
  <div class="product-toolbar">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
      <h2>Danh sách sản phẩm</h2>
      <span class="result-count" id="resultCount"><?= count($products) ?> sản phẩm</span>
    </div>
    <div class="toolbar-controls">
      <!-- Tìm kiếm -->
      <div class="toolbar-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput"
               placeholder="Tìm tên sản phẩm..."
               oninput="filterAndSort()">
      </div>
      <!-- Sắp xếp -->
      <select class="toolbar-sort" id="sortSelect" onchange="filterAndSort()">
        <option value="">Sắp xếp...</option>
        <option value="name-asc">Tên A → Z</option>
        <option value="name-desc">Tên Z → A</option>
        <option value="price-asc">Giá tăng dần</option>
        <option value="price-desc">Giá giảm dần</option>
        <option value="stock-asc">Tồn kho tăng</option>
        <option value="stock-desc">Tồn kho giảm</option>
        <option value="status">Trạng thái</option>
      </select>
    </div>
  </div>

  <div class="table-responsive">
    <table class="site-table">
      <thead>
        <tr>
          <th>Ảnh</th><th>Tên sản phẩm</th><th>Danh mục</th>
          <th>Giá</th><th>Tồn kho</th><th>Trạng thái</th><th>Thao tác</th>
        </tr>
      </thead>
      <tbody id="productTableBody">
        <?php foreach ($products as $p): ?>
          <tr data-name="<?= strtolower(e($p['name'])) ?>"
              data-price="<?= (float)$p['price'] ?>"
              data-stock="<?= (int)$p['stock'] ?>"
              data-status="<?= (int)$p['status'] ?>">
            <td><img src="<?= url($p['image']) ?>" alt="<?= e($p['name']) ?>"
                     style="width:52px;height:52px;object-fit:cover;border-radius:8px;"></td>
            <td><?= e($p['name']) ?><br><span class="small-note"><?= e($p['brand']) ?></span></td>
            <td><?= e($p['category_name']) ?></td>
            <td><?= format_price($p['price']) ?></td>
            <td><?= (int)$p['stock'] ?></td>
            <td>
              <?php if ((int)$p['status']===1): ?>
                <span style="color:#16a34a;font-weight:600;">Hiển thị</span>
              <?php else: ?>
                <span style="color:#dc2626;font-weight:600;">Đã ẩn</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="inline-actions">
             <button class="btn btn-outline" type="button"
        onclick="editProduct(<?= htmlspecialchars(json_encode($p),ENT_QUOTES) ?>)">Sửa</button>
                <?php if ((int)$p['status']===1): ?>
                  <form method="POST" class="inline-form">
                    <input type="hidden" name="action" value="hide">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <button class="btn btn-outline" type="submit"
                            onclick="return confirm('Ẩn sản phẩm này?')">Ẩn</button>
                  </form>
                <?php endif; ?>
                <form method="POST" class="inline-form">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <button class="btn btn-outline" type="submit"
                          style="color:#dc2626;border-color:#dc2626;"
                          onclick="return confirm('Xoá vĩnh viễn? Không thể khôi phục!')">Xoá</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- Không tìm thấy -->
    <div id="noResult" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:15px;">
      <i class="fa-solid fa-box-open" style="font-size:36px;margin-bottom:12px;display:block;"></i>
      Không tìm thấy sản phẩm nào.
    </div>
  </div>
</div>

<script>
function filterAndSort() {
    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    const sortVal = document.getElementById('sortSelect').value;
    const tbody   = document.getElementById('productTableBody');
    let rows = Array.from(tbody.querySelectorAll('tr'));

    // Filter
    rows.forEach(row => {
        const name = row.dataset.name || '';
        row.style.display = name.includes(keyword) ? '' : 'none';
    });

    // Sort
    const visible = rows.filter(r => r.style.display !== 'none');
    if (sortVal) {
        visible.sort((a, b) => {
            if (sortVal === 'name-asc')    return a.dataset.name.localeCompare(b.dataset.name, 'vi');
            if (sortVal === 'name-desc')   return b.dataset.name.localeCompare(a.dataset.name, 'vi');
            if (sortVal === 'price-asc')   return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            if (sortVal === 'price-desc')  return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
            if (sortVal === 'stock-asc')   return parseInt(a.dataset.stock) - parseInt(b.dataset.stock);
            if (sortVal === 'stock-desc')  return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
            if (sortVal === 'status')      return parseInt(b.dataset.status) - parseInt(a.dataset.status);
            return 0;
        });
        visible.forEach(row => tbody.appendChild(row));
    }

    // Đếm kết quả
    const count = visible.length;
    document.getElementById('resultCount').textContent = count + ' sản phẩm';
    document.getElementById('noResult').style.display = count === 0 ? 'block' : 'none';
}

// Modal
let colorCount = 0;
function addColorRow() {
    const i = colorCount++;
    const div = document.createElement('div');
    div.className = 'color-image-row'; div.id = 'colorRow_' + i;
    div.innerHTML = `
        <input type="text" name="color_names[]" placeholder="Tên màu (VD: Đen, Trắng...)" style="width:100%;">
        <div>
            <input type="text" name="color_paths[]" placeholder="assets/images/..." style="width:100%;margin-bottom:6px;">
            <input type="file" name="color_files[]" accept="image/*" style="width:100%;">
        </div>
        <button type="button" class="btn-remove-color" onclick="removeColorRow('colorRow_${i}')">
            <i class="fa-solid fa-xmark"></i> Xoá
        </button>`;
    document.getElementById('colorRows').appendChild(div);
}
function removeColorRow(id) { const el = document.getElementById(id); if (el) el.remove(); }

function openModal() {
    document.getElementById('modalTitle').textContent = 'Thêm sản phẩm mới';
    document.querySelector('#productModal form').reset();
    document.querySelector('[name="id"]').value = '0';
    document.querySelector('[name="existing_image"]').value = '';
    document.getElementById('colorRows').innerHTML = '';
    colorCount = 0;
    document.getElementById('productModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function editProduct(p) {
    document.getElementById('modalTitle').textContent = 'Cập nhật sản phẩm';
    const form = document.querySelector('#productModal form');
    form.querySelector('[name="id"]').value             = p.id;
    form.querySelector('[name="existing_image"]').value = p.image;
    form.querySelector('[name="category_id"]').value    = p.category_id;
    form.querySelector('[name="name"]').value           = p.name;
    form.querySelector('[name="brand"]').value          = p.brand || '';
    form.querySelector('[name="price"]').value          = p.price;
    form.querySelector('[name="old_price"]').value      = p.old_price || '';
    form.querySelector('[name="stock"]').value          = p.stock;
    form.querySelector('[name="image_path"]').value     = p.image;
    form.querySelector('[name="screen"]').value         = p.screen || '';
    form.querySelector('[name="chip"]').value           = p.chip || '';
    form.querySelector('[name="ram"]').value            = p.ram || '';
    form.querySelector('[name="storage"]').value        = p.storage || '';
    form.querySelector('[name="camera"]').value         = p.camera || '';
    form.querySelector('[name="battery"]').value        = p.battery || '';
    form.querySelector('[name="rating"]').value         = p.rating || '4.5';
    form.querySelector('[name="sold"]').value           = p.sold || '0';
    form.querySelector('[name="description"]').value    = p.description || '';
    form.querySelector('[name="featured"]').checked     = parseInt(p.featured) === 1;
    form.querySelector('[name="status"]').checked       = parseInt(p.status) === 1;
    document.getElementById('colorRows').innerHTML = '';
    colorCount = 0;
    document.getElementById('productModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('productModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('productModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
<?php if ($error !== '' || $editId > 0): ?>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($editId > 0): ?>editProduct(<?= json_encode($editing) ?>);
    <?php else: ?>openModal();<?php endif; ?>
});
<?php endif; ?>
</script>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
<?php
require_once __DIR__ . '/config.php';

$id   = (int)($_GET['id'] ?? 0);
$stmt = getDB()->prepare('
    SELECT p.*, c.name AS category_name
    FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE p.id = ? AND p.status = 1
    LIMIT 1
');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Không tìm thấy sản phẩm.');
    redirect('category.php');
}

$title    = 'TechMart - ' . $product['name'];
$extraCss = ['css/product-detail.css'];

// Lấy ảnh theo màu
$imgStmt = getDB()->prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC, id ASC');
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

if (!$images) {
    $images = [['image_url' => $product['image'], 'color' => 'Mặc định', 'is_main' => 1]];
}
$mainImage = $images[0];

// Sản phẩm liên quan
$relatedStmt = getDB()->prepare('SELECT * FROM products WHERE category_id = ? AND id <> ? AND status = 1 ORDER BY featured DESC, sold DESC LIMIT 4');
$relatedStmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $relatedStmt->fetchAll();

// Thông số — chỉ lấy trường CÓ dữ liệu thật, bỏ "Không áp dụng"
$specFields = [
    'screen'  => 'Màn hình',
    'chip'    => 'Chip / CPU',
    'ram'     => 'RAM',
    'storage' => 'Bộ nhớ',
    'camera'  => 'Camera',
    'battery' => 'Pin',
];
$specs = [];
foreach ($specFields as $field => $label) {
    $val = trim((string)($product[$field] ?? ''));
    $valLower = strtolower($val);
    // Bỏ qua nếu trống hoặc là "không áp dụng"
    if ($val !== '' && $valLower !== 'không áp dụng' && $valLower !== 'khong ap dung' && $val !== '-' && $val !== 'N/A') {
        $specs[$label] = $val;
    }
}

require __DIR__ . '/includes/header.php';
?>

<main class="section">
  <div class="container">
    <div class="detail-layout">

      <!-- Ảnh & màu sắc -->
      <div class="detail-gallery card">
        <img src="<?= url($mainImage['image_url']) ?>"
             alt="<?= e($product['name']) ?>"
             class="main-image" id="mainImage" />

        <?php if (count($images) >= 1): ?>
          <div class="product-color-box">
            <strong>Màu sắc:</strong>
            <span id="selectedColor"><?= e($mainImage['color'] ?: 'Mặc định') ?></span>
          </div>
          <div class="thumbnail-list" id="thumbnailList">
            <?php foreach ($images as $i => $img): ?>
  <div class="color-thumb <?= $i === 0 ? 'active' : '' ?>"
       data-image="<?= url($img['image_url']) ?>"
       data-image-path="<?= e($img['image_url']) ?>"
       data-color="<?= e($img['color'] ?: 'Mặc định') ?>">
    <img src="<?= url($img['image_url']) ?>"
         alt="<?= e($img['color'] ?: 'Mặc định') ?>" />
  </div>
<?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Thông tin -->
      <div>
        <div class="detail-info card">
          <h1><?= e($product['name']) ?></h1>
<p class="detail-meta">
            Thương hiệu: <strong><?= e($product['brand']) ?></strong>
            &nbsp;|&nbsp; Danh mục: <?= e($product['category_name']) ?>
          </p>
          <p class="detail-meta">
            <i class="fa-solid fa-star" style="color:#facc15"></i>
            <?= e($product['rating']) ?>
            &nbsp;•&nbsp; Đã bán <?= (int)$product['sold'] ?>
          </p>

          <div class="detail-price"><?= format_price($product['price']) ?></div>

          <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
            <div style="color:#9ca3af; text-decoration:line-through; font-size:15px; margin-top:-8px; margin-bottom:12px;">
              <?= format_price($product['old_price']) ?>
            </div>
          <?php endif; ?>

          <p><?= nl2br(e($product['description'])) ?></p>

          <p class="detail-meta" style="margin-top:12px;">
            <?php if ((int)$product['stock'] > 0): ?>
              <span style="color:#16a34a; font-weight:600;">✓ Còn hàng (<?= (int)$product['stock'] ?> sản phẩm)</span>
            <?php else: ?>
              <span style="color:#dc2626; font-weight:600;">✗ Hết hàng</span>
            <?php endif; ?>
          </p>

          <?php if ((int)$product['stock'] > 0): ?>
            <div class="detail-actions">
              <form method="POST" action="<?= url('add_to_cart.php') ?>">
  <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
  <input type="hidden" name="selected_color" value="<?= e($mainImage['color'] ?: 'Mặc định') ?>" class="selected-color-input">
  <input type="hidden" name="selected_image" value="<?= e($mainImage['image_url']) ?>" class="selected-image-input">
  <input type="hidden" name="redirect" value="<?= url('cart.php') ?>">
  <button class="btn btn-primary" type="submit">Chọn mua</button>
</form>
              <form method="POST" action="<?= url('add_to_cart.php') ?>">
  <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
  <input type="hidden" name="selected_color" value="<?= e($mainImage['color'] ?: 'Mặc định') ?>" class="selected-color-input">
  <input type="hidden" name="selected_image" value="<?= e($mainImage['image_url']) ?>" class="selected-image-input">
  <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? url('product-detail.php?id=' . $id)) ?>">
  <button class="btn btn-outline" type="submit">Thêm vào giỏ</button>
</form>
            </div>
          <?php endif; ?>
        </div>

        <!-- Thông số kỹ thuật — CHỈ HIỆN trường có dữ liệu -->
        <?php if (!empty($specs)): ?>
          <div class="detail-specs card" style="margin-top:20px;">
            <h2 class="section-title" style="margin-bottom:8px;">Thông số kỹ thuật</h2>
            <div class="spec-table">
              <?php foreach ($specs as $label => $value): ?>
                <div class="spec-row">
<div class="spec-label"><?= e($label) ?></div>
                  <div><?= e($value) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <?php if ($relatedProducts): ?>
    <section class="section">
      <div class="container">
        <h2 class="section-title">Sản phẩm liên quan</h2>
        <div class="product-grid">
          <?php foreach ($relatedProducts as $related): ?>
            <?= render_product_card($related) ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const thumbs = document.querySelectorAll('.color-thumb');
    const mainImg = document.getElementById('mainImage');
    const selectedColor = document.getElementById('selectedColor');
    const colorInputs = document.querySelectorAll('.selected-color-input');
    const imageInputs = document.querySelectorAll('.selected-image-input');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            thumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const color = this.dataset.color || 'Mặc định';
            const imageUrl = this.dataset.image || '';
            const imagePath = this.dataset.imagePath || '';

            if (mainImg) mainImg.src = imageUrl;
            if (selectedColor) selectedColor.textContent = color;

            colorInputs.forEach(function (input) {
                input.value = color;
            });

            imageInputs.forEach(function (input) {
                input.value = imagePath;
            });
        });
    });
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
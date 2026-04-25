<?php
require_once __DIR__ . '/config.php';

$title    = 'TechMart - Danh sách sản phẩm';
$extraCss = ['css/category.css'];
$pdo      = getDB();

$category = trim((string)($_GET['category'] ?? ''));
$search   = trim((string)($_GET['search']   ?? ''));
$price    = trim((string)($_GET['price']    ?? 'all'));
$sort     = trim((string)($_GET['sort']     ?? 'default'));
$brands   = $_GET['brand'] ?? [];
if (!is_array($brands)) $brands = [$brands];
$brands = array_values(array_filter(array_map('trim', $brands)));



$sql = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id';
$params = [];

if ($category !== '') { $sql .= ' AND c.name = ?'; $params[] = $category; }
if ($search !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)';
    $kw = '%' . $search . '%';
    $params[] = $kw; $params[] = $kw; $params[] = $kw;
}
if ($price === 'under10')    $sql .= ' AND p.price < 10000000';
elseif ($price === '10to20') $sql .= ' AND p.price BETWEEN 10000000 AND 20000000';
elseif ($price === 'over20') $sql .= ' AND p.price > 20000000';
if ($brands) {
    $ph = implode(',', array_fill(0, count($brands), '?'));
    $sql .= " AND p.brand IN ($ph)";
    array_push($params, ...$brands);
}

$orderBy = match ($sort) {
    'priceAsc'   => ' ORDER BY p.price ASC, p.id DESC',
    'priceDesc'  => ' ORDER BY p.price DESC, p.id DESC',
    'ratingDesc' => ' ORDER BY p.rating DESC, p.sold DESC',
    default      => ' ORDER BY p.featured DESC, p.id DESC',
};

$stmt = $pdo->prepare($sql . $orderBy);
$stmt->execute($params);
$products = $stmt->fetchAll();
$brandOptions = $pdo->query('SELECT DISTINCT brand FROM products WHERE brand <> "" ORDER BY brand')->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = $category !== '' ? $category : ($search !== '' ? 'Kết quả: ' . $search : 'Tất cả sản phẩm');
$resetUrl  = url('category.php' . ($category !== '' ? '?category=' . urlencode($category) : ''));

require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="container category-layout">

    <aside class="filter-sidebar card">
      <form action="<?= url('category.php') ?>" method="GET">
        <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?= e($category) ?>"><?php endif; ?>
        <?php if ($search !== ''): ?><input type="hidden" name="search" value="<?= e($search) ?>"><?php endif; ?>
        <input type="hidden" name="sort" value="<?= e($sort) ?>">

        <h2>Bộ lọc</h2>

        <div class="filter-group">
          <h3>Khoảng giá</h3>
          <label><input type="radio" name="price" value="all"     <?= $price==='all'     ?'checked':'' ?>> Tất cả</label>
          <label><input type="radio" name="price" value="under10" <?= $price==='under10' ?'checked':'' ?>> Dưới 10 triệu</label>
          <label><input type="radio" name="price" value="10to20"  <?= $price==='10to20'  ?'checked':'' ?>> 10 – 20 triệu</label>
          <label><input type="radio" name="price" value="over20"  <?= $price==='over20'  ?'checked':'' ?>> Trên 20 triệu</label>
        </div>

        <div class="filter-group">
          <h3>Thương hiệu</h3>
          <?php foreach ($brandOptions as $brand): ?>
            <label>
              <input type="checkbox" name="brand[]" value="<?= e($brand) ?>" <?= in_array($brand, $brands, true)?'checked':'' ?>>
              <?= e($brand) ?>
            </label>
          <?php endforeach; ?>
        </div>

        <!-- Nút sticky dưới sidebar -->
        <div class="filter-actions">
          <button class="btn btn-primary" type="submit">Áp dụng</button>
          <a href="<?= $resetUrl ?>" class="btn btn-outline">Đặt lại</a>
        </div>
      </form>
    </aside>

    <section class="category-content">
      <div class="category-toolbar card">
        <h1>
          <?= e($pageTitle) ?>
          <span style="font-size:14px;font-weight:400;color:var(--muted);margin-left:8px;">(<?= count($products) ?> sản phẩm)</span>
        </h1>
        <form action="<?= url('category.php') ?>" method="GET">
          <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?= e($category) ?>"><?php endif; ?>
          <?php if ($search !== ''): ?><input type="hidden" name="search" value="<?= e($search) ?>"><?php endif; ?>
          <input type="hidden" name="price" value="<?= e($price) ?>">
          <?php foreach ($brands as $b): ?><input type="hidden" name="brand[]" value="<?= e($b) ?>"><?php endforeach; ?>
          <select name="sort" onchange="this.form.submit()">
            <option value="default"    <?= $sort==='default'   ?'selected':'' ?>>Mặc định</option>
            <option value="priceAsc"   <?= $sort==='priceAsc'  ?'selected':'' ?>>Giá tăng dần</option>
            <option value="priceDesc"  <?= $sort==='priceDesc' ?'selected':'' ?>>Giá giảm dần</option>
            <option value="ratingDesc" <?= $sort==='ratingDesc'?'selected':'' ?>>Đánh giá cao</option>
          </select>
        </form>
      </div>

      <div class="product-grid">
        <?php if (!$products): ?>
          <div class="empty-state card">
            <i class="fa-solid fa-box-open" style="font-size:40px;margin-bottom:14px;display:block;color:#d1d5db;"></i>
            Không tìm thấy sản phẩm phù hợp.
            <br><a href="<?= $resetUrl ?>" style="color:var(--primary);margin-top:10px;display:inline-block;">Xem tất cả</a>
          </div>
        <?php else: ?>
          <?php foreach ($products as $product): ?>
  <article class="product-card">
    <div class="product-thumb-wrap">
      <a href="<?= url('product-detail.php?id=' . (int)$product['id']) ?>">
        <img
          src="<?= url($product['image']) ?>"
          alt="<?= e($product['name']) ?>"
          class="product-thumb"
        >
      </a>
    </div>

    <div class="product-info">
      <h3 class="product-name">
        <a href="<?= url('product-detail.php?id=' . (int)$product['id']) ?>">
          <?= e($product['name']) ?>
        </a>
      </h3>

      <p class="product-brand"><?= e($product['brand'] ?: 'TechMart') ?></p>

      <div class="price-row">
        <strong class="price-current"><?= format_price($product['price']) ?></strong>
        <?php if ((float)($product['old_price'] ?? 0) > (float)$product['price']): ?>
          <span class="price-old"><?= format_price($product['old_price']) ?></span>
        <?php endif; ?>
      </div>

      <div class="meta-row">
        <span><i class="fa-solid fa-star"></i> <?= e($product['rating'] ?? '5.0') ?></span>
        <span>Còn <?= e($product['stock'] ?? '0') ?></span>
      </div>

      <div class="card-actions">
        <form method="post" action="<?= url('add_to_cart.php') ?>" style="display:inline-flex; flex:1;">
          <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
          <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? url('cart.php')) ?>">
          <button class="btn btn-primary btn-add-cart" type="submit">Thêm vào giỏ</button>
        </form>

        <a class="btn btn-outline btn-view-detail" href="<?= url('product-detail.php?id=' . (int)$product['id']) ?>">
          Mua ngay
        </a>
      </div>
    </div>
  </article>
<?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

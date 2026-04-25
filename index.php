
<?php
 
require_once __DIR__ . '/config.php';
// Nếu là admin → về trang quản trị
$currentUser = current_user();
if ($currentUser && $currentUser['role'] === 'admin') {
    redirect(url('admin/index.php'));
}

$title = 'TechMart - Trang chủ';
$extraCss = ['css/home.css'];
$pdo = getDB();

$featuredProductsStmt = $pdo->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.status = 1 ORDER BY p.featured DESC, p.id DESC LIMIT 8');
$featuredProducts = $featuredProductsStmt->fetchAll();




$newProductsStmt = $pdo->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.status = 1 ORDER BY p.id DESC LIMIT 8');
$newProducts = $newProductsStmt->fetchAll();

$categories = $pdo->query('SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id AND p.status = 1 GROUP BY c.id ORDER BY c.name')->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="container">
    <section class="hero card" style="
    padding: 48px 40px;
    margin-bottom: 24px;
    background: linear-gradient(135deg, #eaf4ff 0%, #f0f7ff 50%, #e8f0fe 100%);
    position: relative;
    overflow: hidden;
">
  <!-- Hình trang trí nền -->
  <div style="position:absolute;top:-60px;right:-60px;width:300px;height:300px;
              background:radial-gradient(circle,rgba(26,148,255,0.12),transparent 70%);
              border-radius:50%;pointer-events:none;"></div>
  <div style="position:absolute;bottom:-40px;left:30%;width:200px;height:200px;
              background:radial-gradient(circle,rgba(91,95,248,0.08),transparent 70%);
              border-radius:50%;pointer-events:none;"></div>

  <div style="position:relative;z-index:1;max-width:620px;">
    <!-- Badge -->
    <div style="display:inline-flex;align-items:center;gap:8px;
                background:rgba(26,148,255,0.1);border:1px solid rgba(26,148,255,0.2);
                border-radius:999px;padding:6px 16px;margin-bottom:18px;">
      <span style="width:8px;height:8px;background:#1a94ff;border-radius:50%;
                   animation:pulse 2s infinite;display:inline-block;"></span>
      <span style="font-size:13px;font-weight:700;color:#1a94ff;">
        🛒 Mua sắm công nghệ #1 Việt Nam
      </span>
    </div>

    <h1 style="font-size:42px;font-weight:800;line-height:1.15;
               margin-bottom:14px;letter-spacing:-0.5px;">
      TechMart –<br>
      <span style="background:linear-gradient(135deg,#1a94ff,#5b5ff8);
                   -webkit-background-clip:text;-webkit-text-fill-color:transparent;">
        Công nghệ đỉnh cao,
      </span><br>
      giá cả hợp lý
    </h1>

    <p style="font-size:16px;color:#6b7280;margin-bottom:24px;line-height:1.7;">
      Hàng ngàn sản phẩm chính hãng — điện thoại, laptop, phụ kiện.<br>
      Giao hàng nhanh · Đổi trả dễ dàng · Bảo hành chính hãng.
    </p>

    <!-- Số liệu nhanh -->
    <div style="display:flex;gap:24px;margin-bottom:28px;flex-wrap:wrap;">
      <div style="text-align:center;">
        <div style="font-size:22px;font-weight:800;color:#1a94ff;">500+</div>
        <div style="font-size:12px;color:#9ca3af;">Sản phẩm</div>
      </div>
      <div style="width:1px;background:#e5e7eb;"></div>
      <div style="text-align:center;">
        <div style="font-size:22px;font-weight:800;color:#1a94ff;">10K+</div>
        <div style="font-size:12px;color:#9ca3af;">Khách hàng</div>
      </div>
      <div style="width:1px;background:#e5e7eb;"></div>
      <div style="text-align:center;">
        <div style="font-size:22px;font-weight:800;color:#1a94ff;">4.9★</div>
        <div style="font-size:12px;color:#9ca3af;">Đánh giá</div>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <a class="btn btn-primary" href="<?= url('category.php') ?>"
         style="padding:14px 28px;font-size:15px;">
        🛍️ Mua sắm ngay
      </a>
      <a class="btn btn-outline" href="<?= url('about.php') ?>"
         style="padding:14px 28px;font-size:15px;">
        Tìm hiểu thêm
      </a>
    </div>
  </div>
</section>

<style>
@keyframes pulse {
  0%,100% { opacity:1; transform:scale(1); }
  50%      { opacity:0.5; transform:scale(1.4); }
}
</style>

    <?php
// Icon và màu nền cho từng danh mục
$catStyles = [
    'Điện thoại' => ['icon' => 'fa-mobile-screen-button', 'bg' => '#dcfce7', 'color' => '#16a34a'],
    'Laptop'     => ['icon' => 'fa-laptop',               'bg' => '#fee2e2', 'color' => '#dc2626'],
    'Tablet'     => ['icon' => 'fa-tablet-screen-button', 'bg' => '#fef9c3', 'color' => '#ca8a04'],
    'Phụ kiện'   => ['icon' => 'fa-headphones',           'bg' => '#cffafe', 'color' => '#0891b2'],
];
$defaultStyle = ['icon' => 'fa-tag', 'bg' => '#f3f4f6', 'color' => '#6b7280'];
?>
<section class="section-block" style="margin-bottom:28px;">
  <h2 class="section-title">Danh mục nổi bật</h2>
  <div class="category-grid">
    <?php foreach (array_slice($categories, 0, 6) as $category):
        $style = $catStyles[$category['name']] ?? $defaultStyle;
    ?>
      <a class="cat-card" href="<?= url('category.php?category=' . urlencode($category['name'])) ?>"
         style="--cat-bg:<?= $style['bg'] ?>; --cat-color:<?= $style['color'] ?>;">
        <div class="cat-card-inner">
          <i class="fa-solid <?= $style['icon'] ?>" style="font-size:32px; color:<?= $style['color'] ?>;"></i>
          <span><?= e($category['name']) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

    <section class="section-block" style="margin-bottom:28px;">
      <h2 class="section-title">Sản phẩm nổi bật</h2>
      <div class="product-grid">
        <?php foreach ($featuredProducts as $product): ?>
          <?= render_product_card($product) ?>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section-block">
      <h2 class="section-title">Sản phẩm mới nhất</h2>
      <div class="product-grid">
        <?php foreach ($newProducts as $product): ?>
          <?= render_product_card($product) ?>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

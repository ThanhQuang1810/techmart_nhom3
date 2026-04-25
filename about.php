<?php
require_once __DIR__ . '/config.php';

$user  = current_user();
$error = '';

// Xử lý submit form báo cáo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nếu chưa đăng nhập → về login
    if (!$user) {
        redirect(url('login.php'));
    }

    $subject = trim((string)($_POST['subject'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));

    if ($subject === '' || $message === '') {
        $error = 'Vui lòng điền đầy đủ chủ đề và nội dung.';
    } else {
        $stmt = getDB()->prepare(
            'INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $user['name'],
            $user['email'],
            $user['phone'] ?? '',
            $subject,
            $message,
        ]);
        set_flash('success', 'Đã gửi báo cáo thành công! Chúng tôi sẽ phản hồi sớm.');
        redirect(url('about.php'));
    }
}

$title    = 'TechMart - Giới thiệu';
$extraCss = ['css/about.css'];
require __DIR__ . '/includes/header.php';
?>

<main class="section">
  <div class="container">

    <!-- Thông tin giới thiệu -->
    <section class="about-content card">
      <h1>Về TechMart</h1>
      <p>TechMart là website thương mại điện tử chuyên cung cấp điện thoại, laptop, tablet và phụ kiện chính hãng với giao diện trực quan, dễ sử dụng, lấy cảm hứng từ phong cách mua sắm hiện đại.</p>
      <p>Hệ thống sử dụng PHP + MySQL để xử lý đăng nhập, giỏ hàng, đặt hàng, quản trị sản phẩm và lưu trữ dữ liệu một cách an toàn.</p>

      <div class="about-highlights">
        <div class="highlight-item">
          <i class="fa-solid fa-medal"></i>
          <span>Cam kết chính hãng</span>
        </div>
        <div class="highlight-item">
          <i class="fa-solid fa-truck"></i>
          <span>Giao hàng toàn quốc</span>
        </div>
        <div class="highlight-item">
          <i class="fa-solid fa-headset"></i>
          <span>Hỗ trợ 24/7</span>
        </div>
      </div>
    </section>

    <!-- Công nghệ sử dụng -->
    <section class="tech-section card">
      <h2>Công nghệ sử dụng</h2>
      <div class="tech-list">
        <div class="tech-item">
          <i class="fa-brands fa-html5" style="color:#e44d26"></i>
          <div>
            <strong>HTML5 / CSS3</strong>
            <span>Xây dựng cấu trúc và giao diện trang web</span>
          </div>
        </div>
        <div class="tech-item">
          <i class="fa-brands fa-js" style="color:#f7df1e"></i>
          <div>
            <strong>JavaScript</strong>
            <span>Xử lý tương tác phía trình duyệt</span>
          </div>
        </div>
        <div class="tech-item">
          <i class="fa-brands fa-php" style="color:#777bb4"></i>
          <div>
            <strong>PHP</strong>
            <span>Xử lý logic server: đăng nhập, đặt hàng, phân quyền</span>
          </div>
        </div>
        <div class="tech-item">
          <i class="fa-solid fa-database" style="color:#00758f"></i>
          <div>
            <strong>MySQL</strong>
            <span>Lưu trữ sản phẩm, đơn hàng, tài khoản</span>
          </div>
        </div>
        <div class="tech-item">
          <i class="fa-solid fa-lock" style="color:#27ae60"></i>
          <div>
            <strong>PDO Prepared Statement</strong>
            <span>Bảo mật truy vấn, chống SQL Injection</span>
          </div>
        </div>
        <div class="tech-item">
          <i class="fa-solid fa-key" style="color:#e67e22"></i>
          <div>
            <strong>Session PHP</strong>
            <span>Quản lý đăng nhập, phân quyền user / admin</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Nút báo cáo -->
    <section class="report-section card">
      <div class="report-inner">
        <div class="report-text">
          <h2>Phản hồi & Báo cáo</h2>
          <p>Phát hiện lỗi, sản phẩm không đúng mô tả hoặc muốn góp ý? Hãy gửi báo cáo cho chúng tôi.</p>
        </div>

        <?php if ($user): ?>
          <!-- Đã đăng nhập → mở modal -->
          <button class="btn-report" onclick="openModal()">
            <i class="fa-solid fa-flag"></i>
            Gửi báo cáo
          </button>
        <?php else: ?>
          <!-- Chưa đăng nhập → về trang login -->
          <div class="report-login">
            <a class="btn-report" href="<?= url('login.php') ?>">
              <i class="fa-solid fa-flag"></i>
              Gửi báo cáo
            </a>
            <p>Bạn cần <a href="<?= url('login.php') ?>">đăng nhập</a> để gửi báo cáo.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Bản đồ -->
    <section class="map-section card">
      <iframe
       src="https://www.google.com/maps?q=1+Vo+Van+Ngan+Thu+Duc+Ho+Chi+Minh&z=16&output=embed"
        width="100%" height="380"
        style="border:0; border-radius:12px;"
        allowfullscreen loading="lazy">
      </iframe>
    </section>

  </div>
</main>

<!-- MODAL BÁO CÁO — chỉ render khi đã đăng nhập -->
<?php if ($user): ?>
<div class="modal-overlay" id="reportModal">
  <div class="modal-box">

    <button class="modal-close" onclick="closeModal()" title="Đóng">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <h3>Gửi báo cáo</h3>
    <p class="modal-sub">Thông tin đã được điền sẵn từ tài khoản của bạn.</p>

    <?php if ($error !== ''): ?>
      <div class="alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('about.php') ?>">

      <!-- Thông tin tự động — disabled, không gửi lên -->
      <div class="modal-field">
        <label>Họ và tên</label>
        <input type="text" value="<?= e($user['name']) ?>" disabled>
      </div>
      <div class="modal-field">
        <label>Email</label>
        <input type="email" value="<?= e($user['email']) ?>" disabled>
      </div>
      <?php if (!empty($user['phone'])): ?>
      <div class="modal-field">
        <label>Số điện thoại</label>
        <input type="text" value="<?= e($user['phone']) ?>" disabled>
      </div>
      <?php endif; ?>

      <!-- Thông tin cần nhập -->
      <div class="modal-field">
        <label>Chủ đề *</label>
        <input type="text" name="subject"
               placeholder="VD: Lỗi thanh toán, sản phẩm không đúng..."
               value="<?= e($_POST['subject'] ?? '') ?>"
               required>
      </div>
      <div class="modal-field">
        <label>Nội dung báo cáo *</label>
        <textarea name="message"
                  placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
                  required><?= e($_POST['message'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-paper-plane"></i>
        Gửi báo cáo
      </button>

    </form>
  </div>
</div>

<script>
function openModal() {
    document.getElementById('reportModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('reportModal').classList.remove('open');
    document.body.style.overflow = '';
}
// Click vùng tối bên ngoài để đóng
document.getElementById('reportModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
// Phím ESC để đóng
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
// Tự mở lại modal nếu submit có lỗi
<?php if ($error !== ''): ?>
document.addEventListener('DOMContentLoaded', openModal);
<?php endif; ?>
</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>

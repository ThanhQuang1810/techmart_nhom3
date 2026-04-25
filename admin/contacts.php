<?php
require_once __DIR__ . '/../config.php';
require_admin();

$pdo = getDB();

// Đánh dấu đã đọc
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = :id")
        ->execute([':id' => $id]);
    redirect(url('admin/contacts.php'));
}

// Xoá báo cáo
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM contacts WHERE id = :id")
        ->execute([':id' => $id]);
    set_flash('success', 'Đã xoá báo cáo.');
    redirect(url('admin/contacts.php'));
}

// Lọc theo trạng thái
$filter = $_GET['filter'] ?? 'all';
$where  = '';
if ($filter === 'unread') $where = 'WHERE is_read = 0';
if ($filter === 'read')   $where = 'WHERE is_read = 1';

$contacts    = $pdo->query("SELECT * FROM contacts $where ORDER BY created_at DESC")->fetchAll();
$unreadCount = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();
$totalCount  = (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$readCount   = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 1")->fetchColumn();

$adminTitle = 'Báo cáo & Liên hệ — Admin';
require __DIR__ . '/../includes/admin_header.php';
?>

<style>
.page-header {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
}
.page-header h1 { font-size: 24px; font-weight: 800; display:flex; align-items:center; gap:10px; }
.filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
.filter-tab {
    padding: 8px 18px; border-radius: 20px;
    border: 1.5px solid var(--border, #e5e7eb);
    background: transparent; cursor: pointer;
    font-size: 13px; font-weight: 600;
    color: var(--muted, #6b7280);
    text-decoration: none; transition: all 0.2s ease;
}
.filter-tab:hover { border-color: var(--primary); color: var(--primary); }
.filter-tab.active { background: var(--primary); border-color: var(--primary); color: #fff; }
.badge-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 20px; height: 20px; border-radius: 999px;
    background: #ff4d4f; color: #fff;
    font-size: 11px; font-weight: 700; margin-left: 4px;
}
.stats-strip { display: flex; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
.stat-chip {
    background: var(--white, #fff); border-radius: 12px;
    padding: 14px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    display: flex; align-items: center; gap: 12px; min-width: 150px;
}
.stat-chip-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 17px;
}
.stat-chip-icon.blue  { background: #dbeafe; color: #1d4ed8; }
.stat-chip-icon.red   { background: #fee2e2; color: #dc2626; }
.stat-chip-icon.green { background: #dcfce7; color: #16a34a; }
.stat-chip-num   { font-size: 22px; font-weight: 800; line-height: 1; }
.stat-chip-label { font-size: 12px; color: var(--muted); margin-top: 3px; }
.empty-box {
    text-align: center; padding: 60px 20px;
    background: var(--white, #fff); border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.empty-box i { font-size: 48px; color: #d1d5db; margin-bottom: 14px; display: block; }
.empty-box p { color: var(--muted); font-size: 15px; }
.contact-list { display: flex; flex-direction: column; gap: 14px; }
.contact-card {
    background: var(--white, #fff); border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border-left: 4px solid transparent;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
}
.contact-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.09); }
.contact-card.unread { border-left-color: #ff4d4f; }
.contact-card.read   { border-left-color: #d1d5db; }
.contact-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px 0; gap: 12px; flex-wrap: wrap;
}
.contact-sender { display: flex; align-items: center; gap: 12px; }
.sender-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #1a94ff, #5b5ff8);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 15px; font-weight: 700; flex-shrink: 0;
}
.sender-name { font-size: 14px; font-weight: 700; margin-bottom: 2px; }
.sender-meta { font-size: 12px; color: var(--muted); }
.sender-meta a { color: var(--primary); }
.contact-badges { display: flex; align-items: center; gap: 8px; }
.badge-unread {
    background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5;
    border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 700;
}
.badge-read-label {
    background: #f0fdf4; color: #16a34a; border: 1px solid #86efac;
    border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 700;
}
.contact-date { font-size: 12px; color: var(--muted); }
.contact-card-body { padding: 12px 20px; }
.contact-subject { font-size: 14px; font-weight: 700; margin-bottom: 8px; }
.contact-message {
    font-size: 13px; color: var(--muted); line-height: 1.6;
    background: var(--bg, #f5f5fa); border-radius: 10px; padding: 12px 14px;
}
.contact-card-footer { display: flex; gap: 8px; padding: 10px 20px 16px; flex-wrap: wrap; }
.btn-action {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 10px;
    font-size: 13px; font-weight: 600; border: 1.5px solid;
    cursor: pointer; text-decoration: none;
    transition: all 0.2s ease; background: transparent;
}
.btn-action:hover { transform: translateY(-1px); }
.btn-action.mark-read  { border-color: #16a34a; color: #16a34a; }
.btn-action.mark-read:hover  { background: #dcfce7; }
.btn-action.btn-delete { border-color: #dc2626; color: #dc2626; }
.btn-action.btn-delete:hover { background: #fee2e2; }
.btn-action.btn-reply  { border-color: var(--primary, #1a94ff); color: var(--primary, #1a94ff); }
.btn-action.btn-reply:hover  { background: #dbeafe; }
</style>

<div class="admin-card">

    <!-- Header -->
    <div class="page-header">
        <h1>
            <i class="fa-solid fa-flag" style="color:#ff4d4f;"></i>
            Báo cáo & Liên hệ
            <?php if ($unreadCount > 0): ?>
                <span class="badge-count"><?= $unreadCount ?></span>
            <?php endif; ?>
        </h1>
        <div class="filter-tabs">
            <a href="contacts.php?filter=all"
               class="filter-tab <?= $filter==='all'    ? 'active':'' ?>">
                Tất cả (<?= $totalCount ?>)
            </a>
            <a href="contacts.php?filter=unread"
               class="filter-tab <?= $filter==='unread' ? 'active':'' ?>">
                Chưa đọc
                <?php if ($unreadCount > 0): ?>
                    <span class="badge-count"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <a href="contacts.php?filter=read"
               class="filter-tab <?= $filter==='read'   ? 'active':'' ?>">
                Đã đọc (<?= $readCount ?>)
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-strip">
        <div class="stat-chip">
            <div class="stat-chip-icon blue"><i class="fa-solid fa-envelope"></i></div>
            <div>
                <div class="stat-chip-num"><?= $totalCount ?></div>
                <div class="stat-chip-label">Tổng báo cáo</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-icon red"><i class="fa-solid fa-envelope-open"></i></div>
            <div>
                <div class="stat-chip-num"><?= $unreadCount ?></div>
                <div class="stat-chip-label">Chưa đọc</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-icon green"><i class="fa-solid fa-check-double"></i></div>
            <div>
                <div class="stat-chip-num"><?= $readCount ?></div>
                <div class="stat-chip-label">Đã đọc</div>
            </div>
        </div>
    </div>

    <!-- Danh sách -->
    <?php if (empty($contacts)): ?>
        <div class="empty-box">
            <i class="fa-solid fa-inbox"></i>
            <p>Không có báo cáo nào<?= $filter !== 'all' ? ' trong mục này' : '' ?>.</p>
        </div>
    <?php else: ?>
        <div class="contact-list">
            <?php foreach ($contacts as $c): ?>
                <div class="contact-card <?= $c['is_read'] ? 'read' : 'unread' ?>">

                    <div class="contact-card-header">
                        <div class="contact-sender">
                            <div class="sender-avatar">
                                <?= mb_strtoupper(mb_substr($c['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="sender-name"><?= e($c['name']) ?></div>
                                <div class="sender-meta">
                                    <a href="mailto:<?= e($c['email']) ?>"><?= e($c['email']) ?></a>
                                    <?php if (!empty($c['phone'])): ?>
                                        &nbsp;·&nbsp;<?= e($c['phone']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="contact-badges">
                            <?php if (!$c['is_read']): ?>
                                <span class="badge-unread">● Chưa đọc</span>
                            <?php else: ?>
                                <span class="badge-read-label">✓ Đã đọc</span>
                            <?php endif; ?>
                            <span class="contact-date">
                                <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                            </span>
                        </div>
                    </div>

                    <div class="contact-card-body">
                        <?php if (!empty($c['subject'])): ?>
                            <div class="contact-subject">
                                <i class="fa-solid fa-tag" style="color:var(--primary); font-size:12px; margin-right:6px;"></i>
                                <?= e($c['subject']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="contact-message"><?= nl2br(e($c['message'])) ?></div>
                    </div>

                    <div class="contact-card-footer">
                        <?php if (!$c['is_read']): ?>
                            <a href="contacts.php?read=<?= $c['id'] ?>&filter=<?= $filter ?>"
                               class="btn-action mark-read">
                                <i class="fa-solid fa-check"></i> Đánh dấu đã đọc
                            </a>
                        <?php endif; ?>
                        <a href="mailto:<?= e($c['email']) ?>?subject=Re: <?= urlencode($c['subject'] ?? 'Phản hồi TechMart') ?>"
                           class="btn-action btn-reply">
                            <i class="fa-solid fa-reply"></i> Trả lời Email
                        </a>
                        <a href="contacts.php?delete=<?= $c['id'] ?>&filter=<?= $filter ?>"
                           class="btn-action btn-delete"
                           onclick="return confirm('Xoá báo cáo này?')">
                            <i class="fa-solid fa-trash"></i> Xoá
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
<section class="admin-page-heading">
    <div><span class="eyebrow">BẢNG ĐIỀU KHIỂN</span><h1>Tổng quan Healcare</h1><p>Theo dõi nhanh hoạt động tư vấn và các yêu cầu cần được nhân viên xử lý.</p></div>
    <a class="button" href="<?= url('admin-contacts') ?>">Mở danh sách liên hệ <span>→</span></a>
</section>

<section class="admin-stat-grid" aria-label="Chỉ số tổng quan">
    <article class="admin-stat-card accent"><span class="admin-stat-icon">✉</span><div><strong><?= e((string) $stats['total']) ?></strong><span>Tổng yêu cầu liên hệ</span></div></article>
    <article class="admin-stat-card"><span class="admin-stat-icon">●</span><div><strong><?= e((string) $stats['new']) ?></strong><span>Chờ tiếp nhận</span></div></article>
    <article class="admin-stat-card"><span class="admin-stat-icon">◷</span><div><strong><?= e((string) $stats['in_progress']) ?></strong><span>Đang xử lý</span></div></article>
    <article class="admin-stat-card"><span class="admin-stat-icon">✓</span><div><strong><?= e((string) $stats['resolved']) ?></strong><span>Đã hoàn tất</span></div></article>
</section>

<section class="admin-dashboard-grid">
    <div class="admin-panel">
        <div class="admin-panel-heading"><div><span class="eyebrow">CRM</span><h2>Yêu cầu mới nhất</h2></div><a class="text-link" href="<?= url('admin-contacts') ?>">Xem tất cả →</a></div>
        <?php $latest = array_slice($recentContacts, 0, 5); ?>
        <?php if (!$latest): ?><div class="admin-empty">Chưa có yêu cầu liên hệ nào.</div><?php else: ?>
            <div class="admin-recent-list">
                <?php foreach ($latest as $contact): ?>
                    <a class="admin-recent-item" href="<?= url('admin-contacts', ['q' => $contact['phone']]) ?>">
                        <span class="admin-avatar"><?= e(mb_strtoupper(mb_substr((string) $contact['name'], 0, 1, 'UTF-8'), 'UTF-8')) ?></span>
                        <span class="admin-recent-main"><strong><?= e((string) $contact['name']) ?></strong><small><?= e((string) $contact['phone']) ?> · <?= e((string) $contact['message']) ?></small></span>
                        <span class="status-pill status-<?= e((string) $contact['status']) ?>"><?= e(match ($contact['status']) { 'new' => 'Mới', 'in_progress' => 'Đang xử lý', 'resolved' => 'Hoàn tất', default => 'Lưu trữ' }) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="admin-panel quick-panel">
        <div class="admin-panel-heading"><div><span class="eyebrow">KHO KIẾN THỨC</span><h2>Nội dung hiện có</h2></div></div>
        <div class="content-count-list"><a href="<?= url('admin-content') ?>"><span>Bệnh đang theo dõi</span><strong><?= e((string) $counts['diseases']) ?></strong></a><a href="<?= url('admin-content') ?>"><span>Thực phẩm</span><strong><?= e((string) $counts['foods']) ?></strong></a><a href="<?= url('admin-content') ?>"><span>Công thức món ăn</span><strong><?= e((string) $counts['recipes']) ?></strong></a><a href="<?= url('admin-content') ?>"><span>Cơ sở y tế tham khảo</span><strong><?= e((string) $counts['sources']) ?></strong></a></div>
    </div>
</section>

<section class="admin-safety-note"><strong>Phạm vi của CRM</strong><span>CRM hiện quản lý yêu cầu liên hệ từ website và trạng thái xử lý. Hồ sơ sức khỏe trên trình duyệt của người dùng không được tự động đưa vào danh sách quản trị để hạn chế lộ thông tin y tế.</span></section>

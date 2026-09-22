<section class="admin-page-heading">
    <div><span class="eyebrow">CRM / LIÊN HỆ</span><h1>Quản lý yêu cầu liên hệ</h1><p>Tiếp nhận, phân loại và ghi chú tiến độ hỗ trợ người dùng.</p></div>
</section>

<form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="page" value="admin-contacts">
    <label class="filter-search">Tìm kiếm<input name="q" value="<?= e($search) ?>" placeholder="Tên, số điện thoại, nội dung..." /></label>
    <label>Trạng thái<select name="status"><option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Tất cả</option><option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>Mới</option><option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>Đang xử lý</option><option value="resolved" <?= $statusFilter === 'resolved' ? 'selected' : '' ?>>Hoàn tất</option><option value="archived" <?= $statusFilter === 'archived' ? 'selected' : '' ?>>Lưu trữ</option></select></label>
    <button class="button button-small" type="submit">Lọc dữ liệu</button>
</form>

<section class="admin-panel contacts-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Người liên hệ</th><th>Nội dung</th><th>Tiếp nhận</th><th>Trạng thái & ưu tiên</th><th>Cập nhật</th></tr></thead>
            <tbody>
            <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td><strong><?= e((string) $contact['name']) ?></strong><a class="table-phone" href="tel:<?= e((string) $contact['phone']) ?>"><?= e((string) $contact['phone']) ?></a></td>
                    <td class="message-cell"><?= e((string) $contact['message']) ?></td>
                    <td><span class="date-cell"><?= e(date('d/m/Y H:i', strtotime((string) $contact['created_at']))) ?></span><small><?= e((string) $contact['source']) ?></small></td>
                    <td>
                        <form class="inline-update-form" method="post" action="<?= url('admin-contact-update') ?>">
                            <input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="id" value="<?= e((string) $contact['id']) ?>">
                            <select name="status" aria-label="Trạng thái"><option value="new" <?= $contact['status'] === 'new' ? 'selected' : '' ?>>Mới</option><option value="in_progress" <?= $contact['status'] === 'in_progress' ? 'selected' : '' ?>>Đang xử lý</option><option value="resolved" <?= $contact['status'] === 'resolved' ? 'selected' : '' ?>>Hoàn tất</option><option value="archived" <?= $contact['status'] === 'archived' ? 'selected' : '' ?>>Lưu trữ</option></select>
                            <select name="priority" aria-label="Mức ưu tiên"><option value="low" <?= $contact['priority'] === 'low' ? 'selected' : '' ?>>Thấp</option><option value="normal" <?= $contact['priority'] === 'normal' ? 'selected' : '' ?>>Bình thường</option><option value="high" <?= $contact['priority'] === 'high' ? 'selected' : '' ?>>Cao</option></select>
                            <textarea name="notes" rows="2" placeholder="Ghi chú nội bộ..."> <?= e((string) $contact['notes']) ?></textarea>
                            <button class="button button-small" type="submit">Lưu</button>
                        </form>
                    </td>
                    <td><span class="date-cell"><?= e(date('d/m/Y H:i', strtotime((string) $contact['updated_at']))) ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$contacts): ?><tr><td colspan="5"><div class="admin-empty">Không tìm thấy yêu cầu phù hợp.</div></td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

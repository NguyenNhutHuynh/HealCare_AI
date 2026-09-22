<?php
$success = $_SESSION['admin_success'] ?? '';
$error = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_success'], $_SESSION['admin_error']);
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($view === 'dashboard' ? 'Tổng quan' : ($view === 'contacts' ? 'Quản lý liên hệ' : 'Nội dung')) ?> · Healcare Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">
<header class="admin-header">
    <a class="admin-brand" href="<?= url('admin') ?>"><span class="admin-brand-mark">H</span><span><strong>Healcare Admin</strong><small>Trung tâm vận hành</small></span></a>
    <div class="admin-user"><span>Xin chào, <?= e($adminUser) ?></span><a href="<?= url('admin-logout') ?>">Đăng xuất</a></div>
</header>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <nav aria-label="Điều hướng quản trị">
            <a class="<?= $page === 'admin' ? 'active' : '' ?>" href="<?= url('admin') ?>"><span>⌂</span> Tổng quan</a>
            <a class="<?= $page === 'admin-contacts' ? 'active' : '' ?>" href="<?= url('admin-contacts') ?>"><span>✉</span> Yêu cầu liên hệ</a>
            <a class="<?= $page === 'admin-content' ? 'active' : '' ?>" href="<?= url('admin-content') ?>"><span>▦</span> Kho nội dung</a>
            <a href="<?= url('home') ?>"><span>↗</span> Xem website</a>
        </nav>
        <div class="admin-sidebar-note"><strong>Nhắc an toàn</strong><p>Chỉ truy cập dữ liệu người dùng khi có sự đồng ý và đúng mục đích hỗ trợ.</p></div>
    </aside>
    <main class="admin-main">
        <?php if ($success): ?><div class="admin-alert success"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="admin-alert error"><?= e($error) ?></div><?php endif; ?>
        <?php require __DIR__ . '/pages/' . $view . '.php'; ?>
    </main>
</div>
</body>
</html>

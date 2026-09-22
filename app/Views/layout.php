<?php
$asset = 'assets/app.css';
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Healcare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e($asset) ?>">
</head>
<body data-page="<?= e($page) ?>">
<header class="site-header">
    <div class="site-header-inner">
        <a class="brand" href="<?= url('home') ?>"><span class="brand-mark">✚</span><span><strong>Healcare</strong><small>Dinh dưỡng thông minh</small></span></a>
        <button class="menu-toggle" aria-label="Mở menu">☰</button>
        <nav class="main-nav" aria-label="Điều hướng chính">
            <a class="<?= $page === 'home' ? 'active' : '' ?>" href="<?= url('home') ?>">Trang chủ</a>
            <a class="<?= $page === 'diseases' ? 'active' : '' ?>" href="<?= url('diseases') ?>">Theo bệnh</a>
            <a class="<?= in_array($page, ['foods', 'recipes'], true) ? 'active' : '' ?>" href="<?= url('foods') ?>">Thư viện ăn uống</a>
            <a class="<?= $page === 'profile' ? 'active' : '' ?>" href="<?= url('profile') ?>">Hồ sơ</a>
            <a class="<?= $page === 'recommendations' ? 'active' : '' ?>" href="<?= url('recommendations') ?>">Gợi ý cho bạn</a>
            <a class="<?= $page === 'contact' ? 'active' : '' ?>" href="<?= url('contact') ?>">Liên hệ bác sĩ</a>
            <a class="<?= $page === 'sources' ? 'active' : '' ?>" href="<?= url('sources') ?>">Nguồn tin</a>
        </nav>
        <div class="header-actions">
            <button class="font-btn" data-font-toggle>Aa <span>Cỡ chữ</span></button>
            <a class="button button-small header-ai-btn" href="<?= url('chat') ?>">Hỏi AI <span>→</span></a>
        </div>
    </div>
</header>

<main class="page-shell">
<?php require __DIR__ . '/pages/' . $page . '.php'; ?>
</main>

<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="brand"><span class="brand-mark">✚</span><span><strong>Healcare</strong><small>Dinh dưỡng thông minh</small></span></div>
        <p>Thông tin tham khảo, không thay thế chẩn đoán và điều trị của bác sĩ.</p>
        <a href="<?= url('sources') ?>">Xem nguồn tham khảo →</a>
    </div>
</footer>
<script src="assets/app.js" defer></script>
</body>
</html>

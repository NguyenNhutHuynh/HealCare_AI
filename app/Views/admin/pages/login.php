<main class="admin-login-card">
    <a class="admin-brand login-brand" href="<?= url('home') ?>"><span class="admin-brand-mark">H</span><span><strong>Healcare Admin</strong><small>Khu vực dành riêng cho quản trị viên</small></span></a>
    <div class="admin-login-heading"><span class="eyebrow">KHU VỰC BẢO MẬT</span><h1>Đăng nhập quản trị</h1><p>Quản lý yêu cầu liên hệ, theo dõi vận hành và kho kiến thức Healcare.</p></div>
    <?php if ($error): ?><div class="admin-alert error"><?= e($error) ?></div><?php endif; ?>
    <form class="admin-login-form" method="post" action="<?= url('admin-login') ?>">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <label>Tên đăng nhập<input name="username" autocomplete="username" required autofocus></label>
        <label>Mật khẩu<input name="password" type="password" autocomplete="current-password" required></label>
        <button class="button" type="submit">Đăng nhập <span>→</span></button>
    </form>
    <p class="admin-login-hint">Tài khoản mặc định cho môi trường phát triển: <code>admin</code> / <code>Healcare@2026</code>. Hãy đổi bằng biến môi trường trước khi triển khai thật.</p>
</main>

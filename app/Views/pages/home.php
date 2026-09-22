<section class="hero home-hero">
    <div class="hero-copy"><span class="eyebrow">ĐỒNG HÀNH CÙNG SỨC KHỎE</span>
        <h1>Ăn đúng hơn.<br><em>Sống an tâm hơn.</em></h1>
        <p>Trợ lý dinh dưỡng dành cho bạn và gia đình. Tra cứu thực phẩm, cách chế biến và hỏi đáp đơn giản, dễ hiểu.</p>
        <div class="hero-actions"><a class="button" href="<?= url('chat') ?>">Hỏi chuyên gia AI <span>→</span></a><a class="text-link" href="<?= url('diseases') ?>">Xem theo bệnh</a></div>
    </div>
    <div class="hero-art">
        <div class="sun"></div>
        <img class="hero-image" src="assets/images/hero-bowl.png" alt="Bát ăn cân bằng gồm rau xanh, cá và ngũ cốc nguyên hạt">
        <div class="art-note"><b>✓</b> Kiến thức chọn lọc<br><small>WHO và nguồn y tế chính thống</small></div>
    </div>
</section>
<section class="trust"><span>Kiến thức được chọn lọc từ</span><b>WHO</b><b>FAO</b><b>BỆNH VIỆN BẠCH MAI</b><b>VIỆN DINH DƯỠNG</b></section>
<section class="content-section">
    <div class="section-heading">
        <div><span class="eyebrow">TRA CỨU NHANH</span>
            <h2>Bạn đang quan tâm điều gì?</h2>
        </div><a class="text-link" href="<?= url('diseases') ?>">Xem tất cả →</a>
    </div>
    <div class="card-grid disease-grid"><?php foreach ($knowledge->diseases() as $slug => $disease): ?><a class="disease-card <?= e($disease['color']) ?>" href="<?= url('diseases', ['id' => $slug]) ?>"><svg class="disease-illustration" aria-hidden="true"><use href="assets/illustrations/disease-sprite.svg#<?= e($knowledge->illustration($slug)) ?>"></use></svg>
                <h3><?= e($disease['name']) ?></h3>
                <p><?= e($disease['desc']) ?></p><span class="arrow">↗</span>
            </a><?php endforeach; ?></div>
</section>
<section class="split-section">
    <div class="chat-promo"><span class="eyebrow">HỎI ĐÁP CÙNG AI</span>
        <h2>Một người bạn luôn lắng nghe.</h2>
        <p>Hãy hỏi bằng ngôn ngữ tự nhiên, Healcare sẽ trả lời ngắn gọn, dễ hiểu và nhắc bạn khi cần gặp bác sĩ.</p><a class="button" href="<?= url('chat') ?>">Bắt đầu trò chuyện <span>→</span></a>
    </div>
    <div class="safe-card"><span class="safe-icon">⚕</span>
        <div>
            <h3>An toàn là ưu tiên</h3>
            <p>AI không thay thế bác sĩ. Không tự ý ngừng thuốc hoặc thay đổi chế độ điều trị.</p>
        </div>
    </div>
</section>

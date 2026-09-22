<?php $selectedSlug = (string) ($_GET['id'] ?? '');
$selected = $knowledge->findDisease($selectedSlug);
$diseaseRecipes = $selected ? $knowledge->recipesForDisease($selectedSlug) : [];
$query = trim((string) ($_GET['q'] ?? ''));
$results = $knowledge->search($query, 'disease')['diseases']; ?>
<section class="page-intro"><span class="eyebrow">THƯ VIỆN SỨC KHỎE</span>
    <h1>Tra cứu theo bệnh</h1>
    <p>Tìm gợi ý thực phẩm và cách ăn phù hợp hơn với tình trạng sức khỏe của bạn.</p>
</section>
<form class="search-bar" method="get"><input type="hidden" name="page" value="diseases"><input name="q" value="<?= e($query) ?>" placeholder="Tìm bệnh, ví dụ: tiểu đường, huyết áp..."><button class="button" type="submit">Tìm kiếm</button></form>
<?php if ($selected): ?><section class="detail-panel"><a class="back-link" href="<?= url('diseases') ?>">← Quay lại danh sách</a>
        <div class="detail-title"><svg class="disease-illustration detail-illustration" aria-hidden="true"><use href="assets/illustrations/disease-sprite.svg#<?= e($knowledge->illustration((string) ($_GET['id'] ?? ''))) ?>"></use></svg>
            <div>
                <h2><?= e($selected['name']) ?></h2>
                <p><?= e($selected['desc']) ?></p>
            </div>
        </div>
        <div class="medical-disclaimer">Thông tin dưới đây giúp bạn hiểu tổng quan và chuẩn bị câu hỏi khi đi khám. Dấu hiệu giống nhau có thể do nhiều bệnh khác nhau; chỉ nhân viên y tế mới có thể chẩn đoán.</div>
        <div class="disease-guidance-grid">
            <div class="guidance-block"><h3>Nguyên nhân thường gặp</h3><ul><?php foreach ($selected['causes'] ?? [] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></div>
            <div class="guidance-block"><h3>Cách hạn chế và phòng ngừa</h3><ul><?php foreach ($selected['prevention'] ?? [] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></div>
        </div>
        <div class="disease-facts">
            <article class="fact-card fact-wide"><span class="fact-number">01</span><div><h3>Bệnh này là gì?</h3><p><?= e($selected['overview'] ?? $selected['desc']) ?></p></div></article>
            <article class="fact-card"><span class="fact-number">02</span><div><h3>Dấu hiệu thường gặp</h3><ul><?php foreach ($selected['symptoms'] ?? [] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></div></article>
            <article class="fact-card"><span class="fact-number">03</span><div><h3>Yếu tố nguy cơ</h3><ul><?php foreach ($selected['risk_factors'] ?? [] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></div></article>
        </div>
        <div class="disease-care-grid">
            <article class="care-card"><h3>✓ Theo dõi và tái khám</h3><ul><?php foreach ($selected['monitoring'] ?? [] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></article>
            <article class="care-card"><h3>⌁ Việc nên làm hằng ngày</h3><ul><?php foreach ($selected['daily'] ?? [] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></article>
        </div>
        <div class="urgent-card"><div class="urgent-icon">!</div><div><h3>Khi nào cần đi khám ngay?</h3><ul><?php foreach ($selected['urgent'] ?? [] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul><p>Nếu triệu chứng nặng hoặc xuất hiện đột ngột, hãy gọi cấp cứu địa phương. Không chờ AI tư vấn trong tình huống khẩn cấp.</p></div></div>
        <div class="detail-columns">
            <div>
                <h4 class="green">NÊN ƯU TIÊN</h4><?php foreach ($selected['eat'] as $item): ?><p>✓ <?= e($item) ?></p><?php endforeach; ?>
            </div>
            <div>
                <h4 class="red">NÊN HẠN CHẾ</h4><?php foreach ($selected['limit'] as $item): ?><p>! <?= e($item) ?></p><?php endforeach; ?>
            </div>
        </div>
        <section class="disease-recipe-section">
            <div class="disease-recipe-heading"><div><span class="eyebrow">GỢI Ý THEO BỆNH</span><h3>Món ăn có thể tham khảo</h3><p>Các món dưới đây được gắn với bệnh này trong kho dữ liệu Healcare. Hãy điều chỉnh khẩu phần, gia vị và nguyên liệu theo bác sĩ hoặc chuyên gia dinh dưỡng, đặc biệt khi có bệnh thận, dị ứng hoặc đang dùng thuốc.</p></div><a class="text-link" href="<?= url('foods', ['tab' => 'recipes']) ?>">Xem toàn bộ món →</a></div>
            <?php if ($diseaseRecipes): ?><div class="disease-recipe-grid">
                <?php foreach ($diseaseRecipes as $recipe): ?>
                    <article class="disease-recipe-card">
                        <div class="disease-recipe-image-wrap"><img class="disease-recipe-image" src="<?= e($recipe['image']) ?>" alt="<?= e($recipe['title']) ?>" loading="lazy"></div>
                        <div class="disease-recipe-body"><div class="disease-recipe-meta"><span><?= e($recipe['meal']) ?></span><span><?= e($recipe['time']) ?></span></div><h4><?= e($recipe['title']) ?></h4><p><?= e($recipe['desc']) ?></p>
                            <details><summary>Xem nguyên liệu và cách làm</summary><strong>Nguyên liệu</strong><ul><?php foreach ($recipe['ingredients'] ?? [] as $ingredient): ?><li><?= e($ingredient) ?></li><?php endforeach; ?></ul><strong>Cách làm</strong><ol><?php foreach ($recipe['steps'] ?? [] as $step): ?><li><?= e($step) ?></li><?php endforeach; ?></ol></details>
                            <a class="youtube-button" href="<?= e('https://www.youtube.com/results?search_query=' . rawurlencode($recipe['youtube_query'])) ?>" target="_blank" rel="noopener">▶ Xem video hướng dẫn</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div><?php else: ?><div class="disease-recipe-empty"><strong>Chưa có công thức riêng trong kho dữ liệu cho bệnh này.</strong><p>Không nên tự áp dụng một thực đơn chung. Bạn có thể hỏi AI để tham khảo hoặc liên hệ bác sĩ/dinh dưỡng viên để được cá nhân hóa.</p><div><a class="button button-small" href="<?= url('chat') ?>">Hỏi AI về bệnh này →</a><a class="text-link" href="<?= url('contact') ?>">Liên hệ bác sĩ</a></div></div><?php endif; ?>
        </section>
        <div class="detail-actions"><a class="button button-small" href="<?= url('chat') ?>">Hỏi AI về bệnh này →</a><?php if (!empty($selected['source'])): ?><a class="source-inline" href="<?= e($selected['source']) ?>" target="_blank" rel="noopener">Đọc nguồn tham khảo ↗</a><?php endif; ?></div>
    </section>
    <div class="section-heading results-heading">
        <div><span class="eyebrow">DANH SÁCH BỆNH LÝ</span><h2>Các bệnh lý khác</h2></div>
        <a class="text-link" href="<?= url('diseases') ?>">Xem tất cả →</a>
    </div>
<?php endif; ?>
<div class="card-grid disease-grid results-grid"><?php foreach ($results as $slug => $disease): ?><a class="disease-card <?= e($disease['color']) ?>" href="<?= url('diseases', ['id' => $slug]) ?>"><svg class="disease-illustration" aria-hidden="true"><use href="assets/illustrations/disease-sprite.svg#<?= e($knowledge->illustration($slug)) ?>"></use></svg>
            <h3><?= e($disease['name']) ?></h3>
            <p><?= e($disease['desc']) ?></p><span class="arrow">↗</span>
        </a><?php endforeach; ?></div>
<?php if (!$results): ?><div class="empty-state">Chưa tìm thấy bệnh phù hợp. Hãy thử từ khóa khác.</div><?php endif; ?>

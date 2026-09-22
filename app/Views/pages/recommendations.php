<?php
use Healcare\Services\RecommendationService;

$profile = $_SESSION['patient_profile'] ?? [];
$suggestions = (new RecommendationService($knowledge))->forProfile($profile);
$conditionNames = array_map(static fn(string $slug): string => $knowledge->findDisease($slug)['name'] ?? $slug, $profile['conditions'] ?? []);
?>
<section class="page-intro"><span class="eyebrow">GỢI Ý RIÊNG CHO BẠN</span><h1>Hôm nay nên nấu gì?</h1><p><?= $profile ? 'Healcare đã lọc theo hồ sơ hiện tại của bạn.' : 'Tạo hồ sơ sức khỏe để nhận gợi ý sát với nhu cầu hơn.' ?></p></section>
<?php if (!$profile): ?>
    <div class="empty-state profile-empty"><h2>Chưa có hồ sơ cá nhân</h2><p>Chỉ mất khoảng một phút để nhập bệnh đang theo dõi, dị ứng và thói quen ăn uống.</p><a class="button" href="<?= url('profile') ?>">Tạo hồ sơ sức khỏe →</a></div>
<?php else: ?>
    <section class="profile-summary"><div><span class="eyebrow">HỒ SƠ ĐANG DÙNG</span><h2><?= e((string) ($profile['name'] ?: 'Hồ sơ của bạn')) ?></h2><p><?= e($profile['age'] ? $profile['age'] . ' tuổi · ' : '') ?><?= e(implode(', ', $conditionNames) ?: 'Chưa chọn bệnh') ?></p></div><a class="text-link" href="<?= url('profile') ?>">Chỉnh sửa hồ sơ →</a></section>
    <div class="recommendation-actions"><button class="button" type="button" data-generate-plan>✨ Nhờ AI lập thực đơn hôm nay</button><span data-plan-status></span></div>
    <div class="ai-plan" data-ai-plan hidden></div>
    <div class="recommendation-grid"><?php foreach ($suggestions as $recipe): ?><article class="recommendation-card"><img src="<?= e($recipe['image']) ?>" alt="<?= e($recipe['title']) ?>"><div class="recommendation-content"><span class="tag good">Phù hợp với hồ sơ</span><h2><?= e($recipe['title']) ?></h2><p><?= e($recipe['desc']) ?></p><h4>Cách làm</h4><ol><?php foreach ($recipe['steps'] as $step): ?><li><?= e($step) ?></li><?php endforeach; ?></ol><div class="recipe-meta">◷ <?= e($recipe['time']) ?> <span>•</span> <?= e($recipe['level']) ?></div><a class="youtube-button" href="<?= e($recipe['youtube_url']) ?>" target="_blank" rel="noopener">▶ Xem video nấu món này trên YouTube</a></div></article><?php endforeach; ?></div>
<?php endif; ?>

<?php
declare(strict_types=1);

$query = trim((string) ($_GET['q'] ?? ''));
$tab = (string) ($_GET['tab'] ?? 'all');
$tabs = [
    'all' => ['label' => 'Tất cả', 'categories' => []],
    'main' => ['label' => 'Thực phẩm chính', 'categories' => ['Rau củ', 'Đạm', 'Ngũ cốc']],
    'fruit' => ['label' => 'Trái cây', 'categories' => ['Trái cây']],
    'drink' => ['label' => 'Nước uống', 'categories' => ['Đồ uống']],
    'snack' => ['label' => 'Ăn vặt', 'categories' => ['Ăn vặt']],
    'recipes' => ['label' => 'Món ăn', 'categories' => []],
];
if (!isset($tabs[$tab])) {
    $tab = 'all';
}

$foodResults = $knowledge->search($query, 'food')['foods'];
$recipeResults = $knowledge->search($query, 'recipe')['recipes'];
if ($tab === 'recipes') {
    $foodResults = [];
} elseif ($tab !== 'all') {
    $categories = $tabs[$tab]['categories'];
    $foodResults = array_filter($foodResults, static fn(array $food): bool => in_array($food['category'], $categories, true));
    $recipeResults = [];
} else {
    $recipeResults = array_values($recipeResults);
}
?>
<section class="page-intro"><span class="eyebrow">THƯ VIỆN ĂN UỐNG</span><h1>Chọn thực phẩm và món ăn an tâm</h1><p>Một nơi duy nhất để tra cứu thực phẩm chính, trái cây, nước uống, món ăn vặt và công thức chế biến phù hợp hơn.</p></section>
<form class="search-bar library-search" method="get"><input type="hidden" name="page" value="foods"><input type="hidden" name="tab" value="<?= e($tab) ?>"><input name="q" value="<?= e($query) ?>" placeholder="Tìm thực phẩm, món ăn, trái cây hoặc nước uống..."><button class="button" type="submit">Tìm kiếm</button></form>
<div class="category-pills library-tabs" role="tablist" aria-label="Nhóm ăn uống"><?php foreach ($tabs as $slug => $item): ?><a class="<?= $tab === $slug ? 'active' : '' ?>" href="<?= url('foods', ['tab' => $slug]) ?>" role="tab" aria-selected="<?= $tab === $slug ? 'true' : 'false' ?>"><?= e($item['label']) ?></a><?php endforeach; ?></div>

<?php if ($foodResults): ?>
    <section class="library-section"><div class="library-section-heading"><div><span class="eyebrow">THỰC PHẨM</span><h2><?= e($tabs[$tab]['label']) ?></h2></div><span><?= e((string) count($foodResults)) ?> lựa chọn</span></div><div class="card-grid food-grid">
        <?php foreach ($foodResults as $food): ?><article class="food-card"><div class="food-image-wrap"><?php if ($food['image'] !== ''): ?><img class="food-image" src="<?= e($food['image']) ?>" alt="<?= e($food['name']) ?>" loading="lazy"><?php else: ?><div class="food-image placeholder-image">🍽</div><?php endif; ?></div><span class="tag <?= e($food['tone']) ?>"><?= e($food['tag']) ?></span><small class="food-category"><?= e($food['category']) ?></small><h3><?= e($food['name']) ?></h3><p><?= e($food['desc']) ?></p><a class="text-link" href="<?= url('chat') ?>">Hỏi AI về thực phẩm này →</a></article><?php endforeach; ?>
    </div></section>
<?php endif; ?>

<?php if ($recipeResults): ?>
    <section class="library-section recipe-library-section"><div class="library-section-heading"><div><span class="eyebrow">BẾP HEALCARE</span><h2><?= $tab === 'all' ? 'Món ăn chế biến' : 'Công thức món ăn' ?></h2></div><span><?= e((string) count($recipeResults)) ?> công thức</span></div><div class="recipe-list">
        <?php foreach ($recipeResults as $index => $recipe): ?><article class="recipe-card"><div class="recipe-photo-wrap"><?php if ($recipe['image'] !== ''): ?><img class="recipe-photo" src="<?= e($recipe['image']) ?>" alt="<?= e($recipe['title']) ?>" loading="lazy"><?php else: ?><div class="recipe-photo photo<?= $index % 3 ?>"></div><?php endif; ?></div><div><span class="recipe-meal"><?= e($recipe['meal']) ?></span><h2><?= e($recipe['title']) ?></h2><p><?= e($recipe['desc']) ?></p><div class="recipe-meta">◷ <?= e($recipe['time']) ?> <span>•</span> <?= e($recipe['level']) ?></div><a class="youtube-button" href="<?= e('https://www.youtube.com/results?search_query=' . rawurlencode($recipe['youtube_query'])) ?>" target="_blank" rel="noopener">▶ Xem video hướng dẫn</a></div></article><?php endforeach; ?>
    </div></section>
<?php endif; ?>

<?php if (!$foodResults && !$recipeResults): ?><div class="empty-state">Chưa tìm thấy nội dung phù hợp. Hãy thử từ khóa hoặc nhóm khác.</div><?php endif; ?>

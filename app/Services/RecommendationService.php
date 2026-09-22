<?php
declare(strict_types=1);

namespace Healcare\Services;

use Healcare\Repositories\KnowledgeRepository;

final class RecommendationService
{
    public function __construct(private KnowledgeRepository $knowledge) {}

    public function forProfile(array $profile): array
    {
        $conditions = $profile['conditions'] ?? [];
        $allergies = array_filter(array_map(static fn(string $item): string => mb_strtolower(trim($item), 'UTF-8'), preg_split('/[,;]+/', (string) ($profile['allergies'] ?? ''))));
        $recipes = array_filter($this->knowledge->recipes(), static function (array $recipe) use ($conditions, $allergies): bool {
            $matchesCondition = !$conditions || !$recipe['suitable_for'] || array_intersect($conditions, $recipe['suitable_for']);
            $recipeText = mb_strtolower($recipe['title'] . ' ' . implode(' ', $recipe['ingredients']), 'UTF-8');
            $hasAllergen = false;
            foreach ($allergies as $allergy) {
                if ($allergy !== '' && mb_strpos($recipeText, $allergy, 0, 'UTF-8') !== false) {
                    $hasAllergen = true;
                    break;
                }
            }
            return $matchesCondition && !$hasAllergen;
        });

        return array_map(static function (array $recipe): array {
            $recipe['youtube_url'] = 'https://www.youtube.com/results?search_query=' . rawurlencode($recipe['youtube_query']);
            return $recipe;
        }, array_values($recipes));
    }
}

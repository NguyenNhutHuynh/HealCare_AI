<?php
declare(strict_types=1);

namespace Healcare\Repositories;

final class KnowledgeRepository
{
    private array $diseases;
    private array $foods;
    private array $recipes;
    private array $contacts;

    public function __construct()
    {
        $this->diseases = require __DIR__ . '/../../data/diseases.php';
        $details = require __DIR__ . '/../../data/disease_details.php';
        $guides = require __DIR__ . '/../../data/disease_guides.php';
        foreach ($details as $slug => $detail) {
            if (isset($this->diseases[$slug])) {
                $this->diseases[$slug] = array_merge($this->diseases[$slug], $detail);
            }
        }
        foreach ($guides as $slug => $guide) {
            if (isset($this->diseases[$slug])) {
                $this->diseases[$slug] = array_merge($this->diseases[$slug], $guide);
            }
        }
        $this->foods = require __DIR__ . '/../../data/foods.php';
        $this->recipes = require __DIR__ . '/../../data/recipes.php';
        $this->recipes = array_merge($this->recipes, require __DIR__ . '/../../data/disease_recipes.php');
        $recipeImages = require __DIR__ . '/../../data/recipe_images.php';
        foreach ($this->recipes as &$recipe) {
            $title = (string) ($recipe['title'] ?? '');
            if (isset($recipeImages[$title])) {
                $recipe['image'] = $recipeImages[$title];
            }
        }
        unset($recipe);
        $foodImages = require __DIR__ . '/../../data/food_images.php';
        foreach ($this->foods as &$food) {
            $name = (string) ($food['name'] ?? '');
            if (isset($foodImages[$name])) {
                $food['image'] = $foodImages[$name];
            }
        }
        unset($food);
        $this->contacts = require __DIR__ . '/../../data/medical_contacts.php';
    }

    public function diseases(): array { return $this->diseases; }
    public function foods(): array { return $this->foods; }
    public function recipes(): array { return $this->recipes; }
    public function contacts(): array { return $this->contacts; }

    public function recipesForDisease(string $slug): array
    {
        return array_values(array_filter(
            $this->recipes,
            static fn(array $recipe): bool => in_array($slug, $recipe['suitable_for'] ?? [], true)
        ));
    }

    public function findDisease(string $slug): ?array
    {
        return $this->diseases[$slug] ?? null;
    }

    public function illustration(string $slug): string
    {
        return [
            'tim-mach' => 'heart',
            'tieu-duong' => 'diabetes',
            'tang-huyet-ap' => 'pressure',
            'mo-mau' => 'cholesterol',
            'than-man' => 'kidney',
            'gout' => 'gout',
            'gan-nhiem-mo' => 'liver',
            'da-day' => 'stomach',
            'trao-nguoc' => 'reflux',
            'tao-bon' => 'colon',
            'beo-phi' => 'weight',
            'loang-xuong' => 'bone',
            'thieu-mau' => 'blood',
        ][$slug] ?? 'diabetes';
    }

    public function search(string $query, string $type = 'all'): array
    {
        $query = mb_strtolower(trim($query), 'UTF-8');
        if ($query === '') {
            return ['diseases' => $this->diseases, 'foods' => $this->foods, 'recipes' => $this->recipes];
        }

        $match = static function (array $item) use ($query): bool {
            $flatten = static function (array $values) use (&$flatten): array {
                $result = [];
                foreach ($values as $value) {
                    is_array($value) ? $result = array_merge($result, $flatten($value)) : $result[] = (string) $value;
                }
                return $result;
            };
            return mb_strpos(mb_strtolower(implode(' ', $flatten($item)), 'UTF-8'), $query) !== false;
        };

        return [
            'diseases' => ($type === 'all' || $type === 'disease') ? array_filter($this->diseases, $match) : [],
            'foods' => ($type === 'all' || $type === 'food') ? array_filter($this->foods, $match) : [],
            'recipes' => ($type === 'all' || $type === 'recipe') ? array_filter($this->recipes, $match) : [],
        ];
    }

    public function context(): string
    {
        return json_encode([
            'diseases' => $this->diseases,
            'foods' => $this->foods,
            'recipes' => $this->recipes,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

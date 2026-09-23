<?php
declare(strict_types=1);

namespace Healcare\Repositories;

use PDO;
use Healcare\Services\DiseaseGeneratorService;

final class KnowledgeRepository
{
    private PDO $db;
    private DiseaseGeneratorService $diseaseGenerator;

    public function __construct(PDO $db, DiseaseGeneratorService $diseaseGenerator)
    {
        $this->db = $db;
        $this->diseaseGenerator = $diseaseGenerator;
    }

    public function diseases(): array
    {
        $stmt = $this->db->query("SELECT * FROM diseases");
        $diseases = [];
        while ($row = $stmt->fetch()) {
            $diseases[$row['slug']] = $this->formatDiseaseRow($row);
        }
        return $diseases;
    }

    public function foods(): array
    {
        $stmt = $this->db->query("SELECT * FROM foods");
        $foods = [];
        while ($row = $stmt->fetch()) {
            $row['benefits'] = json_decode($row['benefits'] ?? '[]', true);
            $foods[] = $row;
        }
        return $foods;
    }

    public function recipes(): array
    {
        $stmt = $this->db->query("SELECT * FROM recipes");
        $recipes = [];
        while ($row = $stmt->fetch()) {
            $row['ingredients'] = json_decode($row['ingredients'] ?? '[]', true);
            $row['steps'] = json_decode($row['steps'] ?? '[]', true);
            $row['suitable_for'] = json_decode($row['suitable_for'] ?? '[]', true);
            $row['desc'] = $row['description'] ?? '';
            $row['level'] = $row['difficulty'] ?? '';
            $recipes[] = $row;
        }
        return $recipes;
    }

    public function contacts(): array
    {
        $stmt = $this->db->query("SELECT * FROM medical_contacts");
        return $stmt->fetchAll();
    }

    public function recipesForDisease(string $slug): array
    {
        $recipes = $this->recipes();
        return array_values(array_filter(
            $recipes,
            static fn(array $recipe): bool => in_array($slug, $recipe['suitable_for'] ?? [], true)
        ));
    }

    public function findDisease(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM diseases WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        if ($row) {
            return $this->formatDiseaseRow($row);
        }
        return null;
    }

    public function search(string $query, string $type = 'all'): array
    {
        $queryLower = mb_strtolower(trim($query), 'UTF-8');
        
        $diseases = $this->diseases();
        $foods = $this->foods();
        $recipes = $this->recipes();

        if ($queryLower === '') {
            return ['diseases' => $diseases, 'foods' => $foods, 'recipes' => $recipes];
        }

        $match = static function (array $item) use ($queryLower): bool {
            $flatten = static function (array $values) use (&$flatten): array {
                $result = [];
                foreach ($values as $value) {
                    is_array($value) ? $result = array_merge($result, $flatten($value)) : $result[] = (string) $value;
                }
                return $result;
            };
            return mb_strpos(mb_strtolower(implode(' ', $flatten($item)), 'UTF-8'), $queryLower) !== false;
        };

        $resultDiseases = ($type === 'all' || $type === 'disease') ? array_filter($diseases, $match) : [];

        // If no disease found, ask AI
        if (empty($resultDiseases) && ($type === 'all' || $type === 'disease')) {
            $generated = $this->diseaseGenerator->generateDiseaseInfo($query);
            if ($generated) {
                // Insert to DB
                $stmt = $this->db->prepare("
                    INSERT IGNORE INTO diseases 
                    (slug, name, icon, color, description, eat, limit_food, symptoms, causes, prevention, overview, risk_factors, monitoring, daily, urgent, source) 
                    VALUES (:slug, :name, :icon, :color, :description, :eat, :limit_food, :symptoms, :causes, :prevention, :overview, :risk_factors, :monitoring, :daily, :urgent, :source)
                ");
                $stmt->execute([
                    'slug' => $generated['slug'],
                    'name' => $generated['name'],
                    'icon' => $generated['icon'],
                    'color' => $generated['color'],
                    'description' => $generated['description'],
                    'eat' => json_encode($generated['eat'] ?? []),
                    'limit_food' => json_encode($generated['limit_food'] ?? []),
                    'symptoms' => json_encode($generated['symptoms'] ?? []),
                    'causes' => json_encode($generated['causes'] ?? []),
                    'prevention' => json_encode($generated['prevention'] ?? []),
                    'overview' => $generated['overview'] ?? '',
                    'risk_factors' => json_encode($generated['risk_factors'] ?? []),
                    'monitoring' => json_encode($generated['monitoring'] ?? []),
                    'daily' => json_encode($generated['daily'] ?? []),
                    'urgent' => json_encode($generated['urgent'] ?? []),
                    'source' => $generated['source'] ?? '',
                ]);
                
                // Re-fetch to include the newly added disease
                $resultDiseases[$generated['slug']] = $generated;
            }
        }

        return [
            'diseases' => $resultDiseases,
            'foods' => ($type === 'all' || $type === 'food') ? array_filter($foods, $match) : [],
            'recipes' => ($type === 'all' || $type === 'recipe') ? array_filter($recipes, $match) : [],
        ];
    }

    public function context(): string
    {
        return json_encode([
            'diseases' => $this->diseases(),
            'foods' => $this->foods(),
            'recipes' => $this->recipes(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function illustration(string $slug): string
    {
        // Try to match standard illustrations or return default
        $map = [
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
        ];
        return $map[$slug] ?? 'diabetes';
    }

    private function formatDiseaseRow(array $row): array
    {
        return [
            'name' => $row['name'],
            'icon' => $row['icon'],
            'color' => $row['color'],
            'desc' => $row['description'],
            'eat' => json_decode($row['eat'] ?? '[]', true),
            'limit' => json_decode($row['limit_food'] ?? '[]', true),
            'symptoms' => json_decode($row['symptoms'] ?? '[]', true),
            'causes' => json_decode($row['causes'] ?? '[]', true),
            'prevention' => json_decode($row['prevention'] ?? '[]', true),
            'overview' => $row['overview'] ?? '',
            'risk_factors' => json_decode($row['risk_factors'] ?? '[]', true),
            'monitoring' => json_decode($row['monitoring'] ?? '[]', true),
            'daily' => json_decode($row['daily'] ?? '[]', true),
            'urgent' => json_decode($row['urgent'] ?? '[]', true),
            'source' => $row['source'] ?? '',
        ];
    }
}

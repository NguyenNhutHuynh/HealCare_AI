<?php
declare(strict_types=1);

namespace Healcare\Controllers;

use Healcare\Repositories\KnowledgeRepository;
use Healcare\Services\AIService;

final class RecommendationController
{
    public function __construct(private KnowledgeRepository $knowledge) {}

    public function generate(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $profile = $_SESSION['patient_profile'] ?? [];
        $result = (new AIService($this->knowledge))->recommend($profile);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}

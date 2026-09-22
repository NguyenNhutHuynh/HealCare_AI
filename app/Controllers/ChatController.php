<?php
declare(strict_types=1);

namespace Healcare\Controllers;

use Healcare\Repositories\KnowledgeRepository;
use Healcare\Services\AIService;

final class ChatController
{
    public function __construct(private KnowledgeRepository $knowledge) {}

    public function reply(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $payload = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $question = trim((string) ($payload['message'] ?? ''));
        if ($question === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => 'Vui lòng nhập câu hỏi.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $history = is_array($payload['history'] ?? null) ? $payload['history'] : [];
        echo json_encode((new AIService($this->knowledge))->reply($question, $history, $_SESSION['patient_profile'] ?? []), JSON_UNESCAPED_UNICODE);
    }
}

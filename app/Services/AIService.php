<?php
declare(strict_types=1);

namespace Healcare\Services;

use Healcare\Repositories\KnowledgeRepository;

final class AIService
{
    public function __construct(private KnowledgeRepository $knowledge) {}

    public function reply(string $question, array $history = [], array $profile = []): array
    {
        $apiKey = getenv('OPENAI_API_KEY') ?: '';
        if ($apiKey === '') {
            return ['ok' => false, 'mode' => 'fallback', 'message' => $this->fallback($question)];
        }

        $model = getenv('OPENAI_MODEL') ?: 'gpt-5-mini';
        $profileContext = $profile ? ' Hồ sơ người dùng (chỉ dùng để cá nhân hóa, không coi là bệnh án đã xác minh): ' . json_encode($profile, JSON_UNESCAPED_UNICODE) : '';
        $input = [[
            'role' => 'system',
            'content' => [[
                'type' => 'input_text',
                'text' => 'Bạn là Healcare, trợ lý dinh dưỡng bằng tiếng Việt. Chỉ cung cấp kiến thức tham khảo, không chẩn đoán, không kê đơn, không tự thay đổi thuốc. Luôn hỏi thêm bệnh nền, thuốc đang dùng và giai đoạn điều trị khi cần. Với dấu hiệu cấp cứu, khuyên gọi 115 hoặc đi cấp cứu. Trả lời dễ đọc cho người lớn tuổi, ngắn gọn, có các mục: Nên làm, Nên hạn chế, Lưu ý. Dữ liệu nội bộ của Healcare sau đây là nguồn ưu tiên nhưng không thay thế tư vấn y khoa: ' . $this->knowledge->context() . $profileContext,
            ]],
        ]];

        foreach (array_slice($history, -6) as $message) {
            if (isset($message['role'], $message['content']) && in_array($message['role'], ['user', 'assistant'], true)) {
                $input[] = [
                    'role' => $message['role'],
                    'content' => [['type' => 'input_text', 'text' => (string) $message['content']]],
                ];
            }
        }
        $input[] = ['role' => 'user', 'content' => [['type' => 'input_text', 'text' => $question]]];

        $payload = json_encode(['model' => $model, 'input' => $input, 'store' => false], JSON_UNESCAPED_UNICODE);
        $ch = curl_init('https://api.openai.com/v1/responses');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey],
            CURLOPT_POSTFIELDS => $payload,
        ]);
        $body = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $status < 200 || $status >= 300) {
            return ['ok' => false, 'mode' => 'fallback', 'message' => $this->fallback($question), 'error' => $error ?: 'OpenAI API request failed'];
        }

        $json = json_decode($body, true);
        $text = $json['output_text'] ?? '';
        if ($text === '' && isset($json['output'])) {
            foreach ($json['output'] as $item) {
                foreach (($item['content'] ?? []) as $content) {
                    $text .= $content['text'] ?? '';
                }
            }
        }

        return $text !== ''
            ? ['ok' => true, 'mode' => 'openai', 'message' => $text]
            : ['ok' => false, 'mode' => 'fallback', 'message' => $this->fallback($question)];
    }

    public function recommend(array $profile): array
    {
        $apiKey = getenv('OPENAI_API_KEY') ?: '';
        if ($apiKey === '') {
            return ['ok' => false, 'mode' => 'fallback', 'message' => 'Hãy dùng các món đã lọc theo hồ sơ bên cạnh. Khi cấu hình OPENAI_API_KEY, AI có thể lập thực đơn cá nhân hóa hơn.'];
        }

        $model = getenv('OPENAI_MODEL') ?: 'gpt-5-mini';
        $prompt = 'Dựa trên hồ sơ người dùng sau đây, hãy đề xuất 3 ý tưởng món ăn trong ngày bằng tiếng Việt. Nêu tên món, nguyên liệu chính, cách nấu ngắn gọn và lưu ý an toàn. Không đưa định lượng điều trị, không tự thay đổi thuốc, nhắc người dùng hỏi bác sĩ khi có bệnh thận hoặc dị ứng. Hồ sơ: ' . json_encode($profile, JSON_UNESCAPED_UNICODE) . '. Kho dữ liệu món ăn Healcare: ' . $this->knowledge->context();
        $payload = json_encode(['model' => $model, 'input' => $prompt, 'store' => false], JSON_UNESCAPED_UNICODE);
        $ch = curl_init('https://api.openai.com/v1/responses');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 45, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey], CURLOPT_POSTFIELDS => $payload]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $json = json_decode((string) $body, true);
        $text = $json['output_text'] ?? '';
        if ($text === '' && isset($json['output'])) foreach ($json['output'] as $item) foreach (($item['content'] ?? []) as $content) $text .= $content['text'] ?? '';
        return $status >= 200 && $status < 300 && $text !== '' ? ['ok' => true, 'mode' => 'openai', 'message' => $text] : ['ok' => false, 'mode' => 'fallback', 'message' => 'Chưa tạo được thực đơn AI lúc này. Bạn có thể dùng các món đã được lọc theo hồ sơ.'];
    }

    private function fallback(string $question): string
    {
        $q = mb_strtolower($question, 'UTF-8');
        if (preg_match('/tiểu đường|đường|ngọt/u', $q)) {
            return 'Bạn có thể ưu tiên rau xanh, đậu, ngũ cốc nguyên hạt và trái cây nguyên miếng với khẩu phần vừa phải. Nên hạn chế nước ngọt, trà sữa và bánh kẹo. Đây là thông tin tham khảo; hãy theo kế hoạch của bác sĩ hoặc chuyên gia dinh dưỡng.';
        }
        if (preg_match('/huyết áp|muối|mặn/u', $q)) {
            return 'Hãy ưu tiên món tươi, nêm nhạt, dùng chanh, gừng và tỏi để tăng hương vị. Hạn chế đồ muối chua, đồ hộp và thịt chế biến sẵn. Mức muối phù hợp cần được cá nhân hóa.';
        }
        return 'Mình có thể hỗ trợ về thực phẩm, bệnh lý và cách chế biến. Bạn hãy ghi rõ bệnh đang điều trị cùng món ăn muốn hỏi. Khi có khó thở, đau ngực, lơ mơ hoặc hạ đường huyết, hãy gọi cấp cứu ngay.';
    }
}

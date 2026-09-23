<?php
declare(strict_types=1);

namespace Healcare\Services;

class DiseaseGeneratorService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        // Use Gemini API Key
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        // Use a Gemini model, default to gemini-1.5-pro
        $this->model = $_ENV['GEMINI_MODEL'] ?? 'gemini-1.5-pro';
    }

    public function generateDiseaseInfo(string $diseaseQuery): ?array
    {
        if (empty($this->apiKey)) {
            return null; // Cannot generate without API key
        }

        $systemPrompt = "Bạn là một chuyên gia y tế giàu kinh nghiệm. Người dùng sẽ cung cấp tên một bệnh lý hoặc triệu chứng, bạn hãy trả về thông tin chi tiết của bệnh đó bằng tiếng Việt, dưới định dạng JSON với chính xác các trường sau:
        {
          \"slug\": \"tên-bệnh-viết-thường-không-dấu-cách-nhau-bởi-dấu-gạch-ngang\",
          \"name\": \"Tên bệnh lý đầy đủ\",
          \"icon\": \"Một ký tự emoji hoặc biểu tượng text đơn giản đại diện cho bệnh (vd: ♡, ◒, ◉)\",
          \"color\": \"Màu sắc hiển thị (chọn một trong các màu: rose, mint, peach, lavender, sky, gold, cream)\",
          \"description\": \"Mô tả ngắn gọn về bệnh lý này (dưới 50 từ)\",
          \"eat\": [\"Danh sách 3-5 loại thực phẩm/lời khuyên nên ăn\"],
          \"limit_food\": [\"Danh sách 3-5 loại thực phẩm nên kiêng/hạn chế\"],
          \"symptoms\": [\"Danh sách 3-5 triệu chứng phổ biến\"],
          \"causes\": [\"Danh sách 3-5 nguyên nhân thường gặp\"],
          \"prevention\": [\"Danh sách 3-5 cách hạn chế và phòng ngừa\"],
          \"overview\": \"Giải thích chi tiết bệnh này là gì (dưới 100 từ)\",
          \"risk_factors\": [\"Danh sách yếu tố nguy cơ (ai dễ mắc bệnh)\"],
          \"monitoring\": [\"Việc cần theo dõi và tái khám\"],
          \"daily\": [\"Việc nên làm hằng ngày\"],
          \"urgent\": [\"Khi nào cần đi khám ngay lập tức\"],
          \"source\": \"Link tham khảo (tuỳ chọn, ví dụ từ WHO)\"
        }
        Chỉ trả về JSON hợp lệ, không kèm markdown code block (như ```json) hay text nào khác.";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nBệnh: " . $diseaseQuery]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json'
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $result = json_decode($response, true);
        
        // Extract text from Gemini Response
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $content = $result['candidates'][0]['content']['parts'][0]['text'];
            
            // Clean up possible markdown code blocks around JSON
            $content = preg_replace('/^```json\s*/', '', $content);
            $content = preg_replace('/```\s*$/', '', $content);
            
            $json = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($json['slug'])) {
                return $json;
            }
        }

        return null;
    }
}

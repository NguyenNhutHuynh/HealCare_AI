<?php
declare(strict_types=1);

namespace Healcare\Controllers;

use Healcare\Repositories\KnowledgeRepository;

final class PageController
{
    public function __construct(private KnowledgeRepository $knowledge) {}

    public function show(string $page): void
    {
        if ($page === 'recipes') {
            header('Location: ' . url('foods', ['tab' => 'recipes']));
            exit;
        }
        $allowed = ['home', 'diseases', 'foods', 'recipes', 'sources', 'chat', 'profile', 'recommendations', 'contact'];
        $view = in_array($page, $allowed, true) ? $page : 'home';
        $data = ['page' => $view, 'knowledge' => $this->knowledge, 'title' => $this->title($view)];
        extract($data);
        require __DIR__ . '/../Views/layout.php';
    }

    private function title(string $page): string
    {
        return ['home' => 'Trang chủ', 'diseases' => 'Tra cứu theo bệnh', 'foods' => 'Thư viện ăn uống', 'recipes' => 'Công thức món ăn', 'sources' => 'Nguồn tham khảo', 'chat' => 'Tư vấn cùng AI', 'profile' => 'Hồ sơ sức khỏe', 'recommendations' => 'Gợi ý cho bạn', 'contact' => 'Liên hệ bác sĩ'][$page];
    }
}

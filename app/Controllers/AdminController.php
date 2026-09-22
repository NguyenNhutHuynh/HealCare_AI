<?php
declare(strict_types=1);

namespace Healcare\Controllers;

use Healcare\Repositories\CrmRepository;
use Healcare\Repositories\KnowledgeRepository;
use Healcare\Services\AdminAuthService;

final class AdminController
{
    public function __construct(
        private KnowledgeRepository $knowledge,
        private CrmRepository $crm,
        private AdminAuthService $auth
    ) {}

    public function dispatch(string $page): void
    {
        if ($page === 'admin-logout') {
            $this->auth->logout();
            $this->redirect('admin-login');
        }

        if ($page === 'admin-login') {
            $this->login();
            return;
        }

        if (!$this->auth->isAuthenticated()) {
            $this->redirect('admin-login');
        }

        if ($page === 'admin-contact-update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateContact();
            return;
        }

        $view = match ($page) {
            'admin-contacts' => 'contacts',
            'admin-content' => 'content',
            default => 'dashboard',
        };

        $data = [
            'page' => $page,
            'adminUser' => $this->auth->username(),
            'csrf' => $this->auth->csrfToken(),
            'stats' => $this->crm->stats(),
            'recentContacts' => $this->crm->allContacts('', 'all'),
            'contacts' => $this->crm->allContacts(
                (string) ($_GET['q'] ?? ''),
                (string) ($_GET['status'] ?? 'all')
            ),
            'search' => (string) ($_GET['q'] ?? ''),
            'statusFilter' => (string) ($_GET['status'] ?? 'all'),
            'counts' => [
                'diseases' => count($this->knowledge->diseases()),
                'foods' => count($this->knowledge->foods()),
                'recipes' => count($this->knowledge->recipes()),
                'sources' => count($this->knowledge->contacts()),
            ],
            'knowledge' => $this->knowledge,
        ];

        $this->render($view, $data);
    }

    private function login(): void
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = (string) ($_POST['csrf'] ?? '');
            if (!$this->auth->verifyCsrf($token)) {
                $error = 'Phiên đăng nhập không hợp lệ. Vui lòng thử lại.';
            } elseif ($this->auth->login((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''))) {
                $this->redirect('admin');
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu chưa đúng.';
            }
        }

        $this->renderGuest('login', [
            'csrf' => $this->auth->csrfToken(),
            'error' => $error,
        ]);
    }

    private function updateContact(): void
    {
        if (!$this->auth->verifyCsrf((string) ($_POST['csrf'] ?? ''))) {
            $_SESSION['admin_error'] = 'Phiên quản trị đã hết hạn. Vui lòng tải lại trang.';
            $this->redirect('admin-contacts');
        }

        $updated = $this->crm->updateContact(
            (string) ($_POST['id'] ?? ''),
            (string) ($_POST['status'] ?? 'new'),
            (string) ($_POST['priority'] ?? 'normal'),
            (string) ($_POST['notes'] ?? '')
        );
        $_SESSION[$updated ? 'admin_success' : 'admin_error'] = $updated
            ? 'Đã cập nhật yêu cầu liên hệ.'
            : 'Không tìm thấy yêu cầu cần cập nhật.';
        $this->redirect('admin-contacts');
    }

    private function render(string $view, array $data): void
    {
        extract($data);
        require __DIR__ . '/../Views/admin/layout.php';
    }

    private function renderGuest(string $view, array $data): void
    {
        extract($data);
        require __DIR__ . '/../Views/admin/guest_layout.php';
    }

    private function redirect(string $page): never
    {
        header('Location: ' . url($page));
        exit;
    }
}

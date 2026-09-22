<?php
declare(strict_types=1);

namespace Healcare\Controllers;

use Healcare\Repositories\CrmRepository;

final class ContactController
{
    public function __construct(private CrmRepository $crm) {}

    public function submit(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($name === '' || $phone === '' || $message === '') {
            $_SESSION['contact_error'] = 'Vui lòng điền họ tên, số điện thoại và nội dung cần hỗ trợ.';
        } else {
            $this->crm->createContact([
                'name' => $name,
                'phone' => $phone,
                'message' => $message,
                'source' => 'contact-page',
            ]);
            $email = getenv('HEALCARE_CONTACT_EMAIL') ?: '';
            if ($email !== '') {
                @mail(
                    $email,
                    'Yêu cầu liên hệ Healcare',
                    "Họ tên: $name\nSố điện thoại: $phone\nNội dung: $message"
                );
                $_SESSION['contact_success'] = 'Đã gửi yêu cầu liên hệ. Bộ phận tiếp nhận sẽ phản hồi theo thông tin bạn cung cấp.';
            } else {
                $_SESSION['contact_success'] = 'Đã ghi nhận yêu cầu trên hệ thống. Vui lòng cấu hình hộp thư tiếp nhận để nhân viên có thể phản hồi tự động.';
            }
        }
        header('Location: index.php?page=contact');
    }
}

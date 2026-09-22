<?php
declare(strict_types=1);

require __DIR__ . '/app/Core/bootstrap.php';

use Healcare\Controllers\ChatController;
use Healcare\Controllers\AdminController;
use Healcare\Controllers\ContactController;
use Healcare\Controllers\PageController;
use Healcare\Controllers\ProfileController;
use Healcare\Controllers\RecommendationController;
use Healcare\Repositories\CrmRepository;
use Healcare\Repositories\KnowledgeRepository;
use Healcare\Services\AdminAuthService;

$page = (string) ($_GET['page'] ?? 'home');
$knowledge = new KnowledgeRepository();
$crm = new CrmRepository();

if (str_starts_with($page, 'admin')) {
    (new AdminController($knowledge, $crm, new AdminAuthService()))->dispatch($page);
    exit;
}

if ($page === 'chat' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new ChatController($knowledge))->reply();
    exit;
}

if ($page === 'profile' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new ProfileController())->save();
    exit;
}

if ($page === 'recommendations' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecommendationController($knowledge))->generate();
    exit;
}

if ($page === 'contact' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new ContactController($crm))->submit();
    exit;
}

(new PageController($knowledge))->show($page);

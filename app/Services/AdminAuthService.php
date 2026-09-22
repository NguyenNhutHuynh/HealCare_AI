<?php
declare(strict_types=1);

namespace Healcare\Services;

final class AdminAuthService
{
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['admin_user']) && is_string($_SESSION['admin_user']);
    }

    public function username(): string
    {
        return (string) ($_SESSION['admin_user'] ?? '');
    }

    public function login(string $username, string $password): bool
    {
        $expectedUsername = getenv('HEALCARE_ADMIN_USER') ?: 'admin';
        $expectedPassword = getenv('HEALCARE_ADMIN_PASSWORD') ?: 'Healcare@2026';

        if (!hash_equals($expectedUsername, trim($username)) || !hash_equals($expectedPassword, $password)) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_user'] = $expectedUsername;
        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['admin_user'], $_SESSION['admin_csrf']);
        session_regenerate_id(true);
    }

    public function csrfToken(): string
    {
        if (empty($_SESSION['admin_csrf'])) {
            $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION['admin_csrf'];
    }

    public function verifyCsrf(string $token): bool
    {
        return $token !== ''
            && isset($_SESSION['admin_csrf'])
            && hash_equals((string) $_SESSION['admin_csrf'], $token);
    }
}

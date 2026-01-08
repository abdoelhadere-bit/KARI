<?php
declare(strict_types=1);

namespace utils;

use exceptions\PermissionDeniedException;

class Guard
{
    public static function requireLogin(): void
    {
        Session::start();

        if (!Session::has('user_id')) {
            header("Location: index.php?page=login");
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();

        if (Session::get('role') !== $role) {
            throw new PermissionDeniedException("Accès refusé (rôle requis: $role).");
        }
    }

    public static function requireAnyRole(array $roles): void
    {
        self::requireLogin();

        $current = (string) Session::get('role');
        if (!in_array($current, $roles, true)) {
            throw new PermissionDeniedException("Accès refusé.");
        }
    }
}

<?php
declare(strict_types=1);

namespace services;

use core\Database;
use entities\User;
use utils\Session;

class AuthService
{
    public function register(string $name, string $email, string $password, string $role): array
    {
        $name = trim($name);
        $email = trim($email);

        if ($name === '' || $email === '' || $password === '') {
            return ['ok' => false, 'error' => "Tous les champs sont obligatoires."];
        }

        $Roles = ['traveler', 'host', 'admin'];
        if (!in_array($role, $Roles, true)) {
            return ['ok' => false, 'error' => "Rôle invalide."];
        }

        $pdo = Database::getConnection();
        $userModel = new User($pdo);

        if ($userModel->findByEmail($email)) {
            return ['ok' => false, 'error' => "Email déjà utilisé."];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = $userModel->create($name, $email, $hash, $role);

        return ['ok' => true, 'user_id' => $userId];
    }

    public function login(string $email, string $password): bool
    {
        $pdo = Database::getConnection();
        $userModel = new User($pdo);

        $userModel->setCredentials($email, $password);
        return $userModel->login();
    }

    public function logout(): void
    {
        Session::destroy();
    }
}

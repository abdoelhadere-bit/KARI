<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\UserRepository;
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

        $roles = ['traveler', 'host', 'admin'];
        if (!in_array($role, $roles, true)) {
            return ['ok' => false, 'error' => "Rôle invalide."];
        }

        $pdo = Database::getConnection();
        $repo = new UserRepository($pdo);

        if ($repo->emailExists($email)) {
            return ['ok' => false, 'error' => "Email déjà utilisé."];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = $repo->create($name, $email, $hash, $role);

        return ['ok' => true, 'user_id' => $userId];
    }

    public function login(string $email, string $password): bool
    {
        $pdo = Database::getConnection();
        $repo = new UserRepository($pdo);

        $email = trim($email);
        $password = (string)$password;

        $user = $repo->findByEmail($email);
        if (!$user) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

  
        $hash = (string)($user['password_hash'] ?? $user['password'] ?? '');

        if ($hash === '' || !password_verify($password, $hash)) {
            return false;
        }

        Session::start();
        Session::set('user_id', (int)$user['id']);
        Session::set('role', (string)$user['role']);
        Session::set('user_name', (string)$user['name']);
        Session::set('user_email', (string)$user['email']);

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }
}

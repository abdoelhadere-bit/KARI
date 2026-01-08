<?php
declare(strict_types=1);

namespace entities;

use PDO;
use utils\Session;

class User
{
    private PDO $pdo;

    private string $email = '';
    private string $password = '';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Pour préparer le login()
    public function setCredentials(string $email, string $password): void
    {
        $this->email = trim($email);
        $this->password = $password;
    }

    public function create(string $name, string $email, string $hashPassword, string $role = 'traveler'): int
    {
        $sql = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $email, $hashPassword, $role]);

        return (int)$this->pdo->lastInsertId();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, role, status, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([trim($email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function login(): bool
    {
        Session::start();

        $user = $this->findByEmail($this->email);

        if (!$user) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        if (!password_verify($this->password, $user['password_hash'])) {
            return false;
        }

        Session::set('user_id', (int)$user['id']);
        Session::set('role', $user['role']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);

        return true;
    }
}

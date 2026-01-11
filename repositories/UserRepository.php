<?php
declare(strict_types=1);

namespace repositories;

use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT id, name, email, password, created_at
                FROM users
                WHERE id = :id
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, name, email, password, created_at
                FROM users
                WHERE email = :email
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateProfile(int $id, string $name, string $email): bool
    {
        $sql = "UPDATE users
                SET name = :name, email = :email
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
        ]);
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $sql = "UPDATE users
                SET password = :password
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':password' => $hash,
        ]);
    }

    public function emailExists(string $email, int $ignoreId = 0): bool
    {
        $sql = "SELECT 1
                FROM users
                WHERE email = :email
                  AND id <> :ignoreId
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':ignoreId' => $ignoreId,
        ]);

        return (bool) $stmt->fetchColumn();
    }
}

<?php
declare(strict_types=1);

namespace repositories;

use PDO;
use entities\User;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $row): User
    {
        return User::fromArray($row);
    }

    public function findById(int $id): ?User
    {
        $sql = "SELECT id, name, email, password_hash, role, status, created_at
                FROM users
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, name, email, password_hash, role, status, created_at
                FROM users
                WHERE email = :email
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
    
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    public function create(string $name, string $email, string $hash, string $role): int
    {
        $sql = "INSERT INTO users (name, email, password_hash, role, status)
                VALUES (:name, :email, :hash, :role, 'active')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':hash' => $hash,
            ':role' => $role,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateProfile(User $user): bool
    {
        if ($user->getId() === null) return false;

        $sql = "UPDATE users
                SET name = :name, email = :email
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $user->getId(),
            ':name' => $user->getName(),
            ':email' => $user->getEmail(),
        ]);
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $sql = "UPDATE users
                SET password_hash = :hash
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':hash' => $hash,
        ]);
    }

    public function setStatus(int $id, string $status): bool
    {
        $allowed = ['active', 'disabled'];
        if (!in_array($status, $allowed, true)) return false;

        $sql = "UPDATE users
                SET status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
        ]);
    }

    public function emailExists(string $email, int $ignoreId = 0): bool
    {
        $sql = "SELECT 1 FROM users WHERE email = :email AND id <> :ignoreId LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email, ':ignoreId' => $ignoreId]);
        return (bool)$stmt->fetchColumn();
    }
}

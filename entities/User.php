<?php
declare(strict_types=1);

namespace entities;

final class User
{
    private ?int $id = null;

    private string $name = '';
    private string $email = '';
    private string $passwordHash = '';

    private string $role = 'traveler';   
    private string $status = 'active';  
    private ?string $createdAt = null;

    public static function fromArray(array $row): self
    {
        $u = new self();

        $u->id = isset($row['id']) ? (int)$row['id'] : null;
        $u->name = (string)($row['name'] ?? '');
        $u->email = (string)($row['email'] ?? '');

        $u->passwordHash = (string)($row['password_hash'] ?? '');

        $u->role = (string)($row['role'] ?? 'traveler');
        $u->status = (string)($row['status'] ?? 'active');
        $u->createdAt = isset($row['created_at']) ? (string)$row['created_at'] : null;

        return $u;
    }

    public function setId(int $id): void { $this->id = $id; }

    public function setName(string $name): void { $this->name = trim($name); }
    public function setEmail(string $email): void { $this->email = trim($email); }

    public function setRole(string $role): void { $this->role = $role; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function setPasswordPlain(string $plainPassword): void
    {
        $this->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getRole(): string { return $this->role; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    public function toInsertArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password_hash' => $this->passwordHash,
            'role' => $this->role,
        ];
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->passwordHash);
    }
}

<?php
declare(strict_types=1);

namespace repositories;

use PDO;

class FavoriteRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function exists(int $userId, int $rentalId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND rental_id = ? LIMIT 1");
        $stmt->execute([$userId, $rentalId]);
        return (bool)$stmt->fetchColumn();
    }

    public function add(int $userId, int $rentalId): void
    {
        if ($this->exists($userId, $rentalId)) {
            return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO favorites (user_id, rental_id) VALUES (?, ?)");
        $stmt->execute([$userId, $rentalId]);
    }

    public function remove(int $userId, int $rentalId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND rental_id = ?");
        $stmt->execute([$userId, $rentalId]);
    }

    public function listByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT f.rental_id, f.user_id, r.title, r.city, r.price_per_night
                                     FROM favorites f
                                     JOIN rentals r ON r.id = f.rental_id
                                     WHERE f.user_id = ?
                                     ORDER BY f.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

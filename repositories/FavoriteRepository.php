<?php
declare(strict_types=1);

namespace repositories;

use PDO;
use entities\Favorite;
use entities\FavoriteRental;

final class FavoriteRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function exists(int $userId, int $rentalId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM favorites WHERE user_id = ? AND rental_id = ? LIMIT 1"
        );
        $stmt->execute([$userId, $rentalId]);
        return (bool) $stmt->fetchColumn();
    }

    public function add(int $userId, int $rentalId): Favorite
    {
        if ($this->exists($userId, $rentalId)) {
            return new Favorite($userId, $rentalId, null);
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO favorites (user_id, rental_id) VALUES (?, ?)"
        );
        $stmt->execute([$userId, $rentalId]);

        return new Favorite($userId, $rentalId, null);
    }

    public function remove(int $userId, int $rentalId): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM favorites WHERE user_id = ? AND rental_id = ?"
        );
        $stmt->execute([$userId, $rentalId]);
    }

 
    public function listByUser(int $userId): array
    {
        $sql = "SELECT
                  f.user_id,
                  f.rental_id,
                  f.created_at,
                  r.title,
                  r.city,
                  r.price_per_night
                FROM favorites f
                JOIN rentals r ON r.id = f.rental_id
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $out[] = new FavoriteRental(
                (int) $row['user_id'],
                (int) $row['rental_id'],
                (string) $row['title'],
                (string) $row['city'],
                (float) $row['price_per_night'],
                isset($row['created_at']) ? (string) $row['created_at'] : null
            );
        }

        return $out;
    }
}

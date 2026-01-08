<?php
declare(strict_types=1);

namespace repositories;

use PDO;

class ReviewRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $rentalId, int $userId, int $rating, string $comment): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO reviews (rental_id, user_id, rating, comment)
                                     VALUES (?, ?, ?, ?)");
        $stmt->execute([$rentalId, $userId, $rating, $comment]);
        return (int)$this->pdo->lastInsertId();
    }

    public function listByRental(int $rentalId): array
    {
        $stmt = $this->pdo->prepare("SELECT rv.*, u.name AS user_name
                                     FROM reviews rv
                                     JOIN users u ON u.id = rv.user_id
                                     WHERE rv.rental_id = ?
                                     ORDER BY rv.created_at DESC");
        $stmt->execute([$rentalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function averageByRental(int $rentalId): float
    {
        $stmt = $this->pdo->prepare("SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE rental_id = ?");
        $stmt->execute([$rentalId]);
        return (float)$stmt->fetchColumn();
    }

    public function alreadyReviewed(int $rentalId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM reviews WHERE rental_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$rentalId, $userId]);
        return (bool)$stmt->fetchColumn();
    }

    public function canReview(int $rentalId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1
                                     FROM reservations
                                     WHERE rental_id = ?
                                     AND traveler_id = ?
                                     AND status = 'booked'
                                     AND end_date < CURDATE()
                                     LIMIT 1");
        $stmt->execute([$rentalId, $userId]);
        return (bool)$stmt->fetchColumn();
    }
}

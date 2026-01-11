<?php
declare(strict_types=1);

namespace repositories;

use PDO;
use entities\Review;

final class ReviewRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Review $draft): Review
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO reviews (rental_id, user_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $draft->getRentalId(),
            $draft->getUserId(),
            $draft->getRating(),
            $draft->getComment(),
        ]);

        $id = (int)$this->pdo->lastInsertId();
        $created = $this->findById($id);
        if (!$created) {
            return $draft;
        }
        return $created;
    }

    public function findById(int $id): ?Review
    {
        $stmt = $this->pdo->prepare("
            SELECT rv.id, rv.rental_id, rv.user_id, rv.rating, rv.comment, rv.created_at,
                   u.name AS user_name
            FROM reviews rv
            JOIN users u ON u.id = rv.user_id
            WHERE rv.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Review::fromRow($row) : null;
    }

    public function listByRental(int $rentalId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT rv.id, rv.rental_id, rv.user_id, rv.rating, rv.comment, rv.created_at,
                   u.name AS user_name
            FROM reviews rv
            JOIN users u ON u.id = rv.user_id
            WHERE rv.rental_id = ?
            ORDER BY rv.created_at DESC
        ");
        $stmt->execute([$rentalId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $out[] = Review::fromRow($row);
        }
        return $out;
    }

    public function averageByRental(int $rentalId): float
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(AVG(rating), 0)
            FROM reviews
            WHERE rental_id = ?
        ");
        $stmt->execute([$rentalId]);
        return (float)$stmt->fetchColumn();
    }

    public function alreadyReviewed(int $rentalId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM reviews
            WHERE rental_id = ?
              AND user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$rentalId, $userId]);
        return (bool)$stmt->fetchColumn();
    }

    public function canReview(int $rentalId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM reservations
            WHERE rental_id = ?
              AND traveler_id = ?
              AND status = 'booked'
              AND end_date IS NOT NULL
              AND end_date < CURDATE()
            LIMIT 1
        ");
        $stmt->execute([$rentalId, $userId]);
        return (bool)$stmt->fetchColumn();
    }
}

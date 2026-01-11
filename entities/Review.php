<?php
declare(strict_types=1);

namespace entities;

final class Review
{
    private int $id;
    private int $rentalId;
    private int $userId;
    private int $rating;
    private string $comment;
    private string $createdAt;

    private string $userName;

    private function __construct(
        int $id,
        int $rentalId,
        int $userId,
        int $rating,
        string $comment,
        string $createdAt,
        string $userName
    ) {
        $this->id        = $id;
        $this->rentalId  = $rentalId;
        $this->userId    = $userId;
        $this->rating    = $rating;
        $this->comment   = $comment;
        $this->createdAt = $createdAt;
        $this->userName  = $userName;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int)($row['id'] ?? 0),
            (int)($row['rental_id'] ?? 0),
            (int)($row['user_id'] ?? 0),
            (int)($row['rating'] ?? 0),
            (string)($row['comment'] ?? ''),
            (string)($row['created_at'] ?? ''),
            (string)($row['user_name'] ?? '') 
        );
    }

    public static function draft(int $rentalId, int $userId, int $rating, string $comment): self
    {
        return new self(
            0,
            $rentalId,
            $userId,
            $rating,
            trim($comment),
            '',
            ''
        );
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getRentalId(): int { return $this->rentalId; }
    public function getUserId(): int { return $this->userId; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): string { return $this->comment; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUserName(): string { return $this->userName; }

    public function stars(): string
    {
        $full = max(0, min(5, $this->rating));
        return str_repeat('★', $full) . str_repeat('☆', 5 - $full);
    }
}

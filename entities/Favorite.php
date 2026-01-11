<?php
declare(strict_types=1);

namespace entities;

final class Favorite
{
    private int $userId;
    private int $rentalId;
    private ?string $createdAt;

    public function __construct(int $userId, int $rentalId, ?string $createdAt = null)
    {
        $this->userId = $userId;
        $this->rentalId = $rentalId;
        $this->createdAt = $createdAt;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRentalId(): int
    {
        return $this->rentalId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}

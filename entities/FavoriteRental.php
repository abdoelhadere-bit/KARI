<?php
declare(strict_types=1);

namespace entities;

final class FavoriteRental
{
    private int $userId;
    private int $rentalId;
    private string $title;
    private string $city;
    private float $pricePerNight;
    private ?string $createdAt;

    public function __construct(
        int $userId,
        int $rentalId,
        string $title,
        string $city,
        float $pricePerNight,
        ?string $createdAt = null
    ) {
        $this->userId = $userId;
        $this->rentalId = $rentalId;
        $this->title = $title;
        $this->city = $city;
        $this->pricePerNight = $pricePerNight;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPricePerNight(): float
    {
        return $this->pricePerNight;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}

<?php
declare(strict_types=1);

namespace entities;

final class Rental
{
    private int $id;
    private int $hostId;
    private string $title;
    private string $city;
    private ?string $address;
    private float $pricePerNight;
    private int $maxGuests;
    private ?string $description;
    private ?string $image;
    private string $status;
    private ?string $hostName;

    public function __construct(
        int $id,
        int $hostId,
        string $title,
        string $city,
        float $pricePerNight,
        int $maxGuests,
        ?string $address = null,
        ?string $description = null,
        ?string $image = null,
        string $status = 'active',
        ?string $hostName = null
    ) {
        $this->id = $id;
        $this->hostId = $hostId;
        $this->title = $title;
        $this->city = $city;
        $this->pricePerNight = $pricePerNight;
        $this->maxGuests = $maxGuests;
        $this->address = $address;
        $this->description = $description;
        $this->image = $image;
        $this->status = $status;
        $this->hostName = $hostName;
    }

    public function getId(): int { return $this->id; }
    public function getHostId(): int { return $this->hostId; }
    public function getTitle(): string { return $this->title; }
    public function getCity(): string { return $this->city; }
    public function getAddress(): ?string { return $this->address; }
    public function getPricePerNight(): float { return $this->pricePerNight; }
    public function getMaxGuests(): int { return $this->maxGuests; }
    public function getDescription(): ?string { return $this->description; }
    public function getImage(): ?string { return $this->image; }
    public function getStatus(): string { return $this->status; }
    public function getHostName(): ?string { return $this->hostName; }
}

<?php
declare(strict_types=1);

namespace entities;

final class Reservation
{
    private ?int $id;
    private int $rentalId;
    private int $travelerId;
    private string $startDate;   
    private string $endDate;     
    private int $guests;
    private float $totalPrice;
    private string $status;      
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        int $rentalId,
        int $travelerId,
        string $startDate,
        string $endDate,
        int $guests,
        float $totalPrice,
        string $status = 'booked',
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->rentalId = $rentalId;
        $this->travelerId = $travelerId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->guests = $guests;
        $this->totalPrice = $totalPrice;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            isset($row['id']) ? (int)$row['id'] : null,
            (int)$row['rental_id'],
            (int)$row['traveler_id'],
            (string)$row['start_date'],
            (string)$row['end_date'],
            (int)$row['guests'],
            (float)$row['total_price'],
            (string)$row['status'],
            isset($row['created_at']) ? (string)$row['created_at'] : null
        );
    }

    public function getId(): ?int { return $this->id; }
    public function getRentalId(): int { return $this->rentalId; }
    public function getTravelerId(): int { return $this->travelerId; }
    public function getStartDate(): string { return $this->startDate; }
    public function getEndDate(): string { return $this->endDate; }
    public function getGuests(): int { return $this->guests; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    public function isBooked(): bool
    {
        return $this->status === 'booked';
    }

    public function isOwner(int $userId): bool
    {
        return $this->travelerId === $userId;
    }
}

<?php
declare(strict_types=1);

namespace repositories;

use PDO;

class ReservationRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res ?: null;
    }

    // Vérifie sil y a un conflit de dates
    public function hasConflict(int $rentalId, string $start, string $end): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM reservations
                                     WHERE rental_id = ?
                                     AND status = 'booked'
                                     AND NOT (end_date <= ? OR start_date >= ?)
                                     LIMIT 1 ");

        $stmt->execute([$rentalId, $start, $end]);
        return (bool)$stmt->fetchColumn();
    }

    public function create(int $rentalId, int $travelerId, string $start, string $end, int $guests, float $total): int 
    {
        $stmt = $this->pdo->prepare("INSERT INTO reservations (rental_id, traveler_id, start_date, end_date, guests, total_price, status)
                                     VALUES (?, ?, ?, ?, ?, ?, 'booked') ");

        $stmt->execute([$rentalId, $travelerId, $start, $end, $guests, $total]);

        return (int)$this->pdo->lastInsertId();
    }

    public function cancel(int $reservationId): void
    {
        $stmt = $this->pdo->prepare("UPDATE reservations SET status='cancelled' WHERE id = ?");
        $stmt->execute([$reservationId]);
    }


    // Liste des réservations d'un voyageur
    public function listByTraveler(int $travelerId): array
    {
        $stmt = $this->pdo->prepare("SELECT res.*, r.title AS rental_title, r.city  AS rental_city
                                     FROM reservations res
                                     JOIN rentals r ON r.id = res.rental_id
                                     WHERE res.traveler_id = ?
                                     ORDER BY res.created_at DESC ");

        $stmt->execute([$travelerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hostEmailByReservation(int $reservationId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT u.email AS host_email, r.title
                                     FROM reservations res
                                     JOIN rentals r ON r.id = res.rental_id
                                     JOIN users u ON u.id = r.host_id
                                     WHERE res.id = ?
                                     LIMIT 1");
        $stmt->execute([$reservationId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function receiptData(int $reservationId): ?array
{
    $stmt = $this->pdo->prepare("SELECT res.id, res.start_date, res.end_date, res.guests, res.total_price, res.status, res.created_at,
                                        r.title AS rental_title, r.city  AS rental_city, r.address AS rental_address, r.price_per_night,    
                                        host.name  AS host_name, host.email AS host_email, trav.name  AS traveler_name, trav.email AS traveler_email

                                 FROM reservations res
                                 JOIN rentals r ON r.id = res.rental_id
                                 JOIN users host ON host.id = r.host_id
                                 JOIN users trav ON trav.id = res.traveler_id
                                 WHERE res.id = ?
                                 LIMIT 1");
    $stmt->execute([$reservationId]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}



}

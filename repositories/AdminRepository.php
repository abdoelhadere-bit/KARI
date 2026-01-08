<?php
declare(strict_types=1);

namespace repositories;

use PDO;

class AdminRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function countUsers(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function countRentals(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM rentals")->fetchColumn();
    }

    public function countReservations(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
    }

    public function totalRevenue(): float
    {
        $sql = "SELECT COALESCE(SUM(total_price),0) FROM reservations WHERE status='booked'";
        return (float)$this->pdo->query($sql)->fetchColumn();
    }

    public function topRentals(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("SELECT r.id, r.title, r.city, COALESCE(SUM(res.total_price), 0) AS revenue
                                     FROM rentals r LEFT JOIN reservations res
                                     ON res.rental_id = r.id AND res.status = 'booked'
                                     GROUP BY r.id, r.title, r.city
                                     ORDER BY revenue DESC
                                     LIMIT ? ");
                    
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listUsers(): array
    {
        return $this->pdo->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listRentals(): array
    {
        return $this->pdo->query("SELECT r.id, r.title, r.city, r.status, r.host_id, u.name AS host_name
                FROM rentals r
                JOIN users u ON u.id = r.host_id
                ORDER BY r.created_at DESC ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setUserStatus(int $userId, string $status): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET status=? WHERE id=?");
        $stmt->execute([$status, $userId]);
    }

    public function setRentalStatus(int $rentalId, string $status): void
    {
        $stmt = $this->pdo->prepare("UPDATE rentals SET status=? WHERE id=?");
        $stmt->execute([$status, $rentalId]);
    }

    public function listReservations(int $limit = 20): array
    {
        $stmt = $this->pdo->prepare("SELECT res.id, res.start_date, res.end_date, res.guests, res.total_price, res.status, res.created_at,
                                     r.title AS rental_title, trav.name AS traveler_name
                                     FROM reservations res
                                     JOIN rentals r ON r.id = res.rental_id
                                     JOIN users trav ON trav.id = res.traveler_id
                                     ORDER BY res.created_at DESC
                                     LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

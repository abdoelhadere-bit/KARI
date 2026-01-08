<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\AdminRepository;
use utils\Guard;

class AdminService
{
    private AdminRepository $repo;

    public function __construct()
    {
        Guard::requireRole('admin');
        $this->repo = new AdminRepository(Database::getConnection());
    }

    public function stats(): array
    {
        return [
            'users' => $this->repo->countUsers(),
            'rentals' => $this->repo->countRentals(),
            'reservations' => $this->repo->countReservations(),
            'revenue' => $this->repo->totalRevenue(),
            'topRentals' => $this->repo->topRentals(10),
        ];
    }

    public function users(): array
    {
        return $this->repo->listUsers();
    }

    public function rentals(): array
    {
        return $this->repo->listRentals();
    }

    public function toggleUser(int $userId, string $status): void
    {
        $allowed = ['active','disabled'];
        if (!in_array($status, $allowed, true)) return;
        $this->repo->setUserStatus($userId, $status);
    }

    public function toggleRental(int $rentalId, string $status): void
    {
        $allowed = ['active','disabled'];
        if (!in_array($status, $allowed, true)) return;
        $this->repo->setRentalStatus($rentalId, $status);
    }

    public function reservations(int $limit = 20): array
    {
        \utils\Guard::requireRole('admin');
    
        $pdo = \core\Database::getConnection();
        $repo = new \repositories\AdminRepository($pdo);
    
        return $repo->listReservations($limit);
    }

}

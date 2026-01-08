<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\RentalRepository;

class RentalService
{
    private RentalRepository $repo;

    public function __construct()
    {
        $pdo = Database::getConnection();
        $this->repo = new RentalRepository($pdo);
    }

    public function listActive(int $page = 1, int $perPage = 6): array
    {
        return $this->repo->listActive($page, $perPage);
    }

    public function getDetails(int $rentalId): ?array
    {
        return $this->repo->findById($rentalId);
    }

    public function search(array $filters, int $page = 1, int $perPage = 6): array
    {
        return $this->repo->search($filters, $page, $perPage);
    }

}

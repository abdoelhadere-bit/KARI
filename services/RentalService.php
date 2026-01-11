<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\RentalRepository;
use entities\Rental;
use exceptions\NotFoundException;

final class RentalService
{
    private RentalRepository $repo;

    public function __construct()
    {
        $this->repo = new RentalRepository(Database::getConnection());
    }

    public function listActive(int $page, int $perPage): array
    {
        return $this->repo->listActive($page, $perPage);
    }

    public function search(array $filters, int $page, int $perPage): array
    {
        return $this->repo->search($filters, $page, $perPage);
    }

    public function getDetails(int $id): Rental
    {
        $r = $this->repo->findById($id);
        if (!$r) {
            throw new NotFoundException("Logement introuvable.");
        }
        return $r;
    }
}

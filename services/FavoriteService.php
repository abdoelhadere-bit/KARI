<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\FavoriteRepository;
use exceptions\PermissionDeniedException;
use utils\Session;
use entities\FavoriteRental;

final class FavoriteService
{
    private FavoriteRepository $repo;

    public function __construct()
    {
        Session::start();
        $pdo = Database::getConnection();
        $this->repo = new FavoriteRepository($pdo);
    }

    private function requireLogin(): int
    {
        $userId = (int) Session::get('user_id', 0);
        if ($userId <= 0) {
            throw new PermissionDeniedException("Vous devez être connecté.");
        }
        return $userId;
    }

    public function isFavorite(int $rentalId): bool
    {
        $userId = $this->requireLogin();
        return $this->repo->exists($userId, $rentalId);
    }

    public function toggle(int $rentalId): bool
    {
        $userId = $this->requireLogin();

        if ($this->repo->exists($userId, $rentalId)) {
            $this->repo->remove($userId, $rentalId);
            return false;
        }

        $this->repo->add($userId, $rentalId);
        return true;
    }

    public function myFavorites(): array
    {
        $userId = $this->requireLogin();
        return $this->repo->listByUser($userId);
    }
}

<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\ReviewRepository;
use exceptions\PermissionDeniedException;
use exceptions\NotFoundException;
use utils\Session;

class ReviewService
{
    private ReviewRepository $repo;

    public function __construct()
    {
        Session::start();
        $this->repo = new ReviewRepository(Database::getConnection());
    }

    private function requireLogin(): int
    {
        $userId = (int)Session::get('user_id', 0);
        if ($userId <= 0) {
            throw new PermissionDeniedException("Vous devez être connecté.");
        }
        return $userId;
    }

    public function create(int $rentalId, int $rating, string $comment): int
    {
        $userId = $this->requireLogin();

        if ($rentalId <= 0) {
            throw new NotFoundException("Logement introuvable.");
        }

        if ($rating < 1 || $rating > 5) {
            throw new PermissionDeniedException("La note doit être entre 1 et 5.");
        }

        if ($this->repo->alreadyReviewed($rentalId, $userId)) {
            throw new PermissionDeniedException("Vous avez déjà laissé un avis.");
        }

        if (!$this->repo->canReview($rentalId, $userId)) {
            throw new PermissionDeniedException("Vous pouvez laisser un avis فقط بعد انتهاء séjour.");
        }

        return $this->repo->create($rentalId, $userId, $rating, $comment);
    }

    public function listByRental(int $rentalId): array
    {
        return $this->repo->listByRental($rentalId);
    }

    public function avgByRental(int $rentalId): float
    {
        return $this->repo->averageByRental($rentalId);
    }
}

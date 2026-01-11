<?php
declare(strict_types=1);

namespace services;

use core\Database;
use entities\Review;
use repositories\ReviewRepository;
use exceptions\PermissionDeniedException;
use exceptions\NotFoundException;
use utils\Session;

final class ReviewService
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

    public function create(int $rentalId, int $rating, string $comment): Review
    {
        $userId = $this->requireLogin();

        $comment = trim($comment);

        if ($rentalId <= 0) {
            throw new NotFoundException("Logement introuvable.");
        }

        if ($rating < 1 || $rating > 5) {
            throw new PermissionDeniedException("La note doit être entre 1 et 5.");
        }

        if ($comment === '') {
            throw new PermissionDeniedException("Le commentaire est obligatoire.");
        }

        if ($this->repo->alreadyReviewed($rentalId, $userId)) {
            throw new PermissionDeniedException("Vous avez déjà laissé un avis.");
        }

        if (!$this->repo->canReview($rentalId, $userId)) {
            throw new PermissionDeniedException("Vous pouvez laisser un avis uniquement après la fin du séjour.");
        }

        $draft = Review::draft($rentalId, $userId, $rating, $comment);
        return $this->repo->create($draft);
    }

    public function listByRental(int $rentalId): array
    {
        if ($rentalId <= 0) return [];
        return $this->repo->listByRental($rentalId);
    }

    public function avgByRental(int $rentalId): float
    {
        if ($rentalId <= 0) return 0.0;
        return $this->repo->averageByRental($rentalId);
    }
}

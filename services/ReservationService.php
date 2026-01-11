<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\ReservationRepository;
use repositories\RentalRepository;
use entities\Reservation;
use exceptions\DateConflictException;
use exceptions\NotFoundException;
use exceptions\PermissionDeniedException;
use utils\Session;

class ReservationService
{
    public function book(int $rentalId, string $start, string $end, int $guests): int
    {
        Session::start();

        $userId = (int) Session::get('user_id', 0);
        if ($userId <= 0) {
            throw new PermissionDeniedException("Vous devez être connecté.");
        }

        if ($rentalId <= 0) {
            throw new NotFoundException("Logement introuvable.");
        }

        if ($start === '' || $end === '') {
            throw new DateConflictException("Dates invalides.");
        }

        $startDt = new \DateTime($start);
        $endDt   = new \DateTime($end);
        $today   = new \DateTime('today');

        if ($endDt <= $startDt || $startDt < $today) {
            throw new DateConflictException("Dates invalides.");
        }

        $pdo = Database::getConnection();

        $rentalRepo = new RentalRepository($pdo);
        $rental = $rentalRepo->findById($rentalId);
        if (!$rental) {
            throw new NotFoundException("Logement introuvable.");
        }

        $maxGuests = (int) $rental['max_guests'];
        if ($guests <= 0 || $guests > $maxGuests) {
            throw new DateConflictException("Nombre de guests invalide.");
        }

        $repo = new ReservationRepository($pdo);
        if ($repo->hasConflict($rentalId, $start, $end)) {
            throw new DateConflictException("Ce logement est déjà réservé sur ces dates.");
        }

        $nights = $startDt->diff($endDt)->days;
        if ($nights <= 0) {
            throw new DateConflictException("La durée doit être au moins 1 nuit.");
        }

        $price = (float) $rental['price_per_night'];
        $total = $nights * $price;

        $reservation = new Reservation(
            null,
            $rentalId,
            $userId,
            $start,
            $end,
            $guests,
            $total,
            'booked'
        );

        $reservationId = $repo->create($reservation);

     
        if (!empty($rental['host_email'])) {
            (new EmailService())->send(
                (string)$rental['host_email'],
                "Nouvelle réservation",
                "Votre logement '{$rental['title']}' a été réservé du $start au $end."
            );
        }

        return $reservationId;
    }

    public function cancel(int $reservationId): void
    {
        Session::start();

        $role = (string) Session::get('role', '');
        $userId = (int) Session::get('user_id', 0);

        if ($userId <= 0) {
            throw new PermissionDeniedException("Vous devez être connecté.");
        }

        $pdo = Database::getConnection();
        $repo = new ReservationRepository($pdo);

        $reservation = $repo->findById($reservationId);
        if (!$reservation) {
            throw new NotFoundException("Réservation introuvable.");
        }

        if ($role !== 'admin' && !$reservation->isOwner($userId)) {
            throw new PermissionDeniedException("Vous ne pouvez annuler que vos réservations.");
        }

        if (!$reservation->isBooked()) {
            throw new DateConflictException("Cette réservation ne peut plus être annulée.");
        }

        $info = $repo->hostEmailByReservation($reservationId);

        $repo->cancel($reservationId);

        if ($info) {
            (new EmailService())->send(
                (string)$info['host_email'],
                "Réservation annulée",
                "Une réservation pour '{$info['title']}' a été annulée."
            );
        }
    }

    public function myReservations(): array
    {
        Session::start();

        $userId = (int) Session::get('user_id', 0);
        if ($userId <= 0) {
            throw new PermissionDeniedException("Vous devez être connecté.");
        }

        $pdo = Database::getConnection();
        return (new ReservationRepository($pdo))->listByTraveler($userId);
    }

    public function receipt(int $reservationId): array
    {
        Session::start();

        $role = (string) Session::get('role', '');
        $userId = (int) Session::get('user_id', 0);

        if ($userId <= 0) {
            throw new PermissionDeniedException("Vous devez être connecté.");
        }

        $pdo = Database::getConnection();
        $repo = new ReservationRepository($pdo);

        $reservation = $repo->findById($reservationId);
        if (!$reservation) {
            throw new NotFoundException("Réservation introuvable.");
        }

        if ($role !== 'admin' && !$reservation->isOwner($userId)) {
            throw new PermissionDeniedException("Accès refusé.");
        }

        $data = $repo->receiptData($reservationId);
        if (!$data) {
            throw new NotFoundException("Données du reçu introuvables.");
        }

        return $data;
    }
}

<?php
declare(strict_types=1);

use utils\Guard;
use utils\Session;
use services\FavoriteService;

Guard::requireLogin();     
Session::start();          

try {
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException("Méthode invalide.");
    }

    $rentalId = (int)($_POST['rental_id'] ?? 0);
    if ($rentalId <= 0) {
        throw new RuntimeException("Rental ID invalide.");
    }

    
    $redirect = (string)($_POST['redirect'] ?? '');
    if ($redirect === '') {
        $redirect = 'index.php?page=rental&id=' . $rentalId;
    }

    (new FavoriteService())->toggle($rentalId);

    Session::set('flash_ok', "Favoris mis à jour ✅");

    header("Location: " . $redirect);
    exit;

} catch (Throwable $e) {
    Session::set('flash_err', $e->getMessage());

    $fallbackId = (int)($_POST['rental_id'] ?? 0);
    $fallback = $fallbackId > 0
        ? 'index.php?page=rental&id=' . $fallbackId
        : 'index.php?page=home';

    header("Location: " . $fallback);
    exit;
}

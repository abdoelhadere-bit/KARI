<?php
declare(strict_types=1);

use services\FavoriteService;

$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php");
        exit;
    }

    $rentalId = (int)($_POST['rental_id'] ?? 0);
    $redirect = (string)($_POST['redirect'] ?? 'index.php');

    (new FavoriteService())->toggle($rentalId);

    header("Location: " . $redirect);
    exit;

} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<h2>Favoris</h2>
<p style="color:red;"><?= htmlspecialchars($error) ?></p>
<p><a href="index.php">Retour</a></p>

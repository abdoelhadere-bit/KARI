<?php
declare(strict_types=1);

use utils\Guard;
use services\ReservationService;

Guard::requireLogin();

$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    (new ReservationService())->cancel($id);
    $success = "Réservation annulée avec succès.";
} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<h2>Annulation</h2>

<?php if ($success): ?>
  <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p><a href="index.php?page=my_reservations">Retour à mes réservations</a></p>

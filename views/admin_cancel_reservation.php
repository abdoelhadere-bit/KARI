<?php
declare(strict_types=1);

use utils\Guard;
use services\ReservationService;

Guard::requireRole('admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

try {
    (new ReservationService())->cancel($id);
    header("Location: index.php?page=admin_reservations");
    exit;
} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<h2>Annulation admin</h2>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p><a href="index.php?page=admin_reservations">Retour</a></p>

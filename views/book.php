<?php
declare(strict_types=1);

use utils\Guard;
use services\ReservationService;

Guard::requireLogin();


// ??????
$rentalId = isset($_GET['rental_id']) ? (int)$_GET['rental_id'] : (int)($_POST['rental_id'] ?? 0);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $service = new ReservationService();

        $reservationId = $service->book(
            (int)($_POST['rental_id'] ?? 0),
            (string)($_POST['start_date'] ?? ''),
            (string)($_POST['end_date'] ?? ''),
            (int)($_POST['guests'] ?? 1)
        );

        header("Location: index.php?page=my_reservations");
        exit;

    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
}
?>

<h2>Réserver</h2>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="index.php?page=book">
  <input type="hidden" name="rental_id" value="<?= (int)$rentalId ?>">

  <label>Date début</label><br>
  <input type="date" name="start_date" required><br><br>

  <label>Date fin</label><br>
  <input type="date" name="end_date" required><br><br>

  <label>Guests</label><br>
  <input type="number" name="guests" min="1" value="1" required><br><br>

  <button type="submit">Confirmer réservation</button>
</form>

<p><a href="index.php?page=rental&id=<?= (int)$rentalId ?>">Retour logement</a></p>

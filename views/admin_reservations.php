<?php
declare(strict_types=1);

use utils\Guard;
use services\AdminService;

Guard::requireRole('admin');

$error = '';
$reservations = [];

try {
    $reservations = (new AdminService())->reservations(30);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<h2>Admin — Réservations</h2>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($reservations)): ?>
  <p>Aucune réservation.</p>
<?php else: ?>
  <table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Logement</th>
        <th>Voyageur</th>
        <th>Dates</th>
        <th>Total</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($reservations as $res): ?>
        <tr>
          <td><?= (int)$res['id'] ?></td>
          <td><?= htmlspecialchars($res['rental_title']) ?></td>
          <td><?= htmlspecialchars($res['traveler_name']) ?></td>
          <td><?= htmlspecialchars($res['start_date']) ?> → <?= htmlspecialchars($res['end_date']) ?></td>
          <td><?= htmlspecialchars((string)$res['total_price']) ?></td>
          <td><?= htmlspecialchars($res['status']) ?></td>
          <td>
            <?php if ($res['status'] === 'booked'): ?>
              <a href="index.php?page=admin_cancel_reservation&id=<?= (int)$res['id'] ?>"
                 onclick="return confirm('Annuler cette réservation ?');">
                Annuler
              </a>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<p style="margin-top:10px;"><a href="index.php?page=admin_panel">Retour admin panel</a></p>

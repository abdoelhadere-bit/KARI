<?php
declare(strict_types=1);
require __DIR__ . '/partials/header.php';

use utils\Guard;
use services\ReservationService;

Guard::requireLogin();

$error = '';
$reservations = [];

try {
    $service = new ReservationService();
    $reservations = $service->myReservations();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<section class="glass" style="padding:18px;">
  <h2 class="h1">Mes réservations</h2>
  <p class="sub">Tout ton historique au même endroit.</p>

  <?php if (!empty($error ?? '')): ?>
    <p style="color:#ffb4b4;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if (empty($reservations)): ?>
    <p class="sub">Aucune réservation.</p>
  <?php else: ?>
    <div style="display:grid;gap:10px;">
      <?php foreach ($reservations as $res): ?>
        <div class="card">
          <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;">
            <div>
              <h3 style="margin:0 0 6px;"><?= htmlspecialchars($res['rental_title']) ?></h3>
              <p style="margin:0;">📍 <?= htmlspecialchars($res['rental_city']) ?></p>
              <p style="margin:6px 0 0;color:var(--muted);">
                <?= htmlspecialchars($res['start_date']) ?> → <?= htmlspecialchars($res['end_date']) ?>
                • Guests: <?= (int)$res['guests'] ?>
                • Total: <?= htmlspecialchars((string)$res['total_price']) ?>
              </p>
            </div>

            <div style="display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap;">
              <span class="pill">Statut: <b><?= htmlspecialchars($res['status']) ?></b></span>

              <?php if ($res['status'] === 'booked'): ?>
                <a class="btn" href="index.php?page=cancel&id=<?= (int)$res['id'] ?>"
                   onclick="return confirm('Annuler cette réservation ?');">Annuler</a>
              <?php endif; ?>

              <a class="btn btn-primary" href="index.php?page=reservation_pdf&id=<?= (int)$res['id'] ?>">PDF</a>
              <a class="btn" href="index.php?page=rental&id=<?= (int)$res['rental_id'] ?>">Voir logement</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
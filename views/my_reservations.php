<?php
declare(strict_types=1);
require __DIR__ . '/partials/header.php';
echo '<main class="container">';

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

<div class="glass" style="padding:16px; margin-bottom:14px;">
  <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
    <div>
      <h2 class="h1" style="margin:0;">Mes réservations</h2>
      <p class="sub" style="margin-top:6px;">
        Suis tes réservations, annule si besoin, et récupère ton PDF.
      </p>
    </div>
    <div style="display:flex; gap:10px;">
      <a class="btn" href="index.php?page=home">← Retour</a>
      <a class="btn btn-primary" href="index.php?page=home">Explorer logements</a>
    </div>
  </div>
</div>

<?php if (!empty($error ?? '')): ?>
  <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
    <b style="color:#ffb4b4;">Erreur</b>
    <div class="sub" style="margin-top:6px;"><?= htmlspecialchars((string)$error) ?></div>
  </div>
<?php endif; ?>

<?php if (empty($reservations ?? [])): ?>
  <div class="glass" style="padding:16px;">
    <p class="sub" style="margin:0;">Aucune réservation pour le moment.</p>
    <a class="btn btn-primary" style="margin-top:10px;" href="index.php?page=home">Trouver un logement</a>
  </div>
<?php else: ?>

  <div style="display:grid; gap:12px;">
    <?php foreach ($reservations as $res): ?>
      <div class="card">
        <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:flex-start;">
          <div style="min-width:240px;">
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <b style="font-size:16px;"><?= htmlspecialchars($res['rental_title']) ?></b>
              <span class="pill">📍 <?= htmlspecialchars($res['rental_city']) ?></span>

              <?php if ($res['status'] === 'booked'): ?>
                <span class="pill" style="border-color: rgba(80,255,170,.25);">✅ booked</span>
              <?php else: ?>
                <span class="pill" style="border-color: rgba(255,180,80,.25);">⚠ cancelled</span>
              <?php endif; ?>
            </div>

            <div class="sub" style="margin-top:8px; line-height:1.7;">
              <div>📅 <?= htmlspecialchars($res['start_date']) ?> → <?= htmlspecialchars($res['end_date']) ?></div>
              <div>👥 Guests: <?= (int)$res['guests'] ?> • 💳 Total: <?= htmlspecialchars((string)$res['total_price']) ?></div>
            </div>
          </div>

          <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <a class="btn" href="index.php?page=rental&id=<?= (int)$res['rental_id'] ?>">Voir logement</a>

            <?php if ($res['status'] === 'booked'): ?>
              <a class="btn" href="index.php?page=reservation_pdf&id=<?= (int)$res['id'] ?>">PDF</a>
              <a class="btn btn-danger"
                 href="index.php?page=cancel&id=<?= (int)$res['id'] ?>"
                 onclick="return confirm('Annuler cette réservation ?');">
                 Annuler
              </a>
            <?php else: ?>
              <span class="sub" style="opacity:.8;">Actions indisponibles</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php endif; ?>
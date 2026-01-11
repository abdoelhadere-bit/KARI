<?php
declare(strict_types=1);
require __DIR__ . '/partials/header.php';

use utils\Guard;
use services\ReservationService;
use services\RentalService;

Guard::requireLogin();


// ??????
$rentalId = isset($_GET['rental_id']) ? (int)$_GET['rental_id'] : (int)($_POST['rental_id'] ?? 0);

$service = new RentalService();
$rental = $service->getDetails($rentalId);

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

<div class="glass" style="padding:16px; margin-bottom:14px;">
  <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
    <div>
      <h2 class="h1" style="margin:0;">Réserver</h2>
      <p class="sub" style="margin-top:6px;">
        Choisis tes dates et le nombre de guests. On vérifie la disponibilité automatiquement.
      </p>
    </div>
    <a class="btn" href="index.php?page=home">← Retour</a>
  </div>
</div>

<?php if (!empty($error ?? '')): ?>
  <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
    <b style="color:#ffb4b4;">Erreur</b>
    <div class="sub" style="margin-top:6px;"><?= htmlspecialchars((string)$error) ?></div>
  </div>
<?php endif; ?>

<?php if (!empty($success ?? '')): ?>
  <div class="glass" style="padding:12px; border:1px solid rgba(80,255,170,.25); background:rgba(80,255,170,.08); margin-bottom:12px;">
    <b style="color:#b9ffd6;">OK</b>
    <div class="sub" style="margin-top:6px;"><?= htmlspecialchars((string)$success) ?></div>
  </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr .85fr; gap:14px; align-items:start;">
  <!-- FORM -->
  <section class="glass" style="padding:14px;">
    <h3 style="margin:0 0 10px;">Détails de réservation</h3>

    <form method="POST" action="" class="card" style="margin:0;">
      <input type="hidden" name="rental_id" value="<?= (int)($rentalId ?? 0) ?>">

      <div class="row" style="grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label class="sub" style="display:block; margin-bottom:6px;">Date début</label>
          <input class="field" type="date" name="start_date" required value="<?= htmlspecialchars((string)($_POST['start_date'] ?? '')) ?>">
        </div>

        <div>
          <label class="sub" style="display:block; margin-bottom:6px;">Date fin</label>
          <input class="field" type="date" name="end_date" required value="<?= htmlspecialchars((string)($_POST['end_date'] ?? '')) ?>">
        </div>
      </div>

      <div style="margin-top:10px;">
        <label class="sub" style="display:block; margin-bottom:6px;">Guests</label>
        <input class="field" type="number" name="guests" min="1" required value="<?= htmlspecialchars((string)($_POST['guests'] ?? '1')) ?>">
        <div class="sub" style="margin-top:6px; opacity:.85;">
          Astuce: le système refuse automatiquement si guests > max_guests ou si dates en conflit.
        </div>
      </div>

      <div style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;">
        <button class="btn btn-primary" type="submit">Confirmer la réservation</button>
        <a class="btn" href="index.php?page=rental&id=<?= (int)($rentalId ?? 0) ?>">Voir le logement</a>
      </div>
    </form>
  </section>

  <!-- SUMMARY -->
  <aside class="glass" style="padding:14px;">
    <h3 style="margin:0 0 10px;">Résumé</h3>

    <div class="card">
      <?php if (!empty($rental)): ?>
        <?php if (!empty($rental['image'])): ?>
          <div style="border-radius:14px; overflow:hidden; margin-bottom:10px;">
            <img src="<?= htmlspecialchars((string)$rental['image']) ?>" alt="" style="width:100%; height:180px; object-fit:cover; display:block;">
          </div>
        <?php endif; ?>

        <b><?= htmlspecialchars((string)$rental['title']) ?></b>
        <div class="sub" style="margin-top:6px;">
          📍 <?= htmlspecialchars((string)$rental['city']) ?><br>
          💰 <?= htmlspecialchars((string)$rental['price_per_night']) ?> / nuit<br>
          👥 Max guests: <?= (int)$rental['max_guests'] ?><br>
          👤 Hôte: <?= htmlspecialchars((string)$rental['host_name']) ?>
        </div>
      <?php else: ?>
        <div class="sub">Résumé indisponible (logement non chargé dans cette page).</div>
      <?php endif; ?>
    </div>

    <div class="glass" style="padding:12px; margin-top:10px;">
      <div class="sub">
        Après confirmation, tu retrouveras la réservation dans <b>Mes réservations</b>.
      </div>
      <a class="btn" style="margin-top:10px;" href="index.php?page=my_reservations">Mes réservations</a>
    </div>
  </aside>
</div>
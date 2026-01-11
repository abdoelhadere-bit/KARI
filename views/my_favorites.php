<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';



use utils\Guard;
use services\FavoriteService;

Guard::requireLogin();

$error = '';
$favs = [];

try {
    $favs = (new FavoriteService())->myFavorites();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

  $flashOk  = (string)($_SESSION['flash_ok'] ?? '');
  $flashErr = (string)($_SESSION['flash_err'] ?? '');
  unset($_SESSION['flash_ok'], $_SESSION['flash_err']);
  ?>

  <?php if ($flashOk !== ''): ?>
    <div class="glass"
         style="padding:12px;
                border:1px solid rgba(80,255,170,.28);
                background:rgba(80,255,170,.08);
                margin-bottom:12px;
                border-radius:12px;">
      <b style="color:rgba(180,255,220,.95);">Succès</b>
      <div class="sub" style="margin-top:4px;">
        <?= htmlspecialchars($flashOk) ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($flashErr !== ''): ?>
    <div class="glass"
         style="padding:12px;
                border:1px solid rgba(255,80,80,.28);
                background:rgba(255,80,80,.08);
                margin-bottom:12px;
                border-radius:12px;">
      <b style="color:rgba(255,180,180,.95);">Erreur</b>
      <div class="sub" style="margin-top:4px;">
        <?= htmlspecialchars($flashErr) ?>
      </div>
    </div>
  <?php endif; ?>

<main class="container" style="padding-top:14px;">

  <!-- Header -->
  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Mes favoris</h2>
        <p class="sub" style="margin-top:6px;">Tes logements enregistrés ❤️</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="index.php?page=home">← Home</a>
        <a class="btn btn-primary" href="index.php?page=dashboard_traveler">Dashboard</a>
      </div>
    </div>
  </div>

  <?php if ($error !== ''): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
      <b style="color:#ffb4b4;">Erreur</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <?php if (empty($favs)): ?>
    <div class="glass" style="padding:16px;">
      <p class="sub" style="margin:0;">Aucun favori pour le moment.</p>
      <div style="margin-top:12px;">
        <a class="btn btn-primary" href="index.php?page=home">Explorer les logements</a>
      </div>
    </div>
  <?php else: ?>

    <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:10px;">
      <span class="pill">Total: <?= count($favs) ?></span>
    </div>

    <div class="grid3">
      <?php foreach ($favs as $f): ?>
        <div class="card">
          <div class="card-content">
            <h3 style="margin:0;"><?= htmlspecialchars($f->getTitle()) ?></h3>

            <div class="card-info">
              <p>📍 <span class="sub"><?= htmlspecialchars($f->getCity()) ?></span></p>
              <p class="price">💰 <?= htmlspecialchars((string)$f->getPricePerNight()) ?> / nuit</p>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:auto;">
              <a class="btn btn-primary" style="flex:1;" href="index.php?page=rental&id=<?= (int)$f->getRentalId() ?>">
                Voir
              </a>

              <form method="POST" action="index.php?page=favorite_toggle" style="margin:0;">
                <input type="hidden" name="rental_id" value="<?= (int)$f->getRentalId() ?>">
                <input type="hidden" name="redirect" value="index.php?page=my_favorites">
                <button class="btn btn-danger" type="submit">Retirer</button>
              </form>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</main>

<?php
declare(strict_types=1);

use utils\Guard;
use utils\Session;

Guard::requireRole('traveler');

require __DIR__ . '/partials/header.php';
?>


<section class="glass" style="padding:18px;">
  <h2 class="h1">Dashboard Voyageur</h2>
  <p class="sub">Bienvenue, <?= htmlspecialchars((string)Session::get('user_name')) ?>.</p>

  <div class="grid3">
    <div class="card">
      <h3>Explorer</h3>
      <p>Voir les logements disponibles</p>
      <a class="btn" href="index.php?page=home">Aller</a>
    </div>

    <div class="card">
      <h3>Mes réservations</h3>
      <p>Historique + annulation + PDF</p>
      <a class="btn btn-primary" href="index.php?page=my_reservations">Ouvrir</a>
    </div>

    <div class="card">
      <h3>Favoris</h3>
      <p>Gérer tes logements favoris</p>
      <a class="btn" href="index.php?page=my_favorites">Ouvrir</a>
    </div>
  </div>
</section>

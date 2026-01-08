<?php
declare(strict_types=1);
require __DIR__ . '/partials/header.php';

use utils\Guard;
use utils\Session;

Guard::requireRole('host');
?>

<section class="glass" style="padding:18px;">
  <h2 class="h1">Dashboard Hôte</h2>
  <p class="sub">Bienvenue, <?= htmlspecialchars((string)Session::get('user_name')) ?>.</p>

  <div class="grid3">
    <div class="card">
      <h3>Mes logements</h3>
      <p>Créer / modifier / supprimer</p>
      <a class="btn btn-primary" href="index.php?page=host_rentals">Gérer</a>
    </div>

    <div class="card">
      <h3>Profil</h3>
      <p>Mettre à jour tes infos</p>
      <a class="btn" href="index.php?page=profile">Ouvrir</a>
    </div>

    <div class="card">
      <h3>Explorer</h3>
      <p>Voir les logements côté client</p>
      <a class="btn" href="index.php?page=home">Aller</a>
    </div>
    <a href="index.php?page=logout">Déconnexion</a>

  </div>
</section>

<?php
declare(strict_types=1);

use utils\Session;

Session::start();

$isLogged = Session::has('user_id');
$name = (string) Session::get('user_name', 'Invité');
$role = (string) Session::get('role', '');
$email = (string) Session::get('user_email', '');
$initial = strtoupper(mb_substr($name !== '' ? $name : 'U', 0, 1));

$dash = 'index.php?page=home';
if ($role === 'traveler') $dash = 'index.php?page=dashboard_traveler';
if ($role === 'host')     $dash = 'index.php?page=dashboard_host';
if ($role === 'admin')    $dash = 'index.php?page=dashboard_admin';
?>
<link rel="stylesheet" href="/kari/public/assets/app.css">

<header class="topbar">
  <div class="row">
    <a class="brand" href="index.php?page=home">
      <div class="logo"></div>
      <div>
        <div class="title">KARI • Stays</div>
        <div class="hint">Atlas glass rentals</div>
      </div>
    </a>

    <!-- <div class="quick">
      <span class="pill">⌕</span>
      <input type="text" placeholder="Chercher une ville, un prix, une vibe…" disabled>
    </div> -->

    <div class="acct">
      <?php if ($isLogged): ?>
        <button id="acctBtn" class="acct-btn" aria-label="Compte">
          <span class="avatar"><?= htmlspecialchars($initial) ?></span>
          <span class="acct-name"><?= htmlspecialchars($name) ?></span>
          <span class="pill">▾</span>
        </button>

        <div id="acctMenu" class="menu" aria-hidden="true" hidden>
          <div class="head">
            <div class="avatar"><?= htmlspecialchars($initial) ?></div>
            <div class="meta">
              <b><?= htmlspecialchars($name) ?></b>
              <span><?= $email !== '' ? htmlspecialchars($email) : 'email non chargé' ?></span>
              <span class="small">Rôle: <?= htmlspecialchars($role) ?></span>
            </div>
          </div>

          <div class="grid">
            <a class="item" href="<?= $dash ?>"><span>Dashboard</span><span class="pill">↗</span></a>
            <a class="item" href="index.php?page=profile"><span>Profil</span><span class="pill">✦</span></a>
            <a class="item" href="index.php?page=my_reservations"><span>Mes réservations</span><span class="pill">⟡</span></a>
            <a class="item" href="index.php?page=my_favorites"><span>Mes favoris</span><span class="pill">♥</span></a>
            <?php if ($role === 'host' || $role === 'admin'): ?>
              <a class="item" href="index.php?page=host_rentals"><span>Mes logements</span><span class="pill">⌂</span></a>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
              <a class="item" href="index.php?page=admin_panel"><span>Admin panel</span><span class="pill">⚙</span></a>
            <?php endif; ?>
          </div>

          <div class="foot">
            <a class="btn btn-danger" href="index.php?page=logout">Logout</a>
            <span class="small">KARI UI</span>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn-primary" href="index.php?page=login">Se connecter</a>
      <?php endif; ?>
    </div>
  </div>

  
</header>
<div id="confirmModal" class="modal" aria-hidden="true">
  <div class="modal-backdrop" data-close="1"></div>

  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="cmTitle">
    <div class="modal-head">
      <div>
        <div id="cmTitle" class="modal-title">Confirmation</div>
        <div id="cmMsg" class="modal-sub">Tu es sûr ?</div>
      </div>
      <button class="modal-x" type="button" data-close="1" aria-label="Fermer">✕</button>
    </div>

    <div class="modal-actions">
      <button class="btn" type="button" data-close="1">Retour</button>
      <a id="cmGo" class="btn btn-danger" href="#">Confirmer</a>
    </div>
  </div>
</div>

<script src="/kari/public/assets/app.js" defer></script>


<?php
declare(strict_types=1);

use utils\Guard;
use utils\Session;

Guard::requireRole('admin');
Session::start();
?>

<h2>Dashboard Admin</h2>

<p>Bienvenue <b><?= htmlspecialchars((string)Session::get('user_name')) ?></b></p>

<ul>
  <li><a href="index.php?page=admin_panel">Admin Panel (Stats + Gestion)</a></li>
  <li><a href="index.php?page=home">Voir logements (public)</a></li>
  <li><a href="index.php?page=profile">Mon profil</a></li>
  <li><a href="index.php?page=logout">Déconnexion</a></li>
</ul>

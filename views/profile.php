<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';

use utils\Guard;
use utils\Session;
use services\ProfileService;

Guard::requireLogin();

$userId = (int) Session::get('user_id');
$name   = (string) Session::get('user_name', '');
$email  = (string) Session::get('user_email', '');
$role   = (string) Session::get('role', '');

$error = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $newName  = trim((string)($_POST['name'] ?? ''));
    $newEmail = trim((string)($_POST['email'] ?? ''));

    $oldPass  = (string)($_POST['current_password'] ?? '');
    $pass1    = (string)($_POST['password'] ?? '');
    $pass2    = (string)($_POST['password_confirm'] ?? '');

    $service = new ProfileService();

    $service->updateProfile($userId, $newName, $newEmail);

    if ($pass1 !== '' || $pass2 !== '' || $oldPass !== '') {
      $service->changePassword($userId, $oldPass, $pass1, $pass2);
    }

    Session::set('user_name', $newName);
    Session::set('user_email', $newEmail);

    $ok = "Profil mis à jour ✅";
    $name = $newName;
    $email = $newEmail;

  } catch (Throwable $e) {
    $error = $e->getMessage();
  }
}

?>

<main class="container" style="padding-top:14px;">
  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Mon profil</h2>
        <p class="sub" style="margin-top:6px;">Gère tes infos de compte.</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="index.php?page=home">← Retour</a>
        <?php if ($role === 'traveler'): ?>
          <a class="btn btn-primary" href="index.php?page=dashboard_traveler">Dashboard</a>
        <?php elseif ($role === 'host'): ?>
          <a class="btn btn-primary" href="index.php?page=dashboard_host">Dashboard</a>
        <?php elseif ($role === 'admin'): ?>
          <a class="btn btn-primary" href="index.php?page=dashboard_admin">Dashboard</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if ($ok !== ''): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(80,255,170,.28); background:rgba(80,255,170,.08); margin-bottom:12px;">
      <b style="color:rgba(180,255,220,.95);">Succès</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($ok) ?></div>
    </div>
  <?php endif; ?>

  <?php if ($error !== ''): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
      <b style="color:#ffb4b4;">Erreur</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <div class="layout" style="grid-template-columns: 1fr 380px;">
    <!-- Form -->
    <section class="glass" style="padding:16px;">
      <h3 style="margin:0 0 10px;">Informations</h3>

      <form method="POST" style="display:grid; gap:12px;">
        <div>
          <label class="sub">Nom</label>
          <input class="field" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>

        <div>
          <label class="sub">Email</label>
          <input class="field" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div class="glass" style="padding:12px; border:1px solid rgba(255,255,255,.10); background:rgba(255,255,255,.04);">
          <div style="font-weight:700; margin-bottom:6px;">Changer le mot de passe (optionnel)</div>
          <div class="sub" style="margin:0 0 10px;">Pour changer, tu dois confirmer avec ton mot de passe actuel.</div>

          <div style="display:grid; gap:10px;">
            <div>
              <label class="sub">Mot de passe actuel</label>
              <input class="field" type="password" name="current_password" placeholder="••••••••">
            </div>

            <div class="row" style="grid-template-columns: 1fr 1fr;">
              <div>
                <label class="sub">Nouveau mot de passe</label>
                <input class="field" type="password" name="password" placeholder="••••••••">
              </div>
              <div>
                <label class="sub">Confirmation</label>
                <input class="field" type="password" name="password_confirm" placeholder="••••••••">
              </div>
            </div>
          </div>
        </div>


        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn btn-primary" type="submit">Enregistrer</button>
          <a class="btn" href="index.php?page=logout">Logout</a>
        </div>
      </form>
    </section>

    <!-- Summary card -->
    <aside class="glass" style="padding:16px;">
      <h3 style="margin:0 0 10px;">Résumé</h3>
      <div class="sub" style="line-height:1.8;">
        <div><b>Nom :</b> <?= htmlspecialchars($name) ?></div>
        <div><b>Email :</b> <?= htmlspecialchars($email) ?></div>
        <div><b>Rôle :</b> <span class="pill"><?= htmlspecialchars($role) ?></span></div>
      </div>

      <div style="margin-top:14px;">
        <div class="sub" style="margin-bottom:8px;">Actions rapides</div>
        <div style="display:grid; gap:10px;">
          <a class="btn" href="index.php?page=my_reservations">Mes réservations</a>
          <a class="btn" href="index.php?page=my_favorites">Mes favoris</a>
          <?php if ($role === 'host' || $role === 'admin'): ?>
            <a class="btn" href="index.php?page=host_rentals">Mes logements</a>
          <?php endif; ?>
          <?php if ($role === 'admin'): ?>
            <a class="btn" href="index.php?page=admin_panel">Admin panel</a>
          <?php endif; ?>
        </div>
      </div>
    </aside>
  </div>
</main>

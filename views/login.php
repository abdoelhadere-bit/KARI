<?php
declare(strict_types=1);

use services\AuthService;
use utils\Session;

$auth = new AuthService();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ok = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');

        if ($ok) {
            $role = (string) Session::get('role');

            if ($role === 'admin') {
                header("Location: index.php?page=dashboard_admin");
            } elseif ($role === 'host') {
                header("Location: index.php?page=dashboard_host");
            } else {
                header("Location: index.php?page=dashboard_traveler");
            }
            exit;
        }

        $error = "Email ou mot de passe incorrect.";
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="glass" style="padding:18px;max-width:520px;margin:28px auto;">
  <h2 class="h1">Connexion</h2>
  <p class="sub">Accède à ton espace et gère tes réservations.</p>

  <?php if ($error !== ''): ?>
    <p style="color:#ffb4b4;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="index.php?page=login" style="display:grid;gap:10px;">
    <input class="field" type="email" name="email" placeholder="Email" required>
    <input class="field" type="password" name="password" placeholder="Mot de passe" required>
    <button class="btn btn-primary" type="submit">Se connecter</button>
  </form>

  <p class="sub" style="margin-top:12px;">
    Pas encore de compte ? <a class="btn" href="index.php?page=register">Créer un compte</a>
  </p>
</section>

<?php
declare(strict_types=1);

use services\AuthService;

$auth = new AuthService();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $result = $auth->register(
            $_POST['name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? '',
            $_POST['role'] ?? 'traveler'
        );

        if (($result['ok'] ?? false) === true) {
            header("Location: index.php?page=login");
            exit;
        }

        $error = (string)($result['error'] ?? "Erreur.");
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="glass" style="padding:18px;max-width:620px;margin:28px auto;">
  <h2 class="h1">Créer un compte</h2>
  <p class="sub">Choisis ton rôle et commence.</p>

  <?php if ($error !== ''): ?>
    <p style="color:#ffb4b4;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="index.php?page=register" style="display:grid;gap:10px;">
    <div class="row">
      <input class="field" name="name" placeholder="Nom" required>
      <input class="field" type="email" name="email" placeholder="Email" required>
    </div>

    <div class="row">
      <input class="field" type="password" name="password" placeholder="Mot de passe" required>
      <select class="field" name="role" required>
        <option value="traveler">Voyageur</option>
        <option value="host">Hôte</option>
      </select>
    </div>

    <button class="btn btn-primary" type="submit">Créer</button>
  </form>

  <p class="sub" style="margin-top:12px;">
    Déjà un compte ? <a class="btn" href="index.php?page=login">Connexion</a>
  </p>
</section>

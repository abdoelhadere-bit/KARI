<?php
declare(strict_types=1);

use services\FavoriteService;
use utils\Guard;

Guard::requireLogin();

$error = '';
$favs = [];

try {
    $favs = (new FavoriteService())->myFavorites();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<h2>Mes favoris</h2>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($favs)): ?>
  <p>Aucun favori.</p>
<?php else: ?>
  <ul>
    <?php foreach ($favs as $f): ?>
      <li>
        <b><?= htmlspecialchars($f['title']) ?></b> — <?= htmlspecialchars($f['city']) ?>
        (<?= htmlspecialchars((string)$f['price_per_night']) ?> / nuit)
        | <a href="index.php?page=rental&id=<?= (int)$f['rental_id'] ?>">Voir</a>

        <form method="POST" action="index.php?page=favorite_toggle" style="display:inline;">
          <input type="hidden" name="rental_id" value="<?= (int)$f['rental_id'] ?>">
          <input type="hidden" name="redirect" value="index.php?page=my_favorites">
          <button type="submit">Retirer</button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<p><a href="index.php?page=dashboard_traveler">Retour dashboard</a></p>

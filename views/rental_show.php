<?php
declare(strict_types=1);
require __DIR__ . '/partials/header.php';
echo '<main class="container">';

use services\RentalService;
use services\FavoriteService;
use services\ReviewService;
use utils\Session;

Session::start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<p>Logement introuvable.</p>";
    echo '<p><a href="index.php">Retour</a></p>';
    exit;
}

$service = new RentalService();
$rental = $service->getDetails($id);

if (!$rental) {
    echo "<p>Logement introuvable.</p>";
    echo '<p><a href="index.php">Retour</a></p>';
    exit;
}

// savoir si ce logement est déjà dans les favoris
$isFav = false;
if (Session::has('user_id')) {
    try {
        $isFav = (new FavoriteService())->isFavorite((int)$rental['id']);
    } catch (\Throwable $e) {
        $isFav = false;
    }
}

$reviewService = new ReviewService();
$avg = $reviewService->avgByRental((int)$rental['id']);
$reviews = $reviewService->listByRental((int)$rental['id']);
?>

<h2>Détail du logement</h2>

<h3><?= htmlspecialchars($rental['title']) ?></h3>
<p><b>Ville:</b> <?= htmlspecialchars($rental['city']) ?></p>
<p><b>Adresse:</b> <?= htmlspecialchars((string)($rental['address'] ?? '')) ?></p>
<p><b>Prix:</b> <?= htmlspecialchars((string)$rental['price_per_night']) ?> / nuit</p>
<p><b>Max guests:</b> <?= (int)$rental['max_guests'] ?></p>
<p><b>Hôte:</b> <?= htmlspecialchars($rental['host_name']) ?></p>

<p><b>Description:</b><br><?= nl2br(htmlspecialchars((string)($rental['description'] ?? ''))) ?></p>

<!-- Bouton Favoris -->
<?php if (Session::has('user_id')): ?>
    <form method="POST" action="index.php?page=favorite_toggle" style="margin-top: 15px;">
        <input type="hidden" name="rental_id" value="<?= (int)$rental['id'] ?>">
        <input type="hidden" name="redirect" value="index.php?page=rental&id=<?= (int)$rental['id'] ?>">
        <button type="submit">
            <?= $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
        </button>
    </form>
<?php else: ?>
    <p><i>Connecte-toi pour ajouter ce logement aux favoris.</i></p>
<?php endif; ?>

<?php if (Session::has('user_id')): ?>
  <p style="margin-top:10px;">
    <a href="index.php?page=book&rental_id=<?= (int)$rental['id'] ?>">Réserver ce logement</a>
  </p>
<?php endif; ?>


<p style="margin-top: 15px;"><a href="index.php">← Retour à la liste</a></p>

<hr>
<h3>Avis & Note moyenne</h3>

<p><b>Note moyenne :</b> <?= number_format($avg, 1) ?> / 5</p>

<p>
  <a href="index.php?page=review_create&rental_id=<?= (int)$rental['id'] ?>">
    Laisser un avis
  </a>
</p>

<?php if (empty($reviews)): ?>
  <p>Aucun avis.</p>
<?php else: ?>
  <ul>
    <?php foreach ($reviews as $rv): ?>
      <li>
        <b><?= htmlspecialchars($rv['user_name']) ?></b> :
        <?= (int)$rv['rating'] ?>/5
        <br>
        <?= nl2br(htmlspecialchars((string)$rv['comment'])) ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

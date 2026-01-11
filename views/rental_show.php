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

<!-- HEADER -->
<div class="glass" style="padding:16px; margin-bottom:14px;">
  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <div>
      <h2 class="h1" style="margin:0;"><?= htmlspecialchars($rental['title']) ?></h2>
      <p class="sub" style="margin-top:6px;">
        📍 <?= htmlspecialchars($rental['city']) ?>
        <?php if (!empty($rental['address'])): ?>
          • <?= htmlspecialchars((string)$rental['address']) ?>
        <?php endif; ?>
      </p>

      <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
        <span class="pill">💰 <?= htmlspecialchars((string)$rental['price_per_night']) ?> / nuit</span>
        <span class="pill">👥 Max: <?= (int)$rental['max_guests'] ?></span>
        <span class="pill">⭐ <?= number_format((float)$avg, 1) ?> / 5</span>
        <span class="pill">👤 Hôte: <?= htmlspecialchars($rental['host_name']) ?></span>
      </div>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a class="btn" href="index.php?page=home">← Retour</a>

      <?php if (Session::has('user_id')): ?>
        <form method="POST" action="index.php?page=favorite_toggle" style="margin:0;">
          <input type="hidden" name="rental_id" value="<?= (int)$rental['id'] ?>">
          <input type="hidden" name="redirect" value="index.php?page=rental&id=<?= (int)$rental['id'] ?>">
          <button class="btn <?= $isFav ? 'btn-danger' : 'btn-primary' ?>" type="submit">
            <?= $isFav ? '♥ Retirer' : '♡ Favori' ?>
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- CONTENT GRID -->
<div style="display:grid; gap:14px; grid-template-columns: 1.35fr .65fr; align-items:start;">
  <!-- LEFT -->
  <section class="glass" style="padding:14px;">
    <?php if (!empty($rental['image'])): ?>
      <div class="card" style="padding:0; overflow:hidden; margin-bottom:12px;">
        <img src="<?= htmlspecialchars((string)$rental['image']) ?>" alt="Photo logement" style="width:100%; height:320px; object-fit:cover; display:block;">
      </div>
    <?php endif; ?>

    <h3 style="margin:6px 0 8px;">Description</h3>
    <div class="sub" style="line-height:1.7;">
      <?= nl2br(htmlspecialchars((string)($rental['description'] ?? ''))) ?>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:14px;">
      <?php if (Session::has('user_id')): ?>
        <a class="btn btn-primary" href="index.php?page=book&rental_id=<?= (int)$rental['id'] ?>">
          Réserver ce logement
        </a>
      <?php else: ?>
        <div class="glass" style="padding:10px 12px;">
          <span class="sub">Connecte-toi pour réserver / ajouter en favoris.</span>
          <a class="btn btn-primary" style="margin-left:10px;" href="index.php?page=login">Se connecter</a>
        </div>
      <?php endif; ?>

      <a class="btn" href="index.php?page=review_create&rental_id=<?= (int)$rental['id'] ?>">
        Laisser un avis
      </a>
    </div>
  </section>

  <!-- RIGHT -->
  <aside class="glass" style="padding:14px;">
    <h3 style="margin:0 0 8px;">Avis & Note</h3>

    <div class="card" style="margin-bottom:10px;">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
        <div>
          <div class="sub">Note moyenne</div>
          <div style="font-size:28px; font-weight:800; line-height:1; margin-top:4px;">
            <?= number_format((float)$avg, 1) ?>
            <span class="sub" style="font-size:14px; font-weight:600;">/ 5</span>
          </div>
        </div>
        <div class="pill">📝 <?= count($reviews) ?> avis</div>
      </div>
    </div>

    <?php if (empty($reviews)): ?>
      <div class="glass" style="padding:12px;">
        <p class="sub" style="margin:0;">Aucun avis pour le moment.</p>
      </div>
    <?php else: ?>
      <div style="display:grid; gap:10px; max-height:420px; overflow:auto; padding-right:4px;">
        <?php foreach ($reviews as $rv): ?>
          <div class="card">
            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center;">
              <b><?= htmlspecialchars($rv['user_name']) ?></b>
              <span class="pill">⭐ <?= (int)$rv['rating'] ?>/5</span>
            </div>
            <?php if (!empty($rv['comment'])): ?>
              <div class="sub" style="margin-top:8px; line-height:1.6;">
                <?= nl2br(htmlspecialchars((string)$rv['comment'])) ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </aside>
</div>

<?php echo '</main>'; ?>

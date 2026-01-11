<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';
echo '<main class="container" style="padding-top:14px;">';

use services\RentalService;
use services\FavoriteService;
use services\ReviewService;
use utils\Session;

Session::start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<div class='glass' style='padding:16px;'>
            <b>Logement introuvable.</b>
            <div class='sub' style='margin-top:6px;'>ID invalide.</div>
            <div style='margin-top:10px;'><a class='btn' href='index.php?page=home'>← Retour</a></div>
          </div>";
    echo '</main>';
    exit;
}

$service = new RentalService();

try {
    $rental = $service->getDetails($id);

    $isFav = false;
    if (Session::has('user_id')) {
        try {
            
            
            $isFav = (new FavoriteService())->isFavorite($rental->getId());
        } catch (\Throwable $e) {
            $isFav = false;
        }
    }

    $reviewService = new ReviewService();
    $avg = $reviewService->avgByRental($rental->getId());
    $reviews = $reviewService->listByRental($rental->getId());

} catch (\Throwable $e) {
    echo "<div class='glass' style='padding:16px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08);'>
            <b style='color:#ffb4b4;'>Erreur</b>
            <div class='sub' style='margin-top:6px;'>" . htmlspecialchars($e->getMessage()) . "</div>
            <div style='margin-top:10px;'><a class='btn' href='index.php?page=home'>← Retour</a></div>
          </div>";
    echo '</main>';
    exit;
}
?>

<!-- HEADER -->
<div class="glass" style="padding:16px; margin-bottom:14px;">
  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <div>
      <h2 class="h1" style="margin:0;"><?= htmlspecialchars($rental->getTitle()) ?></h2>
      <p class="sub" style="margin-top:6px;">
        📍 <?= htmlspecialchars($rental->getCity()) ?>
        <?php if ($rental->getAddress()): ?>
          • <?= htmlspecialchars($rental->getAddress()) ?>
        <?php endif; ?>
      </p>

      <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
        <span class="pill">💰 <?= htmlspecialchars((string)$rental->getPricePerNight()) ?> / nuit</span>
        <span class="pill">👥 Max: <?= $rental->getMaxGuests() ?></span>
        <span class="pill">⭐ <?= number_format((float)$avg, 1) ?> / 5</span>
        <span class="pill">👤 Hôte: <?= htmlspecialchars((string)$rental->getHostName()) ?></span>
      </div>
    </div>

    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <a class="btn" href="index.php?page=home">← Retour</a>

      <?php if (Session::has('user_id')): ?>
        <form method="POST" action="index.php?page=favorite_toggle" style="margin:0;">
          <input type="hidden" name="rental_id" value="<?= $rental->getId() ?>">
          <input type="hidden" name="redirect" value="index.php?page=rental&id=<?= $rental->getId() ?>">
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
    <?php if ($rental->getImage()): ?>
      <div class="card" style="padding:0; overflow:hidden; margin-bottom:12px;">
        <img src="<?= htmlspecialchars($rental->getImage()) ?>"
             alt="Photo logement"
             style="width:100%; height:320px; object-fit:cover; display:block;">
      </div>
    <?php endif; ?>

    <h3 style="margin:6px 0 8px;">Description</h3>
    <div class="sub" style="line-height:1.7;">
      <?= nl2br(htmlspecialchars((string)($rental->getDescription() ?? ''))) ?>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:14px;">
      <?php if (Session::has('user_id')): ?>
        <a class="btn btn-primary" href="index.php?page=book&rental_id=<?= $rental->getId() ?>">
          Réserver ce logement
        </a>
      <?php else: ?>
        <div class="glass" style="padding:10px 12px;">
          <span class="sub">Connecte-toi pour réserver / ajouter en favoris.</span>
          <a class="btn btn-primary" style="margin-left:10px;" href="index.php?page=login">Se connecter</a>
        </div>
      <?php endif; ?>

      <a class="btn" href="index.php?page=review_create&rental_id=<?= $rental->getId() ?>">
        Laisser un avis
      </a>
    </div>
  </section>

  <!-- RIGHT -->
  <aside class="glass" style="padding:14px;">
    <h3 style="margin:0 0 10px;">Avis & Note moyenne</h3>

    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:10px;">
      <span class="pill" style="border-color: rgba(124,92,255,.35);">⭐ <?= number_format((float)$avg, 1) ?> / 5</span>
      <span class="pill">🧾 <?= count($reviews) ?> avis</span>
    </div>

    <p style="margin:0 0 12px;">
      <a class="btn btn-primary" href="index.php?page=review_create&rental_id=<?= $rental->getId() ?>">
        Laisser un avis
      </a>
    </p>

    <?php if (empty($reviews)): ?>
      <p class="sub" style="margin:0;">Aucun avis.</p>
    <?php else: ?>
      <div style="display:grid; gap:10px;">
        <?php foreach ($reviews as $rv): ?>
          <div class="glass" style="padding:12px;">
            <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; align-items:center;">
              <b><?= htmlspecialchars((string)$rv->getUserName()) ?></b>
              <span class="pill" style="border-color: rgba(124,92,255,.35);">
                <?= htmlspecialchars($rv->stars()) ?> (<?= $rv->getRating() ?>/5)
              </span>
            </div>

            <div class="sub" style="margin-top:8px; line-height:1.7;">
              <?= nl2br(htmlspecialchars((string)$rv->getComment())) ?>
            </div>

            <div class="small" style="margin-top:8px;">
              🕒 <?= htmlspecialchars((string)$rv->getCreatedAt()) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </aside>
</div>

<?php echo '</main>'; ?>

<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';

use services\ReviewService;
use utils\Guard;

Guard::requireLogin();

$rentalId = isset($_GET['rental_id']) ? (int)$_GET['rental_id'] : 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $rentalId = (int)($_POST['rental_id'] ?? 0);
    $rating   = (int)($_POST['rating'] ?? 0);
    $comment  = (string)($_POST['comment'] ?? '');

    (new ReviewService())->create($rentalId, $rating, $comment);

    header("Location: index.php?page=rental&id=" . $rentalId);
    exit;

  } catch (\Throwable $e) {
    $error = $e->getMessage();
  }
}
?>

<main class="container" style="padding-top:14px; max-width: 820px;">
  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Laisser un avis</h2>
        <p class="sub" style="margin-top:6px;">Partage ton expérience après la fin du séjour.</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="index.php?page=rental&id=<?= (int)$rentalId ?>">← Retour logement</a>
      </div>
    </div>
  </div>

  <?php if ($error !== ''): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
      <b style="color:#ffb4b4;">Erreur</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <section class="glass" style="padding:16px;">
    <form method="POST" action="index.php?page=review_create" style="display:grid; gap:12px;">
      <input type="hidden" name="rental_id" value="<?= (int)$rentalId ?>">

      <div>
        <label class="sub">Note (1 à 5)</label>
        <input class="field" type="number" name="rating" min="1" max="5" required>
      </div>

      <div>
        <label class="sub">Commentaire</label>
        <textarea class="field" name="comment" rows="5" required
          style="resize:vertical; min-height:120px;"></textarea>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn btn-primary" type="submit">Envoyer</button>
        <a class="btn" href="index.php?page=rental&id=<?= (int)$rentalId ?>">Annuler</a>
      </div>
    </form>
  </section>
</main>

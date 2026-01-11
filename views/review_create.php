<?php
declare(strict_types=1);

require __DIR__ . '/../views/partials/header.php';

use services\ReviewService;
use utils\Guard;

Guard::requireLogin();

$rentalId = isset($_GET['rental_id']) ? (int)$_GET['rental_id'] : 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $rentalId = (int)($_POST['rental_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        (new ReviewService())->create($rentalId, $rating, $comment);

        header("Location: index.php?page=rental&id=" . $rentalId);
        exit;

    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
}
?>

<main class="container">

  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Laisser un avis</h2>
        <p class="sub" style="margin-top:6px;">
          Partage ton expérience (note + commentaire) pour aider les autres voyageurs.
        </p>
      </div>
      <div style="display:flex; gap:10px;">
        <a class="btn" href="index.php?page=rental&id=<?= (int)$rentalId ?>">← Retour logement</a>
      </div>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
      <b style="color:#ffb4b4;">Erreur</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <section class="glass" style="padding:14px;">
    <form method="POST" action="index.php?page=review_create" class="card" style="margin:0;">
      <input type="hidden" name="rental_id" value="<?= (int)$rentalId ?>">

      <div style="display:grid; gap:12px;">
        <div>
          <label class="sub" style="display:block; margin-bottom:6px;">Note (1 à 5)</label>

          <!-- UI plus clean qu'un input number -->
          <select name="rating" class="field" required>
            <option value="">Choisir…</option>
            <?php for ($i=1; $i<=5; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?> / 5</option>
            <?php endfor; ?>
          </select>

          <div class="sub" style="margin-top:6px; opacity:.85;">
            1 = pas satisfait • 5 = excellent
          </div>
        </div>

        <div>
          <label class="sub" style="display:block; margin-bottom:6px;">Commentaire</label>
          <textarea name="comment" class="field" rows="5" required
            placeholder="Ex: accueil, propreté, emplacement, communication…"
            style="resize:vertical;"></textarea>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
          <button class="btn btn-primary" type="submit">Envoyer l’avis</button>
          <a class="btn" href="index.php?page=rental&id=<?= (int)$rentalId ?>">Annuler</a>
        </div>
      </div>
    </form>
  </section>

</main>

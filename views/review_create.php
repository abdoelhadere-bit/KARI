<?php
declare(strict_types=1);

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

<h2>Laisser un avis</h2>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="index.php?page=review_create">
  <input type="hidden" name="rental_id" value="<?= (int)$rentalId ?>">

  <label>Note (1-5)</label><br>
  <input type="number" name="rating" min="1" max="5" required><br><br>

  <label>Commentaire</label><br>
  <textarea name="comment" required></textarea><br><br>

  <button type="submit">Envoyer</button>
</form>

<p><a href="index.php?page=rental&id=<?= (int)$rentalId ?>">Retour logement</a></p>

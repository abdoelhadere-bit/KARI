<?php
declare(strict_types=1);
require __DIR__ . '/partials/header.php';
echo '<main class="container">';

use services\RentalService;

$service = new RentalService();

$filters = [
    'city' => $_GET['city'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'guests' => $_GET['guests'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? '',
];

$pageNum = isset($_GET['p']) ? (int)$_GET['p'] : 1;

$hasFilter = false;
foreach ($filters as $v) {
    if ($v !== '') { $hasFilter = true; break; }
}

$data = $hasFilter
    ? $service->search($filters, $pageNum, 6)
    : $service->listActive($pageNum, 6);

$items = $data['items'];
$pages = $data['pages'];
$page  = $data['page'];

function q(array $filters, int $p): string {
    $filters['p'] = $p;
    $filters['page'] = 'home';
    return 'index.php?' . http_build_query($filters);
}
?>

<h2 class="h1">Logements disponibles</h2>
<p class="sub">Trouve un endroit qui match ton mood — ville, budget, dates.</p>

<form method="GET" action="index.php" class="glass" style="padding:14px;margin-bottom:16px;">
  <input type="hidden" name="page" value="home">

  <div class="row">
    <input class="field" type="text" name="city" placeholder="Ville" value="<?= htmlspecialchars($filters['city']) ?>">
    <input class="field" type="number" step="0.01" name="min_price" placeholder="Prix min" value="<?= htmlspecialchars($filters['min_price']) ?>">
    <input class="field" type="number" step="0.01" name="max_price" placeholder="Prix max" value="<?= htmlspecialchars($filters['max_price']) ?>">
    <input class="field" type="number" name="guests" placeholder="Guests" value="<?= htmlspecialchars($filters['guests']) ?>">
    <input class="field" type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date']) ?>">
    <input class="field" type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date']) ?>">
  </div>

  <div style="display:flex;gap:10px;margin-top:12px;align-items:center;">
    <button class="btn btn-primary" type="submit">Rechercher</button>
    <a class="btn" href="index.php?page=home">Reset</a>
  </div>
</form>

<?php if (empty($items)): ?>
  <div class="glass" style="padding:16px;">
    <p class="sub">Aucun logement trouvé pour ces critères.</p>
  </div>
<?php else: ?>
  <div class="grid3">
    <?php foreach ($items as $r): ?>
      <div class="card">
        <?php if (!empty($r['image'])): ?>
          <img src="<?= htmlspecialchars($r['image']) ?>" alt="">
        <?php endif; ?>
        <h3><?= htmlspecialchars($r['title']) ?></h3>
        <p>📍 <?= htmlspecialchars($r['city']) ?></p>
        <p>💰 <?= htmlspecialchars((string)$r['price_per_night']) ?> / nuit</p>
        <p>👤 Hôte: <?= htmlspecialchars($r['host_name']) ?></p>

        <div style="margin-top:10px;">
          <a class="btn" href="index.php?page=rental&id=<?= (int)$r['id'] ?>">Voir détail</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php echo '</main>'?>



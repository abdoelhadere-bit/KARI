<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';

use services\RentalService;

$service = new RentalService();

$filters = [
    'city'       => $_GET['city'] ?? '',
    'min_price'  => $_GET['min_price'] ?? '',
    'max_price'  => $_GET['max_price'] ?? '',
    'guests'     => $_GET['guests'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date'   => $_GET['end_date'] ?? '',
];

$pageNum = isset($_GET['p']) ? (int)$_GET['p'] : 1;

$hasFilter = false;
foreach ($filters as $v) {
    if ($v !== '') { $hasFilter = true; break; }
}

$data = $hasFilter
    ? $service->search($filters, $pageNum, 6)
    : $service->listActive($pageNum, 6);

$items = $data['items'] ?? [];
$pages = (int)($data['pages'] ?? 1);
$page  = (int)($data['page'] ?? 1);

function q(array $filters, int $p): string {
    $filters['p'] = $p;
    $filters['page'] = 'home';
    return 'index.php?' . http_build_query($filters) . '#top';
}
?>

<main class="container home-page">
  <div id="top"></div>

  <div class="home-head">
    <h2>Logements disponibles</h2>
    <p class="sub">Trouve un endroit qui match ton mood — ville, budget, dates.</p>
  </div>

  <div class="home-layout">

    <!-- Sidebar filtres -->
    <aside class="home-sidebar glass">
      <div class="filter-header">
        <span class="pill">Filtres</span>
        <a class="btn" href="index.php?page=home">Reset</a>
      </div>

      <form method="GET" action="index.php" class="filter-form">
        <input type="hidden" name="page" value="home">

        <div class="form-group">
          <label class="sub">Ville</label>
          <input class="field" type="text" name="city" placeholder="Ex: Marrakech"
                value="<?= htmlspecialchars($filters['city']) ?>">
        </div>

        <div class="form-group">
          <label class="sub">Prix min</label>
          <input class="field" type="number" step="0.01" name="min_price" placeholder="0"
                value="<?= htmlspecialchars($filters['min_price']) ?>">
        </div>

        <div class="form-group">
          <label class="sub">Prix max</label>
          <input class="field" type="number" step="0.01" name="max_price" placeholder="999"
                value="<?= htmlspecialchars($filters['max_price']) ?>">
        </div>

        <div class="form-group">
          <label class="sub">Guests</label>
          <input class="field" type="number" name="guests" placeholder="Ex: 2"
                value="<?= htmlspecialchars($filters['guests']) ?>">
        </div>

        <div class="form-group">
          <label class="sub">Start</label>
          <input class="field" type="date" name="start_date"
                value="<?= htmlspecialchars($filters['start_date']) ?>">
        </div>

        <div class="form-group">
          <label class="sub">End</label>
          <input class="field" type="date" name="end_date"
                value="<?= htmlspecialchars($filters['end_date']) ?>">
        </div>

        <button class="btn btn-primary" type="submit">Rechercher</button>
      </form>

      <div class="results-info">
        <p class="sub">
          Résultats : <b><?= (int)($data['total'] ?? count($items)) ?></b>
        </p>
      </div>
    </aside>

    <!-- Résultats + pagination -->
    <section class="home-results">

      <?php if (empty($items)): ?>
        <div class="empty-state glass">
          <p class="sub">Aucun logement trouvé pour ces critères.</p>
        </div>
      <?php else: ?>
        <div class="grid3">
          <?php foreach ($items as $r): ?>
            <div class="card">
              <?php if ($r->getImage()): ?>
                <img src="<?= htmlspecialchars($r->getImage()) ?>" alt="">
              <?php endif; ?>
              
              <div class="card-content">
              
                <div class="card-info">
                  <p>📍 <?= htmlspecialchars($r->getCity()) ?></p>
                  <p class="price">💰 <?= htmlspecialchars((string)$r->getPricePerNight()) ?> / nuit</p>
                </div>
              
                <a class="btn" href="index.php?page=rental&id=<?= $r->getId() ?>">Voir détail</a>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      <?php endif; ?>

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
        <div class="home-pagination">
          <div class="pagination-wrapper glass">

            <?php if ($page > 1): ?>
              <a class="btn" href="<?= htmlspecialchars(q($filters, $page - 1)) ?>">← Prev</a>
            <?php endif; ?>

            <?php
              $start = max(1, $page - 3);
              $end   = min($pages, $page + 3);
            ?>

            <?php if ($start > 1): ?>
              <a class="btn" href="<?= htmlspecialchars(q($filters, 1)) ?>">1</a>
              <span class="sub">…</span>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
              <?php if ($i === $page): ?>
                <span class="pill"><?= $i ?></span>
              <?php else: ?>
                <a class="btn" href="<?= htmlspecialchars(q($filters, $i)) ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php if ($end < $pages): ?>
              <span class="sub">…</span>
              <a class="btn" href="<?= htmlspecialchars(q($filters, $pages)) ?>"><?= $pages ?></a>
            <?php endif; ?>

            <?php if ($page < $pages): ?>
              <a class="btn" href="<?= htmlspecialchars(q($filters, $page + 1)) ?>">Next →</a>
            <?php endif; ?>

          </div>
        </div>
      <?php endif; ?>

    </section>
  </div>
</main>
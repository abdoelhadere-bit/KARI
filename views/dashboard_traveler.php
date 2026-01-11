<?php
declare(strict_types=1);

use utils\Guard;
use utils\Session;
use services\RentalService;

Session::start();
Guard::requireRole('traveler');

require __DIR__ . '/partials/header.php';

function studly(string $s): string
{
    $s = str_replace(['-', '_'], ' ', $s);
    $s = ucwords($s);
    return str_replace(' ', '', $s);
}

function getVal($r, string $key): string
{
    if (is_array($r)) {
        return (string)($r[$key] ?? '');
    }

    if (is_object($r)) {
        $m = 'get' . studly($key);
        if (method_exists($r, $m)) {
            return (string)$r->$m();
        }

        if (method_exists($r, 'toArray')) {
            $arr = $r->toArray();
            return (string)($arr[$key] ?? '');
        }

        if (isset($r->$key)) {
            return (string)$r->$key;
        }
    }

    return '';
}

function getInt($r, string $key): int
{
    return (int)getVal($r, $key);
}

$rentalsPage = (new RentalService())->listActive(1, 6);
$items = $rentalsPage['items'] ?? [];
?>

<section class="glass" style="padding:18px; height: calc(100vh - 92px); overflow:hidden;">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 class="h1" style="margin:0;">Bonjour <?= htmlspecialchars((string)Session::get('user_name')) ?> 👋</h2>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a class="btn btn-primary" href="index.php?page=home">Explorer tout</a>
      <a class="btn" href="index.php?page=my_reservations">Mes réservations</a>
      <a class="btn" href="index.php?page=my_favorites">Favoris</a>
    </div>
  </div>

  <div style="margin-top:14px; display:flex;justify-content:space-between;align-items:center;">
    <span class="pill">✨ Nouveautés (6 logements)</span>
    <a class="btn" href="index.php?page=home">Voir tout →</a>
  </div>

  <div class="grid3" style="margin-top:12px;">
    <?php foreach ($items as $r): ?>
      <?php
        $image = getVal($r, 'image');
        $title = getVal($r, 'title');
        $city  = getVal($r, 'city');
        $price = getVal($r, 'price_per_night');
        $id    = getInt($r, 'id');
      ?>
      <div class="card">
        <?php if ($image !== ''): ?>
          <img src="<?= htmlspecialchars($image) ?>" alt="">
        <?php endif; ?>
        <h3><?= htmlspecialchars($title) ?></h3>
        <p>📍 <?= htmlspecialchars($city) ?></p>
        <p>💰 <?= htmlspecialchars($price) ?> / nuit</p>
          
        <div style="margin-top:10px;">
          <a class="btn" href="index.php?page=rental&id=<?= $id ?>">Détails</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

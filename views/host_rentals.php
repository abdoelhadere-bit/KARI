<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';

use utils\Guard;
use utils\Session;
use services\HostRentalService;

Guard::requireLogin();
Guard::requireAnyRole(['host','admin']);

$service = new HostRentalService();
$hostId  = (int) Session::get('user_id');
$role    = (string) Session::get('role', '');

$err = '';
$ok  = '';

$action = $_GET['action'] ?? '';

try {
    // CREATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
        $service->createRental($hostId, [
            'title'           => trim((string)($_POST['title'] ?? '')),
            'city'            => trim((string)($_POST['city'] ?? '')),
            'address'         => trim((string)($_POST['address'] ?? '')),
            'price_per_night' => (float)($_POST['price_per_night'] ?? 0),
            'guests_max'      => (int)($_POST['guests_max'] ?? 1),
            'description'     => trim((string)($_POST['description'] ?? '')),
            'image'           => trim((string)($_POST['image'] ?? '')),
            'is_active'       => isset($_POST['is_active']) ? 1 : 0,
        ]);
        $ok = "Logement ajouté ✅";
        $action = ''; 
    }

    // UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $service->updateRental($hostId, $id, [
            'title'           => trim((string)($_POST['title'] ?? '')),
            'city'            => trim((string)($_POST['city'] ?? '')),
            'address'         => trim((string)($_POST['address'] ?? '')),
            'price_per_night' => (float)($_POST['price_per_night'] ?? 0),
            'guests_max'      => (int)($_POST['guests_max'] ?? 1),
            'description'     => trim((string)($_POST['description'] ?? '')),
            'image'           => trim((string)($_POST['image'] ?? '')),
            'is_active'       => isset($_POST['is_active']) ? 1 : 0,
        ]);
        $ok = "Logement mis à jour ✅";
        $action = '';
    }

    // DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $service->deleteRental($hostId, $id);
        $ok = "Logement supprimé ✅";
        $action = '';
    }

    $rentals = $service->myRentals($hostId);

    $editRental = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $editId = (int)$_GET['id'];
        foreach ($rentals as $r) {
            if ((int)$r['id'] === $editId) { $editRental = $r; break; }
        }
        if (!$editRental) {
            $err = "Logement introuvable.";
            $action = '';
        }
    }

} catch (Throwable $e) {
    $err = $e->getMessage();
    $rentals = $service->myRentals($hostId);
    $editRental = null;
}

/** dashboard link */
$dash = 'index.php?page=home';
if ($role === 'host')  $dash = 'index.php?page=dashboard_host';
if ($role === 'admin') $dash = 'index.php?page=dashboard_admin';
?>

<main class="container" style="padding-top:14px;">
  <!-- Head -->
  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Mes logements</h2>
        <p class="sub" style="margin-top:6px;">Crée, modifie et gère tes annonces.</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="<?= htmlspecialchars($dash) ?>">← Dashboard</a>
        <a class="btn btn-primary" href="index.php?page=host_rentals&action=create">+ Nouveau logement</a>
      </div>
    </div>
  </div>

  <?php if ($ok !== ''): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(80,255,170,.28); background:rgba(80,255,170,.08); margin-bottom:12px;">
      <b style="color:rgba(180,255,220,.95);">Succès</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($ok) ?></div>
    </div>
  <?php endif; ?>

  <?php if ($err !== ''): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
      <b style="color:#ffb4b4;">Erreur</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($err) ?></div>
    </div>
  <?php endif; ?>

  <!-- Create / Edit form -->
  <?php if ($action === 'create' || $action === 'edit'): ?>
    <?php
      $isEdit = ($action === 'edit' && is_array($editRental));
      $r = $isEdit ? $editRental : [
        'id' => 0,
        'title' => '',
        'city' => '',
        'address' => '',
        'price_per_night' => '',
        'guests_max' => 1,
        'description' => '',
        'image' => '',
        'is_active' => 1,
      ];
    ?>
    <section class="glass" style="padding:16px; margin-bottom:14px;">
      <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:10px;">
        <h3 style="margin:0;"><?= $isEdit ? 'Modifier le logement' : 'Créer un logement' ?></h3>
        <a class="btn" href="index.php?page=host_rentals">Annuler</a>
      </div>

      <form method="POST" style="display:grid; gap:12px;">
        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
        <?php if ($isEdit): ?>
          <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <?php endif; ?>

        <div class="row2">
          <div>
            <label class="sub">Titre</label>
            <input class="field" type="text" name="title" value="<?= htmlspecialchars((string)$r['title']) ?>" required>
          </div>
          <div>
            <label class="sub">Ville</label>
            <input class="field" type="text" name="city" value="<?= htmlspecialchars((string)$r['city']) ?>" required>
          </div>
        </div>

        <div>
          <label class="sub">Adresse</label>
          <input class="field" type="text" name="address" value="<?= htmlspecialchars((string)$r['address']) ?>" required>
        </div>

        <div class="row2">
          <div>
            <label class="sub">Prix / nuit</label>
            <input class="field" type="number" step="0.01" name="price_per_night" value="<?= htmlspecialchars((string)$r['price_per_night']) ?>" required>
          </div>
          <div>
            <label class="sub">Guests max</label>
            <input class="field" type="number" name="guests_max" value="<?= htmlspecialchars((string)$r['guests_max']) ?>" min="1" required>
          </div>
        </div>

        <div>
          <label class="sub">Image (URL) <span class="small">(optionnel)</span></label>
          <input class="field" type="text" name="image" value="<?= htmlspecialchars((string)($r['image'] ?? '')) ?>" placeholder="https://...">
        </div>

        <div>
          <label class="sub">Description</label>
          <textarea class="field" name="description" rows="4" style="resize:vertical;"><?= htmlspecialchars((string)$r['description']) ?></textarea>
        </div>

        <label class="pill" style="width:max-content;">
          <input type="checkbox" name="is_active" <?= ((int)($r['is_active'] ?? 0) === 1) ? 'checked' : '' ?>>
          Actif
        </label>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Enregistrer' : 'Créer' ?></button>
          <a class="btn" href="index.php?page=host_rentals">Retour liste</a>
        </div>
      </form>
    </section>
  <?php endif; ?>

  <!-- List -->
  <section class="glass" style="padding:16px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:10px;">
      <h3 style="margin:0;">Mes annonces</h3>
      <span class="pill">Total : <?= (int)count($rentals) ?></span>
    </div>

    <?php if (empty($rentals)): ?>
      <p class="sub" style="margin:0;">Tu n'as aucun logement pour le moment.</p>
    <?php else: ?>
      <div class="grid3">
        <?php foreach ($rentals as $r): ?>
          <?php
            $active = ((int)($r['is_active'] ?? 0) === 1);
            $img = (string)($r['image'] ?? '');
          ?>
          <article class="card" style="display:flex; flex-direction:column;">
            <?php if ($img !== ''): ?>
              <img src="<?= htmlspecialchars($img) ?>" alt="">
            <?php else: ?>
              <div class="glass" style="height:170px; border-radius:14px; display:grid; place-items:center; border:1px dashed rgba(255,255,255,.18); color:rgba(234,240,255,.55);">
                Aucune image
              </div>
            <?php endif; ?>

            <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:10px;">
              <h3 style="margin:0; font-size:16px;"><?= htmlspecialchars((string)$r['title']) ?></h3>
              <span class="pill" style="border-color: <?= $active ? 'rgba(80,255,170,.28)' : 'rgba(255,180,80,.28)' ?>; background: <?= $active ? 'rgba(80,255,170,.08)' : 'rgba(245,158,11,.10)' ?>;">
                <?= $active ? 'Actif' : 'Inactif' ?>
              </span>
            </div>

            <p style="margin:6px 0 0; color:var(--muted);">📍 <?= htmlspecialchars((string)$r['city']) ?> — <?= htmlspecialchars((string)$r['address']) ?></p>
            <p style="margin:6px 0 0; color:var(--muted);">💰 <?= htmlspecialchars((string)$r['price_per_night']) ?> / nuit</p>
            <p style="margin:6px 0 0; color:var(--muted);">👥 Guests max: <?= (int)$r['guests_max'] ?></p>

            <div style="margin-top:auto; display:flex; gap:10px; flex-wrap:wrap; padding-top:12px;">
              <a class="btn" href="index.php?page=rental&id=<?= (int)$r['id'] ?>">Voir</a>
              <a class="btn btn-primary" href="index.php?page=host_rentals&action=edit&id=<?= (int)$r['id'] ?>">Modifier</a>

              <form method="POST" onsubmit="return confirm('Supprimer ce logement ?');" style="margin:0;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-danger" type="submit">Supprimer</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';

use services\AdminService;

$service = new AdminService();
$error = '';

try {
    if (isset($_GET['toggle_user'])) {
        $service->toggleUser((int)$_GET['toggle_user'], (string)($_GET['status'] ?? ''));
        header("Location: index.php?page=admin_panel");
        exit;
    }

    if (isset($_GET['toggle_rental'])) {
        $service->toggleRental((int)$_GET['toggle_rental'], (string)($_GET['status'] ?? ''));
        header("Location: index.php?page=admin_panel");
        exit;
    }

    $stats = $service->stats();
    $users = $service->users();
    $rentals = $service->rentals();

} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<main class="container" style="padding-top:14px;">

  <!-- Header -->
  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Admin Panel</h2>
        <p class="sub" style="margin-top:6px;">Stats, contrôle comptes, logements et accès rapide aux réservations.</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="index.php?page=dashboard_admin">← Dashboard</a>
      <a class="btn btn-primary" href="index.php?page=admin_reservations">Gérer réservations →</a>
        <a class="btn" href="index.php?page=home">Home</a>
      </div>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
      <b style="color:#ffb4b4;">Erreur</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($error) ?></div>
      <div style="margin-top:10px;">
        <a class="btn" href="index.php?page=dashboard_admin">Retour</a>
      </div>
    </div>
  <?php else: ?>

  <!-- Stats cards -->
  <div class="grid3" style="margin-bottom:14px; grid-template-columns: repeat(4, 1fr);">
    <div class="card">
      <div class="pill">👥 Users</div>
      <h3 style="margin:10px 0 0; font-size:20px;"><?= (int)$stats['users'] ?></h3>
      <p class="sub" style="margin:6px 0 0;">Comptes au total</p>
    </div>
    <div class="card">
      <div class="pill">🏠 Logements</div>
      <h3 style="margin:10px 0 0; font-size:20px;"><?= (int)$stats['rentals'] ?></h3>
      <p class="sub" style="margin:6px 0 0;">Annonces totales</p>
    </div>
    <div class="card">
      <div class="pill">📌 Réservations</div>
      <h3 style="margin:10px 0 0; font-size:20px;"><?= (int)$stats['reservations'] ?></h3>
      <p class="sub" style="margin:6px 0 0;">Toutes réservations</p>
    </div>
    <div class="card">
      <div class="pill">💰 Revenus</div>
      <h3 style="margin:10px 0 0; font-size:20px;"><?= htmlspecialchars((string)$stats['revenue']) ?></h3>
      <p class="sub" style="margin:6px 0 0;">Bookeds uniquement</p>
    </div>
  </div>
  
  <!-- Tables -->
  <div class="layout" style="grid-template-columns: 1fr 1fr; gap:14px;">

    <!-- Users table -->
    <section class="glass" style="padding:16px; overflow:hidden;">
      <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap;">
        <h3 style="margin:0;">Utilisateurs</h3>
        <span class="pill">Total: <?= count($users) ?></span>
      </div>
      
      <div style="overflow:auto; margin-top:12px;">
        <table style="width:100%; border-collapse:collapse; min-width:640px;">
          <thead>
            <tr style="background:rgba(255,255,255,.04);">
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">ID</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Nom</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Email</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Role</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Status</th>
              <th style="text-align:right;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <?php $isActive = ((string)$u['status'] === 'active'); ?>
              <tr>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="pill">#<?= (int)$u['id'] ?></span>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <b><?= htmlspecialchars($u['name']) ?></b>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="sub"><?= htmlspecialchars($u['email']) ?></span>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="pill"><?= htmlspecialchars($u['role']) ?></span>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <?php if ($isActive): ?>
                    <span class="pill" style="border-color: rgba(80,255,170,.25);">✅ active</span>
                    <?php else: ?>
                      <span class="pill" style="border-color: rgba(255,180,80,.25);">⚠ disabled</span>
                      <?php endif; ?>
                    </td>
                    <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08); text-align:right;">
                      <?php if ($isActive): ?>
                      <a class="btn btn-danger js-confirm"
                         href="index.php?page=admin_panel&toggle_user=<?= (int)$u['id'] ?>&status=disabled"
                         data-title="Désactiver utilisateur"
                         data-message="Tu es sûr de vouloir désactiver l'utilisateur #<?= (int)$u['id'] ?> (<?= htmlspecialchars($u['name']) ?>) ?">
                        Désactiver
                      </a>
                  <?php else: ?>
                    <a class="btn btn-primary"
                    href="index.php?page=admin_panel&toggle_user=<?= (int)$u['id'] ?>&status=active">Activer</a>
                    <?php endif; ?>
                  </td>
                </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
    
    <!-- Rentals table -->
    <section class="glass" style="padding:16px; overflow:hidden;">
      <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap;">
        <h3 style="margin:0;">Logements</h3>
        <span class="pill">Total: <?= count($rentals) ?></span>
      </div>

      <div style="overflow:auto; margin-top:12px;">
        <table style="width:100%; border-collapse:collapse; min-width:680px;">
          <thead>
            <tr style="background:rgba(255,255,255,.04);">
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">ID</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Titre</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Ville</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Hôte</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Status</th>
              <th style="text-align:right;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rentals as $r): ?>
              <?php $isActive = ((string)$r['status'] === 'active'); ?>
              <tr>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="pill">#<?= (int)$r['id'] ?></span>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <b><?= htmlspecialchars($r['title']) ?></b>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="sub">📍 <?= htmlspecialchars($r['city']) ?></span>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="sub">#<?= (int)$r['host_id'] ?> <?= htmlspecialchars($r['host_name']) ?></span>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <?php if ($isActive): ?>
                    <span class="pill" style="border-color: rgba(80,255,170,.25);">✅ active</span>
                    <?php else: ?>
                      <span class="pill" style="border-color: rgba(255,180,80,.25);">⚠ disabled</span>
                  <?php endif; ?>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08); text-align:right;">
                  <?php if ($isActive): ?>
                    <a class="btn btn-danger js-confirm"
                       href="index.php?page=admin_panel&toggle_rental=<?= (int)$r['id'] ?>&status=disabled"
                       data-title="Désactiver logement"
                       data-message="Tu es sûr de vouloir désactiver le logement #<?= (int)$r['id'] ?> (<?= htmlspecialchars($r['title']) ?>) ?">
                      Désactiver
                    </a>

                       <?php else: ?>
                        <a class="btn btn-primary"
                        href="index.php?page=admin_panel&toggle_rental=<?= (int)$r['id'] ?>&status=active">Activer</a>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Top rentals -->
    
    <!-- Footer actions -->
    
    <?php endif; ?>
    
    <div class="glass" style="padding:16px; margin-bottom:14px;">
      <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center;">
        <div>
          <h3 style="margin:0;">Top 10 logements (revenus)</h3>
          <p class="sub" style="margin:6px 0 0;">Classement basé sur le total booked.</p>
        </div>
        <a class="btn" href="index.php?page=home">Voir côté public →</a>
      </div>
  
      <div style="display:grid; gap:10px; margin-top:12px;">
        <?php foreach ($stats['topRentals'] as $r): ?>
          <div class="card" style="display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap;">
            <div style="min-width:240px;">
              <b>#<?= (int)$r['id'] ?> <?= htmlspecialchars($r['title']) ?></b>
              <div class="sub" style="margin-top:4px;">📍 <?= htmlspecialchars($r['city']) ?></div>
            </div>
            <div class="pill" style="border-color: rgba(80,255,170,.25);">💳 Revenue: <?= htmlspecialchars((string)$r['revenue']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  </main>

<style>
  @media (max-width: 1100px){
    .grid3[style*="repeat(4"]{ grid-template-columns: repeat(2, 1fr) !important; }
  }
  @media (max-width: 980px){
    .layout{ grid-template-columns: 1fr !important; }
    .grid3[style*="repeat(4"]{ grid-template-columns: 1fr !important; }
  }
</style>

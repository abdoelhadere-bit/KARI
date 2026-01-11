<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';

use utils\Guard;
use services\AdminService;

Guard::requireRole('admin');

$error = '';
$reservations = [];

try {
    $reservations = (new AdminService())->reservations(30);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<main class="container" style="padding-top:14px;">

  <!-- Header -->
  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Admin — Réservations</h2>
      
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="index.php?page=admin_panel">← Admin panel</a>
        <a class="btn" href="index.php?page=dashboard_admin">Dashboard</a>
      </div>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="glass" style="padding:12px; border:1px solid rgba(255,80,80,.35); background:rgba(255,80,80,.08); margin-bottom:12px;">
      <b style="color:#ffb4b4;">Erreur</b>
      <div class="sub" style="margin-top:6px;"><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <?php if (empty($reservations)): ?>
    <div class="glass" style="padding:16px;">
      <p class="sub" style="margin:0;">Aucune réservation.</p>
    </div>
  <?php else: ?>

    <div class="glass" style="padding:16px; overflow:hidden;">
      <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap;">
        <h3 style="margin:0;">Table réservations</h3>
        <span class="pill">Total affiché: <?= count($reservations) ?></span>
      </div>

      <div style="overflow:auto; margin-top:12px;">
        <table style="width:100%; border-collapse:collapse; min-width:860px;">
          <thead>
            <tr style="background:rgba(255,255,255,.04);">
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">ID</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Logement</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Voyageur</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Dates</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Total</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Status</th>
              <th style="text-align:right;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Action</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($reservations as $res): ?>
              <?php $isBooked = ((string)$res['status'] === 'booked'); ?>
              <tr>
                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="pill">#<?= (int)$res['id'] ?></span>
                </td>

                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <b><?= htmlspecialchars($res['rental_title']) ?></b>
                </td>

                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="sub"><?= htmlspecialchars($res['traveler_name']) ?></span>
                </td>

                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="sub">📅 <?= htmlspecialchars($res['start_date']) ?> → <?= htmlspecialchars($res['end_date']) ?></span>
                </td>

                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <span class="pill" style="border-color: rgba(124,92,255,.35);">
                    💳 <?= htmlspecialchars((string)$res['total_price']) ?>
                  </span>
                </td>

                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                  <?php if ($isBooked): ?>
                    <span class="pill" style="border-color: rgba(80,255,170,.25);">✅ booked</span>
                  <?php else: ?>
                    <span class="pill" style="border-color: rgba(255,180,80,.25);">⚠ <?= htmlspecialchars((string)$res['status']) ?></span>
                  <?php endif; ?>
                </td>

                <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08); text-align:right;">
                  <?php if ($isBooked): ?>
                    <a class="btn btn-danger js-confirm"
                       href="index.php?page=admin_cancel_reservation&id=<?= (int)$res['id'] ?>"
                       data-title="Annuler réservation"
                       data-message="Tu es sûr de vouloir annuler cette réservation #<?= (int)$res['id'] ?> ?">
                      Annuler
                    </a>

                  <?php else: ?>
                    <span class="sub" style="opacity:.8;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div style="margin-top:12px; display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; align-items:center;">
        <a class="btn" href="index.php?page=admin_panel">Retour admin panel</a>
      </div>
    </div>

  <?php endif; ?>

</main>

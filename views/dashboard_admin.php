<?php
declare(strict_types=1);

require __DIR__ . '/partials/header.php';

use utils\Guard;
use services\AdminService;

Guard::requireRole('admin');

$service = new AdminService();

// ✅ On récupère des données (lecture uniquement) pour alimenter le dashboard
$stats = $service->stats();                 // ['users'=>..,'rentals'=>..,'booked_reservations'=>..,'revenue'=>..,'top_rentals'=>..]
$recent = $service->reservations(8);        // dernières réservations

$users   = (int)($stats['users'] ?? 0);
$rentals = (int)($stats['rentals'] ?? 0);
$booked  = (int)($stats['booked_reservations'] ?? 0);
$revenue = (float)($stats['revenue'] ?? 0);
$topRentals = $stats['top_rentals'] ?? [];
?>

<main class="container" style="padding-top:14px;">

  <!-- Header block -->
  <div class="glass" style="padding:16px; margin-bottom:14px;">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
      <div>
        <h2 class="h1" style="margin:0;">Dashboard Admin</h2>
        <p class="sub" style="margin-top:6px;">
          Vue d’ensemble : utilisateurs, logements, réservations et revenus.
        </p>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn" href="index.php?page=home">← Home</a>
        <a class="btn btn-primary" href="index.php?page=admin_reservations">Réservations</a>
        <a class="btn" href="index.php?page=admin_panel">Admin panel</a>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="grid3" style="grid-template-columns: repeat(4, 1fr); margin-bottom:14px;">
    <div class="card">
      <div class="pill">👥 Utilisateurs</div>
      <div style="font-size:26px;font-weight:900;margin-top:10px;"><?= $users ?></div>
      <div class="sub" style="margin-top:6px;">Comptes créés</div>
    </div>

    <div class="card">
      <div class="pill">🏠 Logements</div>
      <div style="font-size:26px;font-weight:900;margin-top:10px;"><?= $rentals ?></div>
      <div class="sub" style="margin-top:6px;">Total logements</div>
    </div>

    <div class="card">
      <div class="pill">📌 Réservations</div>
      <div style="font-size:26px;font-weight:900;margin-top:10px;"><?= $booked ?></div>
      <div class="sub" style="margin-top:6px;">Bookées (actives)</div>
    </div>

    <div class="card">
      <div class="pill">💰 Revenus</div>
      <div style="font-size:26px;font-weight:900;margin-top:10px;"><?= number_format($revenue, 2) ?></div>
      <div class="sub" style="margin-top:6px;">Somme total (booked)</div>
    </div>
  </div>

  <div class="layout" style="grid-template-columns: 1.3fr .7fr;">

    <!-- Recent reservations -->
    <section class="glass" style="padding:16px;">
      <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;">
        <div>
          <h3 style="margin:0;">Dernières réservations</h3>
          <p class="sub" style="margin-top:6px;">Actions rapides : voir et annuler si besoin.</p>
        </div>
        <a class="btn" href="index.php?page=admin_reservations">Tout voir →</a>
      </div>

      <?php if (empty($recent)): ?>
        <div class="glass" style="padding:14px;margin-top:10px;">
          <p class="sub" style="margin:0;">Aucune réservation.</p>
        </div>
      <?php else: ?>
        <div class="glass" style="padding:0; overflow:hidden; margin-top:10px;">
          <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse;">
              <thead>
                <tr style="background:rgba(255,255,255,.04);">
                  <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Logement</th>
                  <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Voyageur</th>
                  <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Dates</th>
                  <th style="text-align:left;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Statut</th>
                  <th style="text-align:right;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.10);">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent as $res): ?>
                  <tr>
                    <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                      <b><?= htmlspecialchars((string)$res['rental_title']) ?></b>
                      <div class="sub" style="margin-top:4px;">📍 <?= htmlspecialchars((string)$res['rental_city']) ?></div>
                    </td>
                    <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                      <?= htmlspecialchars((string)$res['traveler_name']) ?>
                      <div class="sub" style="margin-top:4px;"><?= htmlspecialchars((string)$res['traveler_email']) ?></div>
                    </td>
                    <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                      <?= htmlspecialchars((string)$res['start_date']) ?> → <?= htmlspecialchars((string)$res['end_date']) ?>
                      <div class="sub" style="margin-top:4px;">💳 <?= htmlspecialchars((string)$res['total_price']) ?></div>
                    </td>
                    <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08);">
                      <?php if (($res['status'] ?? '') === 'booked'): ?>
                        <span class="pill" style="border-color:rgba(80,255,170,.25);">✅ booked</span>
                      <?php else: ?>
                        <span class="pill" style="border-color:rgba(255,180,80,.25);">⚠ <?= htmlspecialchars((string)$res['status']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td style="padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08); text-align:right;">
                      <?php if (($res['status'] ?? '') === 'booked'): ?>
                        <a class="btn btn-danger"
                           href="index.php?page=admin_cancel_reservation&id=<?= (int)$res['id'] ?>"
                           onclick="return confirm('Annuler cette réservation ?');">
                          Annuler
                        </a>
                      <?php else: ?>
                        <span class="sub">—</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </section>

    <!-- Top rentals + quick actions -->
    <aside class="glass" style="padding:16px;">
      <h3 style="margin:0 0 10px;">Top logements</h3>
      <p class="sub" style="margin:0 0 12px;">Classement par revenus (booked).</p>

      <?php if (empty($topRentals)): ?>
        <div class="glass" style="padding:14px;">
          <p class="sub" style="margin:0;">Aucun résultat.</p>
        </div>
      <?php else: ?>
        <div style="display:grid; gap:10px;">
          <?php foreach ($topRentals as $tr): ?>
            <div class="card" style="padding:12px;">
              <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;">
                <div style="min-width:0;">
                  <b style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?= htmlspecialchars((string)$tr['title']) ?>
                  </b>
                  <div class="sub" style="margin-top:4px;">📍 <?= htmlspecialchars((string)$tr['city']) ?></div>
                </div>
                <span class="pill">💰 <?= htmlspecialchars((string)$tr['revenue']) ?></span>
              </div>
              <div style="margin-top:10px;">
                <a class="btn" href="index.php?page=rental&id=<?= (int)$tr['id'] ?>">Voir</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <hr style="border:0;border-top:1px solid rgba(255,255,255,.10);margin:14px 0;">

      <div>
        <div class="sub" style="margin-bottom:10px;">Raccourcis</div>
        <div style="display:grid; gap:10px;">
          <a class="btn" href="index.php?page=admin_panel">⚙ Admin panel</a>
          <a class="btn" href="index.php?page=admin_reservations">📌 Gérer réservations</a>
          <a class="btn" href="index.php?page=home">🏠 Retour Home</a>
        </div>
      </div>
    </aside>

  </div>

</main>

<style>
  /* responsive: stats 4 -> 2 -> 1 */
  @media (max-width: 980px){
    .grid3[style*="repeat(4"]{ grid-template-columns: repeat(2, 1fr) !important; }
    .layout{ grid-template-columns: 1fr !important; }
  }
  @media (max-width: 640px){
    .grid3[style*="repeat(4"]{ grid-template-columns: 1fr !important; }
  }
</style>

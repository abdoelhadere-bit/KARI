<?php
declare(strict_types=1);

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

<h2>Admin Panel</h2>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <p><a href="index.php?page=dashboard_admin">Retour</a></p>
  <?php exit; ?>
<?php endif; ?>

<h3>Stats</h3>
<ul>
  <li>Total users: <?= (int)$stats['users'] ?></li>
  <li>Total rentals: <?= (int)$stats['rentals'] ?></li>
  <li>Total reservations: <?= (int)$stats['reservations'] ?></li>
  <li>Revenus (booked): <?= htmlspecialchars((string)$stats['revenue']) ?></li>
</ul>

<h3>Top 10 logements (revenus)</h3>
<ol>
  <?php foreach ($stats['topRentals'] as $r): ?>
    <li>
      #<?= (int)$r['id'] ?> <?= htmlspecialchars($r['title']) ?> (<?= htmlspecialchars($r['city']) ?>)
      — revenue: <?= htmlspecialchars((string)$r['revenue']) ?>
    </li>
  <?php endforeach; ?>
</ol>

<hr>

<h3>Utilisateurs</h3>
<table border="1" cellpadding="6">
  <tr>
    <th>ID</th><th>Nom</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th>
  </tr>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= (int)$u['id'] ?></td>
      <td><?= htmlspecialchars($u['name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= htmlspecialchars($u['role']) ?></td>
      <td><?= htmlspecialchars($u['status']) ?></td>
      <td>
        <?php if ($u['status'] === 'active'): ?>
          <a href="index.php?page=admin_panel&toggle_user=<?= (int)$u['id'] ?>&status=disabled">Désactiver</a>
        <?php else: ?>
          <a href="index.php?page=admin_panel&toggle_user=<?= (int)$u['id'] ?>&status=active">Activer</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<hr>

<h3>Logements</h3>
<table border="1" cellpadding="6">
  <tr>
    <th>ID</th><th>Titre</th><th>Ville</th><th>Hôte</th><th>Status</th><th>Action</th>
  </tr>
  <?php foreach ($rentals as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><?= htmlspecialchars($r['title']) ?></td>
      <td><?= htmlspecialchars($r['city']) ?></td>
      <td>#<?= (int)$r['host_id'] ?> <?= htmlspecialchars($r['host_name']) ?></td>
      <td><?= htmlspecialchars($r['status']) ?></td>
      <td>
        <?php if ($r['status'] === 'active'): ?>
          <a href="index.php?page=admin_panel&toggle_rental=<?= (int)$r['id'] ?>&status=disabled">Désactiver</a>
        <?php else: ?>
          <a href="index.php?page=admin_panel&toggle_rental=<?= (int)$r['id'] ?>&status=active">Activer</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<p><a href="index.php?page=admin_reservations">Gérer les réservations</a></p>

<p><a href="index.php?page=dashboard_admin">Retour dashboard</a></p>

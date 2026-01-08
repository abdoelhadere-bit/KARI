<?php
declare(strict_types=1);

use utils\Guard;
use services\HostRentalService;

Guard::requireAnyRole(['host','admin']);

$service = new HostRentalService();

$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $postAction = $_POST['action'] ?? '';

        if ($postAction === 'create') {
            $service->create([
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'price_per_night' => (float)($_POST['price_per_night'] ?? 0),
                'max_guests' => (int)($_POST['max_guests'] ?? 1),
            ], $_FILES); 

            header("Location: index.php?page=host_rentals");
            exit;
        }

        if ($postAction === 'update') {
            $rid = (int)($_POST['id'] ?? 0);

            $service->update($rid, [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'price_per_night' => (float)($_POST['price_per_night'] ?? 0),
                'max_guests' => (int)($_POST['max_guests'] ?? 1),
            ], $_FILES); 

            header("Location: index.php?page=host_rentals");
            exit;
        }

        if ($postAction === 'delete') {
            $rid = (int)($_POST['id'] ?? 0);
            $service->delete($rid);

            header("Location: index.php?page=host_rentals");
            exit;
        }

    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
}


// Liste
try {
    $rentals = $service->myRentals();
} catch (\Throwable $e) {
    $rentals = [];
    $error = $e->getMessage();
}

// Edit : charger le logement
$editRental = null;
if ($action === 'edit' && $id > 0) {
    try {
        $editRental = $service->getForEdit($id);
    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
}
?>

<h2>Mes logements (Hôte)</h2>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p>
  <a href="index.php?page=host_rentals&action=create">+ Ajouter logement</a>
  | <a href="index.php?page=dashboard_host">Retour dashboard</a>
</p>

<!-- FORM CREATE -->
<?php if ($action === 'create'): ?>
  <h3>Ajouter un logement</h3>

  <form method="POST" action="index.php?page=host_rentals" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create">

    <label>Image </label><br>
    
    <img id="preview_create" src="" alt="Preview" style="display:none;width:240px;height:160px;object-fit:cover;border-radius:10px;border:1px solid #ddd;margin-top:10px;">
    <input type="file" name="image" id="image_create" accept="image/*" required>

    <input name="title" placeholder="Titre" required><br><br>
    <input name="city" placeholder="Ville" required><br><br>
    <input name="address" placeholder="Adresse"><br><br>
    <input type="number" step="0.01" name="price_per_night" placeholder="Prix/nuit" required><br><br>
    <input type="number" name="max_guests" placeholder="Max guests" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>

    <button type="submit">Créer</button>  
    <a href="index.php?page=host_rentals">Annuler</a>
  </form>
  <hr>
<?php endif; ?>

<!-- FORM EDIT -->
<?php if ($action === 'edit' && $editRental): ?>
  <h3>Modifier un logement</h3>

  <form method="POST" action="index.php?page=host_rentals" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= (int)$editRental['id'] ?>">

    <?php if (!empty($editRental['image'])): ?>
      <p>Image actuelle :</p>
      <img src="<?= htmlspecialchars($editRental['image']) ?>" style="width:160px;border-radius:10px;"><br><br>
    <?php endif; ?>

    <label>Nouvelle image </label><br>
    
    <img id="preview_edit" src="" alt="Preview" style="display:none;width:240px;height:160px;object-fit:cover;border-radius:10px;border:1px solid #ddd;margin-top:10px;">
    <input type="file" name="image" id="image_edit" accept="image/*">


    <input name="title" value="<?= htmlspecialchars($editRental['title']) ?>" required><br><br>
    <input name="city" value="<?= htmlspecialchars($editRental['city']) ?>" required><br><br>
    <input name="address" value="<?= htmlspecialchars((string)($editRental['address'] ?? '')) ?>"><br><br>
    <input type="number" step="0.01" name="price_per_night" value="<?= htmlspecialchars((string)$editRental['price_per_night']) ?>" required><br><br>
    <input type="number" name="max_guests" value="<?= (int)$editRental['max_guests'] ?>" required><br><br>
    <textarea name="description"><?= htmlspecialchars((string)($editRental['description'] ?? '')) ?></textarea><br><br>

    <button type="submit">Enregistrer</button>
    <a href="index.php?page=host_rentals">Annuler</a>
  </form>
  <hr>
<?php endif; ?>

<!-- LIST -->
<?php if (empty($rentals)): ?>
  <p>Aucun logement.</p>
<?php else: ?>
  <ul>
    <?php foreach ($rentals as $r): ?>
      <li>
        <b><?= htmlspecialchars($r['title']) ?></b> — <?= htmlspecialchars($r['city']) ?>
        | <a href="index.php?page=host_rentals&action=edit&id=<?= (int)$r['id'] ?>">Modifier</a>

        | <form method="POST" action="index.php?page=host_rentals" style="display:inline;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button type="submit" onclick="return confirm('Supprimer ?')">Supprimer</button>
          </form>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<script>
function bindFilePreview(inputId, imgId) {
  const input = document.getElementById(inputId);
  const img = document.getElementById(imgId);
  if (!input || !img) return;

  input.addEventListener('change', () => {
    const file = input.files && input.files[0];
    if (!file) {
      img.style.display = 'none';
      img.src = '';
      return;
    }
    if (!file.type.startsWith('image/')) {
      img.style.display = 'none';
      img.src = '';
      return;
    }
    img.src = URL.createObjectURL(file);
    img.style.display = 'block';
  });
}

bindFilePreview('image_create', 'preview_create');
bindFilePreview('image_edit', 'preview_edit');
</script>



<?php
declare(strict_types=1);

namespace services;

use core\Database;
use repositories\RentalRepository;
use utils\Session;
use utils\Guard;
use exceptions\NotFoundException;
use exceptions\PermissionDeniedException;

class HostRentalService
{
    private RentalRepository $repo;

    public function __construct()
    {
        $pdo = Database::getConnection();
        $this->repo = new RentalRepository($pdo);
    }

    public function myRentals(): array
    {
        Guard::requireAnyRole(['host', 'admin']);
        $hostId = (int) Session::get('user_id');
        return $this->repo->listByHost($hostId);
    }

    public function create(array $data, array $files): int
    {
        Guard::requireAnyRole(['host', 'admin']);
        $hostId = (int) Session::get('user_id');

        if (!isset($files['image']) || $files['image']['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Image obligatoire.");
        }

        $imagePath = $this->uploadImage($files['image'], $hostId);
        $data['image'] = $imagePath;

        return $this->repo->create($hostId, $data);
    }

    public function getForEdit(int $rentalId): array
    {
        Guard::requireAnyRole(['host', 'admin']);

        $rental = $this->repo->findById($rentalId);
        if (!$rental) throw new NotFoundException("Logement introuvable.");

        $role   = (string) Session::get('role');
        $userId = (int) Session::get('user_id');

        if ($role !== 'admin' && (int)$rental['host_id'] !== $userId) {
            throw new PermissionDeniedException("Vous ne pouvez modifier que vos logements.");
        }

        return $rental;
    }

    public function update(int $rentalId, array $data, array $files): void
    {
        $old = $this->getForEdit($rentalId);

        if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
            $hostId = (int) Session::get('user_id');
            $newPath = $this->uploadImage($files['image'], $hostId);

            // supprimer ancienne image 
            if (!empty($old['image'])) {
                $oldFile = __DIR__ . '/../public/' . $old['image'];
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $data['image'] = $newPath;
        }

        $this->repo->update($rentalId, $data);
    }

    public function delete(int $rentalId): void
    {
        $old = $this->getForEdit($rentalId);

        if (!empty($old['image'])) {
            $oldFile = __DIR__ . '/../public/' . $old['image'];
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $this->repo->delete($rentalId);
    }

    private function uploadImage(array $image, int $hostId): string
    {
        $uploadDir = __DIR__ . '/../public/uploads/rentals';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if ($ext === '') $ext = 'jpg';

        $filename = 'rental_' . $hostId . '_' . time() . '.' . $ext;
        $dest = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($image['tmp_name'], $dest)) {
            throw new \Exception("Upload échoué.");
        }

        return 'uploads/rentals/' . $filename;
    }
}

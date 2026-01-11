<?php
declare(strict_types=1);

namespace services;

use repositories\UserRepository;
use PDO;

class ProfileService
{
    private UserRepository $users;

    public function __construct()
    {
        $pdo = $this->getPDO();
        $this->users = new UserRepository($pdo);
    }

    private function getPDO(): PDO
    {
        if (class_exists('\core\Database') && method_exists('\core\Database', 'getConnection')) {
            return \core\Database::getConnection();
        }

        if (class_exists('\config\Database') && method_exists('\config\Database', 'getInstance')) {
            return \config\Database::getInstance()->getConnection();
        }

        throw new \RuntimeException("Impossible de récupérer PDO. Vérifie ta classe Database.");
    }

    public function getProfile(int $userId): array
    {
        $u = $this->users->findById($userId);
        if (!$u) {
            throw new \RuntimeException("Utilisateur introuvable.");
        }
        return $u;
    }

    public function updateProfile(int $userId, string $name, string $email): void
    {
        $name = trim($name);
        $email = trim($email);

        if ($name === '') throw new \RuntimeException("Nom obligatoire.");
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new \RuntimeException("Email invalide.");

        if ($this->users->emailExists($email, $userId)) {
            throw new \RuntimeException("Cet email est déjà utilisé.");
        }

        $ok = $this->users->updateProfile($userId, $name, $email);
        if (!$ok) throw new \RuntimeException("Impossible de mettre à jour le profil.");
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword, string $confirm): void
    {
        if ($oldPassword === '') {
            throw new \RuntimeException("Mot de passe actuel obligatoire.");
        }
        if ($newPassword === '' || $confirm === '') {
            throw new \RuntimeException("Nouveau mot de passe + confirmation obligatoires.");
        }

        if ($newPassword !== $confirm) throw new \RuntimeException("Confirmation incorrecte.");
        if (mb_strlen($newPassword) < 6) throw new \RuntimeException("Mot de passe trop court (min 6).");

        $u = $this->users->findById($userId);
        if (!$u) throw new \RuntimeException("Utilisateur introuvable.");

        if (!password_verify($oldPassword, (string)$u['password'])) {
            throw new \RuntimeException("Ancien mot de passe incorrect.");
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $ok = $this->users->updatePassword($userId, $hash);

        if (!$ok) throw new \RuntimeException("Impossible de changer le mot de passe.");
    }
}

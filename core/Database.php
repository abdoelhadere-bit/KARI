<?php
declare(strict_types=1);

namespace core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = require dirname(__DIR__) . '/config/config.php';
        $db = $config;

        $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";

        try {
            self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Erreur DB: " . $e->getMessage());
        }

        return self::$pdo;
    }
}

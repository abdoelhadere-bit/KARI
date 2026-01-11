<?php
declare(strict_types=1);

namespace repositories;

use PDO;
use entities\Rental;

final class RentalRepository
{
    public function __construct(private PDO $pdo) {}

    private function map(array $r): Rental
    {
        $img = $r['image'] ?? ($r['cover_path'] ?? null);

        return new Rental(
            (int)$r['id'],
            (int)$r['host_id'],
            (string)$r['title'],
            (string)$r['city'],
            (float)$r['price_per_night'],
            (int)$r['max_guests'],
            $r['address'] ?? null,
            $r['description'] ?? null,
            $img !== '' ? $img : null,
            (string)($r['status'] ?? 'active'),
            $r['host_name'] ?? null
        );
    }

    public function findById(int $id): ?Rental
    {
        $sql = "SELECT r.*, u.name AS host_name
                FROM rentals r
                JOIN users u ON u.id = r.host_id
                WHERE r.id = :id
                LIMIT 1";
        $st = $this->pdo->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->map($row) : null;
    }

    public function listActive(int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $total = (int)$this->pdo
            ->query("SELECT COUNT(*) FROM rentals WHERE status='active'")
            ->fetchColumn();

        $pages = max(1, (int)ceil($total / $perPage));

        $sql = "SELECT r.*, u.name AS host_name
                FROM rentals r
                JOIN users u ON u.id = r.host_id
                WHERE r.status='active'
                ORDER BY r.id DESC
                LIMIT :limit OFFSET :offset";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, PDO::PARAM_INT);
        $st->execute();

        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        $items = array_map(fn($row) => $this->map($row), $rows);

        return ['items' => $items, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    public function search(array $filters, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = ["r.status='active'"];
        $params = [];

        if (!empty($filters['city'])) {
            $where[] = "r.city LIKE :city";
            $params[':city'] = '%' . $filters['city'] . '%';
        }
        if ($filters['min_price'] !== '' && is_numeric($filters['min_price'])) {
            $where[] = "r.price_per_night >= :minp";
            $params[':minp'] = (float)$filters['min_price'];
        }
        if ($filters['max_price'] !== '' && is_numeric($filters['max_price'])) {
            $where[] = "r.price_per_night <= :maxp";
            $params[':maxp'] = (float)$filters['max_price'];
        }
        if ($filters['guests'] !== '' && is_numeric($filters['guests'])) {
            $where[] = "r.max_guests >= :guests";
            $params[':guests'] = (int)$filters['guests'];
        }

        $whereSql = implode(' AND ', $where);

        // total
        $stTotal = $this->pdo->prepare("SELECT COUNT(*) FROM rentals r WHERE $whereSql");
        $stTotal->execute($params);
        $total = (int)$stTotal->fetchColumn();

        $pages = max(1, (int)ceil($total / $perPage));

        // items
        $sql = "SELECT r.*, u.name AS host_name
                FROM rentals r
                JOIN users u ON u.id = r.host_id
                WHERE $whereSql
                ORDER BY r.id DESC
                LIMIT :limit OFFSET :offset";
        $st = $this->pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, PDO::PARAM_INT);

        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        $items = array_map(fn($row) => $this->map($row), $rows);

        return ['items' => $items, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }
}

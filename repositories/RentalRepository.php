<?php
declare(strict_types=1);

namespace repositories;

use PDO;

class RentalRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT r.*, u.name AS host_name, u.email AS host_email
                                     FROM rentals r
                                     JOIN users u ON u.id = r.host_id
                                     WHERE r.id = ?
                                     LIMIT 1");
        $stmt->execute([$id]);
        $rental = $stmt->fetch(PDO::FETCH_ASSOC);

        return $rental ?: null;
    }

    public function listActive(int $page = 1, int $perPage = 6): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        // total
        $total = (int)$this->pdo->query("SELECT COUNT(*) FROM rentals WHERE status='active'")->fetchColumn();

        // items
        $stmt = $this->pdo->prepare("SELECT r.*, u.name AS host_name
                                      FROM rentals r
                                      JOIN users u ON u.id = r.host_id
                                      WHERE r.status='active'
                                      ORDER BY r.created_at DESC
                                      LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, (INT)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page'  => $page,
            'perPage' => $perPage,
            'pages' => (int)ceil($total / $perPage),
        ];
    }

    public function search(array $filters, int $page = 1, int $perPage = 6): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
    
        $where = ["r.status='active'"];
        $params = [];
    
        // ville
        if (!empty($filters['city'])) {
            $where[] = "r.city LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }
    
        // prix min/max
        if (!empty($filters['min_price'])) {
            $where[] = "r.price_per_night >= ?";
            $params[] = (float)$filters['min_price'];
        }
    
        if (!empty($filters['max_price'])) {
            $where[] = "r.price_per_night <= ?";
            $params[] = (float)$filters['max_price'];
        }
    
        // guests
        if (!empty($filters['guests'])) {
            $where[] = "r.max_guests >= ?";
            $params[] = (int)$filters['guests'];
        }
    
        // dates
        $hasDates = !empty($filters['start_date']) && !empty($filters['end_date']);
        if ($hasDates) {
            $start = $filters['start_date'];
            $end   = $filters['end_date'];
        
            // Exclure logements avec réservation booked qui chevauche
            $where[] = "NOT EXISTS (SELECT 1 FROM reservations res
                        WHERE res.rental_id = r.id
                        AND res.status = 'booked'
                        AND NOT (res.end_date <= ? OR res.start_date >= ?))";
            $params[] = $start;
            $params[] = $end;
        }
    
        $whereSql = implode(" AND ", $where);
    
        // total
        $stmtTotal = $this->pdo->prepare("SELECT COUNT(*) FROM rentals r WHERE $whereSql");
        $stmtTotal->execute($params);
        $total = (int)$stmtTotal->fetchColumn();
    
        // items
        $sql = "SELECT r.*, u.name AS host_name
                FROM rentals r
                JOIN users u ON u.id = r.host_id
                WHERE $whereSql
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }

        $stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);

        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page'  => $page,
            'perPage' => $perPage,
            'pages' => (int)ceil($total / $perPage),
        ];

    }

    public function listByHost(int $hostId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rentals
                                     WHERE host_id = ?
                                     ORDER BY created_at DESC");
        $stmt->execute([$hostId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(int $hostId, array $data): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO rentals (host_id, title, description, city, address, image, price_per_night, max_guests, status)
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");

        $stmt->execute([$hostId, $data['title'], $data['description'], $data['city'], $data['address'], $data['image'], $data['price_per_night'],$data['max_guests']]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $rentalId, array $data): void
    {
        $hasImage = isset($data['image']) && $data['image'] !== '';
    
        $sql = "UPDATE rentals
                SET title = ?, description = ?, city = ?, address = ?, price_per_night = ?, max_guests = ?";
    
        $params = [$data['title'], $data['description'], $data['city'], $data['address'], $data['price_per_night'], $data['max_guests']];
    
        if ($hasImage) {
            $sql .= ", image = ?";
            $params[] = $data['image'];
        }
    
        $sql .= " WHERE id = ?";
        $params[] = $rentalId;
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }


    public function delete(int $rentalId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM rentals WHERE id = ?");
        $stmt->execute([$rentalId]);
    }


}

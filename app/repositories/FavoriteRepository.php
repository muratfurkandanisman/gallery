<?php

class FavoriteRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function listByUser(int $userId): array
    {
        $sql = "SELECT f.car_id, f.created_at,
                       c.brand, c.model, c.year, c.price, c.status, c.images
                FROM favorites f
                JOIN cars c ON c.car_id = f.car_id
                WHERE f.user_id = :user_id AND c.is_deleted = 0
                ORDER BY f.created_at DESC";

        $rows = $this->db->fetchAll($sql, ['user_id' => $userId]);

        foreach ($rows as &$row) {
            $row['IMAGE_PATH'] = $this->firstImage($row['IMAGES'] ?? null);
        }
        unset($row);

        return $rows;
    }

    public function exists(int $userId, int $carId): bool
    {
        $sql = "SELECT 1 AS found FROM favorites WHERE user_id = :user_id AND car_id = :car_id FETCH FIRST 1 ROWS ONLY";
        return $this->db->fetchOne($sql, ['user_id' => $userId, 'car_id' => $carId]) !== null;
    }

    public function add(int $userId, int $carId): bool
    {
        if ($this->db->isOracle()) {
            $sql = "INSERT INTO favorites (user_id, car_id, created_at) VALUES (:user_id, :car_id, SYSTIMESTAMP)";
        } else {
            $sql = "INSERT INTO favorites (user_id, car_id, created_at) VALUES (:user_id, :car_id, CURRENT_TIMESTAMP)";
        }

        return $this->db->execute($sql, ['user_id' => $userId, 'car_id' => $carId]);
    }

    public function remove(int $userId, int $carId): bool
    {
        $sql = "DELETE FROM favorites WHERE user_id = :user_id AND car_id = :car_id";
        return $this->db->execute($sql, ['user_id' => $userId, 'car_id' => $carId]);
    }

    private function firstImage(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || empty($decoded[0]) || !is_string($decoded[0])) {
            return null;
        }

        return $decoded[0];
    }
}

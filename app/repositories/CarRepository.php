<?php

class CarRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function listAvailable(array $filters = []): array
    {
        $where = ["c.status = 'AVAILABLE'", 'c.is_deleted = 0'];
        $params = [];

        if (!empty($filters['brand'])) {
            $where[] = 'LOWER(c.brand) LIKE LOWER(:brand)';
            $params['brand'] = '%' . $filters['brand'] . '%';
        }
        if (!empty($filters['model'])) {
            $where[] = 'LOWER(c.model) LIKE LOWER(:model)';
            $params['model'] = '%' . $filters['model'] . '%';
        }
        if (!empty($filters['min_price'])) {
            $where[] = 'c.price >= :min_price';
            $params['min_price'] = (float) $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = 'c.price <= :max_price';
            $params['max_price'] = (float) $filters['max_price'];
        }

                if ($this->db->isPgsql()) {
                        $sql = "SELECT c.car_id, c.brand, c.model, c.year, c.price, c.mileage, c.fuel_type, c.gear_type, c.color,
                                             c.description, c.status, c.created_at,
                                             COALESCE(
                                                 (SELECT json_agg(ci.image_path ORDER BY ci.sort_order)
                                                    FROM car_images ci
                                                    WHERE ci.car_id = c.car_id),
                                                 c.images::json,
                                                 '[]'::json
                                             ) AS images
                                        FROM cars c
                                        WHERE " . implode(' AND ', $where) . "
                                        ORDER BY c.created_at DESC";
                } else {
                        $sql = "SELECT c.car_id, c.brand, c.model, c.year, c.price, c.mileage, c.fuel_type, c.gear_type, c.color,
                                             c.description, c.status, c.created_at, c.images
                                        FROM cars c
                                        WHERE " . implode(' AND ', $where) . "
                                        ORDER BY c.created_at DESC";
                }

        $cars = $this->db->fetchAll($sql, $params);

        foreach ($cars as &$car) {
            $images = $this->parseImages($car['IMAGES'] ?? null);
            $car['IMAGE_PATH'] = $images[0]['IMAGE_PATH'] ?? null;
            $car['IMAGES'] = $images;
        }
        unset($car);

        return $cars;
    }

    public function listAllForAdmin(?string $status = null): array
    {
        $where = ['c.is_deleted = 0'];
        $params = [];

        if ($status === 'AVAILABLE' || $status === 'SOLD') {
            $where[] = 'c.status = :status';
            $params['status'] = $status;
        }

        $sql = "SELECT c.car_id, c.brand, c.model, c.year, c.price, c.mileage, c.status, c.created_at, c.sold_at
                FROM cars c
                WHERE " . implode(' AND ', $where) . "
                ORDER BY c.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function findById(int $carId): ?array
    {
                if ($this->db->isPgsql()) {
                        $sql = "SELECT c.car_id, c.brand, c.model, c.year, c.price, c.mileage, c.fuel_type, c.gear_type, c.color,
                                             c.description, c.status, c.created_at, c.sold_at,
                                             COALESCE(
                                                 (SELECT json_agg(ci.image_path ORDER BY ci.sort_order)
                                                    FROM car_images ci
                                                    WHERE ci.car_id = c.car_id),
                                                 c.images::json,
                                                 '[]'::json
                                             ) AS images
                                        FROM cars c
                                        WHERE c.car_id = :car_id AND c.is_deleted = 0
                                        FETCH FIRST 1 ROWS ONLY";
                } else {
                        $sql = "SELECT c.car_id, c.brand, c.model, c.year, c.price, c.mileage, c.fuel_type, c.gear_type, c.color,
                                             c.description, c.status, c.created_at, c.sold_at, c.images
                                        FROM cars c
                                        WHERE c.car_id = :car_id AND c.is_deleted = 0
                                        FETCH FIRST 1 ROWS ONLY";
                }

        $car = $this->db->fetchOne($sql, ['car_id' => $carId]);
        if (!$car) {
            return null;
        }

        $car['IMAGES'] = $this->parseImages($car['IMAGES'] ?? null);

        if ($this->db->isPgsql()) {
            $car['DAMAGE_RECORDS'] = $this->db->fetchAll(
                "SELECT damage_id, car_id, damage_area, damage_type, damage_level, estimated_cost, description, recorded_at
                 FROM damage_records
                 WHERE car_id = :car_id
                 ORDER BY recorded_at DESC, damage_id DESC",
                ['car_id' => $carId]
            );
        } else {
            $car['DAMAGE_RECORDS'] = [];
        }

        return $car;
    }

    public function create(array $data, int $adminId): int
    {
        if ($this->db->isOracle()) {
            $sql = "INSERT INTO cars (
                        car_id, brand, model, year, price, mileage, fuel_type, gear_type, color, description, images,
                        status, created_by, created_at, updated_at, is_deleted
                    ) VALUES (
                        cars_seq.NEXTVAL, :brand, :model, :year, :price, :mileage, :fuel_type, :gear_type, :color, :description, :images,
                        'AVAILABLE', :created_by, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0
                    )";
            
            $this->db->execute($sql, [
                'brand' => $data['brand'],
                'model' => $data['model'],
                'year' => (int) $data['year'],
                'price' => (float) $data['price'],
                'mileage' => (int) $data['mileage'],
                'fuel_type' => $data['fuel_type'] ?? null,
                'gear_type' => $data['gear_type'] ?? null,
                'color' => $data['color'] ?? null,
                'description' => $data['description'] ?? null,
                'images' => $data['images'] ?? '[]',
                'created_by' => $adminId,
            ]);
            
            // For Oracle, get the last inserted sequence value
            $result = $this->db->fetchOne("SELECT cars_seq.CURRVAL AS car_id FROM DUAL", []);
            return (int) ($result['CAR_ID'] ?? 0);
        } else {
            // PostgreSQL: use RETURNING
            $sql = "INSERT INTO cars (
                        brand, model, year, price, mileage, fuel_type, gear_type, color, description, images,
                        status, created_by, created_at, updated_at, is_deleted
                    ) VALUES (
                        :brand, :model, :year, :price, :mileage, :fuel_type, :gear_type, :color, :description, :images,
                        'AVAILABLE', :created_by, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0
                    ) RETURNING car_id";
            
            $result = $this->db->fetchOne($sql, [
                'brand' => $data['brand'],
                'model' => $data['model'],
                'year' => (int) $data['year'],
                'price' => (float) $data['price'],
                'mileage' => (int) $data['mileage'],
                'fuel_type' => $data['fuel_type'] ?? null,
                'gear_type' => $data['gear_type'] ?? null,
                'color' => $data['color'] ?? null,
                'description' => $data['description'] ?? null,
                'images' => $data['images'] ?? '[]',
                'created_by' => $adminId,
            ]);
            
            return (int) ($result['CAR_ID'] ?? 0);
        }
    }

    public function insertDamageRecords(int $carId, array $damageRecords): void
    {
        if (!$this->db->isPgsql() || empty($damageRecords)) {
            return;
        }

        foreach ($damageRecords as $record) {
            $this->db->execute(
                "INSERT INTO damage_records (car_id, damage_area, damage_type, damage_level, estimated_cost, description, recorded_at)
                 VALUES (:car_id, :damage_area, :damage_type, :damage_level, :estimated_cost, :description, CURRENT_TIMESTAMP)",
                [
                    'car_id' => $carId,
                    'damage_area' => $record['damage_area'] ?? '',
                    'damage_type' => $record['damage_type'] ?? '',
                    'damage_level' => $record['damage_level'] ?? 'MINOR',
                    'estimated_cost' => $record['estimated_cost'] ?? null,
                    'description' => $record['description'] ?? null,
                ]
            );
        }
    }

    public function markSold(int $carId): bool
    {
        $sql = "UPDATE cars SET status = 'SOLD', sold_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP
                WHERE car_id = :car_id AND is_deleted = 0";
        return $this->db->execute($sql, ['car_id' => $carId]);
    }

    public function update(int $carId, array $data): bool
    {
        $soldAtSql = strtoupper((string) $data['status']) === 'SOLD'
            ? 'COALESCE(sold_at, CURRENT_TIMESTAMP)'
            : 'NULL';

        $sql = "UPDATE cars
                SET brand = :brand,
                    model = :model,
                    year = :year,
                    price = :price,
                    mileage = :mileage,
                    fuel_type = :fuel_type,
                    gear_type = :gear_type,
                    color = :color,
                    description = :description,
                    images = :images,
                    status = :status,
                    sold_at = {$soldAtSql},
                    updated_at = CURRENT_TIMESTAMP
                WHERE car_id = :car_id AND is_deleted = 0";

        $success = $this->db->execute($sql, [
            'car_id' => $carId,
            'brand' => $data['brand'],
            'model' => $data['model'],
            'year' => (int) $data['year'],
            'price' => (float) $data['price'],
            'mileage' => (int) $data['mileage'],
            'fuel_type' => $data['fuel_type'] !== '' ? $data['fuel_type'] : null,
            'gear_type' => $data['gear_type'] !== '' ? $data['gear_type'] : null,
            'color' => $data['color'] !== '' ? $data['color'] : null,
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'images' => $data['images'] ?? '[]',
            'status' => $data['status'],
        ]);

        if ($success && !empty($data['damage_records']) && $this->db->isPgsql()) {
            // Delete existing damage records for this car
            $this->db->execute('DELETE FROM damage_records WHERE car_id = :car_id', ['car_id' => $carId]);

            // Insert new damage records
            foreach ($data['damage_records'] as $record) {
                $this->db->execute(
                    "INSERT INTO damage_records (car_id, damage_area, damage_type, damage_level, estimated_cost, description, recorded_at)
                     VALUES (:car_id, :damage_area, :damage_type, :damage_level, :estimated_cost, :description, CURRENT_TIMESTAMP)",
                    [
                        'car_id' => $carId,
                        'damage_area' => $record['damage_area'] ?? '',
                        'damage_type' => $record['damage_type'] ?? '',
                        'damage_level' => $record['damage_level'] ?? 'MINOR',
                        'estimated_cost' => $record['estimated_cost'] ?? null,
                        'description' => $record['description'] ?? null,
                    ]
                );
            }
        }

        return $success;
    }

    public function deletePermanently(int $carId): bool
    {
        // inquiries table does not use ON DELETE CASCADE for car_id, so remove dependent rows first.
        $this->db->execute('DELETE FROM inquiries WHERE car_id = :car_id', ['car_id' => $carId]);

        return $this->db->execute(
            'DELETE FROM cars WHERE car_id = :car_id',
            ['car_id' => $carId]
        );
    }

    private function parseImages(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        $images = [];
        $sortOrder = 1;
        foreach ($decoded as $item) {
            if (!is_string($item) || trim($item) === '') {
                continue;
            }

            $images[] = [
                'IMAGE_PATH' => $item,
                'SORT_ORDER' => $sortOrder,
            ];
            $sortOrder++;
        }

        return $images;
    }
}

<?php

class CarService
{
    private CarRepository $cars;
    private const REQUIRED_FIELDS = ['brand', 'model', 'year', 'price', 'mileage'];

    public function __construct(CarRepository $cars)
    {
        $this->cars = $cars;
    }

    public function listForVisitor(array $filters): array
    {
        return $this->cars->listAvailable($filters);
    }

    public function listForAdmin(?string $status): array
    {
        return $this->cars->listAllForAdmin($status);
    }

    public function detail(int $carId, bool $isAdmin = false): array
    {
        $car = $this->cars->findById($carId);
        if (!$car) {
            throw new RuntimeException('Arac bulunamadi.');
        }

        if (!$isAdmin && $car['STATUS'] !== 'AVAILABLE') {
            throw new RuntimeException('Bu ilan satilmistir.');
        }

        return $car;
    }

    public function create(array $data, int $adminId): void
    {
        $this->assertRequiredFields($data);

        $data['images'] = $this->normalizeImages($data['images'] ?? null);

        $carId = $this->cars->create($data, $adminId);
        
        if (!$carId) {
            throw new RuntimeException('Arac eklenemedi.');
        }

        // Insert damage records if provided
        if (!empty($data['damage_records']) && is_array($data['damage_records'])) {
            $this->cars->insertDamageRecords($carId, $data['damage_records']);
        }
    }

    public function update(int $carId, array $data): void
    {
        $this->assertRequiredFields($data);

        $data['images'] = $this->normalizeImages($data['images'] ?? null);

        $status = strtoupper(trim((string) ($data['status'] ?? 'AVAILABLE')));
        if (!in_array($status, ['AVAILABLE', 'SOLD'], true)) {
            throw new InvalidArgumentException('Gecersiz durum.');
        }

        $payload = [
            'brand' => trim((string) $data['brand']),
            'model' => trim((string) $data['model']),
            'year' => (int) $data['year'],
            'price' => (float) $data['price'],
            'mileage' => (int) $data['mileage'],
            'fuel_type' => trim((string) ($data['fuel_type'] ?? '')),
            'gear_type' => trim((string) ($data['gear_type'] ?? '')),
            'color' => trim((string) ($data['color'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'images' => $data['images'],
            'status' => $status,
            'damage_records' => $data['damage_records'] ?? [],
        ];

        $ok = $this->cars->update($carId, $payload);
        if (!$ok) {
            throw new RuntimeException('Arac guncellenemedi.');
        }
    }

    public function markSold(int $carId): void
    {
        $this->cars->markSold($carId);
    }

    public function deletePermanently(int $carId): void
    {
        $this->cars->deletePermanently($carId);
    }

    private function assertRequiredFields(array $data): void
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new InvalidArgumentException('Eksik alan: ' . $field);
            }
        }
    }

    private function normalizeImages(mixed $images): string
    {
        if (is_array($images)) {
            $items = $images;
        } else {
            $raw = trim((string) $images);
            if ($raw === '') {
                return '[]';
            }

            $items = preg_split('/[\r\n,]+/', $raw) ?: [];
        }

        $normalized = [];
        foreach ($items as $item) {
            $url = trim((string) $item);
            if ($url === '') {
                continue;
            }
            $normalized[] = $url;
        }

        return json_encode(array_values(array_unique($normalized)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]';
    }
}

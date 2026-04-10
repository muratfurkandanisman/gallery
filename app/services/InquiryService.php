<?php

class InquiryService
{
    private InquiryRepository $inquiries;
    private CarRepository $cars;

    public function __construct(InquiryRepository $inquiries, CarRepository $cars)
    {
        $this->inquiries = $inquiries;
        $this->cars = $cars;
    }

    public function create(int $userId, int $carId, string $message): void
    {
        if (strlen(trim($message)) < 10) {
            throw new InvalidArgumentException('Mesaj en az 10 karakter olmalidir.');
        }

        $car = $this->cars->findById($carId);
        if (!$car) {
            throw new RuntimeException('Arac bulunamadi.');
        }

        if ($car['STATUS'] !== 'AVAILABLE') {
            throw new RuntimeException('Satilan arac icin talep acilamaz.');
        }

        $this->inquiries->create($userId, $carId, $message);
    }

    public function listForAdmin(?string $status = null): array
    {
        return $this->inquiries->listForAdmin($status);
    }

    public function updateStatus(int $inquiryId, string $status, int $adminId): void
    {
        $allowed = ['NEW', 'IN_PROGRESS', 'CLOSED'];
        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException('Gecersiz status.');
        }
        $this->inquiries->updateStatus($inquiryId, $status, $adminId);
    }
}

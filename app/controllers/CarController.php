<?php

class CarController
{
    private CarService $service;

    public function __construct(CarService $service)
    {
        $this->service = $service;
    }

    public function list(): void
    {
        $filters = [
            'brand' => $_GET['brand'] ?? null,
            'model' => $_GET['model'] ?? null,
            'min_price' => $_GET['minPrice'] ?? null,
            'max_price' => $_GET['maxPrice'] ?? null,
        ];

        Response::json(['success' => true, 'data' => $this->service->listForVisitor($filters)]);
    }

    public function detail(int $carId): void
    {
        try {
            $car = $this->service->detail($carId, Auth::isAdmin());
            Response::json(['success' => true, 'data' => $car]);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    public function adminList(): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        $status = $_GET['status'] ?? null;
        Response::json(['success' => true, 'data' => $this->service->listForAdmin($status)]);
    }

    public function adminCreate(): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        $data = Request::input();
        try {
            $this->service->create($data, Auth::id());
            Response::json(['success' => true, 'message' => 'Arac eklendi.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function adminUpdate(int $carId): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        $data = Request::input();
        try {
            $this->service->update($carId, $data);
            Response::json(['success' => true, 'message' => 'Arac bilgileri guncellendi.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function adminMarkSold(int $carId): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        try {
            $this->service->markSold($carId);
            Response::json(['success' => true, 'message' => 'Arac satildi olarak isaretlendi.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function adminDelete(int $carId): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        try {
            $this->service->deletePermanently($carId);
            Response::json(['success' => true, 'message' => 'Arac kalici olarak silindi.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }
}

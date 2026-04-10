<?php

class FavoriteController
{
    private FavoriteService $service;

    public function __construct(FavoriteService $service)
    {
        $this->service = $service;
    }

    public function list(): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        if (Auth::role() !== 'USER') {
            Response::error('Bu islem sadece USER rolune aciktir.', 403);
        }

        Response::json(['success' => true, 'data' => $this->service->list(Auth::id())]);
    }

    public function toggle(int $carId): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        if (Auth::role() !== 'USER') {
            Response::error('Bu islem sadece USER rolune aciktir.', 403);
        }

        try {
            $state = $this->service->toggle(Auth::id(), $carId);
            Response::json(['success' => true, 'state' => $state]);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function remove(int $carId): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        if (Auth::role() !== 'USER') {
            Response::error('Bu islem sadece USER rolune aciktir.', 403);
        }

        $this->service->remove(Auth::id(), $carId);
        Response::json(['success' => true]);
    }
}

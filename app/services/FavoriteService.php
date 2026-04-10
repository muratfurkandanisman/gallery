<?php

class FavoriteService
{
    private FavoriteRepository $favorites;
    private CarRepository $cars;

    public function __construct(FavoriteRepository $favorites, CarRepository $cars)
    {
        $this->favorites = $favorites;
        $this->cars = $cars;
    }

    public function list(int $userId): array
    {
        return $this->favorites->listByUser($userId);
    }

    public function toggle(int $userId, int $carId): string
    {
        $car = $this->cars->findById($carId);
        if (!$car) {
            throw new RuntimeException('Arac bulunamadi.');
        }

        if ($this->favorites->exists($userId, $carId)) {
            $this->favorites->remove($userId, $carId);
            return 'removed';
        }

        $this->favorites->add($userId, $carId);
        return 'added';
    }

    public function remove(int $userId, int $carId): void
    {
        $this->favorites->remove($userId, $carId);
    }
}

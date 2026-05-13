<?php
require 'app/bootstrap.php';

$db = Database::getInstance($config['db']);
$carService = new CarService(new CarRepository($db));

$testData = [
    'brand' => 'TestBrand',
    'model' => 'TestModelCreate',
    'year' => 2024,
    'price' => 50000,
    'mileage' => 1000,
    'images' => ['https://example.com/test.jpg'],
    'damage_records' => [
        [
            'damage_area' => 'Front Door',
            'damage_type' => 'Scratch',
            'damage_level' => 'MINOR',
            'estimated_cost' => 500,
            'description' => 'Test scratch'
        ]
    ]
];

try {
    $carService->create($testData, 1);
    echo "SUCCESS: Car created\n";
    
    // Verify
    $repo = new CarRepository($db);
    $allCars = $repo->listAllForAdmin("");
    $lastCar = end($allCars);
    $carId = (int) $lastCar["CAR_ID"];
    $detail = $repo->findById($carId);
    echo "Car ID: " . $carId . "\n";
    echo "Damage records: " . count($detail["DAMAGE_RECORDS"] ?? []) . "\n";
    if (!empty($detail["DAMAGE_RECORDS"])) {
        echo "First damage: " . $detail["DAMAGE_RECORDS"][0]["DAMAGE_AREA"] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

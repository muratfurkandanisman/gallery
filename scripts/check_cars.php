<?php
require 'app/bootstrap.php';
$db = Database::getInstance($config['db']);
$pdo = $db->getConnection();
$rows = $pdo->query('SELECT car_id, brand, model, created_at FROM cars ORDER BY car_id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['car_id'] . ' | ' . $r['brand'] . ' ' . $r['model'] . ' | ' . $r['created_at'] . PHP_EOL;
}

echo "\n--- Check damage for newly created ---\n";
$repo = new CarRepository($db);
$testCar = $repo->findById(19);
if ($testCar) {
    echo "Car 19: " . $testCar['BRAND'] . " " . $testCar['MODEL'] . "\n";
    echo "Damage records: " . count($testCar['DAMAGE_RECORDS'] ?? []) . "\n";
    foreach ($testCar['DAMAGE_RECORDS'] ?? [] as $dr) {
        echo "  - " . $dr['DAMAGE_AREA'] . " | " . $dr['DAMAGE_TYPE'] . "\n";
    }
}

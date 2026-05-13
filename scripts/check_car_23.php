<?php
require 'app/bootstrap.php';
$db = Database::getInstance($config['db']);
$repo = new CarRepository($db);
$car = $repo->findById(23);
echo "Car 23: " . $car['BRAND'] . " " . $car['MODEL'] . "\n";
echo "Damage records: " . count($car['DAMAGE_RECORDS'] ?? []) . "\n";
foreach ($car['DAMAGE_RECORDS'] ?? [] as $dr) {
    echo "  - " . $dr['DAMAGE_AREA'] . " | " . $dr['DAMAGE_TYPE'] . " | " . $dr['DAMAGE_LEVEL'] . "\n";
}

<?php
require 'app/bootstrap.php';

try {
    $db = Database::getInstance($config['db']);
    $pdo = $db->getConnection();

    // Check if table exists
    $result = $pdo->query('SELECT table_name FROM information_schema.tables WHERE table_name = \'user_activity_logs\'');
    if ($result && $result->rowCount() > 0) {
        echo "✓ Tablo MEVCUT\n\n";
    } else {
        echo "✗ Tablo BULUNAMADI\n";
        exit(1);
    }

    // Check columns
    $cols = $pdo->query('SELECT column_name, data_type FROM information_schema.columns WHERE table_name = \'user_activity_logs\' ORDER BY ordinal_position');
    echo "Sütunlar:\n";
    foreach ($cols->fetchAll(PDO::FETCH_ASSOC) as $col) {
        echo "  - " . $col['column_name'] . " (" . $col['data_type'] . ")\n";
    }

    // Check record count
    $count = $pdo->query('SELECT COUNT(*) FROM user_activity_logs')->fetchColumn();
    echo "\nToplam kayıt: " . $count . "\n";

    // Check indexes
    $indexes = $pdo->query("
        SELECT indexname FROM pg_indexes 
        WHERE tablename = 'user_activity_logs'
    ");
    echo "\nİndeksler:\n";
    foreach ($indexes->fetchAll(PDO::FETCH_ASSOC) as $idx) {
        echo "  - " . $idx['indexname'] . "\n";
    }

    echo "\n✓ Tüm kontroller başarılı!\n";
} catch (Throwable $e) {
    echo "✗ HATA: " . $e->getMessage() . "\n";
    exit(1);
}

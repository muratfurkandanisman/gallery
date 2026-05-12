<?php
require 'app/bootstrap.php';

try {
    $db = Database::getInstance($config['db']);
    $pdo = $db->getConnection();

    // Check users
    $users = $pdo->query('SELECT user_id, full_name, email, role FROM users LIMIT 3');
    $count = $users ? $users->rowCount() : 0;
    
    echo "Kullanıcı sayısı: " . $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() . "\n\n";
    
    if ($count > 0) {
        echo "Test kullanıcıları:\n";
        foreach ($users->fetchAll(PDO::FETCH_ASSOC) as $user) {
            echo "  ID: " . $user['user_id'] . " | Name: " . $user['full_name'] . " | Email: " . $user['email'] . " | Role: " . $user['role'] . "\n";
        }
    } else {
        echo "⚠ Veritabanında kullanıcı yok - test için örnek veri oluşturalım mı?\n";
    }
    
    echo "\n✓ Veritabanı bağlantısı tamam\n";
} catch (Throwable $e) {
    echo "✗ HATA: " . $e->getMessage() . "\n";
}

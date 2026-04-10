<?php

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

try {
    $db = Database::getInstance($config['db']);

    if (!$db->isPgsql()) {
        throw new RuntimeException('This migration runner is currently for PostgreSQL only. Set DB_DRIVER=pgsql.');
    }

    /** @var PDO $pdo */
    $pdo = $db->getConnection();

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS schema_migrations (
            id BIGSERIAL PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $migrationDir = __DIR__ . '/../database/postgresql/migrations';
    if (!is_dir($migrationDir)) {
        throw new RuntimeException('Migration directory not found: ' . $migrationDir);
    }

    $files = glob($migrationDir . '/*.up.sql') ?: [];
    sort($files, SORT_NATURAL);

    $appliedStmt = $pdo->query('SELECT filename FROM schema_migrations');
    $appliedRows = $appliedStmt ? $appliedStmt->fetchAll(PDO::FETCH_COLUMN) : [];
    $appliedSet = array_fill_keys($appliedRows ?: [], true);

    $appliedCount = 0;

    foreach ($files as $filePath) {
        $file = basename($filePath);
        if (isset($appliedSet[$file])) {
            echo "[SKIP] {$file}\n";
            continue;
        }

        $sql = file_get_contents($filePath);
        if ($sql === false) {
            throw new RuntimeException('Cannot read migration file: ' . $file);
        }

        $pdo->beginTransaction();
        try {
            $pdo->exec($sql);
            $ins = $pdo->prepare('INSERT INTO schema_migrations (filename) VALUES (:filename)');
            $ins->execute([':filename' => $file]);
            $pdo->commit();
            $appliedCount++;
            echo "[OK]   {$file}\n";
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw new RuntimeException('Migration failed at ' . $file . ': ' . $e->getMessage());
        }
    }

    echo "Done. Applied {$appliedCount} migration(s).\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

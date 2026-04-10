<?php

class Database
{
    private static ?Database $instance = null;
    private string $driver;
    private $conn = null;

    private function __construct(array $config)
    {
        $this->driver = strtolower((string) ($config['driver'] ?? 'pgsql'));

        if ($this->driver === 'pgsql') {
            if (!extension_loaded('pdo_pgsql')) {
                throw new RuntimeException('pdo_pgsql extension is not installed. Enable pdo_pgsql for PostgreSQL support.');
            }

            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $config['host'] ?? '127.0.0.1',
                $config['port'] ?? '5432',
                $config['database'] ?? ''
            );

            try {
                $this->conn = new PDO(
                    $dsn,
                    (string) ($config['username'] ?? ''),
                    (string) ($config['password'] ?? ''),
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
                $this->conn->exec("SET NAMES '" . ($config['charset'] ?? 'utf8') . "'");
            } catch (PDOException $e) {
                throw new RuntimeException('PostgreSQL connection failed: ' . $e->getMessage());
            }

            return;
        }

        if ($this->driver === 'oracle') {
            if (!function_exists('oci_connect')) {
                throw new RuntimeException('OCI8 extension is not installed. Enable php_oci8 for Oracle support.');
            }

            $this->conn = @oci_connect(
                $config['oracle_username'] ?? $config['username'],
                $config['oracle_password'] ?? $config['password'],
                $config['oracle_connection_string'] ?? $config['connection_string'],
                $config['oracle_charset'] ?? $config['charset'] ?? 'AL32UTF8'
            );

            if (!$this->conn) {
                $e = oci_error();
                throw new RuntimeException('Oracle connection failed: ' . ($e['message'] ?? 'Unknown error'));
            }

            return;
        }

        throw new RuntimeException('Unsupported DB driver: ' . $this->driver);
    }

    public static function getInstance(array $config): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database($config);
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function driver(): string
    {
        return $this->driver;
    }

    public function isPgsql(): bool
    {
        return $this->driver === 'pgsql';
    }

    public function isOracle(): bool
    {
        return $this->driver === 'oracle';
    }

    public function run(string $sql, array $params = [], bool $commit = false)
    {
        if ($this->isPgsql()) {
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $name => $value) {
                $bindName = str_starts_with((string) $name, ':') ? (string) $name : ':' . $name;
                $stmt->bindValue($bindName, $value);
            }
            $stmt->execute();
            return $stmt;
        }

        $stid = oci_parse($this->conn, $sql);
        if (!$stid) {
            $e = oci_error($this->conn);
            throw new RuntimeException('SQL parse error: ' . ($e['message'] ?? 'Unknown error'));
        }

        foreach ($params as $name => $value) {
            $bindName = str_starts_with($name, ':') ? $name : ':' . $name;
            oci_bind_by_name($stid, $bindName, $params[$name]);
        }

        $ok = @oci_execute($stid, $commit ? OCI_COMMIT_ON_SUCCESS : OCI_NO_AUTO_COMMIT);
        if (!$ok) {
            $e = oci_error($stid);
            throw new RuntimeException('SQL execute error: ' . ($e['message'] ?? 'Unknown error'));
        }

        return $stid;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $result = $this->run($sql, $params);

        if ($this->isPgsql()) {
            $rows = $result->fetchAll();
            return array_map([$this, 'normalizeKeys'], $rows ?: []);
        }

        $rows = [];
        while (($row = oci_fetch_assoc($result)) !== false) {
            $rows[] = $this->normalizeKeys($row);
        }
        oci_free_statement($result);

        return $rows;
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->run($sql, $params);

        if ($this->isPgsql()) {
            $row = $result->fetch() ?: null;
            return $row ? $this->normalizeKeys($row) : null;
        }

        $row = oci_fetch_assoc($result) ?: null;
        oci_free_statement($result);

        return $row ? $this->normalizeKeys($row) : null;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $result = $this->run($sql, $params, true);
        if ($this->isOracle()) {
            oci_free_statement($result);
        }
        return true;
    }

    public function begin(): void
    {
        // Oracle transactions are started automatically with OCI_NO_AUTO_COMMIT.
    }

    public function commit(): void
    {
        if ($this->isPgsql()) {
            $this->conn->commit();
            return;
        }
        oci_commit($this->conn);
    }

    public function rollback(): void
    {
        if ($this->isPgsql()) {
            $this->conn->rollBack();
            return;
        }
        oci_rollback($this->conn);
    }

    private function normalizeKeys(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[strtoupper((string) $key)] = $value;
        }
        return $normalized;
    }
}

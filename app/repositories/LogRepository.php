<?php

class LogRepository {
    private PDO $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getConnection();
    }

    /**
     * Log user activity
     */
    public function logActivity(
        ?int $userId,
        string $action,
        ?array $details = null
    ): int {
        $sql = "INSERT INTO user_activity_logs 
                (user_id, action, details) 
                VALUES (?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $detailsJson = $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null;

        $stmt->execute([
            $userId,
            $action,
            $detailsJson
        ]);

        return (int) $this->pdo->lastInsertId('user_activity_logs_log_id_seq');
    }

    /**
     * Get logs for a specific user
     */
    public function getUserLogs(int $userId, int $limit = 50, int $offset = 0): array {
        $sql = "SELECT log_id, user_id, action, details, created_at 
                FROM user_activity_logs 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
 
        return $this->formatLogs($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get all logs (admin only)
     */
    public function getAllLogs(int $limit = 100, int $offset = 0): array {
        $sql = "SELECT log_id, user_id, action, details, created_at 
                FROM user_activity_logs 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);

        return $this->formatLogs($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get logs by action type
     */
    public function getLogsByAction(string $action, int $limit = 50, int $offset = 0): array {
        $sql = "SELECT log_id, user_id, action, details, created_at 
                FROM user_activity_logs 
                WHERE action = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$action, $limit, $offset]);

        return $this->formatLogs($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get login attempts (successful and failed)
     */
    public function getLoginAttempts(int $limit = 50): array {
        $sql = "SELECT log_id, user_id, action, details, created_at 
                FROM user_activity_logs 
                WHERE action IN ('LOGIN', 'FAILED_LOGIN') 
                ORDER BY created_at DESC 
                LIMIT ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);

        return $this->formatLogs($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get failed login attempts (last N attempts)
     */
    public function getFailedLoginAttempts(int $limit = 50, int $minutes = 30): array {
        $sql = "SELECT log_id, user_id, action, details, created_at 
                FROM user_activity_logs 
                WHERE action = 'FAILED_LOGIN' 
                AND created_at > NOW() - INTERVAL '{$minutes} minutes' 
                ORDER BY created_at DESC 
                LIMIT ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);

        return $this->formatLogs($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get user's recent activity
     */
    public function getUserRecentActivity(int $userId, int $days = 7): array {
        $sql = "SELECT log_id, user_id, action, details, created_at 
                FROM user_activity_logs 
                WHERE user_id = ? 
                AND created_at > NOW() - INTERVAL '{$days} days' 
                ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $this->formatLogs($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Count logs by action
     */
    public function countByAction(string $action): int {
        $sql = "SELECT COUNT(*) FROM user_activity_logs WHERE action = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$action]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Format logs - decode JSON details
     */
    private function formatLogs(array $logs): array {
        return array_map(function ($log) {
            if ($log['details']) {
                $log['details'] = json_decode($log['details'], true);
            }
            return $log;
        }, $logs);
    }

    /**
     * Delete old logs (older than specified days)
     */
    public function deleteOldLogs(int $days = 90): int {
        $sql = "DELETE FROM user_activity_logs 
                WHERE created_at < NOW() - INTERVAL '{$days} days'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats(int $days = 7): array {
        $sql = "SELECT 
                    action,
                    COUNT(*) as count,
                    COUNT(DISTINCT user_id) as unique_users,
                    MIN(created_at) as first_activity,
                    MAX(created_at) as last_activity
                FROM user_activity_logs 
                WHERE created_at > NOW() - INTERVAL '{$days} days'
                GROUP BY action
                ORDER BY count DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

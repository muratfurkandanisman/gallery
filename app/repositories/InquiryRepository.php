<?php

class InquiryRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function create(int $userId, int $carId, string $message): bool
    {
        if ($this->db->isOracle()) {
            $sql = "INSERT INTO inquiries (inquiry_id, user_id, car_id, message, status, created_at)
                    VALUES (inquiries_seq.NEXTVAL, :user_id, :car_id, :message, 'NEW', CURRENT_TIMESTAMP)";
        } else {
            $sql = "INSERT INTO inquiries (user_id, car_id, message, status, created_at)
                    VALUES (:user_id, :car_id, :message, 'NEW', CURRENT_TIMESTAMP)";
        }
        return $this->db->execute($sql, [
            'user_id' => $userId,
            'car_id' => $carId,
            'message' => $message,
        ]);
    }

    public function listForAdmin(?string $status = null): array
    {
        $where = [];
        $params = [];
        if ($status) {
            $where[] = 'i.status = :status';
            $params['status'] = $status;
        }

        $sql = "SELECT i.inquiry_id, i.user_id, i.car_id, i.message, i.status, i.created_at,
                       u.full_name, u.email,
                       c.brand, c.model, c.year
                FROM inquiries i
                JOIN users u ON u.user_id = i.user_id
                JOIN cars c ON c.car_id = i.car_id
                " . ($where ? ('WHERE ' . implode(' AND ', $where)) : '') . "
                ORDER BY i.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function updateStatus(int $inquiryId, string $status, int $adminId): bool
    {
        $sql = "UPDATE inquiries
                SET status = :status, handled_by = :handled_by, handled_at = CURRENT_TIMESTAMP
                WHERE inquiry_id = :inquiry_id";

        return $this->db->execute($sql, [
            'status' => $status,
            'handled_by' => $adminId,
            'inquiry_id' => $inquiryId,
        ]);
    }
}

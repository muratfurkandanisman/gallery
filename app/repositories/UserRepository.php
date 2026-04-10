<?php

class UserRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT user_id, full_name, email, password_hash, role, is_active FROM users WHERE LOWER(email)=LOWER(:email) FETCH FIRST 1 ROWS ONLY";
        return $this->db->fetchOne($sql, ['email' => $email]);
    }

    public function create(string $fullName, string $email, string $passwordHash, string $role = 'USER'): bool
    {
        if ($this->db->isOracle()) {
            $sql = "INSERT INTO users (user_id, full_name, email, password_hash, role, created_at, is_active)
                    VALUES (users_seq.NEXTVAL, :full_name, :email, :password_hash, :role, CURRENT_TIMESTAMP, 1)";
        } else {
            $sql = "INSERT INTO users (full_name, email, password_hash, role, created_at, is_active)
                    VALUES (:full_name, :email, :password_hash, :role, CURRENT_TIMESTAMP, 1)";
        }

        return $this->db->execute($sql, [
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role,
        ]);
    }

    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
        return $this->db->execute($sql, ['user_id' => $userId]);
    }
}

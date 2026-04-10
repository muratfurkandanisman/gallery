<?php

class ChatRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function findConversationByUserAndCar(int $userId, int $carId): ?array
    {
        return $this->db->fetchOne(
            "SELECT conversation_id, user_id, car_id, status
             FROM chat_conversations
             WHERE user_id = :user_id AND car_id = :car_id
             FETCH FIRST 1 ROWS ONLY",
            ['user_id' => $userId, 'car_id' => $carId]
        );
    }

    public function createConversation(int $userId, int $carId): int
    {
        $sql = "INSERT INTO chat_conversations (user_id, car_id, status, created_at, updated_at, last_message_at)
                VALUES (:user_id, :car_id, 'OPEN', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                RETURNING conversation_id";

        $row = $this->db->fetchOne($sql, ['user_id' => $userId, 'car_id' => $carId]);
        return (int) ($row['CONVERSATION_ID'] ?? 0);
    }

    public function listForUser(int $userId): array
    {
        $sql = "SELECT cc.conversation_id, cc.car_id, cc.status, cc.last_message_at,
                       c.brand, c.model, c.year,
                       COALESCE((
                           SELECT cm.message_text
                           FROM chat_messages cm
                           WHERE cm.conversation_id = cc.conversation_id
                           ORDER BY cm.created_at DESC
                           FETCH FIRST 1 ROWS ONLY
                       ), '') AS last_message,
                       COALESCE((
                           SELECT COUNT(*)
                           FROM chat_messages cm
                           WHERE cm.conversation_id = cc.conversation_id
                             AND cm.is_read = 0
                             AND cm.sender_role = 'ADMIN'
                       ), 0) AS unread_count
                FROM chat_conversations cc
                JOIN cars c ON c.car_id = cc.car_id
                WHERE cc.user_id = :user_id
                ORDER BY cc.last_message_at DESC";

        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    public function listForAdmin(): array
    {
        $sql = "SELECT cc.conversation_id, cc.car_id, cc.user_id, cc.status, cc.last_message_at,
                       u.full_name,
                       c.brand, c.model, c.year,
                       COALESCE((
                           SELECT cm.message_text
                           FROM chat_messages cm
                           WHERE cm.conversation_id = cc.conversation_id
                           ORDER BY cm.created_at DESC
                           FETCH FIRST 1 ROWS ONLY
                       ), '') AS last_message,
                       COALESCE((
                           SELECT COUNT(*)
                           FROM chat_messages cm
                           WHERE cm.conversation_id = cc.conversation_id
                             AND cm.is_read = 0
                             AND cm.sender_role = 'USER'
                       ), 0) AS unread_count
                FROM chat_conversations cc
                JOIN users u ON u.user_id = cc.user_id
                JOIN cars c ON c.car_id = cc.car_id
                ORDER BY cc.last_message_at DESC";

        return $this->db->fetchAll($sql);
    }

    public function findConversationById(int $conversationId): ?array
    {
        return $this->db->fetchOne(
            "SELECT conversation_id, user_id, car_id, status
             FROM chat_conversations
             WHERE conversation_id = :conversation_id
             FETCH FIRST 1 ROWS ONLY",
            ['conversation_id' => $conversationId]
        );
    }

    public function listMessages(int $conversationId): array
    {
        return $this->db->fetchAll(
            "SELECT message_id, conversation_id, sender_id, sender_role, message_text, is_read, created_at
             FROM chat_messages
             WHERE conversation_id = :conversation_id
             ORDER BY created_at ASC",
            ['conversation_id' => $conversationId]
        );
    }

    public function addMessage(int $conversationId, int $senderId, string $senderRole, string $messageText): bool
    {
        $ok = $this->db->execute(
            "INSERT INTO chat_messages (conversation_id, sender_id, sender_role, message_text, is_read, created_at)
             VALUES (:conversation_id, :sender_id, :sender_role, :message_text, 0, CURRENT_TIMESTAMP)",
            [
                'conversation_id' => $conversationId,
                'sender_id' => $senderId,
                'sender_role' => $senderRole,
                'message_text' => $messageText,
            ]
        );

        if ($ok) {
            $this->db->execute(
                "UPDATE chat_conversations
                 SET updated_at = CURRENT_TIMESTAMP,
                     last_message_at = CURRENT_TIMESTAMP,
                     status = 'OPEN'
                 WHERE conversation_id = :conversation_id",
                ['conversation_id' => $conversationId]
            );
        }

        return $ok;
    }

    public function markAsRead(int $conversationId, string $viewerRole): bool
    {
        $otherRole = $viewerRole === 'ADMIN' ? 'USER' : 'ADMIN';
        return $this->db->execute(
            "UPDATE chat_messages
             SET is_read = 1
             WHERE conversation_id = :conversation_id
               AND sender_role = :other_role
               AND is_read = 0",
            ['conversation_id' => $conversationId, 'other_role' => $otherRole]
        );
    }

    public function closeConversation(int $conversationId): bool
    {
        return $this->db->execute(
            "UPDATE chat_conversations
             SET status = 'CLOSED', updated_at = CURRENT_TIMESTAMP
             WHERE conversation_id = :conversation_id",
            ['conversation_id' => $conversationId]
        );
    }
}

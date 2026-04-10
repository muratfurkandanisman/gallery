<?php

class ChatService
{
    private ChatRepository $chats;
    private CarRepository $cars;

    public function __construct(ChatRepository $chats, CarRepository $cars)
    {
        $this->chats = $chats;
        $this->cars = $cars;
    }

    public function startConversation(int $userId, int $carId, ?string $initialMessage = null): int
    {
        $car = $this->cars->findById($carId);
        if (!$car) {
            throw new RuntimeException('Arac bulunamadi.');
        }

        $existing = $this->chats->findConversationByUserAndCar($userId, $carId);
        $conversationId = $existing ? (int) $existing['CONVERSATION_ID'] : $this->chats->createConversation($userId, $carId);

        $message = trim((string) $initialMessage);
        if ($message !== '') {
            if (mb_strlen($message) < 2) {
                throw new InvalidArgumentException('Mesaj cok kisa.');
            }
            $this->chats->addMessage($conversationId, $userId, 'USER', $message);
        }

        return $conversationId;
    }

    public function listConversations(int $requesterId, bool $isAdmin): array
    {
        return $isAdmin ? $this->chats->listForAdmin() : $this->chats->listForUser($requesterId);
    }

    public function getMessages(int $conversationId, int $requesterId, bool $isAdmin): array
    {
        $conversation = $this->chats->findConversationById($conversationId);
        if (!$conversation) {
            throw new RuntimeException('Sohbet bulunamadi.');
        }

        if (!$isAdmin && (int) $conversation['USER_ID'] !== $requesterId) {
            throw new RuntimeException('Bu sohbete erisim yetkiniz yok.');
        }

        $this->chats->markAsRead($conversationId, $isAdmin ? 'ADMIN' : 'USER');

        return $this->chats->listMessages($conversationId);
    }

    public function sendMessage(int $conversationId, int $requesterId, bool $isAdmin, string $message): void
    {
        $message = trim($message);
        if (mb_strlen($message) < 1) {
            throw new InvalidArgumentException('Mesaj bos olamaz.');
        }

        $conversation = $this->chats->findConversationById($conversationId);
        if (!$conversation) {
            throw new RuntimeException('Sohbet bulunamadi.');
        }

        if (!$isAdmin && (int) $conversation['USER_ID'] !== $requesterId) {
            throw new RuntimeException('Bu sohbete erisim yetkiniz yok.');
        }

        $senderRole = $isAdmin ? 'ADMIN' : 'USER';
        $this->chats->addMessage($conversationId, $requesterId, $senderRole, $message);
    }

    public function closeConversation(int $conversationId): void
    {
        $this->chats->closeConversation($conversationId);
    }
}

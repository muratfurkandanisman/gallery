<?php

class ChatController
{
    private ChatService $service;

    public function __construct(ChatService $service)
    {
        $this->service = $service;
    }

    public function start(): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        $data = Request::input();

        try {
            $conversationId = $this->service->startConversation(
                Auth::id(),
                (int) ($data['car_id'] ?? 0),
                (string) ($data['message'] ?? '')
            );

            Response::json([
                'success' => true,
                'conversation_id' => $conversationId,
            ]);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function list(): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        $items = $this->service->listConversations(Auth::id(), Auth::isAdmin());
        Response::json(['success' => true, 'data' => $items]);
    }

    public function messages(int $conversationId): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        try {
            $items = $this->service->getMessages($conversationId, Auth::id(), Auth::isAdmin());
            Response::json(['success' => true, 'data' => $items]);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 403);
        }
    }

    public function send(int $conversationId): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        $data = Request::input();

        try {
            $this->service->sendMessage(
                $conversationId,
                Auth::id(),
                Auth::isAdmin(),
                (string) ($data['message'] ?? '')
            );
            Response::json(['success' => true, 'message' => 'Mesaj gonderildi.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function close(int $conversationId): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        $this->service->closeConversation($conversationId);
        Response::json(['success' => true, 'message' => 'Sohbet kapatildi.']);
    }
}

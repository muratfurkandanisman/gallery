<?php

class InquiryController
{
    private InquiryService $service;

    public function __construct(InquiryService $service)
    {
        $this->service = $service;
    }

    public function create(): void
    {
        if (!Auth::check()) {
            Response::error('Giris gerekli.', 401);
        }

        $data = Request::input();
        try {
            $this->service->create(
                Auth::id(),
                (int) ($data['car_id'] ?? 0),
                trim((string) ($data['message'] ?? ''))
            );

            Response::json(['success' => true, 'message' => 'Talebiniz alindi.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function adminList(): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        Response::json(['success' => true, 'data' => $this->service->listForAdmin($_GET['status'] ?? null)]);
    }

    public function adminUpdateStatus(int $inquiryId): void
    {
        if (!Auth::isAdmin()) {
            Response::error('Yetkisiz.', 403);
        }

        $data = Request::input();
        try {
            $this->service->updateStatus($inquiryId, strtoupper((string) ($data['status'] ?? '')), Auth::id());
            Response::json(['success' => true, 'message' => 'Talep guncellendi.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }
}

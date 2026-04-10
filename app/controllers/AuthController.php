<?php

class AuthController
{
    private AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(): void
    {
        $data = Request::input();
        try {
            $this->service->register(trim($data['full_name'] ?? ''), trim($data['email'] ?? ''), (string) ($data['password'] ?? ''));
            Response::json(['success' => true, 'message' => 'Kayit basarili.']);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public function login(): void
    {
        $data = Request::input();
        try {
            $user = $this->service->login(trim($data['email'] ?? ''), (string) ($data['password'] ?? ''));
            Auth::login($user);
            Response::json([
                'success' => true,
                'authenticated' => true,
                'user' => Auth::user(),
            ]);
        } catch (Throwable $e) {
            Response::error($e->getMessage(), 401);
        }
    }

    public function me(): void
    {
        Response::json([
            'success' => true,
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        Response::json(['success' => true, 'message' => 'Cikis yapildi.']);
    }
}

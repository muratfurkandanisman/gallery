<?php

class AuthController
{
    private AuthService $service;
    private LogRepository $logRepository;

    public function __construct(AuthService $service, LogRepository $logRepository)
    {
        $this->service = $service;
        $this->logRepository = $logRepository;
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

            // Log successful login
                $this->logRepository->logActivity(
                    $user['user_id'],
                    'LOGIN',
                    [
                        'email' => $user['email'],
                        'full_name' => $user['full_name'],
                        'role' => $user['role']
                    ]
                );

            Response::json([
                'success' => true,
                'authenticated' => true,
                'user' => Auth::user(),
            ]);
        } catch (Throwable $e) {
            // Log failed login attempt
                $this->logRepository->logActivity(
                    null,
                    'FAILED_LOGIN',
                    [
                        'email' => $data['email'] ?? 'unknown',
                        'reason' => $e->getMessage()
                    ]
                );

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
        $user = Auth::user();

        // Log logout
        if ($user) {
                $this->logRepository->logActivity(
                    $user['user_id'],
                    'LOGOUT',
                    [
                        'email' => $user['email'],
                        'full_name' => $user['full_name']
                    ]
                );
        }

        Auth::logout();
        Response::json(['success' => true, 'message' => 'Cikis yapildi.']);
    }
}

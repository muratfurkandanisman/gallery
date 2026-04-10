<?php

class Auth
{
    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function role(): ?string
    {
        if (!isset($_SESSION['role'])) {
            return null;
        }

        return UserDto::normalizeRole((string) $_SESSION['role']);
    }

    public static function check(): bool
    {
        return self::id() !== null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'ADMIN';
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return UserDto::fromSession($_SESSION);
    }

    public static function login(array $user): void
    {
        $dto = UserDto::fromDbRow($user);

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $dto['user_id'];
        $_SESSION['full_name'] = $dto['full_name'];
        $_SESSION['email'] = $dto['email'];
        $_SESSION['role'] = $dto['role'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}

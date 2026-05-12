<?php

class AuthService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function register(string $fullName, string $email, string $password): void
    {
        if (strlen($fullName) < 2) {
            throw new InvalidArgumentException('Ad soyad en az 2 karakter olmalidir.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Gecerli bir e-posta giriniz.');
        }
        if (strlen($password) < 6) {
            throw new InvalidArgumentException('Sifre en az 6 karakter olmalidir.');
        }
        if ($this->users->findByEmail($email)) {
            throw new InvalidArgumentException('Bu e-posta ile kayitli bir hesap var.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->users->create($fullName, $email, $hash, 'USER');
    }

    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        if (!$user) {
            throw new InvalidArgumentException('Kullanici bulunamadi veya pasif.');
        }

        // Normalize keys to lowercase for consistent access across app
        $user = array_change_key_case($user, CASE_LOWER);

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            throw new InvalidArgumentException('Kullanici bulunamadi veya pasif.');
        }

        if (!password_verify($password, $user['password_hash'])) {
            throw new InvalidArgumentException('E-posta veya sifre hatali.');
        }

        $this->users->updateLastLogin((int) $user['user_id']);
        return $user;
    }
}

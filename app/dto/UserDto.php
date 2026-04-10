<?php

class UserDto
{
    public static function normalizeRole(?string $role): string
    {
        $normalized = strtoupper(trim((string) $role));
        return in_array($normalized, ['ADMIN', 'USER'], true) ? $normalized : 'USER';
    }

    public static function fromSession(array $session): array
    {
        $role = self::normalizeRole($session['role'] ?? null);

        return [
            'user_id' => isset($session['user_id']) ? (int) $session['user_id'] : null,
            'full_name' => $session['full_name'] ?? null,
            'email' => $session['email'] ?? null,
            'role' => $role,
            'permissions' => self::permissions($role),
        ];
    }

    public static function fromDbRow(array $row): array
    {
        $role = self::normalizeRole($row['ROLE'] ?? null);

        return [
            'user_id' => (int) ($row['USER_ID'] ?? 0),
            'full_name' => $row['FULL_NAME'] ?? null,
            'email' => $row['EMAIL'] ?? null,
            'role' => $role,
            'permissions' => self::permissions($role),
        ];
    }

    private static function permissions(string $role): array
    {
        $isAdmin = $role === 'ADMIN';

        return [
            'can_view_admin_panel' => $isAdmin,
            'can_manage_inventory' => $isAdmin,
            'can_manage_inquiries' => $isAdmin,
            'can_use_favorites' => !$isAdmin,
            'can_send_inquiry' => !$isAdmin,
        ];
    }
}

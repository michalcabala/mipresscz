<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'superadmin';
    case Admin = 'admin';
    case Editor = 'editor';
    case Contributor = 'contributor';

    public function label(): string
    {
        return __('roles.' . $this->value);
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::SuperAdmin => [],
            self::Admin => [
                'view.users',
                'manage.users',
            ],
            self::Editor => [
                'view.users',
            ],
            self::Contributor => [
                'view.users',
            ],
        };
    }

    /**
     * @return array<string, string>
     */
    public static function toFilamentSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}

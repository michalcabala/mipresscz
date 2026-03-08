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
        return __('roles.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::SuperAdmin => 'fal-user-crown',
            self::Admin => 'fal-user-shield',
            self::Editor => 'fal-user-pen',
            self::Contributor => 'fal-user',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::Admin => 'warning',
            self::Editor => 'info',
            self::Contributor => 'gray',
        };
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

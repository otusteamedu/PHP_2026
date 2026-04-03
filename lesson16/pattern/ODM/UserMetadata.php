<?php

class UserMetadata
{
    public static function collection(): string
    {
        return 'users';
    }

    public static function fields(): array
    {
        return [
            'id'        => '_id',
            'username'  => 'username',
            'createdAt' => 'created_at',
        ];
    }
}
<?php

class UserMetadata
{
    public static function table(): string
    {
        return 'users';
    }

    public static function fields(): array
    {
        return [
            'id'         => 'id',
            'username'   => 'username',
            'createdAt'  => 'created_at',
        ];
    }
}
<?php

class User extends ActiveRecord
{
    public ?int $id;
    public string $username;
    public ?string $created_at;

    public function __construct(
        ?int $id = null,
        string $username = '',
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->created_at = date('Y-m-d H:i:s');
    }

    public static function tableName(): string
    {
        return 'users';
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'username'   => $this->username,
            'created_at' => $this->created_at,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            username: $data['username'],
            created_at: $data['created_at'] ?? null
        );
    }
}
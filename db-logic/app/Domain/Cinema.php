<?php

declare (strict_types = 1);

namespace App\Domain;

use App\Infrastructure\Model;

class Cinema extends Model
{
    protected static string $table = 'cinemas';

    protected array $fillable = ['name', 'city', 'address'];

    public function halls()
    {
        return $this->hasMany(Hall::class, 'cinema_id');
    }
}

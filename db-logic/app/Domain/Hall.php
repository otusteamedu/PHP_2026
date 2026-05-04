<?php

declare (strict_types = 1);

namespace App\Domain;

use App\Infrastructure\Model;

class Hall extends Model
{
    protected static string $table = 'halls';

    protected array $fillable = ['cinema_id', 'name'];

    public function cinema()
    {
        return $this->belongsTo(Cinema::class, 'cinema_id');
    }
}

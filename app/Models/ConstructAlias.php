<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'construct_id',
        'alias',
    ];

    public function construct(): BelongsTo
    {
        return $this->belongsTo(Construct::class);
    }
}


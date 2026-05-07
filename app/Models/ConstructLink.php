<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'construct_id',
        'title',
        'url',
        'sort',
    ];

    public function construct(): BelongsTo
    {
        return $this->belongsTo(Construct::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructSnippet extends Model
{
    use HasFactory;

    protected $fillable = [
        'construct_id',
        'title',
        'code',
        'sort',
    ];

    public function construct(): BelongsTo
    {
        return $this->belongsTo(Construct::class);
    }
}

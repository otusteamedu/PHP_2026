<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Construct extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'slug',
        'title',
        'summary',
        'details',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function snippets(): HasMany
    {
        return $this->hasMany(ConstructSnippet::class)->orderBy('sort');
    }

    public function links(): HasMany
    {
        return $this->hasMany(ConstructLink::class)->orderBy('sort');
    }
}

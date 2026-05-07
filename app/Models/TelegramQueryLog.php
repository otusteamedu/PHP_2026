<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramQueryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'username',
        'text',
        'language',
        'query',
        'results_count',
        'selected_slug',
    ];
}


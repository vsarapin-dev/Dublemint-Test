<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends Model
{
    use HasFactory;

    protected $table = 'histories';

    protected $fillable = [
        'user_id',
        'random_number',
        'winning_total',
        'game_result',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

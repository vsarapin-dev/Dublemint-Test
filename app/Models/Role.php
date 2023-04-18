<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Return id of role name "admin".
     *
     * @return int|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function adminRole(): ?int
    {
        return self::where('name', 'admin')->value('id');
    }
}

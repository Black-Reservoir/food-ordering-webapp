<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'address', 'city', 'province', 'phone_number', 'is_active', 'image'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

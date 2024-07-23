<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userquantites extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code_unique',
        'quantite',
    ];

    /**
     * Get the user that owns the UserQuantite.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

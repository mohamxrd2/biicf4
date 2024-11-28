<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userquantites extends Model
{
    use HasFactory;

    protected $fillable = [
        'localite',
        'type_achat',
        'user_id',
        'code_unique',
        'quantite',
    ];

    /**
     * Relation avec le modèle User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Relation vers User
    }
}

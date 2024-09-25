<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrediScore extends Model
{
    use HasFactory;

    protected $table = 'credi_score';

    protected $fillable = [
        'ccc',
        'id_user',
    ];

    // Relation avec le modèle UserPromir
    public function userPromir()
    {
        return $this->belongsTo(UserPromir::class, 'id_user');
    }
}

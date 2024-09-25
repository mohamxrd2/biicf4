<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crp extends Model
{
    use HasFactory;

    protected $table = 'crp';

    protected $fillable = [
        'Solde',
        'Numero_compte',
        'Date_Creation',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}

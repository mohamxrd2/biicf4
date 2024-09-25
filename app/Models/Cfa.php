<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cfa extends Model
{
    use HasFactory;

    protected $table = 'cfa';

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

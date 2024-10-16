<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cedd extends Model
{
    use HasFactory;

    protected $table = 'cedd';

    protected $fillable = [
        'Solde',
        'type_compte',
        'Date_Creation',
        'id_wallet',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'id_wallet');
    }
}

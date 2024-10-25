<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Modifier id_user en user_id
        'admin_id', // Modifier id_admin en admin_id
        'balance',
        'Numero_compte',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Modifier id_user en user_id
    }
    public function coi()
    {
        return $this->hasOne(Coi::class, 'id_wallet');
    }
    public function coa()
    {
        return $this->hasOne(Cfa::class, 'id_wallet');
    }
    public function cedd()
    {
        return $this->hasOne(Cedd::class, 'id_wallet');
    }
    public function cefp()
    {
        return $this->hasOne(Cefp::class, 'id_wallet');
    }
    public function crp()
    {
        return $this->hasOne(Crp::class, 'id_wallet');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id'); // Modifier id_admin en admin_id
    }
}

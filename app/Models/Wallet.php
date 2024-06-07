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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Modifier id_user en user_id
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id'); // Modifier id_admin en admin_id
    }
}

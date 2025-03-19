<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promir extends Model
{
    use HasFactory;

    protected $table = 'promir';

    protected $fillable = [
        'user_id',
        'name',
        'last_stname',
        'user_name',
        'email',
        'phone_number',
        'system_client_id',
        'mois_depuis_creation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

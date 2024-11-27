<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComissionAdmin extends Model
{
    use HasFactory;

    protected $table = 'comission_admin';

    protected $fillable = [
        'admin_id',
        'balance',
    ];

    // Définir la relation avec le modèle Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
